// Configurações da aplicação
const CONFIG = {
    urlAtualizarCpf: '/modules/addons/autonotify_module_whmcs/src/Controllers/autonotify_login.php',
    defaultSettings: {
        tentativasRegistro: 3,
        abrirTicket: false,
        intervaloTentativas: 5
    }
};

// Classe principal da aplicação
class ConfiguracaoApp {
    constructor() {
        this.init();
    }

    init() {
        this.setupTabs();
        this.setupCpfCnpjTab();
        this.setupErro129Tab();
        this.loadSavedSettings();
    }

    // Configuração do sistema de abas
    setupTabs() {
        const tabButtons = document.querySelectorAll('.cf_tab-button');
        const tabContents = document.querySelectorAll('.cf_tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.getAttribute('data-tab');

                tabButtons.forEach(btn => btn.classList.remove('cf_active'));
                tabContents.forEach(content => content.classList.remove('cf_active'));

                button.classList.add('cf_active');
                document.getElementById(targetTab).classList.add('cf_active');
            });
        });
    }

    // Configuração da aba CPF/CNPJ
    setupCpfCnpjTab() {
        const btnAtualizar = document.getElementById('btn-atualizar-cpf');
        const statusDiv = document.getElementById('status-cpf');

        btnAtualizar.addEventListener('click', () => {
            this.atualizarCpfCnpj(statusDiv);
        });
    }

    // Configuração da aba Erro 129
    setupErro129Tab() {
        const btnSalvar = document.getElementById('btn-salvar-erro129');
        const btnResetar = document.getElementById('btn-resetar-erro129');
        const statusDiv = document.getElementById('status-erro129');

        btnSalvar.addEventListener('click', () => {
            this.salvarConfiguracoes129(statusDiv);
        });

        btnResetar.addEventListener('click', () => {
            this.resetarConfiguracoes129(statusDiv);
        });

        const tentativasInput = document.getElementById('tentativas-registro');
        const intervaloInput = document.getElementById('intervalo-tentativas');

        tentativasInput.addEventListener('input', () => {
            this.validarCampoNumerico(tentativasInput, 1, 10);
        });

        intervaloInput.addEventListener('input', () => {
            this.validarCampoNumerico(intervaloInput, 1, 60);
        });
    }

    async atualizarCpfCnpj(statusDiv) {
        if (!CONFIG.urlAtualizarCpf) {
            this.showStatus(statusDiv, 'error', 'URL de atualização não configurada. Configure a URL no arquivo script.js na variável CONFIG.urlAtualizarCpf');
            return;
        }

        this.showStatus(statusDiv, 'info', 'Iniciando atualização de clientes...');

        try {
            const response = await fetch(CONFIG.urlAtualizarCpf, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'atualizar_cpf_cnpj',
                    timestamp: new Date().toISOString()
                })
            });

            if (response.ok) {
                const result = await response.json();
                this.showStatus(statusDiv, 'success', `Atualização concluída com sucesso! ${result.message || 'Clientes atualizados.'}`);
            } else {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
        } catch (error) {
            console.error('Erro ao atualizar CPF/CNPJ:', error);
            this.showStatus(statusDiv, 'error', `Erro ao processar solicitação: ${error.message}`);
        }
    }

    salvarConfiguracoes129(statusDiv) {
        const tentativas = document.getElementById('tentativas-registro').value;
        const abrirTicket = document.getElementById('abrir-ticket').checked;
        const intervalo = document.getElementById('intervalo-tentativas').value;

        if (!this.validarConfiguracoes129(tentativas, intervalo)) {
            this.showStatus(statusDiv, 'error', 'Por favor, verifique os valores inseridos. Tentativas: 1-10, Intervalo: 1-60 minutos.');
            return;
        }

        const configuracoes = {
            tentativasRegistro: parseInt(tentativas),
            abrirTicket: abrirTicket,
            intervaloTentativas: parseInt(intervalo),
            dataAtualizacao: new Date().toISOString()
        };

        localStorage.setItem('config_erro129', JSON.stringify(configuracoes));

        this.showStatus(statusDiv, 'success', 'Configurações salvas com sucesso!');
        console.log('Configurações salvas:', configuracoes);
    }

    resetarConfiguracoes129(statusDiv) {
        document.getElementById('tentativas-registro').value = CONFIG.defaultSettings.tentativasRegistro;
        document.getElementById('abrir-ticket').checked = CONFIG.defaultSettings.abrirTicket;
        document.getElementById('intervalo-tentativas').value = CONFIG.defaultSettings.intervaloTentativas;

        localStorage.removeItem('config_erro129');
        this.showStatus(statusDiv, 'info', 'Configurações resetadas para os valores padrão.');
    }

    validarConfiguracoes129(tentativas, intervalo) {
        const numTentativas = parseInt(tentativas);
        const numIntervalo = parseInt(intervalo);

        return numTentativas >= 1 && numTentativas <= 10 && numIntervalo >= 1 && numIntervalo <= 60;
    }

    validarCampoNumerico(input, min, max) {
        const valor = parseInt(input.value);

        if (isNaN(valor) || valor < min || valor > max) {
            input.style.borderColor = '#dc3545';
            input.style.boxShadow = '0 0 0 3px rgba(220,53,69,0.1)';
        } else {
            input.style.borderColor = '#28a745';
            input.style.boxShadow = '0 0 0 3px rgba(40,167,69,0.1)';
        }
    }

    loadSavedSettings() {
        const savedConfig = localStorage.getItem('config_erro129');

        if (savedConfig) {
            try {
                const config = JSON.parse(savedConfig);

                document.getElementById('tentativas-registro').value = config.tentativasRegistro || CONFIG.defaultSettings.tentativasRegistro;
                document.getElementById('abrir-ticket').checked = config.abrirTicket || CONFIG.defaultSettings.abrirTicket;
                document.getElementById('intervalo-tentativas').value = config.intervaloTentativas || CONFIG.defaultSettings.intervaloTentativas;
            } catch (error) {
                console.error('Erro ao carregar configurações salvas:', error);
            }
        }
    }

    showStatus(element, type, message) {
        element.className = `cf_status-message ${type}`;
        element.textContent = message;
        element.style.display = 'block';

        if (type === 'success' || type === 'info') {
            setTimeout(() => {
                element.style.display = 'none';
            }, 5000);
        }
    }

    static configurarUrlCpf(url) {
        CONFIG.urlAtualizarCpf = url;
    }
}

// Inicialização
document.addEventListener('DOMContentLoaded', () => {
    window.configApp = new ConfiguracaoApp();
    console.log('Aplicação de configuração inicializada com sucesso!');
});

window.ConfiguracaoApp = ConfiguracaoApp;
