<?php
require_once 'modelo/conexion.php';

class Proveedor extends Conexion {
    private $id_proveedores;
    private $direccion;
    private $nombre;
    private $telefono;
    private $correo;

    public function getConexion() {
        return $this->pdo;
    }

    public function getIdProveedores() {
        return $this->id_proveedores;
    }

    public function setIdProveedores($id_proveedores) {
        $this->id_proveedores = $id_proveedores;
        return $this;
    }

    public function getDireccion() {
        return $this->direccion;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
        return $this;
    }

    public function getNombre() {
        return $this->nombre;
    }

    public function setNombre($nombre) {
        $this->nombre = $nombre;
        return $this;
    }

    public function getTelefono() {
        return $this->telefono;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
        return $this;
    }

    public function getCorreo() {
        return $this->correo;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
        return $this;
    }

    public function listar() {
        try {
            $sql = "SELECT * FROM proveedores WHERE status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Proveedor->listar(): " . $e->getMessage());
            return [];
        }
    }

    public function buscarPorId($id) {
        try {
            $sql = "SELECT * FROM proveedores
                    WHERE id_proveedores = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en Proveedor->buscarPorId(): " . $e->getMessage());
            return false;
        }
    }

    private function validarDatos($excluirId = null) {
        $errores = [];

        if (empty($this->id_proveedores)) {
            $errores[] = "El RIF es obligatorio";
        }

        if (empty($this->nombre)) {
            $errores[] = "El nombre es obligatorio";
        }

        if (empty($this->telefono)) {
            $errores[] = "El teléfono es obligatorio";
        }

        if (empty($this->correo)) {
            $errores[] = "El correo electrónico es obligatorio";
        } elseif (!filter_var($this->correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del correo electrónico no es válido";
        }

        if (empty($this->direccion)) {
            $errores[] = "La dirección es obligatoria";
        }

        if ($this->existeRif($this->id_proveedores, $excluirId)) {
            $errores[] = "El RIF {$this->id_proveedores} ya está registrado";
        }

        return $errores;
    }

    public function registrar() {
        try {
            $errores = $this->validarDatos();
            if (!empty($errores)) {
                throw new Exception(implode("; ", $errores));
            }

            $sql = "INSERT INTO proveedores (id_proveedores,nombre, telefono, correo, direccion, status) 
                    VALUES (:id_proveedores,:nombre, :telefono, :correo, :direccion, 0)";
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindParam(':id_proveedores', $this->id_proveedores, PDO::PARAM_INT);
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $this->telefono, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $this->correo, PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $this->direccion, PDO::PARAM_STR);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Proveedor->registrar(): " . $e->getMessage());
            throw new Exception("Error al registrar el proveedor en la base de datos");
        } catch (Exception $e) {
            throw $e; 
        }
    }

    public function actualizar() {
        try {

            $errores = $this->validarDatos($this->id_proveedores);
            if (!empty($errores)) {
                throw new Exception(implode("; ", $errores));
            }

            $proveedorActual = $this->buscarPorId($this->id_proveedores);
            if (!$proveedorActual) {
                throw new Exception("Proveedor no encontrado");
            }

            $sql = "UPDATE proveedores SET 
                    nombre = :nombre,
                    telefono = :telefono,
                    correo = :correo,
                    direccion = :direccion
                    WHERE id_proveedores = :id_proveedores AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            
            $stmt->bindParam(':nombre', $this->nombre, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $this->telefono, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $this->correo, PDO::PARAM_STR);
            $stmt->bindParam(':direccion', $this->direccion, PDO::PARAM_STR);
            $stmt->bindParam(':id_proveedores', $this->id_proveedores, PDO::PARAM_INT);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Proveedor->actualizar(): " . $e->getMessage());
            throw new Exception("Error al actualizar el proveedor en la base de datos");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function eliminar() {
        try {
            $proveedorActual = $this->buscarPorId($this->id_proveedores);
            if (!$proveedorActual) {
                throw new Exception("Proveedor no encontrado");
            }

            $sql = "UPDATE proveedores SET status = 1 WHERE id_proveedores = :id AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindParam(':id', $this->id_proveedores, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en Proveedor->eliminar(): " . $e->getMessage());
            throw new Exception("Error al eliminar el proveedor de la base de datos");
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function existeRif($rif, $excluirId = null) {
        try {
            if ($excluirId) {
                $sql = "SELECT COUNT(*) as count FROM proveedores 
                        WHERE id_proveedores = ? AND id_proveedores != ? AND status = 0";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$rif, $excluirId]);
            } else {
                $sql = "SELECT COUNT(*) as count FROM proveedores 
                        WHERE id_proveedores = ? AND status = 0";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute([$rif]);
            }
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error en Proveedor->existeRif(): " . $e->getMessage());
            return false;
        }
    }
}
?>