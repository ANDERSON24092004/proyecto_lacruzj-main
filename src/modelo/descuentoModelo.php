<?php
namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;


class descuentoModelo extends conexion {
    private $id;
    private $porcentaje;

    public function getIdDescuento() {
        return $this->id;
    }

    public function setIdDescuento($id) {
        $this->id = $id;
        return $this;
    }

    public function getPorcentaje() {
        return $this->porcentaje;
    }

    public function setPorcentaje($porcentaje) {
        $this->porcentaje = $porcentaje;
        return $this;
    }

    public function listar() {
        try {
            $sql = "SELECT * FROM descuento WHERE status = 0 ORDER BY porcentaje ASC ";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Descuento->listar(): " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM descuento WHERE id = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Descuento->buscarPorId(): " . $e->getMessage());
            return false;
        }
    }

    public function buscarPorPorcentaje($porcentaje) {
        try {
            $sql = "SELECT id FROM descuento WHERE porcentaje = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$porcentaje]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id'] : null;
        } catch (PDOException $e) {
            error_log("Error en Descuento->buscarPorPorcentaje(): " . $e->getMessage());
            return null;
        }
    }

    public function registrar() {
        try {
            $sql = "INSERT INTO descuento (porcentaje, status) VALUES (:porcentaje, 0)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':porcentaje', $this->porcentaje, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Descuento->registrar(): " . $e->getMessage());
            return false;
        }
    }

    public function actualizar() {
        try {
            $sql = "UPDATE descuento SET porcentaje = :porcentaje WHERE id = :id AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':porcentaje', $this->porcentaje, PDO::PARAM_INT);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Descuento->actualizar(): " . $e->getMessage());
            return false;
        }
    }

    public function eliminar() {
        try {
            $sql = "UPDATE descuento SET status = 1 WHERE id = :id AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $this->id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Descuento->eliminar(): " . $e->getMessage());
            return false;
        }
    }

    public function tieneFacturas($id) {
    try {
        $sql = "SELECT COUNT(*) as total FROM factura_venta WHERE id_descuento = ? AND status = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] > 0;
    } catch (PDOException $e) {
        error_log("Error en Descuento->tieneFacturas(): " . $e->getMessage());
        return false;
    }
    }
}
?>