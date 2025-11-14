<?php
require_once 'vendor/fpdf.php';

class ReporteServicioModelo extends FPDF {
    private $datosReporte;
    
    public function __construct($datosReporte = []) {
        parent::__construct('P', 'mm', 'Letter');
        $this->datosReporte = $datosReporte;
        $this->SetAutoPageBreak(true, 30);
    }
    
    public function Header() {
        $azul_principal = array(41, 128, 185);
        
        $logoPath = 'assets/images/logo2.png';
        $logoWidth = 0;
        if (file_exists($logoPath)) {
            $this->Image($logoPath, 10, 8, 25);
            $logoWidth = 30;
        }
        
        $this->SetX(10 + $logoWidth);
        $this->SetFont('Arial', 'B', 16);
        $this->SetTextColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->Cell(0, 8, 'J LACRUZ C.A.', 0, 1);
        
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(80, 80, 80);
        $this->SetX(10 + $logoWidth);
        $this->Cell(0, 5, 'RIF: J-412192701', 0, 1);
        $this->SetX(10 + $logoWidth);
        $this->Cell(0, 5, 'Carrera 2 entre Calle 6 y 7 Local Casa N914 Nro, Local 3 Sector la Mata Cabudare, Edo.Lara', 0, 1);
        $this->SetX(10 + $logoWidth);
        $this->Cell(0, 5, 'Zona postal 3001 | Telefono: +58 424-5085666 0414-5718890', 0, 1);
        $this->SetX(10 + $logoWidth);
        $this->Cell(0, 5, 'Email: jlacruzca@gmail.com', 0, 1);
        
        $this->SetDrawColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetLineWidth(0.8);
        $this->Line(10, 40, 200, 40);
        $this->SetLineWidth(0.2);
        $this->Ln(5);
        
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'LISTADO DE SERVICIOS', 0, 1, 'C');
        $this->Ln(2);
    }
    
    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);
        $this->Cell(0, 6, 'Pagina ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }
    
    public function infoReporte() {
        $this->SetFont('Arial', '', 10);
        $this->Cell(0, 6, 'Fecha de generacion: ' . date('d/m/Y H:i:s'), 0, 1);
        $this->Cell(0, 6, 'Total de servicios: ' . $this->datosReporte['total_registros'], 0, 1);
        $this->Ln(5);
    }
    
    public function tablaServicios() {
        $servicios = $this->datosReporte['servicios'];
        $azul_principal = array(41, 128, 185);
        $azul_claro = array(236, 240, 245);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 10);
        $this->Cell(15, 8, '#', 1, 0, 'C', true);
        $this->Cell(80, 8, 'Nombre del Servicio', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Unidad Medida', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Costo ($)', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 9);
        
        $contador = 1;
        foreach ($servicios as $servicio) {
            $fill = ($contador % 2 == 0) ? true : false;
            if ($fill) {
                $this->SetFillColor(248, 248, 248);
            } else {
                $this->SetFillColor(255, 255, 255);
            }
            
            $this->Cell(15, 7, $contador, 1, 0, 'C', $fill);
            $this->Cell(80, 7, $this->truncateText($servicio['nombre'], 45), 1, 0, 'L', $fill);
            $this->Cell(40, 7, $servicio['unidad_medida'], 1, 0, 'C', $fill);
            $this->Cell(30, 7, number_format($servicio['costo'], 2, ',', '.'), 1, 1, 'R', $fill);
            
            $contador++;
        }
        
        $this->Ln(8);
    }
    
    private function truncateText($text, $length) {
        if (strlen($text) > $length) {
            return substr($text, 0, $length - 3) . '...';
        }
        return $text;
    }

    public function generarPDF($datos) {
        $this->datosReporte = $datos;
        $this->AddPage();
        $this->AliasNbPages();
        $this->infoReporte();
        $this->tablaServicios();
        
        $this->Output('I', 'Reporte_Servicios_' . date('Y-m-d') . '.pdf');
        exit;
    }
    
    public function obtenerDatosServicios() {
        require_once 'modelo/conexion.php';
        
        try {
            $conexion = new Conexion();
            $pdo = $conexion->getPdo();
            
            $sql = "SELECT 
                        ps.id_inv,
                        ps.nombre,
                        ps.costo,
                        um.nombre as unidad_medida
                    FROM inv_prod_serv ps
                    LEFT JOIN unidades_medida um ON ps.id_unidad_medida = um.id_unidad_medida
                    WHERE ps.tipo = 2 AND ps.status = 0
                    ORDER BY ps.nombre ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $servicios = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'servicios' => $servicios,
                'total_registros' => count($servicios)
            ];

        } catch (PDOException $e) {
            error_log("Error en ReporteServicioModelo->obtenerDatosServicios(): " . $e->getMessage());
            throw new Exception("Error al obtener los datos de servicios: " . $e->getMessage());
        }
    }
}
?>