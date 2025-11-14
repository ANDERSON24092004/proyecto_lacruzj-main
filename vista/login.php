<?php
$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
$success = isset($_SESSION['login_success']) ? $_SESSION['login_success'] : '';
$registro_error = isset($_SESSION['registro_error']) ? $_SESSION['registro_error'] : '';
$registro_success = isset($_SESSION['registro_success']) ? $_SESSION['registro_success'] : '';
$form_data = isset($_SESSION['form_data']) ? $_SESSION['form_data'] : [];

unset($_SESSION['login_error']);
unset($_SESSION['login_success']);
unset($_SESSION['registro_error']);
unset($_SESSION['registro_success']);
unset($_SESSION['form_data']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Multiservicios Lacruz</title>
    <link rel="stylesheet" href="/proyecto-lacruz-j/assets/css/bootstrap.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <link rel="stylesheet" href="/proyecto-lacruz-j/assets/css/login.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container-fluid h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-md-8 col-lg-6 col-xl-4">
                <div class="card shadow-lg">
                    <div class="card-body p-4 p-md-5">
                        <div class="text-center mb-4">
                            <img src="assets/images/logo.png" alt="Logo Multiservicios Lacruz" class="mb-3" style="width: 200px;">
                        </div>
                        
                        <?php if(!empty($success)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?php echo htmlspecialchars($success); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if(!empty($error)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?php echo htmlspecialchars($error); ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST" action="index.php?c=loginControlador&m=validar" id="loginForm" novalidate>
                            <div class="form-floating mb-3">
                                <input type="text" class="form-control" id="usuario" name="usuario" 
                                       placeholder="Usuario" value="<?php echo isset($_POST['usuario']) ? htmlspecialchars($_POST['usuario']) : ''; ?>" 
                                       required>
                                <label for="usuario">Usuario</label>
                                <div class="invalid-feedback">
                                    Por favor ingrese su nombre de usuario.
                                </div>
                            </div>
                            
                            <div class="form-floating mb-4">
                                <input type="password" class="form-control" id="clave" name="clave" 
                                       placeholder="Contraseña" required>
                                <label for="clave">Contraseña</label>
                                <div class="invalid-feedback">
                                    Por favor ingrese su contraseña.
                                </div>
                            </div>
                            
                            <div class="d-grid mb-3">
                                <button class="btn btn-primary btn-lg" type="submit" name="btnLogin" id="btnLogin">
                                    <span id="btnText">Ingresar</span>
                                    <div id="btnLoading" class="spinner-border spinner-border-sm d-none" role="status">
                                        <span class="visually-hidden">Cargando...</span>
                                    </div>
                                </button>
                            </div>
                        </form>
                        <div class="text-center">
                            <p class="text-white-50 mb-0">¿No tienes una cuenta?</p>
                            <a href="#" class="text-white text-decoration-none fw-bold" data-bs-toggle="modal" data-bs-target="#registroModal">
                                Regístrate aquí
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Registro -->
    <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registroModalLabel">Registro de Oficinista</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <?php if(!empty($registro_success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <?php echo htmlspecialchars($registro_success); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <?php if(!empty($registro_error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo htmlspecialchars($registro_error); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="index.php?c=loginControlador&m=crearUsuario" id="registroForm" novalidate>
                        <div class="mb-3">
                            <label for="cedula" class="form-label">Cédula</label>
                            <input type="text" class="form-control" id="cedula" name="cedula" 
                                   value="<?php echo isset($form_data['cedula']) ? htmlspecialchars($form_data['cedula']) : ''; ?>" 
                                   required pattern="[0-9]{6,10}">
                            <div class="form-text">Solo números (6-10 dígitos)</div>
                            <div class="invalid-feedback" id="cedula_error"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre de usuario</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" 
                                   value="<?php echo isset($form_data['nombre']) ? htmlspecialchars($form_data['nombre']) : ''; ?>" 
                                   required minlength="2" maxlength="50">
                            <div class="invalid-feedback" id="nombre_error"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" 
                                   value="<?php echo isset($form_data['telefono']) ? htmlspecialchars($form_data['telefono']) : ''; ?>" 
                                   required>
                            <div class="form-text">Formato: xxxx-xxxxxxx</div>
                            <div class="invalid-feedback" id="telefono_error"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" 
                                   value="<?php echo isset($form_data['correo']) ? htmlspecialchars($form_data['correo']) : ''; ?>" 
                                   required>
                            <div class="invalid-feedback" id="correo_error"></div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="clave" class="form-label">Contraseña</label>
                            <input type="password" class="form-control" id="clave" name="clave" 
                                   required minlength="6">
                            <div class="form-text">Mínimo 6 caracteres</div>
                            <div class="invalid-feedback" id="clave_error"></div>
                        </div>
                        
                        <div class="d-grid">
                            <button class="btn btn-primary btn-lg" type="submit" name="btnRegistro" id="btnRegistro">
                                <span id="btnRegistroText">Registrarse</span>
                                <div id="btnRegistroLoading" class="spinner-border spinner-border-sm d-none" role="status">
                                    <span class="visually-hidden">Cargando...</span>
                                </div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="/proyecto-lacruz-j/assets/js/bootstrap.bundle.min.js"></script>

    
   <script>
    document.addEventListener('DOMContentLoaded', function() {
        <?php if(!empty($registro_error)): ?>
            console.log('Error de registro detectado, abriendo modal...');
            setTimeout(() => {
                const registroModal = new bootstrap.Modal(document.getElementById('registroModal'));
                registroModal.show();
            }, 100);
        <?php endif; ?>

        const registroModal = document.getElementById('registroModal');
        if (registroModal) {
            registroModal.addEventListener('hidden.bs.modal', function() {
                const form = document.getElementById('registroForm');
                if (form) {
                    form.reset();
                    const inputs = form.querySelectorAll('input');
                    const errors = form.querySelectorAll('.invalid-feedback');
                    inputs.forEach(input => {
                        input.style.border = '';
                        input.classList.remove('is-invalid');
                    });
                    errors.forEach(error => {
                        error.style.display = 'none';
                        error.textContent = '';
                    });
                }
            });
        }
    });
</script>

    <script src="/proyecto-lacruz-j/assets/js/login_register.js"></script>
</body>
</html>