<div class="modal fade" id="registrarServicioModal" tabindex="-1" aria-labelledby="registrarServicioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="registrarServicioModalLabel">
                    <i class="fas fa-tools me-2"></i> Registrar Nuevo Servicio
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formServicio" action="index.php?c=ServicioControlador&m=guardar" method="POST">
                    <div class="row g-3">
                        <div id="crear_btnerror" class="error-message text-danger mt-3"></div>
                        <div class="col-md-12">
                            <label for="crear_nombre" class="form-label">Nombre del Servicio</label>
                            <input type="text" class="form-control" id="crear_nombre" name="nombre" required>
                            <div id="crear_nombre_error" style="color: #89879c;"> </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="crear_unidad_medida" class="form-label">Unidad de Medida</label>
                            <select class="form-select" id="crear_unidad_medida" name="id_unidad_medida" required>
                                <option value="" selected disabled>Seleccione una unidad</option>
                                <?php foreach ($unidades as $unidad): ?>
                                    <option value="<?= $unidad['id_unidad_medida'] ?>">
                                        <?= htmlspecialchars($unidad['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="crear_unidad_medida_error" style="color: #89879c;"> </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="crear_precio_base" class="form-label">Precio Base</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" min="0" class="form-control" id="crear_precio_base" name="precio_base" required>
                            </div>
                            <div id="crear_precio_base_error" style="color: #89879c;"> </div>
                        </div>
                        
                        <div class="col-12">
                            <label for="crear_descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="crear_descripcion" name="descripcion" rows="3"></textarea>
                            <div id="crear_descripcion_error" style="color: #89879c;"> </div>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer" style="background-color: #f8f9fa;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i> Cancelar
                </button>
                <button type="submit" form="formServicio" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                    <i class="fas fa-save me-2"></i> Guardar Servicio
                </button>
            </div>
        </div>
    </div>
</div>

