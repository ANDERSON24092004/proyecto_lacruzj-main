document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('#condicion-pago .btn-editar');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id_condicion_pago = this.getAttribute('data-id');
            const forma = this.getAttribute('data-nombre');
            
            document.getElementById('id_condicion_pago').value = id_condicion_pago;
            document.getElementById('editar_forma').value = forma;
        });
    });
});