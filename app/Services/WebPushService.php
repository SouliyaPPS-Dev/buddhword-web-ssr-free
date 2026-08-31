<?php
namespace App\Services;

use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

/**
 * Web Push (RFC 8291 / Web Push Protocol) sender built on minishlink/web-push.
 * Handles VAPID JWT signing, payload encryption (aes128gcm) and delivery to
 * the browser's push service so notifications reach PWA devices even when
 * the app is closed / running in the background.
 */
class WebPushService
{
    private $publicKey;
    private $privateKey;
    private $subject;

    public function __construct()
    {
        $this->publicKey = getenv('VAPID_PUBLIC_KEY') ?: ($_ENV['VAPID_PUBLIC_KEY'] ?? '');
        $this->privateKey = getenv('VAPID_PRIVATE_KEY') ?: ($_ENV['VAPID_PRIVATE_KEY'] ?? '');
        $this->subject = getenv('VAPID_SUBJECT') ?: ($_ENV['VAPID_SUBJECT'] ?? 'mailto:admin@buddhaword.net');
    }

    public function isConfigured()
    {
        return $this->publicKey !== '' && $this->privateKey !== '';
    }

    /**
     * @param array $subscription  ['endpoint'=>..., 'keys'=>['p256dh'=>...,'auth'=>...]]
     * @param array $data          payload ['title','body','icon','url']
     * @return array ['success'=>bool,'status'=>int|null,'error'=>?string,'gone'=>?bool]
     */
    public function send($subscription, $data)
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'VAPID keys not configured'];
        }

        try {
            $webPush = new WebPush([
                'VAPID' => [
                    'subject' => $this->subject,
                    'publicKey' => $this->publicKey,
                    'privateKey' => $this->privateKey,
                ],
            ], [
                'TTL' => 3600,
            ]);

            $sub = Subscription::create($subscription);
            $payload = json_encode($data, JSON_UNESCAPED_UNICODE);

            $report = $webPush->sendOneNotification($sub, $payload);

            if ($report->isSuccess()) {
                return ['success' => true, 'status' => $report->getResponse() ? $report->getResponse()->getStatusCode() : null];
            }

            $status = $report->getResponse() ? $report->getResponse()->getStatusCode() : null;
            $result = [
                'success' => false,
                'status' => $status,
                'error' => $report->getReason() ?: ('Push failed'),
            ];
            // 404/410 -> the subscription is no longer valid
            if ($status === 404 || $status === 410) {
                $result['gone'] = true;
            }
            return $result;
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
