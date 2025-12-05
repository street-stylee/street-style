<!-- app/Views/Admin/Logs/index.php -->

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap');
    
    .logs-container {
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
        padding: 2rem 1rem;
    }
    
    .logs-wrapper {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .page-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    .page-title {
        font-size: 2.5rem;
        font-weight: 900;
        background: var(--cor-secundaria);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .page-subtitle {
        color: #6b7280;
        font-size: 1rem;
        font-weight: 400;
    }
    
    .filter-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    .filter-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #4c1d95;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #6b21a8;
        margin-bottom: 0.5rem;
        letter-spacing: 0.05em;
    }
    
    .form-input, .form-select {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 2px solid #e9d5ff;
        border-radius: 12px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: white;
        color: #1f2937;
    }
    
    .form-input:focus, .form-select:focus {
        outline: none;
        border-color: #var(--cor-secundaria);
        box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.1);
        transform: translateY(-2px);
    }
    
    .filter-actions {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    .btn {
        padding: 0.875rem 1.75rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        border: none;
        text-decoration: none;
    }
    
    .btn-primary {
        background: var(--cor-secundaria);
        color: white;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
    }
    
    .btn-secondary {
        background: white;
        color: #6b7280;
        border: 2px solid #e5e7eb;
    }
    
    .btn-secondary:hover {
        background: #f9fafb;
        border-color: #d1d5db;
        transform: translateY(-2px);
    }
    
    .logs-table-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    .logs-table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .logs-table thead {
        background: var(--cor-secundaria);
    }
    
    .logs-table th {
        padding: 1.25rem 1.5rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        color: white;
        letter-spacing: 0.05em;
    }
    
    .logs-table tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.2s ease;
    }
    
    .logs-table tbody tr:hover {
        background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, rgba(118, 75, 162, 0.05) 100%);
        transform: scale(1.01);
    }
    
    .logs-table td {
        padding: 1.25rem 1.5rem;
        font-size: 0.875rem;
        color: #374151;
    }
    
    .log-id {
        font-weight: 600;
        color: #1f2937;
    }
    
    .level-badge {
        display: inline-flex;
        padding: 0.375rem 0.875rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .level-critical {
        background: var(--cor-secundaria);
        color: white;
        animation: pulse 2s infinite;
    }
    
    .level-error {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .level-warn {
        background: #fef3c7;
        color: #92400e;
    }
    
    .level-info {
        background: #d1fae5;
        color: #065f46;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }
    
    .log-message {
        max-width: 400px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        color: #4b5563;
        cursor: help;
        transition: all 0.3s ease;
    }
    
    .log-message:hover {
        white-space: normal;
        overflow: visible;
        background: #fef3c7;
        padding: 0.5rem;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .user-link {
        font-weight: 600;
        color: var(--cor-secundaria);
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .user-link:hover {
        color: #6d28d9;
        text-decoration: underline;
    }
    
    .action-link {
        color: #var(--cor-secundaria);
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        transition: all 0.2s ease;
    }
    
    .action-link:hover {
        color: #6d28d9;
        transform: translateX(4px);
    }
    
    .table-footer {
        padding: 1.25rem 1.5rem;
        background: #f9fafb;
        border-top: 1px solid #e5e7eb;
        color: #6b7280;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
        background: white;
        border-radius: 20px;
    }
    
    .empty-icon {
        font-size: 4rem;
        color: #c4b5fd;
        margin-bottom: 1.5rem;
        animation: float 3s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }
    
    .empty-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .empty-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    @media (max-width: 768px) {
        .logs-container {
            padding: 1rem 0.5rem;
        }
        
        .page-header, .filter-card, .logs-table-card {
            border-radius: 12px;
            padding: 1.5rem;
        }
        
        .page-title {
            font-size: 1.75rem;
        }
        
        .filter-grid {
            grid-template-columns: 1fr;
        }
        
        .logs-table {
            font-size: 0.75rem;
        }
        
        .logs-table th, .logs-table td {
            padding: 0.75rem 0.5rem;
        }
    }
</style>

<div class="logs-container">
    <div class="logs-wrapper">
        
        <header class="page-header">
            <h1 class="page-title">
                <i class="fa-solid fa-list-check"></i>
                <?= $titulo_pagina ?>
            </h1>
            <p class="page-subtitle">Monitore e analise os eventos recentes do sistema e da atividade do usuário</p>
        </header>

        <form method="GET" action="<?= BASE_URL ?>/admin/log" class="filter-card">
            
            <h2 class="filter-title">
                <i class="fa-solid fa-sliders"></i>
                Opções de Filtragem
            </h2>

            <div class="filter-grid">
                <div class="form-group">
                    <label for="level" class="form-label">Nível</label>
                    <select id="level" name="level" class="form-select">
                        <option value="">-- Todos os Níveis --</option>
                        <?php 
                        $log_levels = $log_levels ?? ['INFO' => 'INFO', 'WARN' => 'WARN', 'ERROR' => 'ERROR', 'CRITICAL' => 'CRITICAL'];
                        foreach ($log_levels as $key => $display) : ?>
                            <option value="<?= $key ?>" <?= ($filtro_level === $key) ? 'selected' : '' ?>>
                                <?= $display ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="search" class="form-label">Buscar na Mensagem</label>
                    <input type="text" id="search" name="search" value="<?= htmlspecialchars($filtro_search) ?>" placeholder="Digite palavra-chave..." class="form-input">
                </div>

                <div class="form-group">
                    <label for="user_id" class="form-label">ID Usuário</label>
                    <input type="number" id="user_id" name="user_id" value="<?= htmlspecialchars($filtro_user_id) ?>" placeholder="ID" class="form-input">
                </div>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter"></i>
                    Aplicar Filtros
                </button>
                <a href="<?= BASE_URL ?>/admin/log" class="btn btn-secondary">
                    <i class="fa-solid fa-xmark"></i>
                    Limpar
                </a>
            </div>
        </form>
        
        <div class="logs-table-card">
            <?php if (!empty($logs)) : ?>
                <table class="logs-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nível</th>
                            <th>Mensagem</th>
                            <th>Usuário</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $log) : ?>
                            <tr>
                                <td class="log-id"><?= htmlspecialchars($log['id']) ?></td>
                                <td>
                                    <?php
                                    $level_class = 'level-info';
                                    if ($log['level'] === 'CRITICAL') {
                                        $level_class = 'level-critical';
                                    } elseif ($log['level'] === 'ERROR') {
                                        $level_class = 'level-error';
                                    } elseif ($log['level'] === 'WARN') {
                                        $level_class = 'level-warn';
                                    }
                                    ?>
                                    <span class="level-badge <?= $level_class ?>">
                                        <?= htmlspecialchars($log['level']) ?>
                                    </span>
                                </td>
                                <td class="log-message" title="<?= htmlspecialchars($log['message']) ?>">
                                    <?= htmlspecialchars($log['message']) ?>
                                </td>
                                <td>
                                    <?= $log['user_id'] ? '<a href="#" class="user-link">ID: ' . htmlspecialchars($log['user_id']) . '</a>' : '<span style="color: #9ca3af;">Sistema</span>' ?>
                                </td>
                                <td style="color: #6b7280;"><?= htmlspecialchars($log['created_at']) ?></td>
                                <td>
                                    <a href="<?= BASE_URL ?>/admin/log/details?id=<?= $log['id'] ?>" class="action-link">
                                        Ver Detalhes
                                        <i class="fa-solid fa-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="table-footer">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                    Exibindo os logs mais recentes (Máximo de 200 entradas)
                </div>
            <?php else : ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fa-solid fa-magnifying-glass-chart"></i>
                    </div>
                    <p class="empty-title">Nenhum log encontrado</p>
                    <p class="empty-subtitle">Tente limpar os filtros ou ajustar os parâmetros de busca</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>