<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketEventModel extends Model
{
    protected $table            = 'ticket_events';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'raffle_id', 'ticket_id', 'ticket_number_id',
        'event_type', 'previous_status', 'new_status',
        'reference_type', 'reference_id', 'description',
        'metadata', 'created_by',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = null;

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true;
    protected $cleanValidationRules = true;

    public function forCompany(int $companyId): self
    {
        return $this->where('ticket_events.raffle_id IN (SELECT id FROM raffles WHERE company_id = ' . $companyId . ')');
    }

    public function findByTicket(int $ticketId): array
    {
        return $this->where('ticket_id', $ticketId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    public function findByRaffle(int $raffleId, int $limit = 50): array
    {
        return $this->where('raffle_id', $raffleId)
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}
