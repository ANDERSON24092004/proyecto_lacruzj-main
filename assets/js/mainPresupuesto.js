$(document).ready(function(){
    console.log('Script de gestión de presupuestos cargado');
    
    let presupuestos = [];

    function inicializarAplicacion() {
        cargarTablaPresupuestos();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarEventosCrear();
    }

    function cargarTablaPresupuestos() {
        $.ajax({
            url: 'index.php?c=PresupuestoControlador&m=index&ajax=1',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderizarTablaPresupuestos(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                mostrarErrorTabla('Error al cargar los presupuestos: ' + error);
            }
        });
    }
    
    function renderizarTablaPresupuestos(presupuestos) {
        const tbody = $('#cuerpo-tabla-presupuestos');
        tbody.empty();
        
        if (presupuestos.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="8" class="text-center text-muted">
                        <i class="fas fa-file-invoice-dollar fa-2x mb-2"></i>
                        <p>No hay presupuestos registrados</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        presupuestos.forEach(presupuesto => {
            const statusClass = presupuesto.status == 1 ? 'table-secondary' : '';
            const statusBadge = presupuesto.status == 1 ? 
                '<span class="badge bg-danger">Anulado</span>' : 
                '<span class="badge bg-success">Vigente</span>';
            
            const fila = `
                <tr class="${statusClass}">
                    <td>#${presupuesto.nro_presupuesto}</td>
                    <td>${escapeHtml(presupuesto.razon_social)}</td>
                    <td>${escapeHtml(presupuesto.rif)}</td>
                    <td>${escapeHtml(presupuesto.numero_orden || 'N/A')}</td>
                    <td>${parseFloat(presupuesto.total_general).toFixed(2)} BS</td>
                    <td>${presupuesto.fecha}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-primary btn-sm btn-ver" 
                                    data-nro-presupuesto="${presupuesto.nro_presupuesto}">
                                <i class="fas fa-eye"></i>
                            </button>
                            <a href="index.php?c=PresupuestoControlador&m=imprimir&id=${presupuesto.nro_presupuesto}" 
                               class="btn btn-info btn-sm" target="_blank">
                                <i class="fas fa-print"></i>
                            </a>
                            ${presupuesto.status == 0 ? 
                                `<button type="button" class="btn btn-danger btn-sm btn-anular" 
                                        data-id="${presupuesto.nro_presupuesto}">
                                    <i class="fas fa-ban"></i>
                                </button>` :
                                `<button type="button" class="btn btn-warning btn-sm btn-reactivar" 
                                        data-id="${presupuesto.nro_presupuesto}">
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
        $('#cuerpo-tabla-presupuestos').html(`
            <tr>
                <td colspan="8" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaPresupuestos()">
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
        $('#crearPresupuestoModal .btn-secondary').on('click', function() {
            limpiarFormularioCrear();
        });
        
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
        $('.btn-ver').off('click').on('click', function() {
            const nroPresupuesto = $(this).data('nro-presupuesto');
            
            if (typeof window.verPresupuesto === 'function') {
                window.verPresupuesto(nroPresupuesto);
            } else {
                console.error('La función verPresupuesto no está disponible');
                if (typeof mostrarMensaje === 'function') {
                    mostrarMensaje('error', 'Error', 'No se puede cargar la función de visualización');
                } else {
                    alert('Error: No se puede cargar los detalles del presupuesto');
                }
            }
        });

        $('.btn-anular').off('click').on('click', function() {
            const nroPresupuesto = $(this).data('id');
            mostrarModalAnular(nroPresupuesto);
        });

        $('.btn-reactivar').off('click').on('click', function() {
            const nroPresupuesto = $(this).data('id');
            mostrarModalReactivar(nroPresupuesto);
        });
    }

    function inicializarEventosCrear() {
        $('#formPresupuesto').off('submit').on('submit', function(e) {
            e.preventDefault();
            console.log('Formulario enviado - Validando...');
            
            if (typeof validarFormularioCompleto !== 'function' || !validarFormularioCompleto()) {
                console.log('Validación falló');
                const primerError = document.querySelector('.is-invalid');
                if (primerError) {
                    primerError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
                return;
            }

            console.log('Validación exitosa - Enviando por AJAX...');
            
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');

            const formData = $(this).serialize();
            
            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('Respuesta del servidor:', response);
                    
                    if (response.success) {
                        if (typeof mostrarMensaje === 'function') {
                            mostrarMensaje('success', response.message, response.details);
                        } else {
                            alert(response.message + ': ' + response.details);
                        }
                        
                        $('#crearPresupuestoModal').modal('hide');
                        if (typeof limpiarModalCompletamente === 'function') {
                            limpiarModalCompletamente();
                        }
                        
                        cargarTablaPresupuestos();
                    } else {
                        if (typeof mostrarErrorGeneral === 'function') {
                            mostrarErrorGeneral('Error: ' + (response.details || response.message));
                        } else {
                            alert('Error: ' + (response.details || response.message));
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error AJAX:', error);
                    console.log('Respuesta:', xhr.responseText);
                    if (typeof mostrarErrorGeneral === 'function') {
                        mostrarErrorGeneral('Error de conexión: ' + error);
                    } else {
                        alert('Error de conexión: ' + error);
                    }
                },
                complete: function() {
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        $('#btnCrearPresupuesto').off('click').on('click', function() {
            $('#crearPresupuestoModal').modal('show');
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
    
    function mostrarModalAnular(nroPresupuesto) {
        $('#nro_presupuesto_anular').val(nroPresupuesto);
        $('#confirmarAnularModal').modal('show');
    }
    
    function mostrarModalReactivar(nroPresupuesto) {
        $('#nro_presupuesto_reactivar').val(nroPresupuesto);
        $('#confirmarReactivarModal').modal('show');
    }
    
    function ejecutarAnulacion() {
        const nroPresupuesto = $('#nro_presupuesto_anular').val();
        
        if (!nroPresupuesto) {
            if (typeof mostrarMensaje === 'function') {
                mostrarMensaje('error', 'Error', 'No se especificó el número de presupuesto a anular');
            } else {
                alert('No se especificó el número de presupuesto a anular');
            }
            $('#confirmarAnularModal').modal('hide');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=PresupuestoControlador&m=anular',
            type: 'POST',
            data: { id: nroPresupuesto },
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
                    cargarTablaPresupuestos();
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
                    mostrarMensaje('error', 'Error', 'No se pudo anular el presupuesto: ' + error);
                } else {
                    alert('Error: No se pudo anular el presupuesto: ' + error);
                }
            }
        });
    }
    
    function ejecutarReactivacion() {
        const nroPresupuesto = $('#nro_presupuesto_reactivar').val();
        
        if (!nroPresupuesto) {
            if (typeof mostrarMensaje === 'function') {
                mostrarMensaje('error', 'Error', 'No se especificó el número de presupuesto a reactivar');
            } else {
                alert('No se especificó el número de presupuesto a reactivar');
            }
            $('#confirmarReactivarModal').modal('hide');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=PresupuestoControlador&m=reactivar',
            type: 'POST',
            data: { id: nroPresupuesto },
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
                    cargarTablaPresupuestos();
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
                    mostrarMensaje('error', 'Error', 'No se pudo reactivar el presupuesto: ' + error);
                } else {
                    alert('Error: No se pudo reactivar el presupuesto: ' + error);
                }
            }
        });
    }

    function limpiarFormularioCrear() {
        $('#formPresupuesto')[0].reset();
        $('.error-message').hide().text('');
        $('#items-container').empty();
    }

    inicializarAplicacion();
    
    window.cargarTablaPresupuestos = cargarTablaPresupuestos;
});