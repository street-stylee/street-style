<?php
?>

<style>
    .admin-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
    .btn-novo { background-color: #27ae60; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; white-space: nowrap; }
    .btn-novo:hover { background-color: #219150; }
    .admin-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; }
    .admin-table th, .admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    .admin-table th { background-color: #f9f9f9; font-size: 0.9em; text-transform: uppercase; color: #555; }
    .admin-table .acoes a { text-decoration: none; margin-right: 10px; font-weight: bold; }
    .acoes .editar { color: #3498db; }
    .acoes .excluir { color: #e74c3c; }
    .alert-success { padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    .alert-error { padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px; }
    
    @media (max-width: 768px) {
        .admin-actions { flex-direction: column; align-items: stretch; }
        .admin-table { font-size: 0.85em; }
        .admin-table th, .admin-table td { padding: 10px 8px; }
        .admin-table th { font-size: 0.75em; }
    }
    
    @media (max-width: 480px) {
        .btn-novo { width: 100%; text-align: center; }
        .admin-table th, .admin-table td { padding: 8px 5px; font-size: 0.75em; }
        .admin-table th { font-size: 0.65em; }
        .admin-table .acoes a { margin-right: 5px; font-size: 0.8em; }
    }
</style>

<div class="admin-actions">
    <h1><?php echo htmlspecialchars($titulo_pagina); ?></h1>
    <a href="<?php echo BASE_URL; ?>/admin/usuarios/novo" class="btn-novo">+ Novo Admin</a>
</div>

<?php if (isset($mensagem)): ?>
    <div class="alert-<?php echo $mensagem['tipo']; ?>">
        <?php echo htmlspecialchars($mensagem['texto']); ?>
    </div>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Email</th>
            <th>Data Cadastro</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $usuario): ?>
            <tr>
                <td>#<?php echo $usuario['id']; ?></td>
                <td><?php echo htmlspecialchars($usuario['nome']); ?></td>
                <td><?php echo htmlspecialchars($usuario['email']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?></td>
                <td class="acoes">
                    <a href="<?php echo BASE_URL; ?>/admin/usuarios/editar/<?php echo $usuario['id']; ?>" class="editar">Alterar Senha</a>
                    
                    <?php if ($usuario['id'] != 1 && $usuario['id'] != $_SESSION['admin_id']): ?>
                        <a href="<?php echo BASE_URL; ?>/admin/usuarios/excluir/<?php echo $usuario['id']; ?>" class="excluir" onclick="return confirm('Tem certeza? Esta ação não pode ser desfeita.');">Excluir</a>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>