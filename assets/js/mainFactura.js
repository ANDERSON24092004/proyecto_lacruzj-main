$(document).ready(function(){
    console.log('Script de gestión de facturas cargado');
    
    let facturas = [];

    function inicializarAplicacion() {
        cargarTablaFacturas();
        inicializarEventos();
        inicializarEventosEliminar();
    }

    function cargarTablaFacturas() {
        $.ajax({
            url: 'index.php?c=FacturaControlador&m=index&ajax=1',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderizarTablaFacturas(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                mostrarErrorTabla('Error al cargar las facturas: ' + error);
            }
        });
    }
    
    function renderizarTablaFacturas(facturas) {
        const tbody = $('#cuerpo-tabla-facturas');
        tbody.empty();
        
        if (facturas.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="9" class="text-center text-muted">
                        <i class="fas fa-file-invoice fa-2x mb-2"></i>
                        <p>No hay facturas registradas</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        facturas.forEach(factura => {
            const statusClass = factura.status == 1 ? 'table-secondary' : '';
            const statusBadge = factura.status == 1 ? 
                '<span class="badge bg-danger">Anulada</span>' : 
                '<span class="badge bg-success">Activa</span>';
            
            const fila = `
                <tr class="${statusClass}">
                    <td>#${factura.nro_fact}</td>
                    <td>${escapeHtml(factura.rif)}</td>
                    <td>${escapeHtml(factura.razon_social)}</td>
                    <td>${escapeHtml(factura.condicion_pago)}</td>
                    <td>${escapeHtml(factura.numero_orden)}</td>
                    <td>${parseFloat(factura.total_general).toFixed(2)} BS</td>
                    <td>${factura.fecha}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary btn-sm btn-ver" 
                                    data-nro-fact="${factura.nro_fact}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="index.php?c=FacturaControlador&m=imprimir&id=${factura.nro_fact}" 
                               class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            ${factura.status == 0 ? 
                                `<button type="button" class="btn btn-danger btn-sm btn-anular" 
                                        data-id="${factura.nro_fact}">
                                    <i class="fas fa-ban"></i>
                                </button>` :
                                `<button type="button" class="btn btn-warning btn-sm btn-reactivar" 
                                        data-id="${factura.nro_fact}">
                                    <i class="fas fa-undo"></i>
                                </button>`
                            }
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(fila);
        });
        
        inicializarEventosBotones();
    }
    
    function mostrarErrorTabla(mensaje) {
        $('#cuerpo-tabla-facturas').html(`
            <tr>
                <td colspan="9" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaFacturas()">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </td>
            </tr>
        `);
    }
    
    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return unsafe
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    function inicializarEventos() {
        $('#crearFacturaModal .btn-secondary').on('click', function() {
            limpiarFormularioCrear();
        });
        
        // Limpiar scroll al cerrar modales
        $('.modal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
    }

    function restaurarScroll() {
        setTimeout(function() {
            const modalesAbiertos = $('.modal.show');
            if (modalesAbiertos.length === 0) {
                $('body').removeClass('modal-open');
                $('body').css({
                    'overflow': 'auto',
                    'padding-right': '0'
                });
                $('.modal-backdrop').remove();
            }
        }, 150);
    }
    
    function inicializarEventosBotones() {
        // Botón Ver factura
        $('.btn-ver').off('click').on('click', function() {
            const nroFact = $(this).data('nro-fact');
            mostrarModalVerFactura(nroFact);
        });

        // Anular factura
        $('.btn-anular').off('click').on('click', function() {
            const nroFact = $(this).data('id');
            mostrarModalAnular(nroFact);
        });

        // Reactivar factura
        $('.btn-reactivar').off('click').on('click', function() {
            const nroFact = $(this).data('id');
            mostrarModalReactivar(nroFact);
        });
    }
    
    function inicializarEventosEliminar() {
        $('#btnAnularConfirmado').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarAnulacion();
        });

        $('#btnReactivarConfirmado').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarReactivacion();
        });
    }
    
    function mostrarModalVerFactura(nroFact) {
        // Disparar el evento para que verFactura.js lo capture
        const event = new CustomEvent('verFactura', { 
            detail: { nroFact: nroFact } 
        });
        document.dispatchEvent(event);
        
        // Abrir el modal
        $('#modalVerFactura').modal('show');
    }
    
    function mostrarModalAnular(nroFact) {
        $('#nro_fact_anular').val(nroFact);
        $('#confirmarAnularModal').modal('show');
    }
    
    function mostrarModalReactivar(nroFact) {
        $('#nro_fact_reactivar').val(nroFact);
        $('#confirmarReactivarModal').modal('show');
    }
    
    function ejecutarAnulacion() {
        const nroFact = $('#nro_fact_anular').val();
        
        if (!nroFact) {
            if (typeof mostrarMensaje === 'function') {
                mostrarMensaje('error', 'Error', 'No se especificó el número de factura a anular');
            } else {
                alert('No se especificó el número de factura a anular');
            }
            $('#confirmarAnularModal').modal('hide');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=FacturaControlador&m=anular',
            type: 'POST',
            data: { id: nroFact },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#btnAnularConfirmado').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Anulando...');
            },
            success: function(response) {
                $('#btnAnularConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                
                if (response.success) {
                    $('#confirmarAnularModal').modal('hide');
                    if (typeof mostrarMensaje === 'function') {
                        mostrarMensaje('success', response.message, response.details);
                    } else {
                        alert(response.message + ': ' + response.details);
                    }
                    cargarTablaFacturas();
                } else {
                    $('#confirmarAnularModal').modal('hide');
                    if (typeof mostrarMensaje === 'function') {
                        mostrarMensaje('error', response.message, response.details);
                    } else {
                        alert('Error: ' + response.message + ': ' + response.details);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#btnAnularConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                $('#confirmarAnularModal').modal('hide');
                if (typeof mostrarMensaje === 'function') {
                    mostrarMensaje('error', 'Error', 'No se pudo anular la factura: ' + error);
                } else {
                    alert('Error: No se pudo anular la factura: ' + error);
                }
            }
        });
    }
    
    function ejecutarReactivacion() {
        const nroFact = $('#nro_fact_reactivar').val();
        
        if (!nroFact) {
            if (typeof mostrarMensaje === 'function') {
                mostrarMensaje('error', 'Error', 'No se especificó el número de factura a reactivar');
            } else {
                alert('No se especificó el número de factura a reactivar');
            }
            $('#confirmarReactivarModal').modal('hide');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=FacturaControlador&m=reactivar',
            type: 'POST',
            data: { id: nroFact },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#btnReactivarConfirmado').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Reactivando...');
            },
            success: function(response) {
                $('#btnReactivarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                
                if (response.success) {
                    $('#confirmarReactivarModal').modal('hide');
                    if (typeof mostrarMensaje === 'function') {
                        mostrarMensaje('success', response.message, response.details);
                    } else {
                        alert(response.message + ': ' + response.details);
                    }
                    cargarTablaFacturas();
                } else {
                    $('#confirmarReactivarModal').modal('hide');
                    if (typeof mostrarMensaje === 'function') {
                        mostrarMensaje('error', response.message, response.details);
                    } else {
                        alert('Error: ' + response.message + ': ' + response.details);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#btnReactivarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                $('#confirmarReactivarModal').modal('hide');
                if (typeof mostrarMensaje === 'function') {
                    mostrarMensaje('error', 'Error', 'No se pudo reactivar la factura: ' + error);
                } else {
                    alert('Error: No se pudo reactivar la factura: ' + error);
                }
            }
        });
    }

    function limpiarFormularioCrear() {
        $('#formFactura')[0].reset();
        $('.error-message').hide().text('');
        $('#items-container').empty();
    }

    inicializarAplicacion();
    
    window.cargarTablaFacturas = cargarTablaFacturas;

    $('#btnCrearFactura').on('click', function() {
        $('#crearFacturaModal').modal('show');
    });
});