<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/estilo_checkout.css">
<style>
    :root {
        --cor-sucesso: #27ae60;
        --cor-sucesso-claro: #f6fff9;
        --cor-principal: #333;
        --cor-secundaria: #555;
        --cor-borda: #ddd;
        --cor-sombra-leve: rgba(0, 0, 0, 0.08);
    }

    .sucesso-container {
        max-width: 700px;
        margin: 150px auto 50px;
        padding: 30px 40px;
        text-align: center;
        border: 1px solid var(--cor-sucesso);
        border-radius: 12px;
        background-color: #fff;
        box-shadow: 0 10px 30px var(--cor-sombra-leve);
    }

    .sucesso-container .bx-check-circle,
    .sucesso-container .bxs-check-circle {
        font-size: 5rem;
        color: var(--cor-sucesso);
        margin-bottom: 15px;
        display: block;
        animation: bounceIn 0.8s ease-out;
    }

    .sucesso-container h1 {
        color: var(--cor-sucesso);
        font-size: 2.2rem;
        font-weight: 700;
        margin-bottom: 15px;
    }

    .sucesso-container p {
        font-size: 1rem;
        color: var(--cor-secundaria);
        line-height: 1.6;
        margin-bottom: 10px;
    }

    .sucesso-container .btn-concluir-compra {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background-color: var(--cor-sucesso);
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 12px 25px;
        font-size: 1rem;
        font-weight: 600;
        text-transform: uppercase;
        text-decoration: none;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.1s;
        margin: 25px 15px 30px 0;
    }

    .sucesso-container .btn-concluir-compra:hover {
        background-color: #1e8449;
        transform: translateY(-1px);
    }

    .sucesso-container .btn-concluir-compra i {
        margin-right: 8px;
        font-size: 1.1rem;
    }

    .sucesso-container .btn-cancelar {
        background: #f1f1f1;
        color: var(--cor-principal);
        border: 1px solid var(--cor-borda);
        padding: 12px 25px;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        transition: background-color 0.3s;
    }

    .sucesso-container .btn-cancelar:hover {
        background: #e9e9e9;
    }

    .sucesso-container .btn-cancelar i {
        margin-right: 8px;
        font-size: 1.1rem;
    }

    .resumo-pedido {
        text-align: left;
        border-top: 1px solid var(--cor-borda);
        padding-top: 25px;
        margin-top: 25px;
        background-color: var(--cor-sucesso-claro);
        padding: 20px;
        border-radius: 8px;
    }

    .resumo-pedido h3 {
        margin-bottom: 15px;
        color: var(--cor-principal);
        font-size: 1.2rem;
        border-left: 4px solid var(--cor-sucesso);
        padding-left: 10px;
    }

    .resumo-pedido p {
        margin: 8px 0;
        font-size: 0.95rem;
        color: var(--cor-secundaria);
        padding-left: 15px;
    }

    .resumo-pedido strong {
        color: var(--cor-principal);
        font-weight: 600;
    }

    @keyframes bounceIn {
        0% {
            opacity: 0;
            transform: scale(0.3);
        }

        50% {
            opacity: 1;
            transform: scale(1.1);
        }

        70% {
            transform: scale(0.9);
        }

        100% {
            transform: scale(1);
        }
    }

    .itens-do-pedido {
        margin-top: 20px;
        padding: 15px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
    }

    .item-row {
        display: flex;
        align-items: flex-start;
        padding: 15px 0;
        border-bottom: 1px dashed #eee;
    }

    .item-row:last-child {
        border-bottom: none;
    }

    .item-row img {
        width: 60px;
        height: 60px;
        object-fit: cover;
        border-radius: 4px;
        margin-right: 15px;
        border: 1px solid #f0f0f0;
    }

    .item-info-text {
        flex-grow: 1;
        text-align: left;
    }

    .item-info-text h4 {
        margin: 0 0 5px 0;
        font-size: 1rem;
        color: var(--cor-principal);
    }

    .item-info-text p {
        margin: 0;
        font-size: 0.85rem;
        color: var(--cor-secundaria);
    }

    .item-price {
        text-align: right;
        font-size: 1rem;
        font-weight: 600;
        color: var(--cor-principal);
        min-width: 90px;
    }
</style>

<div class="sucesso-container">
    <i class='bx bxs-check-circle'></i>
    <h1>Pedido Concluído!</h1>
    <p>Obrigado pela sua compra. O número do seu pedido é **#<?php echo $pedido_id; ?>**.</p>
    <p>Você receberá um e-mail de confirmação em breve e pode acompanhar o status na seção "Meus Pedidos".</p>

    <a href="<?php echo BASE_URL; ?>/pedidos" class="btn-concluir-compra" style="margin-right: 15px;">
        <i class="bx bxs-package"></i> Ver Meus Pedidos
    </a>
    <a href="<?php echo BASE_URL; ?>/" class="btn-cancelar">
        <i class="bx bx-home"></i> Continuar Comprando
    </a>

    <div class="resumo-pedido">
        <h3>Detalhes do Pedido #<?php echo $pedido_id; ?></h3>

        <div class="itens-do-pedido">
            <?php foreach ($itens_pedido as $item): ?>
                <?php
                $produto_id = $item['produto_id'] ?? null;

                if ($produto_id === null) {
                    $produto_id = $item['id_produto'] ?? null;
                }

                if ($produto_id === null) {
                    $produto_id = $item['id'] ?? '0';
                }

                $produto_url = (
                    $produto_id &&
                    $produto_id !== '0' &&
                    $produto_id !== 0
                )
                    ? BASE_URL . '/produto/detalhe/' . htmlspecialchars($produto_id)
                    : '#';
                ?>

                <div class="item-row">
                    <a href="<?php echo $produto_url; ?>" class="produto-link">
                        <img src="<?php echo BASE_URL ?>/_ADM/<?php echo htmlspecialchars($item['imagem_url']); ?>"
                            alt="<?php echo htmlspecialchars($item['nome_produto']); ?>">

                        <div class="item-info-text">
                            <h4><?php echo htmlspecialchars($item['nome_produto']); ?></h4>
                    </a>
                    <p>Quantidade: <?php echo $item['quantidade']; ?></p>
                    <p>Preço Unitário: R$ <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?></p>
                </div>

                <div class="item-price">
                    R$ <?php echo number_format($item['quantidade'] * $item['preco_unitario'], 2, ',', '.'); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <p style="margin-top: 20px;"><strong>Total Pago:</strong> R$
        <?php echo number_format($dados_pedido['total_geral'], 2, ',', '.'); ?>
    </p>
    <p><strong>Endereço de Entrega:</strong>
        <?php echo html_entity_decode(htmlspecialchars($dados_pedido['endereco_completo'])); ?></p>

</div>
</div>
