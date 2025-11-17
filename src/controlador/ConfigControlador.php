<?php 

use src\modelo\unidadMedidaModelo;
use src\modelo\condicionPagoModelo;
use src\modelo\ivaModelo;
use src\modelo\descuentoModelo;
use src\modelo\presentacionModelo;
require_once 'src/controlador/verificar_sesion.php';



$unidadMedida = new unidadMedidaModelo();
$condicionPago = new condicionPagoModelo();
$Iva = new ivaModelo();
$descuento = new descuentoModelo();
$presentacion = new presentacionModelo();

$activeTab = $_GET['tab'] ?? 'condicion-pago';

switch ($metodo) {
    case 'index':
        $unidades = $unidadMedida->listar();
        $condicion = $condicionPago->listar();
        $iva = $Iva->listar();
        $descuentos = $descuento->listar();
        $presentaciones = $presentacion->listar();
        
        require_once 'src/vista/configuracion/index.php';
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