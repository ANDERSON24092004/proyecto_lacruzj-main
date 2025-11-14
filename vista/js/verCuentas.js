function cargarDetallesFacturaModal(nroFact) {
    console.log('Cargando detalles para factura:', nroFact);
    
    document.getElementById('ver_id_factura').textContent = '#' + nroFact;
    document.getElementById('ver_nro_factura').textContent = '#' + nroFact;
    
    document.getElementById('ver_productos_tbody').innerHTML = '';
    document.getElementById('ver_servicios_tbody').innerHTML = '';
    
    document.getElementById('card-productos').style.display = 'none';
    document.getElementById('card-servicios').style.display = 'none';
    
    document.getElementById('ver_rif').textContent = 'Cargando...';
    document.getElementById('ver_razon_social').textContent = 'Cargando...';
    document.getElementById('ver_telefono').textContent = 'Cargando...';
    document.getElementById('ver_correo').textContent = 'Cargando...';
    document.getElementById('ver_direccion').textContent = 'Cargando...';
    
    fetch(`index.php?c=CuentasCobrarControlador&m=ver&id=${nroFact}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            console.log('Datos recibidos:', data);
            
            if (data.success) {
                const cuenta = data.cuenta;
                const productos = data.productos;
                const servicios = data.servicios;
       
                document.getElementById('ver_rif').textContent = cuenta.rif || 'N/A';
                document.getElementById('ver_razon_social').textContent = cuenta.razon_social || 'N/A';
                document.getElementById('ver_telefono').textContent = cuenta.telefono || 'N/A';
                document.getElementById('ver_correo').textContent = cuenta.correo || 'N/A';
                document.getElementById('ver_direccion').textContent = cuenta.direccion || 'N/A';

                document.getElementById('ver_fecha').textContent = cuenta.fecha_factura || 'N/A';
                document.getElementById('ver_fecha_limite').textContent = cuenta.fecha_limite || 'N/A';
                document.getElementById('ver_orden_compra').textContent = cuenta.numero_orden || 'N/A';
                document.getElementById('ver_estado').innerHTML = getBadgeEstado(cuenta.estado_visual);
                document.getElementById('ver_vigencia').innerHTML = cuenta.vigencia == 1 ? 
                    '<span class="badge bg-success">Activa</span>' : 
                    '<span class="badge bg-secondary">Anulada</span>';
                document.getElementById('ver_total_abonado').textContent = formatCurrency(cuenta.total_abonado || 0);
                document.getElementById('ver_saldo_pendiente').textContent = formatCurrency(cuenta.saldo_pendiente || 0);
                document.getElementById('ver_total_iva').textContent = formatCurrency(cuenta.total_iva || 0);
                document.getElementById('ver_total_general').textContent = formatCurrency(cuenta.total_general || 0);
                document.getElementById('ver_subtotal').textContent = formatCurrency(data.subtotales?.general || 0);
                
                if (productos.length > 0) {
                    document.getElementById('card-productos').style.display = 'block';
                    const tbodyProductos = document.getElementById('ver_productos_tbody');
                    productos.forEach(producto => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${escapeHtml(producto.nombre)}</td>
                            <td>${escapeHtml(producto.presentacion || 'N/A')}</td>
                            <td>${producto.cantidad}</td>
                            <td>${formatCurrency(producto.precio_unitario)}</td>
                            <td>${formatCurrency(producto.subtotal)}</td>
                        `;
                        tbodyProductos.appendChild(row);
                    });
                    document.getElementById('ver_subtotal_productos').textContent = formatCurrency(data.subtotales?.productos || 0);
                    document.getElementById('ver_productos_footer').style.display = '';
                }
                
                if (servicios.length > 0) {
                    document.getElementById('card-servicios').style.display = 'block';
                    const tbodyServicios = document.getElementById('ver_servicios_tbody');
                    servicios.forEach(servicio => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${escapeHtml(servicio.nombre)}</td>
                            <td>${escapeHtml(servicio.unidad_medida || 'N/A')}</td>
                            <td>${servicio.cantidad}</td>
                            <td>${formatCurrency(servicio.precio_unitario)}</td>
                            <td>${formatCurrency(servicio.subtotal)}</td>
                        `;
                        tbodyServicios.appendChild(row);
                    });
                    document.getElementById('ver_subtotal_servicios').textContent = formatCurrency(data.subtotales?.servicios || 0);
                    document.getElementById('ver_servicios_footer').style.display = '';
                }
                
                const modalElement = document.getElementById('verFacturaModal');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                
            } else {
                console.error('Error del servidor:', data.message);
                alert('Error: ' + (data.message || 'No se pudieron cargar los datos'));
            }
        })
        .catch(error => {
            console.error('Error en la petición:', error);
            alert('Error al cargar los detalles de la factura: ' + error.message);
        });
}

function getBadgeEstado(estado) {
    switch(estado) {
        case 'Pagada':
            return '<span class="badge bg-success">Pagada</span>';
        case 'Vencida':
            return '<span class="badge bg-danger">Vencida</span>';
        case 'Pendiente':
            return '<span class="badge bg-warning">Pendiente</span>';
        default:
            return '<span class="badge bg-secondary">' + estado + '</span>';
    }
}

function formatCurrency(amount) {
    return parseFloat(amount).toLocaleString('es-VE', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + ' Bs.';
}

function escapeHtml(unsafe) {
    if (!unsafe) return 'N/A';
    return unsafe
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;")
        .replace(/'/g, "&#039;");
}

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado, asignando event listeners...');
    
    document.querySelectorAll('.btn-ver').forEach(button => {
        button.addEventListener('click', function() {
            const cuentaId = this.getAttribute('data-id');
            console.log('Botón Ver clickeado, ID:', cuentaId);
            cargarDetallesFacturaModal(cuentaId);
        });
    });
    
    console.log('Event listeners asignados para', document.querySelectorAll('.btn-ver').length, 'botones Ver');
});