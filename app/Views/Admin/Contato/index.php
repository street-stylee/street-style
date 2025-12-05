<?php

?>

<style>
    .admin-table { width: 100%; border-collapse: collapse; background: white; box-shadow: 0 4px 10px rgba(0,0,0,0.05); border-radius: 8px; overflow: hidden; }
    .admin-table th, .admin-table td { padding: 15px; text-align: left; border-bottom: 1px solid #f0f0f0; }
    .admin-table th { background-color: #f9f9f9; font-size: 0.9em; text-transform: uppercase; color: #555; }
    .admin-table .acoes a { text-decoration: none; margin-right: 10px; font-weight: bold; }
    .acoes .excluir { color: #e74c3c; }
    .alert-success { padding: 10px; background-color: #d4edda; color: #155724; border-radius: 5px; margin-bottom: 20px; }
    .alert-error { padding: 10px; background-color: #f8d7da; color: #721c24; border-radius: 5px; margin-bottom: 20px; }
    .mensagem-conteudo { max-width: 350px; white-space: pre-wrap; word-wrap: break-word; font-size: 0.95em; line-height: 1.5; }
    td small { color: #777; }
    
    @media (max-width: 768px) {
        .admin-table { font-size: 0.85em; }
        .admin-table th, .admin-table td { padding: 10px 8px; }
        .admin-table th { font-size: 0.75em; }
        .mensagem-conteudo { max-width: 200px; }
    }
    
    @media (max-width: 480px) {
        .admin-table th, .admin-table td { padding: 8px 5px; font-size: 0.75em; }
        .admin-table th { font-size: 0.65em; }
        .mensagem-conteudo { max-width: 120px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    }
</style>

<h1><i class='bx bxs-message-dots'></i> <?php echo htmlspecialchars($titulo_pagina); ?></h1>

<?php if (isset($mensagem)): ?>
    <div class="alert-<?php echo $mensagem['tipo']; ?>">
        <?php echo htmlspecialchars($mensagem['texto']); ?>
    </div>
<?php endif; ?>

<table class="admin-table">
    <thead>
        <tr>
            <th>Data</th>
            <th>De</th>
            <th>Contato</th>
            <th>Mensagem</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($mensagens)): ?>
            <tr>
                <td colspan="6" style="text-align: center; padding: 20px;">Nenhuma mensagem de contato recebida.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($mensagens as $msg): ?>
                <tr style="<?php echo ($msg['status'] == 'nao_lido') ? 'font-weight: bold;' : ''; ?>">
                    <td><?php echo date('d/m/Y H:i', strtotime($msg['data_envio'])); ?></td>
                    <td><?php echo htmlspecialchars($msg['nome']); ?></td>
                    <td>
                        <?php echo htmlspecialchars($msg['email']); ?><br>
                        <small><?php echo htmlspecialchars($msg['telefone'] ?? 'N/A'); ?></small>
                    </td>
                    <td class="mensagem-conteudo"><?php echo htmlspecialchars($msg['mensagem']); ?></td>
                    <td><?php echo htmlspecialchars($msg['status']); ?></td>
                    <td class="acoes">
                        <a href="<?php echo BASE_URL; ?>/admin/contato/excluir/<?php echo $msg['id']; ?>" class="excluir" onclick="return confirm('Tem certeza?');">
                            Excluir
                        </a>
                        </td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>