import Alpine from 'alpinejs';
import './bootstrap';

// Make Alpine available globally
window.Alpine = Alpine;

// IOTCNT JavaScript Application
class IOTCNTApp {
    constructor() {
        this.apiToken = document.querySelector('meta[name="api-token"]')?.getAttribute('content');
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        this.init();
    }

    init() {
        this.setupAxiosDefaults();
        this.initializeValveControls();
        this.initializeRealTimeUpdates();
        this.initializeNotifications();

        console.log('🌱 IOTCNT App initialized');
    }

    setupAxiosDefaults() {
        if (this.csrfToken) {
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = this.csrfToken;
        }

        if (this.apiToken) {
            window.axios.defaults.headers.common['Authorization'] = `Bearer ${this.apiToken}`;
        }
    }

    // Controlo de Válvulas
    initializeValveControls() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('[data-valve-control]')) {
                e.preventDefault();
                this.handleValveControl(e.target);
            }
        });
    }

    async handleValveControl(button) {
        const valveId = button.dataset.valveId;
        const action = button.dataset.action; // 'on', 'off', 'toggle'
        const duration = button.dataset.duration || 5;

        try {
            button.disabled = true;
            button.innerHTML = '<div class="loading-spinner"></div> A processar...';

            const response = await window.axios.post('/api/valve/control', {
                valve_id: valveId,
                action: action,
                duration: duration
            });

            if (response.data.success) {
                this.showNotification('Comando enviado com sucesso!', 'success');
                this.updateValveStatus(valveId, response.data.valve);
            } else {
                throw new Error(response.data.message || 'Erro desconhecido');
            }
        } catch (error) {
            console.error('Erro no controlo da válvula:', error);
            this.showNotification('Erro ao controlar válvula: ' + error.message, 'error');
        } finally {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || 'Controlar';
        }
    }

    // Atualizações em Tempo Real
    initializeRealTimeUpdates() {
        // Polling para atualizações de estado das válvulas
        setInterval(() => {
            this.fetchValveStatus();
        }, 10000); // A cada 10 segundos

        // Atualizar imediatamente ao carregar a página
        this.fetchValveStatus();
    }

    async fetchValveStatus() {
        try {
            const response = await window.axios.get('/api/valve/status');
            if (response.data.success) {
                this.updateAllValveStatus(response.data.valves);
            }
        } catch (error) {
            console.error('Erro ao obter estado das válvulas:', error);
        }
    }

    updateValveStatus(valveId, valveData) {
        const valveCard = document.querySelector(`[data-valve-id="${valveId}"]`);
        if (!valveCard) return;

        const statusBadge = valveCard.querySelector('.status-badge');
        const valveCardElement = valveCard.closest('.valve-card');

        if (valveData.current_state) {
            statusBadge.textContent = 'Ligada';
            statusBadge.className = 'status-badge status-active';
            valveCardElement.className = valveCardElement.className.replace('valve-inactive', 'valve-active');
        } else {
            statusBadge.textContent = 'Desligada';
            statusBadge.className = 'status-badge status-inactive';
            valveCardElement.className = valveCardElement.className.replace('valve-active', 'valve-inactive');
        }

        // Atualizar timestamp da última ativação
        const lastActivated = valveCard.querySelector('.last-activated');
        if (lastActivated && valveData.last_activated_at) {
            lastActivated.textContent = new Date(valveData.last_activated_at).toLocaleString('pt-PT');
        }
    }

    updateAllValveStatus(valves) {
        valves.forEach(valve => {
            this.updateValveStatus(valve.id, valve);
        });
    }

    // Sistema de Notificações
    initializeNotifications() {
        // Criar container de notificações se não existir
        if (!document.getElementById('notifications-container')) {
            const container = document.createElement('div');
            container.id = 'notifications-container';
            container.className = 'fixed top-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
        }
    }

    showNotification(message, type = 'info', duration = 5000) {
        const container = document.getElementById('notifications-container');
        const notification = document.createElement('div');

        const typeClasses = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            warning: 'bg-yellow-500 text-black',
            info: 'bg-blue-500 text-white'
        };

        notification.className = `px-4 py-2 rounded-md shadow-lg ${typeClasses[type]} transform transition-all duration-300 translate-x-full opacity-0`;
        notification.textContent = message;

        container.appendChild(notification);

        // Animar entrada
        setTimeout(() => {
            notification.classList.remove('translate-x-full', 'opacity-0');
        }, 100);

        // Remover após duração especificada
        setTimeout(() => {
            notification.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, duration);
    }

    // Utilitários
    formatDateTime(dateString) {
        return new Date(dateString).toLocaleString('pt-PT', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    // Ciclo de Irrigação
    async startIrrigationCycle() {
        try {
            const response = await window.axios.post('/api/valve/start-cycle');
            if (response.data.success) {
                this.showNotification('Ciclo de irrigação iniciado!', 'success');
                this.fetchValveStatus(); // Atualizar estado
            }
        } catch (error) {
            this.showNotification('Erro ao iniciar ciclo: ' + error.message, 'error');
        }
    }

    async stopAllValves() {
        if (!confirm('Tem a certeza que deseja parar todas as válvulas?')) {
            return;
        }

        try {
            const response = await window.axios.post('/api/valve/stop-all');
            if (response.data.success) {
                this.showNotification('Todas as válvulas foram paradas!', 'success');
                this.fetchValveStatus(); // Atualizar estado
            }
        } catch (error) {
            this.showNotification('Erro ao parar válvulas: ' + error.message, 'error');
        }
    }
}

// Funções globais para uso nos templates
window.iotcnt = {
    startCycle: () => window.iotcntApp.startIrrigationCycle(),
    stopAll: () => window.iotcntApp.stopAllValves(),
    controlValve: (valveId, action, duration) => {
        const button = document.createElement('button');
        button.dataset.valveId = valveId;
        button.dataset.action = action;
        button.dataset.duration = duration;
        return window.iotcntApp.handleValveControl(button);
    }
};

// Inicializar aplicação quando DOM estiver pronto
document.addEventListener('DOMContentLoaded', () => {
    window.iotcntApp = new IOTCNTApp();
    Alpine.start();
});
