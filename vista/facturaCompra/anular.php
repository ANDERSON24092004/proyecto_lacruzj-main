<div class="modal fade" id="confirmarAnularModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #dc3545, #e4606d);">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-circle me-2"></i> Confirmar Anulación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-ban fa-4x" style="color: #dc3545;"></i>
                </div>
                <h5 class="fw-bold mb-2 text-dark">¿Confirmar Anulación?</h5>
                <p class="text-muted">La factura se marcará como anulado pero permanecerá en el sistema.</p>

                <input type="hidden" id="id_fact_com" name="id_fact_com_anular" value="">
            </div>
            
            <div class="modal-footer justify-content-center border-top-0" style="background-color: #f5f7fa;">
                <button type="button" class="btn btn-lg btn-outline-secondary px-4 me-3" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <a id="btnAnularConfirmado" href="#" class="btn btn-lg btn-danger px-4 shadow-sm">
                    <i class="fas fa-check me-2"></i> Anular
                </a>
            </div>
        </div>
    </div>
</div>