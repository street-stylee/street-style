<?php

namespace App\Controllers\Admin;
use \mysqli;
use App\Models\ProdutoModel;

class AvaliacoesController {

    protected $conexao;
    protected $produtoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->produtoModel = new ProdutoModel($conexao);
        
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
        }
    }

    public function index() {
        $dados = [
            'titulo_pagina' => 'Gerenciar Avaliações',
            'avaliacoes' => $this->produtoModel->getTodasAvaliacoes(),
            'mensagem' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Avaliacoes/index', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function excluir(int $id) {
        if ($this->produtoModel->deleteAvaliacao($id)) {
            $this->redirectComSucesso('/admin/avaliacoes', 'Avaliação excluída com sucesso.');
        } else {
            $this->redirectComErro('/admin/avaliacoes', 'Erro ao excluir a avaliação.');
        }
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
    private function redirect(string $url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }
    private function redirectComErro(string $url, string $mensagem) {
        $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => $mensagem];
        $this->redirect($url);
    }
    private function redirectComSucesso(string $url, string $mensagem) {
        $_SESSION['mensagem_status'] = ['tipo' => 'sucesso', 'texto' => $mensagem];
        $this->redirect($url);
    }
}