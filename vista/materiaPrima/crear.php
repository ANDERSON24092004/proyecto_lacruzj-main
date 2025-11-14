<div class="modal fade" id="registrarMateriaModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #1e88e5, #64b5f6);">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i> Registrar Nueva Materia Prima
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formCrearMateria" action="index.php?c=MateriaPrimaControlador&m=crear" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre de la Materia Prima</label>
                            <input type="text" class="form-control" id="nombre" name="nombre">
                            <div id="nombre_error" class="text-danger small mt-1 d-none"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="id_unidad_medida" class="form-label">Unidad de Medida</label>
                            <select class="form-select" id="id_unidad_medida" name="id_unidad_medida">
                                <option value="">Seleccionar unidad...</option>
                                <?php
                                $unidadMedida = new UnidadMedida();
                                $unidades = $unidadMedida->listar();
                                foreach ($unidades as $unidad):
                                ?>
                                    <option value="<?= $unidad['id_unidad_medida'] ?>">
                                        <?= htmlspecialchars($unidad['nombre']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="id_unidad_medida_error" class="text-danger small mt-1 d-none"></div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="stock" class="form-label">Stock Inicial</label>
                            <input type="number" class="form-control" id="stock" name="stock" value="0" min="0" step="1">
                            <div id="stock_error" class="text-danger small mt-1 d-none"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="costo" class="form-label">Costo por Unidad ($)</label>
                            <input type="number" class="form-control" id="costo" name="costo" value="0.00" min="0" step="0.01">
                            <div id="costo_error" class="text-danger small mt-1 d-none"></div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Guardar Materia Prima
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>