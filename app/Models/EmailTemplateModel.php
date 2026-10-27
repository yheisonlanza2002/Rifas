<?php

namespace App\Models;

use CodeIgniter\Model;

class EmailTemplateModel extends Model
{
    protected $table            = 'email_templates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'name', 'slug', 'subject',
        'body_html', 'body_text', 'variables', 'is_global', 'status',
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
        return $this->groupStart()
            ->where('company_id', $companyId)
            ->orWhere('is_global', 1)
            ->groupEnd();
    }

    public function findBySlug(int $companyId, string $slug): ?array
    {
        return $this->forCompany($companyId)
            ->where('slug', $slug)
            ->where('deleted_at', null)
            ->first();
    }

    public function findGlobalBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)
            ->where('is_global', 1)
            ->where('deleted_at', null)
            ->first();
    }

    public function countByStatus(int $companyId, string $status): int
    {
        return $this->forCompany($companyId)
            ->where('status', $status)
            ->countAllResults();
    }
}
