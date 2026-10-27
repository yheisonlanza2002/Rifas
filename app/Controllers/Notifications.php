<?php

namespace App\Controllers;

use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected NotificationModel $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }

    public function index(): string
    {
        $userId = (int) session()->get('user_id');

        $notifications = $this->notificationModel
            ->forUser($userId)
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $unreadCount = $this->notificationModel->countUnread($userId);

        return view('pages/notifications/index', [
            'title'         => 'Notificaciones - Rifas',
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
        ]);
    }

    public function markAsRead(int $id)
    {
        $userId = (int) session()->get('user_id');

        $notification = $this->notificationModel
            ->forUser($userId)
            ->find($id);

        if (!$notification) {
            return redirect()->to(base_url('notificaciones'))
                ->with('error', 'La notificación no existe.');
        }

        $this->notificationModel->markAsRead($id);

        return redirect()->to(base_url('notificaciones'))
            ->with('success', 'Notificación marcada como leída.');
    }

    public function markAllAsRead()
    {
        $userId = (int) session()->get('user_id');

        $this->notificationModel->markAllAsRead($userId);

        return redirect()->to(base_url('notificaciones'))
            ->with('success', 'Todas las notificaciones marcadas como leídas.');
    }

    public function delete(int $id)
    {
        $userId = (int) session()->get('user_id');

        $notification = $this->notificationModel
            ->forUser($userId)
            ->find($id);

        if (!$notification) {
            return redirect()->to(base_url('notificaciones'))
                ->with('error', 'La notificación no existe.');
        }

        $this->notificationModel->delete($id);

        return redirect()->to(base_url('notificaciones'))
            ->with('success', 'Notificación eliminada correctamente.');
    }
}
