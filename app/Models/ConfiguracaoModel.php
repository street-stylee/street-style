<?php

namespace App\Models;
use \mysqli;

class ConfiguracaoModel {

    protected $conexao;

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
    }

    public function getAllSettings(): array {
        $settings = [];
        $sql = "SELECT config_key, config_value FROM configuracoes";
        if ($result = $this->conexao->query($sql)) {
            while ($row = $result->fetch_assoc()) {
                $settings[$row['config_key']] = $row['config_value'];
            }
        }
        return $settings;
    }

    public function updateSetting(string $key, string $value): bool {
        $sql = "INSERT INTO configuracoes (config_key, config_value) 
                VALUES (?, ?) 
                ON DUPLICATE KEY UPDATE config_value = ?";
                
        $stmt = $this->conexao->prepare($sql);
        $stmt->bind_param("sss", $key, $value, $value);
        $sucesso = $stmt->execute();
        $stmt->close();
        return $sucesso;
    }
}