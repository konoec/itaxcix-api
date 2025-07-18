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

// Controlador para la lista de tipos de documentos (DocumentTypes)
class DocumentTypesListController {
    constructor() {
        console.log('🏗️ Inicializando DocumentTypesListController');
        this.tableBody = document.getElementById('documentTypesTableBody');
        this.stats = {
            total: document.getElementById('totalDocumentTypes'),
            filtered: document.getElementById('filteredResults'),
            page: document.getElementById('currentPage'),
            range: document.getElementById('itemsRange'),
            totalFooter: document.getElementById('totalDocumentTypesFooter'),
            rangeFooter: document.getElementById('itemsRangeFooter')
        };
        this.filters = {
            search: document.getElementById('searchInput'),
            name: document.getElementById('nameFilter'),
            active: document.getElementById('activeFilter'),
            perPage: document.getElementById('perPageSelect'),
            sortBy: document.getElementById('sortBySelect'),
            sortDirection: document.getElementById('sortOrderSelect'),
            onlyActive: document.getElementById('onlyActiveFilter')
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
        console.log('🎯 Inicializando eventos del controlador DocumentTypes');
        Object.values(this.filters).forEach(input => {
            if (input) {
                input.addEventListener('change', () => this.load(1));
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

        // Delegación de eventos para editar y eliminar
        this.tableBody.addEventListener('click', (e) => {
            const editBtn = e.target.closest('.edit-document-type-btn');
            const delBtn  = e.target.closest('.delete-document-type-btn');
            if (editBtn) {
                const id = editBtn.getAttribute('data-id');
                this.handleEditClick(id);
            } else if (delBtn) {
                const id   = delBtn.getAttribute('data-id');
                const name = delBtn.getAttribute('data-name');
                this.handleDeleteClick(id, name, delBtn);
            }
        });

        console.log('✅ Eventos DocumentTypes inicializados');
    }

    async load(page = 1) {
        console.log('🔄 Cargando tipos de documentos, página:', page);
        const params = {
            page,
            perPage: this.filters.perPage.value,
            search: this.filters.search.value,
            name: this.filters.name.value,
            active: this.filters.active.value === '' ? undefined : this.filters.active.value === 'true',
            sortBy: this.filters.sortBy.value,
            sortDirection: this.filters.sortDirection.value,
            onlyActive: this.filters.onlyActive?.value === '' ? undefined : this.filters.onlyActive?.value === 'true'
        };
        
        console.log('📋 Parámetros de búsqueda DocumentTypes:', params);
        this.setLoading(true);
        
        try {
            const res = await DocumentTypesService.getDocumentTypes(params);
            console.log('✅ Respuesta de la API DocumentTypes:', res);
            this.render(res.data);
        } catch (err) {
            console.error('❌ Error cargando datos DocumentTypes:', err);
            this.tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger">${err.message}</td></tr>`;
        }
        
        this.setLoading(false);
    }

    render(data) {
        console.log('🎨 Renderizando datos DocumentTypes:', data);
        const items = data.items || [];
        this.currentItems = items;
        
        this.tableBody.innerHTML = items.length ? items.map(dt => `
            <tr>
                <td class="text-center">${dt.id}</td>
                <td>${dt.name}</td>
                <td class="text-center">
                    <span class="badge bg-${dt.active ? 'success' : 'danger'}-lt">
                        <i class="fas fa-${dt.active ? 'check-circle text-success' : 'times-circle text-danger'}"></i>
                        ${dt.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="text-center">
                    <button class="btn btn-outline-warning btn-sm me-1 edit-document-type-btn"
                            title="Editar" data-id="${dt.id}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-red delete-document-type-btn"
                            title="Eliminar" data-id="${dt.id}" data-name="${dt.name}">
                        <i class="fas fa-trash text-red"></i>
                    </button>
                </td>
            </tr>
        `).join('') : `<tr><td colspan="4" class="text-center text-muted">No hay resultados</td></tr>`;
        
        // Actualizar stats
        this.stats.total.textContent       = data.meta?.total || 0;
        this.stats.filtered.textContent    = items.length;
        this.stats.page.textContent        = data.meta?.currentPage || 1;
        const start = ((data.meta?.currentPage - 1) * data.meta?.perPage) + 1;
        const end   = start + items.length - 1;
        this.stats.range.textContent       = items.length > 0 ? `${start} - ${end}` : '0';
        if (this.stats.totalFooter) this.stats.totalFooter.textContent = data.meta?.total || 0;
        if (this.stats.rangeFooter) this.stats.rangeFooter.textContent = items.length > 0 ? `${start} - ${end}` : '0';

        // Paginación
        this.renderPagination(data.meta);
        console.log('✅ Renderizado DocumentTypes completado');
    }

    renderPagination(meta) {
        if (!meta || !this.pagination) return;
        const { currentPage, lastPage } = meta;
        let html = '';
        for (let i = 1; i <= lastPage; i++) {
            html += `
            <li class="page-item${i === currentPage ? ' active' : ''}">
                <button class="page-link" data-page="${i}">${i}</button>
            </li>`;
        }
        this.pagination.innerHTML = html;
        this.pagination.querySelectorAll('.page-link').forEach(btn => {
            btn.addEventListener('click', e => {
                e.preventDefault();
                this.load(parseInt(btn.getAttribute('data-page')));
            });
        });
    }

    setLoading(loading) {
        if (loading) {
            this.tableBody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                </td>
            </tr>`;
        }
    }

    clearFilters() {
        Object.values(this.filters).forEach(input => input && (input.value = ''));
        this.load(1);
    }

    handleEditClick(id) {
        console.log('🔄 Editando DocumentType con ID:', id);
        const data = this.currentItems.find(item => item.id == id);
        if (!data) {
            GlobalToast.show('Error: datos no encontrados', 'error');
            return;
        }
        if (window.documentTypeEditController?.openEditModal) {
            window.documentTypeEditController.openEditModal(+id, data);
        } else {
            console.error('❌ documentTypeEditController no está disponible');
            GlobalToast.show('Controlador de edición no disponible', 'error');
        }
    }

    handleDeleteClick(id, name, button) {
        console.log('🗑️ Eliminando DocumentType con ID:', id, 'Nombre:', name);
        if (window.documentTypeDeleteController?.showDeleteModal) {
            window.documentTypeDeleteController.showDeleteModal(+id, name, button);
        } else {
            console.error('❌ documentTypeDeleteController no está disponible');
            GlobalToast.show('Controlador de eliminación no disponible', 'error');
        }
    }

    // Permite refrescar externamente después de operaciones
    refresh() {
        this.load();
    }
}

window.DocumentTypesListController = DocumentTypesListController;
