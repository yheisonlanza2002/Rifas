<?php

namespace App\Models;

use CodeIgniter\Model;

class TicketModel extends Model
{
    protected $table            = 'tickets';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'raffle_id', 'code', 'serial_prefix',
        'status', 'price', 'number_quantity', 'bundle_quantity',
        'reserved_at', 'reservation_expires_at', 'sold_at', 'cancelled_at',
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
        return $this->where('tickets.company_id', $companyId)
            ->join('raffles', 'raffles.id = tickets.raffle_id');
    }

    public function findByCode(int $raffleId, string $code): ?array
    {
        return $this->where('raffle_id', $raffleId)
            ->where('code', $code)
            ->first();
    }

    public function countByStatus(int $raffleId, string $status): int
    {
        return $this->where('raffle_id', $raffleId)
            ->where('status', $status)
            ->countAllResults();
    }
}
