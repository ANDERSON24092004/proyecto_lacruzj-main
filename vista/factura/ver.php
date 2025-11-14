<div class="modal fade" id="modalVerFactura" tabindex="-1" aria-labelledby="modalVerFacturaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg"> 
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice me-2"></i> Detalle de Factura #<span id="modal-nro-fact">-</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <strong>Datos del Cliente</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-3"><strong>RIF:</strong> <span id="modal-rif">-</span></div>
                            <div class="col-md-4"><strong>Razón Social:</strong> <span id="modal-razon-social">-</span></div>
                            <div class="col-md-4"><strong>Teléfono:</strong> <span id="modal-telefono">-</span></div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-5"><strong>Correo:</strong> <span id="modal-correo">-</span></div>
                            <div class="col-md-5"><strong>Dirección:</strong> <span id="modal-direccion">-</span></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Datos de la Factura</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Número de Factura:</strong> <span id="modal-nro-fact-datos">-</span></div> 
                            <div class="col-md-4"><strong>Fecha:</strong> <span id="modal-fecha">-</span></div>
                            <div class="col-md-4"><strong>Estado:</strong> <span id="modal-estado" class="badge">-</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Condición de Pago:</strong> <span id="modal-condicion-pago">-</span></div>
                            <div class="col-md-4"><strong>Número de Orden:</strong> <span id="modal-numero-orden">-</span></div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Productos/Servicios</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="tabla-detalles-factura">
                                <thead>
                                    <tr>
                                        <th>Producto/Servicio</th>
                                        <th>Tipo</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="modal-detalles-body">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <strong>Resumen de Factura</strong>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 offset-md-2">
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="fw-bold">Subtotal:</span>
                                    <span id="modal-subtotal" class="fw-bold">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="fw-bold">Descuento:</span>
                                    <span id="modal-descuento" class="fw-bold">0%</span>
                                </div>
                                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                                    <span class="fw-bold">IVA (<span id="modal-iva-porcentaje">16</span>%):</span>
                                    <span id="modal-total-iva" class="fw-bold">0.00</span>
                                </div>
                                <div class="d-flex justify-content-between pt-2 total-general">
                                    <span class="fw-bold fs-5">Total General:</span>
                                    <span id="modal-total-general" class="fw-bold fs-5 text-primary">0.00</span>
                                </div>
                            </div>
                        </div>
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

<style>
#modalVerFactura .modal-lg {
    max-width: 800px; 
}

#modalVerFactura .card-header {
    font-size: 1.1rem;
}

#modalVerFactura .table th {
    background-color: #f8f9fa;
}

#modalVerFactura .badge {
    font-size: 0.8rem;
    padding: 0.4em 0.7em;
}

#modalVerFactura .total-general {
    border-top: 2px solid #dee2e6;
}
</style>