/**
 * Mock Login Controller
 * Controlador específico para usar con el servicio de login simulado
 * Incluye indicadores visuales y funcionalidades específicas para el modo mock
 */
class MockLoginController {
    constructor() {
        this.form = document.getElementById('login-form');
        this.errorMsg = document.getElementById('error-msg');
        this.submitBtn = this.form.querySelector('.btn-ingresar');
        this.btnText = this.submitBtn.querySelector('.btn-text');
        this.btnLoading = this.submitBtn.querySelector('.btn-loading');
        
        // Inicializar servicio mock
        this.loginService = new MockLoginService();
        
        this.baseUrl = window.location.hostname.includes('github.io') 
            ? '/PanelWeb'
            : '';
            
        console.log('🔧 MockLoginController inicializado');
        this.init();
    }

    init() {
        // Mostrar indicador de modo mock
        this.showMockIndicator();
        
        // Configurar event listeners
        this.setupEventListeners();
        
        // Configurar validación del documento
        this.setupDocumentValidation();
        
        // Configurar toggle de contraseña
        this.setupPasswordToggle();
        
        // Rellenar automáticamente con credenciales de prueba
        this.setupAutoFill();
    }

    /**
     * Muestra el indicador visual de que se está en modo mock
     */
    showMockIndicator() {
        // Crear indicador si no existe
        let mockIndicator = document.getElementById('mock-service-indicator');
        
        if (!mockIndicator) {
            mockIndicator = document.createElement('div');
            mockIndicator.id = 'mock-service-indicator';
            mockIndicator.style.cssText = `
                background: #e8f4fd; 
                border: 1px solid #bee5eb; 
                color: #0c5460; 
                padding: 12px; 
                margin: 10px 0; 
                border-radius: 6px; 
                font-size: 13px;
                text-align: center;
                box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            `;
            
            // Insertar después del formulario
            const loginForm = document.querySelector('.login-form');
            if (loginForm && loginForm.parentNode) {
                loginForm.parentNode.insertBefore(mockIndicator, loginForm.nextSibling);
            }
        }

        // Obtener usuarios mock para mostrar
        const mockUsers = this.loginService.getMockUsers();
        
        mockIndicator.innerHTML = `
            <div style="margin-bottom: 8px;">
                <i class="fas fa-flask" style="color: #17a2b8;"></i> 
                <strong>MODO SIMULADO ACTIVO</strong>
            </div>
            <div style="font-size: 12px; line-height: 1.4;">
                <strong>👥 Usuarios de prueba disponibles:</strong><br>
                ${mockUsers.map(user => 
                    `📋 ${user.document} | Password@123 (${user.roles.join(', ')})`
                ).join('<br>')}
            </div>
            <div style="margin-top: 8px; font-size: 11px; opacity: 0.8;">
                💡 Haz clic en un usuario para rellenar automáticamente
            </div>
        `;

        // Hacer clickeable para auto-rellenar
        mockIndicator.addEventListener('click', () => {
            this.showUserSelector();
        });

        mockIndicator.style.display = 'block';
    }

