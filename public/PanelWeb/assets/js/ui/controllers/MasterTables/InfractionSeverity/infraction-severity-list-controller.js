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

// Controlador para la lista de severidades de infracciones (InfractionSeverity)
class InfractionSeverityListController {
    constructor() {
        console.log('🏗️ Inicializando InfractionSeverityListController');
        this.tableBody = document.getElementById('infractionSeverityTableBody');
        this.stats = {
            total: document.getElementById('totalInfractionSeverities'),
            filtered: document.getElementById('filteredResults'),
            page: document.getElementById('currentPage'),
            range: document.getElementById('itemsRange'),
            totalFooter: document.getElementById('totalInfractionSeveritiesFooter'),
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
        console.log('🎯 Inicializando eventos del controlador InfractionSeverity');
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
            if (e.target.closest('.edit-infraction-severity-btn')) {
                const btn = e.target.closest('.edit-infraction-severity-btn');
                const id = btn.getAttribute('data-id');
                this.handleEditClick(id);
            } else if (e.target.closest('.delete-infraction-severity-btn')) {
                const btn = e.target.closest('.delete-infraction-severity-btn');
                const id = btn.getAttribute('data-id');
                const name = btn.getAttribute('data-name');
                this.handleDeleteClick(id, name, btn);
            }
        });

        console.log('✅ Eventos InfractionSeverity inicializados');
    }

    async load(page = 1) {
        console.log('🔄 Cargando severidades de infracciones, página:', page);
        const params = {
            page,
            perPage: this.filters.perPage.value,
            search: this.filters.search.value,
            name: this.filters.name.value,
            active: this.filters.active.value === '' ? undefined : this.filters.active.value === 'true',
            sortBy: this.filters.sortBy.value,
            sortDirection: this.filters.sortDirection.value
        };
        
        console.log('📋 Parámetros de búsqueda InfractionSeverity:', params);
        this.setLoading(true);
        
        try {
            const res = await InfractionSeverityService.getInfractionSeverities(params);
            console.log('✅ Respuesta de la API InfractionSeverity:', res);
            this.render(res.data);
        } catch (err) {
            console.error('❌ Error cargando datos InfractionSeverity:', err);
            this.tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${err.message}</td></tr>`;
        }
        
        this.setLoading(false);
    }

    render(data) {
        console.log('🎨 Renderizando datos InfractionSeverity:', data);
        const items = data.data || [];
        const pagination = data.pagination || {};
        console.log('📋 Items InfractionSeverity a renderizar:', items);
        
        // Almacenar items para uso posterior
        this.currentItems = items;
        
        this.tableBody.innerHTML = items.length ? items.map((is, i) => `
            <tr>
                <td class="text-center">${is.id}</td>
                <td>${is.name}</td>
                <td class="text-center">
                    <span class="badge bg-${is.active ? 'success' : 'danger'}-lt">
                        <i class="fas fa-${is.active ? 'check-circle text-success' : 'times-circle text-danger'}"></i> ${is.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-outline-warning btn-sm me-1 edit-infraction-severity-btn" title="Editar" data-id="${is.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-red delete-infraction-severity-btn" title="Eliminar" data-id="${is.id}" data-name="${is.name}">
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
        console.log('✅ Renderizado InfractionSeverity completado');
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
        console.log('🔄 Editando InfractionSeverity con ID:', id);
        
        // Buscar los datos del item en los datos actuales
        const infractionSeverityData = this.currentItems?.find(item => item.id == id);
        
        if (!infractionSeverityData) {
            console.error('❌ No se encontraron datos para el ID:', id);
            GlobalToast.show('Error: No se encontraron datos de la severidad de infracción', 'error');
            return;
        }
        
        // Usar el controlador de edición global
        if (window.infractionSeverityEditController) {
            window.infractionSeverityEditController.openEditModal(id, infractionSeverityData);
        } else {
            window.GlobalToast?.showErrorToast('Controlador de edición no disponible');
        }
    }

    handleDeleteClick(id, name, button) {
        console.log('🗑️ Eliminando InfractionSeverity con ID:', id, 'Nombre:', name);
        
        if (window.deleteInfractionSeverityController) {
            window.deleteInfractionSeverityController.showDeleteModal(id, name, button);
        } else {
            console.error('❌ deleteInfractionSeverityController no está disponible');
            GlobalToast.show('Error: Controlador de eliminación no disponible', 'error');
        }
    }

    // Método para refrescar después de crear/editar/eliminar
    refresh() {
        this.load();
    }
}

window.InfractionSeverityListController = InfractionSeverityListController;
