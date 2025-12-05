<?php

namespace App\Services;

use \mysqli;


class LogService
{
    private $conexao;
    
    const INFO = 'INFO';
    const WARN = 'WARN';
    const ERROR = 'ERROR';
    const CRITICAL = 'CRITICAL';

   
    public function __construct(mysqli $conexao)
    {
        $this->conexao = $conexao;
    }
    public function log(string $level, string $message, ?int $userId = null, array $context = []): bool
    {
        $validLevels = [self::INFO, self::WARN, self::ERROR, self::CRITICAL];
        if (!in_array($level, $validLevels)) {
            $level = self::INFO; 
        }

        $level = $this->conexao->real_escape_string($level);
        $message = $this->conexao->real_escape_string($message);
        $userIdSql = $userId ? (int)$userId : 'NULL';
        
       
        $contextJson = $context ? $this->conexao->real_escape_string(json_encode($context)) : 'NULL';

        $sql = "INSERT INTO system_logs (level, message, user_id, context) VALUES (?, ?, ?, ?)";
        
        $stmt = $this->conexao->prepare($sql);
        if ($stmt === false) {
             error_log("Erro ao preparar o log: " . $this->conexao->error);
             return false;
        }

        $stmt->bind_param("ssis", $level, $message, $userIdSql, $contextJson);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        } else {
            error_log("Erro ao executar o log: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }
}