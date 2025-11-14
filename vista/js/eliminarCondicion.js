document.addEventListener('DOMContentLoaded', function() {
    const deleteIvaButtons = document.querySelectorAll('.btn-eliminar-condicion');
    deleteIvaButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const deleteUrl = `index.php?c=CondicionPagoControlador&m=eliminar&id=${id}&tab=condicion-pago`;
            document.getElementById('btnEliminarConfirmadoCondicion').href = deleteUrl;
        });
    });
});