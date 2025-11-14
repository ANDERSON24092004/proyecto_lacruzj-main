$(document).ready(function(){
    console.log('Script de gestión de proveedores cargado');
    
    const expRif = /^\d{6,10}$/;
    const expNombre = /^[A-Za-zÁÉÍÓÚáéíóúÑñ\s]{2,50}$/;
    const expTelefono = /^[0424\,0412\,0416\,0251\,0426\,0414]{4}-\d{7}$/; 
    const expCorreo = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;

    const estilos = {
        valido: '2px solid #28a745',
        invalido: '2px solid #dc3545',
        normal: '1px solid #ced4da'
    };

    function inicializarAplicacion() {
        cargarTablaProveedores();
        inicializarEventos();
        inicializarEventosEliminar();
        inicializarValidaciones();
        inicializarPrevencionEntrada();
    }

    function cargarTablaProveedores() {
        $.ajax({
            url: 'index.php?c=ProveedorControlador&m=listarAjax',
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.success) {
                    renderizarTablaProveedores(response.data);
                } else {
                    mostrarErrorTabla(response.details);
                }
            },
            error: function(xhr, status, error) {
                mostrarErrorTabla('Error al cargar los proveedores: ' + error);
            }
        });
    }
    
    function renderizarTablaProveedores(proveedores) {
        const tbody = $('#tbody-proveedores');
        tbody.empty();
        
        if (proveedores.length === 0) {
            tbody.append(`
                <tr>
                    <td colspan="6" class="text-center text-muted">
                        <i class="fas fa-truck fa-2x mb-2"></i>
                        <p>No hay proveedores registrados</p>
                    </td>
                </tr>
            `);
            return;
        }
        
        proveedores.forEach(proveedor => {
            const fila = `
                <tr>
                    <td>J-${escapeHtml(proveedor.id_proveedores)}</td>
                    <td>${escapeHtml(proveedor.nombre)}</td>
                    <td>${escapeHtml(proveedor.telefono)}</td>
                    <td>${escapeHtml(proveedor.correo)}</td>
                    <td>${escapeHtml(proveedor.direccion)}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button data-id="${proveedor.id_proveedores}" type="button" class="btn btn-primary btn-sm btn-editar-proveedor">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" 
                                class="btn btn-danger btn-sm btn-eliminar-proveedor" 
                                data-id="${proveedor.id_proveedores}" 
                                title="Eliminar">
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
        $('#tbody-proveedores').html(`
            <tr>
                <td colspan="6" class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <p>${mensaje}</p>
                    <button class="btn btn-sm btn-primary mt-2" onclick="cargarTablaProveedores()">
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
        $('#registrarProveedorModal .btn-secondary').on('click', function() {
            limpiarFormularioCrear();
        });
        
        $('#editarProveedorModal .btn-secondary').on('click', function() {
            limpiarFormularioEditar();
        });
        
        $('#registrarProveedorModal').on('hidden.bs.modal', function () {
            restaurarScroll();
        });
        
        $('#editarProveedorModal').on('hidden.bs.modal', function () {
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
        $('.btn-editar-proveedor').off('click').on('click', function() {
            const id = $(this).data('id');
            cargarDatosEditar(id);
        });

        $('.btn-eliminar-proveedor').off('click').on('click', function() {
            const id = $(this).data('id');
            mostrarModalEliminar(id);
        });
    }
    
    function inicializarEventosEliminar() {
        $('#btnEliminarConfirmado').off('click').on('click', function(e) {
            e.preventDefault();
            ejecutarEliminacion();
        });
    }
    
    function inicializarPrevencionEntrada() {
        $('#crear_rif').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 10) value = value.substring(0, 10);
            $(this).val(value);
        });

        $('#crear_telefono, #telefono').on('input', function() {
            let value = $(this).val().replace(/\D/g, '');
            if (value.length > 11) value = value.substring(0, 11);
            
            if (value.length >= 4) {
                value = value.substring(0, 4) + '-' + value.substring(4);
            }
            
            $(this).val(value);
        });

        $('#crear_nombre, #nombre').on('input', function() {
            let value = $(this).val().replace(/[^A-Za-zÁÉÍÓÚáéíóúÑñ\s]/g, '');
            if (value.length > 50) value = value.substring(0, 50);
            $(this).val(value);
        });
    }
    
    function inicializarValidaciones() {
        $('#crear_rif').on('input', validarRifCrear);
        $('#crear_nombre').on('input', validarNombreCrear);
        $('#crear_telefono').on('input', validarTelefonoCrear);
        $('#crear_email').on('input', validarCorreoCrear);
        $('#crear_direccion').on('input', validarDireccionCrear);

        $('#nombre').on('input', validarNombreEditar);
        $('#telefono').on('input', validarTelefonoEditar);
        $('#email').on('input', validarCorreoEditar);
        $('#direccion').on('input', validarDireccionEditar);
    }

    function validarRifCrear() {
        const rif = $('#crear_rif').val().trim();
        const errorElement = $('#rif_error');
        
        if (!rif) {
            mostrarErrorCampo('crear_rif', 'El RIF es obligatorio', errorElement);
            return false;
        } else if (!expRif.test(rif)) {
            mostrarErrorCampo('crear_rif', 'El RIF debe tener entre 6 y 10 dígitos', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_rif', errorElement);
            return true;
        }
    }

    function validarNombreCrear() {
        const nombre = $('#crear_nombre').val().trim();
        const errorElement = $('#nombre_error');
        
        if (!nombre) {
            mostrarErrorCampo('crear_nombre', 'El nombre es obligatorio', errorElement);
            return false;
        } else if (!expNombre.test(nombre)) {
            mostrarErrorCampo('crear_nombre', 'El nombre solo puede contener letras y espacios (2-50 caracteres)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_nombre', errorElement);
            return true;
        }
    }

    function validarTelefonoCrear() {
        const telefono = $('#crear_telefono').val().trim();
        const errorElement = $('#telefono_error');
        
        if (!telefono) {
            mostrarErrorCampo('crear_telefono', 'El teléfono es obligatorio', errorElement);
            return false;
        } else if (!expTelefono.test(telefono)) {
            mostrarErrorCampo('crear_telefono', 'Formato: xxxx-xxxxxxx (11 dígitos)', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_telefono', errorElement);
            return true;
        }
    }

    function validarCorreoCrear() {
        const correo = $('#crear_email').val().trim();
        const errorElement = $('#email_error');
        
        if (!correo) {
            mostrarErrorCampo('crear_email', 'El correo es obligatorio', errorElement);
            return false;
        } else if (!expCorreo.test(correo)) {
            mostrarErrorCampo('crear_email', 'Formato de correo electrónico no válido', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_email', errorElement);
            return true;
        }
    }

    function validarDireccionCrear() {
        const direccion = $('#crear_direccion').val().trim();
        const errorElement = $('#direccion_error');
        
        if (!direccion) {
            mostrarErrorCampo('crear_direccion', 'La dirección es obligatoria', errorElement);
            return false;
        } else if (direccion.length < 5) {
            mostrarErrorCampo('crear_direccion', 'La dirección debe tener al menos 5 caracteres', errorElement);
            return false;
        } else {
            mostrarExitoCampo('crear_direccion', errorElement);
            return true;
        }
    }

    function validarNombreEditar() {
        const nombre = $('#nombre').val().trim();
        const errorElement = $('#enombre_editar_error');
        
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

    function validarTelefonoEditar() {
        const telefono = $('#telefono').val().trim();
        const errorElement = $('#telefono_editar_error');
        
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

    function validarCorreoEditar() {
        const correo = $('#email').val().trim();
        const errorElement = $('#email_editar_error');
        
        if (!correo) {
            mostrarErrorCampo('email', 'El correo es obligatorio', errorElement);
            return false;
        } else if (!expCorreo.test(correo)) {
            mostrarErrorCampo('email', 'Formato de correo electrónico no válido', errorElement);
            return false;
        } else {
            mostrarExitoCampo('email', errorElement);
            return true;
        }
    }

    function validarDireccionEditar() {
        const direccion = $('#direccion').val().trim();
        const errorElement = $('#direccion_editar_error');
        
        if (!direccion) {
            mostrarErrorCampo('direccion', 'La dirección es obligatoria', errorElement);
            return false;
        } else if (direccion.length < 5) {
            mostrarErrorCampo('direccion', 'La dirección debe tener al menos 5 caracteres', errorElement);
            return false;
        } else {
            mostrarExitoCampo('direccion', errorElement);
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

    function verificarRifUnico(rif, excluirId, errorElement) {
        $.ajax({
            url: 'index.php?c=ProveedorControlador&m=verificarRifUnico',
            type: 'POST',
            data: {
                rif: rif,
                excluir_id: excluirId
            },
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response.existe) {
                    mostrarErrorCampo(excluirId ? 'rif' : 'crear_rif', 'El RIF ya está registrado', errorElement);
                } else {
                    mostrarExitoCampo(excluirId ? 'rif' : 'crear_rif', errorElement);
                }
            },
            error: function() {
                console.error('Error al verificar RIF único');
            }
        });
    }
    
    function cargarDatosEditar(id) {
        $.ajax({
            url: 'index.php?c=ProveedorControlador&m=obtenerProveedorAjax&id_proveedores=' + id,
            type: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (response) {
                    $('#id_proveedores').val(response.id_proveedores);
                    $('#rif').val(response.id_proveedores);
                    $('#nombre').val(response.nombre || '');
                    $('#telefono').val(response.telefono || '');
                    $('#email').val(response.correo || '');
                    $('#direccion').val(response.direccion || '');
                    
                    mostrarExitoCampo('nombre', $('#enombre_editar_error'));
                    mostrarExitoCampo('telefono', $('#telefono_editar_error'));
                    mostrarExitoCampo('email', $('#email_editar_error'));
                    mostrarExitoCampo('direccion', $('#direccion_editar_error'));
                    
                    $('#editarProveedorModal').modal('show');
                } else {
                    mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos del proveedor');
                }
            },
            error: function(xhr, status, error) {
                mostrarMensaje('error', 'Error', 'No se pudieron cargar los datos del proveedor: ' + error);
            }
        });
    }
    
    function mostrarModalEliminar(id) {
        $('#btnEliminarConfirmado').data('id', id);
        $('#confirmarEliminarModal').modal('show');
    }
    
    function ejecutarEliminacion() {
        const id = $('#btnEliminarConfirmado').data('id');
        
        if (!id) {
            mostrarMensaje('error', 'Error', 'No se especificó el ID del proveedor a eliminar');
            return;
        }
        
        $.ajax({
            url: 'index.php?c=ProveedorControlador&m=eliminarAjax',
            type: 'POST',
            data: { id_proveedores: id },
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
                    cargarTablaProveedores();
                } else {
                    mostrarMensaje('error', response.message, response.details);
                }
            },
            error: function(xhr, status, error) {
                $('#btnEliminarConfirmado').prop('disabled', false).html('<i class="fas fa-check me-2"></i> Confirmar');
                mostrarMensaje('error', 'Error', 'No se pudo eliminar el proveedor: ' + error);
            }
        });
    }
    
    function validarFormularioCreacion() {
        let isValid = true;
        
        if (!validarRifCrear()) isValid = false;
        if (!validarNombreCrear()) isValid = false;
        if (!validarTelefonoCrear()) isValid = false;
        if (!validarCorreoCrear()) isValid = false;
        if (!validarDireccionCrear()) isValid = false;
        
        return isValid;
    }
    
    function validarFormularioEdicion() {
        let isValid = true;
        
        if (!validarNombreEditar()) isValid = false;
        if (!validarTelefonoEditar()) isValid = false;
        if (!validarCorreoEditar()) isValid = false;
        if (!validarDireccionEditar()) isValid = false;
        
        return isValid;
    }
    
    $('#formProveedor').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioCreacion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=ProveedorControlador&m=guardarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#guardar_proveedor').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#guardar_proveedor').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Proveedor');
                
                if (response.success) {
                    $('#registrarProveedorModal').modal('hide');
                    $('#formProveedor')[0].reset();
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaProveedores();
                } else {
                    const detalles = response.details || response.message;
                    
                    if (detalles.includes('RIF')) {
                        mostrarErrorCampo('crear_rif', detalles, $('#rif_error'));
                    } else {
                        mostrarMensaje('error', response.message, response.details);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#guardar_proveedor').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Proveedor');
                mostrarMensaje('error', 'Error', 'No se pudo registrar el proveedor: ' + error);
            }
        });
    });
    
    $('#formEditarProveedor').on('submit', function(e){
        e.preventDefault();
        
        if (!validarFormularioEdicion()) {
            return;
        }

        const formData = $(this).serialize();
        
        $.ajax({
            url: 'index.php?c=ProveedorControlador&m=actualizarAjax',
            type: 'POST',
            data: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#editar_proveedor').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i> Guardando...');
            },
            success: function(response) {
                $('#editar_proveedor').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                
                if (response.success) {
                    $('#editarProveedorModal').modal('hide');
                    mostrarMensaje('success', response.message, response.details);
                    cargarTablaProveedores();
                } else {
                    const detalles = response.details || response.message;
                    
                    if (detalles.includes('RIF')) {
                        mostrarErrorCampo('rif', detalles, $('#rif_editar_error'));
                    } else {
                        mostrarMensaje('error', response.message, response.details);
                    }
                }
            },
            error: function(xhr, status, error) {
                $('#editar_proveedor').prop('disabled', false).html('<i class="fas fa-save me-2"></i> Guardar Cambios');
                mostrarMensaje('error', 'Error', 'No se pudo actualizar el proveedor: ' + error);
            }
        });
    });
    
    function limpiarFormularioCrear() {
        $('#formProveedor')[0].reset();
        $('.error-message').hide().text('');
        $('#formProveedor .form-control').each(function() {
            resetearEstiloCampo($(this).attr('id'), $(`#${$(this).attr('id').replace('crear_', '')}_error`));
        });
    }
    
    function limpiarFormularioEditar() {
        $('.error-message').hide().text('');
        $('#formEditarProveedor .form-control').each(function() {
            const fieldName = $(this).attr('id');
            const errorElement = $(`#${fieldName}_editar_error`);
            resetearEstiloCampo(fieldName, errorElement);
        });
    }
    
    function mostrarMensaje(tipo, titulo, mensaje) {
        console.log("mensaje:", tipo, titulo, mensaje);
        
        if (typeof window.mostrarMensaje === 'function') {
            window.mostrarMensaje(tipo, titulo, mensaje);
        } else {
            console.error('Función mostrarMensaje no disponible');
            alert(`${titulo}: ${mensaje}`);
            setTimeout(function() {
                cargarTablaProveedores();
            }, 300);
        }
    }
    
    inicializarAplicacion();
    window.cargarTablaProveedores = cargarTablaProveedores;
});