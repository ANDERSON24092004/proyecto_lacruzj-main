<?php
require_once 'modelo/ClienteModelo.php';
require_once 'controlador/verificar_sesion.php';

function isAjaxRequest() {
    return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

$clienteModel = new ClienteModel();

switch ($metodo) {
    case 'index':
        $cliente = $clienteModel->listar();
        
        if (isset($_GET['editar']) && isset($_GET['id'])) {
            $id = $_GET['id'];
            $clienteActual = $clienteModel->obtener($id);
        }
        
        require 'vista/clientes/index.php';
        break;

    case 'crear':
        require 'vista/clientes/crear.php';
        break;

    

    case 'editar':
        $id = $_GET['id'] ?? null;
        if (!$id) {
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'RIF no proporcionado']);
                exit;
            }
            header("Location: index.php?c=ClienteControlador&m=index");
            exit;
        }
        
        $clienteActual = $clienteModel->obtener($id);
        if (!$clienteActual) {
            if (isAjaxRequest()) {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'Cliente no encontrado']);
                exit;
            }
            $_SESSION['tipo_mensaje'] = "error";
            $_SESSION['mensaje'] = "Cliente no encontrado";
            header("Location: index.php?c=ClienteControlador&m=index");
            exit;
        }
        
        if (isAjaxRequest()) {
            header('Content-Type: application/json');
            echo json_encode($clienteActual);
            exit;
        }
        
        require 'vista/clientes/index.php';
        break;

    case 'guardarAjax':
    header('Content-Type: application/json');
    
    $errores = $clienteModel->validarDatosCliente($_POST);
    
    if (!empty($errores)) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en el formulario',
            'errors' => $errores
        ]);
        exit;
    }

    $fecha_registro = date("Y-m-d");

    $clienteModel->setRif($_POST['rif']);
    $clienteModel->setRazonSocial($_POST['razon_social']);
    $clienteModel->setTelefono($_POST['telefono']);
    $clienteModel->setCorreo($_POST['email']);
    $clienteModel->setDireccion($_POST['direccion']);
    $clienteModel->setFechaRegistro($fecha_registro);

    if ($clienteModel->registrarCliente()) {
        echo json_encode([
            'success' => true,
            'message' => 'Cliente registrado correctamente',
            'data' => [
                'rif' => $_POST['rif'],
                'razon_social' => $_POST['razon_social']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al registrar el cliente'
        ]);
    }
    exit;
    break;

    case 'actualizarAjax':
    header('Content-Type: application/json');
    
    $rifCliente = $_POST['rif'] ?? null;
    if (!$rifCliente) {
        echo json_encode([
            'success' => false,
            'message' => 'RIF no proporcionado'
        ]);
        exit;
    }

    $errores = $clienteModel->validarDatosActualizacion($_POST);
    
    if (!empty($errores)) {
        echo json_encode([
            'success' => false,
            'message' => 'Error en el formulario',
            'errors' => $errores
        ]);
        exit;
    }

    $datosCliente = [
        'razon_social' => $_POST['razon_social'],
        'telefono' => $_POST['telefono'],
        'correo' => $_POST['email'],
        'direccion' => $_POST['direccion']
    ];
    
    if ($clienteModel->editarCliente($rifCliente, $datosCliente)) {
        echo json_encode([
            'success' => true,
            'message' => 'Cliente actualizado correctamente',
            'data' => [
                'rif' => $rifCliente,
                'razon_social' => $_POST['razon_social']
            ]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al actualizar el cliente'
        ]);
    }
    exit;
    break;

    case 'eliminarAjax':
    header('Content-Type: application/json');
    
    $rif = $_GET['id'] ?? null;
    if (!$rif) {
        echo json_encode([
            'success' => false,
            'message' => 'RIF no proporcionado'
        ]);
        exit;
    }

    if ($clienteModel->tieneFacturas($rif)) {
        echo json_encode([
            'success' => false,
            'message' => 'No se puede eliminar el cliente porque tiene facturas asociadas'
        ]);
        exit;
    }

    if ($clienteModel->eliminar($rif)) {
        echo json_encode([
            'success' => true,
            'message' => 'Cliente eliminado correctamente',
            'data' => ['rif' => $rif]
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Error al eliminar el cliente'
        ]);
    }
    exit;
    break;

    case 'obtenerClienteAjax':
        $rif = $_GET['rif'] ?? null;
        if (!$rif) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'RIF no proporcionado']);
            exit;
        }
        
        $cliente = $clienteModel->obtener($rif);
        if (!$cliente) {
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Cliente no encontrado']);
            exit;
        }
        
        header('Content-Type: application/json');
        echo json_encode($cliente);
        exit;
        break;

    case 'obtenerTodosRifs':
        $rifs = $clienteModel->obtenerTodosRifs();
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'rifs' => $rifs]);
        exit;
        break;

    case 'listarAjax':
    header('Content-Type: application/json');
    
    $clientes = $clienteModel->listar();
    
    if (empty($clientes)) {
        echo json_encode([
            'success' => true,
            'html' => '',
            'count' => 0
        ]);
        exit;
    }

    $html = '';
    foreach ($clientes as $cli) {
        $html .= '<tr>';
        $html .= '<td>J-' . htmlspecialchars($cli['rif']) . '</td>';
        $html .= '<td>' . htmlspecialchars($cli['razon_social']) . '</td>';
        $html .= '<td>' . htmlspecialchars($cli['correo']) . '</td>';
        $html .= '<td>' . htmlspecialchars($cli['telefono']) . '</td>';
        $html .= '<td>' . htmlspecialchars($cli['direccion']) . '</td>';
        $html .= '<td>' . htmlspecialchars($cli['fecha_registro']) . '</td>';
        $html .= '<td>';
        $html .= '<button type="button" class="btn btn-primary btn-sm btn-editar-cliente" data-bs-toggle="modal" data-bs-target="#editarClienteModal" data-rif="' . $cli['rif'] . '">';
        $html .= '<i class="fas fa-edit"></i>';
        $html .= '</button> ';
        $html .= '<button type="button" class="btn btn-danger btn-sm btn-eliminar-cliente" data-id="' . $cli['rif'] . '" data-bs-toggle="modal" data-bs-target="#confirmarEliminarModal">';
        $html .= '<i class="fas fa-trash-alt"></i>';
        $html .= '</button>';
        $html .= '</td>';
        $html .= '</tr>';
    }

    echo json_encode([
        'success' => true,
        'html' => $html,
        'count' => count($clientes)
    ]);
    exit;
    break;

    default:
        header("Location: index.php?c=ClienteControlador&m=index");
        exit;
}
?>