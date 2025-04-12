<?php

namespace App\Repositories;

use App\Models\Notification;
use App\Repositories\Interfaces\NotificationRepositoryInterface;

class NotificationRepository implements NotificationRepositoryInterface
{
    public function getNotificationsByStagiaire($stagiaireId)
    {
        return Notification::where('stagiaire_id', $stagiaireId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getUnreadNotifications($stagiaireId)
    {
        return Notification::where('stagiaire_id', $stagiaireId)
            ->where('read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function markAsRead($notificationId): bool
    {
        $notification = Notification::find($notificationId);
        return $notification ? $notification->update(['read' => true]) : false;
    }

    public function markAllAsRead($stagiaireId): bool
    {
        return Notification::where('stagiaire_id', $stagiaireId)
            ->where('read', false)
            ->update(['read' => true]);
    }

    public function createNotification(array $data): Notification
    {
        return Notification::create($data);
    }

    public function deleteNotification($id): bool
    {
        $notification = Notification::find($id);
        return $notification ? $notification->delete() : false;
    }
} 