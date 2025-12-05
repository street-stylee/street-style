<?php

if (empty($variacoes)) {
    $variacoes_display = [['tamanho' => '', 'estoque' => 0]];
} else {
    $variacoes_display = [];
    foreach ($variacoes as $tamanho => $dados) {
        $variacoes_display[] = ['tamanho' => $tamanho, 'estoque' => $dados['estoque']];
    }
}

?>

<style>
    .variacoes-container { border: 1px solid #ddd; padding: 20px; border-radius: 8px; background-color: #f9f9f9; }
    .variacao-item { display: grid; grid-template-columns: 2fr 1fr 50px; gap: 10px; margin-bottom: 10px; align-items: center; }
    .variacao-item input, .variacao-item select { padding: 8px; border: 1px solid #ccc; border-radius: 4px; }
    .btn-remover-variacao { background-color: #e74c3c; color: white; border: none; padding: 8px; border-radius: 4px; cursor: pointer; line-height: 1; }
    .btn-adicionar-variacao { background-color: #3498db; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-top: 15px; }
    
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
    .form-group input[type="text"],
    .form-group input[type="number"],
    .form-group input[type="file"],
    .form-group textarea,
    .form-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 5px;
        box-sizing: border-box;
    }
    .form-group textarea { min-height: 120px; resize: vertical; }
    .image-preview img {
        height: 80px; width: 80px;
        object-fit: cover;
        border: 2px solid #ddd;
        border-radius: 5px;
        margin-right: 10px;
    }
    .alert-success { background-color: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .alert-error { background-color: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
    .btn-submit { background-color: #27ae60; color: white; padding: 12px 25px; border: none; border-radius: 5px; cursor: pointer; font-size: 1em; }
    .btn-submit:hover { background-color: #219150; }
    .btn-cancelar { padding: 12px 25px; text-decoration: none; color: #555; }

</style>

<h1>
    <i class='bx bxs-t-shirt'></i>
    <?php echo $is_editing ? 'Editar Produto #' . $produto['id'] : 'Adicionar Novo Produto'; ?>
</h1>

<?php if (isset($mensagem_status)): ?>
    <div class="<?php echo (strpos($mensagem_status, 'Erro') !== false) ? 'alert-error' : 'alert-success'; ?>">
        <?php echo $mensagem_status; ?>
    </div>
<?php endif; ?>

<form action="<?php echo BASE_URL; ?>/admin/produtos/salvar" method="POST" enctype="multipart/form-data">
    
    <?php if ($is_editing): ?>
        <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
    <?php endif; ?>

    <div class="form-group">
        <label for="nome">Nome do Produto</label>
        <input type="text" id="nome" name="nome" value="<?php echo htmlspecialchars($produto['nome']); ?>" required>
    </div>

    <div class="form-group">
        <label for="descricao">Descrição</label>
        <textarea id="descricao" name="descricao" required><?php echo htmlspecialchars($produto['descricao']); ?></textarea>
    </div>

    <div style="display: flex; gap: 20px;">
        <div class="form-group" style="flex: 1;">
            <label for="preco">Preço (R$)</label>
            <input type="number" id="preco" name="preco" step="0.01" value="<?php echo htmlspecialchars($produto['preco']); ?>" required>
        </div>
        <div class="form-group" style="flex: 1;">
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

    <div class="form-group variacoes-container">
        <h3 style="margin-top: 0;">Gerenciar Variações (Tamanho/Estoque)</h3>
        
        <?php if ($is_editing): ?>
            <div id="variacoes-list">
                <div class="variacao-item" style="font-weight: 700;">
                    <span>Tamanho</span>
                    <span>Estoque</span>
                    <span></span>
                </div>

                <?php foreach ($variacoes_display as $variacao): ?>
                    <div class="variacao-item">
                        <select name="variacao_tamanho[]" required>
                            <option value="">Selecione</option>
                            <?php foreach ($tamanhos_opcoes as $t): ?>
                                <option value="<?php echo $t; ?>" <?php echo ($variacao['tamanho'] === $t) ? 'selected' : ''; ?>>
                                    <?php echo $t; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="number" name="variacao_estoque[]" min="0" value="<?php echo htmlspecialchars($variacao['estoque']); ?>" required>
                        <button type="button" class="btn-remover-variacao" onclick="this.parentNode.remove()">
                            <i class='bx bx-x'></i>
                        </button>
                    </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="btn-adicionar-variacao" onclick="adicionarVariacao()">
                <i class='bx bx-plus'></i> Adicionar Variação
            </button>
        <?php else: ?>
            <p class="alert-error" style="background-color: #fef3cd; color: #664d03;">
                Para gerenciar Variações (Tamanho/Estoque), primeiro salve o produto.
            </p>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label>Imagem Principal Atual</label>
        <div class="image-preview">
            <?php if ($produto['imagem_url']): ?>
                <img src="<?php echo BASE_URL; ?>/_ADM/<?php echo htmlspecialchars($produto['imagem_url']); ?>" alt="Imagem Principal">
                <input type="hidden" name="imagem_atual" value="<?php echo htmlspecialchars($produto['imagem_url']); ?>">
            <?php else: ?>
                <p>Nenhuma imagem principal cadastrada.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="form-group">
        <label for="imagem_principal">Upload Imagem Principal (Substituir)</label>
        <input type="file" id="imagem_principal" name="imagem_principal" accept="image/*">
    </div>

    <div style="display: flex; justify-content: space-between; margin-top: 30px;">
        <button type="submit" class="btn-submit">
            <i class='bx bxs-save'></i> <?php echo $is_editing ? 'Salvar Alterações' : 'Criar Produto'; ?>
        </button>
        <a href="<?php echo BASE_URL; ?>/admin/produtos" class="btn-cancelar">Cancelar e Voltar</a>
    </div>
</form>

<script>
    const variacoesList = document.getElementById('variacoes-list');
    const tamanhos = <?php echo json_encode($tamanhos_opcoes); ?>;

    function getVariacaoTemplate() {
        let selectOptions = '<option value="">Selecione</option>';
        tamanhos.forEach(t => {
            selectOptions += `<option value="${t}">${t}</option>`;
        });

        return `
            <div class="variacao-item">
                <select name="variacao_tamanho[]" required>
                    ${selectOptions}
                </select>
                <input type="number" name="variacao_estoque[]" min="0" value="0" required>
                <button type="button" class="btn-remover-variacao" onclick="this.parentNode.remove()">
                    <i class='bx bx-x'></i>
                </button>
            </div>
        `;
    }

    function adicionarVariacao() {
        if (variacoesList) {
            variacoesList.insertAdjacentHTML('beforeend', getVariacaoTemplate());
        }
    }
</script>