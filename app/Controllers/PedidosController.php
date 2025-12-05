<?php

namespace App\Controllers;

use App\Models\PedidoModel;
use App\Models\CarrinhoModel;
use \mysqli;

class PedidosController {

    protected $conexao;
    protected $pedidoModel;
    protected $carrinhoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->pedidoModel = new PedidoModel($conexao);
        $this->carrinhoModel = new CarrinhoModel($conexao);
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /pj/login');
            exit;
        }
        $usuario_id = $_SESSION['usuario_id'];

        $pedidos = $this->pedidoModel->getPedidosPorUsuario($usuario_id);

        $dados = [
            'titulo_pagina' => 'Meus Pedidos',
            'pedidos' => $pedidos,
            'usuario_logado' => true,
            'primeiro_nome' => htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Usuário')[0]),
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho($usuario_id, null)
        ];
        
        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Pedidos/lista', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    public function sucesso(int $pedido_id) {
        if (!isset($_SESSION['usuario_id'])) {
            header('Location: /pj/login');
            exit;
        }
        $usuario_id = $_SESSION['usuario_id'];

        $dados_pedido = $this->pedidoModel->getPedidoPorId($pedido_id, $usuario_id);
        
        if (!$dados_pedido) {
            header('Location: /pj/pedidos');
            exit;
        }
        
        $itens_pedido = $this->pedidoModel->getItensPorPedidoId($pedido_id);

        $dados = [
            'titulo_pagina' => 'Pedido Concluído #' . $pedido_id,
            'dados_pedido' => $dados_pedido,
            'itens_pedido' => $itens_pedido,
            'pedido_id' => $pedido_id,
            'usuario_logado' => true,
            'primeiro_nome' => htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Usuário')[0]),
            'quantidade_carrinho' => 0
        ];
        
        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Pedidos/sucesso', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
}