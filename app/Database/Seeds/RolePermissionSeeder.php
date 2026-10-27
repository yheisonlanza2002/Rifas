<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        $permissions = [
            ['name' => 'companies.view', 'module' => 'companies', 'action' => 'view', 'description' => 'Ver empresas'],
            ['name' => 'companies.create', 'module' => 'companies', 'action' => 'create', 'description' => 'Crear empresas'],
            ['name' => 'companies.edit', 'module' => 'companies', 'action' => 'edit', 'description' => 'Editar empresas'],
            ['name' => 'companies.delete', 'module' => 'companies', 'action' => 'delete', 'description' => 'Eliminar empresas'],
            ['name' => 'users.view', 'module' => 'users', 'action' => 'view', 'description' => 'Ver usuarios'],
            ['name' => 'users.create', 'module' => 'users', 'action' => 'create', 'description' => 'Crear usuarios'],
            ['name' => 'users.edit', 'module' => 'users', 'action' => 'edit', 'description' => 'Editar usuarios'],
            ['name' => 'users.delete', 'module' => 'users', 'action' => 'delete', 'description' => 'Eliminar usuarios'],
            ['name' => 'roles.view', 'module' => 'roles', 'action' => 'view', 'description' => 'Ver roles'],
            ['name' => 'roles.create', 'module' => 'roles', 'action' => 'create', 'description' => 'Crear roles'],
            ['name' => 'roles.edit', 'module' => 'roles', 'action' => 'edit', 'description' => 'Editar roles'],
            ['name' => 'roles.delete', 'module' => 'roles', 'action' => 'delete', 'description' => 'Eliminar roles'],
            ['name' => 'participants.view', 'module' => 'participants', 'action' => 'view', 'description' => 'Ver participantes'],
            ['name' => 'participants.create', 'module' => 'participants', 'action' => 'create', 'description' => 'Crear participantes'],
            ['name' => 'participants.edit', 'module' => 'participants', 'action' => 'edit', 'description' => 'Editar participantes'],
            ['name' => 'participants.delete', 'module' => 'participants', 'action' => 'delete', 'description' => 'Eliminar participantes'],
            ['name' => 'participants.import', 'module' => 'participants', 'action' => 'import', 'description' => 'Importar participantes'],
            ['name' => 'raffles.view', 'module' => 'raffles', 'action' => 'view', 'description' => 'Ver rifas'],
            ['name' => 'raffles.create', 'module' => 'raffles', 'action' => 'create', 'description' => 'Crear rifas'],
            ['name' => 'raffles.edit', 'module' => 'raffles', 'action' => 'edit', 'description' => 'Editar rifas'],
            ['name' => 'raffles.delete', 'module' => 'raffles', 'action' => 'delete', 'description' => 'Eliminar rifas'],
            ['name' => 'raffles.publish', 'module' => 'raffles', 'action' => 'publish', 'description' => 'Publicar rifas'],
            ['name' => 'raffles.draw', 'module' => 'raffles', 'action' => 'draw', 'description' => 'Realizar sorteos'],
            ['name' => 'tickets.view', 'module' => 'tickets', 'action' => 'view', 'description' => 'Ver boletas'],
            ['name' => 'tickets.generate', 'module' => 'tickets', 'action' => 'generate', 'description' => 'Generar boletas'],
            ['name' => 'tickets.reserve', 'module' => 'tickets', 'action' => 'reserve', 'description' => 'Reservar boletas'],
            ['name' => 'tickets.sell', 'module' => 'tickets', 'action' => 'sell', 'description' => 'Vender boletas'],
            ['name' => 'tickets.cancel', 'module' => 'tickets', 'action' => 'cancel', 'description' => 'Cancelar boletas'],
            ['name' => 'sales.view', 'module' => 'sales', 'action' => 'view', 'description' => 'Ver ventas'],
            ['name' => 'sales.create', 'module' => 'sales', 'action' => 'create', 'description' => 'Crear ventas'],
            ['name' => 'sales.cancel', 'module' => 'sales', 'action' => 'cancel', 'description' => 'Cancelar ventas'],
            ['name' => 'sales.refund', 'module' => 'sales', 'action' => 'refund', 'description' => 'Reembolsar ventas'],
            ['name' => 'payments.view', 'module' => 'payments', 'action' => 'view', 'description' => 'Ver pagos'],
            ['name' => 'payments.process', 'module' => 'payments', 'action' => 'process', 'description' => 'Procesar pagos'],
            ['name' => 'payments.reconcile', 'module' => 'payments', 'action' => 'reconcile', 'description' => 'Conciliar pagos'],
            ['name' => 'prizes.view', 'module' => 'prizes', 'action' => 'view', 'description' => 'Ver premios'],
            ['name' => 'prizes.create', 'module' => 'prizes', 'action' => 'create', 'description' => 'Crear premios'],
            ['name' => 'prizes.edit', 'module' => 'prizes', 'action' => 'edit', 'description' => 'Editar premios'],
            ['name' => 'prizes.delete', 'module' => 'prizes', 'action' => 'delete', 'description' => 'Eliminar premios'],
            ['name' => 'winners.view', 'module' => 'winners', 'action' => 'view', 'description' => 'Ver ganadores'],
            ['name' => 'winners.claim', 'module' => 'winners', 'action' => 'claim', 'description' => 'Reclamar premios'],
            ['name' => 'reports.view', 'module' => 'reports', 'action' => 'view', 'description' => 'Ver reportes'],
            ['name' => 'reports.export', 'module' => 'reports', 'action' => 'export', 'description' => 'Exportar reportes'],
            ['name' => 'settings.view', 'module' => 'settings', 'action' => 'view', 'description' => 'Ver configuración'],
            ['name' => 'settings.edit', 'module' => 'settings', 'action' => 'edit', 'description' => 'Editar configuración'],
        ];

        foreach ($permissions as $permission) {
            $exists = $db->table('permissions')->where('name', $permission['name'])->countAllResults();
            if ($exists > 0) {
                continue;
            }
            $db->table('permissions')->insert([
                'name' => $permission['name'],
                'module' => $permission['module'],
                'action' => $permission['action'],
                'description' => $permission['description'],
                'guard_name' => 'web',
                'status' => 'ACTIVE',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        $roles = [
            [
                'name' => 'SUPER_ADMIN',
                'description' => 'Acceso total al sistema (multi-empresa)',
                'scope' => 'GLOBAL',
                'is_system' => 1,
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'ADMIN',
                'description' => 'Administrador de empresa',
                'scope' => 'COMPANY',
                'is_system' => 1,
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'SELLER',
                'description' => 'Vendedor de boletas',
                'scope' => 'COMPANY',
                'is_system' => 1,
                'status' => 'ACTIVE',
            ],
            [
                'name' => 'VIEWER',
                'description' => 'Solo lectura',
                'scope' => 'COMPANY',
                'is_system' => 1,
                'status' => 'ACTIVE',
            ],
        ];

        $roleIds = [];
        foreach ($roles as $role) {
            $role['created_at'] = date('Y-m-d H:i:s');
            $role['updated_at'] = date('Y-m-d H:i:s');
            $existing = $db->table('roles')->where('name', $role['name'])->get()->getRow('id');
            if ($existing) {
                $roleIds[$role['name']] = $existing;
                continue;
            }
            $db->table('roles')->insert($role);
            $roleIds[$role['name']] = $db->insertID();
        }

        $allPermissions = $db->table('permissions')->where('status', 'ACTIVE')->get()->getResultArray();

        $rolePermissions = [
            'SUPER_ADMIN' => array_column($allPermissions, 'id'),
            'ADMIN' => array_filter(array_column($allPermissions, 'id'), function ($id) use ($allPermissions, $db) {
                $perm = array_filter($allPermissions, fn($p) => $p['id'] === $id);
                $perm = array_values($perm)[0] ?? null;
                return $perm && !in_array($perm['name'], ['companies.create', 'companies.delete', 'companies.edit']);
            }),
            'SELLER' => array_filter(array_column($allPermissions, 'id'), function ($id) use ($allPermissions) {
                $perm = array_filter($allPermissions, fn($p) => $p['id'] === $id);
                $perm = array_values($perm)[0] ?? null;
                return $perm && in_array($perm['name'], [
                    'participants.view', 'participants.create', 'participants.edit',
                    'raffles.view', 'tickets.view', 'tickets.reserve', 'tickets.sell',
                    'sales.view', 'sales.create', 'payments.view',
                    'reports.view',
                ]);
            }),
            'VIEWER' => array_filter(array_column($allPermissions, 'id'), function ($id) use ($allPermissions) {
                $perm = array_filter($allPermissions, fn($p) => $p['id'] === $id);
                $perm = array_values($perm)[0] ?? null;
                return $perm && str_ends_with($perm['name'], '.view');
            }),
        ];

        foreach ($rolePermissions as $roleName => $permissionIds) {
            $roleId = $roleIds[$roleName];
            foreach ($permissionIds as $permissionId) {
                $exists = $db->table('role_permissions')
                    ->where('role_id', $roleId)
                    ->where('permission_id', $permissionId)
                    ->countAllResults();
                if ($exists > 0) {
                    continue;
                }
                $db->table('role_permissions')->insert([
                    'role_id' => $roleId,
                    'permission_id' => $permissionId,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            }
        }
    }
}