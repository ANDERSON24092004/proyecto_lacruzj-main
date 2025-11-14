<?php
require_once 'modelo/conexion.php';

class Iva extends Conexion {
    private $id_iva;
    private $porcentaje;

    public function getIdIva() {
        return $this->id_iva;
    }

    public function setIdIva($id_iva) {
        $this->id_iva = $id_iva;
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
            $sql = "SELECT * FROM iva WHERE status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Iva->listar(): " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM iva WHERE id_iva = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Iva->buscarPorId(): " . $e->getMessage());
            return false;
        }
    }

    public function registrar() {
        try {
            $sql = "INSERT INTO iva (porcentaje, status) VALUES (:porcentaje,0)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':porcentaje', $this->porcentaje, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Iva->registrar(): " . $e->getMessage());
            return false;
        }
    }

    public function actualizar() {
        try {
            $sql = "UPDATE iva SET porcentaje = :porcentaje WHERE id_iva = :id_iva AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':porcentaje', $this->porcentaje, PDO::PARAM_INT);
            $stmt->bindParam(':id_iva', $this->id_iva, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Iva->actualizar(): " . $e->getMessage());
            return false;
        }
    }

    public function eliminar() {
        try {
            $sql = "UPDATE iva SET status = 1 WHERE id_iva = :id_iva AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_iva', $this->id_iva, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Iva->eliminar(): " . $e->getMessage());
            return false;
        }
    }
}
?>