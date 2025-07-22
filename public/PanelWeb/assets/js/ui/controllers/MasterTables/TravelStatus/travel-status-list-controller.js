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

// Controlador para la lista de estados de viaje (TravelStatus)
class TravelStatusListController {
    constructor() {
        console.log('🏗️ Inicializando TravelStatusListController');
        this.tableBody = document.getElementById('travelStatusTableBody');
        this.stats = {
            total: document.getElementById('totalTravelStatuses'),
            filtered: document.getElementById('filteredResults'),
            page: document.getElementById('currentPage'),
            range: document.getElementById('itemsRange'),
            totalFooter: document.getElementById('totalTravelStatusesFooter'),
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
        console.log('🎯 Inicializando eventos del controlador TravelStatus');
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
            if (e.target.closest('.edit-travel-status-btn')) {
                const btn = e.target.closest('.edit-travel-status-btn');
                const id = btn.getAttribute('data-id');
                this.handleEditClick(id);
            } else if (e.target.closest('.delete-travel-status-btn')) {
                const btn = e.target.closest('.delete-travel-status-btn');
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                this.handleDeleteClick(id, name, btn);
            }
        });

        console.log('✅ Eventos TravelStatus inicializados');
    }

    async load(page = 1) {
        console.log('🔄 Cargando estados de viaje, página:', page);
        const params = {
            page,
            perPage: this.filters.perPage.value,
            search: this.filters.search.value,
            name: this.filters.name.value,
            active: this.filters.active.value === '' ? undefined : this.filters.active.value === 'true',
            sortBy: this.filters.sortBy.value,
            sortDirection: this.filters.sortDirection.value
        };
        
        console.log('📋 Parámetros de búsqueda TravelStatus:', params);
        this.setLoading(true);
        
        try {
            const res = await TravelStatusService.getTravelStatuses(params);
            console.log('✅ Respuesta de la API TravelStatus:', res);
            this.render(res.data);
        } catch (err) {
            console.error('❌ Error cargando datos TravelStatus:', err);
            this.tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${err.message}</td></tr>`;
        }
        
        this.setLoading(false);
    }

    render(data) {
        console.log('🎨 Renderizando datos TravelStatus:', data);
        const items = data.data || [];
        const pagination = data.pagination || {};
        console.log('📋 Items TravelStatus a renderizar:', items);
        
        // Almacenar items para uso posterior
        this.currentItems = items;
        
        this.tableBody.innerHTML = items.length ? items.map((ts, i) => `
            <tr>
                <td class="text-center">${ts.id}</td>
                <td>${ts.name}</td>
                <td class="text-center">
                    <span class="badge bg-${ts.active ? 'success' : 'danger'}-lt">
                        <i class="fas fa-${ts.active ? 'check-circle text-success' : 'times-circle text-danger'}"></i> ${ts.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-outline-warning btn-sm me-1 edit-travel-status-btn" title="Editar" data-id="${ts.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-red delete-travel-status-btn" title="Eliminar" data-id="${ts.id}" data-name="${ts.name}">
                        <i class="fas fa-trash text-red"></i>
                    </button>
                </td>
            </tr>
        `).join('') : `<tr><td colspan="4" class="text-center text-muted">No hay resultados</td></tr>`;
        
        // Stats
        this.stats.total.textContent = pagination.total_items || 0;
        this.stats.filtered.textContent = items.length || 0;
        this.stats.page.textContent = pagination.current_page || 1;
        
        const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
        const end = start + (items.length || 0) - 1;
        this.stats.range.textContent = items.length > 0 ? `${start} - ${end}` : '0';
        
        // Stats en el footer
        if (this.stats.totalFooter) this.stats.totalFooter.textContent = pagination.total_items || 0;
        if (this.stats.rangeFooter) this.stats.rangeFooter.textContent = items.length > 0 ? `${start} - ${end}` : '0';
        
        // Paginación
        this.renderPagination(pagination);
        console.log('✅ Renderizado TravelStatus completado');
    }

    renderPagination(pagination) {
        if (!pagination || !this.pagination) return;
        
        const { current_page, total_pages } = pagination;
        let html = '';
        
        // Generar botones de paginación
        for (let i = 1; i <= total_pages; i++) {
            html += `<li class="page-item${i === current_page ? ' active' : ''}">
                        <button class="page-link" data-page="${i}">${i}</button>
                     </li>`;
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
            this.tableBody.innerHTML = `<tr><td colspan="4" class="text-center py-4"><div class="spinner-border text-primary"></div></td></tr>`;
        }
    }

    clearFilters() {
        Object.values(this.filters).forEach(input => { 
            if (input) input.value = ''; 
        });
        this.load(1);
    }

    handleEditClick(id) {
        console.log('🔄 Editando TravelStatus con ID:', id);
        
        // Buscar los datos del item en los datos actuales
        const travelStatusData = this.currentItems?.find(item => item.id == id);
        
        if (!travelStatusData) {
            console.error('❌ No se encontraron datos para el ID:', id);
            GlobalToast.show('Error: No se encontraron datos del estado de viaje', 'error');
            return;
        }
        
        if (window.updateTravelStatusController) {
            // Pasar tanto el ID como los datos para evitar llamada a la API
            if (typeof window.updateTravelStatusController.openEditModal === 'function') {
                window.updateTravelStatusController.openEditModal(id, travelStatusData);
            } else {
                console.error('❌ updateTravelStatusController no tiene el método openEditModal');
                GlobalToast.show('Error: El controlador de edición no tiene el método correcto', 'error');
            }
        } else {
            console.error('❌ updateTravelStatusController no está disponible');
            GlobalToast.show('Error: Controlador de edición no disponible', 'error');
        }
    }

    handleDeleteClick(id, name, button) {
        console.log('🗑️ Eliminando TravelStatus con ID:', id, 'Nombre:', name);
        // Crear datos del estado de viaje para el controlador de eliminación
        const travelStatusData = {
            id: parseInt(id, 10),
            name: name
        };
        // Verificar que el controlador de eliminación esté disponible
        if (window.deleteTravelStatusController) {
            window.deleteTravelStatusController.handleDeleteButtonClick(button, travelStatusData);
        } else if (window.DeleteTravelStatusController) {
            // Crear instancia si no existe
            window.deleteTravelStatusController = new window.DeleteTravelStatusController();
            window.deleteTravelStatusController.handleDeleteButtonClick(button, travelStatusData);
        } else {
            console.error('❌ Controlador de eliminación no disponible');
            GlobalToast.show('Error: Funcionalidad de eliminación no disponible', 'error');
        }
    }

    // Método para refrescar después de crear/editar/eliminar
    refresh() {
        this.load();
    }
}

window.TravelStatusListController = TravelStatusListController;
