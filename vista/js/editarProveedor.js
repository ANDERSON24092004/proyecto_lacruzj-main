document.addEventListener('DOMContentLoaded', function() {
    const editarProveedorModal = document.getElementById('editarProveedorModal');
    const btnEditarProveedores = document.querySelectorAll('.btn-editar-proveedor');
    
    if (editarProveedorModal && btnEditarProveedores.length > 0) {
        btnEditarProveedores.forEach(btn => {
            btn.addEventListener('click', function() {
                const idProveedor = this.getAttribute('data-id');
                
                fetch(`index.php?c=ProveedorControlador&m=obtenerProveedorAjax&id_proveedores=${idProveedor}`, {
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
                    .then(proveedor => {
                        console.log('Datos del proveedor:', proveedor);
                        
                        if (!proveedor || !proveedor.id_proveedores) {
                            throw new Error('Datos del proveedor inválidos');
                        }
                        
                        document.getElementById('id_proveedores').value = proveedor.id_proveedores;
                        document.getElementById('rif').value = proveedor.id_proveedores;
                        document.getElementById('nombre').value = proveedor.nombre;
                        document.getElementById('telefono').value = proveedor.telefono;
                        document.getElementById('email').value = proveedor.correo;
                        document.getElementById('direccion').value = proveedor.direccion;
                        
                        const modal = new bootstrap.Modal(editarProveedorModal);
                        modal.show();
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error al cargar los datos del proveedor: ' + error.message);
                    });
            });
        });
    }
});