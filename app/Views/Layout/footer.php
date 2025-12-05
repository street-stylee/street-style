<?php

$footer_contato_html = $configuracoes['footer_contato'] ?? null;
$footer_suporte_html = $configuracoes['footer_suporte'] ?? null;
$footer_junte_se_html = $configuracoes['footer_junte_se'] ?? null;
$footer_pagamento_html = $configuracoes['footer_pagamento'] ?? null;
$footer_ajuda_html = $configuracoes['footer_ajuda'] ?? null;
?>

<section class="contato">
    <div class="contato-info">
        
        <div class="primeiro-info">
            <img src="<?php echo BASE_URL; ?>/_ADM/img/logotipo2.png" alt="Logotipo Street Style Rodapé">
            
            <?php if ($footer_contato_html): ?>
                <?php echo $footer_contato_html; ?>
            <?php else: ?>
                <p>ETEC Jornalista Roberto Marinho, <br> São Paulo - SP</p>
                <p>tccterceiroinfo@gmail.com</p>
            <?php endif; ?>

            <div class="social-icon">
                <a href="https://www.facebook.com/profile.php?id=61554518331187"><i class='bx bxl-facebook'></i></a>
                <a href="https://twitter.com/home"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="https://www.instagram.com/streetstyle.ufc/"><i class='bx bxl-instagram'></i></a>
                <a href="https://www.youtube.com/"><i class='bx bxl-youtube'></i></a>
            </div>
        </div>

        <div class="segundo-info">
            <?php if ($footer_suporte_html): ?>
                <?php echo $footer_suporte_html; ?>
            <?php else: ?>
                <h4>Suporte</h4>
                <p><a href="<?php echo BASE_URL; ?>/contato">Contato</a></p>
                <p><a href="<?php echo BASE_URL; ?>/sobre">Sobre nós</a></p>
                <p>Políticas de privacidade</p>
            <?php endif; ?>
        </div>

        <div class="terceiro-info">
            <?php if ($footer_junte_se_html): ?>
                <?php echo $footer_junte_se_html; ?>
            <?php else: ?>
                <h4>Junte-se conosco</h4>
                <p>Venda na Street Style</p>
                <p>Anuncie sua empresa</p>
            <?php endif; ?>
        </div>

        <div class="quarto-info">
            <?php if ($footer_pagamento_html): ?>
                <?php echo $footer_pagamento_html; ?>
            <?php else: ?>
                <h4>Pagamento</h4>
                <p>Meios de <br>Pagamento</p>
                <p>Cartão de Crédito</p>
            <?php endif; ?>
        </div>

        <div class="cinco">
            <?php if ($footer_ajuda_html): ?>
                <?php echo $footer_ajuda_html; ?>
            <?php else: ?>
                <h4>Deixe-nos ajudar você</h4>
                <p><a href="<?php echo BASE_URL; ?>/login">Sua conta</a></p>
                <p>Frete e prazo de entrega</p>
                <p> <a href="<?php echo BASE_URL; ?>/contato "> Ajuda</a></p>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="texto-final">
    <p>Street Style © 2025. Todos os direitos reservados.</p>
</div>


<div class="acessibilidade-float">
    <button id="btn-diminuir" title="Diminuir Fonte">A-</button>
    <button id="btn-reset" title="Tamanho Original">A</button>
    <button id="btn-aumentar" title="Aumentar Fonte">A+</button>
    
    <span class="divisor">|</span>
    
    <button id="btn-contraste" title="Mudar Cores (Normal -> Cinza -> Contraste)">
        <i class='bx bxs-low-vision'></i>
    </button>

    <span class="divisor">|</span>
    
    <button id="btn-ler" title="Ouvir Texto da Página"><i class='bx bx-volume-full'></i></button>
</div>

