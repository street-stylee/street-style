<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/form_auth.css">

<div class="auth-container">
    <div class="form-box">
        <h2>Criar Conta</h2>
        
        <?php if (!empty($mensagem)): ?>
            <div class="message <?php echo htmlspecialchars($tipo_mensagem ?? 'erro'); ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/cadastro" method="POST">
            
            <div class="input-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" required value="<?php echo htmlspecialchars($nome ?? ''); ?>">
            </div>
            <div class="input-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            
            <div class="input-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            
            <ul id="password-requirements" style="list-style: none; padding: 0; margin-top: -10px; margin-bottom: 15px; font-size: 0.85em;">
                <li id="req-length" style="color: #777;"><i></i> Mínimo de 8 caracteres</li>
                <li id="req-uppercase" style="color: #777;"><i></i> Pelo menos 1 letra maiúscula</li>
                <li id="req-number" style="color: #777;"><i></i> Pelo menos 1 número</li>
            </ul>

            <div class="input-group">
                <label for="senha_confirma">Confirmar Senha</label>
                <input type="password" id="senha_confirma" name="senha_confirma" required>
            </div>

            <div class="form-group" style="margin-bottom: 15px; transform: scale(0.95); -webkit-transform: scale(0.95); transform-origin: 0 0;">
                <div class="g-recaptcha" data-sitekey="6Lc2-AUsAAAAACxYQCiTcP9nTatP72GibeEDU6PF"></div>
            </div>

            <button type="submit" class="btn-submit">Cadastrar</button>
        </form>
        
        <p>Já tem uma conta? <a href="<?php echo BASE_URL; ?>/login">Faça Login</a></p>
    </div>
</div>

<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<style>
    #password-requirements li {
        margin-bottom: 3px;
        display: flex;
        align-items: center;
    }
    #password-requirements li i {
        margin-right: 5px;
        width: 15px;
    }
    #password-requirements li.valid {
        color: green !important;
        font-weight: 600;
    }
    #password-requirements li.invalid {
        color: #d9534f !important;
    }
    #password-requirements li.valid i::before {
        content: '✓';
        color: green;
    }
    #password-requirements li.invalid i::before {
        content: '✗';
        color: #d9534f;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const senhaInput = document.getElementById('senha');
        const reqLength = document.getElementById('req-length');
        const reqUppercase = document.getElementById('req-uppercase');
        const reqNumber = document.getElementById('req-number');

        senhaInput.addEventListener('input', function() {
            const senha = senhaInput.value;

            const isLengthValid = senha.length >= 8;
            updateRequirement(reqLength, isLengthValid);

            const isUppercaseValid = /[A-Z]/.test(senha);
            updateRequirement(reqUppercase, isUppercaseValid);

            const isNumberValid = /\d/.test(senha);
            updateRequirement(reqNumber, isNumberValid);
        });

        function updateRequirement(element, isValid) {
            if (isValid) {
                element.classList.remove('invalid');
                element.classList.add('valid');
            } else {
                element.classList.remove('valid');
                element.classList.add('invalid');
            }
        }
    });
</script>