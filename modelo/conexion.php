<?php
class Conexion {
    protected $pdo;

    public function __construct() {
    try {
        $this->pdo = new PDO("mysql:host=localhost;dbname=proyecto_lacruz", "root", "");
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); 
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
	}

	public function getPdo(){
		return $this->pdo;
	}

}


?>
