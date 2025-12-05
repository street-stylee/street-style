<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin_id'])) {
    header("Location: " . (defined('BASE_URL') ? BASE_URL : '') . "/admin/login");
    exit;
}

$admin_nome = htmlspecialchars($_SESSION['admin_nome'] ?? 'Admin');
$base_url = defined('BASE_URL') ? BASE_URL : '';
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina ?? 'Painel Admin'; ?> - Street Style</title>

    <link rel="shortcut icon" href="<?php echo $base_url; ?>/_ADM/favicon.ico/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <script src="<?php echo $base_url; ?>/js/menu.js" defer></script>

    <style>
        :root {
            --cor-primaria: #3498db;
            --cor-secundaria: #2c3e50;
            --cor-fundo: #f4f7f6;
            --cor-branco: #ffffff;
            --cor-texto: #333;
            --cor-texto-claro: #ecf0f1;
            --cor-borda: #e0e0e0;
            --cor-sucesso: #27ae60;
            --cor-erro: #e74c3c;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background-color: var(--cor-fundo);
            color: var(--cor-texto);
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .admin-header {
            background-color: var(--cor-branco);
            color: var(--cor-texto);
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--cor-borda);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .admin-header .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .admin-header .logo {
            font-size: 1.5em;
            font-weight: 700;
            text-decoration: none;
            color: var(--cor-primaria);
        }

        .logo img {
            max-width: 150px;
            height: auto;
        }

        .menu-toggle {
            display: none;
            background: none;
            border: none;
            font-size: 1.8em;
            color: var(--cor-secundaria);
            cursor: pointer;
            padding: 5px;
            transition: transform 0.3s ease;
        }

        .menu-toggle:hover {
            transform: scale(1.1);
        }

        .menu-toggle.active {
            transform: rotate(90deg);
        }

        .admin-header .user-info {
            display: flex;
            align-items: center;
        }

        .admin-header .user-info span {
            margin-right: 20px;
            font-weight: 500;
        }

        .admin-header .user-info a {
            color: var(--cor-erro);
            text-decoration: none;
            font-size: 0.9em;
            font-weight: 500;
        }

        .admin-header .user-info a:hover {
            text-decoration: underline;
        }

        .admin-container {
            display: flex;
            flex-grow: 1;
            position: relative;
        }

        .admin-sidebar {
            width: 240px;
            background-color: var(--cor-secundaria);
            min-height: calc(100vh - 66px);
            padding-top: 20px;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            overflow-y: auto;
            overflow-x: hidden;
        }

        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }

        .admin-sidebar::-webkit-scrollbar-track {
            background: #1a252f;
        }

        .admin-sidebar::-webkit-scrollbar-thumb {
            background: #3498db;
            border-radius: 3px;
        }

        .admin-sidebar ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .admin-sidebar li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 16px 25px;
            color: var(--cor-texto-claro);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.95em;
            border-left: 4px solid transparent;
            transition: all 0.3s ease;
        }

        .admin-sidebar li a:hover {
            background-color: #34495e;
            border-left: 4px solid var(--cor-primaria);
        }

        .admin-sidebar li a .bx {
            font-size: 1.3em;
        }

        .sidebar-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 998;

            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0s 0.3s;
        }

        .sidebar-overlay.active {
            opacity: 1;
            visibility: visible;
            transition: opacity 0.3s ease, visibility 0s;
        }

        .admin-main {
            flex: 1;
            padding: 30px 40px;
            overflow-y: auto;
        }

        .admin-main h1 {
            font-size: 2em;
            font-weight: 600;
            color: var(--cor-secundaria);
            border-bottom: 2px solid var(--cor-borda);
            padding-bottom: 10px;
            margin-top: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .admin-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-novo {
            background-color: var(--cor-sucesso);
            color: white;
            padding: 10px 18px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            font-size: 0.9em;
            transition: background-color 0.3s;
        }

        .btn-novo:hover {
            background-color: #219150;
        }

        .btn-novo .bx {
            margin-right: 5px;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--cor-branco);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
            border-radius: 8px;
            overflow: hidden;
        }

        .admin-table th,
        .admin-table td {
            padding: 16px;
            text-align: left;
            border-bottom: 1px solid var(--cor-borda);
        }

        .admin-table th {
            background-color: #f9fafb;
            font-size: 0.85em;
            text-transform: uppercase;
            color: #555;
        }

        .admin-table td {
            color: var(--cor-texto);
            vertical-align: middle;
        }

        .admin-table tr:last-child td {
            border-bottom: none;
        }

        .admin-table .acoes a {
            text-decoration: none;
            margin-right: 12px;
            font-weight: 600;
            font-size: 0.9em;
        }

        .acoes .editar {
            color: var(--cor-primaria);
        }

        .acoes .excluir {
            color: var(--cor-erro);
        }

        .alert-success {
            padding: 15px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            padding: 15px;
            background-color: #f8d7da;
            color: #721c24;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {

            .menu-toggle {
                display: block;
            }

            .admin-header {
                padding: 10px 15px;
            }

            .logo img {
                max-width: 120px;
            }

            .admin-header .user-info span {
                display: none;
            }

            .admin-sidebar {
                position: fixed;
                top: 0;
                left: 0;
                height: 100vh;
                z-index: 999;
                transform: translateX(-100%);
                width: 280px;
                padding-top: 70px;
            }

            .admin-sidebar.active {
                transform: translateX(0);
            }

            .admin-main {
                padding: 20px 15px;
            }

            .admin-main h1 {
                font-size: 1.5em;
            }
        }

        @media (max-width: 480px) {
            .admin-sidebar {
                width: 260px;
            }

            .logo img {
                max-width: 100px;
            }

            .admin-main {
                padding: 15px 10px;
            }
        }
    </style>
