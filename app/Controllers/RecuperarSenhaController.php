<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\CarrinhoModel;
use \mysqli;
use App\Services\EmailService;

class RecuperarSenhaController
{

    protected $conexao;
    protected $usuarioModel;
    protected $carrinhoModel;

    private $email_config = [
        'remetente' => 'nao-responda@streetstyle.com',
        'assunto' => 'Recuperação de Senha - Street Style'
    ];

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
        $this->usuarioModel = new UsuarioModel($conexao);
        $this->carrinhoModel = new CarrinhoModel($conexao);
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processarSolicitacao();
        } else {
            $this->showForm();
        }
    }

    private function showForm($mensagem = '', $tipo_mensagem = '', $email = '')
    {
        $dados = [
            'titulo_pagina' => 'Recuperar Senha',
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem,
            'email' => $email,
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho(null, session_id())
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Auth/recuperar_senha_solicitar', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    private function processarSolicitacao()
    {
        $email = trim($_POST['email'] ?? '');

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->showForm('Por favor, insira um e-mail válido.', 'error', $email);
            return;
        }

        $usuario = $this->usuarioModel->findByEmail($email);

        if ($usuario) {
            $token = bin2hex(random_bytes(32));

            $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));

            $this->usuarioModel->savePasswordResetToken($usuario['id'], $token, $expira);

            $host = $_SERVER['HTTP_HOST'];

            $base = BASE_URL;

            $reset_link = "http://{$host}{$base}/resetarsenha?token={$token}&email=" . urlencode($email);

            $mensagem_email = "Você solicitou a recuperação de senha. Clique no link abaixo para redefini-la:\n\n" . $reset_link . "\n\nO link expira em 1 hora.";
        
            EmailService::sendEmail(
                $email,
                $this->email_config['assunto'],
                nl2br($mensagem_email)
            );

            $this->showForm('Se o e-mail estiver cadastrado, você receberá um link para redefinir a senha.', 'sucesso');
            return;

        } else {
            $this->showForm('Se o e-mail estiver cadastrado, você receberá um link para redefinir a senha.', 'sucesso');
            return;
        }
    }

    public function resetarSenha()
    {
        $token = $_GET['token'] ?? '';
        $email = $_GET['email'] ?? '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processarReset();
            return;
        }

        if (empty($token) || empty($email)) {
            $this->showResetForm('Link de redefinição inválido.', 'error');
            return;
        }

        $token_valido = $this->usuarioModel->validatePasswordResetToken($email, $token);

        if (!$token_valido) {
            $this->showResetForm('O link de redefinição é inválido ou expirou.', 'error');
            return;
        }

        $this->showResetForm('Insira sua nova senha.', 'info', $email, $token);
    }

    private function showResetForm($mensagem = '', $tipo_mensagem = '', $email = '', $token = '')
    {
        $dados = [
            'titulo_pagina' => 'Redefinir Senha',
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem,
            'email' => $email,
            'token' => $token,
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho(null, session_id())
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Auth/recuperar_senha_reset', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    private function processarReset()
    {
        $email = trim($_POST['email'] ?? '');
        $token = $_POST['token'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirma_senha = $_POST['confirma_senha'] ?? '';

        $token_valido = $this->usuarioModel->validatePasswordResetToken($email, $token);
        if (!$token_valido) {
            $this->showResetForm('O token expirou. Por favor, solicite um novo link.', 'error');
            return;
        }

        if ($nova_senha !== $confirma_senha) {
            $this->showResetForm('As senhas não coincidem.', 'error', $email, $token);
            return;
        }

        $regex_forca_senha = '/^(?=.*[A-Z])(?=.*\d).{8,}$/';
        if (!preg_match($regex_forca_senha, $nova_senha)) {
            $mensagem_erro = 'A senha deve ter no mínimo 8 caracteres, incluindo pelo menos uma letra maiúscula e um número.';
            $this->showResetForm($mensagem_erro, 'error', $email, $token);
            return;
        }

        if ($this->usuarioModel->updatePasswordAndInvalidateToken($email, $nova_senha, $token)) {
            $this->showResetForm('Sua senha foi redefinida com sucesso! Você pode fazer login agora.', 'sucesso');
            header("Refresh: 5; url=" . BASE_URL . "/login");
            return;
        } else {
            $this->showResetForm('Erro interno ao redefinir a senha.', 'error', $email, $token);
            return;
        }
    }

    private function carregarView(string $caminho, array $dados = [])
    {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
}