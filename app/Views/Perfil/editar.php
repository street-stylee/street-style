<?php
$nome_display = htmlspecialchars($dados_usuario['nome'] ?? '');
$email_display = htmlspecialchars($dados_usuario['email'] ?? '');
$telefone_display = htmlspecialchars($dados_usuario['telefone'] ?? '');
$endereco_display = htmlspecialchars($dados_usuario['endereco'] ?? '');
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/perfil.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/form_geral.css">

<section class="perfil-section">
    <div class="perfil-card editar-card">

        <div class="perfil-header">
            <i class='bx bxs-edit-alt'></i>
            <h2>Editar Meus Dados</h2>
        </div>

        <?php if (!empty($mensagem)): ?>
            <div class="mensagem-feedback <?php echo $tipo_mensagem; ?>">
                <?php echo $mensagem; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/editar_perfil" method="POST" class="form-perfil">

            <div class="form-group">
                <label for="nome"><i class='bx bxs-user'></i> Nome Completo</label>
                <input type="text" id="nome" name="nome" value="<?php echo $nome_display; ?>" required>
            </div>
            <div class="form-group">
                <label for="email"><i class='bx bxs-envelope'></i> E-mail</label>
                <input type="email" id="email" name="email" value="<?php echo $email_display; ?>" required>
            </div>
            <div class="form-group">
                <label for="telefone"><i class='bx bxs-phone'></i> Telefone</label>
                <input type="text" id="telefone" name="telefone" value="<?php echo $telefone_display; ?>">
            </div>
            <div class="form-group">
                <label for="endereco"><i class='bx bxs-home'></i> Endereço Principal</label>
                <input type="text" id="endereco" name="endereco" value="<?php echo $endereco_display; ?>">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-submit"><i class='bx bxs-save'></i> Salvar Alterações</button>
                <a href="<?php echo BASE_URL; ?>/perfil" class="btn-cancelar"><i class='bx bx-arrow-back'></i> Voltar ao Perfil</a>
            </div>

            <hr>

            <div class="form-senha">
                <p>Deseja alterar sua senha?</p>
                <a href="<?php echo BASE_URL; ?>/alterar_senha" class="btn-perfil editar-senha-btn">
                    <i class='bx bxs-lock-alt'></i> Alterar Senha
                </a>
            </div>
        </form>
    </div>
</section>