<?php

if (!function_exists('getImagePath')) {
    function getImagePath($db_path)
    {
        $clean_path = str_ireplace('public/', '', $db_path);
        return BASE_URL . '/' . ltrim($clean_path, '/');
    }
}
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/estilo_pg_produto.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/estilo_carrinho.css">

<div class="carrinho-container">
    <h1><i class='bx bx-shopping-bag' style="vertical-align: middle;"></i> Seu Carrinho</h1>

    <?php if (!empty($carrinho_itens)): ?>
        <div class="carrinho-header">
            <a href="<?php echo BASE_URL; ?>/carrinho/gerenciar?acao=limpar" class="btn btn-limpar-tudo">
                <i class="fa-solid fa-trash-can"></i> Limpar Carrinho
            </a>
        </div>

        <ul class="lista-itens">
            <?php foreach ($carrinho_itens as $item): ?>
                <?php
                $preco_total_item = $item['preco_unitario'] * $item['quantidade'];
                $item_id = $item['item_carrinho_id'];
                $link_base = BASE_URL . "/carrinho/gerenciar?item_id=" . urlencode($item_id);
                ?>
                <li class="item-carrinho">
                    <div class="item-imagem">
                        <img src="<?php echo BASE_URL; ?>/_ADM/<?php echo htmlspecialchars($item['imagem_url']); ?>"
                            alt="<?php echo htmlspecialchars($item['nome_produto']); ?>">
                    </div>
                    <div class="item-info">
                        <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo htmlspecialchars($item['produto_id']); ?>"
                            class="item-nome">
                            <?php echo htmlspecialchars($item['nome_produto']); ?>
                        </a>
                        <span class="item-tamanho">Tamanho:
                            <?php echo htmlspecialchars($item['tamanho']); ?></span>
                        <span class="item-preco-unitario">R$
                            <?php echo number_format($item['preco_unitario'], 2, ',', '.'); ?>/un.</span>

                        <div class="item-acoes-mobile">
                            <span class="quantidade-mobile">Qtde:
                                <?php echo htmlspecialchars($item['quantidade']); ?></span>
                            <a href="<?php echo $link_base; ?>&acao=diminuir" class="btn-remover-mobile">Diminuir</a>
                            <a href="<?php echo $link_base; ?>&acao=aumentar" class="btn-remover-mobile">Aumentar</a>
                        </div>
                    </div>

                    <div class="item-quantidade">
                        <a href="<?php echo $link_base; ?>&acao=diminuir" class="btn-qty">-</a>
                        <span class="quantidade-display"><?php echo htmlspecialchars($item['quantidade']); ?></span>
                        <a href="<?php echo $link_base; ?>&acao=aumentar" class="btn-qty">+</a>
                    </div>

                    <div class="item-subtotal">
                        <span class="subtotal-label">Subtotal</span>
                        <span class="subtotal-valor">R$
                            <?php echo number_format($preco_total_item, 2, ',', '.'); ?></span>
                    </div>

                    <a href="<?php echo $link_base; ?>&acao=excluir" class="btn-remover">
                        <i class="fa-solid fa-xmark"></i>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <div class="resumo-compra">
            <div class="resumo-row">
                <span>Subtotal de Produtos (<?php echo count($carrinho_itens); ?> itens)</span>
                <span class="valor-produtos">R$
                    <?php echo number_format($total_geral, 2, ',', '.'); ?></span>
            </div>
            <div class="resumo-row total-final">
                <h3>Total a Pagar</h3>
                <h3 class="valor-final">R$ <?php echo number_format($total_geral, 2, ',', '.'); ?>
                </h3>
            </div>

            <?php if ($usuario_logado): ?>
                <a href="<?php echo BASE_URL; ?>/checkout" class="btn-finalizar-compra">
                    <i class="fa-solid fa-truck-fast"></i> Finalizar Pedido
                </a>
            <?php else: ?>
                <a href="<?php echo BASE_URL; ?>/login?redirect=checkout" class="btn-finalizar-compra btn-alert">
                    <i class="fa-solid fa-user-lock"></i> Faça Login para Finalizar
                </a>
                <p style="text-align: center; margin-top: 10px;">
                    Não tem conta?
                    <a href="<?php echo BASE_URL; ?>/cadastro?redirect=checkout"
                        style="font-weight: bold; color: #333;">Cadastre-se</a>
                </p>
            <?php endif; ?>

            <style>
                .btn-alert {
                    background-color: #f39c12 !important;
                    box-shadow: 0 4px 8px rgba(243, 156, 18, 0.4);
                }

                .btn-alert:hover {
                    background-color: #e67e22 !important;
                }
            </style>
        </div>

    <?php else: ?>
        <div class="carrinho-vazio">
            <i class='bx bx-shopping-bag' style="font-size: 80px; color: #ccc;"></i>
            <h2>Seu carrinho está vazio!</h2>
            <p>Parece que você ainda não adicionou nenhum item.</p>
        </div>
    <?php endif; ?>

    <a href="<?php echo BASE_URL; ?>/produtos" class="btn-continuar-comprando">
        <i class="fa-solid fa-arrow-left"></i> Continuar Comprando
    </a>
</div>