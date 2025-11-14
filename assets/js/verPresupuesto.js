document.addEventListener('DOMContentLoaded', function() {
   
    window.verPresupuesto = function(presupuestoId) {
        console.log('Cargando presupuesto ID:', presupuestoId);
        
        fetch(`index.php?c=PresupuestoControlador&m=obtener&id=${presupuestoId}`)
            .then(response => response.json())
            .then(data => {
                console.log('Datos recibidos:', data);
                if (data.success) {
                    const presupuesto = data.presupuesto;
                    const cliente = data.cliente;
                    const detalles = data.detalles;
                    
                    document.getElementById('ver_id_factura').textContent = presupuesto.nro_presupuesto || presupuesto.id_factura;
                    document.getElementById('ver_fecha').textContent = presupuesto.fecha;
                    document.getElementById('ver_orden_compra').textContent = presupuesto.numero_orden || 'N/A';
                    document.getElementById('ver_total_general').textContent = parseFloat(presupuesto.total_general).toFixed(2);
                    document.getElementById('ver_total_iva').textContent = parseFloat(presupuesto.total_iva).toFixed(2);
                    
                    const subtotal = parseFloat(presupuesto.total_general) - parseFloat(presupuesto.total_iva);
                    document.getElementById('ver_subtotal').textContent = subtotal.toFixed(2);
                    
                    document.getElementById('ver_rif').textContent = cliente.rif;
                    document.getElementById('ver_razon_social').textContent = cliente.razon_social || 'N/A';
                    document.getElementById('ver_nombre_cliente').textContent = cliente.nombre_cliente || cliente.razon_social;
                    document.getElementById('ver_telefono').textContent = cliente.telefono;
                    document.getElementById('ver_correo').textContent = cliente.correo;
                    document.getElementById('ver_direccion').textContent = cliente.direccion;
                    
                    const productosTbody = document.getElementById('ver_productos_tbody');
                    productosTbody.innerHTML = '';
                    
                    const productos = detalles.productos || [];
                    console.log('Productos a mostrar:', productos);
                    
                    if (productos.length > 0) {
                        productos.forEach(producto => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${producto.id_inv}</td>
                                <td>${producto.nombre || 'Sin nombre'}</td>
                                <td>${parseInt(producto.cantidad)}</td>
                                <td>${parseFloat(producto.precio_unitario || producto.costo || 0).toFixed(2)} BS</td>
                                <td>${parseFloat(producto.subtotal || (producto.cantidad * (producto.precio_unitario || producto.costo || 0))).toFixed(2)} BS</td>
                            `;
                            productosTbody.appendChild(row);
                        });
                    } else {
                        productosTbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center">No hay productos en este presupuesto.</td>
                            </tr>
                        `;
                    }
                    
                    const serviciosTbody = document.getElementById('ver_servicios_tbody');
                    serviciosTbody.innerHTML = '';
                    
                    const servicios = detalles.servicios || [];
                    console.log('Servicios a mostrar:', servicios);
                    
                    if (servicios.length > 0) {
                        servicios.forEach(servicio => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${servicio.id_inv}</td>
                                <td>${servicio.nombre || 'Sin nombre'}</td>
                                <td>${parseInt(servicio.cantidad)}</td>
                                <td>${parseFloat(servicio.precio_unitario || servicio.costo || 0).toFixed(2)} BS</td>
                                <td>${parseFloat(servicio.subtotal || (servicio.cantidad * (servicio.precio_unitario || servicio.costo || 0))).toFixed(2)} BS</td>
                            `;
                            serviciosTbody.appendChild(row);
                        });
                    } else {
                        serviciosTbody.innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center">No hay servicios en este presupuesto.</td>
                            </tr>
                        `;
                    }

                    const statusElement = document.getElementById('ver_status');
                    if (presupuesto.status == 0) {
                        statusElement.textContent = 'Vigente';
                        statusElement.className = 'badge bg-success';
                    } else {
                        statusElement.textContent = 'Anulado';
                        statusElement.className = 'badge bg-danger';
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalVerPresupuesto'));
                    modal.show();
                } else {
                    console.error('Error en la respuesta:', data.message);
                    alert('Error al cargar el presupuesto: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                alert('Error al cargar el presupuesto');
            });
    };

});