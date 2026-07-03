-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generaciÃ³n: 03-07-2026 a las 20:14:20
-- VersiÃ³n del servidor: 10.4.32-MariaDB
-- VersiÃ³n de PHP: 8.2.12

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
-- Este archivo es el volcado de la compaÃ±era (con smtp_config y
-- usuarios.debe_cambiar_contrasena) MÃS lo que agregamos nosotros para
-- Alertas Ejecutivas (usuarios.es_gerente, empresa_alertas.tipo=reunion,
-- empresa_alertas.enlace_reunion, empresa_alertas.comentario_resolucion,
-- y la tabla empresa_alertas_destinatarios). Es la base unificada para
-- que ambos sincronicen sin romper nada.
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

INSERT INTO `comites` (`id`, `empresa_id`, `tipo`, `titulo`, `descripcion`, `fecha`, `lugar`, `estado`, `creado_por`, `creado_en`) VALUES
(3, 6, 'aprobacion', 'Refinorte', 'link reunion', '2026-07-01 11:03:00', 'Virtual', 'programado', 3, '2026-07-01 11:03:31'),
(6, 12, 'seguimiento', 'SEGUIMIENTO', NULL, '2026-07-03 11:39:00', 'Zona franca', 'programado', 3, '2026-07-03 11:39:43');

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

INSERT INTO `comite_compromisos` (`id`, `comite_id`, `descripcion`, `responsable`, `fecha_limite`, `estado`, `observaciones`, `created_at`) VALUES
(6, 3, 'Revisar Informe', 'Ejemplo Refinorte', '2026-07-01', 'cumplido', 'asa', '2026-07-01 11:04:01'),
(7, 3, 'hgfhg', 'Yuliana', '2026-07-09', 'cumplido', 'sdfgsdfgdsfgdfg', '2026-07-02 10:04:55'),
(8, 3, 'revisar', 'Yuliana Melissa Montoya Guapacha', '2026-07-31', 'pendiente', NULL, '2026-07-02 13:20:23'),
(10, 6, 'ASASAS', 'Juliana Tapasco', '2026-07-03', 'pendiente', NULL, '2026-07-03 11:40:02');

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

