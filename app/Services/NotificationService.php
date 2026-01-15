<?php

namespace App\Services;

use App\Jobs\SendNotificationJob;
use App\Models\User;
use App\Notifications\AdminNotification;
use App\Notifications\UserNotification;
use App\Notifications\StoreNotification;
use App\Notifications\MealNotification;
use App\Notifications\OrderNotification;
use Illuminate\Support\Facades\Notification;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;

class NotificationService
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new Factory)
            ->withServiceAccount(config('services.firebase.credentials'))
            ->createMessaging();
    }

    /**
     * ارسال اشعار لكل المستخدمين
     */
    public function sendToAllUsers($title, $data = [])
    {
        $this->sendFirebaseTopic('users', $title, $data);
    }


    /**
     * دالة مساعدة لإرسال Firebase Topic
     */
    protected function sendFirebaseTopic($topic, $title, $data = [])
    {
        $message = CloudMessage::new()
            ->withNotification([
                'title' => $title,
                'body'  => '',
            ])
            ->withData($data)
            ->toTopic($topic);

        $this->firebase->send($message);
    }



    public function sendToUser($user, $title, $body, $data = [])
    {
        $fcmTokens = $user->fcmTokens()->pluck('token')->toArray();

        if ($user && count($fcmTokens) > 0) {

            $firebase = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials'))
                ->createMessaging();

            // 🔒 تأمين القيم (كلها string)
            $data = collect($data)->map(function ($value) {
                return (string) ($value ?? '');
            })->toArray();

            foreach ($fcmTokens as $token) {
                $message = [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $data
                ];

                $firebase->send($message);
            }
        }
    }
}
