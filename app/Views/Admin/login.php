<?php
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo_pagina; ?></title>
    <link rel="shortcut icon" href="<?php echo BASE_URL; ?>/_ADM/favicon.ico/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/form_auth.css">
    <style>
        body { background-color: #f4f7f6; }
        .form-box { background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="form-box">
            <h2>Login do Administrador</h2>
            
            <?php if (!empty($mensagem_erro)): ?>
                <div class="message error">
                    <?php echo htmlspecialchars($mensagem_erro); ?>
                </div>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/admin/login/processar" method="POST">
                <div class="input-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="input-group">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" required>
                </div>
                <button type="submit" class="btn-submit">Entrar</button>
            </form>
        </div>
    </div>
</body>
</html>