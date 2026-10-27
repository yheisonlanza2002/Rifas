<?php

namespace App\Models;

use CodeIgniter\Model;

class SaleModel extends Model
{
    protected $table            = 'sales';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'participant_id', 'sale_code',
        'status', 'quantity', 'subtotal', 'discount',
        'tax_amount', 'commission_amount', 'total', 'paid_amount',
        'pending_amount', 'currency', 'notes',
        'sold_at', 'expires_at', 'cancelled_at',
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
        return $this->where('sales.company_id', $companyId)
            ->join('participants', 'participants.id = sales.participant_id');
    }

    public function findByCode(int $companyId, string $saleCode): ?array
    {
        return $this->where('company_id', $companyId)
            ->where('sale_code', $saleCode)
            ->first();
    }

    public function countByStatus(int $companyId, string $status): int
    {
        return $this->where('company_id', $companyId)
            ->where('status', $status)
            ->countAllResults();
    }
}
