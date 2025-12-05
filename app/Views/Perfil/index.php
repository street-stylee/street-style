<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/perfil.css">

<section class="perfil-section">
    <div class="perfil-card">

        <div class="perfil-header">
            <i class='bx bxs-user-circle'></i>
            <h2>Olá, <?php echo htmlspecialchars(explode(' ', $dados_usuario['nome'])[0]); ?></h2>
        </div>

        <div class="perfil-dados">
            <div class="dado-item">
                <label><i class='bx bxs-user'></i> Nome Completo</label>
                <p><?php echo htmlspecialchars($dados_usuario['nome']); ?></p>
            </div>

            <div class="dado-item">
                <label><i class='bx bxs-envelope'></i> E-mail</label>
                <p><?php echo htmlspecialchars($dados_usuario['email']); ?></p>
            </div>

            <div class="dado-item">
                <label><i class='bx bxs-phone'></i> Telefone</label>
                <p><?php echo htmlspecialchars($dados_usuario['telefone'] ?? 'Não cadastrado'); ?></p>
            </div>

            <div class="dado-item">
                <label><i class='bx bxs-home'></i> Endereço Principal</label>
                <p><?php echo htmlspecialchars($dados_usuario['endereco'] ?? 'Não cadastrado'); ?></p>
            </div>
        </div>

        <div class="perfil-acoes">
            <a href="<?php echo BASE_URL; ?>/editar_perfil" class="btn-perfil editar-btn">
                <i class='bx bxs-edit-alt'></i> Editar Meus Dados
            </a>
            <a href="<?php echo BASE_URL; ?>/pedidos" class="btn-perfil pedidos-btn">
                <i class='bx bxs-package'></i> Meus Pedidos
            </a>
            <a href="<?php echo BASE_URL; ?>/logout" class="btn-perfil sair-btn">
                <i class='bx bx-log-out'></i> Sair da Conta
            </a>
        </div>
    </div>
</section>


<style>
    .favoritos-container {
        max-width: 1080px;
        margin: 40px auto;
        padding: 0 25px;
    }

    .favoritos-container h2 {
        font-size: 1.8em;
        border-bottom: 2px solid #eee;
        padding-bottom: 10px;
        margin-bottom: 30px;
    }

    .favoritos-container .produtos {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 280px));
        gap: 1.5rem;
    }

    .favoritos-container .linha {
        position: relative;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .produtos {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 280px));
        gap: 1.5rem;
        justify-content: center;
    }

    .linha {
        position: relative;
        background: white;
        padding: 15px;
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.3s;
    }

    .linha:hover {
        transform: translateY(-5px);
    }

    .linha img {
        width: 100%;
        border-radius: 5px;
    }

    .linha .coracao-icon {
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 1.5em;
        background: rgba(255, 255, 255, 0.7);
        border-radius: 50%;
        padding: 5px;
    }

    .linha .preco {
        text-align: left;
        padding: 10px 0 0 0;
    }

    .linha .preco h4 {
        font-size: 1.1em;
        margin-bottom: 5px;
    }

    .linha .avaliar {
        color: #f39c12;
        margin-bottom: 5px;
    }

    .linha .preco p {
        font-size: 1.1em;
        font-weight: 600;
        color: #333;
    }
</style>

<div class="favoritos-container">
    <h2><i class='bx bx-heart'></i> Meus Favoritos</h2>

    <div class="produtos">
        <?php if (empty($favoritos)): ?>
            <p>Você ainda não favoritou nenhum produto.</p>
        <?php else: ?>
            <?php foreach ($favoritos as $produto): ?>
                <div class="linha" id="favorito-card-<?php echo $produto['id']; ?>">
                    <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo $produto['id']; ?>">
                        <img src="<?php echo display_image_url($produto['imagem_url']); ?>"
                            alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                    </a>

                    <a href="<?php echo BASE_URL; ?>/favorito/toggle/<?php echo $produto['id']; ?>"
                        class="coracao-icon js-toggle-favorito" data-id="<?php echo $produto['id']; ?>">
                        <i class='bx bxs-heart favorito-icone' style="color: #ee1c47;"></i>
                    </a>

                    <div class="preco">
                        <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                        <?php
                        if (!function_exists('gerarEstrelas')) {
                            function gerarEstrelas($avaliacao)
                            {
                                $html = '<div class="avaliar">';
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
                        echo gerarEstrelas($produto['avaliacao_media'] ?? 0);
                        ?>

                        <?php if (!empty($produto['preco_promocional']) && $produto['is_promocao']): ?>
                            <p style="text-decoration: line-through; color: #777; font-size: 0.9em;">R$
                                <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                            <p>R$ <?php echo number_format($produto['preco_promocional'], 2, ',', '.'); ?></p>
                        <?php else: ?>
                            <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>