<div class="modal fade" id="crearFacturaModal" tabindex="-1" aria-labelledby="crearFacturaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="crearFacturaModalLabel">
                    <i class="fas fa-file-invoice-dollar me-2"></i> Registrar Factura
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <div id="errores-generales" class="alert alert-danger d-none" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <span id="mensaje-error-general"></span>
                </div>
                
                <form id="formFactura" action="index.php?c=FacturaControlador&m=guardar_factura" method="POST">
                    <div class="row g-3 mb-4">
                        
                        <input type="hidden" name="total_iva" id="total_iva_input" value="0">
                        <input type="hidden" name="total_general" id="total_general_input" value="0">
                        
                        <div class="col-md-4">
                            <label for="rif" class="form-label">Cliente</label>
                            <select class="form-select" id="rif" name="rif">
                                <option value="" selected disabled>Seleccione un cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?= $cliente['rif'] ?>">
                                        <?= htmlspecialchars($cliente['rif'] . ' - ' . $cliente['razon_social']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="error-rif">
                                Por favor seleccione un cliente
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="id_condicion_pago" class="form-label">Condición de Pago</label>
                            <select class="form-select" id="id_condicion_pago" name="id_condicion_pago">
                                <option value="" selected disabled>Seleccione condición de pago</option>
                                <?php foreach ($condicionesPago as $pago): ?>
                                    <option value="<?= $pago['id_condicion_pago'] ?>">
                                        <?= htmlspecialchars($pago['forma']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="error-pago">
                                Por favor seleccione una condición de pago
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="numero_orden" class="form-label">N° Orden</label>
                            <input type="text" class="form-control" id="numero_orden" name="numero_orden" pattern="[0-9]+" title="Solo se permiten números">
                            <div class="invalid-feedback" id="error-orden" style="display: none">
                                Por favor ingrese un número de orden válido (solo números)
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="fecha" class="form-label">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" 
                            value="<?= date('Y-m-d') ?>">
                            <div class="invalid-feedback" id="error-fecha">
                                Por favor ingrese una fecha válida
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label for="id_descuento" class="form-label">Descuento</label>
                            <select class="form-select" id="id_descuento" name="id_descuento">
                                <option value="" selected>Sin descuento</option>
                                <?php foreach ($descuentos as $descuento): ?>
                                    <option value="<?= $descuento['id'] ?>">
                                        <?= htmlspecialchars($descuento['porcentaje'] . '%') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="id_iva" class="form-label">IVA</label>
                            <select class="form-select" id="id_iva" name="id_iva">
                                <option value="" selected disabled>Seleccione IVA</option>
                                <?php foreach ($ivas as $i): ?>
                                    <option value="<?= $i['id_iva'] ?>"<?= $i['porcentaje'] == 16 ? 'selected' : ''?>>
                                        <?= htmlspecialchars($i['porcentaje'] . '%') ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="invalid-feedback" id="error-iva">
                                Por favor seleccione un porcentaje de IVA
                            </div>
                        </div>
                    </div>

                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <strong>Productos y Servicios</strong>
                            <div class="btn-group float-end" role="group">
                                <button type="button" class="btn btn-sm btn-success" id="agregarProducto">
                                    <i class="fas fa-cube me-1"></i> Agregar Producto
                                </button>
                                <button type="button" class="btn btn-sm btn-info" id="agregarServicio">
                                    <i class="fas fa-cogs me-1"></i> Agregar Servicio
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div id="items-container">
                            </div>
                            <div id="error-items" class="text-danger small mt-2 d-none">
                                <i class="fas fa-exclamation-circle me-1"></i>
                                <span></span>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <strong>Resumen de la Factura</strong>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>Subtotal:</strong> <span id="subtotal-total">0.00</span> BS</p>
                                    <p><strong>Descuento:</strong> <span id="descuento-total">0.00</span> BS</p>
                                    <p><strong>Subtotal con descuento:</strong> <span id="subtotal-con-descuento">0.00</span> BS</p>
                                    <p><strong>IVA (<span id="iva-porcentaje">16</span>%):</strong> <span id="iva-total">0.00</span> BS</p>
                                </div>
                                <div class="col-md-6">
                                    <p class="h4"><strong>Total General:</strong> <span id="total-general">0.00</span> BS</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btn-cancelar">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formFactura" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Factura
                </button>
            </div>
        </div>
    </div>
</div>

<div style="display: none;">
    <div id="template-producto" class="item-row row g-3 mb-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label small text-muted">Producto</label>
            <select class="form-select item-select producto-select" name="detalles[INDEX][id_inv]" required>
                <option value="" selected disabled>Seleccione un producto</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= $producto['id_inv'] ?>" 
                            data-tipo="1"
                            data-costo="<?= $producto['costo'] ?>"
                            data-costo-mayor="<?= $producto['precio_mayor'] ?? $producto['costo'] ?>"
                            data-unidad="<?= $producto['unidad_medida'] ?>"
                            data-stock="<?= $producto['stock'] ?>">
                        <?= htmlspecialchars($producto['nombre'] . ' - $' . $producto['costo'] . ' (' . $producto['unidad_medida'] . ') - Stock: ' . $producto['stock']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="form-text text-info stock-info" style="display: none;"></div>
            <div class="invalid-feedback">
                Por favor seleccione un producto
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted">Cantidad</label>
            <input type="number" class="form-control cantidad-item" name="detalles[INDEX][cantidad]" 
                   min="1" value="1" placeholder="Cantidad" required>
            <div class="invalid-feedback">
                Por favor ingrese una cantidad válida
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted">Precio Unitario</label>
            <input type="text" class="form-control precio-unitario" readonly placeholder="0.00">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">Subtotal</label>
            <input type="text" class="form-control subtotal-item" readonly placeholder="0.00">
        </div>
        <div class="col-md-1">
            <label class="form-label small text-muted d-block">&nbsp;</label>
            <button type="button" class="btn btn-danger btn-sm quitar-item" title="Eliminar item">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>

<div style="display: none;">
    <div id="template-servicio" class="item-row row g-3 mb-3 align-items-center">
        <div class="col-md-4">
            <label class="form-label small text-muted">Servicio</label>
            <select class="form-select item-select servicio-select" name="detalles[INDEX][id_inv]" required>
                <option value="" selected disabled>Seleccione un servicio</option>
                <?php foreach ($servicios as $servicio): ?>
                    <option value="<?= $servicio['id_inv'] ?>" 
                            data-tipo="2"
                            data-costo="<?= $servicio['costo'] ?>"
                            data-unidad="<?= $servicio['unidad_medida'] ?>">
                        <?= htmlspecialchars($servicio['nombre'] . ' - $' . $servicio['costo'] . ' (' . $servicio['unidad_medida'] . ')') ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <div class="invalid-feedback">
                Por favor seleccione un servicio
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted">Cantidad</label>
            <input type="number" class="form-control cantidad-item" name="detalles[INDEX][cantidad]" 
                   min="1" value="1" placeholder="Cantidad" required>
            <div class="invalid-feedback">
                Por favor ingrese una cantidad válida
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label small text-muted">Precio Unitario</label>
            <input type="text" class="form-control precio-unitario" readonly placeholder="0.00">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted">Subtotal</label>
            <input type="text" class="form-control subtotal-item" readonly placeholder="0.00">
        </div>
        <div class="col-md-1">
            <label class="form-label small text-muted d-block">&nbsp;</label>
            <button type="button" class="btn btn-danger btn-sm quitar-item" title="Eliminar item">
                <i class="fas fa-times"></i>
            </button>
        </div>
    </div>
</div>