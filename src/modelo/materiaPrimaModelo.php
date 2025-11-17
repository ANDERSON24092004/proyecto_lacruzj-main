<?php
namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;
use Exception;

class materiaPrimaModelo extends conexion
{
    private $id_materia;
    private $id_unidad_medida;
    private $nombre;
    private $stock;
    private $costo;
    private $status;

    public function getIdMateria() {
        return $this->id_materia;
    }

    public function setIdMateria($id_materia) {
        $this->id_materia = $id_materia;
        return $this;
    }

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

    public function getStock() {
        return $this->stock;
    }

    public function setStock($stock) {
        $this->stock = $stock;
        return $this;
    }

    public function getCosto() {
        return $this->costo;
    }

    public function setCosto($costo) {
        $this->costo = $costo;
        return $this;
    }

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
        return $this;
    }

    private function validarDatos($esCreacion = true) {
        $errores = [];

        if (empty(trim($this->nombre))) {
            $errores[] = "El nombre es obligatorio";
        }

        if (empty($this->id_unidad_medida)) {
            $errores[] = "La unidad de medida es obligatoria";
        }

        if ($this->stock === null || $this->stock < 0) {
            $errores[] = "El stock debe ser un número mayor o igual a 0";
        }

        if ($this->costo === null || $this->costo <= 0) {
            $errores[] = "El costo debe ser mayor a 0";
        }

        return $errores;
    }

    public function crear() {
        try {
            $errores = $this->validarDatos(true);
            if (!empty($errores)) {
                throw new Exception(implode("; ", $errores));
            }

            $sql = "INSERT INTO inv_materia_prima (nombre, id_unidad_medida, stock, costo, status) 
                   VALUES (?, ?, ?, ?, 0)"; 
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([
                $this->nombre,
                $this->id_unidad_medida,
                $this->stock,
                $this->costo
            ]);

            if ($result) {
                $this->id_materia = $this->pdo->lastInsertId();
                return true;
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en MateriaPrima->crear(): " . $e->getMessage());
            throw new Exception("Error al registrar la materia prima en la base de datos");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function actualizar() {
        try {
            $errores = $this->validarDatos(false);
            if (!empty($errores)) {
                throw new Exception(implode("; ", $errores));
            }

            $materiaActual = $this->buscarPorId($this->id_materia);
            if (!$materiaActual) {
                throw new Exception("Materia prima no encontrada");
            }

            $sql = "UPDATE inv_materia_prima 
                   SET nombre = ?, id_unidad_medida = ?, stock = ?, costo = ?
                   WHERE id_materia = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                $this->nombre,
                $this->id_unidad_medida,
                $this->stock,
                $this->costo,
                $this->id_materia
            ]);
        } catch (PDOException $e) {
            error_log("Error en MateriaPrima->actualizar(): " . $e->getMessage());
            throw new Exception("Error al actualizar la materia prima en la base de datos");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function listar() {
        try {
            $sql = "SELECT 
                        m.id_materia,
                        m.nombre,
                        m.stock,
                        m.costo,
                        um.nombre AS unidad_medida
                    FROM inv_materia_prima m
                    LEFT JOIN unidades_medida um ON m.id_unidad_medida = um.id_unidad_medida 
                    WHERE m.status = 0
                    ORDER BY m.nombre DESC";
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $result;
        } catch (PDOException $e) {
            error_log('Error en MateriaPrima->listar(): ' . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT m.*, um.nombre AS unidad_medida
                   FROM inv_materia_prima m
                   LEFT JOIN unidades_medida um ON m.id_unidad_medida = um.id_unidad_medida
                   WHERE m.id_materia = ? AND m.status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                throw new Exception("Materia prima no encontrada");
            }
            
            return $result;
        } catch (PDOException $e) {
            error_log("Error en MateriaPrima->buscarPorId(): " . $e->getMessage());
            throw new Exception("Error al buscar la materia prima en la base de datos");
        }
    }

    public function eliminar() {
        try {   
            $sql = "UPDATE inv_materia_prima SET status = 1 WHERE id_materia = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$this->id_materia]);
        } catch (PDOException $e) {
            error_log("Error en MateriaPrima->eliminar(): " . $e->getMessage());
            throw new Exception("Error al eliminar la materia prima de la base de datos");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function crearDesdeFactura($nombre, $id_unidad_medida, $costo) {
        try {
            if (empty(trim($nombre))) {
                throw new Exception("El nombre es requerido");
            }

            if (empty($id_unidad_medida)) {
                throw new Exception("La unidad de medida es requerida");
            }

            if ($costo <= 0) {
                throw new Exception("El costo debe ser mayor a 0");
            }

            $sql = "INSERT INTO inv_materia_prima (nombre, id_unidad_medida, stock, costo) 
                   VALUES (?, ?, 0, ?)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$nombre, $id_unidad_medida, $costo]);

            if ($result) {
                $id_materia = $this->pdo->lastInsertId();
                
                $sql_select = "SELECT m.*, um.nombre as unidad_medida 
                              FROM inv_materia_prima m
                              LEFT JOIN unidades_medida um ON m.id_unidad_medida = um.id_unidad_medida
                              WHERE m.id_materia = ?";
                $stmt_select = $this->pdo->prepare($sql_select);
                $stmt_select->execute([$id_materia]);
                
                return $stmt_select->fetch(PDO::FETCH_ASSOC);
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en MateriaPrima->crearDesdeFactura(): " . $e->getMessage());
            throw new Exception("Error al crear la materia prima en la base de datos");
        } catch (Exception $e) {
            throw $e;
        }
    }
}
?>