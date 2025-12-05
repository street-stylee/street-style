<?php

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
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/buscar.css">

<div id="main-container">
    <div id="search-info">
        <?php if (!empty($termo_busca)): ?>
            <h1>Resultados da Busca para: "<?php echo htmlspecialchars($termo_busca); ?>"</h1>
            <p>Encontramos <strong><?php echo $total_resultados; ?></strong> produtos correspondentes.</p>
        <?php else: ?>
            <h1>Página de Busca</h1>
            <p>Digite algo na barra de busca para começar.</p>
        <?php endif; ?>
    </div>

    <div id="produtos-grid">
        <?php if ($total_resultados > 0): ?>
            <?php foreach ($resultados as $produto): ?>
                <div id="produto-card">
                    <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo $produto['id']; ?>">
                        
                        <img src="<?php echo BASE_URL; ?>/_ADM/<?php echo htmlspecialchars($produto['imagem_url']); ?>"
                             alt="<?php echo htmlspecialchars($produto['nome']); ?>">
                        
                        <h3><?php echo htmlspecialchars($produto['nome']); ?></h3>
                        <p id="preco">R$ <?php echo number_format($produto['preco'], 2, ',', '.'); ?></p>
                        <p id="descricao-curta">
                            <?php echo mb_strimwidth(htmlspecialchars($produto['descricao']), 0, 70, "..."); ?>
                        </p>
                    </a>
                    <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo $produto['id']; ?>" class="btn-comprar" style="background-color: #52616b;">Ver Detalhes</a>
                </div>
            <?php endforeach; ?>
        <?php elseif (!empty($termo_busca)): ?>
            <div id="no-results">
                <i class='bx bx-sad'></i>
                <h2>Nenhum resultado encontrado.</h2>
                <p>Tente refinar sua busca com palavras-chave diferentes ou verifique a ortografia.</p>
                <a href="<?php echo BASE_URL; ?>/produtos" id="btn-primary">Ver todos os produtos</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
#produto-card .btn-comprar {
    display: inline-block;
    text-decoration: none;
    background-color: #333; 
    color: white;
    padding: 10px 15px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    text-align: center;
    margin-top: 10px;
    width: 100%;
    box-sizing: border-box;
    transition: background-color 0.3s;
}
#produto-card .btn-comprar:hover {
    background-color: #555;
}
</style>