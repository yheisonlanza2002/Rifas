<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentGatewayModel extends Model
{
    protected $table            = 'payment_gateways';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'name', 'slug', 'driver',
        'config', 'supported_methods', 'test_mode', 'status', 'sort_order',
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
        return $this->where('company_id', $companyId);
    }

    public function findBySlug(int $companyId, string $slug): ?array
    {
        return $this->where('company_id', $companyId)
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();
    }
}
