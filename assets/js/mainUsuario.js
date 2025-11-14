$(document).ready(function(){
    console.log('Script de gestión de usuarios cargado');
    
    let roles = [];
    
    const expCedula = /^\d{6,10}$/;
    const expNombre = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}$/;
    const expTelefono = /^[0424\,0412\,0416\,0251\,0426\,0414]{4}-\d{7}$/; 
    const expCorreo = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const expClave = /^.{6,}$/;

    const estilos = {
        valido: '2px solid #28a745',
        invalido: '2px solid #dc3545',
        normal: '1px solid #ced4da'
    };

    function inicializarAplicacion() {
        cargarRoles();
        cargarTablaUsuarios();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarValidaciones();
        inicializarPrevencionEntrada();
    }

    function cargarRoles() {
        $.ajax({
            url: 'index.php?c=usuarioControlador&m=crear',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    roles = response.data;
                    actualizarSelectRoles();
                }
            },
            error: function() {
                console.error('Error al cargar roles');
            }
        });
    }
    
    function actualizarSelectRoles() {
        $('#id_rol, #rol_editar').empty().append('<option value="" selected disabled>Seleccione un rol</option>');
        
        roles.forEach(rol => {
            $('#id_rol').append(`<option value="${rol.id_rol}">${rol.tipo_usuario}</option>`);
            $('#rol_editar').append(`<option value="${rol.id_rol}">${rol.tipo_usuario}</option>`);
        });
    }
    
    function cargarTablaUsuarios() {
        $.ajax({
            url: 'index.php?c=usuarioControlador&m=listar',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderizarTablaUsuarios(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                mostrarErrorTabla('Error al cargar los usuarios: ' + error);
            }
        });
    }
    
    function renderizarTablaUsuarios(usuarios) {
        const tbody = $('#cuerpo-tabla-usuarios');
        tbody.empty();
        
        if (usuarios.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="fas fa-users fa-2x mb-2"></i>
                        <p>No hay usuarios registrados</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        usuarios.forEach(usuario => {
            const fila = `
                <tr>
                    <td>${usuario.cedula}</td>
                    <td>${escapeHtml(usuario.username)}</td>
                    <td>${escapeHtml(usuario.correo)}</td>
                    <td>${escapeHtml(usuario.telefono)}</td>
                    <td>${escapeHtml(usuario.tipo_usuario)}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button value="${usuario.cedula}" type="button" class="btn btn-primary btn-sm btn-editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm btn-eliminar" data-id="${usuario.cedula}" title="Eliminar">
                                <i class="fas fa-trash-alt"></i>
                            </button>                  
                       </div>  
                    </td>
                </tr>
            `;
            tbody.append(fila);
        });
        
        inicializarEventosBotones();
    }
    
    function mostrarErrorTabla(mensaje) {
        $('#cuerpo-tabla-usuarios').html(`
            <tr>
                <td colspan="6" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaUsuarios()">
                        <i class="fas fa-redo"></i> Reintentar
                    </button>
                </td>
            </tr>
        `);
    }
    
    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return unsafe
            .toString()
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
    
    function inicializarEventos() {
        $('#registrarUsuarioModal .btn-secondary').on('click', function() {
            limpiarFormularioCrear();
        });
        
        $('#editarUsuarioModal .btn-secondary').on('click', function() {
            limpiarFormularioEditar();
        });
        
        $('#registrarUsuarioModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#editarUsuarioModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#confirmarEliminarModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#mensajeModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
    }

    function restaurarScroll() {
        setTimeout(function() {
            const modalesAbiertos = $('.modal.show');
            if (modalesAbiertos.length === 0) {
                $('body').removeClass('modal-open');
                $('body').css({
                    'overflow': 'auto',
                    'padding-right': '0'
                });
                $('.modal-backdrop').remove();
            }
        }, 150);
    }
    
    function inicializarEventosBotones() {
        $('.btn-editar').off('click').on('click', function() {
            const cedula = $(this).val();
            cargarDatosEditar(cedula);
        });

        $('.btn-eliminar').off('click').on('click', function() {
            const cedula = $(this).data('id');
            mostrarModalEliminar(cedula);
        });
    }
    
    function inicializarEventosEliminar() {
        $('#btnEliminarConfirmado').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarEliminacion();
        });
    }
    
    function inicializarPrevencionEntrada() {
        $('#cedula').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 10) value = value.substring(0, 10);
            $(this).val(value);
        });

        $('#telefono, #telefono_editar').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            
            if (value.length >= 4) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }
            
            $(this).val(value);
        });

        $('#nombre, #nombre_editar').on('input', function() {
            let value = $(this).val().replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
            if (value.length > 50) value = value.substring(0, 50);
            $(this).val(value);
        });
    }
    
    function inicializarValidaciones() {
        $('#cedula').on('input', validarCedulaCrear);
        $('#nombre').on('input', validarNombreCrear);
        $('#telefono').on('input', validarTelefonoCrear);
        $('#correo').on('input', validarCorreoCrear);
        $('#id_rol').on('change', validarRolCrear);
        $('#clave').on('input', validarClaveCrear);
        $('#confirmar_clave').on('input', validarConfirmarClaveCrear);

        $('#nombre_editar').on('input', validarNombreEditar);
        $('#telefono_editar').on('input', validarTelefonoEditar);
        $('#correo_editar').on('input', validarCorreoEditar);
        $('#rol_editar').on('change', validarRolEditar);
        $('#clave_editar').on('input', validarClaveEditar);
    }

    function validarCedulaCrear() {
        const cedula = $('#cedula').val().trim();
        const errorElement = $('#cedula_error');
        
        if (!cedula) {
            mostrarErrorCampo('cedula', 'La cédula es obligatoria', errorElement);
            return false;
        } else if (!expCedula.test(cedula)) {
            mostrarErrorCampo('cedula', 'La cédula debe tener entre 6 y 10 dígitos', errorElement);
            return false;
        } else {
            mostrarExitoCampo('cedula', errorElement);
            return true;
        }
    }

    function validarNombreCrear() {
        const nombre = $('#nombre').val().trim();
        const errorElement = $('#nombre_error');
        
        if (!nombre) {
            mostrarErrorCampo('nombre', 'El nombre es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('nombre', 'El nombre solo puede contener letras y espacios (2-50 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('nombre', errorElement);
            return true;
        }
    }

    function validarTelefonoCrear() {
        const telefono = $('#telefono').val().trim();
        const errorElement = $('#telefono_error');
        
        if (!telefono) {
            mostrarErrorCampo('telefono', 'El teléfono es obligatorio', errorElement);
            return false;
        } else if (!expTelefono.test(telefono)) {
            mostrarErrorCampo('telefono', 'Formato: xxxx-xxxxxxx (11 dígitos)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('telefono', errorElement);
            return true;
        }
    }

    function validarCorreoCrear() {
        const correo = $('#correo').val().trim();
        const errorElement = $('#correo_error');
        
        if (!correo) {
            mostrarErrorCampo('correo', 'El correo es obligatorio', errorElement);
            return false;
        } else if (!expCorreo.test(correo)) {
            mostrarErrorCampo('correo', 'Formato de correo electrónico no válido', errorElement);
            return false;
        } else {
            mostrarExitoCampo('correo', errorElement);
            return true;
        }
    }

    function validarRolCrear() {
        const rol = $('#id_rol').val();
        const errorElement = $('#rol_error');
        
        if (!rol) {
            mostrarErrorCampo('id_rol', 'El rol es obligatorio', errorElement);
            return false;
        } else {
            mostrarExitoCampo('id_rol', errorElement);
            return true;
        }
    }

    function validarClaveCrear() {
        const clave = $('#clave').val();
        const errorElement = $('#clave_error');
        
        if (!clave) {
            mostrarErrorCampo('clave', 'La contraseña es obligatoria', errorElement);
            return false;
        } else if (!expClave.test(clave)) {
            mostrarErrorCampo('clave', 'La contraseña debe tener al menos 6 caracteres', errorElement);
            return false;
        } else {
            mostrarExitoCampo('clave', errorElement);
            return true;
        }
    }

    function validarConfirmarClaveCrear() {
        const clave = $('#clave').val();
        const confirmarClave = $('#confirmar_clave').val();
        const errorElement = $('#confirmar_clave_error');
        
        if (!confirmarClave) {
            mostrarErrorCampo('confirmar_clave', 'Debe confirmar la contraseña', errorElement);
            return false;
        } else if (clave !== confirmarClave) {
            mostrarErrorCampo('confirmar_clave', 'Las contraseñas no coinciden', errorElement);
            return false;
        } else {
            mostrarExitoCampo('confirmar_clave', errorElement);
            return true;
        }
    }

    function validarNombreEditar() {
        const nombre = $('#nombre_editar').val().trim();
        const errorElement = $('#editar_nombre_error');
        
        if (!nombre) {
            mostrarErrorCampo('nombre_editar', 'El nombre es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('nombre_editar', 'El nombre solo puede contener letras y espacios (2-50 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('nombre_editar', errorElement);
            return true;
        }
    }

    function validarTelefonoEditar() {
        const telefono = $('#telefono_editar').val().trim();
        const errorElement = $('#editar_telefono_error');
        
        if (!telefono) {
            mostrarErrorCampo('telefono_editar', 'El teléfono es obligatorio', errorElement);
            return false;
        } else if (!expTelefono.test(telefono)) {
            mostrarErrorCampo('telefono_editar', 'Formato: xxxx-xxxxxxx (11 dígitos)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('telefono_editar', errorElement);
            return true;
        }
    }

    function validarCorreoEditar() {
        const correo = $('#correo_editar').val().trim();
        const errorElement = $('#editar_correo_error');
        
        if (!correo) {
            mostrarErrorCampo('correo_editar', 'El correo es obligatorio', errorElement);
            return false;
        } else if (!expCorreo.test(correo)) {
            mostrarErrorCampo('correo_editar', 'Formato de correo electrónico no válido', errorElement);
            return false;
        } else {
            mostrarExitoCampo('correo_editar', errorElement);
            return true;
        }
    }

    function validarRolEditar() {
        const rol = $('#rol_editar').val();
        const errorElement = $('#editar_rol_error');
        
        if (!rol) {
            mostrarErrorCampo('rol_editar', 'El rol es obligatorio', errorElement);
            return false;
        } else {
            mostrarExitoCampo('rol_editar', errorElement);
            return true;
        }
    }

    function validarClaveEditar() {
        const clave = $('#clave_editar').val();
        const errorElement = $('#editar_clave_error');
        
        if (clave && !expClave.test(clave)) {
            mostrarErrorCampo('clave_editar', 'La contraseña debe tener al menos 6 caracteres', errorElement);
            return false;
        } else {
            mostrarExitoCampo('clave_editar', errorElement);
            return true;
        }
    }

    function mostrarErrorCampo(campoId, mensaje, errorElement) {
        $(`#${campoId}`).css('border', estilos.invalido);
        errorElement.text(mensaje).show();
    }

    function mostrarExitoCampo(campoId, errorElement) {
        $(`#${campoId}`).css('border', estilos.valido);
        errorElement.hide().text('');
    }

    function resetearEstiloCampo(campoId, errorElement) {
        $(`#${campoId}`).css('border', estilos.normal);
        errorElement.hide().text('');
    }
    
    function cargarDatosEditar(cedula) {
        $.ajax({
            url: 'index.php?c=usuarioControlador&m=editar&cedula=' + cedula,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    const usuario = response.data;
                    $('#cedula_editar').val(usuario.cedula);
                    $('#cedula_display').val(usuario.cedula);
                    $('#nombre_editar').val(usuario.nombre || '');
                    $('#telefono_editar').val(usuario.telefono || '');
                    $('#correo_editar').val(usuario.correo || '');
                    $('#rol_editar').val(usuario.id_rol || '');
                    $('#clave_editar').val('');
                    
                    mostrarExitoCampo('nombre_editar', $('#editar_nombre_error'));
                    mostrarExitoCampo('telefono_editar', $('#editar_telefono_error'));
                    mostrarExitoCampo('correo_editar', $('#editar_correo_error'));
                    mostrarExitoCampo('rol_editar', $('#editar_rol_error'));
                    
                    $('#editarUsuarioModal').modal('show');
                } else {
                    if (response.details && response.details.includes('cédula')) {
                        mostrarErrorCampo('cedula', response.details, $('#cedula_error'));
                    } else {
                        mostrarMensaje('error', 'Error', response.details || response.message);
                    }
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos del usuario: ' + error);
            }
        });
    }
    
    function mostrarModalEliminar(cedula) {
        $('#cedula_eliminar').val(cedula);
        $('#confirmarEliminarModal').modal('show');
    }
    
    function ejecutarEliminacion() {
        const cedula = $('#cedula_eliminar').val();
        
        if (!cedula) {
            mostrarMensaje('error', 'Error', 'No se especificó la cédula del usuario a eliminar');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=usuarioControlador&m=eliminar',
            type: 'POST',
            data: { cedula: cedula },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#btnEliminarConfirmado').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Eliminando...');
            },
            success: function(response) {
                $('#btnEliminarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                
                if (response.success) {
                    $('#confirmarEliminarModal').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaUsuarios();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnEliminarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error', 'No se pudo eliminar el usuario: ' + error);
            }
        });
    }
    
    function validarFormularioCreacion() {
        let isValid = true;
        
        if (!validarCedulaCrear()) isValid = false;
        if (!validarNombreCrear()) isValid = false;
        if (!validarTelefonoCrear()) isValid = false;
        if (!validarCorreoCrear()) isValid = false;
        if (!validarRolCrear()) isValid = false;
        if (!validarClaveCrear()) isValid = false;
        if (!validarConfirmarClaveCrear()) isValid = false;
        
        return isValid;
    }
    
    function validarFormularioEdicion() {
        let isValid = true;
        
        if (!validarNombreEditar()) isValid = false;
        if (!validarTelefonoEditar()) isValid = false;
        if (!validarCorreoEditar()) isValid = false;
        if (!validarRolEditar()) isValid = false;
        if (!validarClaveEditar()) isValid = false;
        
        return isValid;
    }
    
    $('#formusuario').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioCreacion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=usuarioControlador&m=guardar',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('button[type="submit"]', '#formusuario').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('button[type="submit"]', '#formusuario').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Usuario');
                
                if (response.success) {
                    $('#registrarUsuarioModal').modal('hide');
                    $('#formusuario')[0].reset();
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaUsuarios();
                } else {
                    const detalles = response.details || response.message;
                    
                    if (detalles.includes('cédula')) {
                        mostrarErrorCampo('cedula', detalles, $('#cedula_error'));
                    } else if (detalles.includes('nombre de usuario')) {
                        mostrarErrorCampo('nombre', detalles, $('#nombre_error'));
                    } else if (detalles.includes('correo')) {
                        mostrarErrorCampo('correo', detalles, $('#correo_error'));
                    } else {
                        mostrarMensaje('error', response.message, response.details);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('button[type="submit"]', '#formusuario').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Usuario');
                mostrarMensaje('error', 'Error', 'No se pudo registrar el usuario: ' + error);
            }
        });
    });
    
    $('#formEditarUsuario').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioEdicion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=usuarioControlador&m=actualizar',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('button[type="submit"]', '#formEditarUsuario').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('button[type="submit"]', '#formEditarUsuario').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                
                if (response.success) {
                    $('#editarUsuarioModal').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaUsuarios();
                } else {
                    const detalles = response.details || response.message;
                    
                    if (detalles.includes('nombre de usuario')) {
                        mostrarErrorCampo('nombre_editar', detalles, $('#editar_nombre_error'));
                    } else if (detalles.includes('correo')) {
                        mostrarErrorCampo('correo_editar', detalles, $('#editar_correo_error'));
                    } else {
                        mostrarMensaje('error', response.message, response.details);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('button[type="submit"]', '#formEditarUsuario').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                mostrarMensaje('error', 'Error', 'No se pudo actualizar el usuario: ' + error);
            }
        });
    });
    
    function limpiarFormularioCrear() {
        $('#formusuario')[0].reset();
        $('.error-message').hide().text('');
        $('#formusuario .form-control, #formusuario .form-select').each(function() {
            resetearEstiloCampo($(this).attr('id'), $(`#${$(this).attr('id')}_error`));
        });
    }
    
    function limpiarFormularioEditar() {
        $('#clave_editar').val('');
        $('.error-message').hide().text('');
        $('#formEditarUsuario .form-control, #formEditarUsuario .form-select').each(function() {
            resetearEstiloCampo($(this).attr('id'), $(`#editar_${$(this).attr('id').replace('_editar', '')}_error`));
        });
    }
    
    inicializarAplicacion();
    
    window.cargarTablaUsuarios = cargarTablaUsuarios;
});