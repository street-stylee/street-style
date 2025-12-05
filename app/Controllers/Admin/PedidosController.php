<?php

namespace App\Controllers\Admin;

use \mysqli;
use App\Models\PedidoModel;

class PedidosController {

    protected $conexao;
    protected $pedidoModel;
    private $status_opcoes = ['Pendente', 'Pago', 'Em Preparação', 'Enviado', 'Entregue', 'Cancelado'];

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->pedidoModel = new PedidoModel($conexao);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $action = $_POST['action'];
            if (method_exists($this, $action)) {
                call_user_func([$this, $action]);
                exit;
            }
        }
    }

    public function index() {
        
        $status_filtro = $_GET['status'] ?? 'Todos';
        $user_id_filtro = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
        
        $pedidos = $this->pedidoModel->getTodosPedidosAdmin($status_filtro, $user_id_filtro); 

        $dados = [
            'titulo_pagina' => 'Gerenciar Pedidos',
            'pedidos' => $pedidos,
            'status_filtro' => $status_filtro,
            'user_id_filtro' => $user_id_filtro,
            'status_opcoes' => $this->status_opcoes,
            'mensagem_status' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Pedidos/index', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }
    
    public function detalhe(int $pedido_id) {
        
        $dados_pedido = $this->pedidoModel->getPedidoAdminPorId($pedido_id);
        
        if (!$dados_pedido) {
            $_SESSION['mensagem_status'] = "Erro: Pedido #{$pedido_id} não encontrado.";
            $this->redirect('/admin/pedidos');
        }
        
        $itens_pedido = $this->pedidoModel->getItensPorPedidoId($pedido_id);
        
        $dados = [
            'titulo_pagina' => 'Detalhe Pedido #' . $pedido_id,
            'pedido_id' => $pedido_id,
            'dados_pedido' => $dados_pedido,
            'itens_pedido' => $itens_pedido,
            'status_opcoes' => $this->status_opcoes,
            'mensagem_status' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Pedidos/detalhe', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function atualizar_status() {
        $pedido_id = filter_input(INPUT_POST, 'pedido_id', FILTER_VALIDATE_INT);
        $novo_status = $_POST['novo_status'] ?? null;

        if ($pedido_id && $novo_status) {
            if ($this->pedidoModel->updateStatusPedido($pedido_id, $novo_status)) {
                $_SESSION['mensagem_status'] = "Status do Pedido #{$pedido_id} atualizado para '{$novo_status}'.";
            } else {
                $_SESSION['mensagem_status'] = "Erro ao atualizar o status.";
            }
        }
        
        $this->redirect("/admin/pedidos/detalhe/{$pedido_id}");
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }

    private function redirect(string $url) {
        if (!preg_match("~^(http|https)://~", $url)) {
             if (!str_starts_with($url, '/')) $url = '/' . $url;
             $url = BASE_URL . $url;
        }
        header("Location: " . $url);
        exit;
    }
}