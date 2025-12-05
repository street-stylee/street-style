<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/form_auth.css">
    
    <style>
        .password-requirements p {
            font-size: 0.8em;
            color: #555;
            margin: 2px 0;
        }
        .password-requirements .invalid {
            color: #d9534f;
            font-weight: bold;
        }
        .password-requirements .valid {
            color: #5cb85c;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="form-box">
        <h2>Redefinir Senha</h2>

        <form id="resetForm" action="<?= BASE_URL ?>/resetarsenha/salvar" method="POST">

            <input type="hidden" name="email" value="<?= htmlspecialchars($_GET['email']); ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($_GET['token']); ?>">

            <div class="input-group">
                <label for="nova_senha">Nova senha:</label>
                <input type="password" id="nova_senha" name="senha" required minlength="8" onkeyup="checkPasswordStrength(this.value)">
            </div>

            <div class="password-requirements">
                <p id="reqLength">Mínimo de 8 caracteres</p>
                <p id="reqUppercase">Pelo menos uma letra maiúscula</p>
                <p id="reqNumber">Pelo menos um número</p>
            </div><br>

            <button type="submit" id="submitButton" class="btn-submit" disabled>Salvar nova senha</button>
        </form>
    </div>
</div>

<script>
    const senhaInput = document.getElementById('nova_senha');
    const submitButton = document.getElementById('submitButton');

    const reqLength = document.getElementById('reqLength');
    const reqUppercase = document.getElementById('reqUppercase');
    const reqNumber = document.getElementById('reqNumber');

    function checkPasswordStrength(senha) {
        const isLengthValid = senha.length >= 8;
        const isUppercaseValid = /[A-Z]/.test(senha);
        const isNumberValid = /[0-9]/.test(senha);

        updateRequirement(reqLength, isLengthValid);
        updateRequirement(reqUppercase, isUppercaseValid);
        updateRequirement(reqNumber, isNumberValid);

        submitButton.disabled = !(isLengthValid && isUppercaseValid && isNumberValid);
    }

    function updateRequirement(element, isValid) {
        element.className = isValid ? 'valid' : 'invalid';
    }

    document.getElementById('resetForm').onsubmit = function() {
        if (submitButton.disabled) {
            alert("A senha não atende a todos os requisitos de segurança.");
            return false;
        }
        return true;
    };

    checkPasswordStrength(senhaInput.value);
</script>

</body>
</html>