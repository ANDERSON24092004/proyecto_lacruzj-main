<?php
$titulo_pagina = "Mi Perfil";
require_once 'vista/parcial/header.php';
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-user-circle me-2"></i>
                        Mi Perfil
                    </h4>
                </div>
                <div class="card-body">
                    <form id="formPerfil" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="text-primary mb-4">Información Personal</h5>
                                
                                <div class="mb-3">
                                    <label for="cedula" class="form-label">Cédula</label>
                                    <input type="text" class="form-control" id="cedula" 
                                    value="<?php echo htmlspecialchars($usuarioData['cedula']); ?>" 
                                    readonly>
                                    <div class="form-text">La cédula no puede ser modificada.</div>
                                </div>

                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombre de Usuario</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" 
                                    value="<?php echo htmlspecialchars($usuarioData['username']); ?>" 
                                    maxlength="50" required>
                                    <div class="invalid-feedback" id="error-nombre"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="telefono" class="form-label">Teléfono</label>
                                    <input type="text" class="form-control" id="telefono" name="telefono" 
                                    value="<?php echo htmlspecialchars($usuarioData['telefono']); ?>" required>
                                    <div class="form-text">Formato: 0412-1234567 o 04121234567</div>
                                    <div class="invalid-feedback" id="error-telefono"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" id="correo" name="correo" 
                                    value="<?php echo htmlspecialchars($usuarioData['correo']); ?>" required>
                                    <div class="invalid-feedback" id="error-correo"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="id_rol" class="form-label">Rol</label>
                                    <select class="form-control" id="id_rol" name="id_rol" required>
                                        <option value="">Seleccione un rol</option>
                                        <?php foreach ($roles as $rol): ?>
                                            <option value="<?php echo $rol['id_rol']; ?>" 
                                                <?php echo $usuarioData['id_rol'] == $rol['id_rol'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($rol['tipo_usuario']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback" id="error-rol"></div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <h5 class="text-primary mb-4">Cambiar Contraseña</h5>
                                <div class="form-text mb-3">
                                    Completa estos campos solo si deseas cambiar tu contraseña.
                                </div>

                                <div class="mb-3">
                                    <label for="clave_actual" class="form-label">Contraseña Actual</label>
                                    <input type="password" class="form-control" id="clave_actual" name="clave_actual">
                                    <div class="invalid-feedback" id="error-clave_actual"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="clave_nueva" class="form-label">Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="clave_nueva" name="clave_nueva" minlength="6">
                                    <div class="form-text">Mínimo 6 caracteres.</div>
                                    <div class="invalid-feedback" id="error-clave_nueva"></div>
                                </div>

                                <div class="mb-3">
                                    <label for="confirmar_clave" class="form-label">Confirmar Nueva Contraseña</label>
                                    <input type="password" class="form-control" id="confirmar_clave" name="confirmar_clave" minlength="6">
                                    <div class="invalid-feedback" id="error-confirmar_clave"></div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2" id="btnGuardar">
                                    <i class="fas fa-save me-1"></i>
                                    Guardar Cambios
                                </button>
                                <a href="index.php?c=loginControlador&m=home" class="btn btn-secondary">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    Volver al Inicio
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'vista/parcial/mensaje_modal.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formPerfil');
    const btnGuardar = document.getElementById('btnGuardar');
    
    // Validación en tiempo real
    document.getElementById('telefono').addEventListener('input', function(e) {
        validarTelefono(e.target);
    });
    
    document.getElementById('nombre').addEventListener('blur', function(e) {
        validarNombre(e.target);
    });
    
    document.getElementById('correo').addEventListener('blur', function(e) {
        validarCorreo(e.target);
    });
    
    document.getElementById('clave_nueva').addEventListener('input', function() {
        validarContraseñas();
    });
    
    document.getElementById('confirmar_clave').addEventListener('input', function() {
        validarContraseñas();
    });

    // Envío del formulario con Ajax
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validarFormulario()) {
            guardarPerfil();
        }
    });

    function validarTelefono(input) {
        const telefono = input.value.trim();
        const regex = /^[0-9+\-\s()]{10,15}$/;
        const errorElement = document.getElementById('error-telefono');
        
        if (!telefono) {
            mostrarError(input, errorElement, 'El teléfono es obligatorio');
            return false;
        }
        
        if (!regex.test(telefono)) {
            mostrarError(input, errorElement, 'Formato de teléfono inválido. Use: 0412-1234567 o 04121234567');
            return false;
        }
        
        mostrarExito(input, errorElement);
        return true;
    }

    function validarNombre(input) {
        const nombre = input.value.trim();
        const errorElement = document.getElementById('error-nombre');
        
        if (!nombre) {
            mostrarError(input, errorElement, 'El nombre de usuario es obligatorio');
            return false;
        }
        
        if (nombre.length < 3) {
            mostrarError(input, errorElement, 'El nombre debe tener al menos 3 caracteres');
            return false;
        }
        
        if (nombre.length > 50) {
            mostrarError(input, errorElement, 'El nombre no puede exceder los 50 caracteres');
            return false;
        }
        
        // Verificar disponibilidad del nombre
        verificarNombreDisponible(nombre, '<?php echo $usuarioData['cedula']; ?>')
            .then(disponible => {
                if (!disponible) {
                    mostrarError(input, errorElement, 'Este nombre de usuario ya está en uso');
                    return false;
                } else {
                    mostrarExito(input, errorElement);
                    return true;
                }
            })
            .catch(() => {
                // En caso de error, permitir el envío pero mostrar advertencia
                mostrarExito(input, errorElement);
                return true;
            });
        
        return true;
    }

    function validarCorreo(input) {
        const correo = input.value.trim();
        const errorElement = document.getElementById('error-correo');
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!correo) {
            mostrarError(input, errorElement, 'El correo electrónico es obligatorio');
            return false;
        }
        
        if (!regex.test(correo)) {
            mostrarError(input, errorElement, 'Ingrese una dirección de correo válida');
            return false;
        }
        
        mostrarExito(input, errorElement);
        return true;
    }

    function validarContraseñas() {
        const claveNueva = document.getElementById('clave_nueva').value;
        const confirmarClave = document.getElementById('confirmar_clave').value;
        const errorElement = document.getElementById('error-confirmar_clave');
        
        if (claveNueva || confirmarClave) {
            if (claveNueva.length < 6) {
                mostrarError(document.getElementById('clave_nueva'), 
                    document.getElementById('error-clave_nueva'), 
                    'La contraseña debe tener al menos 6 caracteres');
                return false;
            } else {
                mostrarExito(document.getElementById('clave_nueva'), 
                    document.getElementById('error-clave_nueva'));
            }
            
            if (claveNueva !== confirmarClave) {
                mostrarError(document.getElementById('confirmar_clave'), errorElement, 
                    'Las contraseñas no coinciden');
                return false;
            } else {
                mostrarExito(document.getElementById('confirmar_clave'), errorElement);
            }
        }
        
        return true;
    }

    function validarFormulario() {
        const nombreValido = validarNombre(document.getElementById('nombre'));
        const telefonoValido = validarTelefono(document.getElementById('telefono'));
        const correoValido = validarCorreo(document.getElementById('correo'));
        const contraseñasValidas = validarContraseñas();
        
        return nombreValido && telefonoValido && correoValido && contraseñasValidas;
    }

    function mostrarError(input, errorElement, mensaje) {
        input.classList.add('is-invalid');
        input.classList.remove('is-valid');
        errorElement.textContent = mensaje;
    }

    function mostrarExito(input, errorElement) {
        input.classList.remove('is-invalid');
        input.classList.add('is-valid');
        errorElement.textContent = '';
    }

    function verificarNombreDisponible(nombre, cedula) {
        return new Promise((resolve, reject) => {
            const formData = new FormData();
            formData.append('nombre', nombre);
            if (cedula) {
                formData.append('cedula', cedula);
            }
            
            fetch('index.php?c=usuarioControlador&m=verificarNombre', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                resolve(data.disponible);
            })
            .catch(error => {
                console.error('Error al verificar nombre:', error);
                reject(error);
            });
        });
    }

    function guardarPerfil() {
        const formData = new FormData(form);
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Guardando...';
        
        const modalCarga = mostrarMensajeCarga('Actualizando perfil...');
        
        fetch('index.php?c=usuarioControlador&m=actualizarPerfil', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            modalCarga.hide();
            
            if (data.success) {
                mostrarMensaje('success', data.message, data.details);
                // Limpiar campos de contraseña
                document.getElementById('clave_actual').value = '';
                document.getElementById('clave_nueva').value = '';
                document.getElementById('confirmar_clave').value = '';
                
                // Quitar clases de validación
                document.querySelectorAll('.is-valid').forEach(el => {
                    el.classList.remove('is-valid');
                });
            } else {
                mostrarMensaje('error', data.message, data.details);
            }
        })
        .catch(error => {
            modalCarga.hide();
            mostrarMensaje('error', 'Error de conexión', 'No se pudo conectar con el servidor');
            console.error('Error:', error);
        })
        .finally(() => {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = '<i class="fas fa-save me-1"></i> Guardar Cambios';
        });
    }
});
</script>

<style>
.is-valid {
    border-color: #198754 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.is-invalid {
    border-color: #dc3545 !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.4.4.4-.4'/%3e%3cpath d='M6 7v1.5'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}
</style>

<?php require_once 'vista/parcial/footer.php'; ?>