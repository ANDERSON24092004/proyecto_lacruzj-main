<?php

use src\modelo\clienteModelo;
use src\modelo\productoServicioModelo;
use src\modelo\descuentoModelo;
use src\modelo\ivaModelo;
use src\modelo\presupuestoModelo;
use src\modelo\presupuestoPDFModelo;

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

$iva = new ivaModelo();
$presupuestoModelo = new presupuestoModelo();
$clienteModelo = new clienteModelo();
$productoServicioModelo = new productoServicioModelo();
$descuentoModelo = new descuentoModelo();

switch ($metodo) {
    case 'index':
        if (isAjaxRequest() && isset($_GET['ajax'])) {
            try {
                $presupuestos = $presupuestoModelo->listarPresupuestos();
                sendJsonResponse(true, "Datos cargados", "", $presupuestos);
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al cargar presupuestos", $e->getMessage());
            }
        } else {
            $ivas = $iva->listar();
            $clientes = $clienteModelo->listar();
            $productos = $presupuestoModelo->obtenerProductosDisponibles();
            $servicios = $presupuestoModelo->obtenerServiciosDisponibles();
            $descuentos = $presupuestoModelo->obtenerDescuentos();
            $presupuestos = $presupuestoModelo->listarPresupuestos();
            require 'src/vista/presupuesto/index.php';
        }
        break;

    case 'crear':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $ivas = $iva->listar();
        $clientes = $clienteModelo->listar();
        $productos = $presupuestoModelo->obtenerProductosDisponibles();
        $servicios = $presupuestoModelo->obtenerServiciosDisponibles();
        $descuentos = $presupuestoModelo->obtenerDescuentos();
        
        sendJsonResponse(true, "Datos para crear presupuesto", "", [
            'ivas' => $ivas,
            'clientes' => $clientes,
            'productos' => $productos,
            'servicios' => $servicios,
            'descuentos' => $descuentos
        ]);
        break;

    case 'guardar_presupuesto':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        try {
            $errores = $presupuestoModelo->validarDatosPresupuesto($_POST);
            
            if (!empty($errores)) {
                throw new Exception(implode("<br>", $errores));
            }

            $total_sin_descuento = 0;
            $total_iva = 0;
            $total_general = 0;
            
            $iva_porcentaje = 16;
            if (!empty($_POST['id_iva'])) {
                $ivaData = $iva->buscarPorId($_POST['id_iva']);
                $iva_porcentaje = $ivaData ? $ivaData['porcentaje'] : 16;
            }

            foreach ($_POST['detalles'] as $detalle) {
                $precio = $presupuestoModelo->obtenerPrecioProducto($detalle['id_inv'], $detalle['cantidad']);
                $subtotal = $precio * $detalle['cantidad'];
                $total_sin_descuento += $subtotal;
            }

            $descuento_porcentaje = 0;
            if (!empty($_POST['id_descuento'])) {
                $descuentoData = $descuentoModelo->buscarPorId($_POST['id_descuento']);
                $descuento_porcentaje = $descuentoData ? $descuentoData['porcentaje'] : 0;
            }

            $subtotal_con_descuento = $total_sin_descuento * (1 - $descuento_porcentaje / 100);
            $monto_iva = $subtotal_con_descuento * ($iva_porcentaje / 100);
            $total_iva = $monto_iva;
            $total_general = $subtotal_con_descuento + $monto_iva;

            $presupuestoModelo->setRif($_POST['rif']);
            $presupuestoModelo->setNumeroOrden($_POST['numero_orden']);
            $presupuestoModelo->setTotalIva($total_iva);
            $presupuestoModelo->setTotalGeneral($total_general);
            $presupuestoModelo->setFecha($_POST['fecha']);
            $presupuestoModelo->setIdDescuento($_POST['id_descuento'] ?? null);
            $presupuestoModelo->setIdIva($_POST['id_iva'] ?? null);

            $detalles = [];
            foreach ($_POST['detalles'] as $detalle) {
                $detalles[] = [
                    'id_inv' => $detalle['id_inv'],
                    'cantidad' => $detalle['cantidad']
                ];
            }
            $presupuestoModelo->setDetalles($detalles);

            $nro_presupuesto = $presupuestoModelo->registrarPresupuesto();

            if (!$nro_presupuesto || $nro_presupuesto == 0) {
                throw new Exception("No se pudo obtener el ID del presupuesto creado. ID: " . $nro_presupuesto);
            }

            sendJsonResponse(true, "Presupuesto registrado exitosamente", "Presupuesto #" . $nro_presupuesto . " creado para el cliente " . $_POST['rif'], ['nro_presupuesto' => $nro_presupuesto]);

        } catch (Exception $e) {
            error_log("ERROR en guardar_presupuesto: " . $e->getMessage());
            sendJsonResponse(false, "Error al registrar el presupuesto", $e->getMessage());
        }
        break;

    case 'anular':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $nro_presupuesto = $_POST['id'] ?? null;
        if ($nro_presupuesto) {
            try {
                if ($presupuestoModelo->anularPresupuesto($nro_presupuesto)) {
                    sendJsonResponse(true, "Presupuesto anulado exitosamente", "El presupuesto #" . $nro_presupuesto . " ha sido anulado");
                } else {
                    throw new Exception("No se pudo anular el presupuesto");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al anular el presupuesto", $e->getMessage());
            }
        } else {
            sendJsonResponse(false, "Error", "Número de presupuesto no proporcionado");
        }
        break;

    case 'reactivar':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $nro_presupuesto = $_POST['id'] ?? null;
        if ($nro_presupuesto) {
            try {
                if ($presupuestoModelo->reactivarPresupuesto($nro_presupuesto)) {
                    sendJsonResponse(true, "Presupuesto reactivado exitosamente", "El presupuesto #" . $nro_presupuesto . " ha sido reactivado");
                } else {
                    throw new Exception("No se pudo reactivar el presupuesto");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al reactivar el presupuesto", $e->getMessage());
            }
        } else {
            sendJsonResponse(false, "Error", "Número de presupuesto no proporcionado");
        }
        break;

    case 'obtenerClienteAjax':
        if (isAjaxRequest() && isset($_GET['rif'])) {
            $rif = $_GET['rif'];
            $clienteData = $clienteModelo->obtener($rif);
            header('Content-Type: application/json');
            echo json_encode($clienteData);
            exit;
        }
        break;

    case 'obtenerDetallesPresupuestoAjax':
        if (isAjaxRequest() && isset($_GET['nro_presupuesto'])) {
            $nro_presupuesto = $_GET['nro_presupuesto'];
            
            try {
                if (empty($nro_presupuesto) || !is_numeric($nro_presupuesto)) {
                    throw new Exception("Número de presupuesto inválido");
                }
                
                $presupuestoData = $presupuestoModelo->obtenerDetallesPresupuestoCompleto($nro_presupuesto);
                
                if ($presupuestoData) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'data' => $presupuestoData
                    ]);
                } else {
                    throw new Exception("Presupuesto #{$nro_presupuesto} no encontrado en el sistema");
                }
            } catch (Exception $e) {
                error_log("Error en obtenerDetallesPresupuestoAjax: " . $e->getMessage());
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            exit;
        }
        break;

    case 'obtenerPrecioProductoAjax':
        if (isAjaxRequest() && isset($_POST['id_inv']) && isset($_POST['cantidad'])) {
            $id_inv = $_POST['id_inv'];
            $cantidad = $_POST['cantidad'];
            
            $precio = $presupuestoModelo->obtenerPrecioProducto($id_inv, $cantidad);
            
            header('Content-Type: application/json');
            echo json_encode(['precio' => $precio]);
            exit;
        }
        break;

    case 'obtener':
        if (isset($_GET['id'])) {
            $nro_presupuesto = $_GET['id'];
            
            try {
                $presupuestoData = $presupuestoModelo->obtenerDetallesPresupuestoCompleto($nro_presupuesto);
                
                if ($presupuestoData) {
                    $productos = [];
                    $servicios = [];
                    
                    foreach ($presupuestoData['detalles'] as $detalle) {
                        if ($detalle['tipo'] == 1 || $detalle['tipo_nombre'] == 'Producto') {
                            $productos[] = $detalle;
                        } else {
                            $servicios[] = $detalle;
                        }
                    }
                    
                    $response = [
                        'success' => true,
                        'presupuesto' => $presupuestoData['presupuesto'],
                        'cliente' => [
                            'rif' => $presupuestoData['presupuesto']['rif'],
                            'razon_social' => $presupuestoData['presupuesto']['razon_social'],
                            'nombre_cliente' => $presupuestoData['presupuesto']['razon_social'],
                            'telefono' => $presupuestoData['presupuesto']['telefono'],
                            'correo' => $presupuestoData['presupuesto']['correo'],
                            'direccion' => $presupuestoData['presupuesto']['direccion']
                        ],
                        'detalles' => [
                            'productos' => $productos,
                            'servicios' => $servicios
                        ]
                    ];
                    
                    header('Content-Type: application/json');
                    echo json_encode($response);
                } else {
                    throw new Exception("Presupuesto no encontrado");
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
        }
        break;

    case 'imprimir':
        $nro_presupuesto = $_GET['id'] ?? null;
        if ($nro_presupuesto) {
            try {
                $presupuestoData = $presupuestoModelo->obtenerDetallesPresupuestoCompleto($nro_presupuesto);
                
                if (!$presupuestoData) {
                    throw new Exception("Presupuesto no encontrado");
                }
                $logoPath = 'assets/images/logo2.png';
                
                if (!file_exists($logoPath)) {
                    $logoPath = '';
                    error_log("Logo no encontrado en: " . realpath($logoPath));
                }
                
                $pdf = new presupuestoPDFModelo($presupuestoData, $logoPath);
                $pdf->generarPDF();
                
                $pdf->Output('I', 'presupuesto_' . $nro_presupuesto . '.pdf');
                exit;
                
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al generar el PDF", $e->getMessage());
            }
        }
        break;
}
?>