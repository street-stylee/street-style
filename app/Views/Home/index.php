<?php

if (!function_exists('getImagePath')) {
    function getImagePath($db_path) {
        $clean_path = str_ireplace('public/', '', $db_path);
        return BASE_URL . '/' . ltrim($clean_path, '/');
    }
}
if (!function_exists('gerarEstrelas')) {
    function gerarEstrelas($avaliacao) {
        $html = '<div class="avaliar">';
        $avaliacao_arredondada = round($avaliacao * 2) / 2;
        for ($i = 1; $i <= 5; $i++) {
            if ($avaliacao_arredondada >= $i) { $html .= '<i class="bx bxs-star"></i>'; }
            elseif ($avaliacao_arredondada == $i - 0.5) { $html .= '<i class="bx bxs-star-half"></i>'; }
            else { $html .= '<i class="bx bx-star"></i>'; }
        }
        $html .= '</div>';
        return $html;
    }
}

$favoritos_ids = $favoritos_ids ?? []; 
?>

<section class="main-home">
    <?php if (empty($slides_carrossel)): ?>
    <div class="main-text" style="z-index: 10;">
        <h5>Lançamento Exclusivo</h5>
        <h1>Conjunto Verão<br> 2025</h1>

        <a href="<?php echo BASE_URL; ?>/produto/detalhe/17" class="main-btn" id="link-a">
            Compre Agora <i class='bx bx-right-arrow-alt'></i>
        </a>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($slides_carrossel)): ?>
    <div class="home-carrossel" style="position: absolute; inset: 0; z-index: 1; overflow: hidden;">
        <div class="carrossel-track" id="carrossel-track" style="display:flex; transition: transform 0.6s ease; height:100%;">
            <?php foreach ($slides_carrossel as $slide): ?>
                <div class="carrossel-slide" style="min-width:100%; height:100%; position:relative; display:flex; align-items:center; justify-content:center;">
                    <img src="<?php echo display_image_url($slide['imagem_url']); ?>" alt="<?php echo htmlspecialchars($slide['titulo'] ?? ''); ?>" style="width:100%; height:100%; object-fit:cover; display:block;">
                    <?php if (!empty($slide['titulo']) || !empty($slide['subtitulo'])): ?>
                        <div class="carrossel-caption" style="position:absolute; left:40px; bottom:40px; color:white; text-shadow:0 2px 8px rgba(0,0,0,0.6); display:flex; flex-direction:column; gap:15px;">
                            <div>
                                <?php if (!empty($slide['subtitulo'])): ?><h5 style="margin:0; font-size:0.9em; font-weight:600; letter-spacing:2px;"><?php echo htmlspecialchars($slide['subtitulo']); ?></h5><?php endif; ?>
                                <?php if (!empty($slide['titulo'])): ?><h1 style="margin:0; font-size:2.5em; font-weight:700; line-height:1.2;"><?php echo htmlspecialchars($slide['titulo']); ?></h1><?php endif; ?>
                            </div>
                            <?php if (!empty($slide['link_url'])): ?>
                                <a href="<?php echo htmlspecialchars($slide['link_url']); ?>" class="main-btn" style="display:inline-block; background-color:#ff8c00; color:white; padding:12px 25px; text-decoration:none; border-radius:5px; font-weight:600; font-size:0.95em; transition:background-color 0.3s ease; width:fit-content;">
                                    Comprar <i class='bx bx-right-arrow-alt'></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>

        <button id="carrossel-prev" aria-label="Anterior" style="position:absolute; left:10px; top:50%; transform:translateY(-50%); z-index:5; background:rgba(0,0,0,0.4); border:none; color:white; padding:10px; border-radius:50%; cursor:pointer;">
            ‹
        </button>
        <button id="carrossel-next" aria-label="Próximo" style="position:absolute; right:10px; top:50%; transform:translateY(-50%); z-index:5; background:rgba(0,0,0,0.4); border:none; color:white; padding:10px; border-radius:50%; cursor:pointer;">
            ›
        </button>
        <div id="carrossel-indicators" style="position:absolute; left:50%; transform:translateX(-50%); bottom:14px; z-index:5; display:flex; gap:8px;"></div>
    </div>
    <?php endif; ?>
    
    <div class="seta-baixo" style="z-index: 10;">
        <a href="#emalta" class="baixo"><i class='bx bx-down-arrow-alt'></i></a>
    </div>
</section>

<section class="em-alta sessao-ofertas-destaques" id="emalta">
    <div class="center-text">
        <h2>Nossos produtos <span>em alta</span> (Novidades)</h2>
    </div>

    <div class="produtos">
        <?php if (empty($produtos_novidade)): ?>
            <p style="text-align: center; width: 100%;">Nenhum produto marcado como "Novidade" no momento.</p>
        <?php else: ?>
            <?php foreach ($produtos_novidade as $produto): ?>
                <div class="linha">
                    <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo $produto['id']; ?>">
                        <img src="<?php echo display_image_url($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                        <div class="product-text">
                            <h5>Novo</h5>
                        </div>
                    </a>

                    <a href="<?php echo BASE_URL; ?>/favorito/toggle/<?php echo $produto['id']; ?>" 
                       class="coracao-icon js-toggle-favorito" 
                       data-id="<?php echo $produto['id']; ?>">
                        
                        <?php if (in_array($produto['id'], $favoritos_ids)): ?>
                            <i class='bx bxs-heart favorito-icone' style="color: #ee1c47;"></i>
                        <?php else: ?>
                            <i class='bx bx-heart favorito-icone'></i>
                        <?php endif; ?>
                    </a>
                    
                    <div class="preco">
                        <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                        <?php echo gerarEstrelas($produto['avaliacao_media'] ?? 0); ?>
                        <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="vantagens">
    <div class="vantagem-item">
        <i class='bx bxs-truck'></i>
        <h4>Frete Rápido e Seguro</h4>
        <p>Entregamos para todo o Brasil.</p>
    </div>
    <div class="vantagem-item">
        <i class='bx bxs-check-shield'></i>
        <h4>100% Original</h4>
        <p>Trabalhamos apenas com marcas autênticas.</p>
    </div>
    <div class="vantagem-item">
        <i class='bx bxs-credit-card'></i>
        <h4>Pagamento em 12x</h4>
        <p>Parcele sem juros no cartão de crédito.</p>
    </div>
    <div class="vantagem-item">
        <i class='bx bx-refresh'></i>
        <h4>Troca Grátis</h4>
        <p>Se não serviu, a primeira troca é por nossa conta.</p>
    </div>
