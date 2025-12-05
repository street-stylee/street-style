<?php

namespace App\Controllers\Admin;

use \mysqli;

class LogController {

    protected $conexao;
    
    private $log_levels = [
        'INFO' => 'INFO (Informação)',
        'WARN' => 'WARN (Aviso)',
        'ERROR' => 'ERROR (Erro)',
        'CRITICAL' => 'CRITICAL (Crítico)',
    ];

    public function __construct(mysqli $conexao) {
        $this->conexao = $conexao;
        
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['admin_id'])) { 
             $this->redirect('/admin/login');
        }
    }

    public function index() {
        $filtro_level = $_GET['level'] ?? '';
        $filtro_search = $_GET['search'] ?? '';
        $filtro_user_id = $_GET['user_id'] ?? '';
        
        $sql = "SELECT id, level, message, user_id, created_at FROM system_logs WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filtro_level) && array_key_exists($filtro_level, $this->log_levels)) {
            $sql .= " AND level = ?";
            $types .= "s";
            $params[] = $filtro_level;
        }

        if (!empty($filtro_search)) {
            $sql .= " AND message LIKE ?";
            $types .= "s";
            $params[] = "%" . $filtro_search . "%";
        }
        
        if (is_numeric($filtro_user_id) && $filtro_user_id > 0) {
            $sql .= " AND user_id = ?";
            $types .= "i";
            $params[] = (int)$filtro_user_id;
        }

        $sql .= " ORDER BY created_at DESC LIMIT 200";

        $logs = [];
        
        $stmt = $this->conexao->prepare($sql);
        
        if ($stmt) {
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            $stmt->execute();
            $result = $stmt->get_result();

            while ($row = $result->fetch_assoc()) {
                $logs[] = $row;
            }
            $stmt->close();
        } else {
             error_log("Erro ao carregar logs: " . $this->conexao->error);
        }

        $dados = [
            'titulo_pagina' => 'Logs do Sistema (DB)',
            'logs' => $logs,
            'log_levels' => $this->log_levels, 
            'filtro_level' => $filtro_level,
            'filtro_search' => $filtro_search,
            'filtro_user_id' => $filtro_user_id
        ];

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Logs/index', $dados); 
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    public function details() {
        $log_id = $_GET['id'] ?? 0;
        $log_details = null;
        
        if (is_numeric($log_id) && $log_id > 0) {
            $sql = "SELECT * FROM system_logs WHERE id = ?";
            $stmt = $this->conexao->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("i", $log_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $log_details = $result->fetch_assoc();
                $stmt->close();
            }
        }
        
        if ($log_details && isset($log_details['context'])) {
            $log_details['context_decoded'] = json_decode($log_details['context'], true);
        }

        $dados = [
            'titulo_pagina' => 'Detalhes do Log',
            'log' => $log_details,
        ];

        $this->carregarView('Admin/Layout/header', $dados);
        $this->carregarView('Admin/Logs/details', $dados); 
        $this->carregarView('Admin/Layout/footer', $dados);
    }

    private function carregarView(string $caminho, array $dados = []) {
        extract($dados);
        require_once ROOT . "/app/Views/{$caminho}.php";
    }
    
    private function redirect(string $url) {
        header("Location: " . BASE_URL . $url);
        exit;
    }
}