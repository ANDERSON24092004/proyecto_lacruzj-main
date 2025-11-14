document.addEventListener('DOMContentLoaded', function() {  
    const modalVerFactura = document.getElementById('modalVerFactura');
    if (modalVerFactura) {
        modalVerFactura.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            const nroFact = button.getAttribute('data-nro-fact');
            
            cargarDetallesFactura(nroFact);
        });
    }
    

    function cargarDetallesFactura(nroFact) {
        mostrarLoading();
        
        console.log('Cargando factura:', nroFact);
        
        fetch(`index.php?c=FacturaControlador&m=obtenerDetallesFacturaAjax&nro_fact=${nroFact}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                console.log('Respuesta HTTP:', response.status, response.statusText);
                if (!response.ok) {
                    throw new Error('Error HTTP: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                console.log('Respuesta recibida:', text);
                
                if (!text.trim()) {
                    throw new Error('Respuesta vacía del servidor - No hay datos de factura');
                }
                
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        console.log('Datos cargados exitosamente:', data);
                        actualizarModal(data.data);
                    } else {
                        mostrarError(data.message || 'Error desconocido del servidor');
                    }
                } catch (e) {
                    console.error('Error parseando JSON:', e);
                    console.log('Respuesta cruda:', text);
                    mostrarError('Error en el formato de respuesta: ' + e.message);
                }
            })
            .catch(error => {
                console.error('Error en fetch:', error);
                mostrarError('Error al cargar los detalles: ' + error.message);
            });
    }
    
    function mostrarLoading() {
        document.getElementById('modal-nro-fact').textContent = 'Cargando...';
        document.getElementById('modal-nro-fact-datos').textContent = 'Cargando...'; // Actualizamos el nuevo ID
        document.getElementById('modal-detalles-body').innerHTML = 
            '<tr><td colspan="5" class="text-center">Cargando detalles...</td></tr>';
        
        document.getElementById('modal-fecha').textContent = '-';
        document.getElementById('modal-estado').textContent = '-';
        document.getElementById('modal-estado').className = 'badge';
        document.getElementById('modal-condicion-pago').textContent = '-';
        document.getElementById('modal-numero-orden').textContent = '-';
        document.getElementById('modal-rif').textContent = '-';
        document.getElementById('modal-razon-social').textContent = '-';
        document.getElementById('modal-telefono').textContent = '-';
        document.getElementById('modal-correo').textContent = '-';
        document.getElementById('modal-direccion').textContent = '-';
        document.getElementById('modal-subtotal').textContent = '0.00';
        document.getElementById('modal-descuento').textContent = '0%';
        document.getElementById('modal-iva-porcentaje').textContent = '16';
        document.getElementById('modal-total-iva').textContent = '0.00';
        document.getElementById('modal-total-general').textContent = '0.00';
    }
    
    function actualizarModal(data) {
        const factura = data.factura;
        const detalles = data.detalles;
        
        console.log('Actualizando modal con:', data);
        
        // Actualizamos ambos elementos con el número de factura
        document.getElementById('modal-nro-fact').textContent = factura.nro_fact || '-';
        document.getElementById('modal-nro-fact-datos').textContent = factura.nro_fact || '-';
        document.getElementById('modal-fecha').textContent = formatearFecha(factura.fecha);
        document.getElementById('modal-estado').textContent = factura.vigencia == 1 ? 'Activa' : 'Anulada';
        document.getElementById('modal-estado').className = factura.vigencia == 1 ? 
            'badge bg-success' : 'badge bg-danger';
        document.getElementById('modal-condicion-pago').textContent = factura.condicion_pago || '-';
        document.getElementById('modal-numero-orden').textContent = factura.numero_orden || '-';
        
        document.getElementById('modal-rif').textContent = factura.rif || '-';
        document.getElementById('modal-razon-social').textContent = factura.razon_social || '-';
        document.getElementById('modal-telefono').textContent = factura.telefono || '-';
        document.getElementById('modal-correo').textContent = factura.correo || '-';
        document.getElementById('modal-direccion').textContent = factura.direccion || '-';
        
        const tbody = document.getElementById('modal-detalles-body');
        tbody.innerHTML = '';
        
        let subtotal = 0;
        
        if (detalles && detalles.length > 0) {
            detalles.forEach(detalle => {
                const fila = document.createElement('tr');
                fila.innerHTML = `
                    <td>${detalle.nombre || 'N/A'}</td>
                    <td><span class="badge ${detalle.tipo == 1 ? 'bg-primary' : 'bg-info'}">${detalle.tipo_nombre || 'N/A'}</span></td>
                    <td>${detalle.cantidad || 0} ${detalle.unidad_medida || ''}</td>
                    <td>${formatearMoneda(detalle.precio_unitario || 0)}</td>
                    <td>${formatearMoneda(detalle.subtotal || 0)}</td>
                `;
                tbody.appendChild(fila);
                
                subtotal += parseFloat(detalle.subtotal) || 0;
            });
        } else {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center">No hay detalles</td></tr>';
        }
        
        const descuentoPorcentaje = factura.descuento_porcentaje || 0;
        const ivaPorcentaje = factura.iva_porcentaje || 16;
        const subtotalConDescuento = subtotal * (1 - descuentoPorcentaje / 100);
        const montoIva = subtotalConDescuento * (ivaPorcentaje / 100);
        const totalGeneral = subtotalConDescuento + montoIva;
        
        document.getElementById('modal-subtotal').textContent = formatearMoneda(subtotal);
        document.getElementById('modal-descuento').textContent = descuentoPorcentaje + '%';
        document.getElementById('modal-iva-porcentaje').textContent = ivaPorcentaje;
        document.getElementById('modal-total-iva').textContent = formatearMoneda(montoIva);
        document.getElementById('modal-total-general').textContent = formatearMoneda(totalGeneral);
    }
    
    function mostrarError(mensaje) {
        console.error('Error:', mensaje);
        
        document.getElementById('modal-nro-fact').textContent = 'Error';
        document.getElementById('modal-nro-fact-datos').textContent = 'Error'; // Actualizamos el nuevo ID también
        document.getElementById('modal-detalles-body').innerHTML = 
            `<tr><td colspan="5" class="text-center text-danger">${mensaje}</td></tr>`;
    }
    
    function formatearFecha(fecha) {
        if (!fecha) return '-';
        try {
            return new Date(fecha).toLocaleDateString('es-ES');
        } catch (e) {
            return fecha;
        }
    }
    
    function formatearMoneda(monto) {
        return new Intl.NumberFormat('es-VE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(monto);
    }
});