<?php

namespace App\Controllers;

use App\Models\FavoritoModel;
use App\Models\UsuarioModel;
use App\Models\CarrinhoModel;
use \mysqli;

class PerfilController {
    protected $favoritoModel;
    protected $conexao;
    protected $usuarioModel;
    protected $carrinhoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->usuarioModel = new UsuarioModel($conexao);
        $this->favoritoModel = new FavoritoModel($conexao);
        $this->carrinhoModel = new CarrinhoModel($conexao);
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: /pj/login");
            exit;
        }
        $usuario_id = $_SESSION['usuario_id'];

        $dados_usuario = $this->usuarioModel->findDadosById($usuario_id);

        if (!$dados_usuario) {
            session_destroy();
            header("Location: /pj/login?erro=usuario_nao_encontrado");
            exit;
        }
        $favoritos = $this->favoritoModel->getFavoritosComDetalhes($usuario_id);

        $dados = [
            'titulo_pagina' => 'Meu Perfil',
            'dados_usuario' => $dados_usuario,
            'favoritos' => $favoritos,
            'usuario_logado' => true,
            'primeiro_nome' => htmlspecialchars(explode(' ', $dados_usuario['nome'])[0]),
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho($usuario_id, null)
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Perfil/index', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
}