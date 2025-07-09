class SidebarController {    constructor() {
        console.log('🏗️ Construyendo SidebarController...');
        this.sidebar = document.getElementById('sidebar');
        this.closeButton = document.getElementById('sidebar-close');
        this.logoutButton = document.getElementById('logout-btn');
        
        console.log('🔍 Elementos encontrados:');
        console.log('  - Sidebar:', this.sidebar);
        console.log('  - Close button:', this.closeButton);
        console.log('  - Logout button:', this.logoutButton);
        
        // Verificar estado de routeGuard
        console.log('🔍 Estado de RouteGuard:');
        console.log('  - window.routeGuard:', typeof window.routeGuard);
        console.log('  - RouteGuard class:', typeof RouteGuard);
        
        this.init();
    }init() {
        this.setupEventListeners();
        this.initializeSubmenu();
        this.setupCustomEventListeners(); // Agregar listeners para eventos personalizados
        this.initializeMobileState(); // Inicializar estado móvil
    }

    /**
     * Inicializa el estado del sidebar para móviles
     */
    initializeMobileState() {
        // Detectar si estamos en móvil
        if (window.innerWidth <= 991.98) {
            // Móvil: asegurar que el sidebar comience cerrado (sin .show)
            if (this.sidebar && this.sidebar.classList.contains('show')) {
                this.sidebar.classList.remove('show');
                console.log('📱 Sidebar inicializado como cerrado en móvil');
            }
        } else {
            // Desktop: asegurar que no esté oculto
            if (this.sidebar && this.sidebar.classList.contains('hidden')) {
                this.sidebar.classList.remove('hidden');
                const pageWrapper = document.querySelector('.page-wrapper');
                if (pageWrapper) {
                    pageWrapper.classList.remove('sidebar-hidden');
                }
                console.log('🖥️ Sidebar inicializado como visible en desktop');
            }
        }

        // Escuchar cambios de tamaño de ventana
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 991.98) {
                // Cambió a móvil - cerrar si está abierto
                if (this.sidebar && this.sidebar.classList.contains('show')) {
                    this.sidebar.classList.remove('show');
                }
                // Remover clases de desktop
                if (this.sidebar && this.sidebar.classList.contains('hidden')) {
                    this.sidebar.classList.remove('hidden');
                }
                const pageWrapper = document.querySelector('.page-wrapper');
                if (pageWrapper && pageWrapper.classList.contains('sidebar-hidden')) {
                    pageWrapper.classList.remove('sidebar-hidden');
                }
            } else {
                // Cambió a desktop - remover clases de móvil
                if (this.sidebar && this.sidebar.classList.contains('show')) {
                    this.sidebar.classList.remove('show');
                }
            }
        });
    }

    setupEventListeners() {
        // Backup: Agregar listener directo al botón hamburguesa
        // En caso de que TopBarController no funcione correctamente
        const sidebarToggle = document.getElementById('sidebar-toggle');
        if (sidebarToggle) {
            // Listener principal 
            sidebarToggle.addEventListener('click', (e) => {
                console.log('🍔 Click en botón hamburguesa');
                this.toggle();
            });
            
            // Listener adicional con captura para interceptar el evento antes que otros
            sidebarToggle.addEventListener('click', (e) => {
                console.log('🍔 Click capturado en botón hamburguesa (capture phase)');
                this.toggle();
            }, true); // true = capture phase
            
            console.log('🔧 Listeners backup configurados para botón hamburguesa');
        }
        
        // Manejador para cerrar sidebar (botón X en móviles)
        if (this.closeButton) {
            this.closeButton.addEventListener('click', (e) => {
                e.preventDefault();
                console.log('❌ Click en botón cerrar sidebar');
                this.close();
            });
        }

        // Evento para cerrar sesión
        if (this.logoutButton) {
            console.log('🔗 Configurando evento de logout...');
            this.logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🚪 Botón de logout clicado');
                
                // Verificar si routeGuard está disponible
                if (typeof window.routeGuard !== 'undefined' && window.routeGuard) {
                    console.log('✅ RouteGuard encontrado, ejecutando logout...');
                    window.routeGuard.logout();
                } else if (typeof RouteGuard !== 'undefined') {
                    console.log('✅ Clase RouteGuard encontrada, creando instancia...');
                    const tempGuard = new RouteGuard("../../index.html");
                    tempGuard.logout();
                } else {
                    console.log('⚠️ RouteGuard no encontrado, ejecutando logout manual...');
                    // Fallback manual más robusto
                    try {
                        // Limpiar almacenamiento
                        sessionStorage.clear();
                        localStorage.clear();
                        
                        // Mostrar mensaje de logout si hay sistema de toast
                        if (typeof window.showRecoveryToast === 'function') {
                            window.showRecoveryToast('Cerrando sesión...', 'info');
                        }
                        
                        // Redirigir después de un breve delay
                        setTimeout(() => {
                            window.location.replace("../../index.html");
                        }, 500);
                        
                    } catch (error) {
                        console.error('❌ Error durante logout manual:', error);
                        // Forzar redirección inmediata en caso de error
                        window.location.href = "../../index.html";
                    }
                }
            });
        } else {
            console.error('❌ Botón de logout no encontrado. Verificar que el elemento tenga ID "logout-btn"');
        }
    }

    // AGREGAR ESTE MÉTODO COMPLETO
    initializeSubmenu() {
        // Manejar click en elementos con submenu
        const submenuItems = document.querySelectorAll('.has-submenu > a');
        
        submenuItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const parentLi = item.parentElement;
                const isOpen = parentLi.classList.contains('open');
                
                // Cerrar todos los otros submenus
                document.querySelectorAll('.has-submenu.open').forEach(openItem => {
                    if (openItem !== parentLi) {
                        openItem.classList.remove('open');
                    }
                });
                
                // Toggle el submenu actual
                if (isOpen) {
                    parentLi.classList.remove('open');
                } else {
                    parentLi.classList.add('open');
                }
                
                console.log('Submenu toggled:', isOpen ? 'closed' : 'opened');
            });
        });
    }    /**
     * Método para alternar (toggle) el estado del sidebar
     * Utilizado por el TopBarController cuando se hace clic en el botón hamburguesa
     */
    /**
     * Método para alternar (toggle) el estado del sidebar
     */
    toggle() {
        console.log('🔄 Toggle sidebar');
        
        if (this.sidebar) {
            if (window.innerWidth <= 991.98) {
                // Móvil: usar clase .show
                const isShown = this.sidebar.classList.contains('show');
                this.sidebar.classList.toggle('show');
                const newState = this.sidebar.classList.contains('show');
                console.log('📱 Sidebar móvil:', newState ? 'mostrado' : 'oculto');
                
            } else {
                // Desktop: usar clase .hidden y .sidebar-hidden en page-wrapper
                const isHidden = this.sidebar.classList.contains('hidden');
                const pageWrapper = document.querySelector('.page-wrapper');
                
                if (isHidden) {
                    // Mostrar sidebar
                    this.sidebar.classList.remove('hidden');
                    if (pageWrapper) {
                        pageWrapper.classList.remove('sidebar-hidden');
                    }
                    console.log('🖥️ Sidebar desktop: mostrado');
                } else {
                    // Ocultar sidebar
                    this.sidebar.classList.add('hidden');
                    if (pageWrapper) {
                        pageWrapper.classList.add('sidebar-hidden');
                    }
                    console.log('🖥️ Sidebar desktop: oculto');
                }
            }
        } else {
            console.error('❌ Sidebar element not found');
        }
    }

    /**
     * Método para abrir el sidebar
     */
    open() {
        if (this.sidebar) {
            if (window.innerWidth <= 991.98) {
                // Móvil: agregar clase .show
                this.sidebar.classList.add('show');
                console.log('📂 Sidebar móvil abierto');
            } else {
                // Desktop: remover clase .hidden
                this.sidebar.classList.remove('hidden');
                const pageWrapper = document.querySelector('.page-wrapper');
                if (pageWrapper) {
                    pageWrapper.classList.remove('sidebar-hidden');
                }
                console.log('📂 Sidebar desktop abierto');
            }
        }
    }

    /**
     * Método para cerrar el sidebar
     */
    close() {
        if (this.sidebar) {
            if (window.innerWidth <= 991.98) {
                // Móvil: remover clase .show
                this.sidebar.classList.remove('show');
                console.log('📁 Sidebar móvil cerrado');
            } else {
                // Desktop: agregar clase .hidden
                this.sidebar.classList.add('hidden');
                const pageWrapper = document.querySelector('.page-wrapper');
                if (pageWrapper) {
                    pageWrapper.classList.add('sidebar-hidden');
                }
                console.log('📁 Sidebar desktop cerrado');
            }
        }
    }

    /**
     * Configura event listeners para eventos personalizados
     * Sirve como fallback cuando no hay referencia directa desde otros controladores
     */
    setupCustomEventListeners() {
        // Escuchar evento personalizado para toggle del sidebar
        document.addEventListener('toggleSidebar', () => {
            console.log('📡 Evento personalizado toggleSidebar recibido');
            this.toggle();
        });
    }
}