<style>
    /* Estilo do Widget */
    .acessibilidade-float {
        position: fixed;
        bottom: 20px; left: 20px; z-index: 9999;
        background: rgba(0, 0, 0, 0.9);
        padding: 10px 15px;
        border-radius: 50px;
        display: flex; align-items: center; gap: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.4);
        border: 1px solid #444;
    }
    .acessibilidade-float button {
        background: #fff; color: #333; border: none;
        width: 35px; height: 35px; border-radius: 50%;
        font-weight: bold; cursor: pointer; font-size: 14px;
        transition: all 0.3s;
        display: flex; align-items: center; justify-content: center;
    }
    .acessibilidade-float button:hover {
        background: #f39c12; color: white; transform: scale(1.1);
    }
    .acessibilidade-float .divisor { color: #666; font-size: 1.2em; }
    .acessibilidade-float button.falando {
        background-color: #e74c3c; color: white; animation: pulse 1.5s infinite;
    }
    .acessibilidade-float button i { font-size: 1.2em; }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(231, 76, 60, 0); }
        100% { box-shadow: 0 0 0 0 rgba(231, 76, 60, 0); }
    }

    /* --- MODOS DE COR (DALTONISMO/CONTRASTE) --- */
    
    /* Modo Escala de Cinza (Ajuda na distinção de valores) */
    html.modo-cinza {
        filter: grayscale(100%);
    }

    /* Modo Alto Contraste (Inverte e aumenta contraste) */
    html.modo-contraste {
        filter: contrast(150%) brightness(110%);
    }

    /* Link */
    .contato a{
        color: #333;
            transition: all .42s;
    }
    .contato a:hover{
        color: #ff8c00;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        
        // --- 1. CONTROLE DE FONTE ---
        const btnAumentar = document.getElementById('btn-aumentar');
        const btnDiminuir = document.getElementById('btn-diminuir');
        const btnReset = document.getElementById('btn-reset');

        // Usa ZOOM para garantir compatibilidade com PX
        let currentZoom = localStorage.getItem('userZoom') ? parseFloat(localStorage.getItem('userZoom')) : 1.0;

        function applyZoom(zoom) {
            document.body.style.zoom = zoom;
            // document.body.style.fontSize = zoom + "px";

            // Firefox fallback
            if (navigator.userAgent.indexOf("Firefox") != -1) {
                document.body.style.transform = `scale(${zoom})`;
                document.body.style.transformOrigin = "0 0";
                document.body.style.width = `${100 / zoom}%`;
            }
        }
        // Aplica ao carregar
        applyZoom(currentZoom);

        if(btnAumentar) btnAumentar.addEventListener('click', () => {
            if (currentZoom < 1.5) { currentZoom += 0.1; localStorage.setItem('userZoom', currentZoom); applyZoom(currentZoom); }
        });
        if(btnDiminuir) btnDiminuir.addEventListener('click', () => {
            if (currentZoom > 0.7) { currentZoom -= 0.1; localStorage.setItem('userZoom', currentZoom); applyZoom(currentZoom); }
        });
        if(btnReset) btnReset.addEventListener('click', () => {
            currentZoom = 1.0; localStorage.setItem('userZoom', currentZoom); applyZoom(currentZoom);
        });


        // --- 2. CONTROLE DE CORES (DALTONISMO) ---
        const btnContraste = document.getElementById('btn-contraste');
        const modos = ['', 'modo-cinza', 'modo-contraste']; // '' = Normal
        let modoAtualIndex = localStorage.getItem('userColorMode') ? parseInt(localStorage.getItem('userColorMode')) : 0;

        function aplicarModoCor(index) {
            // Remove todas as classes de modo
            document.documentElement.classList.remove('modo-cinza', 'modo-contraste');
            
            // Adiciona a classe se não for o modo normal (0)
            if (index > 0 && modos[index]) {
                document.documentElement.classList.add(modos[index]);
            }
        }
        // Aplica ao carregar
        aplicarModoCor(modoAtualIndex);

        if(btnContraste) {
            btnContraste.addEventListener('click', () => {
                // Cicla entre os modos: 0 -> 1 -> 2 -> 0
                modoAtualIndex = (modoAtualIndex + 1) % modos.length;
                localStorage.setItem('userColorMode', modoAtualIndex);
                aplicarModoCor(modoAtualIndex);
            });
        }


        // --- 3. LEITURA DE TEXTO (TTS) ---
        const btnLer = document.getElementById('btn-ler');
        let synth = window.speechSynthesis;
        let utterance = null;

        if(btnLer) {
            btnLer.addEventListener('click', () => {
                if (synth.speaking) {
                    synth.cancel();
                    btnLer.classList.remove('falando');
                    btnLer.innerHTML = "<i class='bx bx-volume-full'></i>";
                } else {
                    let elementToRead = document.querySelector('main') || document.querySelector('.pequeno-container') || document.body;
                    let text = elementToRead.innerText;
                    if (text) {
                        utterance = new SpeechSynthesisUtterance(text);
                        utterance.lang = 'pt-BR';
                        utterance.onend = function() {
                            btnLer.classList.remove('falando');
                            btnLer.innerHTML = "<i class='bx bx-volume-full'></i>";
                        };
                        synth.speak(utterance);
                        btnLer.classList.add('falando');
                        btnLer.innerHTML = "<i class='bx bx-stop'></i>";
                    }
                }
            });
        }
    });
</script>

<script src="<?php echo BASE_URL; ?>/java.js"></script>
<script src="<?php echo BASE_URL; ?>/_ADM/js/marquee.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const botoesFavorito = document.querySelectorAll('.js-toggle-favorito');
    botoesFavorito.forEach(botao => {
        botao.addEventListener('click', function(e) {
            e.preventDefault(); 
            const link = this;
            const produtoId = link.dataset.id;
            const urlToggle = `<?php echo BASE_URL; ?>/favorito/toggle/${produtoId}`;
            const icone = link.querySelector('.favorito-icone');
            fetch(urlToggle)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        if (data.action === 'added') {
                            icone.classList.remove('bx-heart'); icone.classList.add('bxs-heart'); icone.style.color = '#ee1c47';
                        } else if (data.action === 'removed') {
                            icone.classList.remove('bxs-heart'); icone.classList.add('bx-heart'); icone.style.color = ''; 
                        }
                    } else if (data.message === 'login_required') {
                        const redirectProdutoId = link.dataset.id || '';
                        window.location.href = `<?php echo BASE_URL; ?>/login?redirect=produto/detalhe/${redirectProdutoId}`;
                    } else { alert('Erro: ' + data.message); }
                })
                .catch(error => { console.error('Erro na requisição AJAX:', error); });
        });
    });
});
</script>

<style>
    .coracao-icon { opacity: 1 !important; visibility: visible !important; background: transparent !important; border: none !important; }
    .coracao-icon .bx-heart, .coracao-icon .bxs-heart { opacity: 1 !important; display: inline-block !important; font-size: 1.5em; color: #333; }
    .coracao-icon .bxs-heart { color: #ee1c47 !important; }
</style>

<div vw class="enabled">
    <div vw-access-button class="active"></div>
    <div vw-plugin-wrapper><div class="vw-plugin-top-wrapper"></div></div>
</div>
<script src="https://vlibras.gov.br/app/vlibras-plugin.js"></script>
<script>new window.VLibras.Widget({ position: 'R', Yposition: 'BF' });</script>

</body>
</html>