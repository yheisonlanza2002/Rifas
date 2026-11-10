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
        return $this->select('sales.*, participants.name AS participant_name', false)
            ->where('sales.company_id', $companyId)
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

    /**
     * Recalcula la venta a partir de sus pagos COMPLETED.
     * Los pagos son la única fuente de verdad de paid_amount/pending_amount.
     */
    public function syncFromPayments(int $saleId): void
    {
        $sale = $this->find($saleId);
        if (!$sale) {
            return;
        }

        $row = $this->db->table('payments')
            ->select('COALESCE(SUM(amount),0) AS paid', false)
            ->where('sale_id', $saleId)
            ->where('status', 'COMPLETED')
            ->get()
            ->getRow();

        $paid   = round((float) ($row->paid ?? 0), 2);
        $total  = (float) $sale['total'];
        $update = [
            'paid_amount'    => $paid,
            'pending_amount' => max(0, round($total - $paid, 2)),
        ];

        if ($sale['status'] !== 'CANCELLED') {
            if ($total > 0 && $paid >= $total) {
                $update['status'] = 'PAID';
                if (empty($sale['sold_at'])) {
                    $update['sold_at'] = date('Y-m-d H:i:s');
                }
            } elseif ($sale['status'] === 'PAID') {
                $update['status'] = 'PENDING';
            }
        }

        $this->update($saleId, $update);
    }
}
