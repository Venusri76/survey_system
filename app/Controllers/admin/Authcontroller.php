<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AdminModel;

class AuthController extends BaseController
{
    // GET  admin/login
    // Show the login form
    public function loginForm()
    {
        if (session()->get('admin_logged_in')) {
            return redirect()->to(base_url('admin/dashboard'));
        }

        return view('admin/login');
    }

    // POST  admin/login
    // Process the login form
    public function login()
    {
        // Validate inputs
        if (! $this->validate([
            'username' => 'required|min_length[3]',
            'password' => 'required|min_length[4]',
        ])) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');

        $admin = model(AdminModel::class)->findByUsername($username);

        // Check admin exists and password matches
        if (! $admin || ! password_verify($password, $admin['password'])) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Invalid username or password.');
        }

        session()->set([
            'admin_logged_in' => true,
            'admin_id'        => $admin['id'],
            'admin_username'  => $admin['username'],
        ]);

        return redirect()->to(base_url('admin/dashboard'))
            ->with('success', 'Welcome, ' . esc($admin['username']) . '!');
    }
    // GET  admin/logout
    // Destroy session and redirect to login
    public function logout()
    {
        session()->destroy();

        return redirect()->to(base_url('admin/login'))
            ->with('success', 'You have been logged out.');
    }
}