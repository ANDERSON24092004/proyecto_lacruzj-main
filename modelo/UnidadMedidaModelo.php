<?php
require_once 'modelo/conexion.php';

class UnidadMedida extends Conexion {
    private $id_unidad_medida;
    private $nombre;

    public function getIdUnidadMedida() {
        return $this->id_unidad_medida;
    }

    public function setIdUnidadMedida($id_unidad_medida) {
        $this->id_unidad_medida = $id_unidad_medida;
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
            $sql = "SELECT * FROM unidades_medida WHERE status = 0 ORDER BY nombre ASC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en UnidadMedida->listar(): " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM unidades_medida WHERE id_unidad_medida = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en UnidadMedida->buscarPorId(): " . $e->getMessage());
            return false;
        }
    }

    public function registrar() {
        try {
            $sql = "INSERT INTO unidades_medida (nombre, status) VALUES (:nombre, 0)";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en UnidadMedida->registrar(): " . $e->getMessage());
            return false;
        }
    }

    public function actualizar() {
        try {
            $sql = "UPDATE unidades_medida SET nombre = :nombre WHERE id_unidad_medida = :id AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':id', $this->id_unidad_medida, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en UnidadMedida->actualizar(): " . $e->getMessage());
            return false;
        }
    }

    public function eliminar() {
    try {
        $sql = "UPDATE unidades_medida SET status = 1 WHERE id_unidad_medida = :id AND status = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindParam(':id', $this->id_unidad_medida, PDO::PARAM_INT);
        return $stmt->execute();
        
    } catch (PDOException $e) {
        error_log("Error en UnidadMedida->eliminar(): " . $e->getMessage());
        throw new Exception("Error de base de datos al eliminar la unidad.");
    } catch (Exception $e) {
        error_log("Error de validación en UnidadMedida->eliminar(): " . $e->getMessage());
        throw $e; 
    }
}

    public function existeNombre($nombre, $excluir_id = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM unidades_medida WHERE nombre = :nombre";
            $params = [':nombre' => $nombre];
            
            if ($excluir_id !== null) {
                $sql .= " AND id_unidad_medida != :excluir_id";
                $params[':excluir_id'] = $excluir_id;
            }
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] > 0;
        } catch (PDOException $e) {
            error_log("Error en UnidadMedida->existeNombre(): " . $e->getMessage());
            return false;
        }
    }
}
?>