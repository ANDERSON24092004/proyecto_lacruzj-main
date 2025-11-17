<?php

namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;
use Exception;
class clienteModelo extends conexion {

    private $rif;
    private $razon_social;
    private $telefono;
    private $correo;
    private $direccion;
    private $fecha_registro;
    private $status;

    public function setRif($rif) { $this->rif = $rif; }
    public function getRif() { return $this->rif; }

    public function setRazonSocial($razon_social) { $this->razon_social = $razon_social; }
    public function getRazonSocial() { return $this->razon_social; }

    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function getTelefono() { return $this->telefono; }

    public function setCorreo($correo) { $this->correo = $correo; }
    public function getCorreo() { return $this->correo; }

    public function setDireccion($direccion) { $this->direccion = $direccion; }
    public function getDireccion() { return $this->direccion; }

    public function setFechaRegistro($fecha_registro) { $this->fecha_registro = $fecha_registro; }
    public function getFechaRegistro() { return $this->fecha_registro; }
    public function setStatus($status) { $this->status = $status; }
    public function getStatus() { return $this->status; }

    public function validarDatosCliente($datos) {
        $errores = [];

        if (empty($datos['rif'])) {
            $errores[] = "El RIF es obligatorio";
        } elseif ($this->rifExiste($datos['rif'])) {
            $errores[] = "El RIF ya existe en el sistema";
        }

        if (empty($datos['razon_social'])) {
            $errores[] = "La razón social es obligatoria";
        }

        if (empty($datos['telefono'])) {
            $errores[] = "El teléfono es obligatorio";
        }

        if (empty($datos['email'])) {
            $errores[] = "El correo electrónico es obligatorio";
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del correo electrónico no es válido";
        }

        if (empty($datos['direccion'])) {
            $errores[] = "La dirección es obligatoria";
        }

        return $errores;
    }

    public function validarDatosActualizacion($datos) {
        $errores = [];

        if (empty($datos['razon_social'])) {
            $errores[] = "La razón social es obligatoria";
        }

        if (empty($datos['telefono'])) {
            $errores[] = "El teléfono es obligatorio";
        }

        if (empty($datos['email'])) {
            $errores[] = "El correo electrónico es obligatorio";
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del correo electrónico no es válido";
        }

        if (empty($datos['direccion'])) {
            $errores[] = "La dirección es obligatoria";
        }

        return $errores;
    }

    public function registrarCliente() {
        $sql = "INSERT INTO cliente (rif, razon_social, telefono, correo, direccion, fecha_registro, status) 
                VALUES (?, ?, ?, ?, ?, ?, 0)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([
            $this->rif,
            $this->razon_social,
            $this->telefono,
            $this->correo,
            $this->direccion,
            $this->fecha_registro,
        ]);
        return $result;
    }

    public function listar() {
        $sql = "SELECT * FROM cliente WHERE status = 0 ORDER BY fecha_registro DESC";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtener($rif) {
        $sql = "SELECT * FROM cliente WHERE rif = ? AND status = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$rif]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function editarCliente($rifCliente, $datosCliente) {
        try {
            $this->pdo->beginTransaction(); 

            $sqlCliente = "UPDATE cliente
                           SET razon_social = ?, telefono = ?, correo = ?, direccion = ?
                           WHERE rif = ? AND status = 0";
            $stmtCliente = $this->pdo->prepare($sqlCliente);
            $stmtCliente->execute([
                $datosCliente['razon_social'],
                $datosCliente['telefono'],
                $datosCliente['correo'],
                $datosCliente['direccion'],
                $rifCliente
            ]);

            $this->pdo->commit(); 
            return true; 
        } catch (Exception $e) {
            $this->pdo->rollBack(); 
            error_log("Error al editar cliente: " . $e->getMessage()); 
            return false; 
        }
    }

    public function obtenerpresupuestos($rif) {
        $sql = "SELECT p.nro_fact, p.fecha, p.total_general, p.estatus
                FROM factura_venta p
                WHERE p.rif = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$rif]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function tieneFacturas($rif) {
        $sql = "SELECT COUNT(*) as count FROM factura_venta WHERE rif = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$rif]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function rifExiste($rif) {
        $sql = "SELECT COUNT(*) as count FROM cliente WHERE rif = ? AND status = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$rif]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    public function obtenerTodosRifs() {
        $sql = "SELECT rif FROM cliente WHERE status = 0";
        $stmt = $this->pdo->query($sql);
        $resultados = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return $resultados;
    }

    public function eliminar($rif) {
        try {
            $sql = "UPDATE cliente SET status = 1 WHERE rif = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$rif]);
        } catch (PDOException $e) {
            error_log("Error en ClienteModel->eliminar(): " . $e->getMessage());
            return false;
        }
    }
}
?>