<?php
namespace src\modelo;
use src\modelo\conexion;
use PDO;
use PDOException;
use Exception;

class usuarioModelo extends conexion {

    private $cedula;
    private $id_rol;
    private $nombre;
    private $telefono;
    private $correo;
    private $clave;

    public function setCedula($cedula) { $this->cedula = $cedula; }
    public function getCedula() { return $this->cedula; }

    public function setId_rol($id_rol) { $this->id_rol = $id_rol; }
    public function getId_rol() { return $this->id_rol; }
    
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function getNombre() { return $this->nombre; }

    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function getTelefono() { return $this->telefono; }

    public function setCorreo($correo) { $this->correo = $correo; }
    public function getCorreo() { return $this->correo; }

    public function setClave($clave) { $this->clave = $clave; }
    public function getClave() { return $this->clave; }

    public function listarRol()
    {
        $sql = "SELECT * FROM rol WHERE status = 0";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    private function validarDatos($esCreacion = true) {
        $errores = [];

        if (empty($this->cedula)) {
            $errores[] = "La cédula es obligatoria";
        } elseif (!preg_match('/^\d{6,10}$/', $this->cedula)) {
            $errores[] = "La cédula debe contener solo números (6-10 dígitos)";
        }

        if (empty(trim($this->nombre))) {
            $errores[] = "El nombre de usuario es obligatorio";
        } elseif (strlen(trim($this->nombre)) < 3) {
            $errores[] = "El nombre debe tener al menos 3 caracteres";
        } elseif (strlen(trim($this->nombre)) > 50) {
            $errores[] = "El nombre no puede exceder los 50 caracteres";
        }

        if (empty($this->telefono)) {
            $errores[] = "El teléfono es obligatorio";
        } elseif (!preg_match('/^[0-9+\-\s()]{10,15}$/', $this->telefono)) {
            $errores[] = "El teléfono debe tener entre 10 y 15 dígitos";
        }

        if (empty($this->correo)) {
            $errores[] = "El correo es obligatorio";
        } elseif (!filter_var($this->correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "Ingrese una dirección de correo electrónico válida";
        }


        if (empty($this->id_rol)) {
            $errores[] = "Debe seleccionar un rol válido";
        }

        if ($esCreacion) {
            if (empty($this->clave)) {
                $errores[] = "La clave es obligatoria";
            } elseif (strlen($this->clave) < 6) {
                $errores[] = "La clave debe tener al menos 6 caracteres";
            }
        } elseif (!empty($this->clave) && strlen($this->clave) < 6) {
            $errores[] = "La clave debe tener al menos 6 caracteres";
        }
        
        return $errores;
    }

 
    private function validarActualizacion() {
        return $this->validarDatos(false);
    }

    public function registrar($cedula, $id_rol, $nombre, $telefono, $correo, $clave) {
        try {
            $this->cedula = $cedula;
            $this->id_rol = $id_rol;
            $this->nombre = $nombre;
            $this->telefono = $telefono;
            $this->correo = $correo;
            $this->clave = $clave;

            $errores = $this->validarDatos(true);
            if (!empty($errores)) {
                throw new Exception(implode(" | ", $errores));
            }

            if ($this->buscarPorId($cedula)) {
                throw new Exception("Ya existe un usuario con esta cédula");
            }

            if ($this->existeUsuario($nombre)) {
                throw new Exception("El nombre de usuario ya existe");
            }

            if ($this->existeCorreo($correo)) {
                throw new Exception("El correo electrónico ya está registrado");
            }

            $sql = "INSERT INTO usuario (cedula, id_rol, username, telefono, correo, clave, status) VALUES (?, ?, ?, ?, ?, ?, 0)";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$cedula, $id_rol, $nombre, $telefono, $correo, $clave]);

            if ($result) {
                error_log("Usuario registrado exitosamente. Cédula: " . $cedula);
                return true;
            } else {
                throw new Exception("No se pudo registrar el usuario en la base de datos");
            }
        } catch (PDOException $e) {
            error_log("Error en Usuario->registrar(): " . $e->getMessage());
            
            if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                if (strpos($e->getMessage(), 'cedula') !== false) {
                    throw new Exception("Ya existe un usuario con esta cédula");
                } elseif (strpos($e->getMessage(), 'username') !== false) {
                    throw new Exception("El nombre de usuario ya existe");
                } elseif (strpos($e->getMessage(), 'correo') !== false) {
                    throw new Exception("El correo electrónico ya está registrado");
                }
            }
            
            throw new Exception("Error al registrar el usuario en la base de datos: " . $e->getMessage());
        }
    }

   
    public function buscarPorCredenciales($usuario) {
        try {
            $sql = "SELECT cedula, username, clave, id_rol FROM usuario WHERE username = :username AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['username' => $usuario]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al buscar usuario: " . $e->getMessage());
            return null;
        }
    }

    public function listar() {
        $sql = "SELECT u.*, r.tipo_usuario 
                FROM usuario u 
                LEFT JOIN rol r ON u.id_rol = r.id_rol WHERE u.status = 0";
        $stmt = $this->pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function buscarPorId($cedula) {
        $sql = "SELECT * FROM usuario WHERE cedula = :cedula AND status = 0";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['cedula' => $cedula]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizar($cedula, $id_rol, $nombre, $telefono, $correo, $clave = null) {
        try {
            $this->cedula = $cedula;
            $this->id_rol = $id_rol;
            $this->nombre = $nombre;
            $this->telefono = $telefono;
            $this->correo = $correo;
            $this->clave = $clave;

            $errores = $this->validarActualizacion();
            if (!empty($errores)) {
                throw new Exception(implode(" | ", $errores));
            }

            if ($this->existeUsuario($nombre, $cedula)) {
                throw new Exception("El nombre de usuario ya existe");
            }

            if ($this->existeCorreo($correo, $cedula)) {
                throw new Exception("El correo electrónico ya está registrado");
            }

            if ($clave) {
                $sql = "UPDATE usuario 
                       SET id_rol = :id_rol, username = :username, telefono = :telefono, 
                           correo = :correo, clave = :clave
                       WHERE cedula = :cedula AND status = 0";
                $stmt = $this->pdo->prepare($sql);
                $claveHash = password_hash($clave, PASSWORD_DEFAULT);
                $stmt->bindParam(':clave', $claveHash);
            } else {
                $sql = "UPDATE usuario 
                       SET id_rol = :id_rol, username = :username, telefono = :telefono, correo = :correo
                       WHERE cedula = :cedula AND status = 0";
                $stmt = $this->pdo->prepare($sql);
            }

            $stmt->bindParam(':cedula', $cedula, PDO::PARAM_INT);
            $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
            $stmt->bindParam(':username', $nombre, PDO::PARAM_STR);
            $stmt->bindParam(':telefono', $telefono, PDO::PARAM_STR);
            $stmt->bindParam(':correo', $correo, PDO::PARAM_STR);
            
            $result = $stmt->execute();
            
            if (!$result) {
                throw new Exception("No se pudieron guardar los cambios en la base de datos");
            }
            
            return true;
               
        } catch (PDOException $e) {
            error_log("Error en Usuario->actualizar(): " . $e->getMessage());
            throw new Exception("Error al actualizar el usuario en la base de datos: " . $e->getMessage());
        }
    }

    public function eliminar($cedula) {
        try {
            if (empty($cedula)) {
                throw new Exception("Cédula no proporcionada");
            }

            $sql = "UPDATE usuario SET status = 1 WHERE cedula = ? AND status = 0";
            $stmt = $this->pdo->prepare($sql);
            $result = $stmt->execute([$cedula]);
            
            if (!$result) {
                throw new Exception("No se pudo eliminar el usuario de la base de datos");
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error en Usuario->eliminar(): " . $e->getMessage());
            throw new Exception("Error al eliminar el usuario de la base de datos: " . $e->getMessage());
        }      
    }

    public function existeUsuario($username, $excluirCedula = null) {
        try {
            if ($excluirCedula) {
                $sql = "SELECT COUNT(*) as count FROM usuario WHERE username = :username AND cedula != :cedula AND status = 0";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['username' => $username, 'cedula' => $excluirCedula]);
            } else {
                $sql = "SELECT COUNT(*) as count FROM usuario WHERE username = :username AND status = 0";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['username' => $username]);
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function existeCorreo($correo, $excluirCedula = null) {
        try {
            if ($excluirCedula) {
                $sql = "SELECT COUNT(*) as count FROM usuario WHERE correo = :correo AND cedula != :cedula AND status = 0";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['correo' => $correo, 'cedula' => $excluirCedula]);
            } else {
                $sql = "SELECT COUNT(*) as count FROM usuario WHERE correo = :correo AND status = 0";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['correo' => $correo]);
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar correo: " . $e->getMessage());
            return false;
        }
    }

    public function verificarNombreDisponible($username, $excluirCedula = null) {
        try {
            if ($excluirCedula) {
                $sql = "SELECT COUNT(*) as count FROM usuario WHERE username = :username AND cedula != :cedula AND status = 0";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['username' => $username, 'cedula' => $excluirCedula]);
            } else {
                $sql = "SELECT COUNT(*) as count FROM usuario WHERE username = :username AND status = 0";
                $stmt = $this->pdo->prepare($sql);
                $stmt->execute(['username' => $username]);
            }
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['count'] == 0;
        } catch (PDOException $e) {
            error_log("Error al verificar nombre de usuario: " . $e->getMessage());
            return false;
        }
    }
}
?>