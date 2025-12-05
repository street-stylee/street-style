<?php
namespace App\Models;
use \mysqli;

class FavoritoModel {

    protected $conexao;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
    }


    private function isFavorito(int $usuario_id, int $produto_id): bool {
        $sql = "SELECT id FROM favoritos WHERE usuario_id = ? AND produto_id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $produto_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $stmt->close();
        return $resultado->num_rows > 0;
    }

    public function toggle(int $usuario_id, int $produto_id): string {
        $action = "error";
        
        if ($this->isFavorito($usuario_id, $produto_id)) {
            $sql = "DELETE FROM favoritos WHERE usuario_id = ? AND produto_id = ?";
            $action = "removed";
        } else {
            $sql = "INSERT INTO favoritos (usuario_id, produto_id) VALUES (?, ?)";
            $action = "added";
        }
        
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("ii", $usuario_id, $produto_id);
        
        if (!$stmt->execute()) {
             $action = "error";
        }
        
        $stmt->close();
        return $action;
    }

    public function getFavoritoIDsPorUsuario(int $usuario_id): array {
        $ids = [];
        $sql = "SELECT produto_id FROM favoritos WHERE usuario_id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        while ($row = $resultado->fetch_assoc()) {
            $ids[] = $row['produto_id'];
        }
        $stmt->close();
        return $ids;
    }
    public function getFavoritosComDetalhes(int $usuario_id): array {
        $produtos = [];
        $sql = "SELECT p.id, p.nome, p.preco, p.imagem_url, p.avaliacao_media, p.preco_promocional, p.is_promocao
                FROM favoritos f
                JOIN produtos p ON f.produto_id = p.id
                WHERE f.usuario_id = ?
                ORDER BY f.data_adicao DESC";
        
        $stmt = $this->conexao->prepare($sql);
        
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        $produtos = $resultado->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
        return $produtos;
    }
}