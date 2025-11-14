document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.btn-editar');
    let editModal = null;

    const editModalElement = document.getElementById('editarUsuarioModal');
    if (editModalElement) {
        editModal = new bootstrap.Modal(editModalElement);
    }
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const cedula = this.value;
            console.log('Cédula a editar:', cedula);
            
            fetch(`index.php?c=usuario&m=editar&cedula=${cedula}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error en la respuesta del servidor');
                }
                return response.json();
            })
            .then(usuario => {
                console.log('Datos recibidos:', usuario);
    
                if (usuario.error) {
                    alert(usuario.error);
                    return;
                }

                document.getElementById('cedula_editar').value = usuario.cedula;
                document.getElementById('cedula_display').value = usuario.cedula;
                document.getElementById('nombre_editar').value = usuario.nombre || '';
                document.getElementById('telefono_editar').value = usuario.telefono || '';
                document.getElementById('correo_editar').value = usuario.correo || '';
                document.getElementById('rol_editar').value = usuario.id_rol || '';
                document.getElementById('clave_editar').value = '';

                if (editModal) {
                    editModal.show();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error al cargar los datos del usuario: ' + error.message);
            });
        });
    });

    if (editModalElement) {
        editModalElement.addEventListener('hidden.bs.modal', function() {
            const inputs = editModalElement.querySelectorAll('input, select');
            inputs.forEach(input => {
                input.style.border = '';
                input.style.boxShadow = '';
            });
            
            const errorMessages = editModalElement.querySelectorAll('.error-message');
            errorMessages.forEach(error => {
                error.style.display = 'none';
                error.textContent = '';
            });
        });
    }
});