// Exponer globalmente el SidebarController
if (typeof window !== 'undefined') {
    window.SidebarController = SidebarController;
    
    // Función global de debugging específica para el botón hamburguesa
    window.debugHamburgerButton = function() {
        console.log('🔍 === DEBUGGING BOTÓN HAMBURGUESA ===');
        
        // 1. Verificar el botón hamburguesa
        const hamburgerBtn = document.getElementById('sidebar-toggle');
        console.log('🔍 Botón hamburguesa encontrado:', hamburgerBtn);
        
        if (hamburgerBtn) {
            console.log('🔍 Botón hamburguesa estilos:', {
                display: window.getComputedStyle(hamburgerBtn).display,
                visibility: window.getComputedStyle(hamburgerBtn).visibility,
                pointerEvents: window.getComputedStyle(hamburgerBtn).pointerEvents,
                zIndex: window.getComputedStyle(hamburgerBtn).zIndex
            });
            
            // Verificar listeners
            console.log('🔍 Event listeners en el botón hamburguesa:', getEventListeners(hamburgerBtn));
        }
        
        // 2. Verificar el sidebar
        const sidebar = document.getElementById('sidebar');
        console.log('🔍 Sidebar encontrado:', sidebar);
        
        if (sidebar) {
            console.log('🔍 Sidebar clases:', sidebar.classList.toString());
            console.log('🔍 Sidebar estilos:', {
                display: window.getComputedStyle(sidebar).display,
                visibility: window.getComputedStyle(sidebar).visibility,
                transform: window.getComputedStyle(sidebar).transform,
                left: window.getComputedStyle(sidebar).left,
                width: window.getComputedStyle(sidebar).width,
                position: window.getComputedStyle(sidebar).position
            });
        }
        
        // 3. Verificar controladores
        console.log('🔍 SidebarController instance:', window.sidebarControllerInstance);
        console.log('🔍 TopBarController instance:', window.topBarControllerInstance);
        
        // 4. Test manual del toggle
        console.log('🔍 === EJECUTANDO TEST MANUAL ===');
        if (window.sidebarControllerInstance) {
            try {
                window.sidebarControllerInstance.toggle();
                console.log('✅ Toggle ejecutado exitosamente');
                
                // Verificar estado después del toggle
                setTimeout(() => {
                    if (sidebar) {
                        console.log('🔍 Sidebar clases DESPUÉS del toggle:', sidebar.classList.toString());
                        console.log('🔍 Sidebar transform DESPUÉS del toggle:', window.getComputedStyle(sidebar).transform);
                    }
                }, 100);
                
            } catch (error) {
                console.error('❌ Error al ejecutar toggle:', error);
            }
        }
        
        console.log('🔍 === FIN DEBUGGING ===');
    };
    
    // Función para simular clic en el botón hamburguesa
    window.simulateHamburgerClick = function() {
        console.log('🎯 Simulando clic en botón hamburguesa...');
        const hamburgerBtn = document.getElementById('sidebar-toggle');
        
        if (hamburgerBtn) {
            hamburgerBtn.click();
            console.log('✅ Clic simulado en botón hamburguesa');
        } else {
            console.error('❌ Botón hamburguesa no encontrado');
        }
    };
}

