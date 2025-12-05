<?php

namespace App\Controllers\Admin;

use \mysqli;
use App\Models\UsuarioModel;

class UsuariosController {

    protected $conexao;
    protected $usuarioModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->usuarioModel = new UsuarioModel($this->conexao);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
        }
    }

    public function index() {
        $dados = [
            'titulo_pagina' => 'Gerenciar Usuários Admin',
            'usuarios' => $this->usuarioModel->getAdmins(),
            'mensagem' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Usuarios/index', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }
    
    public function novo() {
        $dados = [
            'titulo_pagina' => 'Novo Usuário Admin',
            'is_editing' => false,
            'usuario' => null,
            'mensagem' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Usuarios/form', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }
    
    public function editar(int $id) {
        $usuario = $this->usuarioModel->findDadosById($id);
        
        if (!$usuario) {
            $this->redirectComErro('/admin/usuarios', 'Usuário não encontrado.');
        }

        $dados = [
            'titulo_pagina' => 'Alterar Senha de ' . htmlspecialchars($usuario['nome']),
            'is_editing' => true,
            'usuario' => $usuario,
            'user_id_edit' => $id,
            'mensagem' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Usuarios/form', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function salvar() {
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $is_editing = !empty($id);

        if ($is_editing) {
            $senha = $_POST['senha'] ?? '';
            $senha_confirma = $_POST['senha_confirma'] ?? '';
            $admin_id_atual = $_SESSION['admin_id'];

            if ($id === 1 && $admin_id_atual !== 1) {
                 $this->redirectComErro('/admin/usuarios', 'Você não pode alterar a senha do super-admin.');
            }
            if (empty($senha) || $senha !== $senha_confirma) {
                $this->redirectComErro("/admin/usuarios/editar/{$id}", 'As senhas não coincidem ou estão vazias.');
            }
            if (strlen($senha) < 6) {
                $this->redirectComErro("/admin/usuarios/editar/{$id}", 'A senha deve ter no mínimo 6 caracteres.');
            }

            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE usuarios SET senha = ? WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param("si", $senha_hash, $id);
            $stmt->execute();
            
            $this->redirectComSucesso('/admin/usuarios', 'Senha do usuário atualizada com sucesso.');

        } else {
            $nome = trim($_POST['nome'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $senha = $_POST['senha'] ?? '';
            $senha_confirma = $_POST['senha_confirma'] ?? '';

            if (empty($nome) || empty($email) || empty($senha)) {
                $this->redirectComErro('/admin/usuarios/novo', 'Todos os campos são obrigatórios.');
            }
            if ($senha !== $senha_confirma) {
                 $this->redirectComErro('/admin/usuarios/novo', 'As senhas não coincidem.');
            }
            if ($this->usuarioModel->findByEmail($email)) {
                 $this->redirectComErro('/admin/usuarios/novo', 'Este e-mail já está em uso.');
            }

            if ($this->usuarioModel->createUser($nome, $email, $senha, 'admin')) {
                $this->redirectComSucesso('/admin/usuarios', 'Novo administrador criado com sucesso.');
            } else {
                $this->redirectComErro('/admin/usuarios/novo', 'Erro ao salvar no banco de dados.');
            }
        }
    }

    public function excluir(int $id) {
        if ($id === 1) {
            $this->redirectComErro('/admin/usuarios', 'Não é permitido excluir o super-admin (ID 1).');
        }
        if ($id === $_SESSION['admin_id']) {
            $this->redirectComErro('/admin/usuarios', 'Você não pode excluir a si mesmo.');
        }

        if ($this->usuarioModel->deleteUser($id)) {
            $this->redirectComSucesso('/admin/usuarios', 'Usuário administrador excluído com sucesso.');
        } else {
            $this->redirectComErro('/admin/usuarios', 'Erro ao excluir o usuário.');
        }
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
    private function redirect(string $url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }
    private function redirectComErro(string $url, string $mensagem) {
        $_SESSION['mensagem_status'] = ['tipo' => 'erro', 'texto' => $mensagem];
        $this->redirect($url);
    }
    private function redirectComSucesso(string $url, string $mensagem) {
        $_SESSION['mensagem_status'] = ['tipo' => 'sucesso', 'texto' => $mensagem];
        $this->redirect($url);
    }
}