// Archivo: category-delete-initializer.js
// Ubicación: assets/js/ui/initializers/MasterTables/category-delete-initializer.js
// Este archivo se integra en el inicializador principal de categorías

/**
 * Integración del controlador de eliminación de categorías en el inicializador
 * Instancia el service y el controller, y enlaza el evento de los botones de eliminar
 */
(function() {
    // Instanciar el service y el controller
    window.DeleteCategoryService = window.DeleteCategoryService || new DeleteCategoryService();
    window.deleteCategoryControllerInstance = new DeleteCategoryController();
    window.deleteCategoryController = window.deleteCategoryControllerInstance;

    // Enlazar evento click a los botones de eliminar categoría en la tabla
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('[data-action="delete-category"]');
        if (btn) {
            const categoryId = parseInt(btn.getAttribute('data-category-id'), 10);
            const categoryName = btn.getAttribute('data-category-name') || '';
            const categoryData = { id: categoryId, name: categoryName };
            window.deleteCategoryController.handleDeleteButtonClick(btn, categoryData);
        }
    });

    console.log('🗑️ DeleteCategoryController inicializado y eventos registrados');
})();
