<div class="modal fade" id="modalEditarUnidad" tabindex="-1" aria-labelledby="editarUnidadModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Editar Unidad de Medida de Materia Prima
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formEditarUnidadM" action="index.php?c=UnidadMedidaControlador&m=actualizar&tab=unidades" method="POST">

                    <input type="hidden" name="id_unidad_medida" id="id_unidad_medida">

                    <input type="hidden" name="tab" value="<?php echo $activeTab; ?>">
                    <div class="mb-3">
                        <label class="form-label">Nombre de la Unidad</label>
                        <input type="text" class="form-control" name="nombre" id="editar_nombreM" required>
                        <div id="editar_nombre_error" class="text-danger small mt-1 d-none"></div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formEditarUnidadM" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>