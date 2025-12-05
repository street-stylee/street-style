<?php
$log = $log ?? null;
$log_id = $log['id'] ?? 'N/A';
$context_decoded = $log['context_decoded'] ?? null;
?>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&family=Fira+Code:wght@400;500;600&display=swap');
    
    .details-container {
        font-family: 'Inter', sans-serif;
        min-height: 100vh;
        padding: 2rem 1rem;
    }
    
    .details-wrapper {
        max-width: 1200px;
        margin: 0 auto;
    }
    
    .details-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.5);
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .details-title {
        font-size: 2.5rem;
        font-weight: 900;
        color: var(--cor-secundaria);
        display: flex;
        align-items: center;
        gap: 1rem;
    }
    
    .back-btn {
        padding: 0.875rem 1.75rem;
        background: white;
        color: var(--cor-secundaria);
        border: 2px solid #7dd3fc;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.875rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.2);
    }
    
    .back-btn:hover {
        background: linear-gradient(135deg, #0ea5e9 0%, #06b6d4 100%);
        color: white;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
    }
    
    .details-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }
    
    .section {
        padding: 2.5rem;
        border-bottom: 1px solid #e5e7eb;
    }
    
    .section:last-child {
        border-bottom: none;
    }
    
    .section-header {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 2rem;
        padding-bottom: 1rem;
        border-bottom: 2px solid #cbd5e1;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1.5rem;
    }
    
    .info-box {
        background: linear-gradient(135deg, #f9fafb 0%, #ffffff 100%);
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .info-box::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #0ea5e9 0%, #06b6d4 100%);
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }
    
    .info-box:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(14, 165, 233, 0.2);
        border-color: #7dd3fc;
    }
    
    .info-box:hover::before {
        transform: scaleX(1);
    }
    
    .info-label {
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        color: #9ca3af;
        letter-spacing: 0.05em;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1f2937;
    }
    
    .level-badge-lg {
        display: inline-flex;
        padding: 0.5rem 1.25rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    .level-critical {
        background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        color: white;
        animation: pulse 2s infinite;
    }
    
    .level-error {
        background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
        color: #991b1b;
    }
    
    .level-warn {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }
    
    .level-info {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.85; transform: scale(1.02); }
    }
    
    .message-box {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 2px solid #7dd3fc;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: inset 0 2px 8px rgba(125, 211, 252, 0.2);
    }
    
    .message-text {
        color: #1e3a8a;
        font-family: 'Fira Code', monospace;
        font-size: 0.95rem;
        line-height: 1.8;
        white-space: pre-wrap;
        word-break: break-word;
    }
    
    .context-box {
        background: #0f172a;
        border: 2px solid #1e293b;
        border-radius: 16px;
        padding: 2rem;
        overflow-x: auto;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.5);
        position: relative;
    }
    
    .context-box::before {
        content: 'JSON';
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: #1e293b;
        color: #10b981;
        padding: 0.25rem 0.75rem;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 600;
        font-family: 'Fira Code', monospace;
    }
    
    .context-code {
        color: #10b981;
        font-family: 'Fira Code', monospace;
        font-size: 0.875rem;
        line-height: 1.8;
        max-height: 500px;
        overflow-y: auto;
    }
    
    .context-code::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    .context-code::-webkit-scrollbar-track {
        background: #1e293b;
        border-radius: 4px;
    }
    
    .context-code::-webkit-scrollbar-thumb {
        background: #475569;
        border-radius: 4px;
    }
    
    .context-code::-webkit-scrollbar-thumb:hover {
        background: #64748b;
    }
    
    .empty-context {
        background: #f9fafb;
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        padding: 2rem;
        text-align: center;
        color: #6b7280;
        font-style: italic;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    
    .error-state {
        padding: 4rem 2rem;
        text-align: center;
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border: 2px solid #fca5a5;
        border-radius: 20px;
        margin: 2rem 0;
    }
    
    .error-icon {
        font-size: 4rem;
        color: #dc2626;
        margin-bottom: 1.5rem;
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }
    
    .error-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #991b1b;
        margin-bottom: 0.5rem;
    }
    
    .error-subtitle {
        font-size: 1rem;
        color: #7f1d1d;
    }
    
    @media (max-width: 768px) {
        .details-container {
            padding: 1rem 0.5rem;
        }
        
        .details-header {
            flex-direction: column;
            align-items: flex-start;
            padding: 1.5rem;
        }
        
        .details-title {
            font-size: 1.75rem;
        }
        
        .section {
            padding: 1.5rem;
        }
        
        .summary-grid {
            grid-template-columns: 1fr;
        }
        
        .info-box {
            padding: 1rem;
        }
    }
