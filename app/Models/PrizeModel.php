<?php

namespace App\Models;

use CodeIgniter\Model;

class PrizeModel extends Model
{
    protected $table            = 'prizes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'raffle_id', 'lottery_id', 'name', 'description',
        'position', 'quantity', 'value', 'currency',
        'image', 'odds', 'status', 'prize_type',
        'recurrence_days', 'draw_date', 'prize_image',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true;
    protected $cleanValidationRules = true;

    public function forCompany(int $companyId): self
    {
        return $this->where('raffles.company_id', $companyId)
            ->join('raffles', 'raffles.id = prizes.raffle_id');
    }

    public function findByPosition(int $raffleId, int $position): ?array
    {
        return $this->where('raffle_id', $raffleId)
            ->where('position', $position)
            ->where('deleted_at', null)
            ->first();
    }
}
