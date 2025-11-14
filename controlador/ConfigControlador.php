<?php 
require_once 'modelo/UnidadMedidaModelo.php';
require_once 'modelo/CondicionPagoModelo.php';
require_once 'modelo/IvaModelo.php';
require_once 'modelo/DescuentoModelo.php';
require_once 'modelo/PresentacionModelo.php';
require_once 'controlador/verificar_sesion.php';

$unidadMedida = new UnidadMedida();
$condicionPago = new CondicionPago();
$Iva = new Iva();
$descuento = new Descuento();
$presentacion = new Presentacion();

$activeTab = $_GET['tab'] ?? 'condicion-pago';

switch ($metodo) {
    case 'index':
        $unidades = $unidadMedida->listar();
        $condicion = $condicionPago->listar();
        $iva = $Iva->listar();
        $descuentos = $descuento->listar();
        $presentaciones = $presentacion->listar();
        
        require_once 'vista/configuracion/index.php';
        break;
        
    case 'crear':
    case 'guardar':
    case 'editar':
    case 'actualizar':
    case 'eliminar':
        $tipo = $_GET['tipo'] ?? 'condicion';
        $tab = $_GET['tab'] ?? 'condicion-pago';
        
        switch($tipo) {
            case 'medida':
                header("Location: index.php?c=UnidadMedidaControlador&m=$metodo&tab=$activeTab");
                break;
            case 'presentacion':
                header("Location: index.php?c=PresentacionControlador&m=$metodo&tab=$activeTab");
                break;
            case 'iva':
                header("Location: index.php?c=IvaControlador&m=$metodo&tab=$activeTab");
                break;
            case 'descuento':
                header("Location: index.php?c=DescuentoControlador&m=$metodo&tab=$activeTab");
                break;
            case 'condicion-pago':
                header("Location: index.php?c=CondicionPagoControlador&m=$metodo&tab=$activeTab");
                break;
            default:
                header("Location: index.php?c=ConfigControlador&m=index&tab=$activeTab");
                break;
        }
        exit;
        break;
}
?>