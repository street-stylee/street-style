<?php

namespace App\Controllers\Admin;

use \mysqli;
use App\Models\ContatoModel;

class ContatoController {

    protected $conexao;
    protected $contatoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->contatoModel = new ContatoModel($conexao);
        
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
        }
    }

    public function index() {
        $dados = [
            'titulo_pagina' => 'Mensagens de Contato',
            'mensagens' => $this->contatoModel->getMensagens(),
            'mensagem' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Contato/index', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function excluir(int $id) {
        if ($this->contatoModel->deleteMensagem($id)) {
            $_SESSION['mensagem_status'] = ['tipo' => 'sucesso', 'texto' => 'Mensagem excluída com sucesso.'];
        } else {
            $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => 'Erro ao excluir a mensagem.'];
        }
        $this->redirect('/admin/contato');
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