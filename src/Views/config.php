<?php


function config() {
    $layout = '
    <div class="container">
        <header>
            <h1>Configurações do Sistema</h1>
        </header>

        <div class="tabs-container">
            <!-- Navegação das abas -->
            <div class="tabs-nav">
                <button class="tab-button active" data-tab="cpf-cnpj">CPF/CNPJ</button>
                <button class="tab-button" data-tab="erro-129">Erro registro 129</button>
            </div>

            <!-- Conteúdo das abas -->
            <div class="tabs-content">
                <!-- Aba CPF/CNPJ -->
                <div id="cpf-cnpj" class="tab-content active">
                    <div class="config-section">
                        <h2>Configurações CPF/CNPJ</h2>
                        <div class="form-group">
                            <p class="description">
                                Utilize esta opção para atualizar clientes que possuem o campo CPF/CNPJ desconfigurado no sistema.
                            </p>
                            <button id="btn-atualizar-cpf" class="btn btn-primary">
                                Atualizar clientes com campo CPF/CNPJ desconfigurados
                            </button>
                            <div id="status-cpf" class="status-message"></div>
                        </div>
                    </div>
                </div>

                <!-- Aba Erro registro 129 -->
                <div id="erro-129" class="tab-content">
                    <div class="config-section">
                        <h2>Configurações Erro registro 129</h2>
                        
                        <div class="form-group">
                            <label for="tentativas-registro">Quantidade de tentativas de registro até marcar para cancelado:</label>
                            <input type="number" id="tentativas-registro" name="tentativas-registro" min="1" max="10" value="3">
                        </div>

                        <div class="form-group">
                            <label class="checkbox-container">
                                <input type="checkbox" id="abrir-ticket" name="abrir-ticket">
                                <span class="checkmark"></span>
                                Abrir ticket após tentativas?
                            </label>
                        </div>

                        <div class="form-group">
                            <label for="intervalo-tentativas">Tempo de intervalo entre tentativas de registro (em minutos):</label>
                            <input type="number" id="intervalo-tentativas" name="intervalo-tentativas" min="1" max="60" value="5">
                        </div>

                        <div class="form-actions">
                            <button id="btn-salvar-erro129" class="btn btn-success">Salvar Configurações</button>
                            <button id="btn-resetar-erro129" class="btn btn-secondary">Resetar para Padrão</button>
                        </div>
                        
                        <div id="status-erro129" class="status-message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    ';
    return $layout;
}

?>