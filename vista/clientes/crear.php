<div class="modal fade" id="crearClienteModal" tabindex="-1" aria-labelledby="crearClienteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title text-white" id="crearClienteModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Registrar Cliente
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <form action="index.php?c=ClienteControlador&m=guardarAjax" method="POST" id="formCrearCliente">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="rif" class="form-label">RIF</label>
                            <div class="input-group">
                                <span class="input-group-text">J-</span>
                                <input type="text" class="form-control" id="rif" name="rif"
                                placeholder="Ej: 12345678" maxlength="8" pattern="\d{8}">
                            </div>
                            <div id="riferror" style="color:#dc3545; font-size: 0.875em; margin-top: 0.25rem;"></div>       
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="razon_social" class="form-label">Razón Social</label>
                            <input type="text" class="form-control" id="razon_social" name="razon_social"
                            placeholder="Ingrese la razón social del cliente">
                            <div id="razonerror" style="color: #dc3545; font-size: 0.875em; margin-top: 0.25rem;"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono"
                            placeholder="Ej: 0412-1234567" maxlength="12">
                            <div id="telefonoerror" style="color: #dc3545; font-size: 0.875em; margin-top: 0.25rem;"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="email"
                            placeholder="Ej: cliente@example.com">
                            <div id="correoerror" style="color: #dc3545; font-size: 0.875em; margin-top: 0.25rem;"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="direccion" class="form-label">Dirección</label>
                        <textarea class="form-control" id="direccion" name="direccion" rows="3"
                        placeholder="Ingrese la dirección completa del cliente"></textarea>
                        <div id="direccionerror" style="color: #dc3545; font-size: 0.875em; margin-top: 0.25rem;"></div>
                    </div>
                </div>
                <div class="modal-footer" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" id="guardar" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                      <i class="fas fa-save me-2"></i> Guardar Cliente      
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>