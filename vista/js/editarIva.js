document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('#iva .btn-editar');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id_iva = this.getAttribute('data-id');
            const porcentaje = this.getAttribute('data-nombre');
            
            document.getElementById('id_iva').value = id_iva;
            document.getElementById('editar_iva').value = porcentaje;
        });
    });
});