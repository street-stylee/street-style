<?php
?>

<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/estilo_pg_produto.css">
<link rel="stylesheet" href="<?php echo BASE_URL; ?>/_ADM/css/estilo_checkout.css">

<div class="checkout-container">
    <h1>Finalizar Pedido</h1>

    
    <?php if (isset($mensagem_status) && $mensagem_status !== ""): ?>
        <div style="background-color: #ffe6e6; color: #cc0000; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; border: 1px solid #ffb3b3;">
            <?php echo htmlspecialchars($mensagem_status); ?>
        </div>
    <?php endif; ?>

    <form action="<?php echo BASE_URL; ?>/checkout" method="POST" class="checkout-content">
        
        <div class="checkout-main">
            
            <div class="checkout-section endereco-section">
                <h3 class="section-title"><i class="fa-solid fa-map-location-dot"></i> Endereço de Entrega</h3>
                
                <div class="form-row">
                    <div class="form-group" style="flex: 2;">
                        <label for="cep">CEP</label>
                        <input type="text" id="cep" name="cep" required placeholder="00000-000" maxlength="9">
                        <span id="cep-status" style="font-size: 0.8em; color: #cc0000;"></span>
                    </div>
                    <div class="form-group" style="flex: 1;">
                        <label for="cidade">Cidade</label>
                        <input type="text" id="cidade" name="cidade" required value="São Paulo">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="logradouro">Rua/Avenida</label>
                    <input type="text" id="logradouro" name="logradouro" required placeholder="Ex: Rua das Flores">
                </div>

                <div class="form-row">
                    <div class="form-group" style="flex: 1;">
                        <label for="numero">Número</label>
                        <input type="number" id="numero" name="numero" required>
                    </div>
                    <div class="form-group" style="flex: 2;">
                        <label for="complemento">Complemento (Opcional)</label>
                        <input type="text" id="complemento" name="complemento" placeholder="Apto, Bloco, etc.">
                    </div>
                </div>

                <div class="form-group">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" required>
                </div>

                <div style="padding: 10px 0; border-top: 1px solid #eee; margin-top: 15px;">
                    <p style="margin: 0; font-weight: 600;">Frete: Entrega Padrão (5-7 dias) - R$ <?php echo number_format($valor_frete, 2, ',', '.'); ?></p>
                </div>
            </div>
            
            <div class="checkout-section pagamento-section">
                <h3 class="section-title"><i class="fa-solid fa-credit-card"></i> Método de Pagamento</h3>
                
                <div class="payment-methods">
                    
                    <div class="payment-method-item selected">
                        <label>
                            <input type="radio" name="metodo_pagamento" value="pix" checked>
                            PIX
                        </label>
                        <div class="card-details">
                            Pague instantaneamente com PIX. Chave gerada na próxima tela.
                        </div>
                    </div>

                    <div class="payment-method-item">
                        <label>
                            <input type="radio" name="metodo_pagamento" value="cartao">
                            Cartão de Crédito
                        </label>
                        <div class="card-details" style="display: none;">
                            <div class="form-group">
                                <label for="num_cartao">Número do Cartão</label>
                                <input type="text" id="num_cartao" placeholder="0000 0000 0000 0000">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="validade">Validade</label>
                                    <input type="text" id="validade" placeholder="MM/AA">
                                </div>
                                <div class="form-group">
                                    <label for="cvv">CVV</label>
                                    <input type="text" id="cvv" placeholder="123">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="checkout-sidebar">
            <h3 class="section-title" style="margin-top: 0;"><i class="fa-solid fa-receipt"></i> Seu Pedido</h3>

            <?php foreach ($itens_carrinho_db as $item): ?>
                <div class="resumo-item">
                    <div class="resumo-img">
                        <img src="<?php echo BASE_URL; ?>/_ADM/<?php echo htmlspecialchars($item['imagem_url']); ?>" 
                             alt="<?php echo htmlspecialchars($item['nome_produto']); ?>">
                    </div>
                    <div class="resumo-info">
                        <span class="item-nome"><?php echo htmlspecialchars($item['nome_produto']); ?></span>
                        <p><?php echo htmlspecialchars($item['quantidade']); ?>x (T: <?php echo htmlspecialchars($item['tamanho']); ?>)</p>
                        <p>R$ <?php echo number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.'); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="resumo-valores">
                <div class="valor-row">
                    <span>Subtotal de Produtos</span>
                    <span>R$ <?php echo number_format($total_produtos, 2, ',', '.'); ?></span>
                </div>
                <div class="valor-row">
                    <span>Frete</span>
                    <span>R$ <?php echo number_format($valor_frete, 2, ',', '.'); ?></span>
                </div>
                <?php if ($valor_desconto > 0): ?>
                <div class="valor-row" style="color: var(--cor-alerta);">
                    <span>Desconto</span>
                    <span>- R$ <?php echo number_format($valor_desconto, 2, ',', '.'); ?></span>
                </div>
                <?php endif; ?>
                <div class="valor-row total-final">
                    <span>TOTAL A PAGAR</span>
                    <span>R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></span>
                </div>
            </div>

            <button type="submit" class="btn-concluir-compra" name="concluir_pedido">
                <i class="fa-solid fa-check-circle"></i> CONCLUIR COMPRA
            </button>
        </div>
    </form>
