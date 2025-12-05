<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/form_auth.css">
<style>
    #password-requirements li {
        margin-bottom: 3px;
        display: flex;
        align-items: center;
        font-size: 0.85em;
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

<div class="auth-container">
    <div class="form-box">
        <h2>Redefinir Senha</h2>
        
        <?php if (!empty($mensagem)): ?>
            <div class="message <?php echo htmlspecialchars($tipo_mensagem ?? 'erro'); ?>">
                <?php echo htmlspecialchars($mensagem); ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>/resetarsenha" method="POST">
            
            <input type="hidden" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token ?? ''); ?>">

            <p style="color: #333; font-size: 0.9em;">Redefinindo senha para: <strong><?php echo htmlspecialchars($email ?? ''); ?></strong></p>

            <div class="input-group">
                <label for="nova_senha">Nova Senha</label>
                <input type="password" id="nova_senha" name="nova_senha" required>
            </div>
            
            <ul id="password-requirements" style="list-style: none; padding: 0; margin-top: -10px; margin-bottom: 15px;">
                <li id="req-length" style="color: #777;"><i></i> Mínimo de 8 caracteres</li>
                <li id="req-uppercase" style="color: #777;"><i></i> Pelo menos 1 letra maiúscula</li>
                <li id="req-number" style="color: #777;"><i></i> Pelo menos 1 número</li>
            </ul>

            <div class="input-group">
                <label for="confirma_senha">Confirmar Nova Senha</label>
                <input type="password" id="confirma_senha" name="confirma_senha" required>
            </div>

            <button type="submit" class="btn-submit">Redefinir Senha</button>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const senhaInput = document.getElementById('nova_senha'); 
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