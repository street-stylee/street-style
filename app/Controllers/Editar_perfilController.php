<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\CarrinhoModel;
use \mysqli;

class Editar_perfilController {

    protected $conexao;
    protected $usuarioModel;
    protected $carrinhoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->usuarioModel = new UsuarioModel($conexao);
        $this->carrinhoModel = new CarrinhoModel($conexao);
    }

    public function index() {
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: /pj/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $this->processarFormulario();
        } else {
            $this->mostrarFormulario();
        }
    }

    private function mostrarFormulario($mensagem = '', $tipo_mensagem = '', $dados_form = null) {
        $usuario_id = $_SESSION['usuario_id'];
        
        if ($dados_form === null) {
            $dados_form = $this->usuarioModel->findDadosById($usuario_id);
            if (!$dados_form) {
                 $mensagem = "Erro: Seu perfil não foi encontrado.";
                 $tipo_mensagem = 'erro';
            }
        }

        $dados = [
            'titulo_pagina' => 'Editar Perfil',
            'dados_usuario' => $dados_form,
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem,
            
            'usuario_logado' => true,
            'primeiro_nome' => htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? '...')[0]),
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho($usuario_id, null)
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Perfil/editar', $dados);
        $this->carregarView('Layout/footer', $dados);
    }
    
    private function processarFormulario() {
        $usuario_id = $_SESSION['usuario_id'];

        $novo_nome = trim($_POST['nome'] ?? '');
        $novo_email = trim($_POST['email'] ?? '');
        $novo_telefone = trim($_POST['telefone'] ?? '');
        $novo_endereco = trim($_POST['endereco'] ?? '');

        $dados_form = [
            'nome' => $novo_nome, 
            'email' => $novo_email, 
            'telefone' => $novo_telefone, 
            'endereco' => $novo_endereco
        ];

        if (!filter_var($novo_email, FILTER_VALIDATE_EMAIL)) {
            $this->mostrarFormulario("O e-mail fornecido é inválido.", 'erro', $dados_form);
            return;
        }
        if (empty($novo_nome) || strlen($novo_nome) < 3) {
            $this->mostrarFormulario("O nome precisa ter pelo menos 3 caracteres.", 'erro', $dados_form);
            return;
        }

        $sucesso = $this->usuarioModel->updateDados($usuario_id, $novo_nome, $novo_email, $novo_telefone, $novo_endereco);

        if ($sucesso) {
            $_SESSION['usuario_nome'] = $novo_nome; 
            $this->mostrarFormulario("Seu perfil foi atualizado com sucesso!", 'sucesso', $dados_form);
        } else {
            $this->mostrarFormulario("Erro ao atualizar o perfil no banco de dados.", 'erro', $dados_form);
        }
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
}