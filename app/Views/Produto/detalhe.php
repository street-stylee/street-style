<?php

if (!function_exists('gerarEstrelas')) {
    function gerarEstrelas($rating, $max_stars = 5) {
        $html = '<div class="rating-display">'; 
        $rating = round($rating * 2) / 2;
        for ($i = 1; $i <= $max_stars; $i++) {
            if ($rating >= $i) { 
                $html .= '<i class="bx bxs-star"></i>';
            } elseif ($rating > ($i - 1) && $rating < $i) { 
                $html .= '<i class="bx bxs-star-half"></i>';
            } else { 
                $html .= '<i class="bx bx-star"></i>';
            }
        }
        $html .= '</div>';
        return $html;
    }
}

$favoritos_ids = $favoritos_ids ?? [];
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/estilo_pg_produto.css">
<style>
    .opcoes-variacao label { display: block; font-weight: bold; margin-bottom: 8px; }
    .opcoes-lista { display: flex; gap: 10px; flex-wrap: wrap; }
    .variacao-btn { padding: 8px 15px; border: 1px solid #ccc; border-radius: 4px; cursor: pointer; transition: all 0.2s; user-select: none; font-size: 0.9em; }
    .variacao-btn:hover { border-color: #555; }
    .variacao-btn.selected { background-color: #2c3e50; color: white; border-color: #2c3e50; font-weight: bold; }
    .variacao-btn.disabled { opacity: 0.5; cursor: not-allowed; text-decoration: line-through; background-color: #f8f8f8; }
    #add-to-cart-btn:disabled { background-color: #bdc3c7; cursor: not-allowed; opacity: 0.7; }
</style>

<div class="pequeno-container single-product">
    <div class="row">
        <div class="col-2">
            <div id="img-zoom-container">
                <img src="<?php echo BASE_URL; ?>/_ADM/<?php echo htmlspecialchars($produto['imagem_url']); ?>"
                     id="ProductImg" alt="<?php echo htmlspecialchars($produto['nome']); ?>"
                     onmousemove="zoomImage(event)">
            </div>
            
            <div class="pequeno-img-row">
                <div class="pequeno-img-col">
                    <img src="<?php echo BASE_URL; ?>/_ADM/<?php echo htmlspecialchars($produto['imagem_url']); ?>"
                         class="pequeno-img" alt="Vista principal">
                </div>
                <?php foreach ($imagens_extras as $img_extra): ?>
                <div class="pequeno-img-col">
                    <img src="<?php echo BASE_URL; ?>/_ADM/<?php echo htmlspecialchars($img_extra['imagem_url']); ?>" class="pequeno-img" alt="Vista Extra">
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <div class="col-2">
            <p>Produtos / <?php echo htmlspecialchars($produto['categoria']); ?></p>
            <h1><?php echo htmlspecialchars($produto['nome']); ?></h1>
            
            <div class="avaliar">
                <?php echo gerarEstrelas($produto['avaliacao_media'] ?? 0); ?>
                <span class="nota-avaliacao">
                    (<?php echo number_format($produto['avaliacao_media'] ?? 0, 1, ',', '.'); ?>)
                </span>
            </div>
            
            <div style="margin-bottom: 20px;">
                <a href="<?php echo BASE_URL; ?>/favorito/toggle/<?php echo $produto['id']; ?>" 
                   class="coracao-icon js-toggle-favorito" 
                   data-id="<?php echo $produto['id']; ?>">
                    <?php if (in_array($produto['id'], $favoritos_ids)): ?>
                        <i class='bx bxs-heart favorito-icone' style="color: #ee1c47; font-size: 2em;"></i>
                    <?php else: ?>
                        <i class='bx bx-heart favorito-icone' style="font-size: 2em;"></i>
                    <?php endif; ?>
                </a>
            </div>

            <h4> <?php if ($produto['is_promocao']): ?>
                R$ <?php echo number_format($produto['preco_promocional'], 2, ',', '.'); ?>
            <?php else: ?>
                R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?>
            <?php endif; ?></h4>

            <form id="form-carrinho" action="<?php echo BASE_URL; ?>/carrinho/adicionar" method="post">
                <input type="hidden" name="produto_id" value="<?php echo htmlspecialchars($produto['id']); ?>">
                <input type="hidden" name="variacao_id" id="variacao-id" value="">

                <div class="opcoes-variacao">
                    <label>Tamanho:</label>
                    <div id="tamanho-options" class="opcoes-lista">
                        <?php if (empty($tamanhos_disponiveis)): ?>
                            <p style="color: red;">Nenhuma variação disponível.</p>
                        <?php else: ?>
                            <?php foreach ($tamanhos_disponiveis as $tamanho): ?>
                                <span class="variacao-btn" data-tamanho="<?php echo htmlspecialchars($tamanho); ?>">
                                    <?php echo htmlspecialchars($tamanho); ?>
                                </span>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
                <div id="estoque-status" style="margin: 15px 0; font-weight: bold;"></div>
                <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="1" required style="width: 70px;">
                
                <button type="submit" class="btn" id="add-to-cart-btn" disabled>Adicionar ao Carrinho</button>
            </form>
            <h3>Detalhes do Produto</h3>
            <br>
            <p><?php echo nl2br(htmlspecialchars($produto['descricao'])); ?></p>
        </div>
    </div>
</div>

<section class="em-alta" id="emalta">
    <div class="center-text">
        <h2>Produtos <span>Semelhantes</span></h2>
    </div>

    <div class="produtos">
        <?php foreach ($produtos_semelhantes as $similar_produto): ?>
            <div class="linha">
                <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo htmlspecialchars($similar_produto['id']); ?>">
                    <img src="<?php echo BASE_URL; ?>/_ADM/<?php echo htmlspecialchars($similar_produto['imagem_url']); ?>"
                         alt="<?php echo htmlspecialchars($similar_produto['nome']); ?>">
                </a>

                <a href="<?php echo BASE_URL; ?>/favorito/toggle/<?php echo $similar_produto['id']; ?>" 
                   class="coracao-icon js-toggle-favorito" 
                   data-id="<?php echo $similar_produto['id']; ?>">
                    <?php if (in_array($similar_produto['id'], $favoritos_ids)): ?>
                        <i class='bx bxs-heart favorito-icone' style="color: #ee1c47;"></i>
                    <?php else: ?>
                        <i class='bx bx-heart favorito-icone'></i>
                    <?php endif; ?>
                </a>

                <div class="preco">
                    <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo htmlspecialchars($similar_produto['id']); ?>">
                        <h4><?php echo htmlspecialchars($similar_produto['nome']); ?></h4>
                    </a>
                    <div class="avaliar">
                        <?php echo gerarEstrelas($similar_produto['avaliacao_media'] ?? 0); ?>
                    </div>
                    <p>R$ <?php echo number_format($similar_produto['preco'], 2, ',', '.'); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<style>
    .avaliacoes-container { max-width: 1080px; margin: 40px auto; padding: 0 25px; }
    .avaliacoes-container h2 { font-size: 1.8em; border-bottom: 2px solid #eee; padding-bottom: 10px; margin-bottom: 30px; }
    .avaliacao-item { border-bottom: 1px solid #f0f0f0; padding-bottom: 20px; margin-bottom: 20px; }
    .avaliacao-item:last-child { border-bottom: none; }
    .avaliacao-autor { font-weight: bold; color: #333; margin-bottom: 5px; }
    .avaliacao-autor small { font-weight: normal; color: #777; margin-left: 10px; }
    .avaliacao-estrelas-lista { color: #f39c12; margin-bottom: 10px; } 
    .avaliacao-estrelas-lista .bx-star { color: #ccc; }
    .avaliacao-titulo { font-weight: bold; margin-bottom: 5px; }
    .avaliacao-comentario { line-height: 1.6; color: #555; }
    .form-avaliacao { background: #f9f9f9; padding: 25px; border-radius: 8px; margin-top: 30px; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; font-weight: bold; margin-bottom: 5px; }
    .form-group input[type="text"], .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box; }
    .form-group textarea { min-height: 80px; }
    .alert-msg { padding: 15px; border-radius: 5px; margin-bottom: 20px; font-weight: bold; }
    .alert-msg.sucesso { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .alert-msg.erro { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    
    .rating-stars { display: inline-block; }
    .rating-stars input[type=radio] { display: none; }
    .rating-stars label {
        font-family: 'Boxicons';
        font-size: 2em; color: #ddd;
        cursor: pointer; float: right;
        transition: color 0.2s;
        content: "\f005"; 
    }
    .rating-stars label:before {
        content: '\ec27';
    }
    .rating-stars input[type=radio]:checked ~ label:before,
    .rating-stars label:hover:before,
    .rating-stars label:hover ~ label:before {
        content: '\eeb8';
        color: #f39c12;
    }
    .rating-stars {
        display: flex;
        unicode-bidi: bidi-override;
        direction: rtl;
        justify-content: flex-end;
    }
    .rating-stars label { float: none; }

    .rating-display {
        display: inline-flex;
        align-items: center;
        gap: 0px;
        color: #f39c12;
        margin-right: 5px;
        vertical-align: middle;
    }

    .rating-display .bx {
        font-size: 1.2em;
        line-height: 1;
    }

    .avaliar {
        display: flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 10px;
    }
    
    .nota-avaliacao {
        font-size: 0.95em;
        color: #555;
        vertical-align: middle;
    }
</style>

<div class="avaliacoes-container">
    <h2>Avaliações de Clientes (<?php echo count($avaliacoes); ?>)</h2>

    <?php 
    if (isset($mensagem_avaliacao)): ?>
        <div class="alert-msg <?php echo htmlspecialchars($mensagem_avaliacao['tipo']); ?>">
            <?php echo htmlspecialchars($mensagem_avaliacao['texto']); ?>
        </div>
    <?php endif; ?>

    <?php if ($usuario_logado): ?>
        <?php if ($ja_avaliou): ?>
            <div class="alert-msg sucesso" style="background-color: #ffeeb2; color: #856404; border: 1px solid #ffeeba;">
                Você já avaliou este produto. Obrigado pela sua contribuição!
            </div>
        <?php elseif ($pode_avaliar): ?>
            <div class="form-avaliacao">
                <h3>Deixe sua avaliação</h3>
                <p>Você comprou este produto e pode avaliá-lo.</p>
                <form action="<?php echo BASE_URL; ?>/produto/avaliar" method="POST">
                    <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                    <div class="form-group">
                        <label>Sua Nota (Obrigatório)</label>
                        <div class="rating-stars">
                            <input type="radio" id="star5" name="nota" value="5" required><label for="star5"></label>
                            <input type="radio" id="star4" name="nota" value="4"><label for="star4"></label>
                            <input type="radio" id="star3" name="nota" value="3"><label for="star3"></label>
                            <input type="radio" id="star2" name="nota" value="2"><label for="star2"></label>
                            <input type="radio" id="star1" name="nota" value="1"><label for="star1"></label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="titulo">Título da Avaliação (Opcional)</label>
                        <input type="text" id="titulo" name="titulo" placeholder="Ex: Incrível!">
                    </div>
                    <div class="form-group">
                        <label for="comentario">Seu Comentário (Opcional)</label>
                        <textarea id="comentario" name="comentario" placeholder="Descreva sua experiência com o produto..."></textarea>
                    </div>
                    <button type="submit" class="btn">Enviar Avaliação</button>
                </form>
            </div>
        <?php else: ?>
            <div class="alert-msg erro" style="background-color: #fcebeb; color: #a94442; border: 1px solid #ebccd1;">
                Você precisa ter comprado este produto para poder avaliá-lo.
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="alert-msg erro">
            <a href="<?php echo BASE_URL; ?>/login" style="color: #721c24; text-decoration: underline; font-weight: bold;">Faça login</a> para poder enviar uma avaliação (após a compra).
        </div>
    <?php endif; ?>

        <div id="lista-avaliacoes" style="margin-top: 40px;">
            <?php if (empty($avaliacoes)): ?>
                <p>Este produto ainda não possui avaliações.</p>
            <?php else: ?>
                <?php foreach ($avaliacoes as $avaliacao): ?>
                    <div class="avaliacao-item">
                        <div class="avaliacao-autor">
                            <?php echo htmlspecialchars($avaliacao['nome_usuario']); ?>
                            <small><?php echo date('d/m/Y', strtotime($avaliacao['data_avaliacao'])); ?></small>
                        </div>
                        
                        <div class="avaliacao-estrelas-lista" style="color: #f39c12;"> 
                            <?php echo gerarEstrelas($avaliacao['nota']); ?>
                        </div>
                        
                        <?php if (!empty($avaliacao['titulo'])): ?>
                            <h4 class="avaliacao-titulo"><?php echo htmlspecialchars($avaliacao['titulo']); ?></h4>
                        <?php endif; ?>
                        <p class="avaliacao-comentario"><?php echo nl2br(htmlspecialchars($avaliacao['comentario'])); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    var SmallImg = document.getElementsByClassName("pequeno-img");
    var ProductImg = document.getElementById("ProductImg");
    const imgZoomContainer = document.getElementById('img-zoom-container');

    function applyZoomBackground() {
        if (imgZoomContainer && ProductImg) { 
             imgZoomContainer.style.backgroundImage = `url('${ProductImg.src}')`;
        }
    }

    function mudarImagem(novaUrl) {
        if(ProductImg) {
            ProductImg.src = novaUrl;
            applyZoomBackground();
        }
    }

    for (let i = 0; i < SmallImg.length; i++) {
        SmallImg[i].onclick = function () {
            mudarImagem(this.src);
        };
    }

    document.addEventListener('DOMContentLoaded', applyZoomBackground);

    function zoomImage(e) {
        if (!ProductImg || !imgZoomContainer) return;
        const rect = ProductImg.getBoundingClientRect();
        const x = e.clientX - rect.left;
        const y = e.clientY - rect.top;
        const xPercent = (x / rect.width) * 100;
        const yPercent = (y / rect.height) * 100;
        imgZoomContainer.style.backgroundPosition = `${xPercent}% ${yPercent}%`;
    }

    if(ProductImg) {
        ProductImg.addEventListener('mouseover', () => {
            if(imgZoomContainer) imgZoomContainer.classList.add('zoom-active');
        });
        ProductImg.addEventListener('mouseout', () => {
            if(imgZoomContainer) {
                imgZoomContainer.classList.remove('zoom-active');
                imgZoomContainer.style.backgroundPosition = 'center center';
            }
        });
    }
</script>

<script>
    const VARIACAO_DATA = <?php echo json_encode($variacoes_json_data ?? []); ?>;
    const primeiroTamanho = '<?php echo $primeiro_tamanho ?? null; ?>';
    
    const tamanhoOptions = document.getElementById('tamanho-options');
    const variacaoIdInput = document.getElementById('variacao-id');
    const estoqueStatus = document.getElementById('estoque-status');
    const quantidadeInput = document.getElementById('quantidade');
    const addToCartBtn = document.getElementById('add-to-cart-btn');

    let selectedTamanho = null;
    
    document.addEventListener('DOMContentLoaded', () => {
         if (primeiroTamanho && tamanhoOptions) {
            const firstSizeElement = tamanhoOptions.querySelector(`[data-tamanho="${primeiroTamanho}"]`);
            if (firstSizeElement) {
                firstSizeElement.classList.add('selected');
                selectedTamanho = primeiroTamanho;
                updateStatus(selectedTamanho);
            }
         } else if (estoqueStatus) {
            estoqueStatus.innerHTML = '<span style="color: red;">Produto esgotado ou sem variações.</span>';
            if(addToCartBtn) addToCartBtn.disabled = true;
         }
    });

    if(tamanhoOptions) {
        tamanhoOptions.addEventListener('click', (e) => {
            if (e.target.classList.contains('variacao-btn')) {
                document.querySelectorAll('#tamanho-options .variacao-btn').forEach(el => el.classList.remove('selected'));
                e.target.classList.add('selected');
                selectedTamanho = e.target.dataset.tamanho;
                updateStatus(selectedTamanho);
            }
        });
    }

    function updateStatus(tamanho) {
        if (!estoqueStatus || !addToCartBtn || !variacaoIdInput || !quantidadeInput) {
            console.error("Elementos do DOM não encontrados para a variação.");
            return;
        }

        if (!tamanho || !VARIACAO_DATA[tamanho]) {
            estoqueStatus.innerHTML = '<span style="color: red;">Variação não encontrada.</span>';
            addToCartBtn.disabled = true;
            return;
        }

        const variacao = VARIACAO_DATA[tamanho];
        const estoque = variacao.estoque;

        if (estoque > 0) {
            estoqueStatus.innerHTML = `<span style="color: green;">Em estoque: ${estoque} unidades.</span>`;
            addToCartBtn.disabled = false;
            variacaoIdInput.value = variacao.id; 
            quantidadeInput.max = estoque;
            quantidadeInput.value = Math.min(quantidadeInput.value, estoque);
        } else {
            estoqueStatus.innerHTML = '<span style="color: red;">Esgotado!</span>';
            addToCartBtn.disabled = true;
            variacaoIdInput.value = '';
            quantidadeInput.max = 1;
            quantidadeInput.value = 1;
        }
    }
</script>