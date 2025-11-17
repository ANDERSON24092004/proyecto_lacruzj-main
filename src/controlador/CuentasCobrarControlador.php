<?php

use src\modelo\cuentasCobrarModelo;
use src\modelo\cuentasCobrarPDFModelo;
require_once 'src/controlador/verificar_sesion.php';

function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
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

$cuentasModelo = new cuentasCobrarModelo();

switch ($metodo) {
    case 'index':    
        require 'src/vista/cuentas_cobrar/index.php';
        break;

    case 'listar':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        try {
            $cuentas = $cuentasModelo->listarFacturasCredito();
            sendJsonResponse(true, "Datos cargados", "", $cuentas);
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al cargar cuentas", $e->getMessage());
        }
        break;

    case 'ver':     
        $nro_fact = $_GET['id'] ?? null;
        if (!$nro_fact) {
            sendJsonResponse(false, "El número de factura no es proporcionado");
        }
        
        try {
            $cuenta = $cuentasModelo->obtenerDetallesCuenta($nro_fact);
            if (!$cuenta) {
                sendJsonResponse(false, "Cuenta no encontrada");
            }
            
            $detallesFactura = $cuentasModelo->obtenerDetallesFactura($nro_fact);
            
            $productos = array_filter($detallesFactura, function($item) {
                return $item['tipo'] == 1;
            });
            
            $servicios = array_filter($detallesFactura, function($item) {
                return $item['tipo'] == 2;
            });
        
            $subtotalProductos = array_sum(array_column($productos, 'subtotal'));
            $subtotalServicios = array_sum(array_column($servicios, 'subtotal'));
            $subtotalGeneral = $subtotalProductos + $subtotalServicios;
            
            sendJsonResponse(true, "Datos de la cuenta cargados", "", [
                'cuenta' => $cuenta,
                'productos' => array_values($productos),
                'servicios' => array_values($servicios),
                'subtotales' => [
                    'productos' => $subtotalProductos,
                    'servicios' => $subtotalServicios,
                    'general' => $subtotalGeneral
                ]
            ]);
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al cargar los detalles", $e->getMessage());
        }
        break;

    case 'registrar_pago':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $nro_fact = $_POST['nro_fact'] ?? '';
        $fecha_pago = $_POST['fecha_pago'] ?? '';

        try {
            if (empty($nro_fact) || empty($fecha_pago)) {
                throw new Exception("Datos incompletos para registrar el pago");
            }

            $resultado = $cuentasModelo->registrarPagoCompleto($nro_fact, $fecha_pago);
            
            if ($resultado['success']) {
                sendJsonResponse(true, "Pago registrado exitosamente", 
                    "Se registró el pago completo de " . 
                    number_format($resultado['monto_pagado'], 2, ',', '.') . " Bs.");
            } else {
                throw new Exception("No se pudo registrar el pago");
            }
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al registrar pago", $e->getMessage());
        }
        break;

    case 'anular':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $nro_fact = $_POST['nro_fact'] ?? null;
        if (!$nro_fact) {
            sendJsonResponse(false, "El número de factura no es proporcionado");
        }
        
        try {
            if ($cuentasModelo->anularCuenta($nro_fact)) {
                sendJsonResponse(true, "Cuenta anulada exitosamente", "La cuenta ha sido anulada correctamente");
            } else {
                throw new Exception("No se pudo anular la cuenta");
            }
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al anular la cuenta", $e->getMessage());
        }
        break;

    case 'reactivar':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $nro_fact = $_POST['nro_fact'] ?? null;
        if (!$nro_fact) {
            sendJsonResponse(false, "El número de factura no es proporcionado");
        }
        
        try {
            if ($cuentasModelo->reactivarCuenta($nro_fact)) {
                sendJsonResponse(true, "Cuenta reactivada exitosamente", "La cuenta ha sido reactivada correctamente");
            } else {
                throw new Exception("No se pudo reactivar la cuenta");
            }
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al reactivar la cuenta", $e->getMessage());
        }
        break;

    case 'imprimir':
    $nro_fact = $_GET['id'] ?? null;
    if ($nro_fact) {
        try {
            $cuenta = $cuentasModelo->obtenerDetallesCuenta($nro_fact);
            if ($cuenta) {
                $detalles = $cuentasModelo->obtenerDetallesFactura($nro_fact);
                
                $datosPDF = [
                    'cuenta' => $cuenta,
                    'detalles' => $detalles,
                ];
                
                $logoPath = 'assets/images/logo2.png';
                $firmaPath = 'assets/images/firma.jpeg'; 
                
                $pdf = new cuentasCobrarPDFModelo($datosPDF, $logoPath, $firmaPath);
                $pdf->generarPDF();
                
                $pdf->Output('I', 'Estado_Cuenta_' . $nro_fact . '.pdf');
                exit();
                
            } else {
                $_SESSION['tipo_mensaje'] = "error";
                $_SESSION['mensaje'] = "Cuenta no encontrada";
                header("Location: index.php?c=CuentasCobrarControlador&m=index");
                exit();
            }
        } catch (Exception $e) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error al generar el PDF";
            $_SESSION['mensaje_detalle'] = $e->getMessage();
            header("Location: index.php?c=CuentasCobrarControlador&m=index");
            exit();
        }
    } else {
        $_SESSION['tipo_mensaje'] = "error";
        $_SESSION['mensaje'] = "No se especificó la cuenta a imprimir";
        header("Location: index.php?c=CuentasCobrarControlador&m=index");
        exit();
    }
    break;

    default:
        require_once 'src/vista/cuentas_cobrar/index.php';
        break;
}
?>