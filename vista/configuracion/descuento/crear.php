<div class="modal fade" id="modalDescuento" tabindex="-1" aria-labelledby="DescuentoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="DescuentoModalLabel">
                    <i class="fas fa-weight me-2"></i> Registrar Descuento
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formDescuento" action="index.php?c=DescuentoControlador&m=guardarAjax&tab=descuento" method="POST">
                    
                    <input type="hidden" name="tab" value="<?php echo $activeTab; ?>">
                    
                    <div class="mb-3">
                        <label for="crear_descuento" class="form-label">Registrar Descuento</label>
                        <input type="number" class="form-control" id="crear_descuento" name="porcentaje">
                        <div id="porcentaje_error" class="text-danger small mt-1 d-none"></div>
                    </div>
                </form> 
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formDescuento" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Descuento
                </button>
            </div>
        </div>
    </div>
</div>