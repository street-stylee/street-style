<?php

namespace App\Controllers\Admin;

use \mysqli;
use App\Models\CarrosselModel;

class CarrosselController {

    protected $conexao;
    protected $carrosselModel;
    private $upload_dir_relativo = "/public/_ADM/img/carrossel/";
    private $upload_dir_fisico;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->carrosselModel = new CarrosselModel($conexao);
        $this->upload_dir_fisico = ROOT . $this->upload_dir_relativo;

        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
        }
        
        if (!is_dir($this->upload_dir_fisico)) {
            mkdir($this->upload_dir_fisico, 0777, true);
        }
    }

    public function index() {
        $dados = [
            'titulo_pagina' => 'Gerenciar Carrossel',
            'slides' => $this->carrosselModel->getSlides(),
            'mensagem' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Carrossel/index', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function salvar() {
        $link_url = filter_input(INPUT_POST, 'link_url', FILTER_SANITIZE_URL);
        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_SPECIAL_CHARS);
        $subtitulo = filter_input(INPUT_POST, 'subtitulo', FILTER_SANITIZE_SPECIAL_CHARS);
        $imagem = $_FILES['imagem_slide'] ?? null;

        if ($imagem && $imagem['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($imagem['name'], PATHINFO_EXTENSION);
            $novo_nome = "slide-" . uniqid() . "." . $extensao;
            $destino_fisico = $this->upload_dir_fisico . $novo_nome;

            $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
            if (in_array(mime_content_type($imagem['tmp_name']), $allowed_types)) {
                if (move_uploaded_file($imagem['tmp_name'], $destino_fisico)) {
                    
                    $caminho_bd = str_replace(ROOT . '/public', '', $destino_fisico);
                    $caminho_bd = ltrim($caminho_bd, '/');
                    
                    if ($this->carrosselModel->createSlide($caminho_bd, $link_url, $titulo, $subtitulo)) {
                        $this->redirectComSucesso('/admin/carrossel', 'Slide adicionado com sucesso.');
                    }
                }
            }
        }
        $this->redirectComErro('/admin/carrossel', 'Erro no upload ou arquivo inválido.');
    }

    public function excluir(int $id) {
        if ($this->carrosselModel->deleteSlide($id)) {
            $this->redirectComSucesso('/admin/carrossel', 'Slide excluído com sucesso.');
        } else {
            $this->redirectComErro('/admin/carrossel', 'Erro ao excluir o slide.');
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