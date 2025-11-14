<div class="modal fade" id="verFacturaModal" tabindex="-1" aria-labelledby="verFacturaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-file-invoice me-2"></i> Detalle de Factura <span id="ver_id_factura"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <strong><i class="fas fa-user me-2"></i>Datos del Cliente</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-3"><strong>RIF:</strong> <span id="ver_rif"></span></div>
                            <div class="col-md-3"><strong>Razón Social:</strong> <span id="ver_razon_social"></span></div>
                            <div class="col-md-3"><strong>Teléfono:</strong> <span id="ver_telefono"></span></div>
                            <div class="col-md-3"><strong>Correo:</strong> <span id="ver_correo"></span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <strong>Dirección:</strong> <span id="ver_direccion"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4" id="card-productos">
                    <div class="card-header bg-info text-white">
                        <strong><i class="fas fa-box me-2"></i>Productos</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Producto</th>
                                        <th>Presentación</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="ver_productos_tbody">
                                </tbody>
                                <tfoot id="ver_productos_footer" style="display: none;">
                                    <tr class="table-active">
                                        <td colspan="4" class="text-end"><strong>Subtotal Productos:</strong></td>
                                        <td><strong id="ver_subtotal_productos">0.00 Bs.</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4" id="card-servicios">
                    <div class="card-header bg-warning text-white">
                        <strong><i class="fas fa-tools me-2"></i>Servicios</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Servicio</th>
                                        <th>Unidad</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="ver_servicios_tbody">
                                </tbody>
                                <tfoot id="ver_servicios_footer" style="display: none;">
                                    <tr class="table-active">
                                        <td colspan="4" class="text-end"><strong>Subtotal Servicios:</strong></td>
                                        <td><strong id="ver_subtotal_servicios">0.00 Bs.</strong></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong><i class="fas fa-file-alt me-2"></i>Datos de la Factura</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>N° Factura:</strong> <span id="ver_nro_factura"></span></div>
                            <div class="col-md-3"><strong>Fecha:</strong> <span id="ver_fecha"></span></div>
                            <div class="col-md-3"><strong>Fecha Límite:</strong> <span id="ver_fecha_limite"></span></div>
                            <div class="col-md-3"><strong>N° Orden:</strong> <span id="ver_orden_compra"></span></div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>Estado:</strong> <span id="ver_estado"></span></div>
                            <div class="col-md-3"><strong>Vigencia:</strong> <span id="ver_vigencia"></span></div>
                            <div class="col-md-3"><strong>Total Abonado:</strong> <span id="ver_total_abonado"></span></div>
                            <div class="col-md-3"><strong>Saldo Pendiente:</strong> <span id="ver_saldo_pendiente"></span></div>
                        </div>
                        <div class="row">
                            <div class="col-md-4"><strong>Subtotal:</strong> <span id="ver_subtotal"></span> Bs.</div>
                            <div class="col-md-4"><strong>Total IVA:</strong> <span id="ver_total_iva"></span> Bs.</div>
                            <div class="col-md-4"><strong>Total General:</strong> <span id="ver_total_general"></span> Bs.</div>
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