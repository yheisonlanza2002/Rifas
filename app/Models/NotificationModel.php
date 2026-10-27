<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table            = 'notifications';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'notifiable_type', 'notifiable_id', 'type',
        'data', 'read_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true;
    protected $cleanValidationRules = true;

    public function forUser(int $userId): self
    {
        return $this->where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $userId);
    }

    public function unreadForUser(int $userId): array
    {
        return $this->forUser($userId)
            ->where('read_at', null)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function countUnread(int $userId): int
    {
        return $this->forUser($userId)
            ->where('read_at', null)
            ->countAllResults();
    }

    public function markAsRead(int $id): bool
    {
        return $this->update($id, ['read_at' => date('Y-m-d H:i:s')]);
    }

    public function markAllAsRead(int $userId): bool
    {
        return $this->forUser($userId)
            ->where('read_at', null)
            ->set(['read_at' => date('Y-m-d H:i:s')])
            ->update();
    }
}