</style>

<div class="details-container">
    <div class="details-wrapper">
        
        <div class="details-header">
            <h1 class="details-title">
                <i class="fa-solid fa-file-lines"></i>
                <?= $titulo_pagina ?>
            </h1>
            <a href="<?= BASE_URL ?>/admin/log" class="back-btn">
                <i class="fa-solid fa-arrow-left"></i>
                Voltar para a Lista
            </a>
        </div>

        <?php if ($log) : ?>
            <div class="details-card">
                
                <div class="section">
                    <h2 class="section-header">
                        <i class="fa-solid fa-circle-info"></i>
                        Sumário do Evento
                    </h2>
                    <div class="summary-grid">
                        
                        <div class="info-box">
                            <div class="info-label">ID do Log</div>
                            <div class="info-value">#<?= htmlspecialchars($log_id) ?></div>
                        </div>
                        
                        <div class="info-box">
                            <div class="info-label">Data/Hora</div>
                            <div class="info-value" style="font-size: 1rem;"><?= htmlspecialchars($log['created_at'] ?? 'N/A') ?></div>
                        </div>

                        <div class="info-box">
                            <div class="info-label">Nível</div>
                            <div>
                                <?php
                                $level = $log['level'] ?? 'N/A';
                                $level_class = 'level-info';
                                if ($level === 'CRITICAL') {
                                    $level_class = 'level-critical';
                                } elseif ($level === 'ERROR') {
                                    $level_class = 'level-error';
                                } elseif ($level === 'WARN') {
                                    $level_class = 'level-warn';
                                }
                                ?>
                                <span class="level-badge-lg <?= $level_class ?>">
                                    <?= htmlspecialchars($level) ?>
                                </span>
                            </div>
                        </div>
                        
                        <div class="info-box">
                            <div class="info-label">ID do Usuário</div>
                            <div class="info-value"><?= htmlspecialchars($log['user_id'] ?? 'Sistema') ?></div>
                        </div>

                    </div>
                </div>

                <div class="section">
                    <h2 class="section-header">
                        <i class="fa-solid fa-comment-dots"></i>
                        Mensagem do Evento
                    </h2>
                    <div class="message-box">
                        <p class="message-text"><?= htmlspecialchars($log['message'] ?? 'Nenhuma mensagem detalhada disponível.') ?></p>
                    </div>
                </div>
                
                <div class="section">
                    <h2 class="section-header">
                        <i class="fa-solid fa-code"></i>
                        Contexto (Dados Adicionais JSON)
                    </h2>

                    <?php if ($context_decoded && is_array($context_decoded) && count($context_decoded) > 0) : ?>
                        <div class="context-box">
                            <pre class="context-code"><code><?= htmlspecialchars(json_encode($context_decoded, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></code></pre>
                        </div>
                    <?php else : ?>
                        <div class="empty-context">
                            <i class="fa-solid fa-info-circle" style="color: #a5b4fc; font-size: 1.5rem;"></i>
                            <span>Nenhum contexto (dados adicionais) fornecido para este log</span>
                        </div>
                    <?php endif; ?>
                </div>

            </div>
        <?php else : ?>
            <div class="error-state">
                <div class="error-icon">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </div>
                <p class="error-title">Log Não Encontrado (Erro 404)</p>
                <p class="error-subtitle">O log com ID <?= htmlspecialchars($log_id) ?> não existe, foi removido ou o ID é inválido</p>
            </div>
        <?php endif; ?>
    </div>
</div>