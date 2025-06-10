class SidebarController {
    constructor() {
        this.sidebar = document.getElementById('sidebar');
        this.closeButton = document.getElementById('close-sidebar');
        this.logoutButton = document.querySelector('.logout-btn');
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.initializeSubmenu(); // AGREGAR ESTA LÍNEA
    }

    setupEventListeners() {
        // Manejador para cerrar/abrir sidebar
        if (this.closeButton) {
            this.closeButton.addEventListener('click', () => {
                this.sidebar.classList.toggle('collapsed');
            });
        }

        // Evento para cerrar sesión
        if (this.logoutButton) {
            this.logoutButton.addEventListener('click', function(e) {
                e.preventDefault();
                routeGuard.logout();
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
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    if (!window.sidebarControllerInstance) {
        window.sidebarControllerInstance = new SidebarController();
    }
});