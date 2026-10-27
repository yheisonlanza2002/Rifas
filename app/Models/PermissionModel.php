<?php

namespace App\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table            = 'permissions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'module', 'action', 'description', 'guard_name', 'status',
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

    public function getModules(): array
    {
        return $this->distinct()
            ->select('module')
            ->where('deleted_at', null)
            ->orderBy('module', 'ASC')
            ->findColumn('module');
    }

    public function findByModuleAndAction(string $module, string $action): ?array
    {
        return $this->where('module', $module)
            ->where('action', $action)
            ->where('deleted_at', null)
            ->first();
    }

    public function countRoles(int $permissionId): int
    {
        return $this->db->table('role_permissions')
            ->where('permission_id', $permissionId)
            ->countAllResults();
    }
}
