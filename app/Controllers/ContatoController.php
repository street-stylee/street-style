<?php

namespace App\Controllers;

use App\Models\CarrinhoModel;
use App\Models\ContatoModel;
use App\Helpers\EmailHelper; 
use \mysqli;

class ContatoController {

    protected $conexao;
    protected $carrinhoModel;
    protected $contatoModel;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        $this->carrinhoModel = new CarrinhoModel($conexao);
        $this->contatoModel = new ContatoModel($conexao);
    }

    public function index() {
        $mensagem = '';
        $tipo_mensagem = '';
        
        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $motivo_contato = trim($_POST['contato'] ?? ''); 

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            
            if (empty($nome) || empty($email) || empty($motivo_contato)) {
                $mensagem = "Por favor, preencha nome, e-mail e o motivo.";
                $tipo_mensagem = 'erro';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $mensagem = "O e-mail fornecido é inválido.";
                $tipo_mensagem = 'erro';
            } else {
                
                $salvo_no_bd = $this->contatoModel->salvarMensagem($nome, $email, $telefone, $motivo_contato);

                if ($salvo_no_bd) {
                    
                    $email_admin = 'streetstyle.ufc@gmail.com'; 
                    $nome_admin = 'Admin Street Style';
                    $assunto = "Nova Mensagem de Contato de: " . $nome;
                    
                    $corpo_html = "
                        <h2>Nova Mensagem Recebida (via Site)</h2>
                        <hr>
                        <p><strong>De:</strong> " . htmlspecialchars($nome) . "</p>
                        <p><strong>E-mail:</strong> " . htmlspecialchars($email) . "</p>
                        <p><strong>Telefone:</strong> " . htmlspecialchars($telefone) . "</p>
                        <hr>
                        <p><strong>Mensagem:</strong></p>
                        <p>" . nl2br(htmlspecialchars($motivo_contato)) . "</p>
                    ";

                    $email_enviado = EmailHelper::enviar(
                        $email_admin, 
                        $nome_admin, 
                        $assunto, 
                        $corpo_html,
                        $email, 
                        $nome   
                    );

                    if ($email_enviado) {
                        $mensagem = "Obrigado pelo seu contato, {$nome}! Responderemos em breve.";
                        $tipo_mensagem = 'sucesso';
                    } else {
                        $mensagem = "Obrigado pelo contato! (Houve um erro ao enviar a notificação por e-mail, mas recebemos sua mensagem.)";
                        $tipo_mensagem = 'sucesso'; 
                    }
                    
                } else {
                    $mensagem = "Erro ao salvar sua mensagem no banco. Tente novamente.";
                    $tipo_mensagem = 'erro';
                }
               
            }
        }
    
        $this->showForm($mensagem, $tipo_mensagem);
    }

    private function showForm($mensagem = '', $tipo_mensagem = '') {
        $usuario_id = $_SESSION['usuario_id'] ?? null;
        $dados = [
            'titulo_pagina' => 'Contato - Street Style',
            'mensagem' => $mensagem,
            'tipo_mensagem' => $tipo_mensagem,
            'usuario_logado' => isset($usuario_id),
            'primeiro_nome' => htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Convidado')[0]),
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho($usuario_id, session_id())
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Contato/index', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
}