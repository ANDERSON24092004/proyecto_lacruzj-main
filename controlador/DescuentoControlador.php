<?php
require_once 'modelo/DescuentoModelo.php';

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

$descuento = new Descuento();
$activeTab = $_GET['tab'] ?? $_POST['tab'] ?? 'descuento';

switch ($metodo) {
    case 'index':
        $descuentos = $descuento->listar();
        require 'vista/configuracion/index.php';
        break;
        
    case 'crear':
        require 'vista/configuracion/index.php';
        break;
        
    case 'listarAjax':
        if (isAjaxRequest()) {
            try {
                $descuentos = $descuento->listar();
                sendJsonResponse(true, "Datos cargados", "", $descuentos);
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al cargar descuentos", $e->getMessage());
            }
        }
        break;

    case 'obtenerDescuentoAjax':
        if (isAjaxRequest() && isset($_GET['id'])) {
            $id = $_GET['id'];
            $descuentoData = $descuento->buscarPorId($id);
            if ($descuentoData) {
                header('Content-Type: application/json');
                echo json_encode($descuentoData);
            } else {
                header('Content-Type: application/json');
                echo json_encode(null);
            }
            exit;
        }
        break;

    case 'guardarAjax':
        if (isAjaxRequest()) {
            try {
                if (empty($_POST['porcentaje'])) {
                    sendJsonResponse(false, "Error en el registro", "El porcentaje es obligatorio");
                }

                $porcentaje = trim($_POST['porcentaje']);
                
                if (!is_numeric($porcentaje) || $porcentaje < 0 || $porcentaje > 100) {
                    sendJsonResponse(false, "Error en el registro", "El porcentaje debe ser un número entre 0 y 100");
                }

                $descuento->setPorcentaje($porcentaje);

                if ($descuento->registrar()) {
                    sendJsonResponse(true, "Descuento registrado correctamente", "Se registró el descuento: " . $porcentaje . "%");
                } else {
                    sendJsonResponse(false, "Error en el registro", "No se pudo registrar el descuento");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en el registro", $e->getMessage());
            }
        }
        break;

    case 'actualizarAjax':
        if (isAjaxRequest()) {
            try {
                $id = $_POST['id'] ?? null;
                if (!$id) {
                    sendJsonResponse(false, "Error en la actualización", "ID no proporcionado");
                }

                if (empty($_POST['porcentaje'])) {
                    sendJsonResponse(false, "Error en la actualización", "El porcentaje es obligatorio");
                }

                $porcentaje = trim($_POST['porcentaje']);
                
                if (!is_numeric($porcentaje) || $porcentaje < 0 || $porcentaje > 100) {
                    sendJsonResponse(false, "Error en la actualización", "El porcentaje debe ser un número entre 0 y 100");
                }

                $descuento->setIdDescuento($id);
                $descuento->setPorcentaje($porcentaje);

                if ($descuento->actualizar()) {
                    sendJsonResponse(true, "Descuento actualizado correctamente", "Se actualizó el descuento: " . $porcentaje . "%");
                } else {
                    sendJsonResponse(false, "Error en la actualización", "No se pudo actualizar el descuento");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la actualización", $e->getMessage());
            }
        }
        break;

    case 'eliminarAjax':
        if (isAjaxRequest()) {
            $id = $_POST['id'] ?? null;
            
            if (!$id) {
                sendJsonResponse(false, "Error en la eliminación", "ID de descuento no proporcionado");
            }

            try {
                if ($descuento->tieneFacturas($id)) {
                    sendJsonResponse(false, "Error en la eliminación", "No se puede eliminar el descuento porque está en uso por alguna factura");
                }

                $descuento->setIdDescuento($id);
                
                if ($descuento->eliminar()) {
                    sendJsonResponse(true, "Descuento eliminado correctamente", "Se eliminó el descuento con ID " . $id);
                } else {
                    sendJsonResponse(false, "Error en la eliminación", "No se pudo eliminar el descuento");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la eliminación", $e->getMessage());
            }
        }
    break;
        
    default:
        $descuentos = $descuento->listar();
        require 'vista/configuracion/index.php';
    break;
}
?>