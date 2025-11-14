<?php
require_once 'modelo/FacturaModelo.php';
require_once 'modelo/ClienteModelo.php';
require_once 'modelo/ProductoServicioModelo.php';
require_once 'modelo/CondicionPagoModelo.php';
require_once 'modelo/DescuentoModelo.php';
require_once 'modelo/IvaModelo.php';
require_once 'controlador/verificar_sesion.php';

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

$iva = new Iva();
$facturaModelo = new FacturaModel();
$clienteModelo = new ClienteModel();
$productoServicioModelo = new ProductoServicio();
$descuentoModelo = new Descuento();

switch ($metodo) {
    case 'index':
        if (isAjaxRequest() && isset($_GET['ajax'])) {
            try {
                $facturas = $facturaModelo->listarFacturas();
                sendJsonResponse(true, "Datos cargados", "", $facturas);
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al cargar facturas", $e->getMessage());
            }
        } else {
            $ivas = $iva->listar();
            $clientes = $clienteModelo->listar();
            $productos = $facturaModelo->obtenerProductosDisponibles();
            $servicios = $facturaModelo->obtenerServiciosDisponibles();
            $condicionesPago = $facturaModelo->obtenerCondicionesPago();
            $descuentos = $facturaModelo->obtenerDescuentos();
            $facturas = $facturaModelo->listarFacturas();
            require 'vista/factura/index.php';
        }
        break;

    case 'crear':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $ivas = $iva->listar();
        $clientes = $clienteModelo->listar();
        $productos = $facturaModelo->obtenerProductosDisponibles();
        $servicios = $facturaModelo->obtenerServiciosDisponibles();
        $condicionesPago = $facturaModelo->obtenerCondicionesPago();
        $descuentos = $facturaModelo->obtenerDescuentos();
        
        sendJsonResponse(true, "Datos para crear factura", "", [
            'ivas' => $ivas,
            'clientes' => $clientes,
            'productos' => $productos,
            'servicios' => $servicios,
            'condicionesPago' => $condicionesPago,
            'descuentos' => $descuentos
        ]);
        break;

   case 'guardar_factura':
    if (!isAjaxRequest()) {
        sendJsonResponse(false, "Acceso no permitido");
    }
    
    try {
        $errores = $facturaModelo->validarDatosFactura($_POST);
        
        if (!empty($errores)) {
            throw new Exception(implode("<br>", $errores));
        }

        foreach ($_POST['detalles'] as $detalle) {
            if (!$facturaModelo->verificarStockDisponible($detalle['id_inv'], $detalle['cantidad'])) {
                $nombreProducto = $facturaModelo->obtenerNombreProducto($detalle['id_inv']);
                throw new Exception("Stock insuficiente para: " . $nombreProducto);
            }
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
            $precio = $facturaModelo->obtenerPrecioProducto($detalle['id_inv'], $detalle['cantidad']);
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

        $facturaModelo->setRif($_POST['rif']);
        $facturaModelo->setIdCondicionPago($_POST['id_condicion_pago']);
        $facturaModelo->setNumeroOrden($_POST['numero_orden']);
        $facturaModelo->setTotalIva($total_iva);
        $facturaModelo->setTotalGeneral($total_general);
        $facturaModelo->setFecha($_POST['fecha']);
        $facturaModelo->setIdDescuento($_POST['id_descuento'] ?? null);
        $facturaModelo->setIdIva($_POST['id_iva'] ?? null);

        if ($_POST['id_condicion_pago'] == 2) {
            $fechaCredito = date('Y-m-d', strtotime($_POST['fecha'] . ' + 90 days'));
            $facturaModelo->setDuracionCredito($fechaCredito);
        }
    
        $detalles = [];
        foreach ($_POST['detalles'] as $detalle) {
            $detalles[] = [
                'id_inv' => $detalle['id_inv'],
                'cantidad' => $detalle['cantidad']
            ];
        }
        $facturaModelo->setDetalles($detalles);

        $nro_fact = $facturaModelo->registrarFactura();

        if (!$nro_fact || $nro_fact == 0) {
            throw new Exception("No se pudo obtener el ID de la factura creada. ID: " . $nro_fact);
        }

        sendJsonResponse(true, "Factura registrada exitosamente", "Factura #" . $nro_fact . " creada para el cliente " . $_POST['rif'], ['nro_fact' => $nro_fact]);

    } catch (Exception $e) {
        error_log("ERROR en guardar_factura: " . $e->getMessage());
        sendJsonResponse(false, "Error al registrar la factura", $e->getMessage());
    }
    break;

    case 'anular':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $nro_fact = $_POST['id'] ?? null;
        if ($nro_fact) {
            try {
                if ($facturaModelo->anularFactura($nro_fact)) {
                    sendJsonResponse(true, "Factura anulada exitosamente", "La factura #" . $nro_fact . " ha sido anulada");
                } else {
                    throw new Exception("No se pudo anular la factura");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al anular la factura", $e->getMessage());
            }
        } else {
            sendJsonResponse(false, "Error", "Número de factura no proporcionado");
        }
        break;

    case 'reactivar':
        if (!isAjaxRequest()) {
            sendJsonResponse(false, "Acceso no permitido");
        }
        
        $nro_fact = $_POST['id'] ?? null;
        if ($nro_fact) {
            try {
                if ($facturaModelo->reactivarFactura($nro_fact)) {
                    sendJsonResponse(true, "Factura reactivada exitosamente", "La factura #" . $nro_fact . " ha sido reactivada");
                } else {
                    throw new Exception("No se pudo reactivar la factura");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al reactivar la factura", $e->getMessage());
            }
        } else {
            sendJsonResponse(false, "Error", "Número de factura no proporcionado");
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

    case 'verificarStockAjax':
        if (isAjaxRequest() && isset($_POST['id_inv']) && isset($_POST['cantidad'])) {
            $id_inv = $_POST['id_inv'];
            $cantidad = $_POST['cantidad'];
            
            $stockDisponible = $facturaModelo->verificarStockDisponible($id_inv, $cantidad);
            
            header('Content-Type: application/json');
            echo json_encode(['stock_disponible' => $stockDisponible]);
            exit;
        }
        break;

    case 'obtenerDetallesFacturaAjax':
        if (isAjaxRequest() && isset($_GET['nro_fact'])) {
            $nro_fact = $_GET['nro_fact'];
            
            try {
                if (empty($nro_fact) || !is_numeric($nro_fact)) {
                    throw new Exception("Número de factura inválido");
                }
                
                $facturaData = $facturaModelo->obtenerDetallesFacturaCompleta($nro_fact);
                
                if ($facturaData) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'data' => $facturaData
                    ]);
                } else {
                    throw new Exception("Factura #{$nro_fact} no encontrada en el sistema");
                }
            } catch (Exception $e) {
                error_log("Error en obtenerDetallesFacturaAjax: " . $e->getMessage());
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
            
            $precio = $facturaModelo->obtenerPrecioProducto($id_inv, $cantidad);
            
            header('Content-Type: application/json');
            echo json_encode(['precio' => $precio]);
            exit;
        }
        break;

    case 'imprimir':
        $nro_fact = $_GET['id'] ?? null;
        if ($nro_fact) {
            try {
                $facturaData = $facturaModelo->obtenerDetallesFacturaCompleta($nro_fact);
                
                if (!$facturaData) {
                    throw new Exception("Factura no encontrada");
                }
                
                require_once 'modelo/FacturaPDFModelo.php';
            
                $logoPath = 'assets/images/logo2.png';     
                $firmaPath = 'assets/images/firma.jpeg';   

                if (!file_exists($logoPath)) {
                    $logoPath = '';
                    error_log("Logo no encontrado en: " . realpath($logoPath));
                }
                if (!file_exists($firmaPath)) {
                    $firmaPath = '';
                    error_log("Firma no encontrada en: " . realpath($firmaPath));
                }
                
                $pdf = new FacturaPDFModelo($facturaData, $logoPath, $firmaPath);
                $pdf->generarPDF();
                
                $pdf->Output('I', 'factura_' . $nro_fact . '.pdf');
                exit;
                
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al generar el PDF", $e->getMessage());
            }
        }
        break;
}
?>