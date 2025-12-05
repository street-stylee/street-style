<?php

namespace App\Models;

use \mysqli;
use Exception;

class CarrinhoModel {

    private $conexao;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
    }

    public function contarItensCarrinho(?int $usuario_id, ?string $carrinho_session_id): int {
        
        $quantidade_carrinho = 0;
        $is_user = !empty($usuario_id);
        $ref_id = $usuario_id ?? $carrinho_session_id;

        if (empty($ref_id)) {
            return 0;
        }
        
        if ($this->conexao->connect_error) {
            return 0;
        }

        $sql_count = "SELECT SUM(quantidade) FROM carrinho_itens 
                      WHERE " . ($is_user ? "usuario_id = ?" : "carrinho_session_id = ?");

        if ($stmt_count = $this->conexao->prepare($sql_count)) {
            
            if ($is_user) {
                $stmt_count->bind_param("i", $ref_id);
            } else {
                $stmt_count->bind_param("s", $ref_id);
            }

            $stmt_count->execute();
            $resultado = $stmt_count->get_result();

            if ($linha = $resultado->fetch_row()) {
                $quantidade_carrinho = (int) ($linha[0] ?? 0); 
            }

            $stmt_count->close();
        }

        return $quantidade_carrinho;
    }

    public function migrarCarrinho(int $usuario_id, string $session_id_antiga): bool {
        
        $this->conexao->begin_transaction();
        try {
            $sql_delete_duplicates = "
                DELETE CI_SESSION FROM carrinho_itens CI_SESSION
                INNER JOIN carrinho_itens CI_USER ON 
                    CI_SESSION.variacao_id = CI_USER.variacao_id AND CI_USER.usuario_id = ?
                WHERE CI_SESSION.carrinho_session_id = ? AND CI_SESSION.usuario_id IS NULL;
            ";
            $stmt_delete = $this->conexao->prepare($sql_delete_duplicates);
            $stmt_delete->bind_param("is", $usuario_id, $session_id_antiga);
            $stmt_delete->execute();
            $stmt_delete->close();

            $sql_migrate = "
                UPDATE carrinho_itens 
                SET usuario_id = ?, carrinho_session_id = NULL 
                WHERE carrinho_session_id = ? AND usuario_id IS NULL;
            ";
            $stmt_migrate = $this->conexao->prepare($sql_migrate);
            $stmt_migrate->bind_param("is", $usuario_id, $session_id_antiga);
            $stmt_migrate->execute();
            $stmt_migrate->close();

            $this->conexao->commit();
            return true;

        } catch (Exception $e) {
            $this->conexao->rollback();
            return false;
        }
    }
    
    public function getItensCarrinhoComDetalhes(?int $usuario_id, ?string $session_id): array {
        
        $carrinho_ref_val = $usuario_id ?? $session_id;
        $is_user = $usuario_id !== null;

        if (!$carrinho_ref_val) {
            return [];
        }
        
        $sql_carrinho_itens = "
            SELECT 
                CI.id AS item_carrinho_id, 
                CI.quantidade, 
                P.preco AS preco_unitario,
                P.id AS produto_id, 
                P.nome AS nome_produto, 
                P.imagem_url,
                PV.tamanho,
                PV.id AS variacao_id
            FROM carrinho_itens CI
            JOIN produtos P ON CI.produto_id = P.id
            JOIN produto_variacoes PV ON CI.variacao_id = PV.id
            WHERE " . ($is_user ? "CI.usuario_id = ?" : "CI.carrinho_session_id = ?");

        $stmt = $this->conexao->prepare($sql_carrinho_itens);

        if ($is_user) {
            $stmt->bind_param("i", $carrinho_ref_val);
        } else {
            $stmt->bind_param("s", $carrinho_ref_val);
        }

        $stmt->execute();
        $resultado = $stmt->get_result();
        $itens = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();

        return $itens;
    }

    public function adicionarItem(int $produto_id, int $variacao_id, int $quantidade, ?int $usuario_id, ?string $session_id): bool|string {

        $carrinho_ref_id = $usuario_id ?? $session_id;
        $is_user = $usuario_id !== null;

        if (!$carrinho_ref_id) {
            return 'error_data';
        }
        
        $sql_check = "SELECT estoque FROM produto_variacoes WHERE id = ? AND produto_id = ?";
        $stmt_check = $this->conexao->prepare($sql_check);
        $stmt_check->bind_param("ii", $variacao_id, $produto_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows === 0) {
            $stmt_check->close();
            return 'error_variacao';
        }
        $estoque_disponivel = $result_check->fetch_assoc()['estoque'];
        $stmt_check->close();

        $sql_carrinho = "SELECT id, quantidade FROM carrinho_itens 
                         WHERE variacao_id = ? AND " . ($is_user ? "usuario_id = ?" : "carrinho_session_id = ?");
        $stmt_carrinho = $this->conexao->prepare($sql_carrinho);

        if ($is_user) {
            $stmt_carrinho->bind_param("ii", $variacao_id, $carrinho_ref_id);
        } else {
            $stmt_carrinho->bind_param("is", $variacao_id, $carrinho_ref_id);
        }
        $stmt_carrinho->execute();
        $item_existente = $stmt_carrinho->get_result()->fetch_assoc();
        $stmt_carrinho->close();

        $nova_quantidade = $quantidade + ($item_existente['quantidade'] ?? 0);

        if ($nova_quantidade > $estoque_disponivel) {
            return 'error_estoque';
        }

        $this->conexao->begin_transaction();
        $sucesso = true;

        try {
            if ($item_existente) {
                $sql_update = "UPDATE carrinho_itens SET quantidade = ? WHERE id = ?";
                $stmt_update = $this->conexao->prepare($sql_update);
                $stmt_update->bind_param("ii", $nova_quantidade, $item_existente['id']);
                if (!$stmt_update->execute()) $sucesso = false;
                $stmt_update->close();

            } else {
                if ($is_user) {
                     $sql_insert = "INSERT INTO carrinho_itens (usuario_id, produto_id, variacao_id, quantidade) 
                                    VALUES (?, ?, ?, ?)";
                     $stmt_insert = $this->conexao->prepare($sql_insert);
                     $stmt_insert->bind_param("iiii", $usuario_id, $produto_id, $variacao_id, $quantidade);
                } else {
                     $sql_insert = "INSERT INTO carrinho_itens (carrinho_session_id, produto_id, variacao_id, quantidade) 
                                    VALUES (?, ?, ?, ?)";
                     $stmt_insert = $this->conexao->prepare($sql_insert);
                     $stmt_insert->bind_param("siii", $session_id, $produto_id, $variacao_id, $quantidade);
                }
                
                if (!$stmt_insert->execute()) $sucesso = false;
                $stmt_insert->close();
            }

            if ($sucesso) {
                $this->conexao->commit();
                return true;
            } else {
                $this->conexao->rollback();
                return 'error_db';
            }

        } catch (Exception $e) {
            $this->conexao->rollback();
            return 'error_db';
        }
    }

    public function limparCarrinho(?int $usuario_id, ?string $session_id): bool {
        $carrinho_ref_id = $usuario_id ?? $session_id;
        $is_user = $usuario_id !== null;

        if (!$carrinho_ref_id) return true;

        $sql = "DELETE FROM carrinho_itens WHERE " . ($is_user ? "usuario_id = ?" : "carrinho_session_id = ?");
        $stmt = $this->conexao->prepare($sql);
        
        if ($is_user) {
            $stmt->bind_param("i", $carrinho_ref_id);
        } else {
            $stmt->bind_param("s", $carrinho_ref_id);
        }
        
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }
    
    public function gerenciarItem(int $item_carrinho_id, string $acao): bool|string {
        
        if ($acao === 'excluir') {
            $sql = "DELETE FROM carrinho_itens WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            $stmt->bind_param("i", $item_carrinho_id);
            $sucesso = $stmt->execute();
            $stmt->close();
            return $sucesso ? true : 'error_db';
        }
        
        $this->conexao->begin_transaction();
        $sucesso = true;

        try {
            $sql_select = "SELECT CI.quantidade, CI.variacao_id, PV.estoque
                           FROM carrinho_itens CI
                           JOIN produto_variacoes PV ON CI.variacao_id = PV.id
                           WHERE CI.id = ?";
            $stmt_select = $this->conexao->prepare($sql_select);
            $stmt_select->bind_param("i", $item_carrinho_id);
            $stmt_select->execute();
            $item = $stmt_select->get_result()->fetch_assoc();
            $stmt_select->close();

            if (!$item) {
                $this->conexao->rollback();
                return 'error_data';
            }
            
            $nova_quantidade = $item['quantidade'] + ($acao === 'aumentar' ? 1 : -1);

            if ($nova_quantidade < 1) {
                $sql = "DELETE FROM carrinho_itens WHERE id = ?";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bind_param("i", $item_carrinho_id);
                if (!$stmt->execute()) $sucesso = false;
                $stmt->close();
                
            } elseif ($acao === 'aumentar' && $nova_quantidade > $item['estoque']) {
                $this->conexao->rollback();
                return 'error_estoque';
                
            } else {
                $sql = "UPDATE carrinho_itens SET quantidade = ? WHERE id = ?";
                $stmt = $this->conexao->prepare($sql);
                $stmt->bind_param("ii", $nova_quantidade, $item_carrinho_id);
                if (!$stmt->execute()) $sucesso = false;
                $stmt->close();
            }

            if ($sucesso) {
                $this->conexao->commit();
                return true;
            } else {
                $this->conexao->rollback();
                return 'error_db';
            }

        } catch (Exception $e) {
            $this->conexao->rollback();
            return 'error_db';
        }
    }
}