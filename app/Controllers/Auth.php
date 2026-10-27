<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    protected UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function login(): string
    {
        // Si ya está logueado, redirige al dashboard
        if (session()->get('is_logged_in')) {
            return redirect()->to(base_url('dashboard'));
        }

        return view('pages/auth/login', ['title' => 'Iniciar Sesión - Rifas']);
    }

    public function attemptLogin()
    {
        // Validación centralizada en Config/Validation → $userLogin
        if (!$this->validateData($this->request->getPost(), 'userLogin')) {
            return redirect()->back()
                ->withInput()
                ->with('error', implode('<br>', $this->validator->getErrors()));
        }

        $email    = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $remember = $this->request->getPost('remember');

        $user = $this->userModel->findByEmail($email);

        // Mismo mensaje genérico para no revelar si el email existe
        if (!$user || !password_verify($password, $user['password_hash'])) {
            return redirect()->back()
                ->withInput($this->request->getPost('email') ? ['email' => $email] : [])
                ->with('error', 'Credenciales incorrectas.');
        }

        // Obtener roles
        $roles = $this->userModel->getRoles($user['id']);
        $roleNames = array_column($roles, 'name');

        // Sesión
        $session = session();
        $session->set([
            'user_id'      => $user['id'],
            'user_uuid'    => $user['uuid'],
            'user_name'    => $user['name'] . ' ' . ($user['last_name'] ?? ''),
            'user_email'   => $user['email'],
            'user_avatar'  => $user['avatar'],
            'company_id'   => $user['company_id'],
            'roles'        => $roleNames,
            'is_logged_in' => true,
        ]);

        // Actualizar último login
        $this->userModel->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);

        return redirect()->to(base_url('dashboard'))
            ->with('success', '¡Bienvenido ' . esc($user['name']) . '!');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('auth/login'))
            ->with('success', 'Sesión cerrada correctamente.');
    }

    public function register(): string
    {
        return view('pages/auth/register', ['title' => 'Registro - Rifas']);
    }

    public function forgotPassword(): string
    {
        return view('pages/auth/forgot-password', ['title' => 'Recuperar Contraseña - Rifas']);
    }

    public function resetPassword(): string
    {
        return view('pages/auth/reset-password', ['title' => 'Restablecer Contraseña - Rifas']);
    }
}
