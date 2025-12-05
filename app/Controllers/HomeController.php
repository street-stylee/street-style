<?php

namespace App\Controllers;

use App\Models\CarrinhoModel;
use App\Models\ProdutoModel;
use App\Models\FavoritoModel;
use App\Models\ConfiguracaoModel;
use App\Models\CarrosselModel;
use \mysqli;

class HomeController
{
    protected $favoritoModel;
    protected $conexao;
    protected $produtoModel;
    protected $carrinhoModel;
    protected $carrosselModel;
    protected $configModel;

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;

        $this->produtoModel = new ProdutoModel($conexao);
        $this->carrinhoModel = new CarrinhoModel($conexao);
        $this->carrosselModel = new CarrosselModel($conexao);
        $this->configModel = new ConfiguracaoModel($conexao);
        $this->favoritoModel = new FavoritoModel($conexao);

        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $usuario_logado = isset($usuario_id);
        $primeiro_nome = htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Convidado')[0]);
        $quantidade_carrinho = $this->carrinhoModel->contarItensCarrinho($usuario_id, session_id());

        $favoritos_ids = [];
        if ($usuario_id) {
            $favoritos_ids = $this->favoritoModel->getFavoritoIDsPorUsuario($usuario_id);
        }
        
        $slides_carrossel = $this->carrosselModel->getSlides();
        $produtos_novidade = $this->produtoModel->getProdutosNovidade(9);
        $produtos_promocao = $this->produtoModel->getProdutosEmPromocao(9);
        $configuracoes = $this->configModel->getAllSettings();
        
        $dados = [
            'titulo_pagina' => 'Street Style | A melhor loja masculina para você!',
            'usuario_logado' => $usuario_logado,
            'primeiro_nome' => $primeiro_nome,
            'quantidade_carrinho' => $quantidade_carrinho,
            'produtos_novidade' => $produtos_novidade,
            'produtos_promocao' => $produtos_promocao,
            'slides_carrossel' => $slides_carrossel,
            'configuracoes' => $configuracoes,
            
            'favoritos_ids' => $favoritos_ids
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Home/index', $dados);
        $this->carregarView('Layout/footer', $dados);
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