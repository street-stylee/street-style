<?php

namespace App\Controllers;
use App\Models\FavoritoModel;
use App\Models\ProdutoModel;
use App\Models\CarrinhoModel;
use \mysqli;

class ProdutosController {
    
    protected $favoritoModel;
    protected $conexao;
    protected $produtoModel;
    protected $carrinhoModel;
    
    private $categorias = ['Camisetas', 'Calças', 'Shorts', 'Moletons'];
    private $titulos = ['Camisetas', 'Calças', 'Shorts', 'Moletons/Casacos'];
    private $ancoras = ['camisas', 'calca', 'short', 'moletom'];
    private $icones = ['camisa.png', 'calca.png', 'shorts.png', 'moletom.png'];

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->produtoModel = new ProdutoModel($this->conexao);
        $this->carrinhoModel = new CarrinhoModel($this->conexao);
        $this->favoritoModel = new FavoritoModel($this->conexao);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index() {
        
        $produtos_por_categoria = [];
        for ($i = 0; $i < count($this->categorias); $i++) {
            $categoria_nome = $this->categorias[$i];
            
            $produtos_por_categoria[] = [
                'titulo' => $this->titulos[$i],
                'ancora' => $this->ancoras[$i],
                'produtos' => $this->produtoModel->getProdutosPorCategoria($categoria_nome)
            ];
        }

        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $usuario_logado = isset($usuario_id);
        $primeiro_nome = $usuario_logado ? htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Usuário')[0]) : 'Convidado';

        if (!isset($_SESSION['carrinho_session_id'])) {
            $_SESSION['carrinho_session_id'] = session_id();
        }
        $quantidade_carrinho = $this->carrinhoModel->contarItensCarrinho($usuario_id, $_SESSION['carrinho_session_id']);

        
        $favoritos_ids = [];
        if ($usuario_id) {
            $favoritos_ids = $this->favoritoModel->getFavoritoIDsPorUsuario($usuario_id);
        }

        $dados = [
            'titulo_pagina' => 'Lista de Produtos',
            'produtos_por_categoria' => $produtos_por_categoria,
            'categorias_menu' => [
                'titulos' => $this->titulos,
                'ancoras' => $this->ancoras,
                'icones' => $this->icones
            ],
            'primeiro_nome' => $primeiro_nome,
            'usuario_logado' => $usuario_logado,
            'quantidade_carrinho' => $quantidade_carrinho,
            'favoritos_ids' => $favoritos_ids
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Produtos/lista', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
}