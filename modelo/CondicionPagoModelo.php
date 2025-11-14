<?php
require_once 'modelo/conexion.php';

class CondicionPago extends Conexion {
    private $id_condicion_pago;
    private $forma;

    public function getIdCondicionPago() {
        return $this->id_condicion_pago;
    }

    public function setIdCondicionPago($id_condicion_pago) {
        $this->id_condicion_pago = $id_condicion_pago;
        return $this;
    }

    public function getForma() {
        return $this->forma;
    }

    public function setForma($forma) {
        $this->forma = $forma;
        return $this;
    }

    public function listar() {
        try {
            $sql = "SELECT * FROM condicion_pago WHERE status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en CondicionPago->listar(): " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM condicion_pago WHERE id_condicion_pago = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en CondicionPago->buscarPorId(): " . $e->getMessage());
            return false;
        }
    }

    public function registrar() {
        try {
            $sql = "INSERT INTO condicion_pago (forma, status) VALUES (:forma, 0)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':forma', $this->forma, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en CondicionPago->registrar(): " . $e->getMessage());
            return false;
        }
    }

    public function actualizar() {
        try {
            $sql = "UPDATE condicion_pago SET forma = :forma WHERE id_condicion_pago = :id AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':forma', $this->forma, PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->id_condicion_pago, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en CondicionPago->actualizar(): " . $e->getMessage());
            return false;
        }
    }

    public function eliminar() {
        try {
            $sql = "UPDATE condicion_pago SET status = 1 WHERE id_condicion_pago = :id AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $this->id_condicion_pago, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en CondicionPago->eliminar(): " . $e->getMessage());
            return false;
        }
    }

    public function tieneFacturas($id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM factura_venta WHERE id_condicion_pago = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en CondicionPago->tieneFacturas(): " . $e->getMessage());
            return true; 
        }
    }
}
?>