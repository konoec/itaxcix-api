class SidebarController {    constructor() {
        console.log('🏗️ Construyendo SidebarController...');
        this.sidebar = document.getElementById('sidebar');
        this.closeButton = document.getElementById('close-sidebar');
        this.logoutButton = document.querySelector('.logout-btn');
        
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
        if (window.innerWidth <= 768) {
            // Asegurar que el sidebar comience colapsado en móviles
            if (this.sidebar && !this.sidebar.classList.contains('collapsed')) {
                this.sidebar.classList.add('collapsed');
                console.log('📱 Sidebar inicializado como colapsado en móvil');
            }
        } else {
            // En desktop, asegurar que no esté colapsado
            if (this.sidebar && this.sidebar.classList.contains('collapsed')) {
                this.sidebar.classList.remove('collapsed');
                console.log('🖥️ Sidebar inicializado como expandido en desktop');
            }
        }

        // Escuchar cambios de tamaño de ventana
        window.addEventListener('resize', () => {
            if (window.innerWidth <= 768) {
                // Cambió a móvil - colapsar
                if (this.sidebar && !this.sidebar.classList.contains('collapsed')) {
                    this.sidebar.classList.add('collapsed');
                }
            } else {
                // Cambió a desktop - expandir
                if (this.sidebar && this.sidebar.classList.contains('collapsed')) {
                    this.sidebar.classList.remove('collapsed');
                }
            }
        });
    }

    setupEventListeners() {
        // Manejador para cerrar/abrir sidebar
        if (this.closeButton) {
            this.closeButton.addEventListener('click', () => {
                this.sidebar.classList.toggle('collapsed');
            });
        }        // Evento para cerrar sesión
        if (this.logoutButton) {
            this.logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
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
                    // Fallback manual
                    sessionStorage.clear();
                    localStorage.clear();
                    window.location.replace("../../index.html");
                }
            });
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
    toggle() {
        console.log('🔄 Toggle sidebar llamado');
        console.log('🔍 Sidebar element:', this.sidebar);
        
        if (this.sidebar) {
            const wasCollapsed = this.sidebar.classList.contains('collapsed');
            this.sidebar.classList.toggle('collapsed');
            const isCollapsed = this.sidebar.classList.contains('collapsed');
            
            console.log(`� Sidebar cambió de ${wasCollapsed ? 'colapsado' : 'expandido'} a ${isCollapsed ? 'colapsado' : 'expandido'}`);
            console.log('🎨 Clases CSS actuales:', this.sidebar.className);
        } else {
            console.error('❌ Sidebar element not found in toggle()');
        }
    }

    /**
     * Método para abrir el sidebar
     */
    open() {
        if (this.sidebar) {
            this.sidebar.classList.remove('collapsed');
            console.log('📂 Sidebar opened');
        }
    }

    /**
     * Método para cerrar el sidebar
     */
    close() {
        if (this.sidebar) {
            this.sidebar.classList.add('collapsed');
            console.log('📁 Sidebar closed');
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