<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/form_auth.css">

<div class="auth-container">
    <div class="form-box">
        <h2>Acessar Conta</h2>

        <?php if (!empty($mensagem_erro)): ?>
            <div class="message error">
                <?php echo htmlspecialchars($mensagem_erro); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/login?redirect=<?php echo urlencode($redirect_target); ?>" method="POST">

            <input type="hidden" name="redirect_target" value="<?php echo htmlspecialchars($redirect_target); ?>">

            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required
                    value="<?php echo htmlspecialchars($email_digitado); ?>">
            </div>
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <div class="input-group" style="display: flex; align-items: center; gap: 6px; margin-bottom: 15px;">
                <input type="checkbox" id="lembrar" name="lembrar" <?php echo isset($_COOKIE['lembrar_email']) ? 'checked' : ''; ?>>
                <label for="lembrar" style="margin-top: 2px;">Lembrar login</label>
                
            </div>

            
            <p style="text-align: right; margin-top: -10px; margin-bottom: 20px; font-size: 0.9em;">
                <a href="<?php echo BASE_URL; ?>/recuperarsenha">Esqueceu sua senha?</a>
            </p>


            <div class="form-group"
                style="margin-bottom: 15px; transform: scale(0.95); -webkit-transform: scale(0.95); transform-origin: 0 0;">
                <div class="g-recaptcha" data-sitekey="6Lc2-AUsAAAAACxYQCiTcP9nTatP72GibeEDU6PF"></div>
            </div>
            <button type="submit" class="btn-submit">Entrar</button>
        </form>
        <p>Ainda não tem conta? <a
                href="<?php echo BASE_URL; ?>/cadastro?redirect=<?php echo urlencode($redirect_target); ?>">Cadastre-se</a>
        </p>
    </div>
</div>