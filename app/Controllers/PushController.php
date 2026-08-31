<?php
namespace App\Controllers;

use App\Models\PushData;
use App\Services\WebPushService;

require_once __DIR__ . '/../Helpers/view.php';

class PushController
{
    private function model()
    {
        return new PushData();
    }

    private function push()
    {
        return new WebPushService();
    }

    private function json($data, $code = 200)
    {
        if (ob_get_level()) ob_clean();
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function index()
    {
        $model = $this->model();
        $notifications = array_reverse($model->notifications());
        $config = [
            'vapidPublicKey' => $this->push()->isConfigured() ? (getenv('VAPID_PUBLIC_KEY') ?: ($_ENV['VAPID_PUBLIC_KEY'] ?? '')) : '',
            'configured' => $this->push()->isConfigured(),
            'bucketConfigured' => $this->bucketConfigured(),
        ];

        return view('pages.push.index', [
            'notifications' => $notifications,
            'config' => $config,
            'seo' => [
                'title' => 'ຈັດການແຈ້ງເຕືອນ - ຄຳສອນພຸດທະ',
                'description' => 'ຈັດການຂໍ້ຄວາມແຈ້ງເຕືອນ Push Notification ສຳລັບໂທລະສັບ',
                'robots' => 'noindex, nofollow',
            ]
        ]);
    }

    public function pubkey()
    {
        $service = $this->push();
        $this->json([
            'success' => true,
            'publicKey' => $service->isConfigured() ? (getenv('VAPID_PUBLIC_KEY') ?: ($_ENV['VAPID_PUBLIC_KEY'] ?? '')) : '',
            'configured' => $service->isConfigured(),
        ]);
    }

    public function apiList()
    {
        $model = $this->model();
        $notifications = array_reverse($model->notifications());
        $this->json([
            'success' => true,
            'notifications' => $notifications,
            'subscriberCount' => count($model->subscriptions()),
            'config' => [
                'configured' => $this->push()->isConfigured(),
                'bucketConfigured' => $this->bucketConfigured(),
            ],
        ]);
    }

    public function store()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $input = $_POST;
        }

        $id = $input['id'] ?? null;
        $fields = $id ? $this->model()->findNotification($id) : null;

        $data = [
            'id' => $id,
            'title' => $input['title'] ?? ($fields['title'] ?? ''),
            'body' => $input['body'] ?? ($fields['body'] ?? ''),
            'url' => $input['url'] ?? ($fields['url'] ?? ''),
            'enabled' => isset($input['enabled']) ? filter_var($input['enabled'], FILTER_VALIDATE_BOOLEAN) : ($fields['enabled'] ?? true),
            'created_at' => $fields['created_at'] ?? null,
        ];

        if ($data['title'] === '') {
            $this->json(['success' => false, 'error' => 'ກະລຸນາປ້ອນຫົວຂໍ້'], 400);
        }

        $result = $this->model()->storeNotification($data);
        if (!$result['success']) {
            $this->json(['success' => false, 'error' => $result['error'] ?? 'Save failed'], 500);
        }
        $this->json(['success' => true, 'notification' => $result['notification']]);
    }

    public function destroy()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? ($_POST['id'] ?? '');
        if (!$id) {
            $this->json(['success' => false, 'error' => 'Missing id'], 400);
        }

        $result = $this->model()->destroyNotification($id);
        if (!$result['success']) {
            $this->json(['success' => false, 'error' => $result['error'] ?? 'Delete failed'], 404);
        }
        $this->json(['success' => true]);
    }

    /**
     * Save a subscription coming from the PWA client.
     */
    public function subscribe()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input) || empty($input['endpoint'])) {
            $this->json(['success' => false, 'error' => 'Invalid subscription'], 400);
        }

        $this->model()->addSubscription([
            'endpoint' => $input['endpoint'],
            'keys' => [
                'p256dh' => $input['keys']['p256dh'] ?? '',
                'auth' => $input['keys']['auth'] ?? '',
            ],
        ]);

        $this->json(['success' => true]);
    }

    public function unsubscribe()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        $endpoint = $input['endpoint'] ?? ($_POST['endpoint'] ?? '');
        if (!$endpoint) {
            $this->json(['success' => false, 'error' => 'Missing endpoint'], 400);
        }
        $this->model()->removeSubscription($endpoint);
        $this->json(['success' => true]);
    }

    /**
     * Broadcast a notification to every subscribed device.
     */
    public function send()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (!is_array($input)) {
            $input = $_POST;
        }

        $notificationId = $input['id'] ?? '';
        $model = $this->model();
        $title = '';
        $body = '';
        $url = '';

        if ($notificationId) {
            $n = $model->findNotification($notificationId);
            if ($n) {
                $title = $n['title'] ?? '';
                $body = $n['body'] ?? '';
                $url = $n['url'] ?? '';
            }
        }

        // Fallback to input fields (sendNow sends title/body/url too)
        if ($title === '') $title = $input['title'] ?? '';
        if ($body === '') $body = $input['body'] ?? '';
        if ($url === '') $url = $input['url'] ?? '';

        if ($title === '') {
            $this->json(['success' => false, 'error' => 'Missing title'], 400);
        }

        $subs = $model->subscriptions();
        if (empty($subs)) {
            $this->json(['success' => true, 'sent' => 0, 'total' => 0, 'message' => 'No subscribers yet']);
        }

        $payload = [
            'title' => $title,
            'body' => $body,
            'url' => $url ? $url : url('/'),
        ];

        $service = $this->push();
        if (!$service->isConfigured()) {
            $this->json(['success' => false, 'error' => 'VAPID keys not configured'], 500);
        }

        $sent = 0;
        $failures = 0;
        $gone = [];

        foreach ($subs as $sub) {
            $result = $service->send($sub, $payload);
            if ($result['success']) {
                $sent++;
            } else {
                $failures++;
                if (!empty($result['gone'])) {
                    $gone[] = $sub['endpoint'] ?? '';
                }
            }
        }

        // Remove dead subscriptions
        foreach ($gone as $endpoint) {
            $model->removeSubscription($endpoint);
        }

        $this->json([
            'success' => true,
            'sent' => $sent,
            'failed' => $failures,
            'total' => count($subs),
        ]);
    }

    /**
     * Push local notification/subscription data to the HF bucket (durable storage).
     */
    public function syncBucket()
    {
        $result = $this->model()->syncToBucket();
        $this->json($result);
    }

    /**
     * Pull notification data from the HF bucket into local storage.
     */
    public function pullBucket()
    {
        $result = $this->model()->pullFromBucket();
        $this->json($result);
    }

    private function bucketConfigured()
    {
        $bucket = getenv('HF_BUCKET') ?: ($_ENV['HF_BUCKET'] ?? '');
        return $bucket !== '';
    }
}
