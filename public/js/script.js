// Configurações da aplicação
const CONFIG = {
    // URL que será configurada pelo usuário para atualização de CPF/CNPJ
    urlAtualizarCpf: '', // Deixar vazio para ser configurado
    
    // Configurações padrão para erro 129
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
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.getAttribute('data-tab');
                
                // Remove classe active de todos os botões e conteúdos
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Adiciona classe active ao botão clicado e conteúdo correspondente
                button.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
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

        // Validação em tempo real dos campos numéricos
        const tentativasInput = document.getElementById('tentativas-registro');
        const intervaloInput = document.getElementById('intervalo-tentativas');

        tentativasInput.addEventListener('input', () => {
            this.validarCampoNumerico(tentativasInput, 1, 10);
        });

        intervaloInput.addEventListener('input', () => {
            this.validarCampoNumerico(intervaloInput, 1, 60);
        });
    }

    // Função para atualizar CPF/CNPJ
    async atualizarCpfCnpj(statusDiv) {
        if (!CONFIG.urlAtualizarCpf) {
            this.showStatus(statusDiv, 'error', 'URL de atualização não configurada. Configure a URL no arquivo script.js na variável CONFIG.urlAtualizarCpf');
            return;
        }

        this.showStatus(statusDiv, 'info', 'Iniciando atualização de clientes...');
        
        try {
            const response = await fetch(CONFIG.urlAtualizarCpf, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
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

    // Função para salvar configurações do erro 129
    salvarConfiguracoes129(statusDiv) {
        const tentativas = document.getElementById('tentativas-registro').value;
        const abrirTicket = document.getElementById('abrir-ticket').checked;
        const intervalo = document.getElementById('intervalo-tentativas').value;

        // Validação
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

        // Salvar no localStorage
        localStorage.setItem('config_erro129', JSON.stringify(configuracoes));

        // Simular salvamento no backend (aqui você pode adicionar uma requisição real)
        this.showStatus(statusDiv, 'success', 'Configurações salvas com sucesso!');
        
        console.log('Configurações salvas:', configuracoes);
    }

    // Função para resetar configurações do erro 129
    resetarConfiguracoes129(statusDiv) {
        document.getElementById('tentativas-registro').value = CONFIG.defaultSettings.tentativasRegistro;
        document.getElementById('abrir-ticket').checked = CONFIG.defaultSettings.abrirTicket;
        document.getElementById('intervalo-tentativas').value = CONFIG.defaultSettings.intervaloTentativas;

        // Remover do localStorage
        localStorage.removeItem('config_erro129');

        this.showStatus(statusDiv, 'info', 'Configurações resetadas para os valores padrão.');
    }

    // Validação das configurações do erro 129
    validarConfiguracoes129(tentativas, intervalo) {
        const numTentativas = parseInt(tentativas);
        const numIntervalo = parseInt(intervalo);

        return (
            numTentativas >= 1 && numTentativas <= 10 &&
            numIntervalo >= 1 && numIntervalo <= 60
        );
    }

    // Validação de campo numérico em tempo real
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

    // Carregar configurações salvas
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

    // Função utilitária para mostrar mensagens de status
    showStatus(element, type, message) {
        element.className = `status-message ${type}`;
        element.textContent = message;
        element.style.display = 'block';

        // Auto-hide após 5 segundos para mensagens de sucesso e info
        if (type === 'success' || type === 'info') {
            setTimeout(() => {
                element.style.display = 'none';
            }, 5000);
        }
    }

    // Método para configurar a URL de atualização de CPF/CNPJ externamente
    static configurarUrlCpf(url) {
        CONFIG.urlAtualizarCpf = url;
    }
}

// Utilitários adicionais
const Utils = {
    // Formatar data para exibição
    formatarData(dataISO) {
        const data = new Date(dataISO);
        return data.toLocaleString('pt-BR');
    },

    // Validar URL
    validarUrl(url) {
        try {
            new URL(url);
            return true;
        } catch {
            return false;
        }
    },

    // Debounce para otimizar validações em tempo real
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// Inicialização da aplicação quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    window.configApp = new ConfiguracaoApp();
    
    // Exemplo de como configurar a URL de CPF/CNPJ
    // ConfiguracaoApp.configurarUrlCpf('https://sua-api.com/atualizar-cpf-cnpj');
    
    console.log('Aplicação de configuração inicializada com sucesso!');
});

// Exportar para uso global se necessário
window.ConfiguracaoApp = ConfiguracaoApp;
window.Utils = Utils;

