<div class="modal fade" id="confirmarReactivarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #28a745, #20c997);">
                <h5 class="modal-title">
                    <i class="fas fa-check-circle me-2"></i> Confirmar Reactivación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-undo fa-4x" style="color: #28a745;"></i>
                </div>
                <h5 class="fw-bold mb-2 text-dark">¿Confirmar Reactivación?</h5>
                <p class="text-muted">La factura volverá a estar vigente.</p>
            </div>
            
            <div class="modal-footer justify-content-center border-top-0" style="background-color: #f5f7fa;">
                <button type="button" class="btn btn-lg btn-outline-secondary px-4 me-3" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button id="btnReactivarConfirmado" class="btn btn-lg btn-success px-4 shadow-sm">
                    <i class="fas fa-check me-2"></i> Reactivar
                </button>
            </div>
            
            <input type="hidden" id="nro_fact_reactivar">
        </div>
    </div>
</div>