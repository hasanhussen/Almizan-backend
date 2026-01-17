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
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\Messaging\InvalidArgument;
use Illuminate\Support\Facades\Log;


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
        if (!$user) {
            return;
        }

        $fcmTokens = $user->fcmTokens()->pluck('token')->toArray();

        if (empty($fcmTokens)) {
            return;
        }

        $firebase = (new Factory)
            ->withServiceAccount(config('services.firebase.credentials'))
            ->createMessaging();

        // 🔒 تأمين القيم (كلها string)
        $data = collect($data)->map(fn($v) => (string) ($v ?? ''))->toArray();

        foreach ($fcmTokens as $token) {
            try {
                $message = [
                    'token' => $token,
                    'notification' => [
                        'title' => $title,
                        'body'  => $body,
                    ],
                    'data' => $data,
                ];

                $firebase->send($message);
            } catch (NotFound | InvalidArgument $e) {
                // 🧹 حذف التوكن غير الصالح
                $user->fcmTokens()->where('token', $token)->delete();
            } catch (\Throwable $e) {
                // أي خطأ غير متوقع (ما نوقف النظام)
                Log::error('FCM send error', [
                    'user_id' => $user->id,
                    'token'   => $token,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }

    // public function sendToUser($user, $title, $body, $data = [])
    // {
    //     $fcmTokens = $user->fcmTokens()->pluck('token')->toArray();

    // if (!$user || empty($fcmTokens)) {
    //     return;
    // }

    //     if ($user && count($fcmTokens) > 0) {

    //         $firebase = (new Factory)
    //             ->withServiceAccount(config('services.firebase.credentials'))
    //             ->createMessaging();

    //         // 🔒 تأمين القيم (كلها string)
    //         $data = collect($data)->map(function ($value) {
    //             return (string) ($value ?? '');
    //         })->toArray();

    //         foreach ($fcmTokens as $token) {
    //             $message = [
    //                 'token' => $token,
    //                 'notification' => [
    //                     'title' => $title,
    //                     'body'  => $body,
    //                 ],
    //                 'data' => $data
    //             ];

    //             $firebase->send($message);
    //         }
    //     }
    // }
}
