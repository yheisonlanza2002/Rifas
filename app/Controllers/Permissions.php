<?php

namespace App\Controllers;

use App\Models\PermissionModel;

class Permissions extends BaseController
{
    protected PermissionModel $permissionModel;

    private const STATUSES = [
        'ACTIVE'   => 'Activo',
        'INACTIVE' => 'Inactivo',
    ];

    private const GUARDS = [
        'web' => 'Web',
        'api' => 'API',
    ];

    public function __construct()
    {
        $this->permissionModel = new PermissionModel();
    }

    public function index(): string
    {
        $permissions = $this->permissionModel
            ->orderBy('module', 'ASC')
            ->orderBy('action', 'ASC')
            ->findAll();

        $permisosConContadores = array_map(function ($permiso) {
            $permiso['total_roles'] = $this->permissionModel->countRoles($permiso['id']);
            return $permiso;
        }, $permissions);

        $modules = $this->permissionModel->getModules();

        return view('pages/permissions/index', [
            'title'      => 'Permisos - Rifas',
            'permissions' => $permisosConContadores,
            'modules'    => $modules,
            'statuses'   => self::STATUSES,
        ]);
    }

    public function create(): string
    {
        $modules = $this->permissionModel->getModules();

        return view('pages/permissions/create', [
            'title'    => 'Crear Permiso - Rifas',
            'modules'  => $modules,
            'statuses' => self::STATUSES,
            'guards'   => self::GUARDS,
        ]);
    }

    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'permissionCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->permissionModel->insert([
            'name'        => $post['name'],
            'module'      => $post['module'],
            'action'      => $post['action'],
            'description' => $post['description'] ?? null,
            'guard_name'  => $post['guard_name'],
            'status'      => $post['status'],
        ]);

        return redirect()->to(base_url('permisos'))
            ->with('success', 'Permiso creado correctamente.');
    }

    public function edit(int $id)
    {
        $permission = $this->findPermissionOrFail($id);
        if (!is_array($permission)) {
            return $permission;
        }

        $modules = $this->permissionModel->getModules();

        return view('pages/permissions/edit', [
            'title'      => 'Editar Permiso - Rifas',
            'permission' => $permission,
            'modules'    => $modules,
            'statuses'   => self::STATUSES,
            'guards'     => self::GUARDS,
        ]);
    }

    public function update(int $id)
    {
        $permission = $this->findPermissionOrFail($id);
        if (!is_array($permission)) {
            return $permission;
        }

        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'permissionUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->permissionModel->update($id, [
            'name'        => $post['name'],
            'module'      => $post['module'],
            'action'      => $post['action'],
            'description' => $post['description'] ?? null,
            'guard_name'  => $post['guard_name'],
            'status'      => $post['status'],
        ]);

        return redirect()->to(base_url('permisos'))
            ->with('success', 'Permiso actualizado correctamente.');
    }

    public function delete(int $id)
    {
        $permission = $this->findPermissionOrFail($id);
        if (!is_array($permission)) {
            return $permission;
        }

        $rolesCount = $this->permissionModel->countRoles($id);
        if ($rolesCount > 0) {
            return redirect()->to(base_url('permisos'))
                ->with('error', "No se puede eliminar el permiso. Está asignado a {$rolesCount} rol(es).");
        }

        $this->permissionModel->delete($id);

        return redirect()->to(base_url('permisos'))
            ->with('success', 'Permiso eliminado correctamente.');
    }

    private function findPermissionOrFail(int $id)
    {
        $permission = $this->permissionModel
            ->withDeleted()
            ->find($id);

        if (!$permission || $permission['deleted_at'] !== null) {
            return redirect()->to(base_url('permisos'))
                ->with('error', 'El permiso no existe.');
        }

        return $permission;
    }
}
