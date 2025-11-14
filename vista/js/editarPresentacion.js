document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('#presentacion .btn-editar');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id_pres = this.getAttribute('data-id');
            const nombre = this.getAttribute('data-nombre');
            
            document.getElementById('id_pres').value = id_pres;
            document.getElementById('editar_presentacion').value = nombre;
        });
    });
});