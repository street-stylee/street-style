<?php

function gerarEstrelasAdmin($nota) {
    $html = '<div style="color: #f39c12; font-size: 1.1em;">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= ($i <= $nota) ? '★' : '☆';
    }
    $html .= '</div>';
    return $html;
}
?>

<style>
    .admin-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; }
    .admin-table th, .admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    .admin-table th { background-color: #f9f9f9; font-size: 0.9em; text-transform: uppercase; color: #555; }
    .admin-table .acoes a { text-decoration: none; margin-right: 10px; font-weight: bold; }
    .acoes .excluir { color: #e74c3c; }
    .alert-success { padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    .alert-error { padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px; }
    
    .comentario-col { 
        max-width: 300px; 
        overflow: visible;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .comentario-col p { margin: 0; line-height: 1.5; font-size: 0.95em; }
    .comentario-col strong { color: #333; }
    
    .comentario-wrapper {
        flex: 1;
        min-width: 0;
    }
    
    .comentario-texto {
        display: block;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        font-size: 0.9em;
    }
    
    .btn-ver-mais {
        padding: 4px 8px; text-decoration: none; border-radius: 3px;
        font-size: 0.8rem; background-color: #3498db; color: #fff;
        cursor: pointer; border: none; font-weight: 500;
        display: none;
        flex-shrink: 0;
        white-space: nowrap;
    }
    .btn-ver-mais:hover { background-color: #2980b9; }
    
    /* Desktop grande */
    @media (min-width: 1201px) {
        .comentario-col { max-width: 300px; }
        .comentario-texto { font-size: 0.9em; }
    }
    
    /* 1200px até 1000px */
    @media (max-width: 1200px) {
        .comentario-col { max-width: 150px; }
        .comentario-texto { font-size: 0.75em; }
        .btn-ver-mais { padding: 3px 6px; font-size: 0.7rem; display: inline-block; }
    }
    
    /* 999px até 900px */
    @media (max-width: 999px) {
        .comentario-col { max-width: 110px; }
        .comentario-texto { font-size: 0.7em; }
        .btn-ver-mais { padding: 3px 6px; font-size: 0.65rem; display: inline-block; }
    }
    
    /* 899px até 800px */
    @media (max-width: 899px) {
        .comentario-col { max-width: 90px; }
        .comentario-texto { font-size: 0.65em; }
        .btn-ver-mais { padding: 3px 5px; font-size: 0.6rem; display: inline-block; }
    }
    
    /* 799px até 768px */
    @media (max-width: 799px) {
        .admin-table { font-size: 0.8em; }
        .admin-table th, .admin-table td { padding: 8px 6px; }
        .admin-table th { font-size: 0.7em; }
        .comentario-col { max-width: 70px; }
        .comentario-texto { font-size: 0.6em; }
        .btn-ver-mais { padding: 3px 5px; font-size: 0.55rem; display: inline-block; }
        .admin-table .acoes a { margin-right: 3px; font-size: 0.85em; }
    }
    
    @media (max-width: 600px) {
        .comentario-col { max-width: 60px; }
        .comentario-texto { font-size: 0.55em; }
        .btn-ver-mais { padding: 3px 4px; font-size: 0.5rem; display: inline-block; }
    }
    
    @media (max-width: 480px) {
        .admin-table th, .admin-table td { padding: 6px 4px; font-size: 0.7em; }
        .admin-table th { font-size: 0.6em; }
        .comentario-col { max-width: 50px; }
        .comentario-texto { font-size: 0.5em; }
        .btn-ver-mais { display: inline-block; padding: 2px 4px; font-size: 0.45rem; }
    }
    
    @media (max-width: 350px) {
        .comentario-col { max-width: 40px; }
        .comentario-texto { font-size: 0.45em; }
        .btn-ver-mais { display: inline-block; padding: 2px 3px; font-size: 0.4rem; }
    }
    
    @media (max-width: 320px) {
        .comentario-col { max-width: 35px; }
        .comentario-texto { font-size: 0.4em; }
        .btn-ver-mais { display: inline-block; padding: 2px 3px; font-size: 0.35rem; }
    }
</style>

<h1><i class='bx bxs-star-half'></i> <?php echo htmlspecialchars($titulo_pagina); ?></h1>

<?php if (isset($mensagem)): ?>
    <div class="alert-<?php echo $mensagem['tipo']; ?>">
        <?php echo htmlspecialchars($mensagem['texto']); ?>
    </div>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>Data</th>
            <th>Produto</th>
            <th>Cliente</th>
            <th>Nota</th>
            <th>Comentário</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($avaliacoes)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">Nenhuma avaliação de cliente encontrada.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($avaliacoes as $avaliacao): 
                $comentario_completo = (!empty($avaliacao['titulo']) ? $avaliacao['titulo'] . ' - ' : '') . $avaliacao['comentario'];
            ?>
                <tr>
                    <td style="white-space: nowrap;"><?php echo date('d/m/Y H:i', strtotime($avaliacao['data_avaliacao'])); ?></td>
                    <td>
                        <a href="<?php echo BASE_URL; ?>/produto/detalhe/<?php echo $avaliacao['produto_id']; ?>" target="_blank" style="text-decoration: none; color: #3498db;">
                            <?php echo htmlspecialchars($avaliacao['nome_produto']); ?>
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($avaliacao['nome_usuario']); ?></td>
                    <td><?php echo gerarEstrelasAdmin($avaliacao['nota']); ?></td>
                    <td class="comentario-col">
                        <div class="comentario-wrapper">
                            <span class="comentario-texto" title="<?php echo htmlspecialchars($comentario_completo); ?>" data-full="<?php echo htmlspecialchars($comentario_completo); ?>"><?php echo htmlspecialchars($comentario_completo); ?></span>
                        </div>
                        <button type="button" class="btn-ver-mais" onclick="mostrarComentario('<?php echo htmlspecialchars(addslashes($comentario_completo)); ?>');" title="Ver comentário completo">
                            <i class='bx bx-plus'></i>
                        </button>
                    </td>
                    <td class="acoes" style="white-space: nowrap;">
                        <a href="<?php echo BASE_URL; ?>/admin/avaliacoes/excluir/<?php echo $avaliacao['id']; ?>" class="excluir" onclick="return confirm('Tem certeza?');" title="Deletar avaliação">
                            <i class='bx bx-trash'></i> Del
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<script>
function mostrarComentario(comentario) {
    alert(comentario);
}
function checkButtonVisibility() {
    document.querySelectorAll('.comentario-texto').forEach((el) => {
        const btn = el.closest('tr').querySelector('.btn-ver-mais');
        if (!btn) return;
                requestAnimationFrame(() => {
            const textWidth = el.scrollWidth;
            const availableWidth = el.parentElement.offsetWidth;
            
            if (textWidth > availableWidth) {
                btn.style.display = 'inline-block';
            } else {
                btn.style.display = 'none';
            }
        });
    });
}

setTimeout(checkButtonVisibility, 150);
window.addEventListener('load', () => {
    setTimeout(checkButtonVisibility, 100);
});

let resizeTimeout;
window.addEventListener('resize', () => {
    clearTimeout(resizeTimeout);
    resizeTimeout = setTimeout(checkButtonVisibility, 100);
});

const observer = new MutationObserver(() => {
    setTimeout(checkButtonVisibility, 50);
});
observer.observe(document.body, { childList: true, subtree: true });
</script>