document.addEventListener('DOMContentLoaded', function() {
    console.log('Editar Cliente cargado');
    
    document.querySelectorAll('.btn-editar-cliente').forEach(btn => {
        btn.addEventListener('click', function() {
            const rif = this.getAttribute('data-rif');
            console.log('Editando cliente con RIF:', rif);

            mostrarMensajeCarga('Cargando datos del cliente...');

            fetch(`index.php?c=ClienteControlador&m=obtenerClienteAjax&rif=${rif}`)
                .then(res => {
                    if (!res.ok) {
                        throw new Error('Error en la respuesta del servidor');
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.error) {
                        mostrarMensaje('error', 'Error', data.error);
                        return;
                    }

                    const modal = bootstrap.Modal.getInstance(document.getElementById('mensajeModal'));
                    if (modal) modal.hide();

                    if (window.cargarDatosEditar) {
                        window.cargarDatosEditar(data);
                    } else {
                        document.getElementById('rif_editar').value = data.rif || '';
                        document.getElementById('razon_social_editar').value = data.razon_social || '';
                        document.getElementById('telefono_editar').value = data.telefono || '';
                        document.getElementById('email_editar').value = data.correo || '';
                        document.getElementById('direccion_editar').value = data.direccion || '';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos del cliente. Por favor, intente nuevamente.');
                });
        });
    });
});