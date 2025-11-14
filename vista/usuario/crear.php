<div class="modal fade" id="registrarUsuarioModal" tabindex="-1" aria-labelledby="registrarUsuarioModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #4e54c8, #8f94fb);">
                <h5 class="modal-title" id="registrarUsuarioModalLabel">
                    <i class="fas fa-user-plus me-2"></i> Registro de Nuevo Usuario
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <form id="formusuario" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div id="crear_btnerror" class="error-message text-danger small mt-2 mb-3 p-2 bg-light rounded" style="display: none;"></div>
                        <div class="col-md-6 mb-3">
                            <label for="cedula" class="form-label">Cédula</label>
                            <input type="text" class="form-control" id="cedula" name="cedula" 
                                   pattern="[0-9]{6,10}" title="La cédula debe contener solo números (6-10 dígitos)"
                                   placeholder="Ej: 12345678">
                            <div id="cedula_error" class="text-danger small mt-1"></div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label">Nombre de Usuario</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                   placeholder="Ingrese el nombre de usuario">
                            <div id="nombre_error" class="text-danger small mt-1"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="telefono" name="telefono"
                            placeholder="Ej: 0412-1234567">
                            <div id="telefono_error" class="text-danger small mt-1"></div>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo"
                                   placeholder="Ej: usuario@empresa.com">
                            <div id="correo_error" class="text-danger small mt-1"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_rol" class="form-label">Rol </label>
                            <select class="form-select" id="id_rol" name="id_rol">
                                <option value="" selected disabled>Seleccione un rol</option>
                            </select>
                            <div id="rol_error" class="text-danger small mt-1"></div>
                        </div>
                      
                        <div class="col-md-6 mb-3">
                            <label for="clave" class="form-label">Contraseña</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="clave" name="clave"
                                minlength="6" placeholder="Mínimo 6 caracteres">
                            </div>
                            <div id="clave_error" class="text-danger small mt-1"></div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="confirmar_clave" class="form-label">Confirmar Contraseña </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave"
                                placeholder="Repita la contraseña">
                            </div>
                            <div id="confirmar_clave_error" class="text-danger small mt-1"></div>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer" style="background-color: #f8f9fa;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary" style="background: linear-gradient(135deg, #4e54c8, #8f94fb); border: none;">
                        <i class="fas fa-save me-2"></i> Guardar Usuario
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>