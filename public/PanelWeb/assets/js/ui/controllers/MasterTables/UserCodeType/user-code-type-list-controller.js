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
        this.tableBody.innerHTML = items.length ? items.map((s, i) => `
            <tr>
                <td class="text-center">${s.id}</td>
                <td>${s.name}</td>
                <td class="text-center">
                    <span class="badge bg-${s.active ? 'primary' : 'secondary'}-lt">
                        <i class="fas fa-${s.active ? 'toggle-on text-primary' : 'toggle-off text-secondary'}"></i> ${s.active ? 'Activo' : 'Inactivo'}
                    </span>
                </td>
                <td class="text-center">
                    <span class="avatar bg-${s.active ? 'blue' : 'gray'}-lt"><i class="fas fa-barcode"></i></span>
                </td>
                <td class="text-center">
                    <button class="btn btn-outline-primary btn-sm" title="Ver detalles" disabled><i class="fas fa-eye"></i></button>
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
}

// NO INSTANCIAR AUTOMÁTICAMENTE AQUÍ
// window.UserCodeTypeListController = new UserCodeTypeListController();
