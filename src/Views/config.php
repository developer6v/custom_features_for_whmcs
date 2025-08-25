use WHMCS\Database\Capsule;

function config() {
    // Recuperando os valores das configurações armazenadas no banco de dados
    $config = Capsule::table('sr_cf_config')->first();

    // Preencher os valores dos campos com os dados recuperados
    $tentativasRegistro = $config->max_trials; // Quantidade de tentativas
    $intervaloTentativas = $config->interval_between_trials; // Intervalo entre tentativas
    $abrirTicket = $config->openTicketAfterTrials ? 'checked' : ''; // Checkbox para abrir ticket após tentativas

    $layout = '
    <div class="cf_container">
        <div class="header_cf">
            <h1>Configurações do Sistema</h1>
        </div>

        <div class="cf_tabs-container">
            <!-- Navegação das abas -->
            <div class="cf_tabs-nav">
                <button class="cf_tab-button cf_active" data-tab="cpf-cnpj">CPF/CNPJ</button>
                <button class="cf_tab-button" data-tab="erro-129">Erro registro 129</button>
            </div>

            <!-- Conteúdo das abas -->
            <div class="cf_tabs-content">
                <!-- Aba CPF/CNPJ -->
                <div id="cpf-cnpj" class="cf_tab-content cf_active">
                    <div class="cf_config-section">
                        <h2>Configurações CPF/CNPJ</h2>
                        <div class="cf_form-group">
                            <p class="cf_description">
                                Utilize esta opção para atualizar clientes que possuem o campo CPF/CNPJ desconfigurado no sistema.
                            </p>
                            <button id="btn-atualizar-cpf" class="cf_btn cf_btn-primary">
                                Atualizar clientes com campo CPF/CNPJ desconfigurados
                            </button>
                            <div id="status-cpf" class="cf_status-message"></div>
                        </div>
                    </div>
                </div>

                <!-- Aba Erro registro 129 -->
                <div id="erro-129" class="cf_tab-content">
                    <div class="cf_config-section">
                        <h2>Configurações Erro registro 129</h2>
                        
                        <div class="cf_form-group">
                            <label for="tentativas-registro">Quantidade de tentativas de registro até marcar para cancelado:</label>
                            <input type="number" id="tentativas-registro" name="tentativas-registro" min="1" max="10" value="' . $tentativasRegistro . '">
                        </div>

                        <div class="cf_form-group">
                            <label class="cf_checkbox-container">
                                <input type="checkbox" id="abrir-ticket" name="abrir-ticket" ' . $abrirTicket . '>
                                <span class="cf_checkmark"></span>
                                Abrir ticket após tentativas?
                            </label>
                        </div>

                        <div class="cf_form-group">
                            <label for="intervalo-tentativas">Tempo de intervalo entre tentativas de registro (em minutos):</label>
                            <input type="number" id="intervalo-tentativas" name="intervalo-tentativas" min="1" max="60" value="' . $intervaloTentativas . '">
                        </div>

                        <div class="cf_form-actions">
                            <button id="btn-salvar-erro129" class="cf_btn cf_btn-success">Salvar Configurações</button>
                            <button id="btn-resetar-erro129" class="cf_btn cf_btn-secondary">Resetar para Padrão</button>
                        </div>
                        
                        <div id="status-erro129" class="cf_status-message"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    ';
    
    return $layout;
}
