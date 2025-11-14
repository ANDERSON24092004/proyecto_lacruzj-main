<div class="modal fade" id="detalleFacturaModal" tabindex="-1" aria-labelledby="detalleFacturaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="detalleFacturaModalLabel">
                    <i class="fas fa-file-invoice me-2"></i> Detalle de Factura de Compra #<?= htmlspecialchars($factura['id_fact_com']) ?>
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
                                <p class="form-control-plaintext"><?= htmlspecialchars($factura['num_factura']) ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Proveedor</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($factura['proveedor']) ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Fecha</label>
                                <p class="form-control-plaintext"><?= htmlspecialchars($factura['fecha']) ?></p>
                            </div>
                        </div>
                        <div class="row g-3 mt-2">
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Estado</label>
                                <p class="form-control-plaintext">
                                    <?php if ($factura['vigencia'] == 1): ?>
                                        <span class="badge bg-success">Activa</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Anulada</span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total IVA</label>
                                <p class="form-control-plaintext">$<?= number_format($factura['total_iva'], 2) ?></p>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">Total General</label>
                                <p class="form-control-plaintext">$<?= number_format($factura['total_general'], 2) ?></p>
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
                        <?php if (empty($detalles)): ?>
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle me-2"></i> No hay detalles registrados para esta factura
                            </div>
                        <?php else: ?>
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
                                        <?php 
                                        $total_detalles = 0;
                                        foreach ($detalles as $detalle): 
                                            $subtotal = $detalle['cantidad'] * $detalle['costo_compra'];
                                            $total_detalles += $subtotal;
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($detalle['materia_prima']) ?></td>
                                                <td><?= htmlspecialchars($detalle['unidad_medida']) ?></td>
                                                <td class="text-center"><?= number_format($detalle['cantidad'], 0) ?></td>
                                                <td class="text-end">$<?= number_format($detalle['costo_compra'], 2) ?></td>
                                                <td class="text-end">$<?= number_format($subtotal, 2) ?></td>
                                                <td class="text-center"><?= number_format($detalle['stock'], 0) ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr class="table-active">
                                            <td colspan="4" class="text-end fw-bold">Subtotal:</td>
                                            <td class="text-end fw-bold">$<?= number_format($total_detalles, 2) ?></td>
                                            <td></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php endif; ?>
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
