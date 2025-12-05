<?php

namespace App\Controllers;

use App\Models\UsuarioModel;

class ResetarSenhaController
{
    private $db;
    private $usuarioModel;

    public function __construct($conexao)
    {
        $this->db = $conexao;
        $this->usuarioModel = new UsuarioModel($conexao);
    }

    public function index()
    {
        if (!isset($_GET['token']) || !isset($_GET['email'])) {
            echo "Link inválido.";
            return;
        }

        $token = $_GET['token'];
        $email = $_GET['email'];

        if (!$this->usuarioModel->validarTokenReset($email, $token)) {
            echo "Link expirado ou inválido.";
            return;
        }

        require ROOT . "/app/Views/Auth/resetar_senha.php";
    }

    public function salvar()
    {
        if (!isset($_POST['email']) || !isset($_POST['token']) || !isset($_POST['senha'])) {
            echo "Requisição inválida.";
            return;
        }

        $email = $_POST['email'];
        $token = $_POST['token'];
        $senha = $_POST['senha'];
        $min_length = 8;

        $regex = '/^(?=.*[A-Z])(?=.*\d).{8,}$/';
        if (!preg_match($regex, $senha)) {
            echo "A senha deve ter no mínimo {$min_length} caracteres, incluindo pelo menos uma letra maiúscula e um número.";
            return;
        }

        if (!$this->usuarioModel->validarTokenReset($email, $token)) {
            echo "Token expirado ou inválido.";
            return;
        }

        if ($this->usuarioModel->atualizarSenha($email, $senha)) {

            $this->usuarioModel->limparTokenReset($email);

            header("Location: " . BASE_URL . "/login?sucesso=senha_alterada");
            exit;

        } else {
            echo "Erro ao salvar a nova senha.";
        }
    }

}
