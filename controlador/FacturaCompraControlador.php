<?php
require_once 'modelo/FacturaCompraModelo.php';
require_once 'modelo/DetalleFacturaCompraModelo.php';
require_once 'modelo/ProveedorModelo.php';
require_once 'modelo/MateriaPrimaModelo.php';
require_once 'modelo/UnidadMedidaModelo.php';
require_once 'controlador/verificar_sesion.php';

function sendJsonResponse($success, $message, $message_detail = '', $data = []) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'mensaje' => $message,
        'mensaje_detalle' => $message_detail,
        'data' => $data
    ]);
    exit;
}

$facturaCompra = new FacturaCompra();
$detalleFacturaCompra = new DetalleFacturaCompra();
$proveedor = new Proveedor();
$materiaPrima = new MateriaPrima();
$unidadMedida = new UnidadMedida();

switch ($metodo) {
    case 'index':
        $proveedores = $proveedor->listar();
        $materiasPrimas = $materiaPrima->listar();
        $unidadesMedida = $unidadMedida->listar();
        
        require 'vista/facturaCompra/index.php';
        break;

    case 'listar':
        try {
            $facturas = $facturaCompra->listar();
            sendJsonResponse(true, 'Facturas cargadas correctamente', '', ['facturas' => $facturas]);
        } catch (Exception $e) {
            sendJsonResponse(false, 'Error al cargar las facturas', $e->getMessage());
        }
        break;

    case 'registrar':
        try {
            if (empty($_POST['detalles']) || !is_array($_POST['detalles'])) {
                throw new Exception("No se han agregado detalles a la factura");
            }

            $subtotal = 0;
            $total_iva = 0;
            $total_materias_primas = count($_POST['detalles']);
            $total_items = 0;

            foreach ($_POST['detalles'] as $detalle) {
                if (!isset($detalle['cantidad']) || !isset($detalle['costo'])) {
                    throw new Exception("Datos de detalle incompletos");
                }
                $item_subtotal = floatval($detalle['cantidad']) * floatval($detalle['costo']);
                $subtotal += $item_subtotal;
                $total_items += intval($detalle['cantidad']);
            }

            $total_iva = $subtotal * 0.16;
            $total_general = $subtotal + $total_iva;

            if (empty($_POST['id_proveedor'])) {
                throw new Exception("Debe seleccionar un proveedor");
            }
            if (empty($_POST['num_factura'])) {
                throw new Exception("El número de factura es obligatorio");
            }
            if (empty($_POST['fecha'])) {
                throw new Exception("La fecha es obligatoria");
            }

            $facturaCompra->setIdProveedor($_POST['id_proveedor']);
            $facturaCompra->setNum_factura($_POST['num_factura']);
            $facturaCompra->setTotalIva($total_iva);
            $facturaCompra->setTotalGeneral($total_general);
            $facturaCompra->setFecha($_POST['fecha']);

            $id_fact_com = $facturaCompra->registrar($_POST['detalles']);

            if ($id_fact_com) {
                $detalles = [];
                foreach ($_POST['detalles'] as $detalle) {
                    $detalles[] = [
                        'id_materia_prima' => $detalle['id_materia_prima'],
                        'id_fact_com' => $id_fact_com,
                        'cantidad' => $detalle['cantidad'],
                        'costo' => $detalle['costo']
                    ];
                }

                if ($detalleFacturaCompra->registrarDetalle($detalles)) {
                    sendJsonResponse(
                        true, 
                        "Factura de compra registrada correctamente", 
                        "Factura #" . $_POST['num_factura'] . " - " . $total_materias_primas . " materia(s) prima(s) - " . $total_items . " unidad(es) totales",
                        ['id_fact_com' => $id_fact_com]
                    );
                } else {
                    throw new Exception("Error al registrar los detalles de la factura");
                }
            } else {
                throw new Exception("No se pudo obtener el ID de la factura registrada");
            }
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al registrar la factura de compra", $e->getMessage());
        }
        break;

    case 'anular':
        $id = $_POST['id'] ?? null;
        if (!$id) {
            sendJsonResponse(false, "Error al anular la factura", "ID de factura no proporcionado");
        }

        try {
            $factura = $facturaCompra->buscarPorId($id);
            if (!$factura) {
                throw new Exception("Factura no encontrada");
            }

            if ($facturaCompra->anular($id)) {
                sendJsonResponse(
                    true, 
                    "Factura anulada correctamente", 
                    "La factura #" . $factura['num_factura'] . " ha sido anulada"
                );
            } else {
                throw new Exception("No se pudo anular la factura");
            }
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al anular la factura", $e->getMessage());
        }
        break;

    case 'reactivar':
        $id = $_POST['id'] ?? null;
        if (!$id) {
            sendJsonResponse(false, "Error al reactivar la factura", "ID de factura no proporcionado");
        }

        try {
            $factura = $facturaCompra->buscarPorId($id);
            if (!$factura) {
                throw new Exception("Factura no encontrada");
            }

            if ($facturaCompra->reactivar($id)) {
                sendJsonResponse(
                    true, 
                    "Factura reactivada correctamente", 
                    "La factura #" . $factura['num_factura'] . " ha sido reactivada"
                );
            } else {
                throw new Exception("No se pudo reactivar la factura");
            }
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al reactivar la factura", $e->getMessage());
        }
        break;

    case 'verDetalle':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            sendJsonResponse(false, "Error al cargar el detalle", "ID no proporcionado");
        }

        try {
            $factura = $facturaCompra->buscarPorId($id);
            if (!$factura) {
                throw new Exception("Factura no encontrada");
            }
            
            $detalles = $facturaCompra->obtenerDetallePorFactura($id);
            
            sendJsonResponse(
                true, 
                "Detalle cargado correctamente", 
                "", 
                [
                    'factura' => $factura,
                    'detalles' => $detalles
                ]
            );
        } catch (Exception $e) {
            sendJsonResponse(false, "Error al cargar los detalles", $e->getMessage());
        }
        break;

    default:
        header("Location: index.php?c=FacturaCompraControlador&m=index");
        exit;
}
?>