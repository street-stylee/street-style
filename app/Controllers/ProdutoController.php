<?php

namespace App\Controllers;

use App\Models\ProdutoModel;
use App\Models\CarrinhoModel;
use App\Models\FavoritoModel;
use App\Models\PedidoModel;
use \mysqli;

class ProdutoController {
    protected $favoritoModel;
    protected $conexao;
    protected $produtoModel;
    protected $carrinhoModel;
    protected $pedidoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->produtoModel = new ProdutoModel($conexao);
        $this->carrinhoModel = new CarrinhoModel($conexao);
        $this->pedidoModel = new PedidoModel($conexao);
        $this->favoritoModel = new FavoritoModel($conexao);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function detalhe(int $produto_id) {
        
        $usuario_id = $_SESSION['usuario_id'] ?? null;

        $produto = $this->produtoModel->getProdutoPorId($produto_id);

        if (!$produto) {
            http_response_code(404);
            $this->carregarView('Layout/header', ['titulo_pagina' => 'Produto Não Encontrado']);
            echo "<div style='text-align:center; padding: 50px;'><h2>404</h2><p>O produto que você procura não foi encontrado.</p></div>";
            $this->carregarView('Layout/footer');
            return;
        }

        $imagens_extras = $this->produtoModel->getImagensExtras($produto_id);
        $variacoes_data = $this->produtoModel->getVariacoesPorProduto($produto_id);
        $produtos_semelhantes = $this->produtoModel->getProdutosSemelhantes($produto['categoria'], $produto_id);
        
        $avaliacoes = $this->produtoModel->getAvaliacoesPorProduto($produto_id);

        $pode_avaliar = false;
        $ja_avaliou = false;
        if ($usuario_id) {
            $pode_avaliar = $this->pedidoModel->hasUserPurchasedProduct($usuario_id, $produto_id);
            $ja_avaliou = $this->pedidoModel->hasUserReviewedProduct($usuario_id, $produto_id);
        }

        $usuario_logado = isset($usuario_id);
        $primeiro_nome = htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Convidado')[0]);
        $quantidade_carrinho = $this->carrinhoModel->contarItensCarrinho($usuario_id, session_id());

        $favoritos_ids = [];
        if ($usuario_id) {
            $favoritos_ids = $this->favoritoModel->getFavoritoIDsPorUsuario($usuario_id);
        }

        $dados = [
            'titulo_pagina' => htmlspecialchars($produto['nome']) . ' - Street Style',
            'produto' => $produto, 
            'imagens_extras' => $imagens_extras,
            'variacoes_json_data' => $this->produtoModel->getVariacoesPorProduto($produto_id),
            'tamanhos_disponiveis' => array_keys($variacoes_data),
            'primeiro_tamanho' => !empty($variacoes_data) ? array_key_first($variacoes_data) : null,
            'produtos_semelhantes' => $produtos_semelhantes,
            
            'avaliacoes' => $avaliacoes,
            'pode_avaliar' => $pode_avaliar,
            'ja_avaliou' => $ja_avaliou,
            'mensagem_avaliacao' => $_SESSION['mensagem_avaliacao'] ?? null,

            'primeiro_nome' => $primeiro_nome,
            'usuario_logado' => $usuario_logado,
            'quantidade_carrinho' => $quantidade_carrinho,
            'favoritos_ids' => $favoritos_ids
        ];
        unset($_SESSION['mensagem_avaliacao']);

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Produto/detalhe', $dados); 
        $this->carregarView('Layout/footer', $dados);
    }
    
    public function avaliar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('/');
        }
        
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        
        $produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
        $nota = filter_input(INPUT_POST, 'nota', FILTER_VALIDATE_INT);
        $titulo = trim(filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS));
        $comentario = trim(filter_input(INPUT_POST, 'comentario', FILTER_SANITIZE_SPECIAL_CHARS));

        $redirect_url = "/produto/detalhe/{$produto_id}";

        if (!$usuario_id) {
            $this->redirect('/login');
        }
        if (!$produto_id || !$nota || $nota < 1 || $nota > 5) {
            $_SESSION['mensagem_avaliacao'] = ['tipo' => 'erro', 'texto' => 'Dados inválidos. A nota é obrigatória.'];
            $this->redirect($redirect_url);
        }

        if (!$this->pedidoModel->hasUserPurchasedProduct($usuario_id, $produto_id)) {
             $_SESSION['mensagem_avaliacao'] = ['tipo' => 'erro', 'texto' => 'Você precisa ter comprado o produto para avaliá-lo.'];
             $this->redirect($redirect_url);
        }

        if ($this->pedidoModel->hasUserReviewedProduct($usuario_id, $produto_id)) {
             $_SESSION['mensagem_avaliacao'] = ['tipo' => 'erro', 'texto' => 'Você só pode avaliar este produto uma única vez.'];
             $this->redirect($redirect_url);
        }
        $sucesso = $this->produtoModel->createAvaliacao($produto_id, $usuario_id, $nota, $titulo, $comentario);

        if ($sucesso) {
            $this->produtoModel->recalcularAvaliacaoMedia($produto_id);
            $_SESSION['mensagem_avaliacao'] = ['tipo' => 'sucesso', 'texto' => 'Obrigado! Sua avaliação foi enviada.'];
        } else {
            $_SESSION['mensagem_avaliacao'] = ['tipo' => 'erro', 'texto' => 'Você já avaliou este produto.'];
        }
        
        $this->redirect($redirect_url);
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