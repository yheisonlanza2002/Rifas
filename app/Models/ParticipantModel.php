<?php

namespace App\Models;

use CodeIgniter\Model;

class ParticipantModel extends Model
{
    protected $table            = 'participants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'document_type', 'document_number',
        'name', 'last_name', 'email', 'phone', 'address',
        'city', 'department', 'birth_date', 'gender', 'notes',
        'source', 'status',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Las validaciones de formularios viven en Config/Validation.php
    // (grupos: participantCreate, participantUpdate).
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true;
    protected $cleanValidationRules = true;

    /**
     * Lista participantes de la empresa actual (alcance multi-empresa).
     */
    public function forCompany(int $companyId): self
    {
        return $this->where('company_id', $companyId);
    }

    /**
     * Busca participante por documento dentro de la empresa.
     */
    public function findByDocument(int $companyId, string $documentType, string $documentNumber): ?array
    {
        return $this->where('company_id', $companyId)
            ->where('document_type', $documentType)
            ->where('document_number', $documentNumber)
            ->where('deleted_at', null)
            ->first();
    }
}
