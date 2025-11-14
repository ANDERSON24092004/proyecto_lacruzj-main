$(document).ready(function(){
    console.log('Script de gestión de productos y servicios cargado');
    
    const expNombre = /^[A-Za-zÄ-ÿ\u00f1\u00d1\-\s\d]{1,100}$/;
    const expNumero = /^\d+(\.\d{1,2})?$/;
    
    inicializarAplicacion();

    function inicializarAplicacion() {
        cargarTablaProductosServicios();
        inicializarEventos();
        inicializarEventosEliminar();
    }

    function cargarTablaProductosServicios() {
        $.ajax({
            url: 'index.php?c=ProductoServicioControlador&m=listar',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                console.log('Respuesta de listar:', response);
                if (response.success) {
                    renderizarTablaProductosServicios(response.data);
                } else {
                    mostrarErrorTabla(response.details || 'Error al cargar los datos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en cargarTablaProductosServicios:', error);
                mostrarErrorTabla('Error al cargar los productos y servicios: ' + error);
            }
        });
    }
    
    function renderizarTablaProductosServicios(datos) {
    const tbody = $('table tbody');
    tbody.empty();
    
    if (datos.length === 0) {
        tbody.append(`
            <tr>
                <td colspan="8" class="text-center text-muted">
                    <i class="fas fa-boxes fa-2x mb-2"></i>
                    <p>No hay productos o servicios registrados</p>
                </td>
            </tr>
        `);
        return;
    }
    
    datos.forEach(item => {
        const tipo = item.tipo == 1 ? 'Producto' : 'Servicio';
        const esFabricado = item.es_fabricado == 1;
        const esFabricadoBadge = esFabricado ? '<span class="badge bg-info">Fabricado</span>' : '';
        const stock = item.tipo == 1 ? item.stock : 'N/A';
        const precioMayor = item.tipo == 1 ? `$${parseFloat(item.precio_mayor).toFixed(2)}` : 'N/A';
        
        const botonDetalle = esFabricado ?`
            <button type="button" 
                class="btn btn-info btn-sm btn-detalle" 
                data-id="${item.id_inv}" 
                data-nombre="${escapeHtml(item.nombre)}"
                data-fabricado="${item.es_fabricado}"
                title="Ver detalles">
                <i class="fas fa-eye"></i>
            </button>
        `: '';
        
        const fila = `
            <tr>
                <td>${escapeHtml(item.id_inv)}</td>
                <td>
                    ${escapeHtml(item.nombre)}
                </td>
                <td>${stock}</td>
                <td>$${parseFloat(item.costo).toFixed(2)}</td>
                <td>$${parseFloat(item.costo_mayor).toFixed(2)}</td>
                <td>${escapeHtml(item.unidad_medida)}</td>
                <td>${escapeHtml(item.presentacion)}</td>
                <td>
                    <div class="btn-group" role="group">
                        ${botonDetalle}
                        <button type="button" 
                            class="btn btn-primary btn-sm btn-editar" 
                            data-id="${item.id_inv}" 
                            title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" 
                            class="btn btn-danger btn-sm btn-eliminar" 
                            data-id="${item.id_inv}" 
                            title="Eliminar">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
        tbody.append(fila);
    });
    
    inicializarEventosBotones();
}
    
    function mostrarErrorTabla(mensaje) {
        $('table tbody').html(`
            <tr>
                <td colspan="8" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaProductosServicios()">
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
        $('#registrarProductoModal').on('hidden.bs.modal', function() {
            restaurarScroll();
        });
        
        $('#editarProductoModal').on('hidden.bs.modal', function() {
            restaurarScroll();
        });
        
        $('#confirmarEliminarModal').on('hidden.bs.modal', function() {
            restaurarScroll();
        });
        
        $('#mensajeModal').on('hidden.bs.modal', function() {
            restaurarScroll();
        });

        $('[data-bs-target="#registrarProductoModal"]').on('click', function() {
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
        $('.btn-detalle').off('click').on('click', function() {
            const id = $(this).data('id');
            const nombre = $(this).data('nombre');
            const esFabricado = $(this).data('fabricado') == 1;
        
            console.log('Datos para detalle:', { id, nombre, esFabricado });
    
            if (typeof window.detalleMaterias !== 'undefined' && typeof window.detalleMaterias.cargarDetalle === 'function') {
                window.detalleMaterias.cargarDetalle(id, nombre, esFabricado);
            } else if (typeof window.cargarDetalleMaterias === 'function') {
                window.cargarDetalleMaterias(id, nombre, esFabricado);
            } else {
                console.error('Función de detalle no encontrada');
                mostrarMensaje('error', 'Error', 'No se puede cargar el detalle en este momento');
            }
        });

        $('.btn-editar').off('click').on('click', function() {
            const id = $(this).data('id');
            if (typeof window.cargarDatosEditar === 'function') {
                window.cargarDatosEditar(id);
            }
        });

        $('.btn-eliminar').off('click').on('click', function() {
            const id = $(this).data('id');
            mostrarModalEliminar(id);
        });
    }
    
    function inicializarEventosEliminar() {
        $('#btnEliminarConfirmado').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarEliminacion();
        });
    }
    
    function mostrarModalEliminar(id) {
        $('#btnEliminarConfirmado').data('id', id);
        $('#confirmarEliminarModal').modal('show');
    }
    
    function ejecutarEliminacion() {
        const id = $('#btnEliminarConfirmado').data('id');
        
        if (!id) {
            mostrarMensaje('error', 'Error', 'No se especificó el ID del producto/servicio a eliminar');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=ProductoServicioControlador&m=eliminar',
            type: 'POST',
            data: { inv_prod_serv: id },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#btnEliminarConfirmado').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Eliminando...');
            },
            success: function(response) {
                $('#btnEliminarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                
                if (response.success) {
                    $('#confirmarEliminarModal').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaProductosServicios();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnEliminarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error', 'No se pudo eliminar el producto/servicio: ' + error);
            }
        });
    }

    function mostrarMensaje(tipo, titulo, mensaje) {
        if (typeof window.mostrarMensaje === 'function') {
            window.mostrarMensaje(tipo, titulo, mensaje);
        } else {
            console.log(`${titulo}: ${mensaje}`);
            alert(`${titulo}: ${mensaje}`);
        }
    }

    window.cargarTablaProductosServicios = cargarTablaProductosServicios;
});