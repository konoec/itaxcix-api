// Archivo: /initializers/document-type-initializer.js
// Inicializador para la página de Gestión de Tipos de Documentos
// ---
// Cambios necesarios para integrar el controlador de eliminación:
// 1. Instanciar el servicio y el controlador de eliminación.
// 2. Exponer el controlador globalmente como window.deleteDocumentTypeController.
// 3. Enlazar el evento click de los botones [data-action="delete-document-type"] para que usen el controlador.

// Ejemplo de integración (agregar después de cargar los componentes y controladores base):
document.addEventListener('DOMContentLoaded', function() {
    // ...existing code...
    setTimeout(() => {
        // Instanciar el servicio y el controlador de eliminación
        if (!window.DeleteDocumentTypeService) {
            window.DeleteDocumentTypeService = new DeleteDocumentTypeService();
        }
        if (!window.deleteDocumentTypeController) {
            window.deleteDocumentTypeController = new DeleteDocumentTypeController();
        }
        // Delegación de eventos para los botones de eliminar
        document.body.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-action="delete-document-type"]');
            if (btn) {
                const id = Number(btn.getAttribute('data-document-type-id'));
                const name = btn.getAttribute('data-name') || btn.dataset.name || '';
                window.deleteDocumentTypeController.handleDeleteButtonClick(btn, { id, name });
            }
        });
    }, 600); // Ajustar el delay según el resto del inicializador
});
// ...existing code...
