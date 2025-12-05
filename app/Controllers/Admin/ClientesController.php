<?php

namespace App\Controllers\Admin;

use \mysqli;
use App\Models\UsuarioModel;

class ClientesController {

    protected $conexao;
    protected $usuarioModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->usuarioModel = new UsuarioModel($conexao);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_id'])) {
            header("Location: " . BASE_URL . "/admin/login");
            exit;
        }
    }

    public function index() {
        
        $termo_busca = trim($_GET['busca'] ?? '');
        
        $usuarios = $this->usuarioModel->getClientesComContagemPedidos($termo_busca);

        $dados = [
            'titulo_pagina' => 'Gerenciar Clientes',
            'usuarios' => $usuarios,
            'total_usuarios' => count($usuarios),
            'termo_busca' => $termo_busca
        ];

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Clientes/index', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
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