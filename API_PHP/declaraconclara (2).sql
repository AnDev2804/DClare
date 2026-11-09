-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 24-04-2026 a las 00:06:12
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `declaraconclara`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anti_islr`
--

CREATE TABLE `anti_islr` (
  `ID` int(11) NOT NULL,
  `enero` varchar(11) DEFAULT NULL,
  `febrero` varchar(11) DEFAULT NULL,
  `marzo` varchar(11) DEFAULT NULL,
  `abril` varchar(11) DEFAULT NULL,
  `mayo` varchar(11) DEFAULT NULL,
  `junio` varchar(11) DEFAULT NULL,
  `julio` varchar(11) DEFAULT NULL,
  `agosto` varchar(11) DEFAULT NULL,
  `septiembre` varchar(11) DEFAULT NULL,
  `octubre` varchar(11) DEFAULT NULL,
  `noviembre` varchar(11) DEFAULT NULL,
  `diciembre` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `anti_islr`
--

INSERT INTO `anti_islr` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, '15;28', '9;20', '6;25', '1;23', '6;20', '12;29', '8;27', '14;31', '14;29', '5;20', '13;27', '3;16'),
(1, '6;19', '10;23', '3;20', '14;27', '4;18', '11;26', '3;21', '13;25', '3;18', '14;28', '12;26', '15;29'),
(2, '8;21', '5;18', '9;24', '8;21', '14;29', '3;16', '14;30', '12;24', '10;24', '15;29', '2;17', '4;21'),
(3, '16;30', '12;18', '4;23', '16;30', '7;22', '10;18', '7;23', '5;18', '2;21', '7;23', '9;23', '11;28'),
(4, '9;23', '2;25', '11;26', '7;20', '13;21', '2;19', '10;28', '6;19', '9;30', '6;22', '5;20', '7;22'),
(5, '5;22', '13;27', '12;30', '9;22', '15;28', '8;17', '6;22', '3;21', '15;25', '8;30', '4;18', '10;17'),
(6, '13;20', '4;19', '10;27', '13;24', '5;19', '15;30', '9;20', '4;28', '11;28', '2;21', '11;25', '8;18'),
(7, '12;27', '11;24', '2;18', '6;17', '11;26', '4;22', '15;31', '10;20', '4;22', '13;27', '3;19', '2;18'),
(8, '7;26', '3;26', '13;31', '10;29', '12;27', '5;23', '2;17', '7;26', '8;17', '9;26', '6;24', '9;30'),
(9, '14;29', '6;27', '5;17', '15;28', '8;25', '9;25', '13;29', '11;27', '7;23', '1;19', '10;30', '14;23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dd_islr`
--

