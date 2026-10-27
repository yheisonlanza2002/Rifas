<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'uuid', 'company_id', 'name', 'last_name', 'email', 'phone',
        'password_hash', 'avatar', 'timezone', 'locale',
        'two_factor_enabled', 'two_factor_secret', 'two_factor_recovery_codes',
        'status', 'last_login_at',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    // Las validaciones de formularios viven en Config/Validation.php
    // (grupos: userLogin, userCreate, userUpdate).
    // El modelo omite su propia validación para evitar doble validación
    // con campos que no son columnas reales (p. ej. 'password' vs 'password_hash').
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = true;
    protected $cleanValidationRules = true;

    /**
     * Busca un usuario activo por email (para login).
     */
    public function findByEmail(string $email): ?array
    {
        return $this->where('email', $email)
            ->where('status', 'ACTIVE')
            ->where('deleted_at', null)
            ->first();
    }

    /**
     * Obtiene los roles de un usuario.
     */
    public function getRoles(int $userId): array
    {
        $db = \Config\Database::connect();
        return $db->table('user_roles')
            ->select('roles.name')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.user_id', $userId)
            ->where('roles.status', 'ACTIVE')
            ->get()
            ->getResultArray();
    }
}
