<?php

?>

<style>
    .toolbar { display: flex; justify-content: space-between; align-items: center; margin-bottom: 25px; flex-wrap: wrap; gap: 10px; }
    .search-form { display: flex; gap: 10px; flex-wrap: wrap; }
    .search-form input[type="text"] { padding: 8px; border: 1px solid #ccc; border-radius: 4px; width: 300px; box-sizing: border-box; }
    .search-form button { background-color: #3498db; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; white-space: nowrap; }
    .search-form .btn-limpar {
        display: inline-block;
        padding: 8px 10px;
        background-color: #e74c3c;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 0.9em;
        white-space: nowrap;
    }
    
    .admin-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; }
    .admin-table th, .admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    .admin-table th { background-color: #f9f9f9; font-size: 0.9em; text-transform: uppercase; color: #555; }
    .admin-table td { color: #333; }
    .admin-table tr:last-child td { border-bottom: none; }

    .info-col {
        display: flex;
        align-items: center;
        gap: 4px;
        overflow: visible;
    }

    .info-wrapper {
        flex: 1;
        min-width: 0;
    }

    .info-text {
        display: block;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.9em;
    }

    .action-link {
        padding: 5px 10px; text-decoration: none; border-radius: 4px;
        font-size: 0.9rem; display: inline-flex; align-items: center;
        background-color: #27ae60; color: #fff;
        white-space: nowrap;
    }
    .action-link:hover { background-color: #1e8449; }
    .action-link.icon-only {
        display: none;
        padding: 6px 8px;
        font-size: 1rem;
    }
    
    .btn-ver-mais {
        padding: 4px 8px; text-decoration: none; border-radius: 3px;
        font-size: 0.8rem; background-color: #3498db; color: #fff;
        cursor: pointer; border: none; font-weight: 500;
        display: none;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .btn-ver-mais:hover { background-color: #2980b9; }
    
    @media (min-width: 1201px) {
        .info-text { font-size: 0.9em; }
        .action-link { display: inline-flex; }
        .action-link.icon-only { display: none !important; }
    }
    
    @media (max-width: 1200px) {
        .admin-table th, .admin-table td { padding: 12px 8px; font-size: 0.9em; }
        .info-text { font-size: 0.85em; }
        .action-link { padding: 4px 8px; font-size: 0.9rem; }
        .action-link.icon-only { display: none !important; }
        .btn-ver-mais { padding: 3px 6px; font-size: 0.75rem; display: inline-block; }
    }
    
    @media (max-width: 999px) {
        .admin-table th, .admin-table td { padding: 10px 6px; font-size: 0.85em; }
        .info-text { font-size: 0.8em; }
        .action-link { padding: 4px 6px; font-size: 0.85rem; display: inline-flex; }
        .action-link.icon-only { display: none !important; }
        .btn-ver-mais { padding: 3px 5px; font-size: 0.7rem; display: inline-block; }
    }
    
    @media (max-width: 999px) {
        .admin-table th, .admin-table td { padding: 10px 6px; font-size: 0.9em; }
        .info-text { font-size: 0.85em; }
        .action-link { padding: 5px 8px; font-size: 0.9rem; display: inline-flex; }
        .action-link.icon-only { display: none !important; }
        .btn-ver-mais { padding: 4px 6px; font-size: 0.75rem; display: inline-block; }
    }
    
    @media (max-width: 899px) {
        .admin-table th, .admin-table td { padding: 9px 6px; font-size: 0.85em; }
        .admin-table th { font-size: 0.75em; }
        .info-text { font-size: 0.8em; }
        .action-link { padding: 5px 8px; font-size: 0.85rem; }
        .action-link span { display: none; }
        .action-link.icon-only { display: none !important; }
        .btn-ver-mais { padding: 4px 6px; font-size: 0.7rem; display: inline-block; }
    }

    @media (max-width: 767px) {
        .admin-table th, .admin-table td { padding: 8px 5px; font-size: 0.8em; }
        .admin-table th { font-size: 0.7em; }
        .info-text { font-size: 0.75em; }
        .action-link { padding: 4px 6px; font-size: 0.8rem; }
        .action-link span { display: none; }
        .action-link.icon-only { display: none !important; }
        .btn-ver-mais { padding: 3px 5px; font-size: 0.65rem; display: inline-block; }
    }
    
    @media (max-width: 479px) {
        .admin-table th, .admin-table td { padding: 7px 4px; font-size: 0.75em; }
        .admin-table th { font-size: 0.65em; }
        .info-text { font-size: 0.7em; }
        .action-link { padding: 4px 5px; font-size: 0.75rem; }
        .action-link span { display: none; }
        .action-link.icon-only { display: none !important; }
        .btn-ver-mais { padding: 3px 4px; font-size: 0.6rem; display: inline-block; }
    }

    @media (max-width: 389px) {
        .admin-table th, .admin-table td { padding: 6px 3px; font-size: 0.7em; }
        .admin-table th { font-size: 0.6em; }
        .info-text { font-size: 0.65em; }
        .info-col { gap: 3px; }
        .action-link { padding: 3px 4px; font-size: 0.7rem; }
        .action-link span { display: none; }
        .action-link.icon-only { display: none !important; }
        .btn-ver-mais { padding: 3px 4px; font-size: 0.55rem; display: inline-block; }
    }

    @media (max-width: 320px) {
        .admin-table { font-size: 0.65em; }
        .admin-table th, .admin-table td { padding: 5px 3px; }
        .admin-table th { font-size: 0.55em; }
        .info-text { font-size: 0.6em; }
        .info-col { gap: 2px; }
        .action-link { padding: 3px 4px; font-size: 0.65rem; }
        .action-link span { display: none; }
        .action-link.icon-only { display: none !important; }
        .btn-ver-mais { padding: 2px 3px; font-size: 0.5rem; display: inline-block; }
    }
</style>

<h1><i class='bx bxs-user-account'></i> <?php echo $titulo_pagina; ?></h1>

<div class="toolbar">
    <form action="<?php echo BASE_URL; ?>/admin/clientes" method="GET" class="search-form">
        <input type="text" name="busca" placeholder="Buscar por Nome ou Email..." value="<?php echo htmlspecialchars($termo_busca); ?>">
        <button type="submit"><i class='bx bx-search'></i> Buscar</button>
        <?php if ($termo_busca): ?>
            <a href="<?php echo BASE_URL; ?>/admin/clientes" class="btn-limpar">Limpar Busca</a>
        <?php endif; ?>
    </form>
    </div>

<?php if (empty($usuarios)): ?>
    <p>Nenhum cliente encontrado<?php echo !empty($termo_busca) ? " para o termo '<strong>" . htmlspecialchars($termo_busca) . "</strong>'" : "."; ?></p>
<?php else: ?>
    <p>Total de Clientes: <strong><?php echo $total_usuarios; ?></strong></p>
    <table class="admin-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>E-mail</th>
                <th>Telefone</th>
                <th>Cadastrado em</th>
                <th>Total Pedidos</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($usuarios as $usuario): ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td>
                        <div class="info-col">
                            <div class="info-wrapper">
                                <span class="info-text" title="<?php echo htmlspecialchars($usuario['nome']); ?>"><?php echo htmlspecialchars($usuario['nome']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="info-col">
                            <div class="info-wrapper">
                                <span class="info-text" title="<?php echo htmlspecialchars($usuario['email']); ?>"><?php echo htmlspecialchars($usuario['email']); ?></span>
                            </div>
                        </div>
                    </td>
                    <td><?php echo htmlspecialchars($usuario['telefone'] ?? 'N/A'); ?></td>
                    <td><?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?></td>
                    <td><?php echo htmlspecialchars($usuario['total_pedidos']); ?></td>
                    <td style="display: flex; gap: 5px; align-items: center; flex-wrap: wrap;">
                        <a href="<?php echo BASE_URL; ?>/admin/pedidos?user_id=<?php echo $usuario['id']; ?>" class="action-link" title="Ver todos os pedidos do cliente">
                            <i class='bx bxs-package'></i> <span>Pedidos</span>
                        </a>
                        <a href="<?php echo BASE_URL; ?>/admin/pedidos?user_id=<?php echo $usuario['id']; ?>" class="action-link icon-only" title="Ver todos os pedidos do cliente">
                            <i class='bx bxs-package'></i>
                        </a>
                        <button type="button" class="btn-ver-mais" onclick="mostrarDetalhesCliente('<?php echo htmlspecialchars(addslashes($usuario['nome'])); ?>', '<?php echo htmlspecialchars(addslashes($usuario['email'])); ?>', '<?php echo htmlspecialchars(addslashes($usuario['telefone'] ?? 'N/A')); ?>', '<?php echo date('d/m/Y', strtotime($usuario['data_cadastro'])); ?>', '<?php echo htmlspecialchars($usuario['total_pedidos']); ?>');" title="Ver detalhes">
                            <i class='bx bx-plus'></i>
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


<script>
function mostrarDetalhesCliente(nome, email, telefone, dataCadastro, totalPedidos) {
    alert(`DETALHES DO CLIENTE\n\nNome:\n${nome}\n\nEmail:\n${email}\n\nTelefone:\n${telefone}\n\nCadastrado em:\n${dataCadastro}\n\nTotal de Pedidos:\n${totalPedidos}`);
}

function checkClientesButtonVisibility() {
    document.querySelectorAll('.info-text').forEach((el) => {
        const row = el.closest('tr');
        const btnVerMais = row.querySelector('.btn-ver-mais');
        const actionLink = row.querySelector('.action-link:not(.icon-only)');
        const actionLinkIcon = row.querySelector('.action-link.icon-only');
        
        if (!btnVerMais || !actionLink) return;
        
        requestAnimationFrame(() => {
            const textWidth = el.scrollWidth;
            const availableWidth = el.parentElement.offsetWidth;
            const hasOverflow = textWidth > availableWidth;
            
            if (hasOverflow) {
                btnVerMais.style.display = 'inline-block';
            } else {
                btnVerMais.style.display = 'none';
            }
        });
    });
}

setTimeout(checkClientesButtonVisibility, 150);

window.addEventListener('load', () => {
    setTimeout(checkClientesButtonVisibility, 100);
});

let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(checkClientesButtonVisibility, 100);
});

const observer = new MutationObserver(() => {
    setTimeout(checkClientesButtonVisibility, 50);
});
observer.observe(document.body, { childList: true, subtree: true });
</script>