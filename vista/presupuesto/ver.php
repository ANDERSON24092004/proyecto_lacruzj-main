<div class="modal fade" id="modalVerPresupuesto" tabindex="-1" aria-labelledby="modalVerPresupuestoLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-eye me-2"></i> Detalle de Presupuesto <span id="ver_id_factura"></span>
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
                            <div class="col-md-4"><strong>RIF:</strong> <span id="ver_rif"></span></div>
                            <div class="col-md-4"><strong>Razón Social:</strong> <span id="ver_razon_social"></span></div>
                            <div class="col-md-4"><strong>Nombre:</strong> <span id="ver_nombre_cliente"></span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Teléfono:</strong> <span id="ver_telefono"></span></div>
                            <div class="col-md-4"><strong>Correo:</strong> <span id="ver_correo"></span></div>
                            <div class="col-md-4"><strong>Condición Pago:</strong> <span id="ver_condicion_pago">Presupuesto</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <strong>Dirección:</strong> <span id="ver_direccion"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <strong>Productos</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Producto</th>
                                        <th>Nombre</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="ver_productos_tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <strong>Servicios</strong>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>ID Servicio</th>
                                        <th>Nombre</th>
                                        <th>Cantidad</th>
                                        <th>Precio Unitario</th>
                                        <th>Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody id="ver_servicios_tbody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <strong>Datos del Presupuesto</strong>
                    </div>
                    <div class="card-body">
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Fecha:</strong> <span id="ver_fecha"></span></div>
                            <div class="col-md-4"><strong>N° Orden:</strong> <span id="ver_orden_compra"></span></div>
                            <div class="col-md-4"><strong>Vigencia:</strong> <span id="ver_status">Activo</span></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-4"><strong>Total General:</strong> <span id="ver_total_general"></span> BS</div>
                            <div class="col-md-4"><strong>Total IVA:</strong> <span id="ver_total_iva"></span> BS</div>
                            <div class="col-md-4"><strong>Subtotal:</strong> <span id="ver_subtotal"></span> BS</div>
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