<?php

namespace App\Controllers;

use App\Models\FavoritoModel;
use App\Models\UsuarioModel;
use \mysqli;

class FavoritoController {

    protected $conexao;
    protected $favoritoModel;
    protected $usuarioModel; 

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->favoritoModel = new FavoritoModel($conexao);
        $this->usuarioModel = new UsuarioModel($conexao);
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function toggle(int $produto_id) {
        
        header('Content-Type: application/json');
        $response = ['status' => 'error', 'message' => 'Ação falhou'];

        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $usuario_valido = false;

        if ($usuario_id) {
            $usuario = $this->usuarioModel->findDadosById($usuario_id);
            if ($usuario) {
                $usuario_valido = true;
            }
        }

        if (!$usuario_valido) {
            $response['message'] = 'login_required';
            echo json_encode($response);
            exit;
        }

        try {
            $action = $this->favoritoModel->toggle($usuario_id, $produto_id);

            if ($action === 'added' || $action === 'removed') {
                $response['status'] = 'success';
                $response['action'] = $action;
                $response['message'] = 'Favorito atualizado.';
            } else {
                $response['message'] = 'Erro ao atualizar o banco de dados.';
            }

        } catch (\Exception $e) {
            $response['message'] = $e->getMessage();
        }

        echo json_encode($response);
        exit;
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }

    private function redirect(string $url) {
        if (!preg_match("~^(http|https)://~", $url)) {
             if (!str_starts_with($url, '/')) $url = '/' . $url;
             $url = BASE_URL . $url;
        }
        header("Location: " . $url);
        exit;
    }
}