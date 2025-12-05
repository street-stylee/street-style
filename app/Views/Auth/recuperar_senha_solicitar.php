<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/form_auth.css">

<div class="auth-container">
    <div class="form-box">
        <h2>Recuperar Senha</h2>
        <p style="text-align: center; color: #555;">Insira seu e-mail de cadastro para receber um link de redefinição.</p>
        
        <?php if (!empty($mensagem)): ?>
            <div class="message <?php echo htmlspecialchars($tipo_mensagem ?? 'erro'); ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/recuperarsenha" method="POST">
            
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>

            <button type="submit" class="btn-submit">Enviar Link de Redefinição</button>
        </form>
        
        <p style="text-align: center; margin-top: 15px;"><a href="<?php echo BASE_URL; ?>/login">Voltar para o Login</a></p>
    </div>
</div>