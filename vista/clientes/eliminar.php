<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e88e5, #64b5f6);">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-circle me-2"></i> Confirmar Eliminación
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i class="fas fa-trash-alt fa-4x" style="color: #aaa;"></i>
                </div>
                <h5 class="fw-bold mb-2 text-dark">¿Confirmar eliminación?</h5>
                <p class="text-muted mb-0">El cliente será eliminado permanentemente del sistema.</p>
                <p class="text-muted">Esta acción no puede revertirse.</p>
            </div>
            
            <div class="modal-footer justify-content-center border-top-0" style="background-color: #f5f7fa;">
                <button type="button" class="btn btn-lg btn-outline-secondary px-4 me-3" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <a id="btnEliminarConfirmado" href="#" class="btn btn-lg btn-primary px-4 shadow-sm">
                    <i class="fas fa-check me-2"></i> Confirmar
                </a>
            </div>
        </div>
    </div>
</div>