<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/form_geral.css">
<style>
    .alert-success { 
        padding: 12px; 
        background-color: #d4edda; 
        color: #155724; 
        border-radius: 6px; 
        margin-bottom: 25px; 
        border: 1px solid #c3e6cb;
    }
    .alert-error { 
        padding: 12px; 
        background-color: #f8d7da; 
        color: #721c24; 
        border-radius: 6px; 
        margin-bottom: 25px; 
        border: 1px solid #f5c6cb;
    }
    
    .form-container {
        max-width: 700px;
        background: white; 
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
        margin: 30px auto;
        border: 1px solid #f0f0f0;
    }
    
    h1 {
        text-align: center;
        color: #333;
        margin-bottom: 20px;
        font-size: 1.8rem;
    }

    .form-perfil p {
        margin-bottom: 15px;
        color: #555;
    }

    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #444;
        font-size: 0.95rem;
    }
    
    .form-group input[type="text"],
    .form-group input[type="email"],
    .form-group input[type="password"] {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
        font-size: 1rem;
        transition: border-color 0.3s;
    }
    
    .form-group input:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.2);
    }

    .form-group input[disabled] {
        background-color: #f7f7f7;
        color: #777;
        cursor: not-allowed;
    }

    hr {
        border: 0;
        border-top: 1px solid #eee;
        margin: 25px 0;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        margin-top: 30px;
    }
    
    .btn-submit,
    .btn-cancelar {
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background-color 0.3s, transform 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-submit {
        background-color: #28a745;
        color: white;
        border: none;
    }
    
    .btn-submit:hover {
        background-color: #218838;
        transform: translateY(-1px);
    }

    .btn-cancelar {
        background-color: #6c757d;
        color: white;
        border: none;
    }
    
    .btn-cancelar:hover {
        background-color: #5a6268;
        transform: translateY(-1px);
    }
    
    @media (max-width: 768px) {
        .form-container { 
            max-width: 90%; 
            padding: 25px; 
            margin: 20px auto;
        }
        h1 {
            font-size: 1.6rem;
        }
    }
    
    @media (max-width: 480px) {
        .form-container { 
            padding: 15px; 
            max-width: 95%;
        }
        .form-actions {
            flex-direction: column;
            gap: 10px;
        }
        .btn-submit,
        .btn-cancelar {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<h1><?php echo htmlspecialchars($titulo_pagina); ?></h1>

<div class="form-container">
    
    <?php if (isset($mensagem)): ?>
        <div class="alert-<?php echo $mensagem['tipo']; ?>">
            <?php echo htmlspecialchars($mensagem['texto']); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/admin/usuarios/salvar" method="POST" class="form-perfil">
        
        <?php if ($is_editing): ?>
            <input type="hidden" name="id" value="<?php echo $user_id_edit; ?>">
            
            <div class="form-group">
                <label>Nome do Usuário</label>
                <input type="text" value="<?php echo htmlspecialchars($usuario['nome']); ?>" disabled>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" disabled>
            </div>
            
            <hr>
            <p>Para alterar a senha, digite a **nova senha** e confirme:</p>

        <?php else: ?>
            
            <div class="form-group">
                <label for="nome">Nome Completo</label>
                <input type="text" id="nome" name="nome" required>
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
        <?php endif; ?>

        <div class="form-group">
            <label for="senha"><?php echo $is_editing ? 'Nova Senha' : 'Senha'; ?> (mín. 6 caracteres)</label>
            <input type="password" id="senha" name="senha" required>
        </div>
        <div class="form-group">
            <label for="senha_confirma">Confirmar Senha</label>
            <input type="password" id="senha_confirma" name="senha_confirma" required>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit">
                <i class='bx bxs-save'></i> Salvar
            </button>
            <a href="<?php echo BASE_URL; ?>/admin/usuarios" class="btn-cancelar">
                <i class='bx bx-arrow-back'></i> Cancelar
            </a>
        </div>
    </form>
</div>