</head>

<body>

    <header class="admin-header">
        <div class="header-left">
            <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                <i class='bx bx-menu'></i>
            </button>
            <a href="<?php echo $base_url; ?>/admin/dashboard" class="logo">
                <img src="<?php echo $base_url; ?>/_ADM/img/logotipo.png" alt="Logotipo">
            </a>
        </div>
        <div class="user-info">
            <span>Olá, <?php echo $admin_nome; ?></span>
            <a href="<?php echo $base_url; ?>/admin/login/logout">Sair</a>
        </div>
    </header>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="admin-container">
        <nav class="admin-sidebar" id="adminSidebar">
            <ul>
                <li><a href="<?php echo $base_url; ?>/admin/dashboard"><i class='bx bxs-dashboard'></i> Dashboard</a></li>
                <li><a href="<?php echo $base_url; ?>/admin/produtos"><i class='bx bxs-t-shirt'></i> Produtos</a></li>
                <li><a href="<?php echo $base_url; ?>/admin/pedidos"><i class='bx bxs-package'></i> Pedidos</a></li>
                <li><a href="<?php echo $base_url; ?>/admin/clientes"><i class='bx bxs-group'></i> Clientes</a></li>
                <li><a href="<?php echo $base_url; ?>/admin/avaliacoes"><i class='bx bxs-star-half'></i> Avaliações</a></li>
                <li><a href="<?php echo $base_url; ?>/admin/contato"><i class='bx bxs-message-dots'></i> Mensagens</a></li>
                <li><a href="<?php echo $base_url; ?>/admin/carrossel"><i class='bx bxs-carousel'></i> Carrossel</a></li>
                <li><a href="<?php echo $base_url; ?>/admin/usuarios"><i class='bx bxs-user'></i> Usuários ADM</a></li>
                <li><a href="<?php echo $base_url; ?>/admin/log"><i class='bx bxs-archive'></i> Logs</a></li>
                <li><a href="<?php echo $base_url; ?>/admin/configuracoes"><i class='bx bxs-cog'></i> Configurações</a></li>
            </ul>
        </nav>

        <main class="admin-main">