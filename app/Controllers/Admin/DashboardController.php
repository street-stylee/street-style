<?php

namespace App\Controllers\Admin;

use \mysqli;
use App\Models\RelatorioModel;

class DashboardController
{

    protected $conexao;
    protected $relatorioModel;

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
        $this->relatorioModel = new RelatorioModel($conexao);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
        }
    }

    public function index()
    {

        $total_clientes = $this->relatorioModel->contarTotalClientes();
        $total_pedidos_pendentes = $this->relatorioModel->contarTotalPedidos('Pendente');
        $faturamento_total = $this->relatorioModel->getVolumeTotalVendas();
        $total_estoque = $this->relatorioModel->contarTotalEstoque();

        $dados_grafico_status = $this->relatorioModel->getPedidosPorStatus();
        $dados_grafico_vendas = $this->relatorioModel->getVendasUltimos6Meses();
        $dados_grafico_clientes = $this->relatorioModel->getNovosClientesUltimos6Meses();
        $dados_grafico_estoque = $this->relatorioModel->getTopProdutosEstoque(5);

        $dados = [
            'titulo_pagina' => 'Dashboard',
            'total_clientes' => $total_clientes,
            'total_pedidos_pendentes' => $total_pedidos_pendentes,
            'faturamento_total' => $faturamento_total,
            'total_estoque' => $total_estoque,

            'grafico_status' => $dados_grafico_status,
            'grafico_vendas' => $dados_grafico_vendas,
            'grafico_clientes' => $dados_grafico_clientes,
            'grafico_estoque' => $dados_grafico_estoque
        ];

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/dashboard', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    private function carregarView(string $caminho, array $dados = [])
    {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }

    private function redirect(string $url)
    {
        header("Location: " . BASE_URL . $url);
        exit;
    }
}