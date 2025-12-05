<?php
namespace App\Controllers;
require_once ROOT . '/app/Helpers/PHPMailer/PHPMailer.php';
require_once ROOT . '/app/Helpers/PHPMailer/SMTP.php';
require_once ROOT . '/app/Helpers/PHPMailer/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception as PHPMailerException;
use App\Models\CarrinhoModel;
use App\Models\PedidoModel;
use App\Models\UsuarioModel;
use \mysqli;
use Exception;
use App\Services\EmailService;



class CheckoutController
{

    protected $conexao;
    protected $carrinhoModel;
    protected $pedidoModel;
    protected $usuarioModel; 

    public function __construct(mysqli $conexao)
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        $this->conexao = $conexao;
        $this->carrinhoModel = new CarrinhoModel($conexao);
        $this->pedidoModel = new PedidoModel($conexao);
        $this->usuarioModel = new UsuarioModel($conexao);

        if (isset($_SESSION['usuario_id']) && isset($_SESSION['carrinho_session_id'])) {
            $usuario_id = $_SESSION['usuario_id'];
            $session_id_antiga = $_SESSION['carrinho_session_id'];

            if ($this->carrinhoModel->migrarCarrinho($usuario_id, $session_id_antiga)) {
                unset($_SESSION['carrinho_session_id']);
            }
        }
    }

    public function index()
    {
        if (!isset($_SESSION['usuario_id'])) {
            $this->redirect('/login?redirect=checkout');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['concluir_pedido'])) {
            $this->processar();
        } else {
            $this->showForm();
        }
    }

    private function showForm($mensagem_erro = '')
    {
        $usuario_id = $_SESSION['usuario_id'];

        $itens_carrinho_db = $this->carrinhoModel->getItensCarrinhoComDetalhes($usuario_id, null);

        if (empty($itens_carrinho_db)) {
            $this->redirect('/carrinho');
        }

        $total_produtos = 0;
        foreach ($itens_carrinho_db as $item) {
            $total_produtos += $item['preco_unitario'] * $item['quantidade'];
        }
        $valor_frete = 25.00;
        $valor_desconto = 0.00;
        $total_geral = $total_produtos + $valor_frete - $valor_desconto;

        $dados = [
            'titulo_pagina' => 'Finalizar Pedido',
            'itens_carrinho_db' => $itens_carrinho_db,
            'total_produtos' => $total_produtos,
            'valor_frete' => $valor_frete,
            'valor_desconto' => $valor_desconto,
            'total_geral' => $total_geral,
            'mensagem_status' => $mensagem_erro,
            'usuario_logado' => true,
            'primeiro_nome' => htmlspecialchars(explode(' ', $_SESSION['usuario_nome'] ?? 'Usuário')[0]),
            'quantidade_carrinho' => $this->carrinhoModel->contarItensCarrinho($usuario_id, null)
        ];

        $this->carregarView('Layout/header', $dados);
        $this->carregarView('Checkout/index', $dados);
        $this->carregarView('Layout/footer', $dados);
    }

    private function processar()
    {
        $usuario_id = $_SESSION['usuario_id'];

        $cep = trim(filter_input(INPUT_POST, 'cep', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $cidade = trim(filter_input(INPUT_POST, 'cidade', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $logradouro = trim(filter_input(INPUT_POST, 'logradouro', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $numero = trim(filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_NUMBER_INT));
        $complemento = trim(filter_input(INPUT_POST, 'complemento', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $bairro = trim(filter_input(INPUT_POST, 'bairro', FILTER_SANITIZE_FULL_SPECIAL_CHARS));
        $metodo_pagamento = trim(filter_input(INPUT_POST, 'metodo_pagamento', FILTER_SANITIZE_FULL_SPECIAL_CHARS));

        if (empty($cep) || empty($logradouro) || empty($numero) || empty($bairro) || empty($metodo_pagamento)) {
            $this->showForm("Por favor, preencha todos os campos obrigatórios.");
            return;
        }
        $endereco_completo = "CEP: {$cep}, {$logradouro}, Nº {$numero}, {$bairro} - {$cidade} / Comp: {$complemento}";

        $itens_carrinho_db = $this->carrinhoModel->getItensCarrinhoComDetalhes($usuario_id, null);
        if (empty($itens_carrinho_db)) {
            $this->redirect('/carrinho');
        }
        $total_produtos = 0;
        foreach ($itens_carrinho_db as $item) {
            $total_produtos += $item['preco_unitario'] * $item['quantidade'];
        }
        $valor_frete = 25.00;
        $valor_desconto = 0.00;
        $total_geral = $total_produtos + $valor_frete - $valor_desconto;

        $dados_pedido = [
            'total_produtos' => $total_produtos,
            'valor_frete' => $valor_frete,
            'valor_desconto' => $valor_desconto,
            'total_geral' => $total_geral,
            'metodo_pagamento' => $metodo_pagamento,
            'endereco_completo' => $endereco_completo
        ];

        $pedido_id = $this->pedidoModel->criarPedido($usuario_id, $itens_carrinho_db, $dados_pedido);

        if ($pedido_id) {
            $dadosCliente = $this->usuarioModel->findById($usuario_id);
            $emailCliente = $dadosCliente['email'];
            $nomeCliente = $dadosCliente['nome'];


            $assunto = "Confirmação do Pedido #{$pedido_id}";
            $mensagem = "
    <h2>Olá, {$nomeCliente}!</h2>
    <p>Obrigado pela sua compra! O número do seu pedido é <strong>#{$pedido_id}</strong>.</p>
    <p>Em breve você receberá atualizações sobre o processamento e envio.</p>
";

            EmailService::sendEmail($emailCliente, $assunto, $mensagem);

            $this->redirect("/pedidos/sucesso/{$pedido_id}");
        } else {
            $this->showForm("Erro ao finalizar o pedido. Tente novamente.");
        }
    }


    private function montarCorpoEmail(int $pedido_id, array $itens, array $dados_pedido): string
    {
        $body = "<h1>Confirmação de Pedido #{$pedido_id}</h1>";
        $body .= "<p>Obrigado por sua compra! Seu pedido foi confirmado e está sendo processado.</p>";
        $body .= "<h2>Detalhes do Pedido</h2>";
        $body .= "<p><strong>Método de Pagamento:</strong> {$dados_pedido['metodo_pagamento']}</p>";
        $body .= "<p><strong>Endereço de Entrega:</strong> {$dados_pedido['endereco_completo']}</p>";

        $body .= "<h3>Itens:</h3>";
        $body .= "<table border='1' cellpadding='10' cellspacing='0' width='100%'>";
        $body .= "<tr><th>Produto</th><th>Tamanho</th><th>Qtd</th><th>Preço Unit.</th><th>Subtotal</th></tr>";
        foreach ($itens as $item) {
            $subtotal = $item['preco_unitario'] * $item['quantidade'];
            $body .= "<tr>";
            $body .= "<td>" . htmlspecialchars($item['nome_produto']) . "</td>";
            $body .= "<td>" . htmlspecialchars($item['tamanho']) . "</td>";
            $body .= "<td>" . $item['quantidade'] . "</td>";
            $body .= "<td>R$ " . number_format($item['preco_unitario'], 2, ',', '.') . "</td>";
            $body .= "<td>R$ " . number_format($subtotal, 2, ',', '.') . "</td>";
            $body .= "</tr>";
        }
        $body .= "</table>";

        $body .= "<p><strong>Total Produtos:</strong> R$ " . number_format($dados_pedido['total_produtos'], 2, ',', '.') . "</p>";
        $body .= "<p><strong>Frete:</strong> R$ " . number_format($dados_pedido['valor_frete'], 2, ',', '.') . "</p>";
        $body .= "<p><strong>Desconto:</strong> R$ " . number_format($dados_pedido['valor_desconto'], 2, ',', '.') . "</p>";
        $body .= "<h3>Total Geral: R$ " . number_format($dados_pedido['total_geral'], 2, ',', '.') . "</h3>";

        return $body;
    }
    private function enviarConfirmacaoPedido(string $email, string $nome, int $pedido_id, array $itens, array $dados_pedido): bool
    {

        if (!class_exists(PHPMailer::class)) {
            error_log("PHPMailer não encontrado. O e-mail de confirmação não será enviado.");
            return false;
        }

        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'streetstyle.ufc@gmail.com';
            $mail->Password = 'idcy ehgw nlgp bgtl';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            $mail->setFrom('no-reply@streetstyle.com', 'Street Style Main');
            $mail->addAddress($email, $nome);

            $mail->isHTML(true);
            $mail->Subject = "Confirmação do seu Pedido #{$pedido_id} - Street Style Main";
            $mail->Body = $this->montarCorpoEmail($pedido_id, $itens, $dados_pedido);
            $mail->CharSet = 'UTF-8';

            $mail->send();
            error_log("E-mail de confirmação #{$pedido_id} enviado com sucesso para {$email}.");
            return true;
        } catch (PHPMailerException $e) {
            error_log("Erro ao enviar e-mail de confirmação #{$pedido_id}: {$mail->ErrorInfo}");
            return false;
        } catch (Exception $e) {
            error_log("Erro inesperado no envio de e-mail: " . $e->getMessage());
            return false;
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