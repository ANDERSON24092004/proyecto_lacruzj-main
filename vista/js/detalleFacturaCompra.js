$(document).ready(function() {
    $('.btn-ver-detalle').on('click', function() {
        const id = $(this).data('id');
        cargarDetalleFactura(id);
    });

    function cargarDetalleFactura(id) {
        $.ajax({
            url: 'index.php?c=FacturaCompraControlador&m=verDetalle&id=' + id,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.factura && response.detalles) {
                    mostrarModalDetalle(response.factura, response.detalles);
                } else {
                    alert('Error al cargar los detalles de la factura');
                }
            },
            error: function() {
                alert('Error de conexión al cargar los detalles');
            }
        });
    }

    function mostrarModalDetalle(factura, detalles) {
        // Construir el contenido del modal
        let contenidoModal = `
            <div class="modal fade" id="detalleFacturaModal" tabindex="-1" aria-labelledby="detalleFacturaModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content border-0">
                        <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                            <h5 class="modal-title" id="detalleFacturaModalLabel">
                                <i class="fas fa-file-invoice me-2"></i> Detalle de Factura de Compra #${factura.id_fact_com}
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        
                        <div class="modal-body">
                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-info-circle me-2"></i> Información General
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Número de Factura</label>
                                            <p class="form-control-plaintext">${factura.num_factura}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Proveedor</label>
                                            <p class="form-control-plaintext">${factura.proveedor}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Fecha</label>
                                            <p class="form-control-plaintext">${factura.fecha}</p>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-2">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Estado</label>
                                            <p class="form-control-plaintext">
                                                ${factura.vigencia == 1 ? '<span class="badge bg-success">Activa</span>' : '<span class="badge bg-danger">Anulada</span>'}
                                            </p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Total IVA</label>
                                            <p class="form-control-plaintext">$${parseFloat(factura.total_iva).toFixed(2)}</p>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Total General</label>
                                            <p class="form-control-plaintext">$${parseFloat(factura.total_general).toFixed(2)}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-4">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="fas fa-list me-2"></i> Detalles de Materia Prima
                                    </h6>
                                </div>
                                <div class="card-body">
        `;

        if (detalles.length === 0) {
            contenidoModal += `
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle me-2"></i> No hay detalles registrados para esta factura
                </div>
            `;
        } else {
            contenidoModal += `
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Materia Prima</th>
                                <th>Unidad de Medida</th>
                                <th class="text-center">Cantidad</th>
                                <th class="text-end">Costo Unitario</th>
                                <th class="text-end">Subtotal</th>
                                <th class="text-center">Stock Actual</th>
                            </tr>
                        </thead>
                        <tbody>
            `;

            let total_detalles = 0;
            detalles.forEach(detalle => {
                const subtotal = detalle.cantidad * detalle.costo_compra;
                total_detalles += subtotal;
                
                contenidoModal += `
                    <tr>
                        <td>${detalle.materia_prima}</td>
                        <td>${detalle.unidad_medida}</td>
                        <td class="text-center">${parseInt(detalle.cantidad).toLocaleString()}</td>
                        <td class="text-end">$${parseFloat(detalle.costo_compra).toFixed(2)}</td>
                        <td class="text-end">$${parseFloat(subtotal).toFixed(2)}</td>
                        <td class="text-center">${parseInt(detalle.stock).toLocaleString()}</td>
                    </tr>
                `;
            });

            contenidoModal += `
                        </tbody>
                        <tfoot>
                            <tr class="table-active">
                                <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                <td class="text-end fw-bold">$${parseFloat(total_detalles).toFixed(2)}</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            `;
        }

        contenidoModal += `
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer" style="background-color: #f8f9fa;">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="fas fa-times me-2"></i> Cerrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        $('#detalleFacturaModal').remove();
        
        $('body').append(contenidoModal);
        
        const modal = new bootstrap.Modal(document.getElementById('detalleFacturaModal'));
        modal.show();
    }

    $(document).on('hidden.bs.modal', '#detalleFacturaModal', function () {
        $(this).remove();
    });
});