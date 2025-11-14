<div class="modal fade" id="modalEditarPresentacion" tabindex="-1" aria-labelledby="editarPresentacionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Editar Presentacion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formEditarPresentacion" action="index.php?c=PresentacionControlador&m=actualizarAjax&tab=presentacion" method="POST">

                    <input type="hidden" name="id_pres" id="id_pres">
                    <input type="hidden" name="tab" value="<?php echo $activeTab; ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Editar Presentacion</label>
                        <input type="text" class="form-control" name="nombre" id="editar_presentacion">
                        <div id="editar_presentacion_error" class="text-danger small mt-1 d-none"></div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formEditarPresentacion" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>

