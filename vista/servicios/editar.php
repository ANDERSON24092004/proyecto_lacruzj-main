<div class="modal fade" id="editarServicioModal" tabindex="-1" aria-labelledby="editarServicioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i> Editar Servicio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formEditarServicio" action="index.php?c=ServicioControlador&m=actualizar" method="POST">
                    <input type="hidden" name="id_servicio" id="id_servicio">
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label">Nombre del Servicio</label>
                            <input type="text" class="form-control" name="nombre" id="nombre" required>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Unidad de Medida</label>
                            <select class="form-select" name="id_unidad_medida" id="id_unidad_medida" required>
                                <option value="" disabled>Seleccione una unidad</option>
                                <?php foreach ($unidades as $unidad): ?>
                                    <option value="<?= $unidad['id_unidad_medida'] ?>">
                                        <?= htmlspecialchars($unidad['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Precio Base</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" name="precio_base" id="precio_base" required>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="descripcion" rows="3"></textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formEditarServicio" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Cambios
                </button>
            </div>
        </div>
    </div>
</div>