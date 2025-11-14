document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('#descuento .btn-editar');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const porcentaje = this.getAttribute('data-porcentaje');
            
            document.getElementById('id_descuento').value = id;
            document.getElementById('editar_descuento').value = porcentaje;
        });
    });
});