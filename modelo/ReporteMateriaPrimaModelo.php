<?php
require_once 'vendor/fpdf.php';

class ReporteMateriaPrimaModelo extends FPDF {
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
        $this->Cell(0, 5, 'Email: jlacruzca@gmail.com', 0, 1);
        
        $this->SetDrawColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetLineWidth(0.8);
        $this->Line(10, 32, 200, 32);
        $this->SetLineWidth(0.2);
        $this->Ln(5);
        
        $this->SetFont('Arial', 'B', 14);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 10, 'INVENTARIO DE MATERIAS PRIMAS', 0, 1, 'C');
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
        $this->Cell(0, 6, 'Total de materias primas: ' . $this->datosReporte['total_registros'], 0, 1);
        $this->Ln(5);
    }
    
    public function tablaMateriasPrimas() {
        $materiasPrimas = $this->datosReporte['materias_primas'];
        $azul_principal = array(41, 128, 185);
        
        $this->SetFillColor($azul_principal[0], $azul_principal[1], $azul_principal[2]);
        $this->SetTextColor(255, 255, 255);
        $this->SetFont('Arial', 'B', 9);
        
        $this->Cell(15, 8, '#', 1, 0, 'C', true);
        $this->Cell(60, 8, 'Materia Prima', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Stock', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Unidad', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Costo Unit.', 1, 0, 'C', true);
        $this->Cell(30, 8, 'Valor Total', 1, 1, 'C', true);
        
        $this->SetTextColor(0, 0, 0);
        $this->SetFont('Arial', '', 9);
        
        $contador = 1;
        $valorTotalInventario = 0;
        
        foreach ($materiasPrimas as $materia) {
            $fill = ($contador % 2 == 0) ? true : false;
            if ($fill) {
                $this->SetFillColor(248, 248, 248);
            } else {
                $this->SetFillColor(255, 255, 255);
            }
            
            $stock = is_numeric($materia['stock']) ? $materia['stock'] : 0;
            $costo = is_numeric($materia['costo']) ? $materia['costo'] : 0;
            $valorTotal = $costo * $stock;
            $valorTotalInventario += $valorTotal;
            
            $this->Cell(15, 7, $contador, 1, 0, 'C', $fill);
            $this->Cell(60, 7, $this->truncateText($materia['nombre'], 40), 1, 0, 'L', $fill);
            $this->Cell(30, 7, $stock, 1, 0, 'C', $fill);
            $this->Cell(30, 7, $materia['unidad_medida'], 1, 0, 'C', $fill);
            $this->Cell(30, 7, number_format($costo, 2, ',', '.'), 1, 0, 'R', $fill);
            $this->Cell(30, 7, number_format($valorTotal, 2, ',', '.'), 1, 1, 'R', $fill);
            
            $contador++;
        }
        
        $this->Ln(5);
        
        $this->SetFont('Arial', 'B', 10);
        $this->SetFillColor(200, 200, 200);
        $this->Cell(0, 8, 'RESUMEN DEL INVENTARIO', 1, 1, 'C', true);
        
        $this->SetFont('Arial', '', 10);
        $this->Cell(140, 7, 'Valor total del inventario:', 1, 0, 'R');
        $this->Cell(56, 7, number_format($valorTotalInventario, 2, ',', '.'), 1, 1, 'R');
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
        $this->tablaMateriasPrimas();
        
        $this->Output('I', 'Inventario_Materias_Primas_' . date('Y-m-d') . '.pdf');
        exit;
    }
    
    public function obtenerDatosMateriasPrimas() {
        require_once 'modelo/conexion.php';
        
        try {
            $conexion = new Conexion();
            $pdo = $conexion->getPdo();
            
            $sql = "SELECT 
                        m.id_materia,
                        m.nombre,
                        m.stock,
                        m.costo,
                        um.nombre as unidad_medida
                    FROM inv_materia_prima m
                    LEFT JOIN unidades_medida um ON m.id_unidad_medida = um.id_unidad_medida
                    WHERE m.status = 0
                    ORDER BY m.nombre ASC";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $materiasPrimas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'materias_primas' => $materiasPrimas,
                'total_registros' => count($materiasPrimas)
            ];

        } catch (PDOException $e) {
            error_log("Error en ReporteMateriaPrimaModelo->obtenerDatosMateriasPrimas(): " . $e->getMessage());
            throw new Exception("Error al obtener los datos de materias primas: " . $e->getMessage());
        }
    }
}
?>