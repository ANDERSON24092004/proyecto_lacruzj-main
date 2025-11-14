$(document).ready(function(){
    console.log('Script de validación de materia prima cargado');
    
    const expNombre = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]{2,100}$/;
    const expStock = /^\d+$/;
    const expCosto = /^\d+(\.\d{1,2})?$/;
    const expSoloLetras = /^[A-Za-zÁáÉéÍíÓóÚúÑñ\s]*$/;

    const crearNombre = $("#nombre");
    const crearUnidadMedida = $("#id_unidad_medida");
    const crearStock = $("#stock");
    const crearCosto = $("#costo");
    const crearForm = $("#formCrearMateria");

    const editarNombre = $("#edit_nombre");
    const editarUnidadMedida = $("#edit_id_unidad_medida");
    const editarStock = $("#edit_stock");
    const editarCosto = $("#edit_costo");
    const editarForm = $("#formEditarMateria");
    
    agregarElementosError();
    
    function agregarElementosError() {
        if ($('#nombre_error').length === 0) {
            $('#nombre').after('<div class="error-message text-danger small mt-1" id="nombre_error"></div>');
        }
        if ($('#id_unidad_medida_error').length === 0) {
            $('#id_unidad_medida').after('<div class="error-message text-danger small mt-1" id="id_unidad_medida_error"></div>');
        }
        if ($('#stock_error').length === 0) {
            $('#stock').after('<div class="error-message text-danger small mt-1" id="stock_error"></div>');
        }
        if ($('#costo_error').length === 0) {
            $('#costo').after('<div class="error-message text-danger small mt-1" id="costo_error"></div>');
        }
        
        if ($('#edit_nombre_error').length === 0) {
            $('#edit_nombre').after('<div class="error-message text-danger small mt-1" id="edit_nombre_error"></div>');
        }
        if ($('#edit_id_unidad_medida_error').length === 0) {
            $('#edit_id_unidad_medida').after('<div class="error-message text-danger small mt-1" id="edit_id_unidad_medida_error"></div>');
        }
        if ($('#edit_stock_error').length === 0) {
            $('#edit_stock').after('<div class="error-message text-danger small mt-1" id="edit_stock_error"></div>');
        }
        if ($('#edit_costo_error').length === 0) {
            $('#edit_costo').after('<div class="error-message text-danger small mt-1" id="edit_costo_error"></div>');
        }
        
        $(".error-message").hide();
    }
    
    function validarSoloLetras(input, campo) {
        const valor = input.val().trim();
        const errorElement = $(`#${campo}_error`);
        
        if (valor === '') {
            input.css('border-color', '#dc3545');
            errorElement.show().html('Este campo es obligatorio');
            return false;
        }
        
        if (!expSoloLetras.test(valor)) {
            input.css('border-color', '#dc3545');
            errorElement.show().html('Solo se permiten letras y espacios');
            return false;
        }
        
        if (!expNombre.test(valor)) {
            input.css('border-color', '#dc3545');
            errorElement.show().html('El nombre debe tener entre 2 y 100 caracteres (solo letras)');
            return false;
        }
        
        input.css('border-color', '#198754');
        errorElement.hide();
        return true;
    }
    
    function validarObligatorio(input, campo, mensaje) {
        const valor = input.val();
        const errorElement = $(`#${campo}_error`);
        
        if (!valor) {
            input.css('border-color', '#dc3545');
            errorElement.show().html(mensaje);
            return false;
        }
        
        input.css('border-color', '#198754');
        errorElement.hide();
        return true;
    }
    
    function validarNumeroPositivo(input, campo, mensaje, esEntero = false) {
        const valor = input.val();
        const errorElement = $(`#${campo}_error`);
        
        if (valor === '') {
            input.css('border-color', '#dc3545');
            errorElement.show().html('Este campo es obligatorio');
            return false;
        }
        
        const num = esEntero ? parseInt(valor) : parseFloat(valor);
        
        if (isNaN(num) || num < 0) {
            input.css('border-color', '#dc3545');
            errorElement.show().html(mensaje);
            return false;
        }
        
        if (esEntero && !Number.isInteger(num)) {
            input.css('border-color', '#dc3545');
            errorElement.show().html('Debe ser un número entero');
            return false;
        }
        
        if (!esEntero && num <= 0) {
            input.css('border-color', '#dc3545');
            errorElement.show().html('El valor debe ser mayor a 0');
            return false;
        }
        
        input.css('border-color', '#198754');
        errorElement.hide();
        return true;
    }
    
    function prevenirNumeros(input) {
        input.on('keypress', function(e) {
            const char = String.fromCharCode(e.which);
            if (/[0-9]/.test(char)) {
                e.preventDefault();
                return false;
            }
        });
        
        input.on('input', function() {
            const valor = $(this).val();
            const limpio = valor.replace(/[0-9]/g, '');
            if (valor !== limpio) {
                $(this).val(limpio);
            }
        });
    }
    
    prevenirNumeros(crearNombre);
    prevenirNumeros(editarNombre);
    
    crearNombre.on("input", function() {
        validarSoloLetras(crearNombre, 'nombre');
    });
    
    crearUnidadMedida.on("change", function() {
        validarObligatorio(crearUnidadMedida, 'id_unidad_medida', 'Seleccione una unidad de medida');
    });
    
    crearStock.on("input", function() {
        validarNumeroPositivo(crearStock, 'stock', 'El stock debe ser un número entero no negativo', true);
    });
    
    crearCosto.on("input", function() {
        validarNumeroPositivo(crearCosto, 'costo', 'El costo debe ser un número mayor a 0 (ej: 10.50)');
    });

    editarNombre.on("input", function() {
        validarSoloLetras(editarNombre, 'edit_nombre');
    });
    
    editarUnidadMedida.on("change", function() {
        validarObligatorio(editarUnidadMedida, 'edit_id_unidad_medida', 'Seleccione una unidad de medida');
    });
    
    editarStock.on("input", function() {
        validarNumeroPositivo(editarStock, 'edit_stock', 'El stock debe ser un número entero no negativo', true);
    });
    
    editarCosto.on("input", function() {
        validarNumeroPositivo(editarCosto, 'edit_costo', 'El costo debe ser un número mayor a 0 (ej: 10.50)');
    });

    function limpiarFormularioCrear() {
        console.log('Limpiando formulario crear');
        crearForm[0].reset();
        $("#nombre, #id_unidad_medida, #stock, #costo").css('border-color', '');
        $("#nombre_error, #id_unidad_medida_error, #stock_error, #costo_error").hide();
    }
    
    function limpiarFormularioEditar() {
        console.log('Limpiando formulario editar');
        editarForm[0].reset();
        $("#edit_nombre, #edit_id_unidad_medida, #edit_stock, #edit_costo").css('border-color', '');
        $("#edit_nombre_error, #edit_id_unidad_medida_error, #edit_stock_error, #edit_costo_error").hide();
        $("#edit_id_materia").val('');
    }
    
    $(document).on('click', '#registrarMateriaModal .btn-secondary', function() {
        console.log('Botón cancelar crear clickeado');
        limpiarFormularioCrear();
    });
    
    $(document).on('click', '#editarMateriaModal .btn-secondary', function() {
        console.log('Botón cancelar editar clickeado');
        limpiarFormularioEditar();
    });
    
    // Eventos para cierre de modales
    $('#registrarMateriaModal').on('hidden.bs.modal', function () {
        console.log('Modal crear cerrado');
        limpiarFormularioCrear();
    });
    
    $('#editarMateriaModal').on('hidden.bs.modal', function () {
        console.log('Modal editar cerrado');
        limpiarFormularioEditar();
    });

    crearForm.on('submit', function(e){
        console.log('Envío de formulario crear materia prima');
        
        let crearValido = true;
        
        if (!validarSoloLetras(crearNombre, 'nombre')) {
            crearValido = false;
        }
        
        if (!validarObligatorio(crearUnidadMedida, 'id_unidad_medida', 'Seleccione una unidad de medida')) {
            crearValido = false;
        }
        
        if (!validarNumeroPositivo(crearStock, 'stock', 'El stock debe ser un número entero no negativo', true)) {
            crearValido = false;
        }
        
        if (!validarNumeroPositivo(crearCosto, 'costo', 'El costo debe ser un número mayor a 0')) {
            crearValido = false;
        }
        
        if (!crearValido) {
            console.log('Validación de creación falló');
            e.preventDefault();
            mostrarMensajeTemporal('error', 'Error de validación', 'Por favor, complete todos los campos correctamente', 3000);
            
            const primerError = $('.error-message:visible').first();
            if (primerError.length) {
                $('html, body').animate({
                    scrollTop: primerError.offset().top - 100
                }, 500);
            }
        } else {
            console.log('Validación de creación exitosa');
            mostrarMensajeCarga('Registrando materia prima...');
        }
    });
    
    editarForm.on('submit', function(e){
        console.log('Envío de formulario editar materia prima');
        
        let editarValido = true;
        
        if (!validarSoloLetras(editarNombre, 'edit_nombre')) {
            editarValido = false;
        }
        
        if (!validarObligatorio(editarUnidadMedida, 'edit_id_unidad_medida', 'Seleccione una unidad de medida')) {
            editarValido = false;
        }
        
        if (!validarNumeroPositivo(editarStock, 'edit_stock', 'El stock debe ser un número entero no negativo', true)) {
            editarValido = false;
        }
        
        if (!validarNumeroPositivo(editarCosto, 'edit_costo', 'El costo debe ser un número mayor a 0')) {
            editarValido = false;
        }
        
        if (!editarValido) {
            console.log('Validación de edición falló');
            e.preventDefault();
            mostrarMensajeTemporal('error', 'Error de validación', 'Por favor, complete todos los campos correctamente', 3000);
            
            const primerError = $('.error-message:visible').first();
            if (primerError.length) {
                $('html, body').animate({
                    scrollTop: primerError.offset().top - 100
                }, 500);
            }
        } else {
            console.log('Validación de edición exitosa');
            mostrarMensajeCarga('Actualizando materia prima...');
        }
    });
});

function mostrarMensajeTemporal(tipo, titulo, mensaje, tiempo) {
    alert(`${titulo}: ${mensaje}`);
}

function mostrarMensajeCarga(mensaje) {
    console.log(mensaje);
}