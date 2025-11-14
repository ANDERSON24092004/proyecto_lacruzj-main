function cargarDetallesFacturaModal(nroFact) {
    console.log('Cargando detalles para factura:', nroFact);
    
    document.getElementById('ver_id_factura').textContent = '#' + nroFact;
    document.getElementById('ver_nro_factura').textContent = '#' + nroFact;
    document.getElementById('ver_productos_tbody').innerHTML = '<tr><td colspan="5" class="text-center">Cargando...</td></tr>';
    document.getElementById('ver_servicios_tbody').innerHTML = '<tr><td colspan="5" class="text-center">Cargando...</td></tr>';
    document.getElementById('card-productos').style.display = 'none';
    document.getElementById('card-servicios').style.display = 'none';   
    document.getElementById('ver_rif').textContent = 'Cargando...';
    document.getElementById('ver_razon_social').textContent = 'Cargando...';
    document.getElementById('ver_telefono').textContent = 'Cargando...';
    document.getElementById('ver_correo').textContent = 'Cargando...';
    document.getElementById('ver_direccion').textContent = 'Cargando...';
    
    $.ajax({
        url: 'index.php?c=CuentasCobrarControlador&m=ver&id=' + nroFact,
        type: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        success: function(data) {
            console.log('Datos recibidos:', data);
            
            if (data.success) {
                const cuenta = data.data.cuenta;
                const productos = data.data.productos || [];
                const servicios = data.data.servicios || [];
                const subtotales = data.data.subtotales || {};
       
                document.getElementById('ver_rif').textContent = cuenta.rif || 'N/A';
                document.getElementById('ver_razon_social').textContent = cuenta.razon_social || 'N/A';
                document.getElementById('ver_telefono').textContent = cuenta.telefono || 'N/A';
                document.getElementById('ver_correo').textContent = cuenta.correo || 'N/A';
                document.getElementById('ver_direccion').textContent = cuenta.direccion || 'N/A';
                document.getElementById('ver_fecha').textContent = cuenta.fecha_factura || 'N/A';
                document.getElementById('ver_fecha_limite').textContent = cuenta.fecha_limite || 'N/A';
                document.getElementById('ver_orden_compra').textContent = cuenta.numero_orden || 'N/A';
                document.getElementById('ver_estado').innerHTML = getBadgeEstado(cuenta.estado_visual);
                document.getElementById('ver_vigencia').innerHTML = cuenta.vigencia_factura == 0 ? 
                    '<span class="badge bg-primary">Vigente</span>' : 
                    '<span class="badge bg-secondary">Anulada</span>';
                    
                document.getElementById('ver_total_abonado').textContent = formatCurrency(cuenta.total_abonado || 0);
                document.getElementById('ver_saldo_pendiente').textContent = formatCurrency(cuenta.saldo_pendiente || 0);
                document.getElementById('ver_total_iva').textContent = formatCurrency(cuenta.total_iva || 0);
                document.getElementById('ver_total_general').textContent = formatCurrency(cuenta.total_general || 0);
                document.getElementById('ver_subtotal').textContent = formatCurrency(subtotales.general || 0);
                
                const tbodyProductos = document.getElementById('ver_productos_tbody');
                tbodyProductos.innerHTML = '';
                
                if (productos.length > 0) {
                    document.getElementById('card-productos').style.display = 'block';
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
                    document.getElementById('ver_subtotal_productos').textContent = formatCurrency(subtotales.productos || 0);
                    document.getElementById('ver_productos_footer').style.display = '';
                } else {
                    document.getElementById('card-productos').style.display = 'none';
                }
                
                const tbodyServicios = document.getElementById('ver_servicios_tbody');
                tbodyServicios.innerHTML = '';
                
                if (servicios.length > 0) {
                    document.getElementById('card-servicios').style.display = 'block';
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
                    document.getElementById('ver_subtotal_servicios').textContent = formatCurrency(subtotales.servicios || 0);
                    document.getElementById('ver_servicios_footer').style.display = '';
                } else {
                    document.getElementById('card-servicios').style.display = 'none';
                }
                
                const modalElement = document.getElementById('verFacturaModal');
                const modal = new bootstrap.Modal(modalElement);
                modal.show();
                
            } else {
                console.error('Error del servidor:', data.message);
                mostrarMensaje('error', 'Error', data.message || 'No se pudieron cargar los datos');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la petición:', error);
            mostrarMensaje('error', 'Error', 'Error al cargar los detalles: ' + error);
        }
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
            return '<span class="badge bg-secondary">' + (estado || 'Desconocido') + '</span>';
    }
}

function formatCurrency(amount) {
    const number = parseFloat(amount) || 0;
    return number.toLocaleString('es-VE', {
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