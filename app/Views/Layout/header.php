<?php

$titulo_pagina = $titulo_pagina ?? 'Street Style';
$primeiro_nome = $primeiro_nome ?? 'Convidado';
$usuario_logado = $usuario_logado ?? false;
$quantidade_carrinho = $quantidade_carrinho ?? 0;

if (!function_exists('display_image_url')) {
    function display_image_url($path) {
        $path = trim((string) $path);
        $display_base = rtrim(BASE_URL, '/');

        if ($path === '') {
            return $display_base . '/_ADM/img/no-image.png';
        }

        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }

        $clean = ltrim($path, '/');
        if (strpos($clean, '_ADM/') === 0) {
            return $display_base . '/' . $clean;
        }
        return $display_base . '/_ADM/' . $clean;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_pagina); ?></title>

    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/index.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/header-footer.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@100;200;300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/_ADM/favicon.ico/favicon.ico" type="image/x-icon">

    <style>
        header {
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #fff;
            padding: 20px 5%;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all .40s ease;
        }

        .logo img {
            max-width: 180px;
            height: auto;
        }

        .navmenu {
            display: flex;
        }

        .navmenu a {
            color: #2c2c2c;
            font-size: 1.1rem;
            font-weight: 500;
            padding: 5px 10px;
            margin: 0 15px;
            transition: all .40s ease;
            text-decoration: none;
            text-transform: uppercase;
        }

        .navmenu a:hover {
            color: #ff8c00;
        }

        .nav-icon {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .nav-icon i {
            font-size: 24px;
            color: #2c2c2c;
            transition: all .40s ease;
        }

        .nav-icon i:hover {
            color: #ff8c00;
            transform: scale(1.1);
        }

        .carrinho-link {
            position: relative;
            text-decoration: none;
        }

        .carrinho-badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: #e74c3c;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 12px;
            font-weight: bold;
        }

        #menu-icon {
            font-size: 35px;
            color: #2c2c2c;
            cursor: pointer;
            z-index: 10001;
            display: none;
        }

        .mobile-only {
            display: none;
        }

        .desktop-search {
            display: block;
        }

        @media (max-width: 1080px) {
            header {
                padding: 15px 4%;
            }

            .logo img {
                width: 100px;
            }

            .navmenu a {
                margin: 0 10px;
                font-size: 0.7rem;
            }

            .search-form input[type="text"] {
                font-size: 0.5rem;
                padding: 5px 10px;
            }

            .search-container {
                width: 180px;
            }

            .nav-icon i {
                font-size: 17px;
            }
        }

        @media (max-width: 860px) {

            #menu-icon {
                display: block;
            }

            .desktop-search {
                display: none;
            }

            .navmenu {
                position: absolute;
                top: 100%;
                right: -100%;
                width: 280px;
                height: 100vh;
                background: #ffffff;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                padding: 30px 20px;
                transition: all 0.4s ease;
                box-shadow: -2px 5px 10px rgba(0, 0, 0, 0.1);
                border-top: 1px solid #eee;
            }

            .navmenu.open {
                right: 0;
            }

            .navmenu a {
                display: block;
                margin: 15px 0;
                padding: 0;
                font-size: 1.1rem;
            }

            .mobile-only {
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            .nav-icon .desktop-hide {
                display: none;
            }
        }
    </style>
</head>

<body>

    <header>
        <a href="<?php echo BASE_URL; ?>/" class="logo">
                <img src="<?php echo BASE_URL; ?>/_ADM/img/logotipo.png" alt="Street Style Logo">
            </a>

        <ul class="navmenu">
            <li><a href="<?php echo BASE_URL; ?>/">Home</a></li>
            <li><a href="<?php echo BASE_URL; ?>/produtos">Produtos</a></li>
            <li><a href="<?php echo BASE_URL; ?>/sobre">Sobre</a></li>
            <li><a href="<?php echo BASE_URL; ?>/contato">Contato</a></li>

            <li class="mobile-only">
                <hr style="border: 0; width: 230px; border-top: 1px solid #eee; margin: 10px 0;">
            </li>

            <li class="mobile-only">
                <?php if ($usuario_logado): ?>
                    <a href="<?php echo BASE_URL; ?>/perfil"><i class='bx bx-user'></i> Minha Conta</a>
                    <a href="<?php echo BASE_URL; ?>/logout" style="color: #e74c3c;"><i class='bx bx-log-out'></i> Sair</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>/login"><i class='bx bx-user'></i> Fazer Login</a>
                <?php endif; ?>
            </li>
        </ul>

        <div class="nav-icon">

            <div class="search-container desktop-search">
                <form action="<?php echo BASE_URL; ?>/buscar" method="GET" class="search-form">
                    <input type="text" name="q" placeholder="Buscar produtos..." required>
                    <button type="submit" title="Buscar"><i class='bx bx-search'></i></button>
                </form>
            </div>

            <a href="<?php echo $usuario_logado ? (BASE_URL . '/perfil') : (BASE_URL . '/login'); ?>"
                class="desktop-hide">
                <i class='bx bx-user'></i>
            </a>

            <a href="<?php echo BASE_URL; ?>/carrinho" class="carrinho-link">
                <i class='bx bx-cart'></i>
                <?php if ($quantidade_carrinho > 0): ?>
                    <span class="carrinho-badge"><?php echo $quantidade_carrinho; ?></span>
                <?php endif; ?>
            </a>

            <div class="bx bx-menu" id="menu-icon"></div>
        </div>

    </header>

    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let menu = document.querySelector('#menu-icon');
            let navmenu = document.querySelector('.navmenu');

            if (menu && navmenu) {
                menu.onclick = () => {
                    menu.classList.toggle('bx-x');
                    navmenu.classList.toggle('open');
                };

                window.onscroll = () => {
                    menu.classList.remove('bx-x');
                    navmenu.classList.remove('open');
                }
            }
        });
    </script>