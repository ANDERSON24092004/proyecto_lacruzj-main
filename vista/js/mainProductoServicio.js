$(document).ready(function(){
    console.log('Script de validación de productos cargado');
    
    const expNombre = /^[A-Za-zÄ-ÿ\u00f1\u00d1\-\s\d]{1,100}$/;
    const expNumero = /^\d+(\.\d{1,2})?$/;
    
    const crearNombre = $("#nombre");
    const crearStock = $("#stock");
    const crearPrecioMayor = $("#precio_mayor");
    const crearUnidadMedida = $("#unidad_medida");
    const crearPresentacion = $("#presentacion");
    const crearTipo = $("input[name='tipo']");
    const crearCosto = $("#costo");
    const crearGuardar = $("#btnGuardar");
    const esFabricadoCheckbox = $("#es_fabricado");
    
    $(".error-message").hide();
    
    function esServicio() {
        return $("input[name='tipo']:checked").val() == 2;
    }
    
    function esProductoFabricado() {
        return esFabricadoCheckbox.is(':checked');
    }
    
    function mostrarError(campo, mensaje) {
        campo.css('border', '2px solid #dc3545');
        campo.next('.error-message').remove();
        campo.after(`<div class="error-message text-danger small mt-1">${mensaje}</div>`);
    }
    
    function mostrarValido(campo) {
        campo.css('border', '2px solid #198754');
        campo.next('.error-message').remove();
    }
    
    function limpiarEstilo(campo) {
        campo.css('border', '');
        campo.next('.error-message').remove();
    }
    
    function prevenirCaracteresNoValidos(campo) {
        campo.on('keypress', function(e) {
            const charCode = e.which ? e.which : e.keyCode;
            const value = $(this).val();
            
            if (charCode === 46) { 
                if (value.indexOf('.') !== -1) {
                    return false; 
                }
                return true;
            }
            
            if (charCode > 31 && (charCode < 48 || charCode > 57)) {
                return false; 
            }
            
            return true;
        });
        
        campo.on('paste', function(e) {
            const pastedData = e.originalEvent.clipboardData.getData('text');
            if (!/^\d*\.?\d*$/.test(pastedData)) {
                e.preventDefault();
            }
        });
        
        campo.on('input', function() {
            const value = $(this).val();
            if (value && !expNumero.test(value)) {
                $(this).val(value.replace(/[^\d.]/g, ''));
            }
        });
    }
    
    crearNombre.on('keypress', function(e) {
        const charCode = e.which ? e.which : e.keyCode;

        if (!(charCode === 32 || charCode === 45 ||
              (charCode >= 48 && charCode <= 57) || 
              (charCode >= 65 && charCode <= 90) || 
              (charCode >= 97 && charCode <= 122) || 
              charCode === 209 || charCode === 241 || 
              (charCode >= 192 && charCode <= 255))) {
            e.preventDefault();
            return false;
        }
    });
    
    crearNombre.on('paste', function(e) {
        const pastedData = e.originalEvent.clipboardData.getData('text');
        if (!expNombre.test(pastedData)) {
            e.preventDefault();
            mostrarError(crearNombre, 'Texto pegado contiene caracteres no válidos');
        }
    });
    
    prevenirCaracteresNoValidos(crearStock);
    prevenirCaracteresNoValidos(crearCosto);
    prevenirCaracteresNoValidos(crearPrecioMayor);
    
    crearTipo.change(function(){
        const esServ = esServicio();
        console.log('Tipo cambiado:', esServ ? 'Servicio' : 'Producto');
        
        const tituloModal = $('#registrarProductoModalLabel');
        const btnGuardar = $('#btnGuardar');
        
        if (esServ) {
            tituloModal.html('<i class="fas fa-cogs me-2"></i> Registrar Nuevo Servicio');
            btnGuardar.html('<i class="fas fa-save me-1"></i> Guardar Servicio');
            
            crearStock.val('0').prop('readonly', true);
            crearPrecioMayor.val('0').prop('readonly', true);
            crearPresentacion.val('').prop('disabled', true);
            esFabricadoCheckbox.prop('checked', false).prop('disabled', true);
            
            limpiarEstilo(crearStock);
            limpiarEstilo(crearPrecioMayor);
            limpiarEstilo(crearPresentacion);
            
            $("#materiasPrimasSection").hide();
        } else {
            tituloModal.html('<i class="fas fa-cube me-2"></i> Registrar Nuevo Producto');
            btnGuardar.html('<i class="fas fa-save me-1"></i> Guardar Producto');
            
            crearStock.val('0').prop('readonly', false);
            crearPrecioMayor.val('').prop('readonly', false);
            crearPresentacion.val('').prop('disabled', false);
            esFabricadoCheckbox.prop('disabled', false);
        }
    });
    
    crearNombre.on('blur', function(){
        const valor = $(this).val().trim();
        if (!valor) {
            mostrarError(crearNombre, 'El nombre es obligatorio');
        } else if (!expNombre.test(valor)) {
            mostrarError(crearNombre, 'Solo se permiten letras, números, espacios y guiones (máx. 100 caracteres)');
        } else {
            mostrarValido(crearNombre);
        }
    });
    
    crearStock.on('blur', function(){
        if (esServicio()) return;
        
        const valor = $(this).val();
        if (valor === '' || valor < 0) {
            mostrarError(crearStock, 'El stock debe ser un número mayor o igual a 0');
        } else {
            mostrarValido(crearStock);
        }
    });
    
    crearCosto.on('blur', function(){
        const valor = $(this).val();
        if (!valor || parseFloat(valor) < 0) {
            mostrarError(crearCosto, 'El costo debe ser un número mayor o igual a 0');
        } else if (!expNumero.test(valor)) {
            mostrarError(crearCosto, 'Formato de costo no válido');
        } else {
            mostrarValido(crearCosto);
        }
    });
    
    crearPrecioMayor.on('blur', function(){
        if (esServicio() || esProductoFabricado()) return;
        
        const valor = $(this).val();
        if (!valor || parseFloat(valor) <= 0) {
            mostrarError(crearPrecioMayor, 'El precio al por mayor debe ser mayor a 0');
        } else if (!expNumero.test(valor)) {
            mostrarError(crearPrecioMayor, 'Formato de precio no válido');
        } else {
            mostrarValido(crearPrecioMayor);
        }
    });
    
    crearUnidadMedida.on('change', function(){
        if (!$(this).val()) {
            mostrarError(crearUnidadMedida, 'Debe seleccionar una unidad de medida');
        } else {
            mostrarValido(crearUnidadMedida);
        }
    });
    
    crearPresentacion.on('change', function(){
        if (esServicio()) return;
        
        if (!$(this).val()) {
            mostrarError(crearPresentacion, 'Debe seleccionar una presentación');
        } else {
            mostrarValido(crearPresentacion);
        }
    });
    
    esFabricadoCheckbox.change(function(){
        if ($(this).is(':checked')) {
            limpiarEstilo(crearPrecioMayor);
        }
    });
    
    $('#formCrearProducto').on('submit', function(e){
        e.preventDefault();
        
        console.log('Validando formulario de producto/servicio');
        
        let esValido = true;
        const esServ = esServicio();
        const esFabricado = esProductoFabricado();
        
        if (!crearNombre.val().trim()) {
            mostrarError(crearNombre, 'El nombre es obligatorio');
            esValido = false;
        } else if (!expNombre.test(crearNombre.val().trim())) {
            mostrarError(crearNombre, 'El nombre contiene caracteres no válidos');
            esValido = false;
        }
        
        if (!crearCosto.val() || parseFloat(crearCosto.val()) < 0) {
            mostrarError(crearCosto, 'El costo debe ser mayor o igual a 0');
            esValido = false;
        }
        
        if (!crearUnidadMedida.val()) {
            mostrarError(crearUnidadMedida, 'Debe seleccionar una unidad de medida');
            esValido = false;
        }
        
        if (!esServ) {
            if (crearStock.val() === '' || parseFloat(crearStock.val()) < 0) {
                mostrarError(crearStock, 'El stock debe ser mayor o igual a 0');
                esValido = false;
            }
            
            if (!crearPresentacion.val()) {
                mostrarError(crearPresentacion, 'Debe seleccionar una presentación');
                esValido = false;
            }
            
            if (!esFabricado) {
                if (!crearPrecioMayor.val() || parseFloat(crearPrecioMayor.val()) <= 0) {
                    mostrarError(crearPrecioMayor, 'El precio al por mayor debe ser mayor a 0');
                    esValido = false;
                }
            }
        }
        
        if (esValido) {
            console.log('Formulario válido, enviando...');
            
            crearGuardar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Guardando...');
            
            this.submit();
        } else {
            console.log('Formulario inválido');
            $('#crear_btnerror')
                .removeClass('d-none alert-success')
                .addClass('alert-danger')
                .html('<i class="fas fa-exclamation-triangle me-2"></i>Por favor, complete todos los campos correctamente')
                .show();
            
            $('html, body').animate({
                scrollTop: $('.error-message:first').offset().top - 100
            }, 500);
        }
    });
    
    $('#registrarProductoModal').on('hidden.bs.modal', function () {
        $('.form-control').css('border', '');
        $('.error-message').remove();
        $('#crear_btnerror').hide().addClass('d-none');
        $('#formCrearProducto')[0].reset();
        crearGuardar.prop('disabled', false).html('<i class="fas fa-save me-1"></i> Registrar');
        $('#tipo_producto').prop('checked', true).trigger('change');
    });

    setTimeout(function() {
        crearTipo.filter(':checked').trigger('change');
    }, 100);
});