    /**
     * Muestra un selector de usuarios para auto-rellenar
     */
    showUserSelector() {
        const mockUsers = this.loginService.getMockUsers();
        
        // Crear modal simple
        const modal = document.createElement('div');
        modal.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10000;
        `;

        const content = document.createElement('div');
        content.style.cssText = `
            background: white;
            padding: 20px;
            border-radius: 8px;
            max-width: 400px;
            width: 90%;
            box-shadow: 0 4px 20px rgba(0,0,0,0.3);
        `;

        content.innerHTML = `
            <h3 style="margin-top: 0; color: #333;">👥 Seleccionar Usuario de Prueba</h3>
            <div style="margin: 15px 0;">
                ${mockUsers.map((user, index) => `
                    <button onclick="mockLoginController.fillCredentials('${user.document}')" 
                            style="
                                display: block;
                                width: 100%;
                                padding: 10px;
                                margin: 5px 0;
                                border: 1px solid #ddd;
                                border-radius: 4px;
                                background: #f8f9fa;
                                cursor: pointer;
                                text-align: left;
                                transition: background 0.2s;
                            "
                            onmouseover="this.style.background='#e9ecef'"
                            onmouseout="this.style.background='#f8f9fa'">
                        <strong>${user.document}</strong> - ${user.name}<br>
                        <small style="color: #666;">Roles: ${user.roles.join(', ')}</small>
                    </button>
                `).join('')}
            </div>
            <button onclick="document.body.removeChild(this.closest('[style*=\"position: fixed\"]'))" 
                    style="
                        background: #6c757d;
                        color: white;
                        border: none;
                        padding: 8px 16px;
                        border-radius: 4px;
                        cursor: pointer;
                        float: right;
                    ">
                Cerrar
            </button>
            <div style="clear: both;"></div>
        `;

        modal.appendChild(content);
        document.body.appendChild(modal);

        // Cerrar al hacer clic fuera
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                document.body.removeChild(modal);
            }
        });
    }

    /**
     * Rellena automáticamente las credenciales
     */
    fillCredentials(document) {
        const documentInput = document.getElementById('documentValue');
        const passwordInput = document.getElementById('password');
        
        if (documentInput && passwordInput) {
            documentInput.value = document;
            passwordInput.value = document === '12345678' ? '123456' : 'Password@123';
            
            // Cerrar modal si está abierto
            const modal = document.querySelector('[style*="position: fixed"]');
            if (modal) {
                document.body.removeChild(modal);
            }
            
            // Enfocar el botón de login
            this.submitBtn.focus();
            
            console.log('✅ Credenciales rellenadas automáticamente');
        }
    }

    /**
     * Configura auto-relleno rápido con teclas
     */
    setupAutoFill() {
        document.addEventListener('keydown', (e) => {
            // Ctrl + 1, 2, 3 para rellenar usuarios rápidamente
            if (e.ctrlKey) {
                const mockUsers = this.loginService.getMockUsers();
                let userIndex = -1;
                
                switch(e.key) {
                    case '1':
                        userIndex = 0;
                        break;
                    case '2':
                        userIndex = 1;
                        break;
                    case '3':
                        userIndex = 2;
                        break;
                }
                
                if (userIndex >= 0 && userIndex < mockUsers.length) {
                    e.preventDefault();
                    this.fillCredentials(mockUsers[userIndex].document);
                }
            }
        });
    }

    /**
     * Configura los event listeners
     */
    setupEventListeners() {
        // Prevenir comportamiento por defecto del formulario
        this.form.addEventListener('submit', async (e) => {
            e.preventDefault();
            await this.handleLogin();
        });
    }

    /**
     * Configura la funcionalidad para mostrar/ocultar contraseña
     */
    setupPasswordToggle() {
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');
        
        if (passwordInput && eyeIcon) {
            eyeIcon.addEventListener('click', (e) => {
                e.preventDefault();
                
                const isPassword = passwordInput.getAttribute('type') === 'password';
                
                passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                eyeIcon.classList.toggle('fa-eye', !isPassword);
                eyeIcon.classList.toggle('fa-eye-slash', isPassword);
                eyeIcon.setAttribute('title', isPassword ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
            
            eyeIcon.setAttribute('title', 'Mostrar contraseña');
        }
    }

    /**
     * Configura la validación del campo de documento
     */
    setupDocumentValidation() {
        const documentInput = document.getElementById('documentValue');
        
        if (documentInput) {
            documentInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/[^0-9]/g, '');
                if (value.length > 8) {
                    value = value.slice(0, 8);
                }
                e.target.value = value;
            });
            
            documentInput.addEventListener('paste', (e) => {
                e.preventDefault();
                const paste = (e.clipboardData || window.clipboardData).getData('text');
                const filteredPaste = paste.replace(/[^0-9]/g, '').slice(0, 8);
                documentInput.value = filteredPaste;
            });
            
            documentInput.addEventListener('keypress', (e) => {
                if (e.key === 'Backspace' || e.key === 'Delete' || e.key === 'Tab' || 
                    e.key === 'ArrowLeft' || e.key === 'ArrowRight' || e.key === 'Home' || e.key === 'End') {
                    return;
                }
                
                if (!/^[0-9]$/.test(e.key)) {
                    e.preventDefault();
                }
                
                if (documentInput.value.length >= 8) {
                    e.preventDefault();
                }
            });
        }
    }

    setLoading(isLoading) {
        this.submitBtn.disabled = isLoading;
        this.btnText.style.display = isLoading ? 'none' : 'inline';
        this.btnLoading.style.display = isLoading ? 'inline' : 'none';
    }

    async handleLogin() {
        const documentInput = document.getElementById('documentValue');
        const passwordInput = document.getElementById('password');
        
        if (!documentInput || !passwordInput) {
            this.showError("Error interno: formulario no disponible.");
            return;
        }
        
        const documentValue = documentInput.value.trim();
        const password = passwordInput.value;

        // Validaciones
        if (!documentValue) {
            this.showError("Por favor, ingresa tu número de documento.");
            return;
        }
        
        if (!/^[0-9]{8}$/.test(documentValue)) {
            this.showError("El documento debe contener exactamente 8 dígitos numéricos.");
            return;
        }

        try {
            this.setLoading(true);
            console.log('🔐 MockLoginController: Intentando login:', { documento: documentValue });
            
            const response = await this.loginService.verifyCredentials(documentValue, password);
            
            console.log('📋 MockLoginController: Respuesta recibida:', response);

            if (response && response.token) {
                console.log('✅ Login exitoso en modo mock');
                
                // Limpiar sessionStorage
                sessionStorage.clear();
                
                // Guardar datos de autenticación
                sessionStorage.setItem("isLoggedIn", "true");
                sessionStorage.setItem("authToken", response.token);
                sessionStorage.setItem("userId", response.userId?.toString() || "");
                sessionStorage.setItem("documentValue", response.documentValue || documentValue);
                sessionStorage.setItem("userRoles", JSON.stringify(response.roles || []));
                
                const permissionNames = (response.permissions || []).map(p => p.name);
                sessionStorage.setItem("userPermissions", JSON.stringify(permissionNames));
                sessionStorage.setItem("userAvailability", response.availability?.toString() ?? "true");
                
                sessionStorage.setItem("firstName", response.firstName || "");
                sessionStorage.setItem("lastName", response.lastName || "");
                sessionStorage.setItem("userFullName", `${response.firstName || ""} ${response.lastName || ""}`.trim());
                sessionStorage.setItem("userRating", response.rating?.toString() || "0");
                sessionStorage.setItem("loginTime", Date.now().toString());

                // Redirigir
                this.handleRedirect();
                
            } else {
                this.showError("Error en la respuesta del servidor simulado.");
            }

        } catch (error) {
            console.error('❌ Error durante el login mock:', error);
            
            let errorMessage = "Error inesperado en el servicio simulado.";
            
            if (error.message) {
                const msg = error.message.toLowerCase();
                if (msg.includes('documento o contraseña') || msg.includes('credenciales')) {
                    errorMessage = "Las credenciales ingresadas no son válidas. Prueba con los usuarios de prueba mostrados arriba.";
                } else {
                    errorMessage = error.message;
                }
            }
            
            this.showError(errorMessage);
            
        } finally {
            this.setLoading(false);
        }
    }

    /**
     * Maneja la redirección después del login
     */
    handleRedirect() {
        // Cargar PermissionsService si está disponible
        if (typeof window.PermissionsService !== 'undefined') {
            try {
                const authorizedRoute = window.PermissionsService.getFirstAvailableRoute();
                console.log(`✅ Redirigiendo a: ${authorizedRoute}`);
                window.history.pushState(null, '', window.location.href);
                window.location.replace(`${this.baseUrl}${authorizedRoute}`);
                return;
            } catch (error) {
                console.error('❌ Error con PermissionsService:', error);
            }
        }
        
        // Redirección por defecto
        console.log('🔄 Usando redirección por defecto al inicio');
        window.history.pushState(null, '', window.location.href);
        window.location.replace(`${this.baseUrl}/pages/Inicio/Inicio.html`);
    }

    showError(message) {
        this.errorMsg.textContent = message;
        this.errorMsg.style.display = 'block';
        setTimeout(() => {
            this.errorMsg.style.display = 'none';
        }, 6000);
    }
}

// Variable global para el controlador
let mockLoginController;

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    mockLoginController = new MockLoginController();
    
    // Hacer disponible globalmente para las funciones inline
    window.mockLoginController = mockLoginController;
});

console.log('✅ MockLoginController cargado');
