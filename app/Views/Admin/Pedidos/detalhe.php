<?php

function format_status_class($status) {
    return str_replace(' ', '', htmlspecialchars($status));
}
function decode_address($address) {
    return html_entity_decode(htmlspecialchars($address));
}

if (!function_exists('getImagePath')) {
    function getImagePath($db_path) {
        $clean_path = str_ireplace('public/', '', $db_path);
        return BASE_URL . '/' . ltrim($clean_path, '/');
    }
}
?>

<style>
    .detalhes-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 20px; }
    .info-card { background-color: #f9f9f9; padding: 20px; border-radius: 6px; border: 1px solid #eee; margin-bottom: 20px; }
    .info-card h3 { border-bottom: 1px solid #ddd; padding-bottom: 10px; margin-bottom: 15px; font-size: 1.3rem; }
    .info-card p { margin: 8px 0; font-size: 0.95rem; line-height: 1.5; }
    .financeiro div { display: flex; justify-content: space-between; padding: 5px 0; border-bottom: 1px dashed #ddd; }
    .financeiro .total { font-size: 1.1rem; font-weight: 700; color: #c0392b; border-bottom: 2px solid #c0392b; margin-top: 10px; }
    .itens-table { width: 100%; border-collapse: collapse; margin-top: 15px; }
    .itens-table th, .itens-table td { padding: 12px; border-bottom: 1px solid #eee; text-align: left; }
    .itens-table th { background-color: #eee; font-weight: 600; }
    .itens-table img { width: 50px; height: 50px; object-fit: cover; border-radius: 4px; vertical-align: middle; margin-right: 10px; }
    .status-tag { padding: 5px 10px; border-radius: 4px; font-weight: 600; font-size: 0.9em; text-transform: uppercase; }
    .status-Pendente { background-color: #fff3cd; color: #856404; }
    .status-Pago { background-color: #d4edda; color: #155724; }
    .status-EmPreparação { background-color: #d1ecf1; color: #0c5460; }
    .status-Enviado { background-color: #cce5ff; color: #004085; }
    .status-Entregue { background-color: #d4edda; color: #155724; }
    .status-Cancelado { background-color: #f8d7da; color: #721c24; }
    .status-action { display: flex; gap: 10px; align-items: center; margin-bottom: 15px; }
    .status-action select { padding: 8px; border-radius: 5px; border: 1px solid #ccc; }
    .status-action button { background-color: #3498db; color: #fff; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }
    .alert-success { padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    
    @media (max-width: 768px) {
        .detalhes-grid { grid-template-columns: 1fr; gap: 20px; }
        .status-action { flex-direction: column; }
        .status-action select { width: 100%; }
        .status-action button { width: 100%; }
        .info-card h3 { font-size: 1.1rem; }
        .itens-table { font-size: 0.85em; }
        .itens-table th, .itens-table td { padding: 8px; }
        .itens-table img { width: 40px; height: 40px; }
    }
    
    @media (max-width: 480px) {
        .info-card { padding: 15px; margin-bottom: 15px; }
        .info-card h3 { font-size: 1em; padding-bottom: 8px; margin-bottom: 10px; }
        .info-card p { font-size: 0.85em; margin: 5px 0; }
        .itens-table th, .itens-table td { padding: 6px; font-size: 0.7em; }
        .itens-table img { width: 30px; height: 30px; margin-right: 5px; }
        .status-tag { padding: 3px 6px; font-size: 0.7em; }
    }
</style>

<div style="display: flex; justify-content: space-between; align-items: center;">
    <h1><i class='bx bxs-detail'></i> Detalhes do Pedido #<?php echo $pedido_id; ?></h1>
    <a href="<?php echo BASE_URL; ?>/admin/pedidos" style="font-weight: 600; text-decoration: none; color: #3498db;"><i class='bx bx-arrow-back'></i> Voltar à Lista</a>
</div>

<?php if (isset($mensagem_status)): ?>
    <div class="alert-success"><?php echo htmlspecialchars($mensagem_status); ?></div>
<?php endif; ?>

<div class="info-card">
    <h3>Status e Data</h3>
    <div class="status-action">
        <p style="margin: 0;">Status Atual: 
            <span class="status-tag status-<?php echo format_status_class($dados_pedido['status']); ?>">
                <?php echo htmlspecialchars($dados_pedido['status']); ?>
            </span>
        </p>
        
        <form method="POST" style="display: flex; gap: 10px;">
            <input type="hidden" name="pedido_id" value="<?php echo $pedido_id; ?>">
            <input type="hidden" name="action" value="atualizar_status">
            <select name="novo_status" required>
                <?php foreach ($status_opcoes as $op): ?>
                    <option value="<?php echo $op; ?>" <?php echo ($dados_pedido['status'] === $op) ? 'selected' : ''; ?>>
                        <?php echo $op; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="submit"><i class='bx bx-refresh'></i> Atualizar Status</button>
        </form>
    </div>
    <p><strong>Data do Pedido:</strong> <?php echo date('d/m/Y H:i', strtotime($dados_pedido['data_pedido'])); ?></p>
    <p><strong>Método de Pagamento:</strong> <?php echo htmlspecialchars($dados_pedido['metodo_pagamento']); ?></p>
</div>

<div class="detalhes-grid">
    <div>
        <div class="info-card">
            <h3>Endereço de Entrega</h3>
            <p><?php echo decode_address($dados_pedido['endereco_completo']); ?></p>
        </div>
        
        <div class="info-card">
            <h3>Informações do Cliente</h3>
            <p><strong>Nome:</strong> <?php echo htmlspecialchars($dados_pedido['nome_usuario']); ?></p>
            <p><strong>E-mail:</strong> <?php echo htmlspecialchars($dados_pedido['email_usuario']); ?></p>
            <p><strong>Telefone:</strong> <?php echo htmlspecialchars($dados_pedido['telefone_usuario']); ?></p>
        </div>
    </div>

    <div>
        <div class="info-card financeiro">
            <h3>Resumo Financeiro</h3>
            <div>
                <span>Subtotal Produtos:</span>
                <span>R$ <?php echo number_format($dados_pedido['total_produtos'], 2, ',', '.'); ?></span>
            </div>
            <div>
                <span>Frete:</span>
                <span>R$ <?php echo number_format($dados_pedido['valor_frete'], 2, ',', '.'); ?></span>
            </div>
            <?php if ($dados_pedido['valor_desconto'] > 0): ?>
                <div style="color: #27ae60; font-weight: 600;">
                    <span>Desconto Aplicado:</span>
                    <span>- R$ <?php echo number_format($dados_pedido['valor_desconto'], 2, ',', '.'); ?></span>
                </div>
            <?php endif; ?>
            <div class="total">
                <span>Total Geral:</span>
                <span>R$ <?php echo number_format($dados_pedido['total_geral'], 2, ',', '.'); ?></span>
            </div>
        </div>
    </div>
</div>

<h2>Itens do Pedido (<?php echo count($itens_pedido); ?>)</h2>
<table class="itens-table">
    <thead>
        <tr>
            <th>Produto</th>
            <th>Preço Unitário</th>
            <th>Quantidade</th>
            <th>Subtotal</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($itens_pedido as $item): ?>
            <?php $subtotal_item = $item['preco_unitario'] * $item['quantidade']; ?>
            <tr>
                <td>
                    <img src="<?php echo BASE_URL . '/_ADM/' . htmlspecialchars($item['imagem_url']); ?>" alt="Miniatura">
                    <?php echo htmlspecialchars($item['nome_produto']); ?> 
                    <?php if (!empty($item['tamanho'])): ?>
                        <small>(Tam: <?php echo htmlspecialchars($item['tamanho']); ?>)</small>
                    <?php endif; ?>
                </td>
                <td>R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($item['quantidade']); ?></td>
                <td>R$ <?php echo number_format($subtotal_item, 2, ',', '.'); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>