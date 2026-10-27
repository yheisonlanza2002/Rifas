<?php

namespace App\Controllers;

use App\Models\UserModel;

class Users extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    /**
     * GET /usuarios — Listado de usuarios
     */
    public function index(): string
    {
        $db = \Config\Database::connect();

        $users = $db->table('users')
            ->select('users.*, GROUP_CONCAT(roles.name ORDER BY roles.name SEPARATOR ", ") AS role_names')
            ->join('user_roles', 'user_roles.user_id = users.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id', 'left')
            ->where('users.deleted_at', null)
            ->groupBy('users.id')
            ->orderBy('users.name', 'ASC')
            ->get()
            ->getResultArray();

        return view('pages/users/index', [
            'title' => 'Usuarios - Rifas',
            'users' => $users,
        ]);
    }

    /**
     * GET /usuarios/crear — Formulario de creación
     */
    public function create(): string
    {
        return view('pages/users/create', [
            'title' => 'Crear Usuario - Rifas',
            'roles' => $this->getActiveRoles(),
        ]);
    }

    /**
     * POST /usuarios — Guardar nuevo usuario
     */
    public function store()
    {
        if (!$this->validateData($this->request->getPost(), 'userCreate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $this->userModel->insert([
            'uuid'          => $this->generateUuid(),
            'company_id'    => session()->get('company_id'),
            'name'          => $post['name'],
            'last_name'     => $post['last_name'] ?? null,
            'email'         => $post['email'],
            'phone'         => $post['phone'] ?? null,
            'password_hash' => password_hash($post['password'], PASSWORD_DEFAULT),
            'timezone'      => 'America/Bogota',
            'locale'        => 'es_CO',
            'status'        => 'ACTIVE',
        ]);

        $userId = $this->userModel->getInsertID();
        $this->syncRoles($userId, (array) $this->request->getPost('roles'));

        return redirect()->to(base_url('usuarios'))
            ->with('success', 'Usuario creado correctamente.');
    }

    /**
     * GET /usuarios/{id}/editar — Formulario de edición
     */
    public function edit(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user || $user['deleted_at'] !== null) {
            return redirect()->to(base_url('usuarios'))
                ->with('error', 'El usuario no existe.');
        }

        $db = \Config\Database::connect();
        $userRoles = array_column(
            $db->table('user_roles')->where('user_id', $id)->get()->getResultArray(),
            'role_id'
        );

        return view('pages/users/edit', [
            'title'     => 'Editar Usuario - Rifas',
            'user'      => $user,
            'roles'     => $this->getActiveRoles(),
            'userRoles' => $userRoles,
        ]);
    }

    /**
     * POST /usuarios/{id} — Actualizar usuario
     */
    public function update(int $id)
    {
        $user = $this->userModel->find($id);
        if (!$user || $user['deleted_at'] !== null) {
            return redirect()->to(base_url('usuarios'))
                ->with('error', 'El usuario no existe.');
        }

        // Pasar el id para que is_unique ignore al usuario actual
        $data = $this->request->getPost();
        $data['id'] = $id;

        if (!$this->validateData($data, 'userUpdate')) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors())
                ->with('error', 'Revisa los errores del formulario.');
        }

        $post = $this->request->getPost();

        $updateData = [
            'name'      => $post['name'],
            'last_name' => $post['last_name'] ?? null,
            'email'     => $post['email'],
            'phone'     => $post['phone'] ?? null,
        ];

        // Contraseña solo si se diligenció
        if (!empty($post['password'])) {
            $updateData['password_hash'] = password_hash($post['password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update($id, $updateData);
        $this->syncRoles($id, (array) $this->request->getPost('roles'));

        return redirect()->to(base_url('usuarios'))
            ->with('success', 'Usuario actualizado correctamente.');
    }

    /**
     * GET /usuarios/{id}/eliminar — Soft delete (con confirmación SweetAlert2 en la vista)
     */
    public function delete(int $id)
    {
        $currentUserId = session()->get('user_id');

        if ((int) $currentUserId === $id) {
            return redirect()->to(base_url('usuarios'))
                ->with('warning', 'No puedes eliminar tu propio usuario.');
        }

        $user = $this->userModel->find($id);
        if (!$user || $user['deleted_at'] !== null) {
            return redirect()->to(base_url('usuarios'))
                ->with('error', 'El usuario no existe.');
        }

        $this->userModel->delete($id);

        return redirect()->to(base_url('usuarios'))
            ->with('success', 'Usuario eliminado correctamente.');
    }

    // --------------------------------------------------------------------
    // Helpers privados
    // --------------------------------------------------------------------

    private function getActiveRoles(): array
    {
        $db = \Config\Database::connect();
        return $db->table('roles')
            ->where('status', 'ACTIVE')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function syncRoles(int $userId, array $roleIds): void
    {
        $db = \Config\Database::connect();
        $db->table('user_roles')->where('user_id', $userId)->delete();

        $now = date('Y-m-d H:i:s');
        foreach ($roleIds as $roleId) {
            $db->table('user_roles')->insert([
                'user_id'    => $userId,
                'role_id'    => (int) $roleId,
                'created_at' => $now,
            ]);
        }
    }

    private function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0x8000, 0xbfff), mt_rand(0x8000, 0xbfff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
