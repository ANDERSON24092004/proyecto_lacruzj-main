<div class="modal fade" id="registrarPagoModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i> Registrar Pago Completo
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formRegistrarPago">
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Importante:</strong> Se registrará el pago completo de la factura.
                    </div>
                    
                    <div class="mb-3">
                        <label for="fecha_pago" class="form-label fw-semibold">Fecha del Pago</label>
                        <input type="date" class="form-control" id="fecha_pago" name="fecha_pago" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Información de la cuenta:</strong>
                            <div id="infoCuenta" class="mt-1"></div>
                        </small>
                    </div>
                    
                    <input type="hidden" id="nro_fact_pago" name="nro_fact">
                </div>
                
                <div class="modal-footer justify-content-center border-top-0" style="background-color: #f5f7fa;">
                    <button type="button" class="btn btn-lg btn-outline-secondary px-4 me-3" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-lg btn-success px-4 shadow-sm">
                        <i class="fas fa-check-circle me-2"></i> Registrar Pago Completo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>