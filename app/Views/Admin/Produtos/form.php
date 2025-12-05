<?php
?>

<style>
    .form-section { background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 25px; border: 1px solid #eee; }
    .form-group label { font-weight: 600; color: #444; margin-bottom: 8px; display: block; }
    input[type="text"], input[type="number"], textarea, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; font-size: 14px; }
    
    .upload-area {
        border: 2px dashed #3498db;
        background-color: #f0f8ff;
        padding: 30px;
        text-align: center;
        border-radius: 10px;
        cursor: pointer;
        transition: 0.3s;
        position: relative;
    }
    .upload-area:hover, .upload-area.dragover {
        background-color: #e1f0fa;
        border-color: #2980b9;
    }
    .upload-area i { font-size: 40px; color: #3498db; margin-bottom: 10px; }
    .upload-area p { margin: 0; color: #555; font-weight: 500; }
    
    .hidden-file-input {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .gallery-container {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-top: 15px;
    }
    .image-card {
        width: 120px;
        height: 120px;
        position: relative;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid #ddd;
        background: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .image-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-card .btn-remove {
        position: absolute;
        top: 5px;
        right: 5px;
        background: rgba(231, 76, 60, 0.9);
        color: white;
        border: none;
        border-radius: 50%;
        width: 24px;
        height: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        transition: 0.2s;
    }
    .image-card .btn-remove:hover { background: #c0392b; }
    .image-card .badge-type {
        position: absolute;
        bottom: 0; left: 0; width: 100%;
        background: rgba(0,0,0,0.6);
        color: white;
        font-size: 10px;
        text-align: center;
        padding: 3px 0;
    }

    .btn-submit { background-color: #27ae60; color: white; border: none; padding: 12px 25px; border-radius: 5px; font-weight: bold; cursor: pointer; font-size: 16px; display: inline-flex; align-items: center; gap: 8px; }
    .btn-submit:hover { background-color: #219150; }
    .btn-cancelar { background-color: #95a5a6; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-flex; align-items: center; }
    .btn-cancelar:hover { background-color: #7f8c8d; }
</style>

<form action="<?php echo BASE_URL; ?>/admin/produtos/salvar" method="POST" enctype="multipart/form-data">
    
    <?php if ($is_editing): ?>
        <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
    <?php endif; ?>

    <div class="form-section">
        <h2 style="margin-bottom: 20px; font-size: 1.2rem; border-bottom: 2px solid #eee; padding-bottom: 10px;">Informações Básicas</h2>
        <div class="form-group">
            <label for="nome">Nome do Produto</label>
            <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
        </div>

        <div class="form-group" style="margin-top: 15px;">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="4" required><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
        </div>

        <div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 15px;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="preco">Preço (R$)</label>
                <input type="number" id="preco" name="preco" step="0.01" value="<?php echo htmlspecialchars($produto['preco']); ?>" required>
            </div>
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" required>
                    <?php foreach ($categorias as $cat): ?>
                        <option value="<?php echo $cat; ?>" <?php echo ($produto['categoria'] === $cat) ? 'selected' : ''; ?>>
                            <?php echo $cat; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div style="background-color: #fdfaf3; padding: 15px; border-radius: 5px; margin-top: 20px; border: 1px solid #fae5b0;">
            <div style="display: flex; gap: 20px; align-items: center;">
                <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; font-weight: normal;">
                    <input type="checkbox" name="is_promocao" value="1" 
                           <?php echo ($produto['is_promocao'] ?? 0) ? 'checked' : ''; ?> 
                           onchange="togglePrecoPromocional(this.checked)">
                    Em Promoção
                </label>
                <label style="cursor: pointer; display: flex; align-items: center; gap: 5px; font-weight: normal;">
                    <input type="checkbox" name="is_novidade" value="1"
                           <?php echo ($produto['is_novidade'] ?? 0) ? 'checked' : ''; ?>>
                    Novidade
                </label>
            </div>
            
            <div class="form-group" style="margin-top: 15px; <?php echo ($produto['is_promocao'] ?? 0) ? '' : 'display: none;'; ?>" id="campo-preco-promocional">
                <label for="preco_promocional">Preço Promocional (R$)</label>
                <input type="number" id="preco_promocional" name="preco_promocional" step="0.01" 
                       value="<?php echo htmlspecialchars($produto['preco_promocional'] ?? '0.00'); ?>">
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 style="border-bottom: 2px solid #3498db; padding-bottom: 10px; margin-bottom: 20px; color: #3498db;">
            <i class='bx bx-image'></i> Imagem Principal
        </h2>
        
        <div style="display: flex; gap: 20px; flex-wrap: wrap; align-items: flex-start;">
            <div class="image-card" style="width: 200px; height: 200px; border-style: dashed; display: flex; align-items: center; justify-content: center; background: #f9f9f9;">
                <img id="main-preview-img" 
                     src="<?php echo !empty($produto['imagem_url']) ? BASE_URL . '/_ADM/' . $produto['imagem_url'] : ''; ?>" 
                     style="display: <?php echo !empty($produto['imagem_url']) ? 'block' : 'none'; ?>;">
                <span id="main-placeholder" style="color: #ccc; display: <?php echo !empty($produto['imagem_url']) ? 'none' : 'block'; ?>;">Sem imagem</span>
            </div>

            <div style="flex: 1;">
                <label>Selecione a imagem de capa</label>
                <input type="file" name="imagem_principal" class="form-control" accept="image/*" onchange="previewMainImage(this)">
                <small style="color: #777; display: block; margin-top: 5px;">
                    Formatos: JPG, PNG, WEBP. Se não selecionar nova, a atual será mantida.
                </small>

                <?php if (!empty($produto['imagem_url'])): ?>
                    <input type="hidden" name="imagem_url_atual" value="<?php echo $produto['imagem_url']; ?>">
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="form-section">
        <h2 style="border-bottom: 2px solid #e67e22; padding-bottom: 10px; margin-bottom: 20px; color: #e67e22;">
            <i class='bx bx-images'></i> Galeria de Fotos
        </h2>
        
        <div class="upload-area" id="drop-zone">
            <i class='bx bx-cloud-upload'></i>
            <p>Arraste imagens extras aqui ou clique para selecionar</p>
            <input type="file" name="imagens_extras[]" multiple class="hidden-file-input" id="input-extras" accept="image/*" onchange="handleFiles(this.files)">
        </div>

        <div class="gallery-container" id="gallery-container">
            
            <?php if (!empty($imagens_extras)): ?>
                <?php foreach ($imagens_extras as $img): ?>
                    <div class="image-card existing-img">
                        <img src="<?php echo BASE_URL . '/_ADM/' . $img['imagem_url']; ?>">
                        
                        <input type="hidden" name="imagens_mantidas[]" value="<?php echo $img['imagem_url']; ?>">
                        
                        <div class="badge-type">Salva</div>
                        <button type="button" class="btn-remove" onclick="removeExistingImage(this)">
                            <i class='bx bx-x'></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
    </div>

    <div class="form-section">
        <h2 style="border-bottom: 2px solid #27ae60; padding-bottom: 10px; margin-bottom: 20px; color: #27ae60;">
            <i class='bx bx-box'></i> Estoque por Tamanho
        </h2>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 15px;">
            <?php 
            $tamanhos = ['P', 'M', 'G', 'GG'];
            $estoque_por_tamanho = $estoque_por_tamanho ?? [];
            
            foreach ($tamanhos as $tamanho): 
                $valor_estoque = 0;
                if (isset($estoque_por_tamanho[$tamanho])) {
                    $valor_estoque = is_array($estoque_por_tamanho[$tamanho]) ? ($estoque_por_tamanho[$tamanho]['estoque'] ?? 0) : $estoque_por_tamanho[$tamanho];
                }
            ?>
                <div style="text-align: center; padding: 10px; border: 1px solid #ddd; border-radius: 5px; background: #f8fcf9;">
                    <label style="font-size: 1.2rem; font-weight: bold; color: #27ae60; margin-bottom: 5px;"><?php echo $tamanho; ?></label>
                    <input type="number" name="estoque[<?php echo $tamanho; ?>]" 
                           value="<?php echo $valor_estoque; ?>" 
                           min="0" placeholder="0"
                           style="text-align: center; font-weight: bold;">
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <button type="submit" class="btn-submit">
            <i class='bx bxs-save'></i> <?php echo $is_editing ? 'Salvar Alterações' : 'Criar Produto'; ?>
        </button>
        <a href="<?php echo BASE_URL; ?>/admin/produtos" class="btn-cancelar">Cancelar</a>
    </div>
</form>

<script>
    function previewMainImage(input) {
        const preview = document.getElementById('main-preview-img');
        const placeholder = document.getElementById('main-placeholder');

        if (!preview || !placeholder) return;

        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                placeholder.style.display = 'none';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function togglePrecoPromocional(checked) {
        const campo = document.getElementById('campo-preco-promocional');
        if (!campo) return;
        campo.style.display = checked ? 'block' : 'none';
    }

    let dt = new DataTransfer();
    const inputExtras = document.getElementById('input-extras');

    function handleFiles(files) {
        const gallery = document.getElementById('gallery-container');
        if (!gallery || !inputExtras) return;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            dt.items.add(file);

            const reader = new FileReader();
            reader.onload = (function(f) {
                return function(e) {
                    const card = document.createElement('div');
                    card.className = 'image-card new-img';

                    const fileId = f.name + '-' + f.size + '-' + f.lastModified;
                    card.dataset.fileId = fileId;

                    card.innerHTML = `
                        <img src="${e.target.result}" alt="${f.name}">
                        <div class="badge-type" style="background: rgba(52, 152, 219, 0.8)">Nova</div>
                        <button type="button" class="btn-remove" onclick="removeNewFile('${fileId}', this)">
                            <i class='bx bx-x'></i>
                        </button>
                    `;
                    gallery.appendChild(card);
                };
            })(file);
            reader.readAsDataURL(file);
        }

        inputExtras.files = dt.files;
    }

    function removeNewFile(fileId, btn) {
        const card = btn.closest('.image-card');
        if (card) card.remove();

        const newDt = new DataTransfer();
        const files = dt.files;
        for (let i = 0; i < files.length; i++) {
            const currentId = files[i].name + '-' + files[i].size + '-' + files[i].lastModified;
            if (currentId !== fileId) {
                newDt.items.add(files[i]);
            }
        }
        dt = newDt;
        if (inputExtras) inputExtras.files = dt.files;
    }

    function removeExistingImage(btn) {
        if (!confirm('Essa imagem será excluída ao salvar. Deseja continuar?')) return;
        const card = btn.closest('.image-card');
        if (!card) return;
        const hidden = card.querySelector('input[type="hidden"][name="imagens_mantidas[]"]');
        if (hidden) hidden.remove();
        card.remove();
    }

    const dropZone = document.getElementById('drop-zone');
    if (dropZone) {
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('dragover'), false);
        });

        dropZone.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            handleFiles(files);
        }, false);

        dropZone.addEventListener('click', () => {
            if (inputExtras) inputExtras.click();
        });
    }
</script>