// Función global de logout para testing y uso manual
window.forceLogout = function() {
    console.log('🚨 Logout forzado iniciado...');
    
    try {
        // Mostrar mensaje si hay sistema de toast
        if (typeof window.showRecoveryToast === 'function') {
            window.showRecoveryToast('Cerrando sesión...', 'info');
        }
        
        // Limpiar almacenamiento
        sessionStorage.clear();
        localStorage.clear();
        
        // Redirigir
        setTimeout(() => {
            window.location.replace("../../index.html");
        }, 500);
        
    } catch (error) {
        console.error('❌ Error en logout forzado:', error);
        window.location.href = "../../index.html";
    }
};

// Función global para testing del sidebar
window.testSidebar = function() {
    console.log('🧪 Testing sidebar...');
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        console.log('📐 Window width:', window.innerWidth);
        console.log('🎨 Current classes:', sidebar.className);
        console.log('🔍 Computed transform:', window.getComputedStyle(sidebar).transform);
        console.log('🔍 Computed position:', window.getComputedStyle(sidebar).position);
        console.log('🔍 Computed left:', window.getComputedStyle(sidebar).left);
        
        // Test toggle
        sidebar.classList.toggle('show');
        console.log('🔄 After toggle - classes:', sidebar.className);
        console.log('🔄 After toggle - transform:', window.getComputedStyle(sidebar).transform);
    } else {
        console.error('❌ Sidebar not found');
    }
};

// NOTA: La inicialización automática está comentada para evitar doble inicialización
// El SidebarController debe ser inicializado por el inicializador específico de cada página
/*
// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (!window.sidebarControllerInstance) {
        window.sidebarControllerInstance = new SidebarController();
    }
});
*/