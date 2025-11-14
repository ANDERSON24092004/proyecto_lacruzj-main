document.addEventListener('DOMContentLoaded', function() {
    const btnEditarList = document.querySelectorAll('.btn-editar');
    const editarModal = new bootstrap.Modal(document.getElementById('editarMateriaModal'));
    
    btnEditarList.forEach(btn => {
        btn.addEventListener('click', function() {
            const idMateria = this.value;
            
            const mensajeCarga = mostrarMensajeCarga('Cargando datos...');
            
            fetch(`index.php?c=MateriaPrimaControlador&m=editar&id=${idMateria}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return response.json();
                })
                .then(data => {
                    if (mensajeCarga && mensajeCarga.hide) {
                        mensajeCarga.hide();
                    }
                    
                    if (data.success && data.data) {
                        const materia = data.data;
                        
                        document.getElementById('edit_id_materia').value = materia.id_materia;
                        document.getElementById('edit_nombre').value = materia.nombre;
                        document.getElementById('edit_id_unidad_medida').value = materia.id_unidad_medida;
                        document.getElementById('edit_stock').value = materia.stock;
                        document.getElementById('edit_costo').value = materia.costo;
                        
                        document.getElementById('edit_nombre').style.borderColor = '#198754';
                        document.getElementById('edit_id_unidad_medida').style.borderColor = '#198754';
                        document.getElementById('edit_stock').style.borderColor = '#198754';
                        document.getElementById('edit_costo').style.borderColor = '#198754';
                        
                        editarModal.show();
                    } else {
                        mostrarMensaje('error', 'Error al cargar datos', data.message || 'No se pudieron cargar los datos de la materia prima');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    
                    if (mensajeCarga && mensajeCarga.hide) {
                        mensajeCarga.hide();
                    }
                    
                    mostrarMensaje('error', 'Error de conexión', 'No se pudo conectar con el servidor. Intente nuevamente.');
                });
        });
    });
    
    document.getElementById('editarMateriaModal').addEventListener('hidden.bs.modal', function() {
        const inputs = this.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.style.borderColor = '';
        });
        
        const errorMessages = this.querySelectorAll('.error-message');
        errorMessages.forEach(msg => {
            msg.style.display = 'none';
        });
    });
});