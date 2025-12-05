<?php

namespace App\Controllers;
use \mysqli;

class LogoutController {

    public function __construct(mysqli $conexao) {
    }

    public function index() {
        session_unset();
        session_destroy();
        
        $this->redirect('/login');
    }

    private function redirect(string $url) {
        if (!str_starts_with($url, '/')) {
             $url = '/' . $url;
        }
        header("Location: " . BASE_URL . $url);
        exit;
    }
}