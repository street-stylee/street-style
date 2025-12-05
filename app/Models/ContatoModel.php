<?php

namespace App\Models;
use \mysqli;

class ContatoModel {

    protected $conexao;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
    }

    public function salvarMensagem(string $nome, string $email, string $telefone, string $mensagem): bool {
        $sql = "INSERT INTO contato_mensagens (nome, email, telefone, mensagem) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexao->prepare($sql);
        
        if (!$stmt) return false;
        
        $stmt->bind_param("ssss", $nome, $email, $telefone, $mensagem);
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }

    public function getMensagens(): array {
        $sql = "SELECT * FROM contato_mensagens ORDER BY data_envio DESC";
        $resultado = $this->conexao->query($sql);
        return $resultado->fetch_all(MYSQLI_ASSOC);
    }

    public function deleteMensagem(int $id): bool {
        $sql = "DELETE FROM contato_mensagens WHERE id = ?";
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("i", $id);
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }
}