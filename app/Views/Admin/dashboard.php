<?php

$labels_status = [];
$data_status = [];
foreach ($grafico_status as $item) {
    $labels_status[] = $item['status'];
    $data_status[] = $item['total'];
}

$labels_vendas = [];
$data_vendas = [];
foreach ($grafico_vendas as $item) {
    $dataObj = DateTime::createFromFormat('Y-m', $item['mes']);
    $labels_vendas[] = $dataObj ? $dataObj->format('m/Y') : $item['mes'];
    $data_vendas[] = $item['total'];
}

$labels_clientes = [];
$data_clientes = [];
foreach ($grafico_clientes as $item) {
    $dataObj = DateTime::createFromFormat('Y-m', $item['mes']);
    $labels_clientes[] = $dataObj ? $dataObj->format('m/Y') : $item['mes'];
    $data_clientes[] = $item['total'];
}

$labels_estoque = [];
$data_estoque = [];
foreach ($grafico_estoque as $item) {
    $labels_estoque[] = $item['nome'];
    $data_estoque[] = $item['total_estoque'];
}
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

    .texto_bem{
        font-size: 23px;
        margin: 20px 0px;
    }

    .dashboard-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
    }

    .card {
        background: var(--cor-branco);
        padding: 25px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        display: flex;
        align-items: center;
        gap: 20px;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .card.clickable {
        cursor: pointer;
    }

    .card.clickable:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .card.active {
        box-shadow: 0 8px 25px rgba(52, 152, 219, 0.3);
        border: 2px solid #3498db;
    }

    .card .icon {
        font-size: 3em;
        padding: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .card .info {
        flex: 1;
        min-width: 0;
    }

    .card .info h3 {
        margin-top: 0;
        margin-bottom: 5px;
        color: #555;
        font-size: 1.1em;
        text-transform: uppercase;
        font-weight: 600;
    }

    .card .info .valor {
        font-size: 2.2em;
        font-weight: 700;
        color: var(--cor-secundaria);
        line-height: 1.1;
    }

    .card .info .subtexto {
        font-size: 0.9em;
        color: #777;
        margin-top: 5px;
    }

    .card.faturamento .icon {
        color: #27ae60;
        background: #eaf8f1;
    }

    .card.pedidos .icon {
        color: #e67e22;
        background: #fdf3e9;
    }

    .card.clientes .icon {
        color: #3498db;
        background: #ebf5fb;
    }

    .card.estoque .icon {
        color: #8e44ad;
        background: #f4ecf7;
    }

    .chart-section {
        margin-top: 30px;
        background: white;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.07);
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transition: max-height 0.5s ease, opacity 0.5s ease, margin-top 0.5s ease;
    }

    .chart-section.active {
        max-height: 600px;
        opacity: 1;
    }

    .chart-section .chart-header {
        padding: 20px 25px;
        border-bottom: 2px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8f9fa;
    }

    .chart-section .chart-header h3 {
        margin: 0;
        color: #333;
        font-size: 1.5em;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-section .chart-header .close-btn {
        background: #e74c3c;
        color: white;
        border: none;
        padding: 8px 15px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 0.9em;
        transition: background 0.3s;
    }

    .chart-section .chart-header .close-btn:hover {
        background: #c0392b;
    }

    .chart-section .chart-wrapper {
        padding: 30px;
        height: 450px;
    }

    .chart-section canvas {
        width: 100% !important;
        height: 100% !important;
    }

    @media (max-width: 1200px) {
        .dashboard-cards {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .dashboard-cards {
            grid-template-columns: 1fr;
        }
        
        .card {
            padding: 20px;
        }
        
        .card .icon {
            font-size: 2.5em;
            padding: 15px;
        }
        
        .card .info h3 {
            font-size: 1em;
        }
        
        .card .info .valor {
            font-size: 1.8em;
        }

        .chart-section.active {
            max-height: 500px;
        }

        .chart-section .chart-wrapper {
            padding: 20px;
            height: 350px;
        }

        .chart-section .chart-header h3 {
            font-size: 1.2em;
        }
    }

    @media (max-width: 480px) {
        .card {
            flex-direction: column;
            text-align: center;
            padding: 15px;
        }

        .card .icon {
            font-size: 2em;
            padding: 12px;
        }

        .card .info .valor {
            font-size: 1.6em;
        }

        .card .info h3 {
            font-size: 0.9em;
        }

        .chart-section.active {
            max-height: 450px;
        }

        .chart-section .chart-wrapper {
            padding: 15px;
            height: 300px;
        }

        .chart-section .chart-header {
            padding: 15px;
            flex-direction: column;
            gap: 10px;
            text-align: center;
        }

        .chart-section .chart-header h3 {
            font-size: 1em;
        }

        .chart-section .chart-header .close-btn {
            width: 100%;
        }
    }
</style>

<h1><i class='bx bxs-dashboard'></i> Dashboard</h1>
<p class="texto_bem">Bem-vindo ao painel de administração da Street Style.</p>

<div class="dashboard-cards">
    <div class="card faturamento clickable" onclick="toggleChart('vendas')">
        <div class="icon"><i class='bx bx-dollar-circle'></i></div>
        <div class="info">
            <h3>Faturamento Total</h3>
            <span class="valor">R$ <?php echo number_format($faturamento_total, 2, ',', '.'); ?></span>
            <p class="subtexto">(Pedidos confirmados)</p>
        </div>
    </div>
    <div class="card pedidos clickable" onclick="toggleChart('status')">
        <div class="icon"><i class='bx bx-loader-circle'></i></div>
        <div class="info">
            <h3>Pedidos Pendentes</h3>
            <span class="valor"><?php echo $total_pedidos_pendentes; ?></span>
            <p class="subtexto">Aguardando ação</p>
        </div>
    </div>
    <div class="card clientes clickable" onclick="toggleChart('clientes')">
        <div class="icon"><i class='bx bxs-group'></i></div>
        <div class="info">
            <h3>Total de Clientes</h3>
            <span class="valor"><?php echo $total_clientes; ?></span>
            <p class="subtexto">Cadastrados</p>
        </div>
    </div>
    <div class="card estoque clickable" onclick="toggleChart('estoque')">
        <div class="icon"><i class='bx bxs-archive-in'></i></div>
        <div class="info">
            <h3>Itens em Estoque</h3>
            <span class="valor"><?php echo $total_estoque; ?></span>
            <p class="subtexto">Variações totais</p>
        </div>
    </div>
</div>

<div id="chartSectionVendas" class="chart-section">
    <div class="chart-header">
        <h3><i class='bx bx-bar-chart-alt-2'></i> Vendas (Últimos 6 Meses)</h3>
        <button class="close-btn" onclick="closeChart('vendas')">
            <i class='bx bx-x'></i> Fechar
        </button>
    </div>
    <div class="chart-wrapper">
        <canvas id="chartVendas"></canvas>
    </div>
</div>

<div id="chartSectionStatus" class="chart-section">
    <div class="chart-header">
        <h3><i class='bx bx-pie-chart-alt-2'></i> Pedidos por Status</h3>
        <button class="close-btn" onclick="closeChart('status')">
            <i class='bx bx-x'></i> Fechar
        </button>
    </div>
    <div class="chart-wrapper">
        <canvas id="chartStatus"></canvas>
    </div>
</div>

<div id="chartSectionClientes" class="chart-section">
    <div class="chart-header">
        <h3><i class='bx bx-line-chart'></i> Novos Clientes (Últimos 6 Meses)</h3>
        <button class="close-btn" onclick="closeChart('clientes')">
            <i class='bx bx-x'></i> Fechar
        </button>
    </div>
    <div class="chart-wrapper">
        <canvas id="chartClientes"></canvas>
    </div>
</div>

<div id="chartSectionEstoque" class="chart-section">
    <div class="chart-header">
        <h3><i class='bx bx-layer'></i> Top 5 Itens em Estoque</h3>
        <button class="close-btn" onclick="closeChart('estoque')">
            <i class='bx bx-x'></i> Fechar
        </button>
        
    </div>
    <div class="chart-wrapper">
        <canvas id="chartEstoque"></canvas>
    </div>
</div>

<script>
    let chartVendas = null;
    let chartStatus = null;
    let chartClientes = null;
    let chartEstoque = null;
    let currentOpenChart = null;

    function toggleChart(chartType) {
        let section;
        let card;

        switch(chartType) {
            case 'vendas':
                section = document.getElementById('chartSectionVendas');
                card = document.querySelector('.card.faturamento');
                break;
            case 'status':
                section = document.getElementById('chartSectionStatus');
                card = document.querySelector('.card.pedidos');
                break;
            case 'clientes':
                section = document.getElementById('chartSectionClientes');
                card = document.querySelector('.card.clientes');
                break;
            case 'estoque':
                section = document.getElementById('chartSectionEstoque');
                card = document.querySelector('.card.estoque');
                break;
            default:
                return; 
        }

        if (currentOpenChart === chartType) {
            closeChart(chartType);
            return;
        }

        if (currentOpenChart) {
            closeChart(currentOpenChart);
        }

        document.querySelectorAll('.card').forEach(c => c.classList.remove('active'));
        
        card.classList.add('active');

        section.classList.add('active');
        currentOpenChart = chartType;

        setTimeout(() => {
            section.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }, 100);

        if (chartType === 'vendas') {
            if (!chartVendas) {
                createChartVendas();
            } else {
                chartVendas.resize();
            }
        } else if (chartType === 'status') {
            if (!chartStatus) {
                createChartStatus();
            } else {
                chartStatus.resize();
            }
        } else if (chartType === 'clientes') {
            if (!chartClientes) {
                createChartClientes();
            } else {
                chartClientes.resize();
            }
        } else if (chartType === 'estoque') {
            if (!chartEstoque) {
                createChartEstoque();
            } else {
                chartEstoque.resize();
            }
        }
    }

    function closeChart(chartType) {
        let section;
        let card;

        switch(chartType) {
            case 'vendas':
                section = document.getElementById('chartSectionVendas');
                card = document.querySelector('.card.faturamento');
                break;
            case 'status':
                section = document.getElementById('chartSectionStatus');
                card = document.querySelector('.card.pedidos');
                break;
            case 'clientes':
                section = document.getElementById('chartSectionClientes');
                card = document.querySelector('.card.clientes');
                break;
            case 'estoque':
                section = document.getElementById('chartSectionEstoque');
                card = document.querySelector('.card.estoque');
                break;
            default:
                return; 
        }

        section.classList.remove('active');
        card.classList.remove('active');
        currentOpenChart = null;
    }

    function createChartVendas() {
        const ctx = document.getElementById('chartVendas').getContext('2d');
        chartVendas = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_vendas); ?>,
                datasets: [{
                    label: 'Faturamento (R$)',
                    data: <?php echo json_encode($data_vendas); ?>,
                    backgroundColor: '#3498db',
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'R$ ' + value.toLocaleString('pt-BR');
                            }
                        },
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'R$ ' + context.parsed.y.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }
                    }
                }
            }
        });
    }

    function createChartStatus() {
        const ctx = document.getElementById('chartStatus').getContext('2d');
        chartStatus = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($labels_status); ?>,
                datasets: [{
                    data: <?php echo json_encode($data_status); ?>,
                    backgroundColor: [
                        '#f1c40f',
                        '#2ecc71',
                        '#3498db',
                        '#e74c3c',
                        '#9b59b6',
                        '#95a5a6'
                    ],
                    borderWidth: 3,
                    borderColor: '#fff',
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 14
                            },
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }

    function createChartClientes() {
        const ctx = document.getElementById('chartClientes').getContext('2d');
        chartClientes = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($labels_clientes); ?>,
                datasets: [{
                    label: 'Novos Clientes',
                    data: <?php echo json_encode($data_clientes); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.5)',
                    borderColor: '#3498db',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                return context.parsed.y.toLocaleString('pt-BR') + ' Clientes';
                            }
                        }
                    }
                }
            }
        });
    }

    function createChartEstoque() {
        const ctx = document.getElementById('chartEstoque').getContext('2d');
        chartEstoque = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($labels_estoque); ?>,
                datasets: [{
                    label: 'Quantidade em Estoque',
                    data: <?php echo json_encode($data_estoque); ?>,
                    backgroundColor: [
                        '#2ecc71', '#3498db', '#f1c40f', '#e67e22', '#8e44ad'
                    ],
                    borderRadius: 5,
                    borderWidth: 0
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { 
                        beginAtZero: true,
                        grid: {
                            color: '#f0f0f0'
                        },
                        ticks: {
                             callback: function(value) {
                                return value.toLocaleString('pt-BR');
                            }
                        }
                    },
                    y: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                         callbacks: {
                            label: function(context) {
                                return context.parsed.x.toLocaleString('pt-BR') + ' Unidades';
                            }
                        }
                    }
                }
            }
        });
    }

    window.addEventListener('resize', function() {
        if (chartVendas) chartVendas.resize();
        if (chartStatus) chartStatus.resize();
        if (chartClientes) chartClientes.resize();
        if (chartEstoque) chartEstoque.resize();
    });
</script>