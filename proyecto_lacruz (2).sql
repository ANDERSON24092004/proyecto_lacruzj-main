-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 14-11-2025 a las 03:59:50
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `proyecto_lacruz`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `rif` varchar(20) NOT NULL,
  `razon_social` varchar(200) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `fecha_registro` date NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`rif`, `razon_social`, `telefono`, `correo`, `direccion`, `fecha_registro`, `status`) VALUES
('12312312', 'Jhonnel Gonzalez', '6231-2321323', 'gabriel@gmail.com', 'Barqusimeto', '2025-07-09', 0),
('123123123', 'Corpoelec Lara', '0410-4124124', 'deri@gmail.com', 'Barquisimeto ', '2025-10-02', 0),
('12324122', 'Luis Padron', '0352-2342454', 'luiz@gmail.com', 'Cojedes ', '2025-07-07', 0),
('12334241', 'asdasd', '0416-4081836', 'deriss@gmail.com', 'asdadsww', '2025-11-02', 1),
('13123123', 'sadasdasd', '0416-4081836', 'carlosxsdasdd@gmail.com', 'asdasdasd', '2025-11-03', 1),
('13124342', 'calo', '0416-4081836', 'carloshurtad@gmail.com', 'xdxdxd\r\n', '2025-11-03', 1),
('13453456', 'Jangely Lacruz', '0424-4883243', 'carloshurtado30e15@gmail.com', 'Caracas centro', '2025-10-26', 0),
('14314134', 'asdasda', '0416-4081836', 'asdassssd@gmail.com', 'sadada', '2025-11-03', 1),
('21312312', 'ssadasdsdsd', '0424-4883242', 'carlosadasdasdH@gmail.com', 'asdasdasd', '2025-11-03', 1),
('2131313123', 'Delia Alvarez', '0410-4124124', 'delia@gmail.com', 'Caracas Petare', '2025-10-08', 0),
('21342331', 'Jose Alverto', '0424-5683456', 'josealverto@gmail.com', 'Merida', '2025-11-03', 0),
('21423423', 'asdasd', '0416-4081836', 'carlosssH@gmail.com', 'sadasd', '2025-11-02', 1),
('23423132', 'Gabriel Perez', '0424-5998762', 'gav@gmail.com', 'Yaracuy centro\r\n', '2025-10-01', 0),
('23423532', 'Deriana Hurtado', '0424-5998764', 'deri@gmail.com', 'Barquisimeto centro', '2025-10-26', 0),
('23424242', 'ssssss', '0410-4124124', 'carloszzzH@gmail.com', 'weqeqweq', '2025-11-03', 1),
('24124124', 'Alexander Rodriguez', '2412-4124124', 'alexaderodriguez@gmail.com', 'Caracas centro', '2025-07-07', 1),
('34234234', 'asdasd', '0416-4081836', 'adad@gamil.com', 'asdadada', '2025-11-03', 1),
('34254322', 'Irirs Daza', '0424-5362276', 'irisDaza@gmail.com', 'dhasudha', '2025-11-01', 0),
('34324233', 'saasdasdsa', '0424-4883243', 'carlosxssdsdd@gmail.com', 'xdd\r\n', '2025-11-03', 1),
('34534632', 'Enrrique Hurtado', '0416-4081835', 'enrriqueH@gmail.com', 'Barquisimeto zona norte\r\n', '2025-10-26', 0),
('345454225', 'Ana Diaz', '0424-4883245', 'anadiaz@gmail.com', 'Maracaybo', '2025-10-01', 0),
('35322653', 'wewewe', '0416-4081835', 'carlasdasdosxd@gmail.com', 'adsadadsd', '2025-11-05', 1),
('35432342', 'Grecia Falcon', '0251-2345321', 'grecia@gmail.com', 'Caracas centro\r\n', '2025-11-05', 1),
('43534534', 'Carlos Hurtado', '0410-4124124', 'carlosxd@gmail.com', 'Barquisimeto zona Norte\r\n', '2025-10-26', 0),
('45323434', 'Juan Angulo', '0424-4883243', 'carloshurtado30e15@gmail.com', 'Barquisimeto zona norte', '2025-10-26', 0),
('77398478', 'asdasd xd', '0416-4081836', 'asdad@gmail.com', 'dasdada', '2025-11-03', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `condicion_pago`
--

CREATE TABLE `condicion_pago` (
  `id_condicion_pago` int(11) NOT NULL,
  `forma` varchar(30) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `condicion_pago`
--

INSERT INTO `condicion_pago` (`id_condicion_pago`, `forma`, `status`) VALUES
(1, 'No aplica', 0),
(2, 'Credito', 0),
(3, 'Contado', 0),
(4, 'Presupuesto', 0),
(5, 'xddd', 1),
(6, 'xd', 1),
(7, 'asdasd', 1),
(8, 'xd', 1),
(9, 'asdasd', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `descuento`
--

CREATE TABLE `descuento` (
  `id` int(11) NOT NULL,
  `porcentaje` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `descuento`
--

INSERT INTO `descuento` (`id`, `porcentaje`, `status`) VALUES
(1, 10, 0),
(2, 2, 1),
(4, 21, 1),
(5, 23, 1),
(6, 0, 0),
(7, 14, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_factura`
--

CREATE TABLE `detalle_factura` (
  `id-det` int(11) NOT NULL,
  `nro_fact` int(11) NOT NULL,
  `id_inv` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `detalle_factura`
--

INSERT INTO `detalle_factura` (`id-det`, `nro_fact`, `id_inv`, `cantidad`) VALUES
(71, 37, 14, 1),
(72, 37, 12, 1),
(73, 38, 14, 1),
(74, 38, 16, 1),
(75, 39, 11, 3),
(76, 39, 9, 1),
(77, 39, 9, 4),
(78, 40, 9, 1),
(79, 40, 16, 1),
(80, 40, 12, 1),
(81, 41, 9, 1),
(82, 41, 12, 1),
(83, 42, 9, 1),
(84, 42, 13, 1),
(85, 42, 12, 1),
(86, 43, 9, 4),
(87, 43, 12, 1),
(88, 43, 16, 1),
(89, 44, 9, 1),
(90, 44, 16, 1),
(91, 45, 16, 1),
(92, 45, 13, 1),
(93, 46, 17, 1),
(94, 47, 9, 2),
(95, 47, 12, 1),
(96, 48, 11, 1),
(97, 48, 16, 1),
(98, 49, 12, 1),
(99, 50, 16, 1),
(100, 51, 12, 1),
(101, 51, 14, 1),
(102, 52, 12, 1),
(103, 52, 14, 1),
(104, 53, 9, 1),
(105, 53, 11, 1),
(106, 54, 16, 1),
(107, 55, 16, 1),
(108, 55, 12, 1),
(109, 57, 16, 1),
(110, 58, 12, 1),
(111, 58, 16, 1),
(112, 59, 12, 1),
(113, 59, 13, 1),
(114, 59, 9, 7),
(115, 59, 17, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_fact_compra`
--

CREATE TABLE `detalle_fact_compra` (
  `id_fact` int(11) NOT NULL,
  `id_materia_prima` int(11) NOT NULL,
  `id_fact_com` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `costo_compra` float(15,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci;

--
-- Volcado de datos para la tabla `detalle_fact_compra`
--

INSERT INTO `detalle_fact_compra` (`id_fact`, `id_materia_prima`, `id_fact_com`, `cantidad`, `costo_compra`) VALUES
(8, 11, 78, 2, 0.50),
(9, 13, 78, 4, 0.06),
(10, 15, 79, 5, 1.00),
(11, 14, 79, 5, 1.00),
(12, 11, 80, 2, 0.70),
(13, 15, 81, 2, 1.00),
(14, 14, 82, 2, 1.00),
(15, 14, 83, 2, 1.00),
(16, 15, 83, 2, 1.00),
(17, 11, 84, 9, 0.70),
(18, 17, 85, 2, 2.00),
(19, 11, 86, 3, 0.70),
(20, 15, 86, 1, 1.00),
(21, 14, 87, 5, 1.00),
(22, 15, 87, 6, 1.00),
(23, 14, 88, 7, 1.00),
(24, 11, 89, 2, 0.70),
(25, 13, 90, 1, 0.06);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `detalle_producto`
--

CREATE TABLE `detalle_producto` (
  `id_detalle_producto` int(11) NOT NULL,
  `id_materia` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `detalle_producto`
--

INSERT INTO `detalle_producto` (`id_detalle_producto`, `id_materia`, `id_producto`, `cantidad`) VALUES
(5, 11, 14, 2),
(58, 11, 17, 5),
(59, 13, 17, 3),
(78, 11, 32, 2),
(79, 14, 32, 1),
(91, 14, 11, 4),
(92, 11, 11, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_cobrar`
--

CREATE TABLE `factura_cobrar` (
  `id` int(11) NOT NULL,
  `id_facturas` int(11) NOT NULL,
  `pago` float(15,2) NOT NULL,
  `estado_pago` int(11) NOT NULL,
  `fecha_pago` date DEFAULT NULL,
  `duracion` date DEFAULT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `factura_cobrar`
--

INSERT INTO `factura_cobrar` (`id`, `id_facturas`, `pago`, `estado_pago`, `fecha_pago`, `duracion`, `status`) VALUES
(7, 38, 64.26, 1, '2025-11-06', '2026-01-31', 0),
(8, 40, 295.94, 1, '2025-11-03', '2026-01-31', 0),
(9, 42, 349.30, 1, '2025-11-03', '2026-02-01', 0),
(10, 48, 0.00, 0, NULL, '2026-02-03', 1),
(11, 56, 0.00, 1, '2025-11-06', '2026-02-04', 0),
(12, 58, 294.64, 1, '2025-11-06', '2026-02-04', 0),
(13, 59, 0.00, 0, NULL, '2026-02-04', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_compra`
--

CREATE TABLE `factura_compra` (
  `id_fact_com` int(11) NOT NULL,
  `id_proveedor` int(11) DEFAULT NULL,
  `num_factura` varchar(100) NOT NULL,
  `total_iva` decimal(15,2) NOT NULL,
  `total_general` float(15,2) NOT NULL,
  `fecha` date NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `factura_compra`
--

INSERT INTO `factura_compra` (`id_fact_com`, `id_proveedor`, `num_factura`, `total_iva`, `total_general`, `fecha`, `status`) VALUES
(78, 12345678, '001-2345-23123', 0.20, 1.64, '2025-10-22', 0),
(79, 22345674, '123-343-5454', 1.60, 13.20, '2025-10-24', 0),
(80, 23523422, '123-4435-545', 0.22, 1.62, '2025-10-24', 0),
(81, 42342342, '153-7564-6456', 0.32, 2.32, '2025-10-24', 0),
(82, 21323454, '312-3435-54234', 0.32, 2.32, '2025-10-24', 0),
(83, 42342342, '324-5456-23423', 0.64, 4.64, '2025-10-24', 0),
(84, 22345674, '21474836', 1.01, 7.31, '2025-11-02', 0),
(85, 21323454, '2147483434', 0.64, 4.64, '2025-11-02', 0),
(86, 22345674, '002-323-45545', 0.50, 3.60, '2025-11-10', 0),
(87, 1231234, '00-3232-43343', 1.76, 12.76, '2025-11-10', 0),
(88, 2413123, '122-323213', 1.12, 8.12, '2025-11-10', 1),
(89, 6862332, '000-323-3433', 0.22, 1.62, '2025-11-10', 0),
(90, 21323454, '000-2324-2314', 0.01, 0.07, '2025-11-10', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `factura_venta`
--

CREATE TABLE `factura_venta` (
  `nro_fact` int(11) NOT NULL,
  `rif` varchar(20) NOT NULL,
  `id_condicion_pago` int(11) NOT NULL DEFAULT 5,
  `id_descuento` int(11) DEFAULT NULL,
  `numero_orden` varchar(20) NOT NULL,
  `total_iva` float(15,2) NOT NULL,
  `total_general` float(15,2) NOT NULL,
  `fecha` date NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `factura_venta`
--

INSERT INTO `factura_venta` (`nro_fact`, `rif`, `id_condicion_pago`, `id_descuento`, `numero_orden`, `total_iva`, `total_general`, `fecha`, `status`) VALUES
(37, '13453456', 3, 1, '5674768779', 29.00, 210.26, '2025-11-02', 0),
(38, '13453456', 2, NULL, '23525324', 8.86, 64.26, '2025-11-02', 0),
(39, '34254322', 3, NULL, '45645647655', 1.77, 12.83, '2025-11-02', 0),
(40, '13453456', 2, NULL, '23425352', 40.82, 295.94, '2025-11-02', 0),
(41, '2131313123', 3, NULL, '35234324234', 32.18, 233.30, '2025-11-02', 0),
(42, '45323434', 2, NULL, '35234324234', 48.18, 349.30, '2025-11-03', 0),
(43, '123123123', 3, NULL, '345354364334', 41.36, 299.84, '2025-11-03', 0),
(44, '34534632', 4, NULL, '1312435245', 8.82, 63.94, '2025-11-03', 0),
(45, '23423532', 4, NULL, '324235234', 24.64, 178.64, '2025-11-03', 0),
(46, '13453456', 4, NULL, '', 0.16, 1.16, '2025-11-03', 0),
(47, '23423532', 4, NULL, '', 32.36, 234.60, '2025-11-03', 0),
(48, '21342331', 2, NULL, '0002355045', 8.93, 64.75, '2025-11-05', 1),
(49, '21342331', 3, NULL, '', 32.00, 232.00, '2025-11-05', 0),
(50, '34534632', 3, NULL, '1423435424', 8.64, 62.64, '2025-11-05', 0),
(51, '21342331', 4, NULL, '', 32.22, 233.62, '2025-11-05', 0),
(52, '21342331', 4, NULL, '', 32.22, 233.62, '2025-11-05', 0),
(53, '23423132', 4, NULL, '1231432234', 0.47, 3.41, '2025-11-05', 0),
(54, '23423532', 4, NULL, '', 8.64, 62.64, '2025-11-05', 0),
(55, '34254322', 4, NULL, '', 40.64, 294.64, '2025-11-06', 0),
(56, '21342331', 2, NULL, '24235235534', 0.00, 0.00, '2025-11-06', 0),
(57, '13453456', 4, NULL, '', 8.64, 62.64, '2025-11-06', 0),
(58, '21342331', 2, NULL, '0085768433', 40.64, 294.64, '2025-11-06', 0),
(59, '345454225', 2, NULL, '0877754323', 49.41, 358.25, '2025-11-06', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inv_materia_prima`
--

CREATE TABLE `inv_materia_prima` (
  `id_materia` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `stock` int(11) NOT NULL,
  `costo` float(15,2) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `inv_materia_prima`
--

INSERT INTO `inv_materia_prima` (`id_materia`, `id_unidad_medida`, `nombre`, `stock`, `costo`, `status`) VALUES
(11, 8, 'Hipoclorito', 25, 0.70, 0),
(13, 5, 'Soda Liquida', 26, 0.06, 0),
(14, 5, 'Metanol', 29, 1.00, 0),
(15, 5, 'Manifenol', 30, 1.00, 0),
(16, 13, 'Soda', 15, 1.00, 1),
(17, 5, 'asd', 2, 2.00, 1),
(18, 13, 'aads', 10, 0.50, 1),
(19, 13, 'xddd', 25, 2.00, 1),
(20, 12, 'dsda', 20, 2.00, 1),
(21, 12, 'xdd', 30, 3.00, 1),
(22, 13, 'sadadasd', 20, 3.00, 1),
(23, 12, 'asdasd', 23, 23.00, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inv_prod_serv`
--

CREATE TABLE `inv_prod_serv` (
  `id_inv` int(11) NOT NULL,
  `id_pres` int(11) NOT NULL,
  `id_unidad_medida` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `costo` float(15,2) NOT NULL,
  `costo_mayor` float(15,2) NOT NULL,
  `stock` int(11) NOT NULL,
  `tipo` int(11) NOT NULL,
  `es_fabricado` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `inv_prod_serv`
--

INSERT INTO `inv_prod_serv` (`id_inv`, `id_pres`, `id_unidad_medida`, `nombre`, `costo`, `costo_mayor`, `stock`, `tipo`, `es_fabricado`, `status`) VALUES
(9, 1, 5, 'Desengrasante', 1.12, 1.01, 12, 1, 0, 0),
(11, 1, 5, 'Desinfectante', 4.70, 4.23, 8, 1, 1, 0),
(12, 6, 8, 'Desmalesado', 200.00, 0.00, 0, 2, 0, 0),
(13, 6, 9, 'Pico y Podaa', 100.00, 0.00, 0, 2, 0, 0),
(14, 2, 5, 'Cloro', 1.40, 1.26, 0, 1, 1, 0),
(16, 6, 10, 'Limpieza de Baños', 1000.00, 0.00, 0, 2, 0, 0),
(17, 1, 5, 'Mistolin', 3.68, 3.31, 20, 1, 1, 0),
(26, 6, 8, 'asdad', 200.00, 0.00, 0, 2, 0, 1),
(28, 6, 5, 'llenado de botellones', 50.00, 0.00, 0, 2, 0, 0),
(31, 2, 8, 'asdasd', 1.70, 1.53, 20, 1, 1, 1),
(32, 3, 5, 'Jabon', 2.40, 2.16, 30, 1, 1, 0),
(33, 3, 5, 'adasd', 5.00, 0.00, 10, 1, 1, 1),
(34, 4, 13, 'prueba con materia', 4.10, 3.69, 15, 1, 1, 1),
(35, 3, 5, 'prueba sin materia', 20.00, 18.00, 10, 1, 0, 1),
(36, 6, 12, 'prueba de servicio', 55.00, 0.00, 0, 2, 0, 1),
(37, 2, 12, 'prueba 2', 30.00, 27.00, 5, 1, 0, 1),
(38, 1, 9, 'sadadsa', 10.00, 9.00, 1, 1, 0, 1),
(39, 1, 12, 'xdxdx', 3.00, 0.00, 1, 1, 0, 1),
(40, 1, 12, 'asdsdxdd', 9.00, 8.10, 1, 1, 0, 1),
(41, 1, 12, 'sdasd', 2.00, 1.80, 0, 1, 0, 1),
(42, 3, 13, 'asdasd', 21.00, 18.90, 12, 1, 0, 1),
(43, 2, 8, 'xddd', 1.58, 1.42, 23, 1, 1, 1),
(44, 1, 8, 'asdad', 4.12, 3.71, 12, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iva`
--

CREATE TABLE `iva` (
  `id_iva` int(11) NOT NULL,
  `porcentaje` int(11) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `iva`
--

INSERT INTO `iva` (`id_iva`, `porcentaje`, `status`) VALUES
(1, 16, 0),
(2, 2, 1),
(3, 3, 1),
(4, 22, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `iva_factura`
--

CREATE TABLE `iva_factura` (
  `id` int(11) NOT NULL,
  `id_iva` int(11) NOT NULL,
  `id_factura` int(11) NOT NULL,
  `fecha_cambio` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `iva_factura`
--

INSERT INTO `iva_factura` (`id`, `id_iva`, `id_factura`, `fecha_cambio`) VALUES
(30, 1, 37, '2025-11-02'),
(31, 1, 38, '2025-11-02'),
(32, 1, 39, '2025-11-02'),
(33, 1, 40, '2025-11-02'),
(34, 1, 41, '2025-11-02'),
(35, 1, 42, '2025-11-03'),
(36, 1, 43, '2025-11-03'),
(37, 1, 44, '2025-11-03'),
(38, 1, 45, '2025-11-03'),
(39, 1, 46, '2025-11-03'),
(40, 1, 47, '2025-11-03'),
(41, 1, 48, '2025-11-05'),
(42, 1, 49, '2025-11-05'),
(43, 1, 50, '2025-11-05'),
(44, 1, 51, '2025-11-05'),
(45, 1, 52, '2025-11-05'),
(46, 1, 53, '2025-11-05'),
(47, 1, 54, '2025-11-05'),
(48, 1, 55, '2025-11-06'),
(49, 1, 56, '2025-11-06'),
(50, 1, 57, '2025-11-06'),
(51, 1, 58, '2025-11-06'),
(52, 1, 59, '2025-11-06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id_proveedores` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `direccion` text NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id_proveedores`, `nombre`, `telefono`, `correo`, `direccion`, `status`) VALUES
(1231234, 'Luis Padron', '0414-0818354', 'carlosxd@gmail.com', 'Cojedes puebla', 0),
(2413123, 'Carlos Hurtado', '0416-4081836', 'deri@gmail.com', 'asdasdasd', 0),
(6862332, 'Maria Jose', '0412-2388091', 'maria@gmail.com', 'Barquisimeto', 0),
(12345678, 'Toma Angulo', '0416-4081878', 'tomas@gmail.com', 'Caracas calle 20', 0),
(21323454, 'Pedro', '0410412412', 'gabriel@gmail.com', 'Yaracuy centro\r\n', 0),
(22345674, 'Miguel', '04104124124', 'deri@gmail.com', 'Barquisimeto centro\r\n', 0),
(23434234, 'adad', '0416-4081836', 'asdsdsssad@gmail.com', 'saddsds', 1),
(23523422, 'Maria', '3131232133', 'gabriel@gmail.com', 'Barquisimeto, via duaca', 1),
(24245232, 'carlossss', '04164081836', 'deri@gmail.com', 'dasdasd', 1),
(24522342, 'adasd', '0424488324', 'gabriel@gmail.com', 'asdadasdasd', 1),
(34534536, 'adasdasd', '0416408183', 'gavaa@gmail.com', 'asdadds', 1),
(42342342, 'Juan', '03213132323', 'carlosH@gmail.com', 'Caracas asdad', 1),
(1214325234, 'dfhfsdfsg', '0424-4884134', 'asdsad@gmail.com', 'asdadsdasd', 1),
(2147483647, 'pendejo', '0424-4534254', 'dasdel@gmail.com', 'asdadasd', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `id_rol` int(11) NOT NULL,
  `tipo_usuario` varchar(20) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`id_rol`, `tipo_usuario`, `status`) VALUES
(1, 'admin', 0),
(2, 'oficina', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_presentacion`
--

CREATE TABLE `tipo_presentacion` (
  `id_pres` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `tipo_presentacion`
--

INSERT INTO `tipo_presentacion` (`id_pres`, `nombre`, `status`) VALUES
(1, 'Envase de litro', 0),
(2, 'Envase de 2 litros', 0),
(3, 'Pipa', 0),
(4, 'Galon', 0),
(5, 'Botellon', 0),
(6, 'No aplica', 0),
(8, 'dasdadasd', 1),
(9, 'xddd', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidades_medida`
--

CREATE TABLE `unidades_medida` (
  `id_unidad_medida` int(11) NOT NULL,
  `nombre` varchar(30) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `unidades_medida`
--

INSERT INTO `unidades_medida` (`id_unidad_medida`, `nombre`, `status`) VALUES
(5, 'litro', 0),
(8, 'kilo', 0),
(9, 'metro', 0),
(10, 'metro cuadrado', 0),
(11, 'mililitro', 0),
(12, 'hectarea', 0),
(13, 'gramos', 0),
(14, 'asdasasdasda', 1),
(15, 'xddd', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `cedula` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `clave` varchar(300) NOT NULL,
  `telefono` varchar(20) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`cedula`, `id_rol`, `username`, `clave`, `telefono`, `correo`, `status`) VALUES
(1231324, 1, 'adasd', '$2y$10$TUsCmxgzBH5bAsxCvQV4Tu35toh6dDftL8yD1xwI6imxz2D8s5o5C', '0416-4081836', 'gabosd@gmail.com', 1),
(1425243, 1, 'asd', '$2y$10$uVk6vnjkTbakx/MG1ldXQOPLguTpzL6jjH9elP6JqPLLLcCkes0Sy', '0410-4124124', 'carlosxsssd@gmail.com', 1),
(13122223, 1, 'xdcxdxd', '$2y$10$v/HwrzyYyJB/C7m/yP5YCuN6PO5fqFQ.X/rWTltzE.jb2OtVqwNJO', '0416-4081836', 'sdsxxxd@gmail.com', 1),
(13123123, 1, 'pepe', '$2y$10$ImG1tY4mFy4fbs8gunIDHuuDoNNr7LDO2ZCTk4EWKv/mAmg4PBG9y', '0424-599876', 'gabo@gmail.com', 0),
(13143243, 1, 'carlosxd', '$2y$10$XcTdLGTNaCeMdRrNi18sXuYKUGcVuWg/a1B/bpEccaLWCo0qYwMa2', '04164081836', 'carlosxd@gmail.com', 0),
(13435254, 1, 'weqeqda', '$2y$10$rTjxBCP6ksPA6ldxPYJ9Wur3Ob5JAcfaHhbt9FEctYiG2k2EAvT.6', '0416-4034513', 'sdsdosds@gmail.com', 1),
(15265756, 1, 'jangely', '$2y$10$0E9zwcpplK0MNWl4hUccE./IsqAXAFjmi2skQZxmEQXXRtzdTZ9E6', '0426-7503595', 'jangely@gmail.com', 0),
(30145565, 1, 'jose', '123456', '04245003076', 'g@gmail.com', 0),
(30300300, 1, 'gab', '$2y$10$gwpagfW06NEG2.qZiNOAmOfhbyqnk0ay3KOSCsR7THqhuU3RiFxxm', '424124124', 'gabriel@gmail.com', 0),
(31317400, 1, 'Deriana', '$2y$10$mzjUJ/gjIrqWjRs2acjD2u5QpTjtWz49rnGimQRPFQz29cyBqQ/r.', '0412-2929090', 'deriana@gmail.com', 0),
(31435453, 2, 'deriss', '$2y$10$iYQ7fzUVop.bLCAIrLMwk.3ms0u6Z88WLlhSvp8koSdSc19JmmGpS', '0416-4081836', 'derisss@gmail.com', 0),
(32542657, 1, 'ares', '$2y$10$6h1d/1wjT4oRLlConKmMCeCPGtrNNeU1TPFko0dZL.Z6Z7xqZObwi', '0416-4083464', 'ares@gmail.com', 0),
(34234523, 1, 'xddsss', '$2y$10$26QQhpvR39DoIFUMPG4cs..VOIsgmZf/tYOlCCKvcWueqewykjcxm', '0424488324', 'dessriana@gmail.com', 1),
(41241241, 2, 'carlos', '$2y$10$vwNGAE2v0hYFVGWfr1uJ/O8/rfziODH2654F1fkvl3DKDwcIdaZJ.', '942714912', 'daniel@gmail.com', 0),
(42554674, 2, 'aresxdxdxd', '$2y$10$ouD3xVQ/IewT9w74NfsICOFuW.70rIXAIEu9QI0.uYUrgb638u9J2', '0424-4545223', 'aresxdd@gmail.com', 1),
(52352235, 2, 'xdd', '$2y$10$U4M7iT/eYm8oAVHuR6iH.eOLBT9./OY0CuycSJ9wkwEPAOQPkHXzy', '0416-4083412', 'sxd@gmail.com', 1),
(73234343, 1, 'Harina Pan', '$2y$10$g4Vi/tH1HD.5Eb8Qd8HkWO15UD.BPO66pYE8AC7.WkrIhTZtalxLi', '0424-48832434', 'gaasasdosds@gmail.com', 1),
(93945343, 2, 'asdadda', '$2y$10$Dda2a1Hc4e5AduLdkNQOEOEq1BlUTsYNQmZaSBDk6gTmVnZ2P7Sqa', '0424-4883244', 'gabasadso@gmail.com', 1),
(123124343, 1, 'asdadsad', '$2y$10$GS06PdADNt4v93ZG/yfNoeB7fkQ2vjBmY2YC1MfaTVpXcSZ1tkCIO', '0412-4081836', 'asdisxd@gmail.com', 1),
(234523135, 1, 'asda', '$2y$10$xF85YDsDlD03pt/Y9FRdouDfHk8Wu93K61N.YdanCvJfh/wXP0M5G', '0424-353531', 'asda@gmial.com', 1),
(324235345, 2, 'pepexdd', '$2y$10$kXP926LdX.BiXvWQSUvz3OaAJ.uE56zp3Rq31e7JAdF5mxjX3haUm', '0416-4081836', 'pepe@gmial.com', 0),
(324352344, 2, 'wiwiwi', '$2y$10$y.EY/Q6DHlG2EXD4988YvOOrvqX/xZxs.IdTKikwbFilBcJGgsn5i', '0412-4124124', 'gabosds@gmail.com', 1),
(325435346, 1, 'asdadww', '$2y$10$qYLl.AvbHViPspBiYM7yIO0EZ6Ekx0qsielJkPi/r0NsqmiunHgEm', '0424-4883244', 'cdsdsd@gmail.com', 1),
(353245124, 1, 'qwersad', '$2y$10$/v5N2Wbs4z6/2R/jav8XUuwDf.g8D1Swx0YkCLYgNSUvAMKUIH5J2', '0416-4081835', 'sdasdrlosxd@gmail.com', 1),
(452343214, 1, 'gfgdfh', '$2y$10$TCbXsF784Goicbw9QPfr/ePvhXGg1oXA1WCrQX1bCLaCBLi0Ylz72', '0416-405362', 'asdadv@gmail.com', 1),
(764564453, 1, 'jabonsadsds', '$2y$10$8ygg8P003E1b/a6rCPbNTOaPowmje9ja.7.8qTNQmLlEEei1LJSz2', '0424-4883243', 'dsdsdsd@gmail.com', 1),
(964635234, 1, 'Harina', '$2y$10$meApoqM2Ny40OngxpVP/1eQ91NGvG9tGSmRqWYJyPvqKzUAmhlW3K', '0410-41241243', 'harias@gmail.com', 1),
(1234352454, 1, 'carlos3015', '$2y$10$C8ul8wMWaQbpcawQCZd1auEee9cD99W.WEQ6/DhEytMCUP2ic5G46', '04164081836', 'derisxdxddd@gmail.com', 0),
(2147483647, 2, 'gabriel', '$2y$10$wigwjOA4g3eDc9Put70AxuDCjUpxaQZVfILSHSvvoh.R6KYe6ac2u', '04244324124', 'gav@gmail.com', 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`rif`),
  ADD KEY `idx_cliente_rif` (`rif`);

--
-- Indices de la tabla `condicion_pago`
--
ALTER TABLE `condicion_pago`
  ADD PRIMARY KEY (`id_condicion_pago`);

--
-- Indices de la tabla `descuento`
--
ALTER TABLE `descuento`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD PRIMARY KEY (`id-det`),
  ADD KEY `id_producto` (`nro_fact`),
  ADD KEY `id_inv` (`id_inv`);

--
-- Indices de la tabla `detalle_fact_compra`
--
ALTER TABLE `detalle_fact_compra`
  ADD PRIMARY KEY (`id_fact`),
  ADD KEY `id_servicio` (`id_materia_prima`),
  ADD KEY `id_fact_com` (`id_fact_com`);

--
-- Indices de la tabla `detalle_producto`
--
ALTER TABLE `detalle_producto`
  ADD PRIMARY KEY (`id_detalle_producto`),
  ADD KEY `id_materia` (`id_materia`),
  ADD KEY `id_producto` (`id_producto`);

--
-- Indices de la tabla `factura_cobrar`
--
ALTER TABLE `factura_cobrar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_facturas` (`id_facturas`);

--
-- Indices de la tabla `factura_compra`
--
ALTER TABLE `factura_compra`
  ADD PRIMARY KEY (`id_fact_com`),
  ADD KEY `idx_factura_fecha` (`fecha`),
  ADD KEY `id_detalle_factura_servicio` (`id_proveedor`);

--
-- Indices de la tabla `factura_venta`
--
ALTER TABLE `factura_venta`
  ADD PRIMARY KEY (`nro_fact`),
  ADD KEY `id_presupuesto` (`rif`),
  ADD KEY `idx_factura_fecha` (`fecha`),
  ADD KEY `id_forma_pago` (`id_condicion_pago`),
  ADD KEY `id_descuento` (`id_descuento`);

--
-- Indices de la tabla `inv_materia_prima`
--
ALTER TABLE `inv_materia_prima`
  ADD PRIMARY KEY (`id_materia`),
  ADD KEY `id_unidad_medida` (`id_unidad_medida`);

--
-- Indices de la tabla `inv_prod_serv`
--
ALTER TABLE `inv_prod_serv`
  ADD PRIMARY KEY (`id_inv`),
  ADD KEY `id_unidad_medida_producto` (`id_pres`),
  ADD KEY `id_descuento` (`id_unidad_medida`);

--
-- Indices de la tabla `iva`
--
ALTER TABLE `iva`
  ADD PRIMARY KEY (`id_iva`);

--
-- Indices de la tabla `iva_factura`
--
ALTER TABLE `iva_factura`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_factura` (`id_factura`),
  ADD KEY `id_iva` (`id_iva`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id_proveedores`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `tipo_presentacion`
--
ALTER TABLE `tipo_presentacion`
  ADD PRIMARY KEY (`id_pres`);

--
-- Indices de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  ADD PRIMARY KEY (`id_unidad_medida`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`cedula`),
  ADD KEY `id_rol` (`id_rol`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `condicion_pago`
--
ALTER TABLE `condicion_pago`
  MODIFY `id_condicion_pago` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `descuento`
--
ALTER TABLE `descuento`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  MODIFY `id-det` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT de la tabla `detalle_fact_compra`
--
ALTER TABLE `detalle_fact_compra`
  MODIFY `id_fact` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `detalle_producto`
--
ALTER TABLE `detalle_producto`
  MODIFY `id_detalle_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT de la tabla `factura_cobrar`
--
ALTER TABLE `factura_cobrar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `factura_compra`
--
ALTER TABLE `factura_compra`
  MODIFY `id_fact_com` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT de la tabla `factura_venta`
--
ALTER TABLE `factura_venta`
  MODIFY `nro_fact` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `inv_materia_prima`
--
ALTER TABLE `inv_materia_prima`
  MODIFY `id_materia` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `inv_prod_serv`
--
ALTER TABLE `inv_prod_serv`
  MODIFY `id_inv` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT de la tabla `iva`
--
ALTER TABLE `iva`
  MODIFY `id_iva` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `iva_factura`
--
ALTER TABLE `iva_factura`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id_proveedores` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2147483648;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `tipo_presentacion`
--
ALTER TABLE `tipo_presentacion`
  MODIFY `id_pres` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `unidades_medida`
--
ALTER TABLE `unidades_medida`
  MODIFY `id_unidad_medida` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `detalle_factura`
--
ALTER TABLE `detalle_factura`
  ADD CONSTRAINT `detalle_factura_ibfk_2` FOREIGN KEY (`nro_fact`) REFERENCES `factura_venta` (`nro_fact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_factura_ibfk_3` FOREIGN KEY (`id_inv`) REFERENCES `inv_prod_serv` (`id_inv`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_fact_compra`
--
ALTER TABLE `detalle_fact_compra`
  ADD CONSTRAINT `detalle_fact_compra_ibfk_1` FOREIGN KEY (`id_materia_prima`) REFERENCES `inv_materia_prima` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_fact_compra_ibfk_2` FOREIGN KEY (`id_fact_com`) REFERENCES `factura_compra` (`id_fact_com`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `detalle_producto`
--
ALTER TABLE `detalle_producto`
  ADD CONSTRAINT `detalle_producto_ibfk_1` FOREIGN KEY (`id_materia`) REFERENCES `inv_materia_prima` (`id_materia`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detalle_producto_ibfk_2` FOREIGN KEY (`id_producto`) REFERENCES `inv_prod_serv` (`id_inv`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura_cobrar`
--
ALTER TABLE `factura_cobrar`
  ADD CONSTRAINT `factura_cobrar_ibfk_2` FOREIGN KEY (`id_facturas`) REFERENCES `factura_venta` (`nro_fact`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `factura_compra`
--
ALTER TABLE `factura_compra`
  ADD CONSTRAINT `factura_compra_ibfk_6` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedores` (`id_proveedores`);

--
-- Filtros para la tabla `factura_venta`
--
ALTER TABLE `factura_venta`
  ADD CONSTRAINT `factura_venta_ibfk_1` FOREIGN KEY (`rif`) REFERENCES `cliente` (`rif`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `factura_venta_ibfk_2` FOREIGN KEY (`id_condicion_pago`) REFERENCES `condicion_pago` (`id_condicion_pago`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `factura_venta_ibfk_3` FOREIGN KEY (`id_descuento`) REFERENCES `descuento` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `inv_materia_prima`
--
ALTER TABLE `inv_materia_prima`
  ADD CONSTRAINT `inv_materia_prima_ibfk_1` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `inv_prod_serv`
--
ALTER TABLE `inv_prod_serv`
  ADD CONSTRAINT `inv_prod_serv_ibfk_3` FOREIGN KEY (`id_unidad_medida`) REFERENCES `unidades_medida` (`id_unidad_medida`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `inv_prod_serv_ibfk_4` FOREIGN KEY (`id_pres`) REFERENCES `tipo_presentacion` (`id_pres`);

--
-- Filtros para la tabla `iva_factura`
--
ALTER TABLE `iva_factura`
  ADD CONSTRAINT `iva_factura_ibfk_1` FOREIGN KEY (`id_factura`) REFERENCES `factura_venta` (`nro_fact`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `iva_factura_ibfk_2` FOREIGN KEY (`id_iva`) REFERENCES `iva` (`id_iva`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD CONSTRAINT `usuario_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `rol` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
