<?php
namespace App\Models;
use \mysqli;
class Database
{
    private static $host = "localhost:3306";
    private static $user = "root";
    private static $password = "";
    private static $database = "streetstyle";
    private static $instance = null;

    private $conexao;

    private function __construct()
    {
        $this->conexao = new mysqli(self::$host, self::$user, self::$password, self::$database);

        if ($this->conexao->connect_error) {
            die("Erro de Conexão com o Banco de Dados: " . $this->conexao->connect_error);
        }

        $this->conexao->set_charset("utf8mb4");
    }

    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConexao()
    {
        return $this->conexao;
    }
}