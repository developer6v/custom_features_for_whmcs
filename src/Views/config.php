<?php


function config() {
    $layout = '
       <div class="manus-config-container">
        <header class="manus-config-header">
            <h1 class="manus-config-title">Configurações do Sistema</h1>
        </header>

        <div class="manus-tabs-wrapper">
            <!-- Navegação das abas -->
            <div class="manus-tabs-navigation">
                <button class="manus-tab-btn manus-tab-active" data-tab="cpf-cnpj">CPF/CNPJ</button>
                <button class="manus-tab-btn" data-tab="erro-129">Erro registro 129</button>
            </div>

            <!-- Conteúdo das abas -->
            <div class="manus-tabs-content-area">
                <!-- Aba CPF/CNPJ -->
                <div id="cpf-cnpj" class="manus-tab-panel manus-tab-active">
                    <div class="manus-config-section">
                        <h2 class="manus-section-title">Configurações CPF/CNPJ</h2>
                        <div class="manus-form-group">
                            <p class="manus-description">
                                Utilize esta opção para atualizar clientes que possuem o campo CPF/CNPJ desconfigurado no sistema.
                            </p>
                            <button id="btn-atualizar-cpf" class="manus-btn manus-btn-primary">
                                Atualizar clientes com campo CPF/CNPJ desconfigurados
                            </button>
                            <div id="status-cpf" class="manus-status-msg"></div>
                        </div>
                    </div>
                </div>

                <!-- Aba Erro registro 129 -->
                <div id="erro-129" class="manus-tab-panel">
                    <div class="manus-config-section">
                        <h2 class="manus-section-title">Configurações Erro registro 129</h2>
                        
                        <div class="manus-form-group">
                            <label for="tentativas-registro" class="manus-form-label">Quantidade de tentativas de registro até marcar para cancelado:</label>
                            <input type="number" id="tentativas-registro" name="tentativas-registro" min="1" max="10" value="3" class="manus-form-input">
                        </div>

                        <div class="manus-form-group">
                            <label class="manus-checkbox-wrapper">
                                <input type="checkbox" id="abrir-ticket" name="abrir-ticket" class="manus-checkbox-input">
                                <span class="manus-checkbox-mark"></span>
                                Abrir ticket após tentativas?
                            </label>
                        </div>

                        <div class="manus-form-group">
                            <label for="intervalo-tentativas" class="manus-form-label">Tempo de intervalo entre tentativas de registro (em minutos):</label>
                            <input type="number" id="intervalo-tentativas" name="intervalo-tentativas" min="1" max="60" value="5" class="manus-form-input">
                        </div>

                        <div class="manus-form-actions">
                            <button id="btn-salvar-erro129" class="manus-btn manus-btn-success">Salvar Configurações</button>
                            <button id="btn-resetar-erro129" class="manus-btn manus-btn-secondary">Resetar para Padrão</button>
                        </div>
                        
                        <div id="status-erro129" class="manus-status-msg"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    ';
    return $layout;
}

?>