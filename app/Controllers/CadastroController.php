<?php

namespace App\Controllers; 

use App\Models\UsuarioModel;
use App\Models\CarrinhoModel;
use \mysqli;

class CadastroController {

    protected $conexao;
    protected $usuarioModel;
    protected $carrinhoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->usuarioModel = new UsuarioModel($conexao);
        $this->carrinhoModel = new CarrinhoModel($conexao);
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processCadastro();
        } else {
            $this->showForm();
        }
    }

    private function showForm($mensagem = '', $tipo_mensagem = '', $nome = '', $email = '') {
        
        $dados = [
            'titulo_pagina' => 'Cadastro - Street Style',
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem,
            'nome' => $nome,
            'email' => $email,
            'usuario_logado' => false,
            'primeiro_nome' => 'Convidado',
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho(null, session_id())
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Auth/cadastro', $dados); 
        $this->carregarView('Layout/footer', $dados);
    }

    private function processCadastro() {
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $senha_confirma = $_POST['senha_confirma'] ?? ''; 

        $recaptcha_response = $_POST['g-recaptcha-response'] ?? null;
        $recaptcha_secret = '6Lc2-AUsAAAAADMiCgtNrJBvhIGRTr1NdTdsxi2P'; 

        if (empty($recaptcha_response)) {
            $this->showForm('Por favor, marque a caixa "Não sou um robô".', 'error', $nome, $email);
            return;
        }
        
        $url = "https://www.google.com/recaptcha/api/siteverify";
        $data = ['secret' => $recaptcha_secret, 'response' => $recaptcha_response];
        $options = ['http' => ['header'  => "Content-type: application/x-www-form-urlencoded\r\n", 'method'  => 'POST', 'content' => http_build_query($data)]];
        $context = stream_context_create($options);
        $result_json = @file_get_contents($url, false, $context);
        
        if ($result_json === FALSE) {
            $this->showForm('Erro ao verificar o CAPTCHA. Verifique a conexão do servidor.', 'error', $nome, $email);
            return;
        }
        $result = json_decode($result_json);
        if ($result === null || $result->success !== true) {
            $this->showForm('Falha na verificação (reCAPTCHA). Tente novamente.', 'error', $nome, $email);
            return;
        }

        if (empty($nome) || empty($email) || empty($senha)) {
            $this->showForm('Todos os campos são obrigatórios.', 'error', $nome, $email);
            return;
        }
        if ($senha !== $senha_confirma) {
            $this->showForm('As senhas não coincidem.', 'error', $nome, $email);
            return;
        }

        $regex_forca_senha = '/^(?=.*[A-Z])(?=.*\d).{8,}$/';

        if (!preg_match($regex_forca_senha, $senha)) {
            $mensagem_erro = 'A senha deve ter no mínimo 8 caracteres, incluindo pelo menos uma letra maiúscula e um número.';
            $this->showForm($mensagem_erro, 'error', $nome, $email);
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->showForm('Formato de e-mail inválido.', 'error', $nome, $email);
            return;
        }
        if ($this->usuarioModel->findByEmail($email)) {
            $this->showForm('Este e-mail já está cadastrado.', 'error', $nome, $email);
            return;
        }
        
        if ($this->usuarioModel->createUser($nome, $email, $senha, 'cliente')) {
            $dados_sucesso = [
                'titulo_pagina' => 'Sucesso!',
                'mensagem' => 'Cadastro realizado com sucesso! Redirecionando para o login...',
                'tipo_mensagem' => 'sucesso',
                'usuario_logado' => false,
                'primeiro_nome' => 'Convidado',
                'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho(null, session_id())
            ];
            
            header("Refresh: 3; url=" . BASE_URL . "/login");

            $this->carregarView('Layout/header', $dados_sucesso);
            $this->carregarView('Auth/cadastro', $dados_sucesso); 
            $this->carregarView('Layout/footer', $dados_sucesso);
        } else {
            $this->showForm('Erro interno ao cadastrar. Tente novamente.', 'error', $nome, $email);
        }
    }
    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }

    private function redirect(string $url) {
        if (!str_starts_with($url, '/')) {
             $url = '/' . $url;
        }
        header("Location: ". BASE_URL . $url);
        exit;
    }
}