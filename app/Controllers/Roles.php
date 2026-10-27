<?php

namespace App\Controllers;

use App\Models\RoleModel;
use App\Models\PermissionModel;

class Roles extends BaseController
{
    protected RoleModel $roleModel;
    protected PermissionModel $permissionModel;

    private const SCOPES = [
        'COMPANY' => 'Empresa',
        'SYSTEM'  => 'Sistema',
    ];

    private const STATUSES = [
        'ACTIVE'   => 'Activo',
        'INACTIVE' => 'Inactivo',
    ];

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
    }

    public function index(): string
    {
        $roles = $this->roleModel
            ->orderBy('name', 'ASC')
            ->findAll();

        $rolesConContadores = array_map(function ($rol) {
            $rol['total_permisos'] = count($this->roleModel->getPermissions($rol['id']));
            $rol['total_usuarios'] = $this->roleModel->countUsers($rol['id']);
            return $rol;
        }, $roles);

        return view('pages/roles/index', [
            'title'   => 'Roles y Permisos - Rifas',
            'roles'   => $rolesConContadores,
            'scopes'  => self::SCOPES,
            'statuses' => self::STATUSES,
        ]);
    }

    public function create(): string
    {
        $permissions = $this->permissionModel
            ->where('status', 'ACTIVE')
            ->orderBy('module', 'ASC')
            ->orderBy('action', 'ASC')
            ->findAll();

        return view('pages/roles/create', [
            'title'       => 'Crear Rol - Rifas',
            'permissions' => $permissions,
            'scopes'      => self::SCOPES,
            'statuses'    => self::STATUSES,
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'roleCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $roleId = $this->roleModel->insert([
            'name'        => $post['name'],
            'description' => $post['description'] ?? null,
            'scope'       => $post['scope'],
            'is_system'   => 0,
            'status'      => $post['status'],
        ]);

        $this->roleModel->syncPermissions($roleId, $post['permissions'] ?? []);

        return redirect()->to(base_url('roles'))
            ->with('success', 'Rol creado correctamente.');
    }

    public function edit(int $id)
    {
        $role = $this->findRoleOrFail($id);
        if (!is_array($role)) {
            return $role;
        }

        $permissions = $this->permissionModel
            ->where('status', 'ACTIVE')
            ->orderBy('module', 'ASC')
            ->orderBy('action', 'ASC')
            ->findAll();

        $permisosDelRol = $this->roleModel->getPermissions($id);
        $idsPermisosDelRol = array_column($permisosDelRol, 'id');

        return view('pages/roles/edit', [
            'title'              => 'Editar Rol - Rifas',
            'role'               => $role,
            'permissions'        => $permissions,
            'permisosDelRol'     => $idsPermisosDelRol,
            'scopes'             => self::SCOPES,
            'statuses'           => self::STATUSES,
        ]);
    }

    public function update(int $id)
    {
        $role = $this->findRoleOrFail($id);
        if (!is_array($role)) {
            return $role;
        }

        if ($role['is_system']) {
            return redirect()->to(base_url('roles'))
                ->with('error', 'No se puede editar un rol del sistema.');
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'roleUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->roleModel->update($id, [
            'name'        => $post['name'],
            'description' => $post['description'] ?? null,
            'scope'       => $post['scope'],
            'status'      => $post['status'],
        ]);

        $this->roleModel->syncPermissions($id, $post['permissions'] ?? []);

        return redirect()->to(base_url('roles'))
            ->with('success', 'Rol actualizado correctamente.');
    }

    public function delete(int $id)
    {
        $role = $this->findRoleOrFail($id);
        if (!is_array($role)) {
            return $role;
        }

        if ($role['is_system']) {
            return redirect()->to(base_url('roles'))
                ->with('error', 'No se puede eliminar un rol del sistema.');
        }

        $usersCount = $this->roleModel->countUsers($id);
        if ($usersCount > 0) {
            return redirect()->to(base_url('roles'))
                ->with('error', "No se puede eliminar el rol. Tiene {$usersCount} usuario(s) asignado(s).");
        }

        $this->roleModel->delete($id);

        return redirect()->to(base_url('roles'))
            ->with('success', 'Rol eliminado correctamente.');
    }

    public function permisos(int $id): string
    {
        $role = $this->findRoleOrFail($id);
        if (!is_array($role)) {
            return $role;
        }

        $permisos = $this->permissionModel
            ->where('status', 'ACTIVE')
            ->orderBy('module', 'ASC')
            ->orderBy('action', 'ASC')
            ->findAll();

        $permisosAgrupados = [];
        foreach ($permisos as $p) {
            $permisosAgrupados[$p['module']][] = $p;
        }

        $permisosDelRol = $this->roleModel->getPermissions($id);
        $idsPermisosDelRol = array_column($permisosDelRol, 'id');

        return view('pages/roles/permisos', [
            'title'              => 'Permisos de ' . $role['name'] . ' - Rifas',
            'role'               => $role,
            'permisosAgrupados'  => $permisosAgrupados,
            'permisosDelRol'     => $idsPermisosDelRol,
        ]);
    }

    public function actualizarPermisos(int $id)
    {
        $role = $this->findRoleOrFail($id);
        if (!is_array($role)) {
            return $role;
        }

        $permissionIds = $this->request->getPost('permisos') ?? [];

        $this->roleModel->syncPermissions($id, $permissionIds);

        return redirect()->to(base_url('roles'))
            ->with('success', 'Permisos actualizados correctamente.');
    }

    private function findRoleOrFail(int $id)
    {
        $role = $this->roleModel
            ->withDeleted()
            ->find($id);

        if (!$role || $role['deleted_at'] !== null) {
            return redirect()->to(base_url('roles'))
                ->with('error', 'El rol no existe.');
        }

        return $role;
    }
}
