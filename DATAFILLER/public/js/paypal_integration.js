/**
 * PayPal Integration JavaScript
 * Maneja la integración con PayPal para pagos de planes Premium
 */

class PayPalIntegration {
    constructor(config) {
        this.clientId = config.client_id;
        this.environment = config.environment;
        this.currency = config.currency || 'USD';
        this.isInitialized = false;
    }
    
    /**
     * Inicializar PayPal SDK
     */
    async init() {
        if (this.isInitialized) return;
        
        try {
            // Cargar PayPal SDK dinámicamente
            await this.loadPayPalSDK();
            this.isInitialized = true;
            console.log('PayPal SDK initialized successfully');
        } catch (error) {
            console.error('Error initializing PayPal SDK:', error);
            throw error;
        }
    }
    
    /**
     * Cargar PayPal SDK
     */
    loadPayPalSDK() {
        return new Promise((resolve, reject) => {
            // Verificar si ya está cargado
            if (window.paypal) {
                resolve();
                return;
            }
            
            const script = document.createElement('script');
            script.src = `https://www.paypal.com/sdk/js?client-id=${this.clientId}&currency=${this.currency}`;
            script.onload = resolve;
            script.onerror = reject;
            document.head.appendChild(script);
        });
    }
    
    /**
     * Renderizar botones de PayPal
     */
    renderPayPalButtons(containerId, planId, amount) {
        if (!this.isInitialized) {
            throw new Error('PayPal SDK not initialized. Call init() first.');
        }
        
        const container = document.getElementById(containerId);
        if (!container) {
            throw new Error(`Container with ID "${containerId}" not found`);
        }
        
        // Limpiar contenedor
        container.innerHTML = '';
        
        window.paypal.Buttons({
            style: {
                layout: 'vertical',
                color: 'gold',
                shape: 'rect',
                label: 'paypal'
            },
            
            createOrder: (data, actions) => {
                return this.createOrder(planId);
            },
            
            onApprove: (data, actions) => {
                return this.onApprove(data.orderID);
            },
            
            onError: (err) => {
                this.onError(err);
            },
            
            onCancel: (data) => {
                this.onCancel(data);
            }
            
        }).render(`#${containerId}`);
    }
    
