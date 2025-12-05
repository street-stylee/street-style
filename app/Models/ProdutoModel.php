<?php

namespace App\Models;

use \mysqli;

class ProdutoModel
{

    protected $conexao;

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
    }

    public function getProdutoPorId(int $id): ?array
    {
        $sql = "SELECT id, nome, descricao, preco, imagem_url, categoria, 
                        avaliacao_media, is_promocao, is_novidade, preco_promocional 
                FROM produtos 
                WHERE id = ?";

        $produto = null;
        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            if ($linha = $resultado->fetch_assoc()) {
                $produto = $linha;
            }
            $stmt->close();
        }
        return $produto;
    }

    public function getImagensExtras(int $produto_id): array
    {
        $imagens_extras = [];
        $sql = "SELECT imagem_url FROM produto_imagens WHERE produto_id = ?";

        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("i", $produto_id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            while ($imagem = $resultado->fetch_assoc()) {
                $imagens_extras[] = ['imagem_url' => $imagem['imagem_url']];
            }
            $stmt->close();
        }
        return $imagens_extras;
    }

    public function getVariacoesPorProduto(int $produto_id): array
    {
        $variacoes_json_data = [];
        $sql = "SELECT id, tamanho, estoque FROM produto_variacoes WHERE produto_id = ? ORDER BY tamanho";

        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("i", $produto_id);
            $stmt->execute();
            $resultado = $stmt->get_result();

            while ($variacao = $resultado->fetch_assoc()) {
                $tamanho = $variacao['tamanho'];
                $variacoes_json_data[$tamanho] = [
                    'id' => (int) $variacao['id'],
                    'estoque' => (int) $variacao['estoque']
                ];
            }
            $stmt->close();
        }
        return $variacoes_json_data;
    }

    public function getProdutosSemelhantes(string $categoria, int $excluir_id): array
    {
        $produtos_semelhantes = [];
        $sql = "SELECT id, nome, preco, imagem_url, avaliacao_media 
                FROM produtos 
                WHERE categoria = ? AND id != ?
                ORDER BY RAND() LIMIT 4";

        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("si", $categoria, $excluir_id);
            $stmt->execute();
            $resultado = $stmt->get_result();
            while ($produto_semelhante = $resultado->fetch_assoc()) {
                if (!isset($produto_semelhante['avaliacao_media'])) {
                    $produto_semelhante['avaliacao_media'] = rand(30, 50) / 10;
                }
                $produtos_semelhantes[] = $produto_semelhante;
            }
            $stmt->close();
        }
        return $produtos_semelhantes;
    }

    public function getProdutosPorCategoria(string $categoria): array
    {
        $produtos = [];
        $sql = "SELECT id, nome, preco, imagem_url, avaliacao_media FROM produtos WHERE categoria = ? ORDER BY nome";

        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("s", $categoria);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $produtos = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
        return $produtos;
    }

    public function buscarProdutos(string $termo_busca): array
    {
        $resultados = [];
        $param_busca_like = '%' . $termo_busca . '%';
        $sql = "SELECT id, nome, preco, imagem_url, descricao 
                FROM produtos 
                WHERE nome LIKE ? OR descricao LIKE ? OR SOUNDEX(nome) = SOUNDEX(?)
                ORDER BY nome ASC";

        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("sss", $param_busca_like, $param_busca_like, $termo_busca);
            $stmt->execute();
            $resultados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
        return $resultados;
    }

    public function getProdutosEmPromocao(int $limite = 8): array
    {
        $produtos = [];
        $sql = "SELECT id, nome, preco, imagem_url, preco_promocional, avaliacao_media, is_promocao 
                FROM produtos 
                WHERE is_promocao = 1 
                ORDER BY RAND() LIMIT ?";

        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("i", $limite);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $produtos = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
        return $produtos;
    }

    public function getProdutosNovidade(int $limite = 8): array
    {
        $produtos = [];
        $sql = "SELECT id, nome, preco, imagem_url, avaliacao_media 
                FROM produtos 
                WHERE is_novidade = 1 
                ORDER BY id DESC LIMIT ?";

        if ($stmt = $this->conexao->prepare($sql)) {
            $stmt->bind_param("i", $limite);
            $stmt->execute();
            $resultado = $stmt->get_result();
            $produtos = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
        return $produtos;
    }

    public function getPrecoFinal($produto)
    {
        if (!empty($produto['is_promocao']) && $produto['is_promocao'] == 1 && floatval($produto['preco_promocional']) > 0) {
            return number_format($produto['preco_promocional'], 2, ',', '.');
        }

        return number_format($produto['preco'], 2, ',', '.');
    }


    public function getAvaliacoesPorProduto(int $produto_id): array
    {
        $avaliacoes = [];
        $sql = "SELECT a.*, u.nome AS nome_usuario 
                FROM produto_avaliacoes a
                JOIN usuarios u ON a.usuario_id = u.id
                WHERE a.produto_id = ?
                ORDER BY a.data_avaliacao DESC";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $produto_id);
        $stmt->execute();
        $avaliacoes = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $avaliacoes;
    }

    public function createAvaliacao(int $produto_id, int $usuario_id, int $nota, string $titulo, string $comentario): bool
    {
        $sql = "INSERT INTO produto_avaliacoes (produto_id, usuario_id, nota, titulo, comentario)
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("iiiss", $produto_id, $usuario_id, $nota, $titulo, $comentario);
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }

    public function recalcularAvaliacaoMedia(int $produto_id): bool
    {
        $sql_avg = "SELECT AVG(nota) AS media FROM produto_avaliacoes WHERE produto_id = ?";
        $stmt_avg = $this->conexao->prepare($sql_avg);
        $stmt_avg->bind_param("i", $produto_id);
        $stmt_avg->execute();
        $resultado = $stmt_avg->get_result()->fetch_assoc();
        $stmt_avg->close();

        $nova_media = (float) ($resultado['media'] ?? 0.0);
        $nova_media_formatada = round($nova_media, 1);

        $sql_update = "UPDATE produtos SET avaliacao_media = ? WHERE id = ?";
        $stmt_update = $this->conexao->prepare($sql_update);
        $stmt_update->bind_param("di", $nova_media_formatada, $produto_id);
        $sucesso = $stmt_update->execute();
        $stmt_update->close();
        return $sucesso;
    }

    public function getAllProdutosAdmin(): array
    {
        $produtos = [];
        $sql = "SELECT id, nome, preco, categoria FROM produtos ORDER BY id DESC";
        if ($resultado = $this->conexao->query($sql)) {
            $produtos = $resultado->fetch_all(MYSQLI_ASSOC);
            $resultado->free();
        }
        return $produtos;
    }

    public function createProduto(array $dados): int|false
    {
        $sql = "INSERT INTO produtos (nome, descricao, preco, imagem_url, categoria, 
                                     is_promocao, is_novidade, preco_promocional, avaliacao_media) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0.0)";
        $stmt = $this->conexao->prepare($sql);

        if (!$stmt)
            return false;

        $nome = $dados['nome'];
        $descricao = $dados['descricao'];
        $preco = (float) $dados['preco'];
        $imagem_url = $dados['imagem_url']; // Caminho já tratado pelo Controller
        $categoria = $dados['categoria'];
        $is_promocao = (int) $dados['is_promocao'];
        $is_novidade = (int) $dados['is_novidade'];
        $preco_promocional = $dados['preco_promocional'] !== null ? (float) $dados['preco_promocional'] : null;

        $stmt->bind_param(
            "ssdssiid",
            $nome,
            $descricao,
            $preco,
            $imagem_url,
            $categoria,
            $is_promocao,
            $is_novidade,
            $preco_promocional
        );

        if ($stmt->execute()) {
            $novo_id = $this->conexao->insert_id;
            $stmt->close();
            return $novo_id;
        }
        $stmt->close();
        return false;
    }

    public function updateProduto(int $id, array $dados): bool
    {
        $sql = "UPDATE produtos SET 
                    nome = ?, descricao = ?, preco = ?, imagem_url = ?, categoria = ?,
                    is_promocao = ?, is_novidade = ?, preco_promocional = ?
                WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        if (!$stmt)
            return false;

        $nome = $dados['nome'];
        $descricao = $dados['descricao'];
        $preco = (float) $dados['preco'];
        $imagem_url = $dados['imagem_url'];
        $categoria = $dados['categoria'];
        $is_promocao = (int) $dados['is_promocao'];
        $is_novidade = (int) $dados['is_novidade'];
        $preco_promocional = $dados['preco_promocional'] !== null ? (float) $dados['preco_promocional'] : null;

        $stmt->bind_param(
            "ssdssiidi",
            $nome,
            $descricao,
            $preco,
            $imagem_url,
            $categoria,
            $is_promocao,
            $is_novidade,
            $preco_promocional,
            $id
        );
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }

    public function syncVariacoes(int $produto_id, array $variacoes): bool
    {
        $sql_delete = "DELETE FROM produto_variacoes WHERE produto_id = ?";
        $stmt_delete = $this->conexao->prepare($sql_delete);
        $stmt_delete->bind_param("i", $produto_id);
        $stmt_delete->execute();
        $stmt_delete->close();

        if (empty($variacoes)) {
            return true;
        }

        $sql_insert = "INSERT INTO produto_variacoes (produto_id, tamanho, estoque) 
                        VALUES (?, ?, ?)";
        $stmt_insert = $this->conexao->prepare($sql_insert);
        if (!$stmt_insert)
            return false;

        foreach ($variacoes as $variacao) {
            $stmt_insert->bind_param(
                "isi",
                $produto_id,
                $variacao['tamanho'],
                $variacao['estoque']
            );
            if (!$stmt_insert->execute()) {
                $stmt_insert->close();
                return false;
            }
        }
        $stmt_insert->close();
        return true;
    }

    public function syncImagensExtras(int $produto_id, array $imagens): bool
    {
        $sql_delete = "DELETE FROM produto_imagens WHERE produto_id = ?";
        $stmt_delete = $this->conexao->prepare($sql_delete);
        $stmt_delete->bind_param("i", $produto_id);
        $stmt_delete->execute();
        $stmt_delete->close();

        if (empty($imagens)) {
            return true;
        }

        $sql_insert = "INSERT INTO produto_imagens (produto_id, imagem_url) VALUES (?, ?)";
        $stmt_insert = $this->conexao->prepare($sql_insert);
        if (!$stmt_insert) {
            return false;
        }

        foreach ($imagens as $imagem_url) {
            $url_limpa = trim($imagem_url);
            if (!empty($url_limpa)) {
                $stmt_insert->bind_param("is", $produto_id, $url_limpa);
                if (!$stmt_insert->execute()) {
                    $stmt_insert->close();
                    return false;
                }
            }
        }
        $stmt_insert->close();
        return true;
    }


    public function deleteProduto(int $id): bool
    {
        $sql = "DELETE FROM produtos WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }

    public function getTodasAvaliacoes(): array
    {
        $avaliacoes = [];
        $sql = "SELECT a.*, u.nome AS nome_usuario, p.nome AS nome_produto 
                FROM produto_avaliacoes a
                JOIN usuarios u ON a.usuario_id = u.id
                JOIN produtos p ON a.produto_id = p.id
                ORDER BY a.data_avaliacao DESC";
        if ($resultado = $this->conexao->query($sql)) {
            $avaliacoes = $resultado->fetch_all(MYSQLI_ASSOC);
            $resultado->free();
        }
        return $avaliacoes;
    }

    public function deleteAvaliacao(int $id): bool
    {
        $sql = "DELETE FROM produto_avaliacoes WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }
}