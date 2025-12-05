<?php

namespace App\Controllers\Admin;

use \mysqli;
use App\Models\ConfiguracaoModel;

class ConfiguracoesController {

    protected $conexao;
    protected $configModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->configModel = new ConfiguracaoModel($conexao);
        
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
        }
    }

    public function index() {
        $dados = [
            'titulo_pagina' => 'Configurações do Site',
            'config' => $this->configModel->getAllSettings(),
            'mensagem' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Configuracoes/index', $dados); 
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function salvar() {
        $dados_post = $_POST;
        $sucesso = true;
        
        foreach ($dados_post as $key => $value) {
            if (!$this->configModel->updateSetting($key, $value)) {
                $sucesso = false;
            }
        }

        if ($sucesso) {
            $this->redirectComSucesso('/admin/configuracoes', 'Configurações salvas com sucesso!');
        } else {
            $this->redirectComErro('/admin/configuracoes', 'Erro ao salvar as configurações.');
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