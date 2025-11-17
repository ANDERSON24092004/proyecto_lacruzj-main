<?php

use src\modelo\proveedorModelo;

require_once 'src/controlador/verificar_sesion.php';

function isAjaxRequest()
{
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
        strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

function sendJsonResponse($success, $message, $details = '', $data = [])
{
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'details' => $details,
        'data' => $data
    ]);
    exit;
}

$proveedorModelo = new proveedorModelo();

switch ($metodo) {
    case 'index':
        require 'src/vista/proveedores/index.php';
        break;

    case 'listarAjax':
        if (isAjaxRequest()) {
            try {
                $proveedores = $proveedorModelo->listar();
                sendJsonResponse(true, "Datos cargados", "", $proveedores);
            } catch (Exception $e) {
                sendJsonResponse(false, "Error al cargar proveedores", $e->getMessage());
            }
        }
        break;

    case 'obtenerProveedorAjax':
        if (isAjaxRequest() && isset($_GET['id_proveedores'])) {
            $id = $_GET['id_proveedores'];
            $proveedorData = $proveedorModelo->buscarPorId($id);
            if ($proveedorData) {
                header('Content-Type: application/json');
                echo json_encode($proveedorData);
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
                $proveedorModelo->setIdProveedores($_POST['rif']);
                $proveedorModelo->setNombre($_POST['nombre']);
                $proveedorModelo->setTelefono($_POST['telefono']);
                $proveedorModelo->setCorreo($_POST['email']);
                $proveedorModelo->setDireccion($_POST['direccion']);

                if ($proveedorModelo->registrar()) {
                    sendJsonResponse(true, "Proveedor registrado exitosamente", "Se registró el proveedor " . $_POST['nombre'] . " con RIF " . $_POST['rif']);
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en el registro", $e->getMessage());
            }
        }
        break;

    case 'actualizarAjax':
        if (isAjaxRequest()) {
            try {
                $proveedorModelo->setIdProveedores($_POST['id_proveedores']);
                $proveedorModelo->setNombre($_POST['nombre']);
                $proveedorModelo->setTelefono($_POST['telefono']);
                $proveedorModelo->setCorreo($_POST['email']);
                $proveedorModelo->setDireccion($_POST['direccion']);

                if ($proveedorModelo->actualizar()) {
                    sendJsonResponse(true, "Proveedor actualizado exitosamente", "Se editó el proveedor " . $_POST['nombre'] . " con RIF " . $_POST['id_proveedores']);
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la actualización", $e->getMessage());
            }
        }
        break;

    case 'eliminarAjax':
        if (isAjaxRequest()) {
            $id_proveedores = $_POST['id_proveedores'] ?? null;

            if (!$id_proveedores) {
                sendJsonResponse(false, "Error en la eliminación", "ID de proveedor no proporcionado");
            }

            try {
                $proveedorModelo->setIdProveedores($id_proveedores);

                if ($proveedorModelo->eliminar()) {
                    sendJsonResponse(true, "Proveedor eliminado exitosamente", "Se eliminó el proveedor con RIF " . $id_proveedores);
                }
            } catch (Exception $e) {
                sendJsonResponse(false, "Error en la eliminación", $e->getMessage());
            }
        }
        break;

    case 'verificarRifUnico':
        if (isAjaxRequest() && isset($_POST['rif'])) {
            $rif = $_POST['rif'];
            $excluirId = $_POST['excluir_id'] ?? null;

            $existe = $proveedorModelo->existeRif($rif, $excluirId);

            header('Content-Type: application/json');
            echo json_encode(['existe' => $existe]);
            exit;
        }
        break;

    case 'guardar_proveedores':
        try {
            $proveedorModelo->setIdProveedores($_POST['rif']);
            $proveedorModelo->setNombre($_POST['nombre']);
            $proveedorModelo->setTelefono($_POST['telefono']);
            $proveedorModelo->setCorreo($_POST['email']);
            $proveedorModelo->setDireccion($_POST['direccion']);

            if ($proveedorModelo->registrar()) {
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Proveedor registrado exitosamente";
                $_SESSION['mensaje_detalle'] = "Se registró el proveedor " . $_POST['nombre'] . " con RIF " . $_POST['rif'];
            }
        } catch (Exception $e) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error en el registro";
            $_SESSION['mensaje_detalle'] = $e->getMessage();
        }

        header("Location: index.php?c=ProveedorControlador&m=index");
        break;

    case 'actualizar_proveedor':
        try {
            $proveedorModelo->setIdProveedores($_POST['id_proveedores']);
            $proveedorModelo->setNombre($_POST['nombre']);
            $proveedorModelo->setTelefono($_POST['telefono']);
            $proveedorModelo->setCorreo($_POST['email']);
            $proveedorModelo->setDireccion($_POST['direccion']);

            if ($proveedorModelo->actualizar()) {
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Proveedor actualizado exitosamente";
                $_SESSION['mensaje_detalle'] = "Se editó el proveedor " . $_POST['nombre'] . " con RIF " . $_POST['id_proveedores'];
            }
        } catch (Exception $e) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error en la actualización";
            $_SESSION['mensaje_detalle'] = $e->getMessage();
        }

        header("Location: index.php?c=ProveedorControlador&m=index");
        break;

    case 'eliminar':
        $id_proveedores = $_GET['id_proveedores'] ?? $_GET['id'] ?? null;

        if (!$id_proveedores) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error en la eliminación";
            $_SESSION['mensaje_detalle'] = "ID de proveedor no proporcionado";
            header("Location: index.php?c=ProveedorControlador&m=index");
            exit;
        }

        try {
            $proveedorModelo->setIdProveedores($id_proveedores);

            if ($proveedorModelo->eliminar()) {
                $_SESSION['tipo_mensaje'] = "success";
                $_SESSION['mensaje'] = "Proveedor eliminado exitosamente";
                $_SESSION['mensaje_detalle'] = "Se eliminó el proveedor con RIF " . $id_proveedores;
            }
        } catch (Exception $e) {
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Error en la eliminación";
            $_SESSION['mensaje_detalle'] = $e->getMessage();
        }

        header("Location: index.php?c=ProveedorControlador&m=index");
        break;
}
