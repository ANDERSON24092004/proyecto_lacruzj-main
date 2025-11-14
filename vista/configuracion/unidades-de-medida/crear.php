<div class="modal fade" id="modalUnidad" tabindex="-1" aria-labelledby="UnidadMedidaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="UnidadMedidaModalLabel">
                    <i class="fas fa-weight me-2"></i> Registrar Nueva Unidad
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formUnidadM" action="index.php?c=UnidadMedidaControlador&m=guardar&tab=unidades" method="POST">

                    <input type="hidden" name="tab" value="<?php echo $activeTab; ?>">
                    
                    <div class="mb-3">
                        <label for="crear_nombreM" class="form-label">Nombre de la Unidad</label>
                        <input type="text" class="form-control" id="crear_nombreM" name="nombre">
                        <div id="nombre_error" class="text-danger small mt-1 d-none"></div>
                    </div>
                </form> 
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formUnidadM" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Unidad
                </button>
            </div>
        </div>
    </div>
</div>