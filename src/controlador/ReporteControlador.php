<?php

use src\modelo\reporteVentasModelo;
use src\modelo\reporteComprasModelo;
use src\modelo\reporteComprasPDFModelo;
use src\modelo\reporteVentasPDFModelo;
use src\modelo\reporteServicioModelo;
use src\modelo\reporteMateriaPrimaModelo;
use src\modelo\reporteProductoModelo;

require_once 'src/controlador/verificar_sesion.php';


function isAjaxRequest() {
    return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') ||
           (isset($_GET['_']) && is_numeric($_GET['_'])); 
}

function sendJsonResponse($success, $message, $details = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'details' => $details,
        'data' => $data
    ]);
    exit;
}

$reporteVentasModelo = new reporteVentasModelo();
$reporteComprasModelo = new reporteComprasModelo();

switch ($metodo) {
    case 'index':
        require 'src/vista/parcial/reportes.php';
        break;

    case 'ajaxCargarItems':
        if (!isAjaxRequest() && empty($_GET['tipo'])) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        try {
            $tipo = $_GET['tipo'] ?? 'todos';
            $items = $reporteVentasModelo->obtenerItemsParaFiltro($tipo);
            
            sendJsonResponse(true, "Items cargados exitosamente", "", ['data' => $items]);
            
        } catch (Exception $e) {
            error_log("Error en ajaxCargarItems: " . $e->getMessage());
            sendJsonResponse(false, "Error al cargar items", $e->getMessage());
        }
        break;

    case 'ajaxCargarMateriasPrimas':
        if (!isAjaxRequest() && empty($_GET['_'])) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        try {
            $materias = $reporteComprasModelo->obtenerMateriasPrimasParaFiltro();
            
            sendJsonResponse(true, "Materias primas cargadas exitosamente", "", ['data' => $materias]);
            
        } catch (Exception $e) {
            error_log("Error en ajaxCargarMateriasPrimas: " . $e->getMessage());
            sendJsonResponse(false, "Error al cargar materias primas", $e->getMessage());
        }
        break;

    case 'ventasParametrizadas':
        try {
            $tipoProducto = $_POST['tipo_producto'] ?? 'todos';
            $idItem = $_POST['id_item'] ?? null;
            $periodo = $_POST['periodo'] ?? 'dia';
            $fechaDesde = $_POST['fecha_desde'] ?? null;
            $fechaHasta = $_POST['fecha_hasta'] ?? null;
            $mes = $_POST['mes'] ?? date('m');
            $anio = $_POST['anio'] ?? date('Y');
            
            $datosReporte = $reporteVentasModelo->obtenerVentasParametrizadas(
                $tipoProducto, 
                $idItem, 
                $periodo, 
                $fechaDesde, 
                $fechaHasta, 
                $mes, 
                $anio
            );
            
            $pdf = new reporteVentasPDFModelo();
            $pdf->generarPDF($datosReporte);
            
        } catch (Exception $e) {
            error_log("Error en ventasParametrizadas: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'comprasParametrizadas':
        try {
            $tipoMateria = $_POST['tipo_materia'] ?? 'todos';
            $idMateria = $_POST['id_materia'] ?? null;
            $periodo = $_POST['periodo'] ?? 'dia';
            $fechaDesde = $_POST['fecha_desde'] ?? null;
            $fechaHasta = $_POST['fecha_hasta'] ?? null;
            $mes = $_POST['mes'] ?? date('m');
            $anio = $_POST['anio'] ?? date('Y');
            
            $datosReporte = $reporteComprasModelo->obtenerComprasParametrizadas(
                $tipoMateria, 
                $idMateria, 
                $periodo, 
                $fechaDesde, 
                $fechaHasta, 
                $mes, 
                $anio
            );
            
            $pdf = new reporteComprasPDFModelo();
            $pdf->generarPDF($datosReporte);
            
        } catch (Exception $e) {
            error_log("Error en comprasParametrizadas: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'listarServicios':
        try {
            $reporteServicio = new reporteServicioModelo();
            $datosServicios = $reporteServicio->obtenerDatosServicios();
            $reporteServicio->generarPDF($datosServicios);
            
        } catch (Exception $e) {
            error_log("Error en listarServicios: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte de servicios: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'inventarioProductos':
        try {
            $reporteProducto = new reporteProductoModelo();
            $datosProductos = $reporteProducto->obtenerDatosProductos();
            $reporteProducto->generarPDF($datosProductos);
            
        } catch (Exception $e) {
            error_log("Error en inventarioProductos: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte de productos: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'inventarioMateriasPrimas':
        try {
            $reporteMateriaPrima = new reporteMateriaPrimaModelo();
            $datosMateriasPrimas = $reporteMateriaPrima->obtenerDatosMateriasPrimas();
            $reporteMateriaPrima->generarPDF($datosMateriasPrimas);
            
        } catch (Exception $e) {
            error_log("Error en inventarioMateriasPrimas: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte de materias primas: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'ventasDia':
        try {
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            $datosReporte = $reporteVentasModelo->obtenerVentasPorDia($fecha);
            
            $pdf = new reporteVentasPDFModelo();
            $pdf->generarPDF($datosReporte, 'dia');
            
        } catch (Exception $e) {
            error_log("Error en ventasDia: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte del día: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'ventasSemana':
        try {
            $fechaInicio = $_POST['fecha_inicio'] ?? null;
            $fechaFin = $_POST['fecha_fin'] ?? null;
            $datosReporte = $reporteVentasModelo->obtenerVentasPorSemana($fechaInicio, $fechaFin);
            
            $pdf = new reporteVentasPDFModelo();
            $pdf->generarPDF($datosReporte, 'semana');
            
        } catch (Exception $e) {
            error_log("Error en ventasSemana: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte semanal: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'ventasMes':
        try {
            $mes = $_POST['mes'] ?? date('m');
            $anio = $_POST['anio'] ?? date('Y');
            $datosReporte = $reporteVentasModelo->obtenerVentasPorMes($mes, $anio);
            
            $pdf = new reporteVentasPDFModelo();
            $pdf->generarPDF($datosReporte, 'mes');
            
        } catch (Exception $e) {
            error_log("Error en ventasMes: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte mensual: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'ventasAnio':
        try {
            $anio = $_POST['anio'] ?? date('Y');
            $datosReporte = $reporteVentasModelo->obtenerVentasPorAnio($anio);
            
            $pdf = new reporteVentasPDFModelo();
            $pdf->generarPDF($datosReporte, 'anio');
            
        } catch (Exception $e) {
            error_log("Error en ventasAnio: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte anual: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'comprasDia':
        try {
            $fecha = $_POST['fecha'] ?? date('Y-m-d');
            $datosReporte = $reporteComprasModelo->obtenerComprasPorDia($fecha);
            
            $pdf = new reporteComprasPDFModelo();
            $pdf->generarPDF($datosReporte, 'dia');
            
        } catch (Exception $e) {
            error_log("Error en comprasDia: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte de compras del día: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'comprasSemana':
        try {
            $fechaInicio = $_POST['fecha_inicio'] ?? null;
            $fechaFin = $_POST['fecha_fin'] ?? null;
            $datosReporte = $reporteComprasModelo->obtenerComprasPorSemana($fechaInicio, $fechaFin);
            
            $pdf = new reporteComprasPDFModelo();
            $pdf->generarPDF($datosReporte, 'semana');
            
        } catch (Exception $e) {
            error_log("Error en comprasSemana: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte de compras semanal: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'comprasMes':
        try {
            $mes = $_POST['mes'] ?? date('m');
            $anio = $_POST['anio'] ?? date('Y');
            $datosReporte = $reporteComprasModelo->obtenerComprasPorMes($mes, $anio);
            
            $pdf = new reporteComprasPDFModelo();
            $pdf->generarPDF($datosReporte, 'mes');
            
        } catch (Exception $e) {
            error_log("Error en comprasMes: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte de compras mensual: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    case 'comprasAnio':
        try {
            $anio = $_POST['anio'] ?? date('Y');
            $datosReporte = $reporteComprasModelo->obtenerComprasPorAnio($anio);
            
            $pdf = new reporteComprasPDFModelo();
            $pdf->generarPDF($datosReporte, 'anio');
            
        } catch (Exception $e) {
            error_log("Error en comprasAnio: " . $e->getMessage());
            echo "<script>alert('Error al generar reporte de compras anual: " . $e->getMessage() . "'); history.back();</script>";
        }
        break;

    default:
        require 'src/vista/reportes.php';
        break;
}
?>