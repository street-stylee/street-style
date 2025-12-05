<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/perfil.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/form_geral.css">

<section class="perfil-section">
    <div class="perfil-card editar-card">

        <div class="perfil-header">
            <i class='bx bxs-lock-alt'></i>
            <h2>Alterar Senha</h2>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem-feedback <?php echo $tipo_mensagem; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/alterar_senha" method="POST" class="form-perfil">

            <div class="form-group">
                <label for="senha_atual"><i class='bx bxs-lock-alt'></i> Senha Atual</label>
                <input type="password" id="senha_atual" name="senha_atual" required>
            </div>
            <hr style="border-color: var(--color-cinza2); margin: 25px 0;">
            <div class="form-group">
                <label for="nova_senha"><i class='bx bxs-key'></i> Nova Senha (mín. 6 caracteres)</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
            </div>
            <div class="form-group">
                <label for="confirmar_senha"><i class='bx bxs-key'></i> Confirme a Nova Senha</label>
                <input type="password" id="confirmar_senha" name="confirmar_senha" required>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class='bx bxs-check-circle'></i> Mudar Senha</button>
                <a href="<?php echo BASE_URL; ?>/perfil" class="btn-cancelar"><i class='bx bx-arrow-back'></i> Voltar ao Perfil</a>
            </div>
        </form>
    </div>
</section>