</section>

<section class="em-alta sessao-ofertas-destaques" id="ofertas">
    <div class="center-text">
        <h2>Ofertas!</h2>
    </div>

    <div class="produtos">
        <?php if (empty($produtos_promocao)): ?>
            <p style="text-align: center; width: 100%;">Nenhuma promoção ativa no momento.</p>
        <?php else: ?>
            <?php foreach ($produtos_promocao as $produto): ?>
                <div class="linha">
                    <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo $produto['id']; ?>">
                        <img src="<?php echo display_image_url($produto['imagem_url']); ?>" alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                        <div class="product-text">
                            <h5>Oferta!</h5>
                        </div>
                    </a>
                    
                    <a href="<?php echo BASE_URL; ?>/favorito/toggle/<?php echo $produto['id']; ?>" 
                       class="coracao-icon js-toggle-favorito" 
                       data-id="<?php echo $produto['id']; ?>">
                        
                        <?php if (in_array($produto['id'], $favoritos_ids)): ?>
                            <i class='bx bxs-heart favorito-icone' style="color: #ee1c47;"></i>
                        <?php else: ?>
                            <i class='bx bx-heart favorito-icone'></i>
                        <?php endif; ?>
                    </a>
                    
                    <div class="preco">
                        <h4><?php echo htmlspecialchars($produto['nome']); ?></h4>
                        <?php echo gerarEstrelas($produto['avaliacao_media'] ?? 0); ?>
                        
                        <?php if (!empty($produto['preco_promocional']) && $produto['preco_promocional'] > 0): ?>
                            <p style="text-decoration: line-through; color: #777; font-size: 0.9em;">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                            <p>R$ <?php echo number_format($produto['preco_promocional'], 2, ',', '.'); ?></p>
                        <?php else: ?>
                            <p>R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<h1 class="h1-marquee">Marcas</h1>
<div class="marquee" id="marquee">
    <div class="marquee-track">
        <div class="marquee-group">
            <span class="span-img"><img class="marquee-img" src="<?php echo display_image_url('_ADM/img/marcas/nike.png'); ?>" alt="Nike"></span>
            <span class="span-img"><img class="marquee-img" src="<?php echo display_image_url('_ADM/img/marcas/high.webp'); ?>" alt="High"></span>
            <span class="span-img"><img class="marquee-img" src="<?php echo display_image_url('_ADM/img/marcas/adidas.png'); ?>" alt="Adidas"></span>
            <span class="span-img"><img class="marquee-img" src="<?php echo display_image_url('_ADM/img/marcas/oakley.png'); ?>" alt="Oakley"></span>
            <span class="span-img"><img class="marquee-img" src="<?php echo display_image_url('_ADM/img/marcas/asics.png'); ?>" alt="Asics"></span>
            <span class="span-img"><img class="marquee-img" src="<?php echo display_image_url('_ADM/img/marcas/under.png'); ?>" alt="Under Armour"></span>
        </div>
    </div>
</div>
<br><br>
<?php if (!empty($slides_carrossel)): ?>
<script>
    (function(){
        const track = document.getElementById('carrossel-track');
        const slides = track ? track.children : [];
        const prevBtn = document.getElementById('carrossel-prev');
        const nextBtn = document.getElementById('carrossel-next');
        const indicators = document.getElementById('carrossel-indicators');
        if (!track || slides.length === 0) return;

        let index = 0;
        const total = slides.length;
        let timer = null;

        function goTo(i){
            index = (i + total) % total;
            track.style.transform = 'translateX(' + (-index * 100) + '%)';
            Array.from(indicators.children).forEach((c, idx)=> c.style.opacity = (idx===index? '1':'0.4'));
        }

        function next(){ goTo(index+1); }
        function prev(){ goTo(index-1); }

        for(let i=0;i<total;i++){
            const b = document.createElement('button');
            b.style.width='10px'; b.style.height='10px'; b.style.borderRadius='50%'; b.style.border='none'; b.style.background='white'; b.style.opacity = i===0? '1':'0.4'; b.style.cursor='pointer';
            b.addEventListener('click', ()=>{ goTo(i); resetTimer(); });
            indicators.appendChild(b);
        }

        if (nextBtn) nextBtn.addEventListener('click', ()=>{ next(); resetTimer(); });
        if (prevBtn) prevBtn.addEventListener('click', ()=>{ prev(); resetTimer(); });

        function resetTimer(){ if (timer) clearInterval(timer); timer = setInterval(next, 5000); }
        resetTimer();

        window.addEventListener('resize', ()=> goTo(index));
    })();
</script>
<?php endif; ?>