document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('#unidades .btn-editar');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const unidadId = this.getAttribute('data-id');
            const unidadNombre = this.getAttribute('data-nombre');
            
            document.getElementById('id_unidad_medida').value = unidadId;
            document.getElementById('editar_nombreM').value = unidadNombre;
        });
    });
});