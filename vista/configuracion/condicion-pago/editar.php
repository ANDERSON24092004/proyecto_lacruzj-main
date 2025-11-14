<div class="modal fade" id="modalEditarCondicion" tabindex="-1" aria-labelledby="editarCondicionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Editar Condicion de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formEditarCondicion" action="index.php?c=CondicionPagoControlador&m=actualizarAjax&tab=condicion-pago" method="POST">

                    <input type="hidden" name="id_condicion_pago" id="id_condicion_pago">
                    <input type="hidden" name="tab" value="<?php echo $activeTab; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Condicion de Pago</label>
                        <input type="text" class="form-control" name="forma" id="editar_forma">
                        <div id="editar_forma_error" class="text-danger small mt-1 d-none"></div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formEditarCondicion" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