</div>

<script>
    const cepInput = document.getElementById('cep');
    const ruaInput = document.getElementById('logradouro');
    const bairroInput = document.getElementById('bairro');
    const cidadeInput = document.getElementById('cidade');
    const cepStatus = document.getElementById('cep-status');

    if(cepInput) {
        cepInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 5) {
                value = value.replace(/^(\d{5})(\d{0,3})/, '$1-$2');
            }
            e.target.value = value;
            
            if (e.target.value.length === 9) {
                consultarCEP(e.target.value);
            } else {
                limparFormulario(false);
                if(cepStatus) cepStatus.textContent = '';
            }
        });
    }

    async function consultarCEP(cep) {
        const cepLimpo = cep.replace(/\D/g, '');
        if (cepLimpo.length !== 8) return;
        
        const url = `https://viacep.com.br/ws/${cepLimpo}/json/`;
        
        if(cepStatus) cepStatus.textContent = 'Buscando...';
        if(ruaInput) ruaInput.disabled = true;
        if(bairroInput) bairroInput.disabled = true;
        
        try {
            const response = await fetch(url);
            const data = await response.json();

            if (data.erro) {
                if(cepStatus) cepStatus.textContent = 'CEP não encontrado.';
                limparFormulario(true);
            } else {
                if(cepStatus) cepStatus.textContent = '';
                if(ruaInput) ruaInput.value = data.logradouro;
                if(bairroInput) bairroInput.value = data.bairro;
                if(cidadeInput) cidadeInput.value = data.localidade;
                
                if(ruaInput) ruaInput.disabled = false;
                if(bairroInput) bairroInput.disabled = false;
            }
        } catch (error) {
            if(cepStatus) cepStatus.textContent = 'Erro ao consultar o CEP.';
            limparFormulario(true);
        }
    }

    function limparFormulario(habilitarCampos) {
        if(ruaInput) ruaInput.value = '';
        if(bairroInput) bairroInput.value = '';
        if(cidadeInput) cidadeInput.value = 'São Paulo';
        
        if (habilitarCampos) {
            if(ruaInput) ruaInput.disabled = false;
            if(bairroInput) bairroInput.disabled = false;
        }
    }

    document.querySelectorAll('.payment-method-item').forEach(item => {
        item.addEventListener('click', function() {
            document.querySelectorAll('.payment-method-item').forEach(i => {
                i.classList.remove('selected');
                i.querySelector('.card-details').style.display = 'none';
            });
            this.classList.add('selected');
            const radio = this.querySelector('input[type="radio"]');
            if (radio.value === 'cartao') {
                this.querySelector('.card-details').style.display = 'block';
            }
            radio.checked = true;
        });
    });
</script>