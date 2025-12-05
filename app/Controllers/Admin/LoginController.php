<?php

namespace App\Controllers\Admin;

use App\Models\UsuarioModel;
use \mysqli;

class LoginController {

    protected $conexao;
    protected $usuarioModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->usuarioModel = new UsuarioModel($conexao);
    }

    public function index($mensagem_erro = '') {
        if (isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/dashboard');
        }
        
        $dados = [
            'titulo_pagina' => 'Login Administrativo',
            'mensagem_erro' => $mensagem_erro
        ];

        $this->carregarView('Admin/login', $dados);
    }

    public function processar() {
        if (isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/dashboard');
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $admin = $this->usuarioModel->findAdminByEmail($email);

        if ($admin && password_verify($senha, $admin['senha'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nome'] = $admin['nome'];
            $this->redirect('/admin/dashboard');
        } else {
            $this->index('E-mail ou senha inválidos.');
        }
    }
    
    public function logout() {
        unset($_SESSION['admin_id']);
        unset($_SESSION['admin_nome']);
        $this->redirect('/admin/login');
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }

    private function redirect(string $url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }
}