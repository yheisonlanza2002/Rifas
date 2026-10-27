<?php

namespace App\Models;

use CodeIgniter\Model;

class LotteryModel extends Model
{
    protected $table            = 'lotteries';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'name', 'code', 'status',
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
        return $this->where('company_id', $companyId)
            ->where('status', 'ACTIVE')
            ->orderBy('name', 'ASC');
    }

    public function findByName(int $companyId, string $name): ?array
    {
        return $this->where('company_id', $companyId)
            ->where('name', $name)
            ->where('deleted_at', null)
            ->first();
    }
}
