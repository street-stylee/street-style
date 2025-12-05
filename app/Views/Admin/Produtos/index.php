<?php
?>

<style>
    .admin-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
    .btn-novo { background-color: #27ae60; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; font-weight: bold; white-space: nowrap; }
    .btn-novo:hover { background-color: #219150; }
    
    .admin-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; }
    .admin-table th, .admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    .admin-table th { background-color: #f9f9f9; font-size: 0.9em; text-transform: uppercase; color: #555; }
    .admin-table td { color: #333; }
    .admin-table tr:last-child td { border-bottom: none; }
    .admin-table .acoes a { text-decoration: none; margin-right: 10px; font-weight: bold; }
    .acoes .editar { color: #3498db; }
    .acoes .excluir { color: #e74c3c; }
    
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
    <h1><?php echo htmlspecialchars($titulo_pagina); ?> (<?php echo count($produtos); ?>)</h1>
    <a href="<?php echo BASE_URL; ?>/admin/produtos/novo" class="btn-novo">+ Adicionar Produto</a>
</div>

<table class="admin-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome do Produto</th>
            <th>Categoria</th>
            <th>Preço</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($produtos)): ?>
            <tr>
                <td colspan="5" style="text-align: center; padding: 20px;">Nenhum produto cadastrado ainda.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td>#<?php echo $produto['id']; ?></td>
                    <td><?php echo htmlspecialchars($produto['nome']); ?></td>
                    <td><?php echo htmlspecialchars($produto['categoria']); ?></td>
                    <td>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></td>
                    <td class="acoes">
                        <a href="<?php echo BASE_URL; ?>/admin/produtos/editar/<?php echo $produto['id']; ?>" class="editar">Editar</a>
                        <a href="<?php echo BASE_URL; ?>/admin/produtos/excluir/<?php echo $produto['id']; ?>" class="excluir" onclick="return confirm('Tem certeza que deseja excluir este produto?');">Excluir</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>