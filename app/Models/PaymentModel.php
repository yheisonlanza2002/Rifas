<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table            = 'payments';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'sale_id', 'gateway_id',
        'amount', 'fee_amount', 'net_amount', 'payment_method',
        'status', 'transaction_reference', 'gateway',
        'raw_request', 'raw_response', 'paid_at',
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
        return $this->where('payments.company_id', $companyId)
            ->join('sales', 'sales.id = payments.sale_id');
    }

    public function findByReference(int $companyId, string $reference): ?array
    {
        return $this->where('company_id', $companyId)
            ->where('transaction_reference', $reference)
            ->first();
    }

    public function countByStatus(int $companyId, string $status): int
    {
        return $this->where('company_id', $companyId)
            ->where('status', $status)
            ->countAllResults();
    }
}
