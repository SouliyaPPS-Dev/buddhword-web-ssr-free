<?php
namespace App\Models;

/**
 * Lightweight JSON file store for push notification content and device
 * subscriptions. Mirrors the storage pattern used by the book management
 * feature (public/assets/books.json), but keeps site-managed data inside
 * the writable storage directory so it works on HF Spaces, Vercel and local.
 *
 * The notifications file can optionally be synced to a Hugging Face bucket
 * for durable storage and cross-deployment sharing (see syncToBucket()).
 */
class PushData
{
    private function dataDir()
    {
        $dir = __DIR__ . '/../../storage/data';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * Build a writable HF home and wrap a command so the hf/xet CLI can cache
     * its logs/data there. Apache runs as www-data (not root), so the default
     * /root/.cache/huggingface is not writable and hf cp fails with
     * "Permission denied (os error 13)".
     */
    private function hfCommand($args)
    {
        $home = getenv('HF_HOME') ?: ($_ENV['HF_HOME'] ?? ($this->dataDir() . '/.hfhome'));
        @mkdir($home, 0777, true);
        return 'HF_HOME=' . escapeshellarg($home)
            . ' HF_TOKEN=' . escapeshellarg(getenv('HF_TOKEN') ?: ($_ENV['HF_TOKEN'] ?? ''))
            . ' hf ' . $args . ' 2>&1';
    }

    private function notificationsPath()
    {
        return $this->dataDir() . '/notifications.json';
    }

    private function subscriptionsPath()
    {
        return $this->dataDir() . '/push-subscriptions.json';
    }

    private function load($path, $default = [])
    {
        if (!file_exists($path)) return $default;
        $raw = @file_get_contents($path);
        if ($raw === false) return $default;
        $data = json_decode($raw, true);
        return is_array($data) ? $data : $default;
    }

    private function save($path, $data)
    {
        file_put_contents($path, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    /* ----------------------- notifications ----------------------- */

    public function notifications()
    {
        return array_values($this->load($this->notificationsPath(), []));
    }

    public function findNotification($id)
    {
        foreach ($this->notifications() as $n) {
            if (($n['id'] ?? '') === $id) return $n;
        }
        return null;
    }

    public function saveNotifications($notifications)
    {
        $this->save($this->notificationsPath(), array_values($notifications));
    }

    public function storeNotification($input)
    {
        $notifications = $this->notifications();
        $id = !empty($input['id']) ? $input['id'] : uniqid('notif_', true);

        $entry = [
            'id' => $id,
            'title' => trim($input['title'] ?? ''),
            'body' => trim($input['body'] ?? ''),
            'url' => trim($input['url'] ?? ''),
            'icon' => trim($input['icon'] ?? ''),
            'enabled' => !empty($input['enabled']),
            'created_at' => $input['created_at'] ?? date('c'),
            'updated_at' => date('c'),
        ];

        // Update in place if editing an existing record
        foreach ($notifications as $i => &$n) {
            if (($n['id'] ?? '') === $id) {
                $entry['created_at'] = $n['created_at'] ?? $entry['created_at'];
                unset($n);
                $notifications[$i] = $entry;
                $this->saveNotifications($notifications);
                return ['success' => true, 'notification' => $entry];
            }
        }
        unset($n);

        $notifications[] = $entry;
        $this->saveNotifications($notifications);
        return ['success' => true, 'notification' => $entry];
    }

    public function destroyNotification($id)
    {
        $notifications = $this->notifications();
        $filtered = array_values(array_filter($notifications, function ($n) use ($id) {
            return ($n['id'] ?? '') !== $id;
        }));

        if (count($filtered) === count($notifications)) {
            return ['success' => false, 'error' => 'Notification not found'];
        }

        $this->saveNotifications($filtered);
        return ['success' => true];
    }

    public function destroyNotificationByTitle($title)
    {
        if (!$title) return ['success' => false, 'error' => 'Missing title'];
        $notifications = $this->notifications();
        $filtered = array_values(array_filter($notifications, function ($n) use ($title) {
            return ($n['title'] ?? '') !== $title;
        }));

        if (count($filtered) === count($notifications)) {
            return ['success' => false, 'error' => 'Notification not found'];
        }

        $this->saveNotifications($filtered);
        return ['success' => true];
    }

    /* --------------------- subscriptions --------------------- */

    public function subscriptions()
    {
        return array_values($this->load($this->subscriptionsPath(), []));
    }

    public function saveSubscriptions($subs)
    {
        $this->save($this->subscriptionsPath(), array_values($subs));
    }

    public function addSubscription($sub)
    {
        $subs = $this->subscriptions();
        $endpoint = $sub['endpoint'] ?? '';

        foreach ($subs as $i => &$s) {
            if (($s['endpoint'] ?? '') === $endpoint) {
                $sub['created_at'] = $s['created_at'] ?? date('c');
                $sub['updated_at'] = date('c');
                $subs[$i] = $sub;
                $this->saveSubscriptions($subs);
                return ['success' => true];
            }
        }
        unset($s);

        $sub['created_at'] = date('c');
        $sub['updated_at'] = date('c');
        $subs[] = $sub;
        $this->saveSubscriptions($subs);
        return ['success' => true];
    }

    public function removeSubscription($endpoint)
    {
        $subs = $this->subscriptions();
        $filtered = array_values(array_filter($subs, function ($s) use ($endpoint) {
            return ($s['endpoint'] ?? '') !== $endpoint;
        }));
        $this->saveSubscriptions($filtered);
        return ['success' => true];
    }

    /* ------------------- HF bucket sync ------------------- */

    /**
     * Best-effort push of the notifications (and subscriptions) files to a
     * Hugging Face bucket. Requires the hf CLI and a valid HF_TOKEN with
     * write access to the bucket in the running environment.
     */
    public function syncToBucket()
    {
        $bucket = getenv('HF_BUCKET') ?: ($_ENV['HF_BUCKET'] ?? '');
        $path = getenv('HF_BUCKET_PATH') ?: ($_ENV['HF_BUCKET_PATH'] ?? 'notifications');
        $dataDir = $this->dataDir();
        $canWrite = $this->canWriteToBucket();

        if (!$bucket || !$canWrite) {
            return ['success' => false, 'synced' => false, 'reason' => 'bucket_not_configured'];
        }

        $uploads = [
            'notifications.json' => "$path/notifications.json",
            'push-subscriptions.json' => "$path/push-subscriptions.json",
        ];

        $synced = [];
        $errors = [];
        foreach ($uploads as $file => $target) {
            $src = "$dataDir/$file";
            if (!file_exists($src)) continue;
            $cmd = $this->hfCommand('cp ' . escapeshellarg($src) . ' hf://buckets/' . $bucket . '/' . escapeshellarg($target));
            $out = [];
            $code = 0;
            exec($cmd, $out, $code);
            if ($code === 0) {
                $synced[] = $file;
            } else {
                $errors[$file] = implode("\n", $out);
            }
        }

        return [
            'success' => count($synced) > 0,
            'synced' => count($synced) > 0,
            'files' => $synced,
            'bucket' => "$bucket/$path",
            'errors' => $errors,
        ];
    }

    /**
     * Best-effort pull (download) of the notifications file from the bucket
     * into local storage, so a fresh deployment can load managed content.
     */
    public function pullFromBucket()
    {
        $bucket = getenv('HF_BUCKET') ?: ($_ENV['HF_BUCKET'] ?? '');
        $path = getenv('HF_BUCKET_PATH') ?: ($_ENV['HF_BUCKET_PATH'] ?? 'notifications');
        $dataDir = $this->dataDir();
        $canWrite = $this->canWriteToBucket();

        if (!$bucket || !$canWrite) {
            return ['success' => false, 'reason' => 'bucket_not_configured'];
        }

        $target = "$path/notifications.json";
        $local = "$dataDir/notifications.json";
        $cmd = $this->hfCommand('cp hf://buckets/' . $bucket . '/' . escapeshellarg($target) . ' ' . escapeshellarg($local));
        $out = [];
        $code = 0;
        exec($cmd, $out, $code);

        return [
            'success' => $code === 0 && file_exists($local),
            'output' => implode("\n", $out),
            'exit' => $code,
            'bucket' => "$bucket/$target",
            'local' => $local,
        ];
    }

    private function canWriteToBucket()
    {
        // Only attempt bucket writes when an explicit token is present.
        // The deployed Space passes HF_TOKEN as a secret.
        if (getenv('HF_TOKEN')) return true;
        if (isset($_ENV['HF_TOKEN']) && $_ENV['HF_TOKEN']) return true;
        return false;
    }
}
