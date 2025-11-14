<div class="modal fade" id="modalIva" tabindex="-1" aria-labelledby="IvaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="IvaModalLabel">
                    <i class="fas fa-weight me-2"></i> Registrar IVA
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formIva" action="index.php?c=IvaControlador&m=guardar&tab=iva" method="POST">
                    
                    <input type="hidden" name="tab" value="<?php echo $activeTab; ?>">
                    
                    <div class="mb-3">
                        <label for="crear_iva" class="form-label">Registrar IVA</label>
                        <input type="number" class="form-control" id="crear_iva" name="porcentaje">
                        <div id="porcentaje_error" class="text-danger small mt-1 d-none"></div>
                    </div>
                </form> 
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formIva" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar IVA
                </button>
            </div>
        </div>
    </div>
</div>