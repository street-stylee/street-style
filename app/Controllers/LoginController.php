<?php

namespace App\Controllers;

use App\Models\UsuarioModel;
use App\Models\CarrinhoModel;
use \mysqli;

class LoginController
{

    protected $conexao;
    protected $usuarioModel;
    protected $carrinhoModel;

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
        if (isset($_SESSION['usuario_id'])) {
            $this->redirect('/perfil');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
        } else {
            $this->showForm();
        }
    }

    private function showForm($mensagem_erro = '', $email_digitado = '')
    {
        $emailLembrado = $_COOKIE['lembrar_email'] ?? '';
        if ($email_digitado === '' && $emailLembrado !== '') {
            $email_digitado = $emailLembrado;
        }

        $redirect_target = filter_input(INPUT_GET, 'redirect', FILTER_SANITIZE_URL) ?? '/';
        if ($redirect_target === 'index.php')
            $redirect_target = '/';
        $dados = [
            'titulo_pagina' => 'Login - Street Style',
            'redirect_target' => $redirect_target,
            'mensagem_erro' => $mensagem_erro,
            'email_digitado' => $email_digitado,
            'usuario_logado' => false,
            'primeiro_nome' => 'Convidado',
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho(null, session_id())
        ];
        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Auth/login', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    private function processLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $redirect_target = $_POST['redirect_target'] ?? '/';
        if ($redirect_target === 'index.php')
            $redirect_target = '/';

        $recaptcha_response = $_POST['g-recaptcha-response'] ?? null;
        $recaptcha_secret = '6Lc2-AUsAAAAADMiCgtNrJBvhIGRTr1NdTdsxi2P';

        if (empty($recaptcha_response)) {
            $this->showForm('Por favor, marque a caixa "Não sou um robô".', htmlspecialchars($email));
            return;
        }

        $url = "https://www.google.com/recaptcha/api/siteverify";
        $data = ['secret' => $recaptcha_secret, 'response' => $recaptcha_response];
        $options = ['http' => ['header' => "Content-type: application/x-www-form-urlencoded\r\n", 'method' => 'POST', 'content' => http_build_query($data)]];
        $context = stream_context_create($options);
        $result_json = @file_get_contents($url, false, $context);

        if ($result_json === FALSE) {
            $this->showForm('Erro ao verificar o CAPTCHA. Verifique a conexão do servidor.', htmlspecialchars($email));
            return;
        }
        $result = json_decode($result_json);
        if ($result === null || $result->success !== true) {
            $this->showForm('Falha na verificação (reCAPTCHA). Tente novamente.', htmlspecialchars($email));
            return;
        }

        $usuario = $this->usuarioModel->findByEmail($email);
        if ($usuario && password_verify($senha, $usuario['senha'])) {

            if (!empty($_POST['lembrar_login'])) {

                $token = bin2hex(random_bytes(32));

                setcookie(
                    'remember_token',
                    $token,
                    time() + (86400 * 30),
                    '/',
                    '',
                    false,
                    true
                );

                $sql = "UPDATE usuarios SET remember_token = ? WHERE id = ?";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bind_param("si", $token, $usuario['id']);
                $stmt->execute();
            } else {
                setcookie('remember_token', '', time() - 3600, '/');
                $sql = "UPDATE usuarios SET remember_token = NULL WHERE id = ?";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bind_param("i", $usuario['id']);
                $stmt->execute();
            }

            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];

            $this->redirect($redirect_target);
        } else {
            $this->showForm('E-mail ou senha inválidos.', htmlspecialchars($email));
        }
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