<?php
namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;

class presentacionModelo extends conexion {
    private $id_pres;
    private $nombre;

    public function getIdPresentacion() {
        return $this->id_pres;
    }

    public function setIdPresentacion($id_pres) {
        $this->id_pres = $id_pres;
        return $this;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
        return $this;
    }

    public function listar() {
        try {
            $sql = "SELECT * FROM tipo_presentacion WHERE status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Presentacion->listar(): " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM tipo_presentacion WHERE id_pres = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Presentacion->buscarPorId(): " . $e->getMessage());
            return false;
        }
    }

    public function buscarIdPorNombre($nombre) {
        try {
            $sql = "SELECT id_pres FROM tipo_presentacion WHERE nombre = ? AND status = 0 LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nombre]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id_pres'] : null;
        } catch (PDOException $e) {
            error_log("Error en Presentacion->buscarIdPorNombre(): " . $e->getMessage());
            return null;
        }
    }

    public function buscarPorPorcentaje($nombre) {
        try {
            $sql = "SELECT id_pres FROM tipo_presentacion WHERE nombre = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$nombre]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ? $result['id_pres'] : null;
        } catch (PDOException $e) {
            error_log("Error en Presentacion->buscarPorPorcentaje(): " . $e->getMessage());
            return null;
        }
    }

    public function tieneProductos($id_pres) {
        try {
            $sql = "SELECT COUNT(*) as count FROM inv_prod_serv WHERE id_pres = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id_pres]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error en Presentacion->tieneProductos(): " . $e->getMessage());
            return true; 
        }
    }

    public function registrar() {
        try {
            $sql = "INSERT INTO tipo_presentacion (nombre, status) VALUES (:nombre, 0)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Presentacion->registrar(): " . $e->getMessage());
            return false;
        }
    }

    public function actualizar() {
        try {
            $sql = "UPDATE tipo_presentacion SET nombre = :nombre WHERE id_pres = :id_pres AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id_pres', $this->id_pres, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Presentacion->actualizar(): " . $e->getMessage());
            return false;
        }
    }

    public function eliminar() {
        try {
            $sql = "UPDATE tipo_presentacion SET status = 1 WHERE id_pres = :id_pres AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id_pres', $this->id_pres, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Presentacion->eliminar(): " . $e->getMessage());
            return false;
        }
    }
}
?>