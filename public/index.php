<?php

if (!isset($_SESSION['usuario_id']) && isset($_COOKIE['remember_token'])) {

    $token = $_COOKIE['remember_token'];

    $sql = "SELECT id, nome FROM usuarios WHERE remember_token = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $user = $resultado->fetch_assoc();

        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['usuario_nome'] = $user['nome'];
    }
}


session_start();

define('ROOT', dirname(__DIR__)); 
define('APP_PATH', ROOT . '/app');

$base_url = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', rtrim(str_replace('\\', '/', $base_url), '/'));


spl_autoload_register(function ($classe) {
    $prefixo = 'App\\';
    
    $base_dir = APP_PATH . '/';

    $len = strlen($prefixo);
    if (strncmp($prefixo, $classe, $len) !== 0) {
        return;
    }

    $relativa = substr($classe, $len);

    $arquivo = $base_dir . str_replace('\\', '/', $relativa) . '.php';

    if (file_exists($arquivo)) {
        require_once $arquivo;
    }
});

use App\Models\Database;

$db_instance = Database::getInstance();
$conexao = $db_instance->getConexao();

$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$route = '';

if (strpos($request_uri, BASE_URL) === 0) {
    $route = substr($request_uri, strlen(BASE_URL));
} else {
    $route = $request_uri;
}

$route = trim($route, '/');
$segments = explode('/', $route);


$namespace = "App\\Controllers\\";
$controllerName = 'Home';
$actionName = 'index';
$params = [];

if (!empty($segments[0]) && strtolower($segments[0]) === 'admin') {

    $namespace = "App\\Controllers\\Admin\\";
    $controllerName = ucfirst(strtolower($segments[1] ?? 'Login')); 
    $actionName = strtolower($segments[2] ?? 'index'); 
    $params = array_slice($segments, 3);
} elseif (!empty($segments[0])) {

    $controllerName = ucfirst(strtolower($segments[0]));
    $actionName = strtolower($segments[1] ?? 'index');
    $params = array_slice($segments, 2);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($segments[2])) {
    $actionName = strtolower($segments[2]);
    $params = array_slice($segments, 3);
}

$controllerClass = $namespace . $controllerName . "Controller"; 


if (class_exists($controllerClass)) {
    try {
        $controller = new $controllerClass($conexao);
        
        if (method_exists($controller, $actionName)) {
            call_user_func_array([$controller, $actionName], $params);
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "Erro 404: Ação '{$actionName}' não encontrada no Controller '{$controllerName}'.";
        }
    } catch (Exception $e) {
         header("HTTP/1.0 500 Internal Server Error");
         echo "Erro 500: " . $e->getMessage();
    }
} else {
    header("HTTP/1.0 404 Not Found");
    echo "Erro 404: Controller '{$controllerClass}' não encontrado.";
}