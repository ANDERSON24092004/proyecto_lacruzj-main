document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-eliminar');
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmarEliminarModal'));
    const btnEliminarConfirmado = document.getElementById('btnEliminarConfirmado');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id').trim();
            const controlador = this.getAttribute('data-controlador') || 'DefaultControlador';
            
            btnEliminarConfirmado.href = `index.php?c=${controlador}&m=eliminar&id=${itemId}`;
            confirmDeleteModal.show();
        });
    });
});

