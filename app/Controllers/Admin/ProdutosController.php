<?php

namespace App\Controllers\Admin;

use \mysqli;
use App\Models\ProdutoModel;

class ProdutosController
{

    protected $conexao;
    protected $produtoModel;

    // Define o caminho RELATIVO ao ROOT da aplicação
    private $upload_dir = "public/_ADM/img/produtos/"; 

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
        $this->produtoModel = new ProdutoModel($this->conexao);

        // Lógica de autenticação e sessão mantida conforme o seu código
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        if (!isset($_SESSION['admin_id'])) {
            // BASE_URL precisa estar definido (ex: em um arquivo de configuração/bootstrap)
            header("Location: " . BASE_URL . "/admin/login"); 
            exit;
        }
    }

    public function index()
    {
        $produtos = $this->produtoModel->getAllProdutosAdmin();

        $dados = [
            'titulo_pagina' => 'Gerenciar Produtos',
            'produtos' => $produtos,
            'mensagem_sucesso' => $_SESSION['mensagem_sucesso'] ?? null
        ];
        unset($_SESSION['mensagem_sucesso']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Produtos/index', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function novo()
    {
        $dados = [
            'titulo_pagina' => 'Adicionar Novo Produto',
            'produto' => ['id' => null, 'nome' => '', 'descricao' => '', 'preco' => 0.00, 'imagem_url' => '', 'categoria' => '', 'is_promocao' => 0, 'is_novidade' => 0, 'preco_promocional' => null],
            'imagens_extras' => [],
            'estoque_por_tamanho' => [],
            'is_editing' => false,
            'categorias' => ['Calças', 'Camisetas', 'Conjuntos', 'Moletons', 'Shorts', 'Acessórios'],
            'tamanhos_opcoes' => ['P', 'M', 'G', 'GG', 'U']
        ];

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Produtos/form', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function editar(int $id)
    {
        $produto = $this->produtoModel->getProdutoPorId($id);
        if (!$produto) {
            $this->redirectComErro('/admin/produtos', 'Produto não encontrado.');
        }

        $dados = [
            'titulo_pagina' => 'Editar Produto #' . $id,
            'produto' => $produto,
            'imagens_extras' => $this->produtoModel->getImagensExtras($id),
            'estoque_por_tamanho' => $this->produtoModel->getVariacoesPorProduto($id),
            'is_editing' => true,
            'categorias' => ['Calças', 'Camisetas', 'Conjuntos', 'Moletons', 'Shorts', 'Acessórios'],
            'tamanhos_opcoes' => ['P', 'M', 'G', 'GG', 'U'],
            'mensagem_status' => $_SESSION['mensagem_status'] ?? null
        ];
        unset($_SESSION['mensagem_status']);

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Produtos/form', $dados);
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function salvar()
    {
        // 1. Coleta e Sanitiza Dados
        $produto_id = filter_input(INPUT_POST, 'produto_id', FILTER_VALIDATE_INT);
        $is_editing = !empty($produto_id);
        $is_promocao = isset($_POST['is_promocao']) ? 1 : 0;
        $is_novidade = isset($_POST['is_novidade']) ? 1 : 0;

        $preco_promocional = ($is_promocao)
            ? filter_input(INPUT_POST, 'preco_promocional', FILTER_VALIDATE_FLOAT)
            : null;

        $nome_produto = trim(filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_SPECIAL_CHARS));
        $descricao = trim(filter_input(INPUT_POST, 'descricao', FILTER_SANITIZE_SPECIAL_CHARS));
        $preco = filter_input(INPUT_POST, 'preco', FILTER_VALIDATE_FLOAT);
        $categoria = filter_input(INPUT_POST, 'categoria', FILTER_SANITIZE_SPECIAL_CHARS);

        // Validação básica
        if (empty($nome_produto)) {
            $this->redirectComErro($_SERVER['HTTP_REFERER'], "O nome do produto é obrigatório.");
            return;
        }

        // Monta array inicial
        $dados_produto = [
            'nome' => $nome_produto,
            'descricao' => $descricao,
            'preco' => number_format((float) $preco, 2, '.', ''),
            'categoria' => $categoria,
            'imagem_url' => $_POST['imagem_url_atual'] ?? '', // Mantém a antiga se não houver nova
            'is_promocao' => $is_promocao,
            'is_novidade' => $is_novidade,
            'preco_promocional' => $preco_promocional
        ];

        // 2. Define o caminho físico de destino com normalização de barras CRÍTICA
        $caminho_upload_completo = rtrim(ROOT, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR, $this->upload_dir);

        // 3. Upload da Imagem Principal
        if (isset($_FILES['imagem_principal']) && $_FILES['imagem_principal']['error'] === UPLOAD_ERR_OK) {
            $resultado_upload = $this->handle_upload($_FILES['imagem_principal'], $caminho_upload_completo, $dados_produto['nome'], true);

            if (is_string($resultado_upload)) {
                $dados_produto['imagem_url'] = $resultado_upload;
            } else {
                // Se o resultado for um array com erro
                $this->redirectComErro($_SERVER['HTTP_REFERER'], $resultado_upload['erro']);
                return;
            }
        }

        // 4. Processamento das Imagens Extras
        $imagens_finais = [];
        // Mantém as imagens existentes (input hidden)
        if (isset($_POST['imagens_mantidas']) && is_array($_POST['imagens_mantidas'])) {
            foreach ($_POST['imagens_mantidas'] as $img_antiga) {
                $imagens_finais[] = trim($img_antiga);
            }
        }

        // Faz upload das novas imagens extras
        if (isset($_FILES['imagens_extras']) && !empty($_FILES['imagens_extras']['name'][0])) {
            $files = $_FILES['imagens_extras'];
            $count = count($files['name']);

            for ($i = 0; $i < $count; $i++) {
                if ($files['error'][$i] === UPLOAD_ERR_OK) {
                    $file_array = [
                        'name' => $files['name'][$i],
                        'type' => $files['type'][$i],
                        'tmp_name' => $files['tmp_name'][$i],
                        'error' => $files['error'][$i],
                        'size' => $files['size'][$i]
                    ];

                    $upload_extra = $this->handle_upload($file_array, $caminho_upload_completo, $dados_produto['nome'], false);

                    if (is_string($upload_extra)) {
                        $imagens_finais[] = $upload_extra;
                    }
                }
            }
        }

        // 5. Estoque
        $variacoes_post = [];
        $tamanhos_opcoes = ['P', 'M', 'G', 'GG', 'U']; // Use a lista completa
        if (isset($_POST['estoque']) && is_array($_POST['estoque'])) {
            foreach ($tamanhos_opcoes as $tamanho) {
                if (isset($_POST['estoque'][$tamanho])) {
                    $estoque_int = filter_var($_POST['estoque'][$tamanho], FILTER_VALIDATE_INT);
                    $variacoes_post[] = ['tamanho' => $tamanho, 'estoque' => max(0, (int) $estoque_int)];
                }
            }
        }

        // 6. Salvar no Banco (Transação)
        $this->conexao->begin_transaction();
        try {
            if ($is_editing) {
                $this->produtoModel->updateProduto($produto_id, $dados_produto);
            } else {
                if (empty($dados_produto['imagem_url'])) {
                    // Fallback se não enviou imagem nenhuma (apenas para criação)
                    $dados_produto['imagem_url'] = 'img/produtos/placeholder.webp'; 
                }
                $produto_id = $this->produtoModel->createProduto($dados_produto);
                if (!$produto_id)
                    throw new \Exception("Falha ao criar o produto.");
            }

            if (!empty($variacoes_post)) {
                $this->produtoModel->syncVariacoes($produto_id, $variacoes_post);
            }

            $this->produtoModel->syncImagensExtras($produto_id, $imagens_finais);

            $this->conexao->commit();
            $_SESSION['mensagem_status'] = "Produto salvo com sucesso!";
            $this->redirect('/admin/produtos/editar/' . $produto_id);

        } catch (\Exception $e) {
            $this->conexao->rollback();
            $this->redirectComErro($_SERVER['HTTP_REFERER'], "Erro: " . $e->getMessage());
        }
    }

    // --- FUNÇÃO DE UPLOAD CORRIGIDA E LIMPA ---
    private function handle_upload($file_array, $upload_dir_fisico, $produto_nome, $is_main = true)
    {
        // 1. Garante que a pasta existe
        if (!is_dir($upload_dir_fisico)) {
            if (!mkdir($upload_dir_fisico, 0755, true)) {
                return ['erro' => "Falha ao criar diretório: " . $upload_dir_fisico];
            }
        }

        // 2. Limpa nome e extensão
        $nome_limpo = preg_replace("/[^a-zA-Z0-9-]/", "", strtolower(str_replace(' ', '-', $produto_nome)));
        $extensao = strtolower(pathinfo($file_array['name'], PATHINFO_EXTENSION));

        // 3. Gera nome único
        $novo_nome = $is_main ? $nome_limpo . '-principal.' . $extensao : $nome_limpo . '-' . uniqid() . '.' . $extensao;

        // 4. Define o destino físico com normalização
        $destino_raw = rtrim($upload_dir_fisico, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $novo_nome;
        $destino = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $destino_raw);
        
        // 5. Validação de Mime Type
        $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
        $mime_type = @mime_content_type($file_array['tmp_name']);

        if (!$mime_type || !in_array($mime_type, $allowed_types)) {
            return ['erro' => "Tipo de arquivo não permitido ({$mime_type}). Use JPG, PNG ou WEBP."];
        }

        // 6. Move o arquivo
        if (move_uploaded_file($file_array['tmp_name'], $destino)) {
            // SUCESSO: Retorna o caminho RELATIVO ao ROOT para salvar no banco
            return 'img/produtos/' . $novo_nome;
        }

        // 7. Falha ao mover
        return ['erro' => "Falha ao mover o arquivo para: " . $destino];
    }

    public function excluir(int $id)
    {
        // Lógica de exclusão (manter)
        if ($this->produtoModel->deleteProduto($id)) {
            $_SESSION['mensagem_sucesso'] = "Produto #{$id} excluído com sucesso.";
        } else {
            $_SESSION['mensagem_sucesso'] = "Erro ao excluir o produto #{$id}.";
        }
        $this->redirect('/admin/produtos');
    }

    // --- Métodos de View e Redirecionamento (mantidos) ---

    private function carregarView(string $caminho, array $dados = [])
    {
        extract($dados);
        // ROOT precisa estar definido
        require_once ROOT . "/app/Views/{$caminho}.php"; 
    }

    private function redirect(string $url)
    {
        // BASE_URL precisa estar definido
        header("Location: " . BASE_URL . $url); 
        exit;
    }

    private function redirectComErro(string $url, string $mensagem)
    {
        $_SESSION['mensagem_status'] = $mensagem;
        header("Location: " . $url);
        exit;
    }
}