<?php

function format_status_class($status) {
    return str_replace(' ', '', htmlspecialchars($status));
}
?>

<style>
    .filter-bar { margin-bottom: 20px; display: flex; align-items: center; gap: 15px; flex-wrap: wrap; }
    .filter-bar select, .filter-bar input { padding: 8px; border-radius: 5px; border: 1px solid #ccc; box-sizing: border-box; }
    .data-table { width: 100%; border-collapse: collapse; margin-top: 15px; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .data-table th, .data-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    .data-table th { background-color: #f9f9f9; font-weight: 600; text-transform: uppercase; font-size: 0.9em; }
    .status-tag { padding: 5px 10px; border-radius: 4px; font-weight: 600; font-size: 0.9em; text-transform: uppercase; }
    .status-Pendente { background-color: #fff3cd; color: #856404; }
    .status-Pago { background-color: #d4edda; color: #155724; }
    .status-EmPreparação { background-color: #d1ecf1; color: #0c5460; }
    .status-Enviado { background-color: #cce5ff; color: #004085; }
    .status-Entregue { background-color: #d4edda; color: #155724; }
    .status-Cancelado { background-color: #f8d7da; color: #721c24; }
    .status-form { display: flex; gap: 5px; align-items: center; flex-wrap: wrap; }
    .status-form select { padding: 6px; border-radius: 4px; }
    .status-form button { background-color: #27ae60; color: #fff; border: none; padding: 6px 10px; border-radius: 4px; cursor: pointer; white-space: nowrap; }
    .status-form button:hover { background-color: #1e8449; }
    
    .action-link { 
        background-color: #3498db; 
        color: #fff; 
        padding: 6px 10px; 
        border-radius: 4px; 
        text-decoration: none; 
        font-size: 0.9em;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        white-space: nowrap;
    }
    .action-link:hover { background-color: #2980b9; }
    .action-link.icon-only {
        display: none;
        padding: 6px 8px;
        font-size: 1rem;
    }

    .alert-success { padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    
    @media (min-width: 1101px) {
        .data-table th, .data-table td { padding: 12px; }
        .data-table { font-size: 0.9em; }
        .action-link { display: inline-flex; padding: 6px 10px; font-size: 0.9em; }
        .action-link.icon-only { display: none !important; }
    }

    @media (max-width: 1100px) {
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar form { display: flex; flex-direction: column; gap: 10px; }
        .filter-bar select, .filter-bar input { width: 100%; }
        .data-table { font-size: 0.9em; }
        .data-table th, .data-table td { padding: 10px; }
        .action-link { display: inline-flex; padding: 6px 10px; font-size: 0.9em; }
        .action-link span { display: none; }
        .action-link.icon-only { display: none !important; }
        .status-form { flex-wrap: wrap; gap: 4px; }
        .status-form select { padding: 5px; font-size: 0.85em; }
        .status-form button { padding: 5px 8px; font-size: 0.85em; }
    }

    @media (max-width: 999px) {
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar form { display: flex; flex-direction: column; gap: 10px; }
        .filter-bar select, .filter-bar input { width: 100%; }
        .data-table { font-size: 0.85em; }
        .data-table th, .data-table td { padding: 9px; }
        .action-link { display: inline-flex; padding: 6px 10px; font-size: 0.9em; }
        .action-link span { display: none; }
        .action-link.icon-only { display: none !important; }
        .status-form { flex-wrap: wrap; gap: 4px; }
        .status-form select { padding: 5px; font-size: 0.8em; }
        .status-form button { padding: 5px 8px; font-size: 0.8em; }
    }

    @media (max-width: 849px) {
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar form { display: flex; flex-direction: column; gap: 10px; }
        .filter-bar select, .filter-bar input { width: 100%; }
        .data-table { font-size: 0.8em; }
        .data-table th, .data-table td { padding: 8px; }
        .action-link { display: inline-flex; padding: 6px 10px; font-size: 0.9em; }
        .action-link span { display: none; }
        .action-link.icon-only { display: none !important; }
        .status-form { flex-wrap: wrap; gap: 3px; }
        .status-form select { padding: 4px; font-size: 0.75em; }
        .status-form button { padding: 4px 6px; font-size: 0.75em; }
    }

    @media (max-width: 767px) {
        .filter-bar { flex-direction: column; align-items: stretch; }
        .filter-bar form { display: flex; flex-direction: column; gap: 10px; }
        .filter-bar select, .filter-bar input { width: 100%; }
        .data-table { font-size: 0.75em; }
        .data-table th, .data-table td { padding: 7px; }
        .action-link { display: inline-flex; padding: 6px 10px; font-size: 0.9em; }
        .action-link span { display: none; }
        .action-link.icon-only { display: none !important; }
        .status-form { flex-direction: column; gap: 4px; }
        .status-form select { width: 100%; padding: 5px; font-size: 0.75em; }
        .status-form button { width: 100%; padding: 5px; font-size: 0.75em; }
    }

    @media (max-width: 599px) {
        .data-table { font-size: 0.7em; }
        .data-table th, .data-table td { padding: 6px; }
        .data-table th { font-size: 0.65em; }
        .status-tag { padding: 3px 6px; font-size: 0.75em; }
        .action-link { display: inline-flex; padding: 6px 10px; font-size: 0.9em; }
        .action-link.icon-only { display: none !important; }
        .status-form { flex-direction: column; gap: 3px; }
        .status-form select { width: 100%; padding: 4px; font-size: 0.7em; }
        .status-form button { width: 100%; padding: 4px; font-size: 0.7em; }
    }

    @media (max-width: 479px) {
        .data-table { font-size: 0.65em; }
        .data-table th, .data-table td { padding: 5px; }
        .data-table th { font-size: 0.6em; }
        .status-tag { padding: 2px 4px; font-size: 0.7em; }
        .action-link { display: inline-flex; padding: 6px 10px; font-size: 0.9em; }
        .action-link.icon-only { display: none !important; }
        .status-form { flex-direction: column; gap: 3px; }
        .status-form select { width: 100%; padding: 3px; font-size: 0.65em; }
        .status-form button { width: 100%; padding: 3px; font-size: 0.65em; }
    }

    @media (max-width: 389px) {
        .data-table { font-size: 0.6em; }
        .data-table th, .data-table td { padding: 4px; }
        .data-table th { font-size: 0.55em; }
        .status-tag { padding: 2px 3px; font-size: 0.65em; }
        .action-link { display: inline-flex; padding: 6px 10px; font-size: 0.9em; }
        .action-link.icon-only { display: none !important; }
        .status-form { flex-direction: column; gap: 2px; }
        .status-form select { width: 100%; padding: 3px; font-size: 0.6em; }
        .status-form button { width: 100%; padding: 3px; font-size: 0.6em; }
    }
</style>

<h1><i class='bx bxs-package'></i> Gerenciar Pedidos</h1>

<?php if (isset($mensagem_status)): ?>
    <div class="alert-success"><?php echo htmlspecialchars($mensagem_status); ?></div>
<?php endif; ?>

<div class="filter-bar">
    <form action="<?php echo BASE_URL; ?>/admin/pedidos" method="GET" style="display:flex; gap: 15px;">
        <label for="status-filter">Filtrar por Status:</label>
        <select id="status-filter" name="status">
            <option value="Todos" <?php echo ($status_filtro === 'Todos') ? 'selected' : ''; ?>>Todos</option>
            <?php foreach ($status_opcoes as $op): ?>
                <option value="<?php echo $op; ?>" <?php echo ($status_filtro === $op) ? 'selected' : ''; ?>>
                    <?php echo $op; ?>
                </option>
            <?php endforeach; ?>
        </select>
        
        <?php if ($user_id_filtro): ?>
             <input type="hidden" name="user_id" value="<?php echo $user_id_filtro; ?>">
             <span style="font-weight: 700; color: #c0392b;">(Filtrando por Cliente #<?php echo $user_id_filtro; ?>)</span>
             <a href="<?php echo BASE_URL; ?>/admin/pedidos" style="color: #c0392b;">Limpar Filtro</a>
        <?php endif; ?>
        
        <button type="submit" style="background-color: #3498db; color: white; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer;">Filtrar</button>
    </form>
</div>

<?php if (empty($pedidos)): ?>
    <p>Nenhum pedido encontrado com os filtros aplicados.</p>
<?php else: ?>
    <table class="data-table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Data/Hora</th>
                <th>Cliente</th>
                <th>Total</th>
                <th>Status Atual</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pedidos as $pedido): ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($pedido['id']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($pedido['nome_usuario']); ?></strong><br>
                        <small><?php echo htmlspecialchars($pedido['email_usuario']); ?></small>
                    </td>
                    <td>R$ <?php echo number_format($pedido['total_geral'], 2, ',', '.'); ?></td>
                    <td>
                        <span class="status-tag status-<?php echo format_status_class($pedido['status']); ?>">
                            <?php echo htmlspecialchars($pedido['status']); ?>
                        </span>
                    </td>
                    <td>
                        <div class="action-col">
                            <a href="<?php echo BASE_URL; ?>/admin/pedidos/detalhe/<?php echo $pedido['id']; ?>" class="action-link" title="Ver Detalhes"><i class='bx bx-search-alt'></i> <span>Detalhes</span></a>
                            
                            <form method="POST" action="<?php echo BASE_URL; ?>/admin/pedidos/atualizar_status" class="status-form">
                                <input type="hidden" name="pedido_id" value="<?php echo $pedido['id']; ?>">
                                <input type="hidden" name="redirect_url" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                <select name="novo_status" required>
                                    <?php foreach ($status_opcoes as $op): ?>
                                        <option value="<?php echo $op; ?>" <?php echo ($pedido['status'] === $op) ? 'selected' : ''; ?>>
                                            <?php echo $op; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <button type="submit">Mudar</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>