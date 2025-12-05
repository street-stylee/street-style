<?php
?>

<style>
    .form-container { max-width: 100%; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
    .form-group textarea { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-family: Arial, sans-serif; }
    .btn-submit { background-color: #27ae60; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; }
    .alert-success { padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    .alert-error { padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px; }
    
    @media (max-width: 768px) {
        .form-container { padding: 20px; }
        .form-group label { font-size: 0.95em; }
        .btn-submit { padding: 10px 20px; font-size: 0.95em; }
    }
    
    @media (max-width: 480px) {
        .form-container { padding: 15px; }
        .form-group label { font-size: 0.9em; }
        .btn-submit { width: 100%; padding: 12px; font-size: 0.9em; }
    }
</style>

<script src="https://cdn.tiny.cloud/1/g649hd00h9z1vrkpxtssn3w3kuoh67azcnbobd9t5xdy8eew/tinymce/8/tinymce.min.js" referrerpolicy="origin" crossorigin="anonymous"></script>
<script>
  tinymce.init({
    selector: 'textarea.tinymce-editor',
    plugins: 'lists link image code table help wordcount',
    toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | link image | code',
    height: 300,
    menubar: 'file edit view insert format tools'
  });
</script>

<h1><i class='bx bx-cog'></i> <?php echo htmlspecialchars($titulo_pagina); ?></h1>

<?php if (isset($mensagem)): ?>
    <div class="alert-<?php echo $mensagem['tipo']; ?>">
        <?php echo htmlspecialchars($mensagem['texto']); ?>
    </div>
<?php endif; ?>

<div class="form-container">
    <form action="<?php echo BASE_URL; ?>/admin/configuracoes/salvar" method="POST">
        
        <div class="form-group">
            <label for="footer_contato"><i class='bx bx-info-circle'></i> Coluna 1: Informações de Contato</label>
            <textarea id="footer_contato" class="tinymce-editor" name="footer_contato"><?php echo htmlspecialchars($config['footer_contato'] ?? '<p>ETEC Jornalista Roberto Marinho, São Paulo - SP</p><p>tccterceiroinfo@gmail.com</p>'); ?></textarea>
        </div>

        <div class="form-group">
            <label for="footer_suporte"><i class='bx bx-headphone'></i> Coluna 2: Suporte</label>
            <textarea id="footer_suporte" class="tinymce-editor" name="footer_suporte"><?php echo htmlspecialchars($config['footer_suporte'] ?? '<h4>Suporte</h4><p><a href="#">Contato</a></p><p><a href="#">Sobre nós</a></p><p>Políticas de privacidade</p>'); ?></textarea>
        </div>

        <div class="form-group">
            <label for="footer_junte_se"><i class='bx bx-group'></i> Coluna 3: Junte-se Conosco</label>
            <textarea id="footer_junte_se" class="tinymce-editor" name="footer_junte_se"><?php echo htmlspecialchars($config['footer_junte_se'] ?? '<h4>Junte-se conosco</h4><p>Venda na Street Style</p><p>Anuncie sua empresa</p>'); ?></textarea>
        </div>

        <div class="form-group">
            <label for="footer_pagamento"><i class='bx bx-credit-card'></i> Coluna 4: Pagamento</label>
            <textarea id="footer_pagamento" class="tinymce-editor" name="footer_pagamento"><?php echo htmlspecialchars($config['footer_pagamento'] ?? '<h4>Pagamento</h4><p>Meios de Pagamento</p><p>Cartão de Crédito</p>'); ?></textarea>
        </div>

        <div class="form-group">
            <label for="footer_ajuda"><i class='bx bx-help-circle'></i> Coluna 5: Deixe-nos Ajudar</label>
            <textarea id="footer_ajuda" class="tinymce-editor" name="footer_ajuda"><?php echo htmlspecialchars($config['footer_ajuda'] ?? '<h4>Deixe-nos ajudar você</h4><p><a href="#">Sua conta</a></p><p>Frete e prazo de entrega</p><p><a href="#">Ajuda</a></p>'); ?></textarea>
        </div>
        
        <button type="submit" class="btn-submit"><i class='bx bx-save'></i> Salvar Configurações</button>
    </form>
</div>