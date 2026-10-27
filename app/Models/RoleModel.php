<?php

namespace App\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table            = 'roles';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'name', 'description', 'scope', 'is_system', 'status',
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

    public function getPermissions(int $roleId): array
    {
        return $this->db->table('role_permissions rp')
            ->select('p.*')
            ->join('permissions p', 'p.id = rp.permission_id')
            ->where('rp.role_id', $roleId)
            ->where('p.deleted_at', null)
            ->get()
            ->getResultArray();
    }

    public function syncPermissions(int $roleId, array $permissionIds): void
    {
        $this->db->table('role_permissions')
            ->where('role_id', $roleId)
            ->delete();

        if (empty($permissionIds)) {
            return;
        }

        $data = array_map(fn($pid) => [
            'role_id'        => $roleId,
            'permission_id'  => $pid,
            'created_at'     => date('Y-m-d H:i:s'),
        ], $permissionIds);

        $this->db->table('role_permissions')->insertBatch($data);
    }

    public function countUsers(int $roleId): int
    {
        return $this->db->table('user_roles')
            ->where('role_id', $roleId)
            ->countAllResults();
    }
}
