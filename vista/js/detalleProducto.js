$(document).ready(function() {
    inicializarDetalleMaterias();
});

function inicializarDetalleMaterias() {
    console.log(' Inicializando detalle de materias primas...');

    $(document).on('click', '.btn-ver-detalle', function(e) {
        e.preventDefault();
        const idProducto = $(this).data('id');
        const nombreProducto = $(this).data('nombre');
        const esFabricado = $(this).data('fabricado') == 1;
        
        console.log(' Abriendo detalle para producto:', { 
            idProducto, 
            nombreProducto, 
            esFabricado 
        });
        
        cargarDetalleMaterias(idProducto, nombreProducto, esFabricado);
    });

    $('#detalleMateriasModal').on('hidden.bs.modal', function() {
        limpiarModalDetalle();
    });
}

function cargarDetalleMaterias(idProducto, nombreProducto, esFabricado) {
    console.log(' Cargando detalle de materias primas para producto ID:', idProducto);
    
    mostrarLoadingDetalle();

    $('#nombreProductoDetalle').text(nombreProducto || 'Producto sin nombre');
    
    const badge = $('#fabricadoBadge');
    if (esFabricado) {
        badge.text('Fabricado').removeClass('bg-secondary').addClass('bg-primary');
    } else {
        badge.text('No Fabricado').removeClass('bg-primary').addClass('bg-secondary');
    }

    if (!esFabricado) {
        mostrarMensajeNoFabricado();
        $('#detalleMateriasModal').modal('show');
        return;
    }

    $.ajax({
        url: 'index.php?c=ProductoServicioControlador&m=obtenerDetalle&id=' + idProducto,
        type: 'GET',
        dataType: 'json',
        timeout: 10000, 
        success: function(response) {
            console.log(' Respuesta detalle materias:', response);
            
            if (response.success) {
                if (response.materias_primas && response.materias_primas.length > 0) {
                    mostrarMateriasPrimasEnTabla(response.materias_primas);
                } else {
                    mostrarMensajeSinMaterias();
                }
            } else {
                mostrarErrorDetalle(response.error || 'Error al cargar los detalles del producto');
            }
            
            $('#detalleMateriasModal').modal('show');
        },
        error: function(xhr, status, error) {
            console.error(' Error al cargar detalle de materias primas:', error);
            
            let mensajeError = 'No se pudieron cargar los detalles del producto';
            if (xhr.status === 404) {
                mensajeError = 'Producto no encontrado';
            } else if (xhr.status === 500) {
                mensajeError = 'Error interno del servidor';
            } else if (status === 'timeout') {
                mensajeError = 'Tiempo de espera agotado';
            }
            
            mostrarErrorDetalle(mensajeError);
            $('#detalleMateriasModal').modal('show');
        },
        complete: function() {
            console.log('Carga de detalle completada');
        }
    });
}

function mostrarMateriasPrimasEnTabla(materiasPrimas) {
    console.log('Mostrando materias primas en tabla:', materiasPrimas);
    
    const tbody = $('#tablaMateriasDetalle tbody');
    tbody.empty();
    
    let html = '';
    let totalMaterias = 0;
    
    materiasPrimas.forEach(function(materia, index) {
        const cantidad = parseFloat(materia.cantidad || 0);
        const stock = parseInt(materia.stock || 0);
        const unidadMedida = materia.unidad_medida || 'No especificada';
        
        let stockClass = 'text-success';
        let stockIcon = 'fas fa-check';
        let stockText = 'Suficiente';
        
        if (stock === 0) {
            stockClass = 'text-danger';
            stockIcon = 'fas fa-times';
            stockText = 'Sin stock';
        } else if (stock < cantidad) {
            stockClass = 'text-warning';
            stockIcon = 'fas fa-exclamation-triangle';
            stockText = 'Stock insuficiente';
        }
        
        html += `
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <i class="fas fa-cube text-primary me-2"></i>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">${materia.nombre || 'Sin nombre'}</h6>
                            <small class="${stockClass}">
                                <i class="${stockIcon} me-1"></i>
                                ${stockText} (${stock} ${unidadMedida})
                            </small>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="fw-bold text-primary">${cantidad.toFixed(2)}</span>
                    <span class="text-muted small d-block">${unidadMedida}</span>
                </td>
                <td>
                    <span class="badge bg-light text-dark border">${unidadMedida}</span>
                </td>
            </tr>
        `;
        
        totalMaterias++;
    });
    
    if (totalMaterias > 0) {
        html += `
            <tr class="table-light">
                <td colspan="3" class="text-center">
                    <small class="text-muted">
                        <i class="fas fa-info-circle me-1"></i>
                        Total de materias primas utilizadas: ${totalMaterias}
                    </small>
                </td>
            </tr>
        `;
    }
    
    tbody.html(html);
}

function mostrarLoadingDetalle() {
    const tbody = $('#tablaMateriasDetalle tbody');
    tbody.html(`
        <tr>
            <td colspan="3" class="text-center py-5">
                <div class="d-flex flex-column align-items-center">
                    <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Cargando...</span>
                    </div>
                    <h5 class="text-primary">Cargando detalles...</h5>
                    <p class="text-muted mb-0">Obteniendo información de materias primas</p>
                </div>
            </td>
        </tr>
    `);
}

function mostrarMensajeNoFabricado() {
    const tbody = $('#tablaMateriasDetalle tbody');
    tbody.html(`
        <tr>
            <td colspan="3" class="text-center py-5">
                <div class="text-center text-muted">
                    <i class="fas fa-box-open fa-4x mb-3 opacity-50"></i>
                    <h4 class="text-muted">Producto No Fabricado</h4>
                    <p class="mb-0">Este producto no está compuesto por materias primas.</p>
                    <small>Los productos no fabricados se venden directamente sin proceso de producción.</small>
                </div>
            </td>
        </tr>
    `);
}

function mostrarMensajeSinMaterias() {
    const tbody = $('#tablaMateriasDetalle tbody');
    tbody.html(`
        <tr>
            <td colspan="3" class="text-center py-5">
                <div class="text-center text-warning">
                    <i class="fas fa-exclamation-triangle fa-4x mb-3"></i>
                    <h4 class="text-warning">Sin Materias Primas</h4>
                    <p class="mb-2">Este producto no tiene materias primas asignadas.</p>
                    <small class="text-muted">Puede editar el producto para agregar materias primas.</small>
                </div>
            </td>
        </tr>
    `);
}

function mostrarErrorDetalle(mensaje) {
    const tbody = $('#tablaMateriasDetalle tbody');
    tbody.html(`
        <tr>
            <td colspan="3" class="text-center py-5">
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-circle fa-4x mb-3"></i>
                    <h4 class="text-danger">Error al Cargar</h4>
                    <p class="mb-2">${mensaje}</p>
                    <small class="text-muted">Por favor, intente nuevamente.</small>
                </div>
            </td>
        </tr>
    `);
}

function limpiarModalDetalle() {
    console.log(' Limpiando modal de detalle...');
    
    $('#nombreProductoDetalle').text('');
    $('#fabricadoBadge').text('Fabricado').removeClass('bg-secondary').addClass('bg-primary');
    $('#tablaMateriasDetalle tbody').empty();
}

function formatearNumero(numero, decimales = 2) {
    return parseFloat(numero || 0).toFixed(decimales);
}

if (typeof window.detalleMaterias === 'undefined') {
    window.detalleMaterias = {
        cargarDetalle: cargarDetalleMaterias,
        limpiarModal: limpiarModalDetalle
    };
}