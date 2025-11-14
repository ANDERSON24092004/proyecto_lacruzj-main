<div class="modal fade" id="modalCondicionPago" tabindex="-1" aria-labelledby="CondicionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="IvaModalLabel">
                    <i class="fas fa-weight me-2"></i> Registrar Condicion de Pago
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formCondicion" action="index.php?c=CondicionPagoControlador&m=guardarAjax&tab=condicion-pago" method="POST">
                    
                    <input type="hidden" name="tab" value="<?php echo $activeTab; ?>">
                    
                    <div class="mb-3">
                        <label for="crear_forma" class="form-label">Registrar Condición de Pago</label>
                        <input type="text" class="form-control" id="crear_forma" name="forma">
                        <div id="forma_error" class="text-danger small mt-1 d-none"></div>
                    </div>
                </form> 
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formCondicion" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Condicion de Pago
                </button>
            </div>
        </div>
    </div>
</div>