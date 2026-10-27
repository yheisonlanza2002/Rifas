<?php

namespace App\Models;

use CodeIgniter\Model;

class DrawModel extends Model
{
    protected $table            = 'draws';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'raffle_id', 'name',
        'scheduled_at', 'executed_at', 'status', 'method',
        'seed', 'result_hash', 'external_reference', 'created_by',
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
        return $this->where('draws.company_id', $companyId)
            ->join('raffles', 'raffles.id = draws.raffle_id');
    }

    public function findByRaffle(int $raffleId): array
    {
        return $this->where('raffle_id', $raffleId)
            ->orderBy('scheduled_at', 'DESC')
            ->findAll();
    }

    public function findPendingByRaffle(int $raffleId): ?array
    {
        return $this->where('raffle_id', $raffleId)
            ->where('status', 'SCHEDULED')
            ->where('scheduled_at <=', date('Y-m-d H:i:s'))
            ->first();
    }

    public function countByStatus(int $companyId, string $status): int
    {
        return $this->where('company_id', $companyId)
            ->where('status', $status)
            ->countAllResults();
    }
}
