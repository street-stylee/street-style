<?php

namespace App\Models;

use \mysqli;
use Exception;

class PedidoModel
{

    protected $conexao;

    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
    }

    public function criarPedido(int $usuario_id, array $itens_carrinho, array $dados_pedido): int|false
    {

        $this->conexao->begin_transaction();

        try {
            $sql_pedido = "INSERT INTO pedidos (usuario_id, data_pedido, total_produtos, valor_frete, valor_desconto, total_geral, status, metodo_pagamento, endereco_completo) 
                           VALUES (?, NOW(), ?, ?, ?, ?, ?, ?, ?)";

            $stmt_pedido = $this->conexao->prepare($sql_pedido);
            if ($stmt_pedido === false) {
                throw new Exception("Falha ao preparar [pedidos]: " . $this->conexao->error);
            }

            $status = 'Pendente';

            $stmt_pedido->bind_param(
                "iddddsss",
                $usuario_id,
                $dados_pedido['total_produtos'],
                $dados_pedido['valor_frete'],
                $dados_pedido['valor_desconto'],
                $dados_pedido['total_geral'],
                $status,
                $dados_pedido['metodo_pagamento'],
                $dados_pedido['endereco_completo']
            );

            if (!$stmt_pedido->execute()) {
                throw new Exception("Falha ao executar [pedidos]: " . $stmt_pedido->error);
            }

            $pedido_id = $this->conexao->insert_id;
            $stmt_pedido->close();
            

            $sql_itens = "INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario, nome_produto, tamanho) 
                          VALUES (?, ?, ?, ?, ?, ?)";

            $stmt_itens = $this->conexao->prepare($sql_itens);
            if ($stmt_itens === false) {
                throw new Exception("Falha ao preparar [itens_pedido]: " . $this->conexao->error);
            }

            foreach ($itens_carrinho as $item) {

                $stmt_itens->bind_param(
                    "iidsss",
                    $pedido_id,
                    $item['produto_id'],
                    $item['quantidade'],
                    $item['preco_unitario'],
                    $item['nome_produto'],
                    $item['tamanho']
                );
                if (!$stmt_itens->execute()) {
                    throw new Exception("Falha ao inserir item {$item['produto_id']}: " . $stmt_itens->error);
                }
            }
            $stmt_itens->close();
            $sql_update_estoque = "UPDATE produto_variacoes pv
                                JOIN (
                                    SELECT id, estoque, produto_id, tamanho 
                                    FROM produto_variacoes
                                ) AS sub ON pv.produto_id = sub.produto_id AND pv.tamanho = sub.tamanho
                                SET pv.estoque = pv.estoque - ?
                                WHERE pv.produto_id = ? AND pv.tamanho = ?";
        
            $stmt_update_estoque = $this->conexao->prepare($sql_update_estoque);
        
            if ($stmt_update_estoque === false) {
                throw new Exception("Falha ao preparar [update_estoque]: " . $this->conexao->error);
            }

            foreach ($itens_carrinho as $item) {
                $quantidade = $item['quantidade'];
                $produto_id = $item['produto_id'];
                $tamanho = $item['tamanho'];

                $stmt_update_estoque->bind_param("iis", $quantidade, $produto_id, $tamanho);
                
                if (!$stmt_update_estoque->execute()) {
                    throw new Exception("Falha ao atualizar estoque para Produto {$produto_id}, Tamanho {$tamanho}: " . $stmt_update_estoque->error);
                }

                if ($this->conexao->affected_rows === 0) {
                     error_log("Aviso: Variacao de estoque não encontrada/atualizada para Produto {$produto_id}, Tamanho {$tamanho}");
                }

            }
            $stmt_update_estoque->close();

            $sql_limpar = "DELETE FROM carrinho_itens WHERE usuario_id = ?";

            $sql_limpar = "DELETE FROM carrinho_itens WHERE usuario_id = ?";
            $stmt_limpar = $this->conexao->prepare($sql_limpar);
            $stmt_limpar->bind_param("i", $usuario_id);
            $stmt_limpar->execute();
            $stmt_limpar->close();

            $this->conexao->commit();
            return $pedido_id;

        } catch (Exception $e) {
            $this->conexao->rollback();
            error_log($e->getMessage());
            return false;
        }
    }

    public function getPedidoPorId(int $pedido_id, int $usuario_id): ?array
    {
        $sql = "SELECT * FROM pedidos WHERE id = ? AND usuario_id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ii", $pedido_id, $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dados_pedido = $resultado->fetch_assoc();
        $stmt->close();
        return $dados_pedido ?: null;
    }

    public function getItensPorPedidoId(int $pedido_id): array
    {
        $itens_pedido = [];
        $sql = "SELECT 
                    ip.produto_id, 
                    ip.quantidade, 
                    ip.preco_unitario,
                    ip.tamanho, 
                    p.nome AS nome_produto,
                    p.imagem_url
                FROM itens_pedido ip 
                LEFT JOIN produtos p ON ip.produto_id = p.id
                WHERE ip.pedido_id = ?";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        while ($item = $resultado->fetch_assoc()) {
            $itens_pedido[] = $item;
        }
        $stmt->close();
        return $itens_pedido;
    }
    public function getTodosPedidosAdmin(string $status_filtro = 'Todos', ?int $user_id_filtro = null): array
    {
        $pedidos = [];
        $params = [];
        $types = '';
        $where_parts = [];

        if ($status_filtro !== 'Todos') {
            $where_parts[] = "p.status = ?";
            $params[] = $status_filtro;
            $types .= "s";
        }
        if ($user_id_filtro) {
            $where_parts[] = "p.usuario_id = ?";
            $params[] = $user_id_filtro;
            $types .= "i";
        }
        $where_clause = empty($where_parts) ? "" : "WHERE " . implode(" AND ", $where_parts);

        $sql = "SELECT 
                    p.id, p.data_pedido, p.total_geral, p.status, 
                    u.nome AS nome_usuario, u.email AS email_usuario
                FROM pedidos p
                JOIN usuarios u ON p.usuario_id = u.id
                {$where_clause}
                ORDER BY p.data_pedido DESC";

        $stmt = $this->conexao->prepare($sql);
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $resultado = $stmt->get_result();
            $pedidos = $resultado->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
        }
        return $pedidos;
    }
    public function getPedidoAdminPorId(int $pedido_id): ?array
    {
        $sql = "SELECT 
                    p.*, 
                    u.nome AS nome_usuario, 
                    u.email AS email_usuario,
                    u.telefone AS telefone_usuario
                FROM pedidos p
                JOIN usuarios u ON p.usuario_id = u.id
                WHERE p.id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $pedido_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $dados = $resultado->fetch_assoc();
        $stmt->close();
        return $dados ?: null;
    }

    public function updateStatusPedido(int $pedido_id, string $novo_status): bool
    {
        $sql_update = "UPDATE pedidos SET status = ? WHERE id = ?";
        $stmt_update = $this->conexao->prepare($sql_update);
        if ($stmt_update) {
            $stmt_update->bind_param("si", $novo_status, $pedido_id);
            $sucesso = $stmt_update->execute();
            $stmt_update->close();
            return $sucesso;
        }
        return false;
    }

    public function getPedidosPorUsuario(int $usuario_id): array
    {
        $pedidos = [];

        $sql_pedidos = "SELECT id, data_pedido, total_geral, status FROM pedidos WHERE usuario_id = ? ORDER BY data_pedido DESC";
        $stmt_pedidos = $this->conexao->prepare($sql_pedidos);

        if (!$stmt_pedidos) {
            error_log("Falha ao preparar a query de pedidos: " . $this->conexao->error);
            return [];
        }

        $stmt_pedidos->bind_param("i", $usuario_id);
        $stmt_pedidos->execute();
        $resultado = $stmt_pedidos->get_result();

        while ($pedido = $resultado->fetch_assoc()) {
            $pedido['itens'] = $this->getItensPorPedidoId($pedido['id']);

            $pedido['data_pedido_formatada'] = date('d/m/Y H:i', strtotime($pedido['data_pedido']));

            $pedidos[] = $pedido;
        }

        $stmt_pedidos->close();
        return $pedidos;
    }

    public function hasUserPurchasedProduct(int $usuario_id, int $produto_id): bool
    {
        $sql = "SELECT COUNT(ip.id) AS total
                FROM itens_pedido ip
                JOIN pedidos p ON ip.pedido_id = p.id
                WHERE p.usuario_id = ? 
                  AND ip.produto_id = ?
                  AND p.status = 'Entregue'";

        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $produto_id);
        $stmt->execute();
        $resultado = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        return ($resultado['total'] ?? 0) > 0;
    }

    public function hasUserReviewedProduct(int $usuario_id, int $produto_id): bool
    {
        $sql = "SELECT COUNT(*) FROM produto_avaliacoes WHERE usuario_id = ? AND produto_id = ?";
        $stmt = $this->conexao->prepare($sql);
        
        if (!$stmt) {
            return true; 
        }

        $stmt->bind_param("ii", $usuario_id, $produto_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $linha = $resultado->fetch_row();
        $count = $linha[0] ?? 0;
        $stmt->close();

        return $count > 0;
    }
}