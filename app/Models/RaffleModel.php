<?php

namespace App\Models;

use CodeIgniter\Model;

class RaffleModel extends Model
{
    protected $table            = 'raffles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'name', 'description', 'code',
        'lottery_id', 'status', 'ticket_quantity', 'numbers_per_ticket',
        'number_start', 'number_end', 'number_length',
        'number_assignment_mode', 'number_selection_mode',
        'ticket_price', 'currency', 'commission_rate',
        'reservation_minutes', 'terms_conditions',
        'banner_image', 'gallery', 'settings',
        'start_at', 'end_at',
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

    public function findByCode(int $companyId, string $code): ?array
    {
        return $this->where('company_id', $companyId)
            ->where('code', $code)
            ->where('deleted_at', null)
            ->first();
    }
}
