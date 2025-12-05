<?php

if (!function_exists('gerarEstrelas')) {
    function gerarEstrelas($avaliacao)
    {
        $html = '<div class="product-rating">';
        $avaliacao_arredondada = round($avaliacao * 2) / 2;
        for ($i = 1; $i <= 5; $i++) {
            if ($avaliacao_arredondada >= $i) {
                $html .= '<i class="bx bxs-star"></i>';
            } elseif ($avaliacao_arredondada == $i - 0.5) {
                $html .= '<i class="bx bxs-star-half"></i>';
            } else {
                $html .= '<i class="bx bx-star"></i>';
            }
        }
        $html .= '</div>';
        return $html;
    }
}
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/produto.css">

<body id="cmc">

    <section class="menu-categorias">
        <div id="fijogadores">
            <?php
            $titulos = $categorias_menu['titulos'];
            $ancoras = $categorias_menu['ancoras'];
            $icones = $categorias_menu['icones'];
            ?>
            <?php for ($i = 0; $i < count($titulos); $i++): ?>
                <a class="jog" href="#<?php echo htmlspecialchars($ancoras[$i]); ?>">
                    <img class="ijo"
                        src="<?php echo BASE_URL; ?>/_ADM/img/icones/<?php echo htmlspecialchars($icones[$i]); ?>"
                        alt="<?php echo htmlspecialchars($titulos[$i]); ?>">
                    <p><?php echo htmlspecialchars($titulos[$i]); ?></p>
                </a>
            <?php endfor; ?>
        </div>
    </section>

    <?php $count = count($produtos_por_categoria); ?>
    <?php foreach ($produtos_por_categoria as $i => $grupo): ?>

        <br id="<?php echo htmlspecialchars($grupo['ancora']); ?>"><br>

        <section class="em-alta">
            <div class="center-text">
                <h2><?php echo htmlspecialchars($grupo['titulo']); ?></h2>
            </div>
            <div class="produtos">

                <?php if (!empty($grupo['produtos'])): ?>
                    <?php foreach ($grupo['produtos'] as $produto): ?>
                        <div class="linha">
                            <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo htmlspecialchars($produto['id']); ?>">

                                <img src="<?php echo display_image_url($produto['imagem_url']); ?>"
                                    alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                            </a>

                            <div class="product-info">
                                <?php echo gerarEstrelas($produto['avaliacao_media'] ?? 0); ?>
                                <a href="<?php echo BASE_URL; ?>/favorito/toggle/<?php echo $produto['id']; ?>"
                                    class="coracao-icon js-toggle-favorito" data-id="<?php echo $produto['id']; ?>">

                                    <?php if (in_array($produto['id'], $favoritos_ids)): ?>
                                        <i class='bx bxs-heart favorito-icone' style="color: #ee1c47;"></i>
                                    <?php else: ?>
                                        <i class='bx bx-heart favorito-icone'></i>
                                    <?php endif; ?>
                                </a>
                            </div>

                            <div class="preco">
                                <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                                <p>R$<?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="text-align: center; color: #52616B; width: 100%;">Nenhum produto cadastrado nesta categoria.</p>
                <?php endif; ?>
            </div>
        </section>

        <?php if ($i < $count - 1): ?>
            <div class="categoria-divisor"></div>
        <?php endif; ?>

    <?php endforeach; ?>

    <br>

    <div class="seta-baixo">
        <a href="#cmc" class="baixo"><i class='bx bx-up-arrow-alt'></i></a>
    </div>

</body>

</html>