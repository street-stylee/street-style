<?php

?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/perfil.css">
<style>

    :root {
        --cor-principal: #333;
        --cor-secundaria: #555;
        --cor-borda-clara: #eee;
        --cor-sucesso: #27ae60;
        --cor-atencao: #ffc107;
        --cor-fundo-card: #f9f9f9;
    }

    .pedidos-container {
        max-width: 850px;
        margin: 100px auto 50px;
        padding: 30px 20px;
    }

    .pedidos-container h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--cor-principal);
        margin-bottom: 30px;
        border-bottom: 2px solid var(--cor-borda-clara);
        padding-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .pedido-card {
        background-color: var(--cor-fundo-card);
        border: 1px solid var(--cor-borda-clara);
        border-radius: 12px;
        margin-bottom: 25px;
        padding: 25px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .pedido-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }

    .pedido-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        margin-bottom: 15px;
        border-bottom: 1px solid #e0e0e0;
    }

    .pedido-header h3 {
        font-size: 1.6rem;
        color: var(--cor-principal);
        font-weight: 700;
        margin: 0;
    }

    .pedido-details p {
        margin: 8px 0;
        font-size: 1rem;
        color: var(--cor-secundaria);
    }

    .pedido-details strong {
        color: var(--cor-principal);
        font-weight: 600;
        display: inline-block;
        min-width: 50px;
    }

    .pedido-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
    }

    .status-Pendente {
        background-color: #ffeeb3;
        color: #b08d00;
        border: 1px solid #ffcc00;
    }

    .status-Concluido,
    .status-Enviado {
        background-color: #d8f5e0;
        color: #1e8449;
        border: 1px solid var(--cor-sucesso);
    }

    .status-Cancelado {
        background-color: #fcebeb;
        color: #cc3333;
        border: 1px solid #cc3333;
    }

    .pedido-details a.btn-primary {
        background-color: var(--cor-principal);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 8px 15px;
        font-size: 0.9rem;
        font-weight: 600;
        text-decoration: none;
        transition: background-color 0.3s;
        margin-top: 15px;
        display: inline-block;
    }

    .pedido-details a.btn-primary:hover {
        background-color: #555;
    }

    .pedidos-container .pedido-card[style="text-align: center;"] {
        padding: 40px;
        border: 2px dashed var(--cor-borda-clara);
    }

    .order-items-summary {
        margin-top: 15px;
        padding-top: 15px;
        border-top: 1px dashed #ddd;
    }

    .order-items-summary h4 {
        font-size: 1rem;
        color: var(--cor-principal);
        margin-bottom: 8px;
        font-weight: 600;
    }

    .item-line {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 5px;
    }

    .item-thumb {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 4px;
        border: 1px solid var(--cor-borda-clara);
    }

    .item-line span {
        font-size: 0.9rem;
        color: var(--cor-secundaria);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>


<div class="pedidos-container">
    <h1><i class='bx bxs-package'></i> Meus Pedidos</h1>

    <?php if (empty($pedidos)): ?>
        <div class="pedido-card" style="text-align: center;">
            <p>Você ainda não fez nenhum pedido.</p>
            <a href="<?php echo BASE_URL; ?>/produtos" class="btn-primary">Começar a comprar</a>
        </div>
    <?php else: ?>
        <?php foreach ($pedidos as $pedido): ?>
            <div class="pedido-card">
                <div class="pedido-header">
                    <h3>Pedido #<?php echo htmlspecialchars($pedido['id']); ?></h3>
                    <span class="pedido-status status-<?php echo htmlspecialchars($pedido['status']); ?>">
                        <?php echo htmlspecialchars($pedido['status']); ?>
                    </span>
                </div>
                <div class="pedido-details">
                    <p><strong>Data:</strong> <?php echo date('d/m/Y H:i', strtotime($pedido['data_pedido'])); ?></p>
                    <p><strong>Total:</strong> R$ <?php echo number_format($pedido['total_geral'], 2, ',', '.'); ?></p>

                    <?php if (!empty($pedido['itens'])): ?>
                        <div class="order-items-summary">
                            <h4>Itens Comprados (<?= count($pedido['itens']) ?>):</h4>
                            <?php $i = 0;
                            foreach ($pedido['itens'] as $item): ?>
                                <?php if ($i < 2): ?>
                                    <div class="item-line">
                                        <img src="<?php echo BASE_URL; ?>/_ADM/<?php echo htmlspecialchars($item['imagem_url']); ?>"
                                            alt="<?php echo htmlspecialchars($item['nome_produto']); ?>" class="item-thumb">
                                        <span><?php echo htmlspecialchars($item['nome_produto']); ?>
                                            (x<?php echo $item['quantidade']; ?>)</span>
                                    </div>
                                <?php endif;
                                $i++; ?>
                            <?php endforeach; ?>
                            <?php if (count($pedido['itens']) > 2): ?>
                                <p style="font-size: 0.8em; margin-top: 5px;">+ mais <?= count($pedido['itens']) - 2 ?> produtos</p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>/pedidos/sucesso/<?php echo $pedido['id']; ?>" class="btn-primary"
                        style="margin-top: 10px; display: inline-block;">Ver Detalhes</a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>