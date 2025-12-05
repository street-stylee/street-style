<?php

namespace App\Controllers;
use App\Models\FavoritoModel;

use App\Models\ProdutoModel;
use App\Models\CarrinhoModel;
use \mysqli;

class BuscarController {

    protected $conexao;
    protected $produtoModel;
    protected $carrinhoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->produtoModel = new ProdutoModel($this->conexao);
        $this->carrinhoModel = new CarrinhoModel($conexao);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        
        $termo_busca = trim($_GET['q'] ?? '');
        $resultados = [];
        $total_resultados = 0;

        if (!empty($termo_busca)) {
            $resultados = $this->produtoModel->buscarProdutos($termo_busca);
            $total_resultados = count($resultados);
        }

        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $dados_layout = [
            'usuario_logado' => isset($usuario_id),
            'primeiro_nome' => htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Convidado')[0]),
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho($usuario_id, session_id())
        ];

        $dados_view = [
            'titulo_pagina' => 'Resultados para: ' . htmlspecialchars($termo_busca),
            'termo_busca' => $termo_busca,
            'resultados' => $resultados,
            'total_resultados' => $total_resultados,
        ];

        $dados = array_merge($dados_layout, $dados_view);

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Busca/index', $dados); 
        $this->carregarView('Layout/footer', $dados);
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