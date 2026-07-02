-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 02-07-2026 a las 15:04:30
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
-- Base de datos: `zfipe`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comites`
--

CREATE TABLE `comites` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `tipo` varchar(50) NOT NULL DEFAULT 'seguimiento',
  `titulo` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `fecha` datetime NOT NULL,
  `lugar` varchar(255) DEFAULT NULL,
  `estado` enum('programado','realizado','cancelado') NOT NULL DEFAULT 'programado',
  `creado_por` int(10) UNSIGNED DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comites`
--

INSERT INTO `comites` (`id`, `empresa_id`, `tipo`, `titulo`, `descripcion`, `fecha`, `lugar`, `estado`, `creado_por`, `creado_en`) VALUES
(3, 6, 'aprobacion', 'Refinorte', 'link reunion', '2026-07-01 11:03:00', 'Virtual', 'programado', 3, '2026-07-01 11:03:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comite_compromisos`
--

CREATE TABLE `comite_compromisos` (
  `id` int(10) UNSIGNED NOT NULL,
  `comite_id` int(10) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `responsable` varchar(255) DEFAULT NULL,
  `fecha_limite` date DEFAULT NULL,
  `estado` enum('pendiente','en_progreso','cumplido','vencido') NOT NULL DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comite_compromisos`
--

INSERT INTO `comite_compromisos` (`id`, `comite_id`, `descripcion`, `responsable`, `fecha_limite`, `estado`, `observaciones`, `created_at`) VALUES
(6, 3, 'Revisar Informe', 'Ejemplo Refinorte', '2026-07-01', 'cumplido', 'asa', '2026-07-01 11:04:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compromisos`
--

CREATE TABLE `compromisos` (
  `id` int(10) UNSIGNED NOT NULL,
  `comite_id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `descripcion` text NOT NULL,
  `responsable` varchar(150) DEFAULT NULL,
  `fecha_limite` datetime DEFAULT NULL,
  `estado` enum('pendiente','cumplido','vencido') NOT NULL DEFAULT 'pendiente',
  `observaciones` text DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compromiso_actualizaciones`
--

CREATE TABLE `compromiso_actualizaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `compromiso_id` int(10) UNSIGNED NOT NULL,
  `estado` enum('pendiente','en_progreso','cumplido','vencido') NOT NULL,
  `observaciones` text DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compromiso_actualizaciones`
--

INSERT INTO `compromiso_actualizaciones` (`id`, `compromiso_id`, `estado`, `observaciones`, `usuario_id`, `created_at`) VALUES
(3, 6, 'cumplido', 'asa', 7, '2026-07-01 15:34:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `compromiso_documentos`
--

CREATE TABLE `compromiso_documentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `compromiso_id` int(10) UNSIGNED NOT NULL,
  `actualizacion_id` int(10) UNSIGNED DEFAULT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `nombre_guardado` varchar(255) NOT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `tamano` int(10) UNSIGNED DEFAULT NULL,
  `subido_por` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `compromiso_documentos`
--

INSERT INTO `compromiso_documentos` (`id`, `compromiso_id`, `actualizacion_id`, `nombre_original`, `nombre_guardado`, `tipo_mime`, `tamano`, `subido_por`, `created_at`) VALUES
(4, 6, 3, 'Image.jpg', '6_6a4579d7bdf3a8.77362669.jpg', 'image/jpeg', 1131123, 7, '2026-07-01 15:34:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documentos`
--

CREATE TABLE `documentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `requisito_id` int(10) UNSIGNED NOT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `nombre_guardado` varchar(255) NOT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `tamano` int(10) UNSIGNED DEFAULT NULL,
  `subido_por` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas`
--

CREATE TABLE `empresas` (
  `id` int(10) UNSIGNED NOT NULL,
  `nit` varchar(20) NOT NULL,
  `razon_social` varchar(150) NOT NULL,
  `representante` varchar(100) NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `correo` varchar(100) DEFAULT NULL,
  `contrasena` varchar(255) NOT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresas`
--

INSERT INTO `empresas` (`id`, `nit`, `razon_social`, `representante`, `telefono`, `correo`, `contrasena`, `creado_en`) VALUES
(6, '123456789', 'REFINORTE', 'Ejemplo', '3116996990', 'contacto@zonafrancadepereira.com', '$2y$10$5W8PYyqciTNWV1jGJBwhc.u4CXEQ299HyRegPyVb.zYvOVphmKnRi', '2026-07-01 10:51:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_alertas`
--

CREATE TABLE `empresa_alertas` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `regla_alerta_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('vencimiento','bloqueo','pendiente','documento','decision') NOT NULL,
  `mensaje` text NOT NULL,
  `prioridad` enum('alta','media','baja') NOT NULL DEFAULT 'media',
  `resuelta` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_resolucion` datetime DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_etapa_progreso`
--

CREATE TABLE `empresa_etapa_progreso` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `etapa_id` int(10) UNSIGNED NOT NULL,
  `porcentaje_avance` decimal(5,2) NOT NULL DEFAULT 0.00,
  `estado` enum('pendiente','en_progreso','completa') NOT NULL DEFAULT 'pendiente',
  `fecha_inicio` datetime DEFAULT NULL,
  `fecha_completado` datetime DEFAULT NULL,
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa_etapa_progreso`
--

INSERT INTO `empresa_etapa_progreso` (`id`, `empresa_id`, `etapa_id`, `porcentaje_avance`, `estado`, `fecha_inicio`, `fecha_completado`, `actualizado_en`) VALUES
(25, 6, 5, 0.00, 'pendiente', NULL, NULL, '2026-07-01 10:51:39'),
(26, 6, 6, 0.00, 'pendiente', NULL, NULL, '2026-07-01 10:51:39'),
(27, 6, 7, 0.00, 'pendiente', NULL, NULL, '2026-07-01 10:51:39'),
(28, 6, 8, 0.00, 'pendiente', NULL, NULL, '2026-07-01 10:51:39'),
(29, 6, 9, 0.00, 'pendiente', NULL, NULL, '2026-07-01 10:51:39'),
(37, 6, 12, 0.00, 'pendiente', NULL, NULL, '2026-07-01 15:26:01'),
(38, 6, 13, 0.00, 'pendiente', NULL, NULL, '2026-07-01 15:26:01'),
(39, 6, 14, 0.00, 'pendiente', NULL, NULL, '2026-07-01 15:26:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_indicador`
--

CREATE TABLE `empresa_indicador` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `indicador_id` int(11) NOT NULL,
  `valor_actual` decimal(15,2) DEFAULT NULL,
  `fecha_reporte` date DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `registrado_por` int(11) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa_indicador`
--

INSERT INTO `empresa_indicador` (`id`, `empresa_id`, `indicador_id`, `valor_actual`, `fecha_reporte`, `observaciones`, `registrado_por`, `updated_at`) VALUES
(1, 6, 1, 50.00, '2026-04-01', NULL, 3, '2026-07-01 20:56:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_indicador_valor`
--

CREATE TABLE `empresa_indicador_valor` (
  `id` int(11) NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `indicador_id` int(11) NOT NULL,
  `periodo` varchar(10) NOT NULL,
  `valor` decimal(15,2) DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `registrado_por` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa_indicador_valor`
--

INSERT INTO `empresa_indicador_valor` (`id`, `empresa_id`, `indicador_id`, `periodo`, `valor`, `observaciones`, `registrado_por`, `created_at`) VALUES
(1, 6, 1, '2026-04', 30.00, NULL, 3, '2026-07-01 21:00:12'),
(3, 6, 1, '2026-05', 60.00, NULL, 3, '2026-07-01 21:12:55'),
(4, 6, 1, '2026-06', 100.00, NULL, 3, '2026-07-01 21:15:12'),
(5, 6, 1, '2026-07', 10.00, NULL, 3, '2026-07-01 21:15:30'),
(6, 6, 1, '2024-08', 80.00, NULL, 3, '2026-07-01 21:15:53'),
(7, 6, 1, '2024-09', 100.00, NULL, 3, '2026-07-01 21:39:28'),
(8, 6, 1, '2024-10', 100.00, NULL, 3, '2026-07-01 21:39:46'),
(9, 6, 1, '2024-11', 80.00, NULL, 3, '2026-07-01 21:40:02');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_requisito_estado`
--

CREATE TABLE `empresa_requisito_estado` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `requisito_id` int(10) UNSIGNED NOT NULL,
  `estado` enum('pendiente','en_progreso','cumplido','no_aplica') NOT NULL DEFAULT 'pendiente',
  `aprobado` tinyint(1) NOT NULL DEFAULT 0,
  `aprobado_por` int(10) UNSIGNED DEFAULT NULL,
  `fecha_aprobacion` datetime DEFAULT NULL,
  `fecha_cumplimiento` datetime DEFAULT NULL,
  `fecha_vencimiento` datetime DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empresa_requisito_estado`
--

INSERT INTO `empresa_requisito_estado` (`id`, `empresa_id`, `requisito_id`, `estado`, `aprobado`, `aprobado_por`, `fecha_aprobacion`, `fecha_cumplimiento`, `fecha_vencimiento`, `observaciones`, `actualizado_en`) VALUES
(61, 6, 8, 'pendiente', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-01 10:51:39'),
(62, 6, 9, 'pendiente', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-01 10:51:39'),
(63, 6, 10, 'pendiente', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-01 10:51:39'),
(64, 6, 11, 'pendiente', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-01 10:51:39'),
(65, 6, 12, 'pendiente', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-01 10:51:39'),
(66, 6, 13, 'pendiente', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-01 10:51:39'),
(67, 6, 14, 'pendiente', 0, NULL, NULL, NULL, NULL, NULL, '2026-07-01 10:51:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa_requisito_item_estado`
--

CREATE TABLE `empresa_requisito_item_estado` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `requisito_item_id` int(10) UNSIGNED NOT NULL,
  `cumplido` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_cumplimiento` datetime DEFAULT NULL,
  `observaciones` text DEFAULT NULL,
  `registrado_por` int(10) UNSIGNED DEFAULT NULL,
  `actualizado_en` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `entidades`
--

CREATE TABLE `entidades` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `entidades`
--

INSERT INTO `entidades` (`id`, `nombre`, `descripcion`, `activo`, `creado_en`) VALUES
(1, 'DIAN', 'Dirección de Impuestos y Aduanas Nacionales', 1, '2026-06-25 16:06:33'),
(2, 'MINCIT', 'Ministerio de Comercio, Industria y Turismo', 1, '2026-06-25 16:06:33'),
(3, 'Usuario Operador', 'Operador de la Zona Franca', 1, '2026-06-25 16:06:33'),
(6, 'Firma de auditoria externa', NULL, 1, '2026-07-01 14:35:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `etapas`
--

CREATE TABLE `etapas` (
  `id` int(10) UNSIGNED NOT NULL,
  `fase_id` int(10) UNSIGNED DEFAULT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `peso_porcentual` decimal(5,2) NOT NULL DEFAULT 0.00,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `etapas`
--

INSERT INTO `etapas` (`id`, `fase_id`, `nombre`, `descripcion`, `orden`, `peso_porcentual`, `activo`, `creado_en`) VALUES
(5, 1, 'Garantia', NULL, 1, 25.00, 1, '2026-06-26 11:18:39'),
(6, 1, 'Cerramiento Provisional', NULL, 2, 25.00, 1, '2026-06-26 11:19:09'),
(7, 1, 'Control frentes de Obra', NULL, 3, 25.00, 1, '2026-06-26 11:19:26'),
(8, 1, 'Manuales y Procedimientos', NULL, 4, 25.00, 1, '2026-06-26 11:19:51'),
(9, 2, 'Sistema de control de inventarios', NULL, 5, 16.60, 1, '2026-06-26 12:03:21'),
(12, 2, 'Activación de Proceso Productivo', NULL, 8, 0.00, 1, '2026-07-01 14:32:21'),
(13, 2, 'Notificación al MINCIT Puesta en marcha', NULL, 9, 0.00, 1, '2026-07-01 14:32:37'),
(14, 2, 'Seguimiento Compromisos', NULL, 10, 0.00, 1, '2026-07-01 14:33:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fases`
--

CREATE TABLE `fases` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `fases`
--

INSERT INTO `fases` (`id`, `nombre`, `descripcion`, `orden`, `activo`, `created_at`) VALUES
(1, 'AVANCE DEL PROYECTO - ETAPA PREOPERATIVA', 'Administración y seguimiento de las actividades preoperativas de la Zona Franca Permanente Especial, bajo la supervisión del Usuario Operador Zona Franca Internacional de Pereira, garantizando el cumplimiento de los requisitos normativos, operativos y de infraestructura necesarios para su entrada en operación.', 1, 1, '2026-06-26 12:00:46'),
(2, 'AVANCE DEL PROYECTO - ETAPA OPERATIVA', 'Administración y control de las operaciones de la Zona Franca Permanente Especial, bajo la supervisión del Usuario Operador Zona Franca Internacional de Pereira, garantizando el cumplimiento de los requisitos aduaneros, operativos y normativos para el desarrollo eficiente de las actividades autorizadas dentro del régimen franco.', 2, 1, '2026-06-26 12:01:43');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `indicadores`
--

CREATE TABLE `indicadores` (
  `id` int(11) NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `unidad` varchar(100) DEFAULT NULL,
  `meta` decimal(15,2) DEFAULT NULL,
  `periodicidad` enum('mensual','trimestral','semestral','anual') DEFAULT 'anual',
  `tipo_grafico` enum('linea','barra','area','radar','torta','combo') DEFAULT 'linea',
  `comparativo_anual` tinyint(1) NOT NULL DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `indicadores`
--

INSERT INTO `indicadores` (`id`, `nombre`, `descripcion`, `unidad`, `meta`, `periodicidad`, `tipo_grafico`, `comparativo_anual`, `activo`, `created_at`) VALUES
(1, 'Nivel de servicio del operador​', NULL, '%', 100.00, 'mensual', 'barra', 1, 1, '2026-07-01 20:55:29');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reglas_alerta`
--

CREATE TABLE `reglas_alerta` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('vencimiento','bloqueo','pendiente','documento','decision') NOT NULL,
  `dias_anticipacion` int(11) NOT NULL DEFAULT 0,
  `prioridad` enum('alta','media','baja') NOT NULL DEFAULT 'media',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reglas_decision`
--

CREATE TABLE `reglas_decision` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('bloqueo_etapa','vencimiento','dependencia_gerencia','afecta_dian','afecta_mincit','impide_operativo','impide_preoperativo','documento_critico') NOT NULL,
  `prioridad` enum('alta','media','baja') NOT NULL DEFAULT 'media',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requisitos`
--

CREATE TABLE `requisitos` (
  `id` int(10) UNSIGNED NOT NULL,
  `etapa_id` int(10) UNSIGNED NOT NULL,
  `entidad_id` int(10) UNSIGNED DEFAULT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `obligatorio` tinyint(1) NOT NULL DEFAULT 1,
  `requiere_documento` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_fecha_vencimiento` tinyint(1) NOT NULL DEFAULT 0,
  `requiere_aprobacion` tinyint(1) NOT NULL DEFAULT 0,
  `peso_porcentual` decimal(5,2) NOT NULL DEFAULT 0.00,
  `responsable` varchar(150) DEFAULT NULL,
  `alerta_asociada` text DEFAULT NULL,
  `accion_recomendada` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `requisitos`
--

INSERT INTO `requisitos` (`id`, `etapa_id`, `entidad_id`, `nombre`, `descripcion`, `obligatorio`, `requiere_documento`, `requiere_fecha_vencimiento`, `requiere_aprobacion`, `peso_porcentual`, `responsable`, `alerta_asociada`, `accion_recomendada`, `activo`, `creado_en`) VALUES
(8, 6, 3, 'Respuesta MINCIT', NULL, 1, 1, 0, 0, 40.00, 'Operaciones', NULL, NULL, 1, '2026-06-26 11:22:45'),
(9, 6, 3, 'Notificación de implementación del cerramiento', NULL, 1, 1, 0, 0, 40.00, 'Operaciones', NULL, NULL, 1, '2026-06-26 11:23:27'),
(10, 6, 3, 'Posible Visita MINCIT', NULL, 0, 0, 0, 0, 20.00, 'Operaciones', NULL, NULL, 1, '2026-06-26 11:23:54'),
(11, 7, 3, 'Detalle Del Cronograma De Ejecución De Obra.', 'xxxx', 1, 1, 0, 0, 40.00, 'Operaciones', 'xxx', 'xxx', 1, '2026-06-26 11:24:26'),
(12, 7, 3, 'Control operativo de materiales.', 'xxx', 1, 0, 0, 0, 40.00, 'Operaciones', 'xxx', 'xx', 1, '2026-06-26 11:24:52'),
(13, 7, 3, 'Básculas', 'xxx', 1, 1, 1, 1, 20.00, 'Operaciones', 'xxx', 'xxx', 1, '2026-06-26 11:25:22'),
(14, 8, 3, 'Elaboración e implementación', 'xx', 1, 1, 1, 1, 100.00, 'Operaciones', 'xxx', 'xxx', 1, '2026-06-26 11:25:48'),
(16, 5, 1, ' Radicación virtual', NULL, 1, 0, 0, 0, 25.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:28:17'),
(17, 5, 1, ' Evaluación Dian', NULL, 1, 1, 0, 0, 25.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:29:06'),
(18, 5, 1, ' Observaciones y subsanación de información', NULL, 1, 0, 0, 0, 25.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:29:55'),
(19, 5, 1, 'Aceptación', NULL, 1, 1, 0, 0, 25.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:31:23'),
(20, 14, 2, 'Reporte Trimestral : sobre estado de avance en la ejecución del plan maestro de desarrollo', NULL, 1, 1, 0, 0, 0.00, 'Usuario Operador y ZFPE', NULL, NULL, 1, '2026-07-01 14:34:34'),
(21, 14, 6, 'Auditoria', NULL, 1, 1, 0, 0, 0.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:36:03'),
(22, 9, 3, 'Capacitaciones-Montaje- puesta en marcha- interoperabilidad DIAN.', NULL, 1, 0, 0, 0, 0.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:48:18'),
(23, 12, 3, 'Activación de proceso productivo', NULL, 1, 0, 0, 0, 100.00, 'Usuario Operador y ZFPE', NULL, NULL, 1, '2026-07-01 14:49:25'),
(24, 13, 2, 'Primera facturación', 'Enviar al MINCIT', 1, 1, 0, 0, 0.00, 'Usuario Operador y ZFPE', NULL, NULL, 1, '2026-07-01 14:53:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `requisito_items`
--

CREATE TABLE `requisito_items` (
  `id` int(10) UNSIGNED NOT NULL,
  `requisito_id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `obligatorio` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('admin','operaciones','usuario') NOT NULL DEFAULT 'usuario',
  `empresa_id` int(10) UNSIGNED DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contrasena`, `rol`, `empresa_id`, `creado_en`) VALUES
(1, 'Administrador', 'admin@zfipe.com', '$2y$10$A.IReoQZy1eu7rRcdayBTOcvpqK644.asFJs0.cIf3tsMgsjmfItm', 'admin', NULL, '2026-06-25 11:34:03'),
(3, 'Yaqueline Garcia Zapata', 'ygarciaz@zonafrancadepereira.com', '$2y$10$cSaCaRVW4C3GsoQb2o4gYeuRuSimSPEgo0ZPcHghepiDxMw/tuknm', 'operaciones', NULL, '2026-06-25 11:40:35'),
(6, 'Yuliana', 'contacto@zonafrancadepereira.com', '$2y$10$A.IReoQZy1eu7rRcdayBTOcvpqK644.asFJs0.cIf3tsMgsjmfItm', 'usuario', 6, '2026-06-26 11:33:41'),
(7, 'Ejemplo Refinorte', 'ymontoyag@zonafrancadepereira.com', '$2y$10$G5lvYDyvS5hfJpbvON9jM.zflXyNOkfI57f.0iIeL8D3MDU2OppM6', 'usuario', 6, '2026-07-01 10:54:04');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `comites`
--
ALTER TABLE `comites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comite_empresa` (`empresa_id`),
  ADD KEY `fk_comite_creado_por` (`creado_por`);

--
-- Indices de la tabla `comite_compromisos`
--
ALTER TABLE `comite_compromisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comite_id` (`comite_id`);

--
-- Indices de la tabla `compromisos`
--
ALTER TABLE `compromisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comp_comite` (`comite_id`),
  ADD KEY `fk_comp_empresa` (`empresa_id`);

--
-- Indices de la tabla `compromiso_actualizaciones`
--
ALTER TABLE `compromiso_actualizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compromiso` (`compromiso_id`),
  ADD KEY `idx_usuario` (`usuario_id`);

--
-- Indices de la tabla `compromiso_documentos`
--
ALTER TABLE `compromiso_documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compromiso` (`compromiso_id`),
  ADD KEY `idx_subido_por` (`subido_por`),
  ADD KEY `idx_actualizacion` (`actualizacion_id`);

--
-- Indices de la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `requisito_id` (`requisito_id`),
  ADD KEY `subido_por` (`subido_por`);

--
-- Indices de la tabla `empresas`
--
ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nit` (`nit`);

--
-- Indices de la tabla `empresa_alertas`
--
ALTER TABLE `empresa_alertas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_alerta_empresa` (`empresa_id`),
  ADD KEY `fk_alerta_regla` (`regla_alerta_id`);

--
-- Indices de la tabla `empresa_etapa_progreso`
--
ALTER TABLE `empresa_etapa_progreso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_empresa_etapa` (`empresa_id`,`etapa_id`),
  ADD KEY `fk_prog_etapa` (`etapa_id`);

--
-- Indices de la tabla `empresa_indicador`
--
ALTER TABLE `empresa_indicador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ei` (`empresa_id`,`indicador_id`),
  ADD KEY `indicador_id` (`indicador_id`);

--
-- Indices de la tabla `empresa_indicador_valor`
--
ALTER TABLE `empresa_indicador_valor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_eiv` (`empresa_id`,`indicador_id`,`periodo`),
  ADD KEY `indicador_id` (`indicador_id`);

--
-- Indices de la tabla `empresa_requisito_estado`
--
ALTER TABLE `empresa_requisito_estado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_empresa_requisito` (`empresa_id`,`requisito_id`),
  ADD KEY `fk_ereq_requisito` (`requisito_id`),
  ADD KEY `fk_ereq_aprobado_por` (`aprobado_por`);

--
-- Indices de la tabla `empresa_requisito_item_estado`
--
ALTER TABLE `empresa_requisito_item_estado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_empresa_item` (`empresa_id`,`requisito_item_id`),
  ADD KEY `fk_eitem_item` (`requisito_item_id`),
  ADD KEY `fk_eitem_reg_por` (`registrado_por`);

--
-- Indices de la tabla `entidades`
--
ALTER TABLE `entidades`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `etapas`
--
ALTER TABLE `etapas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_etapa_fase` (`fase_id`);

--
-- Indices de la tabla `fases`
--
ALTER TABLE `fases`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reglas_alerta`
--
ALTER TABLE `reglas_alerta`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `reglas_decision`
--
ALTER TABLE `reglas_decision`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `requisitos`
--
ALTER TABLE `requisitos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_req_etapa` (`etapa_id`),
  ADD KEY `fk_req_entidad` (`entidad_id`);

--
-- Indices de la tabla `requisito_items`
--
ALTER TABLE `requisito_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_requisito` (`requisito_id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_usuarios_empresa` (`empresa_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `comites`
--
ALTER TABLE `comites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `comite_compromisos`
--
ALTER TABLE `comite_compromisos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `compromisos`
--
ALTER TABLE `compromisos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `compromiso_actualizaciones`
--
ALTER TABLE `compromiso_actualizaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `compromiso_documentos`
--
ALTER TABLE `compromiso_documentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `documentos`
--
ALTER TABLE `documentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `empresas`
--
ALTER TABLE `empresas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `empresa_alertas`
--
ALTER TABLE `empresa_alertas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `empresa_etapa_progreso`
--
ALTER TABLE `empresa_etapa_progreso`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `empresa_indicador`
--
ALTER TABLE `empresa_indicador`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `empresa_indicador_valor`
--
ALTER TABLE `empresa_indicador_valor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `empresa_requisito_estado`
--
ALTER TABLE `empresa_requisito_estado`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT de la tabla `empresa_requisito_item_estado`
--
ALTER TABLE `empresa_requisito_item_estado`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `entidades`
--
ALTER TABLE `entidades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `etapas`
--
ALTER TABLE `etapas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `fases`
--
ALTER TABLE `fases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `indicadores`
--
ALTER TABLE `indicadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `reglas_alerta`
--
ALTER TABLE `reglas_alerta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `reglas_decision`
--
ALTER TABLE `reglas_decision`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `requisitos`
--
ALTER TABLE `requisitos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `requisito_items`
--
ALTER TABLE `requisito_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comites`
--
ALTER TABLE `comites`
  ADD CONSTRAINT `fk_comite_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_comite_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `comite_compromisos`
--
ALTER TABLE `comite_compromisos`
  ADD CONSTRAINT `comite_compromisos_ibfk_1` FOREIGN KEY (`comite_id`) REFERENCES `comites` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compromisos`
--
ALTER TABLE `compromisos`
  ADD CONSTRAINT `fk_comp_comite` FOREIGN KEY (`comite_id`) REFERENCES `comites` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comp_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `compromiso_actualizaciones`
--
ALTER TABLE `compromiso_actualizaciones`
  ADD CONSTRAINT `fk_compromiso_actualizaciones_compromiso` FOREIGN KEY (`compromiso_id`) REFERENCES `comite_compromisos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_compromiso_actualizaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `compromiso_documentos`
--
ALTER TABLE `compromiso_documentos`
  ADD CONSTRAINT `fk_compromiso_documentos_actualizacion` FOREIGN KEY (`actualizacion_id`) REFERENCES `compromiso_actualizaciones` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_compromiso_documentos_compromiso` FOREIGN KEY (`compromiso_id`) REFERENCES `comite_compromisos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_compromiso_documentos_usuario` FOREIGN KEY (`subido_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `documentos`
--
ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_ibfk_2` FOREIGN KEY (`requisito_id`) REFERENCES `requisitos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_ibfk_3` FOREIGN KEY (`subido_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `empresa_alertas`
--
ALTER TABLE `empresa_alertas`
  ADD CONSTRAINT `fk_alerta_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_alerta_regla` FOREIGN KEY (`regla_alerta_id`) REFERENCES `reglas_alerta` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `empresa_etapa_progreso`
--
ALTER TABLE `empresa_etapa_progreso`
  ADD CONSTRAINT `fk_prog_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_prog_etapa` FOREIGN KEY (`etapa_id`) REFERENCES `etapas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empresa_indicador`
--
ALTER TABLE `empresa_indicador`
  ADD CONSTRAINT `empresa_indicador_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_indicador_ibfk_2` FOREIGN KEY (`indicador_id`) REFERENCES `indicadores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empresa_indicador_valor`
--
ALTER TABLE `empresa_indicador_valor`
  ADD CONSTRAINT `empresa_indicador_valor_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_indicador_valor_ibfk_2` FOREIGN KEY (`indicador_id`) REFERENCES `indicadores` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empresa_requisito_estado`
--
ALTER TABLE `empresa_requisito_estado`
  ADD CONSTRAINT `fk_ereq_aprobado_por` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ereq_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ereq_requisito` FOREIGN KEY (`requisito_id`) REFERENCES `requisitos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `empresa_requisito_item_estado`
--
ALTER TABLE `empresa_requisito_item_estado`
  ADD CONSTRAINT `fk_eitem_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eitem_item` FOREIGN KEY (`requisito_item_id`) REFERENCES `requisito_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eitem_reg_por` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `etapas`
--
ALTER TABLE `etapas`
  ADD CONSTRAINT `fk_etapa_fase` FOREIGN KEY (`fase_id`) REFERENCES `fases` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `requisitos`
--
ALTER TABLE `requisitos`
  ADD CONSTRAINT `fk_req_entidad` FOREIGN KEY (`entidad_id`) REFERENCES `entidades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_req_etapa` FOREIGN KEY (`etapa_id`) REFERENCES `etapas` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `requisito_items`
--
ALTER TABLE `requisito_items`
  ADD CONSTRAINT `fk_item_requisito` FOREIGN KEY (`requisito_id`) REFERENCES `requisitos` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
