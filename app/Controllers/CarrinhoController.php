<?php

namespace App\Controllers;

use App\Models\CarrinhoModel;
use \mysqli;
use Exception;

class CarrinhoController
{

    protected $conexao;
    protected $carrinhoModel;

    public function __construct(mysqli $conexao)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['carrinho_session_id'])) {

            if (!isset($_COOKIE['carrinho_id'])) {
                $id = bin2hex(random_bytes(20));
                setcookie("carrinho_id", $id, time() + (86400 * 30), "/", "", false, true);
                $_SESSION['carrinho_session_id'] = $id;
            } else {
                $_SESSION['carrinho_session_id'] = $_COOKIE['carrinho_id'];
            }
        }

        $this->conexao = $conexao;
        $this->carrinhoModel = new CarrinhoModel($this->conexao);

        if (isset($_SESSION['usuario_id']) && isset($_SESSION['carrinho_session_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
            $session_id_antiga = $_SESSION['carrinho_session_id'];

            if ($this->carrinhoModel->migrarCarrinho($usuario_id, $session_id_antiga)) {
                unset($_SESSION['carrinho_session_id']);
            }
        }
    }
    public function index()
    {

        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $session_id = $usuario_id ? null : ($_SESSION['carrinho_session_id'] ?? null);

        $carrinho_itens = $this->carrinhoModel->getItensCarrinhoComDetalhes($usuario_id, $session_id);

        $total_geral = 0;
        foreach ($carrinho_itens as $item) {
            $total_geral += $item['preco_unitario'] * $item['quantidade'];
        }

        $dados = [
            'titulo_pagina' => 'Seu Carrinho',
            'carrinho_itens' => $carrinho_itens,
            'total_geral' => $total_geral,
            'usuario_logado' => $usuario_id !== null,

            'primeiro_nome' => htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Convidado')[0]),

            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho($usuario_id, $session_id)
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Carrinho/index', $dados); 
        $this->carregarView('Layout/footer', $dados);

        $this->conexao->close();
    }
    public function adicionar()
    {

        $produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
        $variacao_id = filter_input(INPUT_POST, 'variacao_id', FILTER_VALIDATE_INT);
        $quantidade = filter_input(INPUT_POST, 'quantidade', FILTER_VALIDATE_INT);

        if (!$produto_id || !$variacao_id || $quantidade <= 0) {
            $this->redirect('/produto/detalhe/' . ($produto_id ?? 0) . '?status=error_data');
        }

        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $session_id = $usuario_id ? null : ($_SESSION['carrinho_session_id'] ?? null);

        $resultado = $this->carrinhoModel->adicionarItem($produto_id, $variacao_id, $quantidade, $usuario_id, $session_id);

        if ($resultado === true) {
            $this->redirect('/carrinho?status=added');
        } elseif ($resultado === 'error_estoque') {
            $this->redirect('/produto/detalhe/' . $produto_id . '?status=error_estoque');
        } else {
            $this->redirect('/produto/detalhe/' . $produto_id . '?status=error_db');
        }

        $this->conexao->close();
    }
    public function gerenciar()
    {

        $acao = $_GET['acao'] ?? null;
        $item_id = filter_input(INPUT_GET, 'item_id', FILTER_VALIDATE_INT);

        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $session_id = $usuario_id ? null : ($_SESSION['carrinho_session_id'] ?? null);

        if ($acao === 'limpar') {
            $sucesso = $this->carrinhoModel->limparCarrinho($usuario_id, $session_id);
            $this->redirect('/carrinho' . ($sucesso ? '' : '?status=error_limpar'));
        }

        if ($item_id && in_array($acao, ['excluir', 'diminuir', 'aumentar'])) {
            $resultado = $this->carrinhoModel->gerenciarItem($item_id, $acao);

            if ($resultado === true) {
                $this->redirect('/carrinho');
            } elseif ($resultado === 'error_estoque') {
                $this->redirect('/carrinho?status=error_estoque_aumento');
            } else {
                $this->redirect('/carrinho?status=error_db');
            }
        }

        $this->redirect('/carrinho');
        $this->conexao->close();
    }
    private function carregarView(string $caminho, array $dados = [])
    {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }

    private function redirect(string $url)
    {
        if (!str_starts_with($url, '/')) {
            $url = '/' . $url;
        }
        header("Location: " . BASE_URL . $url);
        exit;
    }
}