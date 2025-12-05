<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/stylecontato.css">

<style>
    .message {
        padding: 15px;
        margin-bottom: 20px;
        border-radius: 5px;
        font-weight: bold;
        text-align: center;
    }

    .message.sucesso {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .message.erro {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<section class="form-espaco">
    <form action="<?php echo BASE_URL; ?>/contato" method="post" class="formContato">
        <h1>Entre em contato</h1>
        <p>Preencher o formulário para realizar o contato</p>

        <?php if (!empty($mensagem)): ?>
            <div class="message <?php echo htmlspecialchars($tipo_mensagem); ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <div class="inputP">
            <input type="text" name="nome" id="nome" class="input" required>
            <label for="nome">Nome completo</label>
        </div>

        <div class="inputP">
            <input type="text" name="email" id="email" class="input" required>
            <label for="email">Email de contato</label>
        </div>

        <div class="inputP">
            <input type="tel" name="telefone" id="telefone" class="input" required>
            <label for="telefone">Telefone de contato</label>
        </div>

        <div class="inputP">
            <input type="text" name="contato" id="contato" class="input" required>
            <label for="contato">Motivo do contato</label>
        </div>

        <div class="botao"><input type="submit" value="Enviar"></div>
    </form>
</section>