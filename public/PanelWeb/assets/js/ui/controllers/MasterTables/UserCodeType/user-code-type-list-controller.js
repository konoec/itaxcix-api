// Función debounce para optimizar las búsquedas
function debounce(func, wait) {
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

// Controlador para la lista de tipos de código de usuario
class UserCodeTypeListController {
    constructor() {
        console.log('🏗️ Inicializando UserCodeTypeListController');
        this.tableBody = document.getElementById('userCodeTypeTableBody');
        this.stats = {
            total: document.getElementById('totalUserCodeTypes'),
            filtered: document.getElementById('filteredResults'),
            page: document.getElementById('currentPage'),
            range: document.getElementById('itemsRange'),
            totalFooter: document.getElementById('totalUserCodeTypesFooter'),
            rangeFooter: document.getElementById('itemsRangeFooter')
        };
        this.filters = {
            search: document.getElementById('searchInput'),
            name: document.getElementById('nameFilter'),
            active: document.getElementById('activeFilter'),
            perPage: document.getElementById('perPageSelect'),
            sortBy: document.getElementById('sortBySelect'),
            sortDirection: document.getElementById('sortOrderSelect')
        };
        this.pagination = document.getElementById('paginationContainer');
        this.refreshBtn = document.getElementById('refreshBtn');
        this.clearBtn = document.getElementById('clearFiltersBtn');
        
        console.log('📊 Elementos encontrados:', {
            tableBody: !!this.tableBody,
            stats: Object.keys(this.stats).filter(k => !!this.stats[k]),
            filters: Object.keys(this.filters).filter(k => !!this.filters[k]),
            pagination: !!this.pagination,
            refreshBtn: !!this.refreshBtn,
            clearBtn: !!this.clearBtn
        });
        
        this.initEvents();
        setTimeout(() => this.load(), 100); // Pequeño delay para asegurar que el DOM esté listo
    }
    initEvents() {
        console.log('🎯 Inicializando eventos del controlador');
        Object.values(this.filters).forEach(input => {
            if (input) {
                input.addEventListener('change', () => this.load(1));
                // Para inputs de texto, también escuchar el evento 'input'
                if (input.type === 'text') {
                    input.addEventListener('input', debounce(() => this.load(1), 300));
                }
            }
        });
        if (this.refreshBtn) this.refreshBtn.addEventListener('click', () => this.load());
        if (this.clearBtn) this.clearBtn.addEventListener('click', () => this.clearFilters());
        
        // Limpiar búsqueda
        const clearSearchBtn = document.getElementById('clearSearchBtn');
        if (clearSearchBtn) {
            clearSearchBtn.addEventListener('click', () => {
                this.filters.search.value = '';
                this.load(1);
            });
        }

        // Event delegation para botones de editar y eliminar
        this.tableBody.addEventListener('click', (e) => {
            if (e.target.closest('.edit-user-code-type-btn')) {
                const btn = e.target.closest('.edit-user-code-type-btn');
                const id = parseInt(btn.getAttribute('data-id'), 10);
                this.handleEditClick(id);
            } else if (e.target.closest('.delete-user-code-type-btn')) {
                const btn = e.target.closest('.delete-user-code-type-btn');
                const id = parseInt(btn.getAttribute('data-id'), 10);
                const name = btn.getAttribute('data-name');
                this.handleDeleteClick(id, name, btn);
            }
        });

        // Configurar ícono dinámico del filtro activo
        this.setupActiveFilterIcon();

        console.log('✅ Eventos inicializados');
    }
    async load(page = 1) {
        console.log('🔄 Cargando tipos de código de usuario, página:', page);
        const params = {
            page,
            perPage: this.filters.perPage.value,
            search: this.filters.search.value,
            name: this.filters.name.value,
            active: this.filters.active.value === '' ? undefined : this.filters.active.value === 'true',
            sortBy: this.filters.sortBy.value,
            sortDirection: this.filters.sortDirection.value
        };
        console.log('📋 Parámetros de búsqueda:', params);
        this.setLoading(true);
        try {
            const res = await UserCodeTypeService.getUserCodeTypes(params);
            console.log('✅ Respuesta de la API:', res);
            this.render(res.data);
        } catch (err) {
            console.error('❌ Error cargando datos:', err);
            this.tableBody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${err.message}</td></tr>`;
        }
        this.setLoading(false);
    }
    render(data) {
        console.log('🎨 Renderizando datos:', data);
        const items = data.items || [];
        console.log('📋 Items a renderizar:', items);
        
        // Almacenar items para uso posterior
        this.currentItems = items;
        
        this.tableBody.innerHTML = items.length ? items.map((s, i) => `
            <tr>
                <td class="text-center">${s.id}</td>
                <td>${s.name}</td>
                <td class="text-center">
                    <span class="badge bg-${s.active ? 'success' : 'danger'}-lt">
                        <i class="fas fa-${s.active ? 'check-circle text-success' : 'times-circle text-danger'}"></i> ${s.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-outline-warning btn-sm me-1 edit-user-code-type-btn" title="Editar" data-id="${s.id}"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-outline-red delete-user-code-type-btn" title="Eliminar" data-id="${s.id}" data-name="${s.name}"><i class="fas fa-trash text-red"></i></button>
                </td>
            </tr>
        `).join('') : `<tr><td colspan="5" class="text-center text-muted">No hay resultados</td></tr>`;
        // Stats
        this.stats.total.textContent = data.pagination?.total || 0;
        this.stats.filtered.textContent = items.length || 0;
        this.stats.page.textContent = data.pagination?.page || 1;
        const start = ((data.pagination?.page - 1) * data.pagination?.perPage) + 1;
        const end = start + (items.length || 0) - 1;
        this.stats.range.textContent = `${start} - ${end}`;
        
        // Stats en el footer
        if (this.stats.totalFooter) this.stats.totalFooter.textContent = data.pagination?.total || 0;
        if (this.stats.rangeFooter) this.stats.rangeFooter.textContent = `${start} - ${end}`;
        
        // Paginación
        this.renderPagination(data.pagination);
        console.log('✅ Renderizado completado');
    }
    renderPagination(meta) {
        if (!meta) return;
        const { page, totalPages } = meta;
        let html = '';
        for (let i = 1; i <= totalPages; i++) {
            html += `<li class="page-item${i === page ? ' active' : ''}"><button class="page-link" data-page="${i}">${i}</button></li>`;
        }
        this.pagination.innerHTML = html;
        
        // Agregar event listeners a los botones de paginación
        this.pagination.querySelectorAll('.page-link').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                const pageNum = parseInt(e.target.getAttribute('data-page'));
                this.load(pageNum);
            });
        });
    }
    setLoading(loading) {
        if (loading) {
            this.tableBody.innerHTML = `<tr><td colspan="5" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>`;
        }
    }
    clearFilters() {
        Object.values(this.filters).forEach(input => { if (input) input.value = ''; });
        this.load(1);
    }

    handleEditClick(id) {
        console.log('🔄 Editando UserCodeType con ID:', id);
        
        // Buscar los datos del item en los datos actuales
        const userCodeTypeData = this.currentItems?.find(item => item.id == id);
        
        if (!userCodeTypeData) {
            console.error('❌ No se encontraron datos para el ID:', id);
            window.GlobalToast?.show('Error: No se encontraron datos del tipo de código de usuario', 'error');
            return;
        }
        
        if (window.updateUserCodeTypeController) {
            // Pasar tanto el ID como los datos para evitar llamada a la API
            window.updateUserCodeTypeController.showEditModal(id, userCodeTypeData);
        } else {
            console.error('❌ updateUserCodeTypeController no está disponible');
            window.GlobalToast?.show('Error: Controlador de edición no disponible', 'error');
        }
    }

    /**
     * Maneja el click en el botón de eliminar
     * @param {string} id - ID del tipo de código de usuario
     * @param {string} name - Nombre del tipo de código de usuario
     * @param {HTMLElement} button - Botón que disparó la acción
     */
    handleDeleteClick(id, name, button) {
        console.log('🗑️ Eliminando UserCodeType con ID:', id, 'Nombre:', name);
        
        // Crear datos del tipo para el controlador de eliminación
        const userCodeTypeData = {
            id: parseInt(id),
            name: name
        };
        
        // Verificar que el controlador de eliminación esté disponible
        if (window.deleteUserCodeTypeController) {
            // Usar el controlador de eliminación con modal de confirmación
            window.deleteUserCodeTypeController.handleDeleteButtonClick(button, userCodeTypeData);
        } else if (window.DeleteUserCodeTypeController) {
            // Crear instancia si no existe
            window.deleteUserCodeTypeController = new window.DeleteUserCodeTypeController();
            window.deleteUserCodeTypeController.handleDeleteButtonClick(button, userCodeTypeData);
        } else {
            console.error('❌ Controlador de eliminación no disponible');
            window.GlobalToast?.show('Error: Funcionalidad de eliminación no disponible', 'error');
        }
    }

    /**
     * Configura el cambio dinámico del ícono del filtro de estado
     */
    setupActiveFilterIcon() {
        const activeFilter = document.getElementById('activeFilter');
        const activeFilterIcon = document.getElementById('activeFilterIcon');
        if (activeFilter && activeFilterIcon) {
            activeFilter.addEventListener('change', () => {
                this.updateActiveFilterIcon(activeFilter.value, activeFilterIcon);
            });
            // Establecer el ícono inicial
            this.updateActiveFilterIcon(activeFilter.value, activeFilterIcon);
            console.log('✅ Ícono de filtro de estado configurado');
        }
    }

    /**
     * Actualiza el ícono del filtro de estado según el valor seleccionado
     * @param {string} value - Valor del filtro ('true', 'false', o '')
     * @param {HTMLElement} iconElement - Elemento del ícono a actualizar
     */
    updateActiveFilterIcon(value, iconElement) {
        if (value === 'true') {
            iconElement.className = 'fas fa-toggle-on me-1 text-primary';
        } else if (value === 'false') {
            iconElement.className = 'fas fa-toggle-off me-1 text-secondary';
        } else {
            iconElement.className = 'fas fa-circle-half-stroke me-1 text-primary';
        }
    }
}

// NO INSTANCIAR AUTOMÁTICAMENTE AQUÍ
// window.UserCodeTypeListController = new UserCodeTypeListController();