CREATE TABLE `dd_islr` (
  `ID` int(11) NOT NULL,
  `enero` int(11) DEFAULT NULL,
  `febrero` int(11) DEFAULT NULL,
  `marzo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `dd_islr`
--

INSERT INTO `dd_islr` (`ID`, `enero`, `febrero`, `marzo`) VALUES
(0, NULL, NULL, 6),
(1, NULL, NULL, 11),
(2, 30, NULL, NULL),
(3, 30, NULL, NULL),
(4, NULL, NULL, 11),
(5, NULL, 27, NULL),
(6, NULL, NULL, 16),
(7, NULL, NULL, 16),
(8, NULL, NULL, 6),
(9, NULL, 27, NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dec_auto_entdes`
--

CREATE TABLE `dec_auto_entdes` (
  `ID` int(11) NOT NULL,
  `enero` int(11) DEFAULT NULL,
  `febrero` int(11) DEFAULT NULL,
  `marzo` int(11) DEFAULT NULL,
  `abril` int(11) DEFAULT NULL,
  `mayo` int(11) DEFAULT NULL,
  `junio` int(11) DEFAULT NULL,
  `julio` int(11) DEFAULT NULL,
  `agosto` int(11) DEFAULT NULL,
  `septiembre` int(11) DEFAULT NULL,
  `octubre` int(11) DEFAULT NULL,
  `noviembre` int(11) DEFAULT NULL,
  `diciembre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `dec_auto_entdes`
--

INSERT INTO `dec_auto_entdes` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, 15, 9, 13, 10, 12, 12, 8, 14, 8, 9, 13, 9),
(1, 9, 10, 11, 14, 13, 11, 10, 13, 9, 14, 12, 15),
(2, 8, 12, 9, 8, 14, 10, 14, 12, 10, 15, 9, 11),
(3, 8, 12, 9, 8, 14, 10, 14, 12, 10, 15, 9, 11),
(4, 9, 10, 11, 14, 13, 11, 10, 13, 9, 14, 12, 15),
(5, 14, 13, 12, 9, 15, 9, 13, 11, 15, 8, 10, 10),
(6, 13, 11, 10, 13, 11, 15, 9, 10, 11, 13, 11, 8),
(7, 13, 11, 10, 13, 11, 15, 9, 10, 11, 13, 11, 8),
(8, 15, 9, 13, 10, 12, 12, 8, 14, 8, 9, 13, 9),
(9, 14, 13, 12, 9, 15, 9, 13, 11, 15, 8, 10, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dec_def_islrei`
--

CREATE TABLE `dec_def_islrei` (
  `ID` int(11) NOT NULL,
  `enero` int(11) DEFAULT NULL,
  `febrero` int(11) DEFAULT NULL,
  `marzo` int(11) DEFAULT NULL,
  `abril` int(11) DEFAULT NULL,
  `mayo` int(11) DEFAULT NULL,
  `junio` int(11) DEFAULT NULL,
  `julio` int(11) DEFAULT NULL,
  `agosto` int(11) DEFAULT NULL,
  `septiembre` int(11) DEFAULT NULL,
  `octubre` int(11) DEFAULT NULL,
  `noviembre` int(11) DEFAULT NULL,
  `diciembre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `dec_def_islrei`
--

INSERT INTO `dec_def_islrei` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, 26, 20, NULL, 23, 20, 23, 17, 26, 17, 20, 24, 16),
(1, 23, 23, NULL, 27, 21, 19, 21, 25, 18, 22, 20, 22),
(2, 21, 18, NULL, 21, 22, 18, 23, 24, 21, 23, 17, 21),
(3, 21, 18, NULL, 21, 22, 18, 23, 24, 21, 23, 17, 21),
(4, 23, 23, NULL, 27, 21, 19, 21, 25, 18, 22, 20, 22),
(5, 22, 19, NULL, 22, 25, 17, 22, 21, 23, 19, 18, 17),
(6, 27, 24, NULL, 24, 19, 22, 20, 20, 22, 21, 19, 18),
(7, 27, 24, NULL, 24, 19, 22, 20, 20, 22, 21, 19, 18),
(8, 26, 20, NULL, 23, 20, 23, 17, 26, 17, 20, 24, 16),
(9, 22, 19, NULL, 22, 25, 17, 22, 21, 23, 19, 18, 17);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dec_gra_patri`
--

CREATE TABLE `dec_gra_patri` (
  `ID` int(11) NOT NULL,
  `octubre` int(11) DEFAULT NULL,
  `noviembre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `dec_gra_patri`
--

INSERT INTO `dec_gra_patri` (`ID`, `octubre`, `noviembre`) VALUES
(0, 9, 13),
(1, 14, 12),
(2, 15, 9),
(3, 15, 9),
(4, 14, 12),
(5, 8, 10),
(6, 13, 11),
(7, 13, 11),
(8, 9, 13),
(9, 8, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dec_igtf`
--

CREATE TABLE `dec_igtf` (
  `ID` int(11) NOT NULL,
  `enero` varchar(11) DEFAULT NULL,
  `febrero` varchar(11) DEFAULT NULL,
  `marzo` varchar(11) DEFAULT NULL,
  `abril` varchar(11) DEFAULT NULL,
  `mayo` varchar(11) DEFAULT NULL,
  `junio` varchar(11) DEFAULT NULL,
  `julio` varchar(11) DEFAULT NULL,
  `agosto` varchar(11) DEFAULT NULL,
  `septiembre` varchar(11) DEFAULT NULL,
  `octubre` varchar(11) DEFAULT NULL,
  `noviembre` varchar(11) DEFAULT NULL,
  `diciembre` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `dec_igtf`
--

INSERT INTO `dec_igtf` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, '15;28', '9;20', '6;25', '1;23', '6;20', '12;29', '8;27', '14;31', '14;29', '5;20', '13;27', '3;16'),
(1, '6;19', '10;23', '3;20', '14;27', '4;18', '11;26', '3;21', '13;25', '3;18', '14;28', '12;26', '15;29'),
(2, '8;21', '5;18', '9;24', '8;21', '14;29', '3;16', '14;30', '12;24', '10;24', '15;29', '2;17', '4;21'),
(3, '16;30', '12;18', '4;23', '16;30', '7;22', '10;18', '7;23', '5;18', '2;21', '7;23', '9;23', '11;28'),
(4, '9;23', '2;25', '11;26', '7;20', '13;21', '2;19', '10;28', '6;19', '9;30', '6;22', '5;20', '7;22'),
(5, '5;22', '13;27', '12;30', '9;22', '15;28', '8;17', '6;22', '3;21', '15;25', '8;30', '4;18', '10;17'),
(6, '13;20', '4;19', '10;27', '13;24', '5;19', '15;30', '9;20', '4;28', '11;28', '2;21', '11;25', '8;18'),
(7, '12;27', '11;24', '2;18', '6;17', '11;26', '4;22', '15;31', '10;20', '4;22', '13;27', '3;19', '2;18'),
(8, '7;26', '3;26', '13;31', '10;29', '12;27', '5;23', '2;17', '7;26', '8;17', '9;26', '6;24', '9;30'),
(9, '14;29', '6;27', '5;17', '15;28', '8;25', '9;25', '13;29', '11;27', '7;23', '1;19', '10;30', '14;23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dec_iva`
--

CREATE TABLE `dec_iva` (
  `ID` int(11) NOT NULL,
  `enero` varchar(225) DEFAULT NULL,
  `febrero` varchar(225) DEFAULT NULL,
  `marzo` varchar(225) DEFAULT NULL,
  `abril` varchar(225) DEFAULT NULL,
  `mayo` varchar(225) DEFAULT NULL,
  `junio` varchar(225) DEFAULT NULL,
  `julio` varchar(225) DEFAULT NULL,
  `agosto` varchar(225) DEFAULT NULL,
  `septiembre` varchar(225) DEFAULT NULL,
  `octubre` varchar(225) DEFAULT NULL,
  `noviembre` varchar(225) DEFAULT NULL,
  `diciembre` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `dec_iva`
--

INSERT INTO `dec_iva` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, '15', '9', '13', '10', '12', '12', '8', '14', '8', '9', '13', '9'),
(1, '9', '10', '11', '14', '13', '11', '10', '13', '9', '14', '12', '15'),
(2, '8', '12', '9', '8', '14', '10', '14', '12', '10', '15', '9', '11'),
(3, '8', '12', '9', '8', '14', '10', '14', '12', '10', '15', '9', '11'),
(4, '9', '10', '11', '14', '13', '11', '10', '13', '9', '14', '12', '15'),
(5, '14', '13', '12', '9', '15', '9', '13', '11', '15', '8', '10', '10'),
(6, '13', '11', '10', '13', '11', '15', '9', '10', '11', '13', '11', '8'),
(7, '13', '11', '10', '13', '11', '15', '9', '10', '11', '13', '11', '8'),
(8, '15', '9', '13', '10', '12', '12', '8', '14', '8', '9', '13', '9'),
(9, '14', '13', '12', '9', '15', '9', '13', '11', '15', '8', '10', '10');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `dec_prot_pen`
--

CREATE TABLE `dec_prot_pen` (
  `ID` int(11) NOT NULL,
  `junio` int(11) DEFAULT NULL,
  `julio` int(11) DEFAULT NULL,
  `agosto` int(11) DEFAULT NULL,
  `septiembre` int(11) DEFAULT NULL,
  `octubre` int(11) DEFAULT NULL,
  `noviembre` int(11) DEFAULT NULL,
  `diciembre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `dec_prot_pen`
--

INSERT INTO `dec_prot_pen` (`ID`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, 7, 3, 14, 9, 2, 15, 9),
(1, 5, 1, 6, 10, 4, 14, 13),
(2, 12, 8, 2, 4, 7, 11, 15),
(3, 6, 15, 12, 11, 14, 1, 12),
(4, 13, 11, 13, 3, 11, 6, 6),
(5, 18, 4, 15, 6, 8, 12, 2),
(6, 10, 2, 8, 5, 3, 13, 3),
(7, 4, 10, 5, 12, 10, 5, 11),
(8, 14, 9, 7, 2, 9, 8, 4),
(9, 11, 12, 9, 13, 15, 7, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `envite_azar`
--

CREATE TABLE `envite_azar` (
  `ID` int(11) NOT NULL,
  `enero` int(11) DEFAULT NULL,
  `febrero` int(11) DEFAULT NULL,
  `marzo` int(11) DEFAULT NULL,
  `abril` int(11) DEFAULT NULL,
  `mayo` int(11) DEFAULT NULL,
  `junio` int(11) DEFAULT NULL,
  `julio` int(11) DEFAULT NULL,
  `agosto` int(11) DEFAULT NULL,
  `septiembre` int(11) DEFAULT NULL,
  `octubre` int(11) DEFAULT NULL,
  `noviembre` int(11) DEFAULT NULL,
  `diciembre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `envite_azar`
--

INSERT INTO `envite_azar` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9),
(1, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9),
(2, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9),
(3, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9),
(4, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9),
(5, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9),
(6, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9),
(7, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9),
(8, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9),
(9, 9, 9, 9, 8, 11, 9, 9, 10, 8, 8, 10, 9);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `est_islr_rent`
--

CREATE TABLE `est_islr_rent` (
  `ID` int(11) NOT NULL,
  `enero` int(11) DEFAULT NULL,
  `febrero` int(11) DEFAULT NULL,
  `marzo` int(11) DEFAULT NULL,
  `abril` int(11) DEFAULT NULL,
  `mayo` int(11) DEFAULT NULL,
  `junio` int(11) DEFAULT NULL,
  `julio` int(11) DEFAULT NULL,
  `agosto` int(11) DEFAULT NULL,
  `septiembre` int(11) DEFAULT NULL,
  `octubre` int(11) DEFAULT NULL,
  `noviembre` int(11) DEFAULT NULL,
  `diciembre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `est_islr_rent`
--

INSERT INTO `est_islr_rent` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, 15, 9, 13, 10, 12, 12, 8, 14, 8, 9, 13, 9),
(1, 9, 10, 11, 14, 13, 11, 10, 13, 9, 14, 12, 15),
(2, 8, 12, 9, 8, 14, 10, 14, 12, 10, 15, 9, 11),
(3, 8, 12, 9, 8, 14, 10, 14, 12, 10, 15, 9, 11),
(4, 9, 10, 11, 14, 13, 11, 10, 13, 9, 14, 12, 15),
(5, 14, 13, 12, 9, 15, 9, 13, 11, 15, 8, 10, 10),
(6, 13, 11, 10, 13, 11, 15, 9, 10, 11, 13, 11, 8),
(7, 13, 11, 10, 13, 11, 15, 9, 10, 11, 13, 11, 8),
(8, 15, 9, 13, 10, 12, 12, 8, 14, 8, 9, 13, 9),
(9, 14, 13, 12, 9, 15, 9, 13, 11, 15, 8, 10, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infoper`
--

CREATE TABLE `infoper` (
  `ID_Foranea` int(11) DEFAULT NULL,
  `nya` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `infoper`
--

INSERT INTO `infoper` (`ID_Foranea`, `nya`) VALUES
(1, 'André Mendoza');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logdata`
--

CREATE TABLE `logdata` (
  `ID` int(11) NOT NULL,
  `Correo` varchar(225) DEFAULT NULL,
  `Clave` varchar(225) DEFAULT NULL,
  `reg_state` tinyint(1) NOT NULL DEFAULT 0,
  `num_rol_data` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logdata`
--

INSERT INTO `logdata` (`ID`, `Correo`, `Clave`, `reg_state`, `num_rol_data`) VALUES
(1, 'mendozaluisandre030@gmail.com', '$2y$10$a76gqGxV9GEO5U7W.Odlu.rZhrb.prtRMB7ND/Ij0FltFGK2UK6Oa', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pending_email_verifications`
--

CREATE TABLE `pending_email_verifications` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `verification_code_hash` varchar(255) NOT NULL,
  `code_expires_at` datetime NOT NULL,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `max_attempts` int(11) NOT NULL DEFAULT 5,
  `verified` tinyint(1) NOT NULL DEFAULT 0,
  `purpose` enum('register','password_reset') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ret_islr_lot`
--

CREATE TABLE `ret_islr_lot` (
  `ID` int(11) NOT NULL,
  `enero` varchar(225) DEFAULT NULL,
  `febrero` varchar(225) DEFAULT NULL,
  `marzo` varchar(225) DEFAULT NULL,
  `abril` varchar(225) DEFAULT NULL,
  `mayo` varchar(225) DEFAULT NULL,
  `junio` varchar(225) DEFAULT NULL,
  `julio` varchar(225) DEFAULT NULL,
  `agosto` varchar(225) DEFAULT NULL,
  `septiembre` varchar(225) DEFAULT NULL,
  `octubre` varchar(225) DEFAULT NULL,
  `noviembre` varchar(225) DEFAULT NULL,
  `diciembre` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ret_islr_lot`
--

INSERT INTO `ret_islr_lot` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17'),
(1, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17'),
(2, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17'),
(3, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17'),
(4, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17'),
(5, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17'),
(6, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17'),
(7, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17'),
(8, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17'),
(9, '6;20', '3;18', '3;17', '6;21', '5;19', '3;17', '2;17', '4;20', '2;17', '2;19', '3;17', '2;17');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ret_islr_rent`
--

CREATE TABLE `ret_islr_rent` (
  `ID` int(11) NOT NULL,
  `enero` int(11) DEFAULT NULL,
  `febrero` int(11) DEFAULT NULL,
  `marzo` int(11) DEFAULT NULL,
  `abril` int(11) DEFAULT NULL,
  `mayo` int(11) DEFAULT NULL,
  `junio` int(11) DEFAULT NULL,
  `julio` int(11) DEFAULT NULL,
  `agosto` int(11) DEFAULT NULL,
  `septiembre` int(11) DEFAULT NULL,
  `octubre` int(11) DEFAULT NULL,
  `noviembre` int(11) DEFAULT NULL,
  `diciembre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ret_islr_rent`
--

INSERT INTO `ret_islr_rent` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, 15, 9, 6, 10, 12, 5, 8, 7, 8, 9, 6, 9),
(1, 9, 10, 11, 7, 13, 11, 10, 6, 9, 6, 5, 7),
(2, 8, 5, 9, 8, 7, 10, 7, 12, 10, 7, 9, 4),
(3, 8, 5, 9, 8, 7, 10, 7, 12, 10, 7, 9, 4),
(4, 9, 10, 11, 7, 13, 11, 10, 6, 9, 6, 5, 7),
(5, 14, 6, 5, 9, 8, 9, 6, 11, 7, 8, 10, 10),
(6, 13, 11, 10, 6, 11, 4, 9, 10, 4, 13, 11, 8),
(7, 13, 11, 10, 6, 11, 4, 9, 10, 4, 13, 11, 8),
(8, 15, 9, 6, 10, 12, 5, 8, 7, 8, 9, 6, 9),
(9, 14, 6, 5, 9, 8, 9, 6, 11, 7, 8, 10, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ret_iva`
--

CREATE TABLE `ret_iva` (
  `ID` int(11) NOT NULL,
  `enero` varchar(11) DEFAULT NULL,
  `febrero` varchar(11) DEFAULT NULL,
  `marzo` varchar(11) DEFAULT NULL,
  `abril` varchar(11) DEFAULT NULL,
  `mayo` varchar(11) DEFAULT NULL,
  `junio` varchar(11) DEFAULT NULL,
  `julio` varchar(11) DEFAULT NULL,
  `agosto` varchar(11) DEFAULT NULL,
  `septiembre` varchar(11) DEFAULT NULL,
  `octubre` varchar(11) DEFAULT NULL,
  `noviembre` varchar(11) DEFAULT NULL,
  `diciembre` varchar(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ret_iva`
--

INSERT INTO `ret_iva` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, '15;28', '9;20', '6;25', '1;23', '6;20', '12;29', '8;27', '14;31', '14;29', '5;20', '13;27', '3;16'),
(1, '6;19', '10;23', '3;20', '14;27', '4;18', '11;26', '3;21', '13;25', '3;18', '14;28', '12;26', '15;29'),
(2, '8;21', '5;18', '9;24', '8;21', '14;29', '3;16', '14;30', '12;24', '10;24', '15;29', '2;17', '4;21'),
(3, '16;30', '12;18', '4;23', '16;30', '7;22', '10;18', '7;23', '5;18', '2;21', '7;23', '9;23', '11;28'),
(4, '9;23', '2;25', '11;26', '7;20', '13;21', '2;19', '10;28', '6;19', '9;30', '6;22', '5;20', '7;22'),
(5, '5;22', '13;27', '12;30', '9;22', '15;28', '8;17', '6;22', '3;21', '15;25', '8;30', '4;18', '10;17'),
(6, '13;20', '4;19', '10;27', '13;24', '5;19', '15;30', '9;20', '4;28', '11;28', '2;21', '11;25', '8;18'),
(7, '12;27', '11;24', '2;18', '6;17', '11;26', '4;22', '15;31', '10;20', '4;22', '13;27', '3;19', '2;18'),
(8, '7;26', '3;26', '13;31', '10;29', '12;27', '5;23', '2;17', '7;26', '8;17', '9;26', '6;24', '9;30'),
(9, '14;29', '6;27', '5;17', '15;28', '8;25', '9;25', '13;29', '11;27', '7;23', '1;19', '10;30', '14;23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rif`
--

CREATE TABLE `rif` (
  `ID_Foranea` int(11) DEFAULT NULL,
  `RIF` varchar(225) DEFAULT NULL,
  `tipo_de_cliente` int(1) NOT NULL,
  `nombre_empresa` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rif`
--

INSERT INTO `rif` (`ID_Foranea`, `RIF`, `tipo_de_cliente`, `nombre_empresa`) VALUES
(1, '46617279', 1, 'OnlyFans');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rif_tax_preferences`
--

CREATE TABLE `rif_tax_preferences` (
  `rif` varchar(225) NOT NULL,
  `anti_islr` tinyint(1) NOT NULL DEFAULT 1,
  `dd_islr` tinyint(1) NOT NULL DEFAULT 1,
  `dec_auto_entdes` tinyint(1) NOT NULL DEFAULT 1,
  `dec_def_islrei` tinyint(1) NOT NULL DEFAULT 1,
  `dec_gra_patri` tinyint(1) NOT NULL DEFAULT 1,
  `dec_igtf` tinyint(1) NOT NULL DEFAULT 1,
  `dec_iva` tinyint(1) NOT NULL DEFAULT 1,
  `dec_prot_pen` tinyint(1) NOT NULL DEFAULT 1,
  `envite_azar` tinyint(1) NOT NULL DEFAULT 1,
  `est_islr_rent` tinyint(1) NOT NULL DEFAULT 1,
  `ret_islr_lot` tinyint(1) NOT NULL DEFAULT 1,
  `ret_islr_rent` tinyint(1) NOT NULL DEFAULT 1,
  `ret_iva` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `rif_tax_preferences`
--

INSERT INTO `rif_tax_preferences` (`rif`, `anti_islr`, `dd_islr`, `dec_auto_entdes`, `dec_def_islrei`, `dec_gra_patri`, `dec_igtf`, `dec_iva`, `dec_prot_pen`, `envite_azar`, `est_islr_rent`, `ret_islr_lot`, `ret_islr_rent`, `ret_iva`) VALUES
('46617279', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `Num_Rol` int(1) NOT NULL,
  `Rol` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`Num_Rol`, `Rol`) VALUES
(1, 'No Premium'),
(2, 'Premium');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_cliente`
--

CREATE TABLE `tipo_cliente` (
  `id_tipo_cliente` int(1) NOT NULL,
  `desc_tc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tipo_cliente`
--

INSERT INTO `tipo_cliente` (`id_tipo_cliente`, `desc_tc`) VALUES
(1, 'Ordinario'),
(2, 'Especial');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anti_islr`
--
ALTER TABLE `anti_islr`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `dd_islr`
--
ALTER TABLE `dd_islr`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `dec_auto_entdes`
--
ALTER TABLE `dec_auto_entdes`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `dec_def_islrei`
--
ALTER TABLE `dec_def_islrei`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `dec_gra_patri`
--
ALTER TABLE `dec_gra_patri`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `dec_igtf`
--
ALTER TABLE `dec_igtf`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `dec_iva`
--
ALTER TABLE `dec_iva`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `envite_azar`
--
ALTER TABLE `envite_azar`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `est_islr_rent`
--
ALTER TABLE `est_islr_rent`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `infoper`
--
ALTER TABLE `infoper`
  ADD KEY `ID_Foranea` (`ID_Foranea`);

--
-- Indices de la tabla `logdata`
--
ALTER TABLE `logdata`
  ADD PRIMARY KEY (`ID`),
  ADD UNIQUE KEY `Correo` (`Correo`),
  ADD KEY `num_rol_data` (`num_rol_data`);

--
-- Indices de la tabla `pending_email_verifications`
--
ALTER TABLE `pending_email_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email_purpose` (`email`,`purpose`);

--
-- Indices de la tabla `ret_islr_lot`
--
ALTER TABLE `ret_islr_lot`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `rif`
--
ALTER TABLE `rif`
  ADD UNIQUE KEY `RIF` (`RIF`),
  ADD KEY `ID_Foranea` (`ID_Foranea`),
  ADD KEY `tipo_de_cliente` (`tipo_de_cliente`);

--
-- Indices de la tabla `rif_tax_preferences`
--
ALTER TABLE `rif_tax_preferences`
  ADD PRIMARY KEY (`rif`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`Num_Rol`);

--
-- Indices de la tabla `tipo_cliente`
--
ALTER TABLE `tipo_cliente`
  ADD PRIMARY KEY (`id_tipo_cliente`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `logdata`
--
ALTER TABLE `logdata`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `pending_email_verifications`
--
ALTER TABLE `pending_email_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `tipo_cliente`
--
ALTER TABLE `tipo_cliente`
  MODIFY `id_tipo_cliente` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `infoper`
--
ALTER TABLE `infoper`
  ADD CONSTRAINT `infoper_ibfk_1` FOREIGN KEY (`ID_Foranea`) REFERENCES `logdata` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `logdata`
--
ALTER TABLE `logdata`
  ADD CONSTRAINT `logdata_ibfk_1` FOREIGN KEY (`num_rol_data`) REFERENCES `roles` (`Num_Rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `rif`
--
ALTER TABLE `rif`
  ADD CONSTRAINT `rif_ibfk_1` FOREIGN KEY (`ID_Foranea`) REFERENCES `logdata` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `rif_ibfk_3` FOREIGN KEY (`tipo_de_cliente`) REFERENCES `tipo_cliente` (`id_tipo_cliente`);

--
-- Filtros para la tabla `rif_tax_preferences`
--
ALTER TABLE `rif_tax_preferences`
  ADD CONSTRAINT `fk_rif_tax_preferences_rif` FOREIGN KEY (`rif`) REFERENCES `rif` (`RIF`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