CREATE TABLE `compromiso_actualizaciones` (
  `id` int(10) UNSIGNED NOT NULL,
  `compromiso_id` int(10) UNSIGNED NOT NULL,
  `estado` enum('pendiente','en_progreso','cumplido','vencido') NOT NULL,
  `observaciones` text DEFAULT NULL,
  `usuario_id` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `compromiso_actualizaciones` (`id`, `compromiso_id`, `estado`, `observaciones`, `usuario_id`, `created_at`) VALUES
(3, 6, 'cumplido', 'asa', NULL, '2026-07-01 15:34:31'),
(4, 7, 'pendiente', 'sdfsdaf', 6, '2026-07-02 10:06:10'),
(5, 7, 'en_progreso', 'fjhgfyy', 6, '2026-07-02 10:06:16'),
(6, 7, 'cumplido', 'sdfgsdfgdsfgdfg', 6, '2026-07-02 10:06:26');

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

INSERT INTO `compromiso_documentos` (`id`, `compromiso_id`, `actualizacion_id`, `nombre_original`, `nombre_guardado`, `tipo_mime`, `tamano`, `subido_por`, `created_at`) VALUES
(4, 6, 3, 'Image.jpg', '6_6a4579d7bdf3a8.77362669.jpg', 'image/jpeg', 1131123, NULL, '2026-07-01 15:34:31'),
(5, 7, 4, 'Image.jpg', '7_6a467e6254a0e7.54389107.jpg', 'image/jpeg', 1131123, 6, '2026-07-02 10:06:10');

CREATE TABLE `documentos` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `requisito_id` int(10) UNSIGNED NOT NULL,
  `nombre_original` varchar(255) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL,
  `nombre_guardado` varchar(255) NOT NULL,
  `tipo_mime` varchar(100) DEFAULT NULL,
  `tamano` int(10) UNSIGNED DEFAULT NULL,
  `subido_por` int(10) UNSIGNED DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `documentos` (`id`, `empresa_id`, `requisito_id`, `nombre_original`, `descripcion`, `nombre_guardado`, `tipo_mime`, `tamano`, `subido_por`, `created_at`) VALUES
(9, 6, 19, 'Propuesta CRM.pdf', NULL, '6_6a466ebab3c412.49002677.pdf', 'application/pdf', 224396, 3, '2026-07-02 08:59:22'),
(10, 6, 19, 'Image.jpg', NULL, '6_6a467228924459.37808706.jpg', 'image/jpeg', 1131123, 3, '2026-07-02 09:14:00'),
(11, 6, 18, 'Image.jpg', 'PRUEBA', '6_6a467936bbf0e9.00665998.jpg', 'image/jpeg', 1131123, 3, '2026-07-02 09:44:06'),
(12, 12, 19, 'bda653e28546c87af0867e976fe34b18.jpg', 'ssss', '12_6a47e8695cd571.20224312.jpg', 'image/jpeg', 329221, 3, '2026-07-03 11:50:49');

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

INSERT INTO `empresas` (`id`, `nit`, `razon_social`, `representante`, `telefono`, `correo`, `contrasena`, `creado_en`) VALUES
(6, '123456789', 'REFINORTE', 'Ejemplo', '3116996990', 'contacto@zonafrancadepereira.com', '$2y$10$5W8PYyqciTNWV1jGJBwhc.u4CXEQ299HyRegPyVb.zYvOVphmKnRi', '2026-07-01 10:51:39'),
(12, '90031158-9', 'BUSTAMANTE SAS', 'Isabel Cristina Bustamante', '606 3343000', 'cbustamante@zonafrancadepereira.com', '$2y$10$rHtnQRCe.MAT4oYuPWbvBeROn7i.bVzt5h.YvAtN09OV4LvWwvug.', '2026-07-03 10:56:41');

CREATE TABLE `empresa_alertas` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `regla_alerta_id` int(10) UNSIGNED DEFAULT NULL,
  `tipo` enum('vencimiento','bloqueo','pendiente','documento','decision','reunion') NOT NULL,
  `mensaje` text NOT NULL,
  `enlace_reunion` varchar(500) DEFAULT NULL,
  `prioridad` enum('alta','media','baja') NOT NULL DEFAULT 'media',
  `resuelta` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_resolucion` datetime DEFAULT NULL,
  `comentario_resolucion` text DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Estructura de tabla para la tabla `empresa_alertas_destinatarios`
-- (agregada: destinatarios especificos de una alerta ejecutiva; si una
-- alerta no tiene filas aqui, se asume difusion por defecto: el/la gerente
-- de la empresa y todo Operaciones/Admin)
--

CREATE TABLE `empresa_alertas_destinatarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `alerta_id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

INSERT INTO `empresa_etapa_progreso` (`id`, `empresa_id`, `etapa_id`, `porcentaje_avance`, `estado`, `fecha_inicio`, `fecha_completado`, `actualizado_en`) VALUES
(25, 6, 5, 50.00, 'en_progreso', '2026-07-02 08:59:38', NULL, '2026-07-02 09:44:29'),
(26, 6, 6, 0.00, 'pendiente', NULL, NULL, '2026-07-01 10:51:39'),
(27, 6, 7, 0.00, 'pendiente', NULL, NULL, '2026-07-01 10:51:39'),
(28, 6, 8, 0.00, 'pendiente', NULL, NULL, '2026-07-01 10:51:39'),
(29, 6, 9, 0.00, 'pendiente', NULL, NULL, '2026-07-01 10:51:39'),
(37, 6, 12, 0.00, 'pendiente', NULL, NULL, '2026-07-01 15:26:01'),
(38, 6, 13, 0.00, 'pendiente', NULL, NULL, '2026-07-01 15:26:01'),
(39, 6, 14, 0.00, 'pendiente', NULL, NULL, '2026-07-01 15:26:01'),
(82, 12, 5, 25.00, 'en_progreso', '2026-07-03 11:50:57', NULL, '2026-07-03 11:50:57'),
(83, 12, 6, 0.00, 'pendiente', NULL, NULL, '2026-07-03 10:56:41'),
(84, 12, 7, 0.00, 'pendiente', NULL, NULL, '2026-07-03 10:56:41'),
(85, 12, 8, 0.00, 'pendiente', NULL, NULL, '2026-07-03 10:56:41'),
(86, 12, 9, 0.00, 'pendiente', NULL, NULL, '2026-07-03 10:56:41'),
(87, 12, 12, 0.00, 'pendiente', NULL, NULL, '2026-07-03 10:56:41'),
(88, 12, 13, 0.00, 'pendiente', NULL, NULL, '2026-07-03 10:56:41'),
(89, 12, 14, 0.00, 'pendiente', NULL, NULL, '2026-07-03 10:56:41');

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

INSERT INTO `empresa_indicador` (`id`, `empresa_id`, `indicador_id`, `valor_actual`, `fecha_reporte`, `observaciones`, `registrado_por`, `updated_at`) VALUES
(1, 6, 1, 50.00, '2026-04-01', NULL, 3, '2026-07-01 20:56:00');

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

INSERT INTO `empresa_indicador_valor` (`id`, `empresa_id`, `indicador_id`, `periodo`, `valor`, `observaciones`, `registrado_por`, `created_at`) VALUES
(1, 6, 1, '2026-04', 30.00, NULL, 3, '2026-07-01 21:00:12'),
(3, 6, 1, '2026-05', 60.00, NULL, 3, '2026-07-01 21:12:55'),
(4, 6, 1, '2026-06', 100.00, NULL, 3, '2026-07-01 21:15:12'),
(5, 6, 1, '2026-07', 10.00, NULL, 3, '2026-07-01 21:15:30'),
(6, 6, 1, '2024-08', 80.00, NULL, 3, '2026-07-01 21:15:53'),
(7, 6, 1, '2024-09', 100.00, NULL, 3, '2026-07-01 21:39:28'),
(8, 6, 1, '2024-10', 100.00, NULL, 3, '2026-07-01 21:39:46'),
(9, 6, 1, '2024-11', 80.00, NULL, 3, '2026-07-01 21:40:02');

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

CREATE TABLE `empresa_requisito_historial` (
  `id` int(10) UNSIGNED NOT NULL,
  `empresa_id` int(10) UNSIGNED NOT NULL,
  `requisito_id` int(10) UNSIGNED NOT NULL,
  `estado_anterior` varchar(20) DEFAULT NULL,
  `estado_nuevo` varchar(20) NOT NULL,
  `observaciones` text DEFAULT NULL,
  `fecha_cumplimiento` datetime DEFAULT NULL,
  `documento_id` int(10) UNSIGNED DEFAULT NULL,
  `registrado_por` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `entidades` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `entidades` (`id`, `nombre`, `descripcion`, `activo`, `creado_en`) VALUES
(1, 'DIAN', 'DirecciÃ³n de Impuestos y Aduanas Nacionales', 1, '2026-06-25 16:06:33'),
(2, 'MINCIT', 'Ministerio de Comercio, Industria y Turismo', 1, '2026-06-25 16:06:33'),
(3, 'Usuario Operador', 'Operador de la Zona Franca', 1, '2026-06-25 16:06:33'),
(6, 'Firma de Auditoria Externa', 'Entidad contratada para el proceso de auditorias', 1, '2026-07-01 14:35:29');

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

INSERT INTO `etapas` (`id`, `fase_id`, `nombre`, `descripcion`, `orden`, `peso_porcentual`, `activo`, `creado_en`) VALUES
(5, 1, 'Garantia', NULL, 1, 12.50, 1, '2026-06-26 11:18:39'),
(6, 1, 'Cerramiento Provisional', NULL, 2, 12.50, 1, '2026-06-26 11:19:09'),
(7, 1, 'Control frentes de Obra', NULL, 3, 12.50, 1, '2026-06-26 11:19:26'),
(8, 1, 'Manuales y Procedimientos', NULL, 4, 12.50, 1, '2026-06-26 11:19:51'),
(9, 2, 'Sistema de control de inventarios', NULL, 5, 12.50, 1, '2026-06-26 12:03:21'),
(12, 2, 'ActivaciÃ³n de Proceso Productivo', NULL, 6, 12.50, 1, '2026-07-01 14:32:21'),
(13, 2, 'NotificaciÃ³n al MINCIT Puesta en marcha', NULL, 7, 12.50, 1, '2026-07-01 14:32:37'),
(14, 2, 'Seguimiento Compromisos', NULL, 8, 12.50, 1, '2026-07-01 14:33:34');

CREATE TABLE `fases` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(150) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT 0,
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `fases` (`id`, `nombre`, `descripcion`, `orden`, `activo`, `created_at`) VALUES
(1, 'AVANCE DEL PROYECTO - ETAPA PREOPERATIVA', 'AdministraciÃ³n y seguimiento de las actividades preoperativas de la Zona Franca Permanente Especial, bajo la supervisiÃ³n del Usuario Operador Zona Franca Internacional de Pereira, garantizando el cumplimiento de los requisitos normativos, operativos y de infraestructura necesarios para su entrada en operaciÃ³n.', 1, 1, '2026-06-26 12:00:46'),
(2, 'AVANCE DEL PROYECTO - ETAPA OPERATIVA', 'AdministraciÃ³n y control de las operaciones de la Zona Franca Permanente Especial, bajo la supervisiÃ³n del Usuario Operador Zona Franca Internacional de Pereira, garantizando el cumplimiento de los requisitos aduaneros, operativos y normativos para el desarrollo eficiente de las actividades autorizadas dentro del rÃ©gimen franco.', 2, 1, '2026-06-26 12:01:43');

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

INSERT INTO `indicadores` (`id`, `nombre`, `descripcion`, `unidad`, `meta`, `periodicidad`, `tipo_grafico`, `comparativo_anual`, `activo`, `created_at`) VALUES
(1, 'Nivel de servicio del operadorÃ¢', NULL, '%', 100.00, 'mensual', 'barra', 1, 1, '2026-07-01 20:55:29');

CREATE TABLE `notificaciones_leidas` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario_id` int(10) UNSIGNED NOT NULL,
  `clave` varchar(64) NOT NULL,
  `leido_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

CREATE TABLE `reglas_decision` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(200) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('bloqueo_etapa','vencimiento','dependencia_gerencia','afecta_dian','afecta_mincit','impide_operativo','impide_preoperativo','documento_critico') NOT NULL,
  `prioridad` enum('alta','media','baja') NOT NULL DEFAULT 'media',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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

INSERT INTO `requisitos` (`id`, `etapa_id`, `entidad_id`, `nombre`, `descripcion`, `obligatorio`, `requiere_documento`, `requiere_fecha_vencimiento`, `requiere_aprobacion`, `peso_porcentual`, `responsable`, `alerta_asociada`, `accion_recomendada`, `activo`, `creado_en`) VALUES
(8, 6, 3, 'Respuesta MINCIT', NULL, 1, 1, 0, 0, 33.33, 'Operaciones', NULL, NULL, 1, '2026-06-26 11:22:45'),
(9, 6, 3, 'NotificaciÃ³n de implementaciÃ³n del cerramiento', NULL, 1, 1, 0, 0, 33.33, 'Operaciones', NULL, NULL, 1, '2026-06-26 11:23:27'),
(10, 6, 3, 'Posible Visita MINCIT', NULL, 0, 0, 0, 0, 33.33, 'Operaciones', NULL, NULL, 1, '2026-06-26 11:23:54'),
(11, 7, 3, 'Detalle Del Cronograma De EjecuciÃ³n De Obra.', NULL, 1, 1, 0, 0, 33.33, 'Operaciones', NULL, NULL, 1, '2026-06-26 11:24:26'),
(12, 7, 3, 'Control operativo de materiales.', NULL, 1, 0, 0, 0, 33.33, 'Operaciones', NULL, NULL, 1, '2026-06-26 11:24:52'),
(13, 7, 3, 'BÃ¡sculas', '', 1, 1, 0, 0, 33.33, 'Operaciones', NULL, NULL, 1, '2026-06-26 11:25:22'),
(14, 8, 3, 'ElaboraciÃ³n e implementaciÃ³n', '', 1, 1, 1, 1, 100.00, 'Operaciones', '', '', 1, '2026-06-26 11:25:48'),
(16, 5, 1, 'RadicaciÃ³n virtual', NULL, 1, 1, 0, 0, 25.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:28:17'),
(17, 5, 1, 'EvaluaciÃ³n Dian', NULL, 1, 1, 0, 0, 25.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:29:06'),
(18, 5, 1, 'Observaciones y subsanaciÃ³n de informaciÃ³n', NULL, 1, 1, 0, 0, 25.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:29:55'),
(19, 5, 1, 'AceptaciÃ³n', NULL, 1, 1, 0, 0, 25.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:31:23'),
(20, 14, 2, 'Reporte Trimestral : sobre estado de avance en la ejecuciÃ³n del plan maestro de desarrollo', NULL, 1, 1, 0, 0, 100.00, 'Usuario Operador y ZFPE', NULL, NULL, 1, '2026-07-01 14:34:34'),
(21, 14, 6, 'Auditoria', NULL, 1, 1, 0, 0, 100.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:36:03'),
(22, 9, 3, 'Capacitaciones-Montaje- puesta en marcha- interoperabilidad DIAN.', NULL, 1, 1, 0, 0, 100.00, 'Usuario Operador', NULL, NULL, 1, '2026-07-01 14:48:18'),
(23, 12, 3, 'ActivaciÃ³n de proceso productivo', NULL, 1, 0, 0, 0, 100.00, 'Usuario Operador y ZFPE', NULL, NULL, 1, '2026-07-01 14:49:25'),
(24, 13, 2, 'Primera facturaciÃ³n', 'Enviar al MINCIT', 1, 1, 0, 0, 100.00, 'Usuario Operador y ZFPE', NULL, NULL, 1, '2026-07-01 14:53:27');

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

CREATE TABLE `smtp_config` (
  `id` int(10) UNSIGNED NOT NULL,
  `host` varchar(255) NOT NULL,
  `puerto` smallint(5) UNSIGNED NOT NULL DEFAULT 587,
  `usuario` varchar(255) NOT NULL,
  `clave_cifrada` varchar(500) NOT NULL,
  `cifrado` enum('tls','ssl') NOT NULL DEFAULT 'tls',
  `correo_remitente` varchar(255) NOT NULL,
  `nombre_remitente` varchar(255) NOT NULL DEFAULT '',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `actualizado_por` int(10) UNSIGNED DEFAULT NULL,
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `smtp_config` (`id`, `host`, `puerto`, `usuario`, `clave_cifrada`, `cifrado`, `correo_remitente`, `nombre_remitente`, `activo`, `actualizado_por`, `actualizado_en`) VALUES
(3, 'smtp.hostinger.com', 465, 'noreply@zonafrancaespecial.zonafrancadepereira.com', 'EEv9CEhzSuo/uKvOSbHsmVEk9Vo6ptQriJcz1JjbmwU=', 'ssl', 'noreply@zonafrancaespecial.zonafrancadepereira.com', 'ZONA FRANCA INTERNACIONAL DE PEREIRA', 1, 1, '2026-07-03 17:19:03');

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `rol` enum('admin','operaciones','usuario') NOT NULL DEFAULT 'usuario',
  `debe_cambiar_contrasena` tinyint(1) NOT NULL DEFAULT 0,
  `empresa_id` int(10) UNSIGNED DEFAULT NULL,
  `es_gerente` tinyint(1) NOT NULL DEFAULT 0,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `usuarios` (`id`, `nombre`, `correo`, `contrasena`, `rol`, `debe_cambiar_contrasena`, `empresa_id`, `creado_en`) VALUES
(1, 'Administrador', 'admin@zfipe.com', '$2y$10$A.IReoQZy1eu7rRcdayBTOcvpqK644.asFJs0.cIf3tsMgsjmfItm', 'admin', 0, NULL, '2026-06-25 11:34:03'),
(3, 'Yaqueline Garcia Zapata', 'ygarciaz@zonafrancadepereira.com', '$2y$10$cSaCaRVW4C3GsoQb2o4gYeuRuSimSPEgo0ZPcHghepiDxMw/tuknm', 'operaciones', 0, NULL, '2026-06-25 11:40:35'),
(6, 'Yuliana Melissa Montoya Guapacha', 'contacto@zonafrancadepereira.com', '$2y$10$zZU5ZgrkTiHsokWny40lQ.xuX1zHBfxa2Zbxuli5P7/6SCD2kzWR2', 'usuario', 0, 6, '2026-06-26 11:33:41'),
(22, 'BUSTAMANTE SAS', 'cbustamante@zonafrancadepereira.com', '$2y$10$ilLuk7nJhQL0T/ARLEUSyulBgSsLN7bZTFyfzQpIsAhUAol5iqg9y', 'usuario', 0, 12, '2026-07-03 10:56:41'),
(25, 'Juliana Tapasco', 'ygarciazapata62@gmail.com', '$2y$10$FsuYwNtVwRojjoUTZLm.7eRmagOESyWBIdnkLl1oewiY3gewfrr72', 'usuario', 1, 12, '2026-07-03 11:08:24');

--
-- Ãndices para tablas volcadas
--

ALTER TABLE `comites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comite_empresa` (`empresa_id`),
  ADD KEY `fk_comite_creado_por` (`creado_por`);

ALTER TABLE `comite_compromisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `comite_id` (`comite_id`);

ALTER TABLE `compromisos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_comp_comite` (`comite_id`),
  ADD KEY `fk_comp_empresa` (`empresa_id`);

ALTER TABLE `compromiso_actualizaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compromiso` (`compromiso_id`),
  ADD KEY `idx_usuario` (`usuario_id`);

ALTER TABLE `compromiso_documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_compromiso` (`compromiso_id`),
  ADD KEY `idx_subido_por` (`subido_por`),
  ADD KEY `idx_actualizacion` (`actualizacion_id`);

ALTER TABLE `documentos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `empresa_id` (`empresa_id`),
  ADD KEY `requisito_id` (`requisito_id`),
  ADD KEY `subido_por` (`subido_por`);

ALTER TABLE `empresas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nit` (`nit`);

ALTER TABLE `empresa_alertas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_alerta_empresa` (`empresa_id`),
  ADD KEY `fk_alerta_regla` (`regla_alerta_id`);

ALTER TABLE `empresa_alertas_destinatarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_alerta_usuario` (`alerta_id`,`usuario_id`),
  ADD KEY `fk_dest_usuario` (`usuario_id`);

ALTER TABLE `empresa_etapa_progreso`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_empresa_etapa` (`empresa_id`,`etapa_id`),
  ADD KEY `fk_prog_etapa` (`etapa_id`);

ALTER TABLE `empresa_indicador`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_ei` (`empresa_id`,`indicador_id`),
  ADD KEY `indicador_id` (`indicador_id`);

ALTER TABLE `empresa_indicador_valor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_eiv` (`empresa_id`,`indicador_id`,`periodo`),
  ADD KEY `indicador_id` (`indicador_id`);

ALTER TABLE `empresa_requisito_estado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_empresa_requisito` (`empresa_id`,`requisito_id`),
  ADD KEY `fk_ereq_requisito` (`requisito_id`),
  ADD KEY `fk_ereq_aprobado_por` (`aprobado_por`);

ALTER TABLE `empresa_requisito_historial`
  ADD PRIMARY KEY (`id`),
  ADD KEY `requisito_id` (`requisito_id`),
  ADD KEY `documento_id` (`documento_id`),
  ADD KEY `registrado_por` (`registrado_por`),
  ADD KEY `idx_erh_empresa_requisito` (`empresa_id`,`requisito_id`);

ALTER TABLE `empresa_requisito_item_estado`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_empresa_item` (`empresa_id`,`requisito_item_id`),
  ADD KEY `fk_eitem_item` (`requisito_item_id`),
  ADD KEY `fk_eitem_reg_por` (`registrado_por`);

ALTER TABLE `entidades`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `etapas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_etapa_fase` (`fase_id`);

ALTER TABLE `fases`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `indicadores`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `notificaciones_leidas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_usuario_clave` (`usuario_id`,`clave`);

ALTER TABLE `reglas_alerta`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `reglas_decision`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `requisitos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_req_etapa` (`etapa_id`),
  ADD KEY `fk_req_entidad` (`entidad_id`);

ALTER TABLE `requisito_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_item_requisito` (`requisito_id`);

ALTER TABLE `smtp_config`
  ADD PRIMARY KEY (`id`),
  ADD KEY `actualizado_por` (`actualizado_por`);

ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `correo` (`correo`),
  ADD KEY `fk_usuarios_empresa` (`empresa_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

ALTER TABLE `comites`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `comite_compromisos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;
ALTER TABLE `compromisos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `compromiso_actualizaciones`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `compromiso_documentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `documentos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
ALTER TABLE `empresas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
ALTER TABLE `empresa_alertas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `empresa_alertas_destinatarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `empresa_etapa_progreso`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;
ALTER TABLE `empresa_indicador`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `empresa_indicador_valor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
ALTER TABLE `empresa_requisito_estado`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;
ALTER TABLE `empresa_requisito_historial`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
ALTER TABLE `empresa_requisito_item_estado`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;
ALTER TABLE `entidades`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `etapas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `fases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
ALTER TABLE `indicadores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
ALTER TABLE `notificaciones_leidas`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
ALTER TABLE `reglas_alerta`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `reglas_decision`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;
ALTER TABLE `requisitos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
ALTER TABLE `requisito_items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
ALTER TABLE `smtp_config`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Restricciones para tablas volcadas
--

ALTER TABLE `comites`
  ADD CONSTRAINT `fk_comite_creado_por` FOREIGN KEY (`creado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_comite_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

ALTER TABLE `comite_compromisos`
  ADD CONSTRAINT `comite_compromisos_ibfk_1` FOREIGN KEY (`comite_id`) REFERENCES `comites` (`id`) ON DELETE CASCADE;

ALTER TABLE `compromisos`
  ADD CONSTRAINT `fk_comp_comite` FOREIGN KEY (`comite_id`) REFERENCES `comites` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_comp_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE;

ALTER TABLE `compromiso_actualizaciones`
  ADD CONSTRAINT `fk_compromiso_actualizaciones_compromiso` FOREIGN KEY (`compromiso_id`) REFERENCES `comite_compromisos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_compromiso_actualizaciones_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `compromiso_documentos`
  ADD CONSTRAINT `fk_compromiso_documentos_actualizacion` FOREIGN KEY (`actualizacion_id`) REFERENCES `compromiso_actualizaciones` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_compromiso_documentos_compromiso` FOREIGN KEY (`compromiso_id`) REFERENCES `comite_compromisos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_compromiso_documentos_usuario` FOREIGN KEY (`subido_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `documentos`
  ADD CONSTRAINT `documentos_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_ibfk_2` FOREIGN KEY (`requisito_id`) REFERENCES `requisitos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `documentos_ibfk_3` FOREIGN KEY (`subido_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `empresa_alertas`
  ADD CONSTRAINT `fk_alerta_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_alerta_regla` FOREIGN KEY (`regla_alerta_id`) REFERENCES `reglas_alerta` (`id`) ON DELETE SET NULL;

ALTER TABLE `empresa_alertas_destinatarios`
  ADD CONSTRAINT `fk_dest_alerta` FOREIGN KEY (`alerta_id`) REFERENCES `empresa_alertas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dest_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `empresa_etapa_progreso`
  ADD CONSTRAINT `fk_prog_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_prog_etapa` FOREIGN KEY (`etapa_id`) REFERENCES `etapas` (`id`) ON DELETE CASCADE;

ALTER TABLE `empresa_indicador`
  ADD CONSTRAINT `empresa_indicador_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_indicador_ibfk_2` FOREIGN KEY (`indicador_id`) REFERENCES `indicadores` (`id`) ON DELETE CASCADE;

ALTER TABLE `empresa_indicador_valor`
  ADD CONSTRAINT `empresa_indicador_valor_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_indicador_valor_ibfk_2` FOREIGN KEY (`indicador_id`) REFERENCES `indicadores` (`id`) ON DELETE CASCADE;

ALTER TABLE `empresa_requisito_estado`
  ADD CONSTRAINT `fk_ereq_aprobado_por` FOREIGN KEY (`aprobado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_ereq_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_ereq_requisito` FOREIGN KEY (`requisito_id`) REFERENCES `requisitos` (`id`) ON DELETE CASCADE;

ALTER TABLE `empresa_requisito_historial`
  ADD CONSTRAINT `empresa_requisito_historial_ibfk_1` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_requisito_historial_ibfk_2` FOREIGN KEY (`requisito_id`) REFERENCES `requisitos` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `empresa_requisito_historial_ibfk_3` FOREIGN KEY (`documento_id`) REFERENCES `documentos` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `empresa_requisito_historial_ibfk_4` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `empresa_requisito_item_estado`
  ADD CONSTRAINT `fk_eitem_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eitem_item` FOREIGN KEY (`requisito_item_id`) REFERENCES `requisito_items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_eitem_reg_por` FOREIGN KEY (`registrado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `etapas`
  ADD CONSTRAINT `fk_etapa_fase` FOREIGN KEY (`fase_id`) REFERENCES `fases` (`id`) ON DELETE SET NULL;

ALTER TABLE `notificaciones_leidas`
  ADD CONSTRAINT `notificaciones_leidas_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE;

ALTER TABLE `requisitos`
  ADD CONSTRAINT `fk_req_entidad` FOREIGN KEY (`entidad_id`) REFERENCES `entidades` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_req_etapa` FOREIGN KEY (`etapa_id`) REFERENCES `etapas` (`id`) ON DELETE CASCADE;

ALTER TABLE `requisito_items`
  ADD CONSTRAINT `fk_item_requisito` FOREIGN KEY (`requisito_id`) REFERENCES `requisitos` (`id`) ON DELETE CASCADE;

ALTER TABLE `smtp_config`
  ADD CONSTRAINT `smtp_config_ibfk_1` FOREIGN KEY (`actualizado_por`) REFERENCES `usuarios` (`id`) ON DELETE SET NULL;

ALTER TABLE `usuarios`
  ADD CONSTRAINT `fk_usuarios_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
