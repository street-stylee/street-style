<?php

namespace App\Controllers;

use App\Models\CarrinhoModel;
use \mysqli;

class SobreController {

    protected $conexao;
    protected $carrinhoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->carrinhoModel = new CarrinhoModel($conexao);
    }

    public function index() {
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $dados = [
            'titulo_pagina' => 'Sobre Nós - Street Style',
            'usuario_logado' => isset($usuario_id),
            'primeiro_nome' => htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Convidado')[0]),
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho($usuario_id, session_id())
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Sobre/index', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
}