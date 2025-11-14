document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-eliminar-cliente');
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmarEliminarModal'));
    const btnEliminarConfirmado = document.getElementById('btnEliminarConfirmado');

    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.getAttribute('data-id').trim();
            
            btnEliminarConfirmado.href = `index.php?c=ClienteControlador&m=eliminar&id=${itemId}`;
            confirmDeleteModal.show();
        });
    });
});