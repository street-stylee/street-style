<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\CarrinhoModel;
use \mysqli;

class Alterar_senhaController {

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

    private function mostrarFormulario($mensagem = '', $tipo_mensagem = '') {
        $usuario_id = $_SESSION['usuario_id'];
        
        $dados = [
            'titulo_pagina' => 'Alterar Senha',
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem,
            
            'usuario_logado' => true,
            'primeiro_nome' => htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? '...')[0]),
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho($usuario_id, null)
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Perfil/alterar_senha', $dados);
        $this->carregarView('Layout/footer', $dados);
    }
    
    private function processarFormulario() {
        $usuario_id = $_SESSION['usuario_id'];

        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_senha = $_POST['confirmar_senha'] ?? '';

        if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_senha)) {
            $this->mostrarFormulario("Todos os campos são obrigatórios.", 'erro');
            return;
        }
        if (strlen($nova_senha) < 6) {
            $this->mostrarFormulario("A nova senha deve ter no mínimo 6 caracteres.", 'erro');
            return;
        }
        if ($nova_senha !== $confirmar_senha) {
            $this->mostrarFormulario("A nova senha e a confirmação não coincidem.", 'erro');
            return;
        }

        $resultado = $this->usuarioModel->updateSenha($usuario_id, $senha_atual, $nova_senha);

        if ($resultado === 'sucesso') {
            $this->mostrarFormulario("Sua senha foi alterada com sucesso!", 'sucesso');
        } elseif ($resultado === 'senha_invalida') {
            $this->mostrarFormulario("A senha atual está incorreta.", 'erro');
        } else {
            $this->mostrarFormulario("Erro interno ao atualizar a senha.", 'erro');
        }
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
}