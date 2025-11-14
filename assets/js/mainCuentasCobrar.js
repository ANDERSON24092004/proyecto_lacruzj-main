$(document).ready(function(){
    console.log('Script de gestión de cuentas por cobrar cargado');

    function inicializarAplicacion() {
        cargarTablaCuentas();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarEventosReactivar();
    }

    function cargarTablaCuentas() {
        $.ajax({
            url: 'index.php?c=CuentasCobrarControlador&m=listar',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderizarTablaCuentas(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                    mostrarMensaje('error', 'Error al cargar datos', response.details);
                }
            },
            error: function(xhr, status, error) {
                mostrarErrorTabla('Error al cargar las cuentas: ' + error);
                mostrarMensaje('error', 'Error de conexión', 'No se pudieron cargar las cuentas: ' + error);
            }
        });
    }
    
    function renderizarTablaCuentas(cuentas) {
        const tbody = $('#cuerpo-tabla-cuentas');
        tbody.empty();
        
        if (cuentas.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="7" class="text-center text-muted">
                        <i class="fas fa-receipt fa-2x mb-2"></i>
                        <p>No hay cuentas por cobrar registradas</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        cuentas.forEach(cuenta => {
            const estadoBadge = cuenta.estado_visual === 'Pagada' ? 
                '<span class="badge bg-success">Pagada</span>' : 
                cuenta.estado_visual === 'Vencida' ? 
                '<span class="badge bg-danger">Vencida</span>' : 
                '<span class="badge bg-warning">Pendiente</span>';
            
            const vigenciaBadge = cuenta.vigencia_factura == 1 ? 
                '<span class="badge bg-secondary">Anulada</span>' : 
                '<span class="badge bg-primary">Vigente</span>';
            
            const fechaLimiteClass = cuenta.estado_visual === 'Vencida' ? 'text-danger fw-bold' : '';
            
            const botones = cuenta.vigencia_factura == 0 && 
                           cuenta.estado_visual !== 'Pagada' && 
                           cuenta.estado_visual !== 'Vencida' ? 
                `
                <button type="button" class="btn btn-success btn-sm btn-registrar-pago" 
                    data-id="${cuenta.nro_fact}" data-total="${cuenta.total_general}">
                    <i class="fas fa-check-circle"></i> Pagar
                </button>
                <button type="button" class="btn btn-danger btn-sm btn-anular" 
                    data-id="${cuenta.nro_fact}">
                    <i class="fas fa-ban"></i> Anular
                </button>
                ` : 
                (cuenta.vigencia_factura == 1 && cuenta.estado_visual !== 'Pagada' ? 
                `
                <button type="button" class="btn btn-warning btn-sm btn-reactivar" 
                    data-id="${cuenta.nro_fact}">
                    <i class="fas fa-undo"></i> Reactivar
                </button>
                ` : '');
            
            const fila = `
                <tr class="${cuenta.vigencia_factura == 1 ? 'table-secondary' : ''}">
                    <td>#${cuenta.nro_fact}</td>
                    <td>${escapeHtml(cuenta.razon_social)}</td>
                    <td>${escapeHtml(number_format(cuenta.total_general, 2, ',', '.'))} Bs.</td>
                    <td class="${fechaLimiteClass}">${escapeHtml(cuenta.fecha_limite)}</td>
                    <td>${estadoBadge}</td>
                    <td>${vigenciaBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary btn-sm btn-ver" 
                                data-id="${cuenta.nro_fact}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="index.php?c=CuentasCobrarControlador&m=imprimir&id=${cuenta.nro_fact}" 
                                class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            ${botones}
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(fila);
        });
        
        inicializarEventosBotones();
    }
    
    function number_format(number, decimals, dec_point, thousands_sep) {
        number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
        var n = !isFinite(+number) ? 0 : +number,
            prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
            sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
            dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
            s = '',
            toFixedFix = function (n, prec) {
                var k = Math.pow(10, prec);
                return '' + Math.round(n * k) / k;
            };
        
        s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
        if (s[0].length > 3) {
            s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
        }
        if ((s[1] || '').length < prec) {
            s[1] = s[1] || '';
            s[1] += new Array(prec - s[1].length + 1).join('0');
        }
        return s.join(dec);
    }
    
    function mostrarErrorTabla(mensaje) {
        $('#cuerpo-tabla-cuentas').html(`
            <tr>
                <td colspan="7" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaCuentas()">
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
        $('#registrarPagoModal .btn-secondary').on('click', function() {
            limpiarFormularioPago();
        });
        
        $('#registrarPagoModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#confirmarAnularModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#confirmarReactivarModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#verFacturaModal').on('hidden.bs.modal', function () {
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
        $('.btn-ver').off('click').on('click', function() {
            const nroFact = $(this).data('id');
            console.log('ver:',nroFact);
            cargarDetallesFacturaModal(nroFact);
        });

        $('.btn-registrar-pago').off('click').on('click', function() {
            const nroFact = $(this).data('id');
            const totalGeneral = $(this).data('total');
            mostrarModalPago(nroFact, totalGeneral);
        });

        $('.btn-anular').off('click').on('click', function() {
            const nroFact = $(this).data('id');
            mostrarModalAnular(nroFact);
        });

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
    }
    
    function inicializarEventosReactivar() {
        $('#btnReactivarConfirmado').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarReactivacion();
        });
    }
    
    function mostrarModalPago(nroFact, totalGeneral) {
        $('#infoCuenta').html(
            `Monto total a pagar: <strong>${number_format(totalGeneral, 2, ',', '.')} Bs.</strong><br>
             N° Factura: ${nroFact}`
        );
        
        $('#nro_fact_pago').val(nroFact);
        $('#fecha_pago').val(new Date().toISOString().split('T')[0]);
        
        $('#registrarPagoModal').modal('show');
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
            mostrarMensaje('error', 'Error', 'No se especificó la factura a anular');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=CuentasCobrarControlador&m=anular',
            type: 'POST',
            data: { nro_fact: nroFact },
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
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaCuentas();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnAnularConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error del servidor', 'No se pudo anular la cuenta: ' + error);
            }
        });
    }
    
    function ejecutarReactivacion() {
        const nroFact = $('#nro_fact_reactivar').val();
        
        if (!nroFact) {
            mostrarMensaje('error', 'Error', 'No se especificó la factura a reactivar');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=CuentasCobrarControlador&m=reactivar',
            type: 'POST',
            data: { nro_fact: nroFact },
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
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaCuentas();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnReactivarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error del servidor', 'No se pudo reactivar la cuenta: ' + error);
            }
        });
    }
    
    function validarFormularioPago() {
        const fechaPago = $('#fecha_pago').val();
        
        if (!fechaPago) {
            mostrarMensaje('error', 'Error de validación', 'La fecha del pago es obligatoria');
            $('#fecha_pago').focus();
            return false;
        }
        
        return true;
    }
    
    $('#formRegistrarPago').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioPago()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=CuentasCobrarControlador&m=registrar_pago',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('button[type="submit"]', '#formRegistrarPago').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Registrando...');
            },
            success: function(response) {
                $('button[type="submit"]', '#formRegistrarPago').prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Registrar Pago Completo');
                
                if (response.success) {
                    $('#registrarPagoModal').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaCuentas();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('button[type="submit"]', '#formRegistrarPago').prop('disabled', false).html('<i class="fas fa-check-circle me-2"></i> Registrar Pago Completo');
                mostrarMensaje('error', 'Error del servidor', 'No se pudo registrar el pago: ' + error);
            }
        });
    });
    
    function limpiarFormularioPago() {
        $('#formRegistrarPago')[0].reset();
    }
    
    inicializarAplicacion();
    
    window.cargarTablaCuentas = cargarTablaCuentas;
});