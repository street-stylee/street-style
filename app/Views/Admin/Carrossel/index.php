<?php
?>

<style>
    .form-container { max-width: 100%; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); margin-bottom: 30px;}
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
    .form-group input[type="text"], .form-group input[type="file"] {
        width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; box-sizing: border-box;
    }
    .btn-submit { background-color: #27ae60; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; }
    
    .slides-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
    .slide-card { background: white; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); overflow: hidden; }
    .slide-card img { width: 100%; height: 150px; object-fit: cover; background-color: #eee; }
    .slide-card-info { padding: 15px; }
    .slide-card-info p { margin: 0; font-size: 0.9em; color: #555; word-wrap: break-word; }
    .slide-card-info p strong { color: #333; }
    .slide-card-actions { padding: 10px 15px; background: #f9f9f9; text-align: right; }
    .btn-excluir { color: #e74c3c; text-decoration: none; font-weight: bold; font-size: 0.9em; }
    
    .alert-success { padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    .alert-error { padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px; }
    
    @media (max-width: 768px) {
        .form-container { padding: 20px; }
        .slides-grid { grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 15px; }
        .slide-card img { height: 120px; }
    }
    @media (max-width: 480px) {
        .form-container { padding: 15px; }
        .slides-grid { grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 10px; }
        .slide-card img { height: 100px; }
        .slide-card-info { padding: 10px; }
        .form-group label { font-size: 0.9em; }
        .btn-submit { padding: 10px 15px; font-size: 0.9em; }
    }
</style>

<h1><?php echo htmlspecialchars($titulo_pagina); ?></h1>

<?php if (isset($mensagem)): ?>
    <div class="alert-<?php echo $mensagem['tipo']; ?>">
        <?php echo htmlspecialchars($mensagem['texto']); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="<?php echo BASE_URL; ?>/admin/carrossel/salvar" method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label for="imagem_slide">Imagem do Slide (Obrigatório)</label>
            <input type="file" id="imagem_slide" name="imagem_slide" accept="image/png, image/jpeg, image/webp" required>
        </div>
        <div class="form-group">
            <label for="titulo">Título (Opcional)</label>
            <input type="text" id="titulo" name="titulo" placeholder="Ex: Nova Coleção">
        </div>
        <div class="form-group">
            <label for="subtitulo">Subtítulo (Opcional)</label>
            <input type="text" id="subtitulo" name="subtitulo" placeholder="Ex: Verão 2025">
        </div>
        <div class="form-group">
            <label for="link_url">Link do Botão (Opcional)</label>
            <input type="text" id="link_url" name="link_url" placeholder="Ex: /produtos/detalhe/17">
        </div>
        <button type="submit" class="btn-submit">Adicionar Slide</button>
    </form>
</div>

<h2>Slides Atuais</h2>
<div class="slides-grid">
    <?php if (empty($slides)): ?>
        <p>Nenhum slide cadastrado.</p>
    <?php else: ?>
        <?php foreach ($slides as $slide): ?>
            <div class="slide-card">
                <?php
                $src = '';
                if (function_exists('display_image_url')) {
                    $src = display_image_url($slide['imagem_url']);
                } else {
                    $src = rtrim(BASE_URL, '/') . '/' . ltrim($slide['imagem_url'], '/');
                }
                ?>
                <img src="<?php echo $src; ?>" alt="Slide">
                <div class="slide-card-info">
                    <p><strong>Título:</strong> <?php echo htmlspecialchars($slide['titulo'] ?? 'N/A'); ?></p>
                    <p><strong>Link:</strong> <?php echo htmlspecialchars($slide['link_url'] ?? 'N/A'); ?></p>
                </div>
                <div class="slide-card-actions">
                    <a href="<?php echo BASE_URL; ?>/admin/carrossel/excluir/<?php echo $slide['id']; ?>" class="btn-excluir" onclick="return confirm('Tem certeza?');">
                        Excluir
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>