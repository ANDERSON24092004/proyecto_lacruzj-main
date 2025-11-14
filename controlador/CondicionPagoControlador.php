<?php
require_once 'modelo/CondicionPagoModelo.php';

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

$condicionPago = new CondicionPago();
$activeTab = $_GET['tab'] ?? $_POST['tab'] ?? 'condicion-pago';

switch ($metodo) {
    case 'index':
        $condicion = $condicionPago->listar();
        require 'vista/configuracion/index.php';
        break;
        
    case 'crear':
        require 'vista/configuracion/index.php';
        break;
        
    case 'listarAjax':
        if (isAjaxRequest()) {
            try {
                $condiciones = $condicionPago->listar();
                sendJsonResponse(true, "Datos cargados", "", $condiciones);
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al cargar condiciones de pago", $e->getMessage());
            }
        }
        break;

    case 'obtenerCondicionAjax':
        if (isAjaxRequest() && isset($_GET['id_condicion_pago'])) {
            $id = $_GET['id_condicion_pago'];
            $condicionData = $condicionPago->buscarPorId($id);
            if ($condicionData) {
                header('Content-Type: application/json');
                echo json_encode($condicionData);
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
                if (empty($_POST['forma'])) {
                    sendJsonResponse(false, "Error en el registro", "La forma de pago es obligatoria");
                }

                $forma = trim($_POST['forma']);
                $condicionPago->setForma($forma);

                if ($condicionPago->registrar()) {
                    sendJsonResponse(true, "Condición de pago registrada correctamente", "Se registró la condición de pago: " . $forma);
                } else {
                    sendJsonResponse(false, "Error en el registro", "No se pudo registrar la condición de pago");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en el registro", $e->getMessage());
            }
        }
        break;

    case 'actualizarAjax':
        if (isAjaxRequest()) {
            try {
                $id = $_POST['id_condicion_pago'] ?? null;
                if (!$id) {
                    sendJsonResponse(false, "Error en la actualización", "ID no proporcionado");
                }

                if (empty($_POST['forma'])) {
                    sendJsonResponse(false, "Error en la actualización", "La forma de pago es obligatoria");
                }

                $forma = trim($_POST['forma']);
                $condicionPago->setIdCondicionPago($id);
                $condicionPago->setForma($forma);

                if ($condicionPago->actualizar()) {
                    sendJsonResponse(true, "Condición de pago actualizada correctamente", "Se actualizó la condición de pago: " . $forma);
                } else {
                    sendJsonResponse(false, "Error en la actualización", "No se pudo actualizar la condición de pago");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la actualización", $e->getMessage());
            }
        }
        break;

    case 'eliminarAjax':
        if (isAjaxRequest()) {
            $id_condicion_pago = $_POST['id_condicion_pago'] ?? null;
            
            if (!$id_condicion_pago) {
                sendJsonResponse(false, "Error en la eliminación", "ID de condición de pago no proporcionado");
            }

            try {
                if ($condicionPago->tieneFacturas($id_condicion_pago)) {
                    sendJsonResponse(false, "Error en la eliminación", "No se puede eliminar la condición de pago porque está en uso por alguna factura");
                }

                $condicionPago->setIdCondicionPago($id_condicion_pago);
                
                if ($condicionPago->eliminar()) {
                    sendJsonResponse(true, "Condición de pago eliminada correctamente", "Se eliminó la condición de pago con ID " . $id_condicion_pago);
                } else {
                    sendJsonResponse(false, "Error en la eliminación", "No se pudo eliminar la condición de pago");
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la eliminación", $e->getMessage());
            }
        }
    break;
}
?>