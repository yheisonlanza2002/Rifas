<?php

namespace App\Models;

use CodeIgniter\Model;

class WinnerModel extends Model
{
    protected $table            = 'winners';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'draw_id', 'raffle_id', 'prize_id',
        'ticket_id', 'ticket_number_id', 'participant_id',
        'status', 'announced_at', 'notification_sent_at',
        'claim_deadline', 'claimed_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true;
    protected $cleanValidationRules = true;

    public function forCompany(int $companyId): self
    {
        return $this->where('winners.raffle_id IN (SELECT id FROM raffles WHERE company_id = ' . $companyId . ')');
    }

    public function findByDraw(int $drawId): array
    {
        return $this->where('draw_id', $drawId)
            ->orderBy('id', 'ASC')
            ->findAll();
    }

    public function findByParticipant(int $participantId): array
    {
        return $this->where('participant_id', $participantId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function countByStatus(int $companyId, string $status): int
    {
        return $this->where('winners.raffle_id IN (SELECT id FROM raffles WHERE company_id = ' . $companyId . ')')
            ->where('status', $status)
            ->countAllResults();
    }
}