    /**
     * Crear orden de pago
     */
    async createOrder(planId) {
        try {
            this.showLoading('Creando orden de pago...');
            
            const response = await fetch('../../controllers/paypal_create_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    plan_id: planId
                })
            });
            
            const result = await response.json();
            
            if (!result.success) {
                throw new Error(result.error || 'Error creando orden');
            }
            
            // Trackear evento en Application Insights
            if (typeof appInsights !== 'undefined') {
                appInsights.trackEvent({
                    name: 'PayPalOrderCreated',
                    properties: {
                        planId: planId,
                        orderId: result.order_id
                    }
                });
            }
            
            this.hideLoading();
            return result.order_id;
            
        } catch (error) {
            this.hideLoading();
            this.showError('Error creando orden: ' + error.message);
            throw error;
        }
    }
    
    /**
     * Manejar aprobación de pago
     */
    async onApprove(orderID) {
        try {
            this.showLoading('Procesando pago...');
            
            const response = await fetch('../../controllers/paypal_capture_order.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    order_id: orderID
                })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Trackear pago exitoso
                if (typeof appInsights !== 'undefined') {
                    appInsights.trackEvent({
                        name: 'PayPalPaymentSuccess',
                        properties: {
                            orderId: orderID
                        }
                    });
                }
                
                this.showSuccess(result.message);
                
                // Redirigir después de 3 segundos
                setTimeout(() => {
                    window.location.href = '../User/generardata.php';
                }, 3000);
                
            } else {
                throw new Error(result.error || 'Error procesando pago');
            }
            
        } catch (error) {
            this.hideLoading();
            this.showError('Error procesando pago: ' + error.message);
            
            // Trackear error de pago
            if (typeof appInsights !== 'undefined') {
                appInsights.trackEvent({
                    name: 'PayPalPaymentError',
                    properties: {
                        orderId: orderID,
                        error: error.message
                    }
                });
            }
        }
    }
    
    /**
     * Manejar errores de PayPal
     */
    onError(err) {
        console.error('PayPal error:', err);
        this.showError('Error en PayPal: ' + (err.message || 'Error desconocido'));
        
        // Trackear error
        if (typeof appInsights !== 'undefined') {
            appInsights.trackEvent({
                name: 'PayPalSDKError',
                properties: {
                    error: err.message || 'Unknown error'
                }
            });
        }
    }
    
    /**
     * Manejar cancelación de pago
     */
    onCancel(data) {
        console.log('PayPal payment cancelled:', data);
        this.showInfo('Pago cancelado. Puedes intentar nuevamente cuando desees.');
        
        // Trackear cancelación
        if (typeof appInsights !== 'undefined') {
            appInsights.trackEvent({
                name: 'PayPalPaymentCancelled',
                properties: {
                    orderId: data.orderID || 'unknown'
                }
            });
        }
    }
    
    /**
     * Mostrar mensaje de carga
     */
    showLoading(message) {
        this.hideMessages();
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'paypal-loading';
        loadingDiv.className = 'paypal-message paypal-loading';
        loadingDiv.innerHTML = `
            <div class="loading-spinner"></div>
            <p>${message}</p>
        `;
        document.body.appendChild(loadingDiv);
    }
    
    /**
     * Ocultar mensaje de carga
     */
    hideLoading() {
        const loading = document.getElementById('paypal-loading');
        if (loading) {
            loading.remove();
        }
    }
    
    /**
     * Mostrar mensaje de éxito
     */
    showSuccess(message) {
        this.hideMessages();
        this.showMessage(message, 'success');
    }
    
    /**
     * Mostrar mensaje de error
     */
    showError(message) {
        this.hideMessages();
        this.showMessage(message, 'error');
    }
    
    /**
     * Mostrar mensaje informativo
     */
    showInfo(message) {
        this.hideMessages();
        this.showMessage(message, 'info');
    }
    
    /**
     * Mostrar mensaje genérico
     */
    showMessage(message, type) {
        const messageDiv = document.createElement('div');
        messageDiv.id = 'paypal-message';
        messageDiv.className = `paypal-message paypal-${type}`;
        messageDiv.innerHTML = `
            <div class="message-content">
                <p>${message}</p>
                <button onclick="this.parentElement.parentElement.remove()" class="close-btn">×</button>
            </div>
        `;
        document.body.appendChild(messageDiv);
        
        // Auto-ocultar después de 10 segundos (excepto errores)
        if (type !== 'error') {
            setTimeout(() => {
                if (messageDiv.parentNode) {
                    messageDiv.remove();
                }
            }, 10000);
        }
    }
    
    /**
     * Ocultar todos los mensajes
     */
    hideMessages() {
        const existingMessages = document.querySelectorAll('.paypal-message');
        existingMessages.forEach(msg => msg.remove());
    }
}

// CSS para los mensajes (se inyecta dinámicamente)
const paypalCSS = `
.paypal-message {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 10000;
    max-width: 400px;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    animation: slideIn 0.3s ease-out;
}

.paypal-loading {
    background: #fff;
    border-left: 4px solid #0070ba;
    color: #333;
}

.paypal-success {
    background: #d4edda;
    border-left: 4px solid #28a745;
    color: #155724;
}

.paypal-error {
    background: #f8d7da;
    border-left: 4px solid #dc3545;
    color: #721c24;
}

.paypal-info {
    background: #d1ecf1;
    border-left: 4px solid #17a2b8;
    color: #0c5460;
}

.message-content {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.close-btn {
    background: none;
    border: none;
    font-size: 20px;
    cursor: pointer;
    padding: 0;
    margin-left: 10px;
    opacity: 0.6;
}

.close-btn:hover {
    opacity: 1;
}

.loading-spinner {
    width: 20px;
    height: 20px;
    border: 2px solid #f3f3f3;
    border-top: 2px solid #0070ba;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    display: inline-block;
    margin-right: 10px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

@keyframes slideIn {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}
`;

// Inyectar CSS
const style = document.createElement('style');
style.textContent = paypalCSS;
document.head.appendChild(style);

// Exportar la clase para uso global
window.PayPalIntegration = PayPalIntegration;
