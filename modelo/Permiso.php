<?php
class Permiso {
    private $pdo;
    
    public function __construct() {
        require_once 'modelo/conexion.php';
        $conexion = new Conexion();
        $this->pdo = $conexion->getPdo();
    }
    
    private $permisosPorRol = [
        1 => [
            'clientes', 'presupuestos', 'facturacion', 'cuentas_cobrar',
            'productos_servicios', 'materia_prima', 'proveedores', 
            'facturas_compra', 'usuarios', 'reportes', 'configuracion','Descuentos','Iva','Condicion_pago','Presentacion','Unidades_medida'
        ],
        2 => [
            'clientes', 'presupuestos', 'facturacion', 'cuentas_cobrar',
            'productos_servicios'
        ]
    ];
    
    public function tienePermiso($id_rol, $modulo) {
        if (!isset($this->permisosPorRol[$id_rol])) {
            return false;
        }
        
        return in_array($modulo, $this->permisosPorRol[$id_rol]);
    }
    
    public function getModulosPermitidos($id_rol) {
        return $this->permisosPorRol[$id_rol] ?? [];
    }
    
    public function getModuloPorControlador($controlador) {
        $mapeo = [
            'ClienteControlador' => 'clientes',
            'PresupuestoControlador' => 'presupuestos',
            'FacturaControlador' => 'facturacion',
            'CuentasCobrarControlador' => 'cuentas_cobrar',
            'ProductoServicioControlador' => 'productos_servicios',
            'MateriaPrimaControlador' => 'materia_prima',
            'ProveedorControlador' => 'proveedores',
            'FacturaCompraControlador' => 'facturas_compra',
            'usuarioControlador' => 'usuarios',
            'ReporteControlador' => 'reportes',
            'ConfigControlador' => 'configuracion',
            'IvaControlador' => 'Iva',
            'DescuentoControlador' => 'Descuentos',
            'CondicionPagoControlador' => 'Condicion_pago',
            'PresentacionControlador' => 'Presentacion',
            'UnidadMedidaControlador' => 'Unidades_medida',
            'login' => 'login'
        ];
        
        return $mapeo[$controlador] ?? $controlador;
    }
}
?>