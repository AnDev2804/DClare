-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Servidor: localhost:3306
-- Tiempo de generación: 08-11-2026 a las 19:23:07
-- Versión del servidor: 5.7.44-48
-- Versión de PHP: 8.4.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `hgrqrrte_clara_test`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ANTI_ISLR`
--

CREATE TABLE `ANTI_ISLR` (
  `ID` int(11) NOT NULL,
  `enero` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `febrero` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marzo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abril` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mayo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `junio` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `julio` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agosto` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `septiembre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `octubre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `noviembre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `diciembre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `ANTI_ISLR`
--

INSERT INTO `ANTI_ISLR` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
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
-- Estructura de tabla para la tabla `auth_sessions`
--

CREATE TABLE `auth_sessions` (
  `id_session` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `token_hash` char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `expires_at` datetime NOT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `revoked_at` datetime DEFAULT NULL,
  `ip_created` varbinary(16) DEFAULT NULL,
  `user_agent` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `auth_sessions`
--

INSERT INTO `auth_sessions` (`id_session`, `id_usuario`, `token_hash`, `created_at`, `expires_at`, `last_used_at`, `revoked_at`, `ip_created`, `user_agent`) VALUES
(6, 5, '0660c3b8167e31a3a16a1481db86fcd05f7824c9c8910dc19b59b0916c236bc8', '2026-10-06 15:17:40', '2026-11-05 15:17:40', '2026-10-09 00:53:38', NULL, 0x2dbad1ad, 'Dalvik/2.1.0 (Linux; U; Android 15; ELA-LX3 Build/AP3A.240905.015.A2)'),
(7, 6, '1cd429805650b952efd4e1ad37584d109d199f72f88e4b17053cfceee3897007', '2026-10-09 18:50:41', '2026-11-08 18:50:41', '2026-10-09 18:59:12', '2026-10-09 18:59:12', 0xbaa56135, 'Dalvik/2.1.0 (Linux; U; Android 14; 2109119DG Build/UKQ1.240624.001)'),
(8, 1, '7e6384e1214aad0dd56771a2a3423735df9faa85ec3d806ba251c8276a37de35', '2026-10-12 22:14:49', '2026-11-11 22:14:49', '2026-10-12 23:19:09', '2026-10-12 23:19:09', 0xb33f27a7, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(10, 7, '695c45d8a2ad5577aa40b77ee5914f8d14a81f56cd12d3c372c06ae23962f8b1', '2026-10-13 01:49:31', '2026-11-12 01:49:31', '2026-10-13 01:51:44', NULL, 0xb33f27a7, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(13, 8, '1ab280cce671532487e72eff820ba614527195bb17e7ee7fbfb9ddc182bd32eb', '2026-10-19 22:03:08', '2026-11-18 22:03:08', '2026-10-19 22:21:50', NULL, 0xb33f27a7, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(14, 8, '821a1d1927102393197983f612d1d4b78ea39166b08492ced6d0894db41042f7', '2026-10-19 22:24:30', '2026-11-18 22:24:30', '2026-10-19 22:25:54', '2026-10-19 22:25:54', 0xb33f27a7, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(15, 8, '8cb440f3e207b465c707c1c722f42f7b154d3b869be8e7956cd6e714906af148', '2026-10-19 22:26:08', '2026-11-18 22:26:08', '2026-10-19 22:29:48', '2026-10-19 22:29:48', 0xb33f27a7, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(17, 8, '935a98c0ec4fe33a48c94002adc2fd716ad6b1b6321663ffd28545a053d0adda', '2026-10-19 22:32:52', '2026-11-18 22:32:52', '2026-10-19 22:49:16', '2026-10-19 22:49:16', 0xb33f27a7, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(19, 7, '60e9ea8da7f6067dc94108c41601772c086e4bd7148c3eb4b9208c54c163dfcd', '2026-10-19 23:02:16', '2026-11-18 23:02:16', '2026-10-19 23:03:28', NULL, 0xb33f27a7, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(21, 8, '73af50f3cad2d8a48303dd7047a2e60b37fb1da91851f628512f195cdf12b25b', '2026-10-19 23:19:32', '2026-11-18 23:19:32', '2026-10-19 23:30:15', NULL, 0xb33f27a7, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(23, 4, '9eeda72328da41d58ee5872eb02c114ae4058478422ba6baaa6cf151b8c4af55', '2026-10-20 20:08:49', '2026-11-19 20:08:49', '2026-10-29 19:23:32', '2026-10-29 19:23:32', 0x2dbad193, 'Dalvik/2.1.0 (Linux; U; Android 16; 24116RACCG Build/BP2A.250605.031.A3)'),
(25, 7, 'daa3fdf754e40cf637963d7cb420ce9e34af26c1610a140ab2818ab714acf709', '2026-10-26 14:20:19', '2026-11-25 14:20:19', '2026-10-26 14:23:52', '2026-10-26 14:23:52', 0xb33f25e5, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(29, 9, 'a46539baa02613002a8b03605a84ad9e5fdc72f7a3dba6c304e8d4a4c5a88905', '2026-10-29 19:27:52', '2026-11-28 19:27:52', '2026-11-04 12:51:42', NULL, 0x2dbad186, 'Dalvik/2.1.0 (Linux; U; Android 16; 24116RACCG Build/BP2A.250605.031.A3)'),
(31, 7, '583ab8036fbe09be3754ebf81113ff727d9e96b42dbb52c3cbe6064d56e289d6', '2026-10-29 20:43:00', '2026-11-28 20:43:00', '2026-10-29 20:43:28', '2026-10-29 20:43:28', 0xb33f25e5, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(32, 3, '318b3b8471ef2654305dd6ee7413c4d9d7c1fdc1152fa4f913abbb3de20adc5e', '2026-10-29 21:57:42', '2026-11-28 21:57:42', '2026-10-29 21:58:47', '2026-10-29 21:58:47', 0x2dbad186, 'Dalvik/2.1.0 (Linux; U; Android 16; TECNO CN7c Build/BP2A.250605.031.A3)'),
(33, 2, '93fa9ed54b19d92cab10303473bdaa55bf3fe9977a3b3eab4e2a5d8ad6eef37b', '2026-11-02 21:37:56', '2026-12-02 21:37:56', '2026-11-02 21:38:02', '2026-11-02 21:38:02', 0xb33f25e5, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)'),
(34, 2, '98d43f0e49cc486673e2c27efa978a6ab0a1a4b53ca25b5659805608adca3572', '2026-11-06 19:45:17', '2026-12-06 19:45:17', '2026-11-06 19:45:39', '2026-11-06 19:45:39', 0xb33f25e5, 'Dalvik/2.1.0 (Linux; U; Android 12; TECNO KI5k Build/SP1A.210812.016)');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `DD_ISLR`
--

CREATE TABLE `DD_ISLR` (
  `ID` int(11) NOT NULL,
  `enero` int(11) DEFAULT NULL,
  `febrero` int(11) DEFAULT NULL,
  `marzo` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `DD_ISLR`
--

INSERT INTO `DD_ISLR` (`ID`, `enero`, `febrero`, `marzo`) VALUES
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
-- Estructura de tabla para la tabla `DEC_AUTO_ENTDES`
--

CREATE TABLE `DEC_AUTO_ENTDES` (
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
-- Volcado de datos para la tabla `DEC_AUTO_ENTDES`
--

INSERT INTO `DEC_AUTO_ENTDES` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
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
-- Estructura de tabla para la tabla `DEC_DEF_ISLREI`
--

CREATE TABLE `DEC_DEF_ISLREI` (
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
-- Volcado de datos para la tabla `DEC_DEF_ISLREI`
--

INSERT INTO `DEC_DEF_ISLREI` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
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
-- Estructura de tabla para la tabla `DEC_GRA_PATRI`
--

CREATE TABLE `DEC_GRA_PATRI` (
  `ID` int(11) NOT NULL,
  `octubre` int(11) DEFAULT NULL,
  `noviembre` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `DEC_GRA_PATRI`
--

INSERT INTO `DEC_GRA_PATRI` (`ID`, `octubre`, `noviembre`) VALUES
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
-- Estructura de tabla para la tabla `DEC_IGTF`
--

CREATE TABLE `DEC_IGTF` (
  `ID` int(11) NOT NULL,
  `enero` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `febrero` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marzo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abril` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mayo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `junio` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `julio` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agosto` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `septiembre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `octubre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `noviembre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `diciembre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `DEC_IGTF`
--

INSERT INTO `DEC_IGTF` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
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
-- Estructura de tabla para la tabla `DEC_IVA`
--

CREATE TABLE `DEC_IVA` (
  `ID` int(11) NOT NULL,
  `enero` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `febrero` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marzo` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abril` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mayo` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `junio` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `julio` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agosto` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `septiembre` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `octubre` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `noviembre` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `diciembre` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `DEC_IVA`
--

INSERT INTO `DEC_IVA` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
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
-- Estructura de tabla para la tabla `DEC_PROT_PEN`
--

CREATE TABLE `DEC_PROT_PEN` (
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
-- Volcado de datos para la tabla `DEC_PROT_PEN`
--

INSERT INTO `DEC_PROT_PEN` (`ID`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, 7, 3, 14, 9, 2, 15, 9),
(1, 5, 1, 6, 10, 4, 14, 13),
(2, 12, 8, 2, 4, 7, 11, 15),
(3, 6, 15, 12, 11, 14, 1, 12),
(4, 13, 11, 13, 3, 11, 6, 6),
(5, 18, 4, 15, 6, 8, 12, 2),
(6, 10, 2, 8, 5, 3, 13, 3),
(7, 4, 10, 5, 12, 10, 5, 11),
(8, 14, 9, 7, 2, 9, 8, 4),
(9, 11, 12, 9, 13, 15, 7, 10),
(0, 5, 8, 7, 8, 9, 6, 9),
(1, 11, 10, 6, 9, 6, 5, 7),
(2, 10, 7, 12, 10, 7, 9, 4),
(3, 10, 7, 12, 10, 7, 9, 4),
(4, 11, 10, 6, 9, 6, 5, 7),
(5, 9, 6, 11, 7, 8, 10, 10),
(6, 4, 9, 10, 4, 13, 11, 8),
(7, 4, 9, 10, 4, 13, 11, 8),
(8, 5, 8, 7, 8, 9, 6, 9),
(9, 9, 6, 11, 7, 8, 10, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresas_eliminadas`
--

CREATE TABLE `empresas_eliminadas` (
  `id_eliminada` bigint(20) UNSIGNED NOT NULL,
  `id_rif_original` bigint(20) UNSIGNED NOT NULL,
  `ID_Foranea` int(11) NOT NULL,
  `RIF` varchar(225) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_de_cliente` int(1) NOT NULL,
  `nombre_empresa` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rif_created_at` datetime DEFAULT NULL,
  `usuario_eliminador_id` int(11) NOT NULL,
  `usuario_eliminador_nombre` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `usuario_eliminador_email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fecha_eliminacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `empresas_eliminadas`
--

INSERT INTO `empresas_eliminadas` (`id_eliminada`, `id_rif_original`, `ID_Foranea`, `RIF`, `tipo_de_cliente`, `nombre_empresa`, `rif_created_at`, `usuario_eliminador_id`, `usuario_eliminador_nombre`, `usuario_eliminador_email`, `fecha_eliminacion`) VALUES
(3, 13, 8, 'J-359946762', 1, 'Juan Carlos C.A', '0000-00-00 00:00:00', 8, '', '', '2026-10-19 22:33:14');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ENVITE_AZAR`
--

CREATE TABLE `ENVITE_AZAR` (
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
-- Volcado de datos para la tabla `ENVITE_AZAR`
--

INSERT INTO `ENVITE_AZAR` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
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
-- Estructura de tabla para la tabla `EST_ISLR_RENT`
--

CREATE TABLE `EST_ISLR_RENT` (
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
-- Volcado de datos para la tabla `EST_ISLR_RENT`
--

INSERT INTO `EST_ISLR_RENT` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
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
-- Estructura de tabla para la tabla `historial_membresias`
--

CREATE TABLE `historial_membresias` (
  `id_historial` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `correo_usuario_snapshot` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nombre_usuario_snapshot` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_pago` bigint(20) UNSIGNED DEFAULT NULL,
  `tipo_asignacion` enum('PRUEBA_GRATIS','PAGO_APROBADO','ASIGNACION_ADMIN','MIGRACION') COLLATE utf8_unicode_ci NOT NULL,
  `estado` enum('ACTIVA','VENCIDA','REVOCADA') COLLATE utf8_unicode_ci NOT NULL DEFAULT 'ACTIVA',
  `fecha_inicio` datetime NOT NULL,
  `fecha_fin` datetime NOT NULL,
  `fecha_cambio_estado` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `id_admin` int(11) DEFAULT NULL,
  `observacion` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_admin_revocacion` int(11) DEFAULT NULL,
  `fecha_revocacion` datetime DEFAULT NULL,
  `motivo_revocacion` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `historial_membresias`
--

INSERT INTO `historial_membresias` (`id_historial`, `id_usuario`, `correo_usuario_snapshot`, `nombre_usuario_snapshot`, `id_pago`, `tipo_asignacion`, `estado`, `fecha_inicio`, `fecha_fin`, `fecha_cambio_estado`, `id_admin`, `observacion`, `id_admin_revocacion`, `fecha_revocacion`, `motivo_revocacion`, `created_at`, `updated_at`) VALUES
(1, 4, 'damontes32@gmail.com', 'Daniels Montes', NULL, 'PRUEBA_GRATIS', 'REVOCADA', '2026-10-06 15:29:15', '2027-01-06 15:29:15', '2026-10-28 18:49:10', NULL, 'Prueba gratuita activada por el usuario.', 2, '2026-10-28 18:49:10', 'prueba', '2026-10-06 15:29:15', '2026-10-28 18:49:10'),
(2, 1, 'folksan16@gmail.com', 'Folk San', NULL, 'PRUEBA_GRATIS', 'REVOCADA', '2026-10-12 22:14:49', '2027-01-12 22:14:49', '2026-10-26 14:19:53', NULL, 'Prueba gratuita de 3 meses asignada automáticamente.', 2, '2026-10-26 14:19:53', NULL, '2026-10-12 22:14:49', '2026-10-26 14:19:53'),
(3, 7, 'mendozaluisandre030@gmail.com', 'Luis André Mendoza Colmenares', NULL, 'PRUEBA_GRATIS', 'REVOCADA', '2026-10-13 01:49:20', '2027-01-13 01:49:20', '2026-10-26 14:19:20', NULL, 'Prueba gratuita de 3 meses asignada automáticamente al registrarse.', 2, '2026-10-26 14:19:20', NULL, '2026-10-13 01:49:20', '2026-10-26 14:19:20'),
(4, 3, 'yulee.delgado@gmail.com', 'yulexis delgado', NULL, 'PRUEBA_GRATIS', 'ACTIVA', '2026-10-14 14:33:28', '2027-01-14 14:33:28', '2026-10-14 14:33:28', NULL, 'Prueba gratuita de 3 meses asignada automáticamente.', NULL, NULL, NULL, '2026-10-14 14:33:28', '2026-10-14 14:33:28'),
(5, 4, 'damontes32@gmail.com', 'Daniels Montes', 1, 'PAGO_APROBADO', 'VENCIDA', '2026-10-28 19:19:12', '2026-10-28 19:20:12', '2026-10-28 19:22:02', 2, 'prueba', NULL, NULL, NULL, '2026-10-28 19:19:12', '2026-10-28 19:22:02'),
(6, 4, 'damontes32@gmail.com', 'Daniels Montes', 2, 'PAGO_APROBADO', 'VENCIDA', '2026-10-28 20:57:46', '2026-10-28 20:58:46', '2026-10-28 20:58:52', 2, 'prueba 2', NULL, NULL, NULL, '2026-10-28 20:57:46', '2026-10-28 20:58:52'),
(7, 4, 'damontes32@gmail.com', 'Daniels Montes', 3, 'PAGO_APROBADO', 'VENCIDA', '2026-10-28 21:00:24', '2026-10-28 21:01:24', '2026-10-28 21:02:31', 2, 'prueba', NULL, NULL, NULL, '2026-10-28 21:00:24', '2026-10-28 21:02:31'),
(8, 4, 'damontes32@gmail.com', 'Daniels Montes', 4, 'PAGO_APROBADO', 'VENCIDA', '2026-10-28 21:03:25', '2026-10-28 21:04:25', '2026-10-28 21:06:02', 2, NULL, NULL, NULL, NULL, '2026-10-28 21:03:25', '2026-10-28 21:06:02'),
(9, 4, 'damontes32@gmail.com', 'Daniels Montes', 5, 'PAGO_APROBADO', 'VENCIDA', '2026-10-28 21:10:50', '2026-10-28 21:11:50', '2026-10-29 19:17:53', 2, NULL, NULL, NULL, NULL, '2026-10-28 21:10:50', '2026-10-29 19:17:53'),
(10, 9, 'ranchscoctel@gmail.com', 'Ranchs Coctel', NULL, 'PRUEBA_GRATIS', 'VENCIDA', '2026-10-29 19:35:48', '2026-10-29 19:38:48', '2026-10-29 20:42:31', NULL, 'Prueba gratuita activada por el usuario.', NULL, NULL, NULL, '2026-10-29 19:35:48', '2026-10-29 20:42:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `infoper`
--

CREATE TABLE `infoper` (
  `ID_Foranea` int(11) DEFAULT NULL,
  `nya` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `infoper`
--

INSERT INTO `infoper` (`ID_Foranea`, `nya`) VALUES
(1, 'Folk San'),
(2, 'Clara Admin'),
(3, 'yulexis delgado'),
(4, 'Daniels Montes'),
(5, 'Carolina perez'),
(6, 'Patricia Urbina'),
(7, 'Luis André Mendoza Colmenares'),
(8, 'Luis André Mendoza Colmenares'),
(9, 'Ranchs Coctel');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logdata`
--

CREATE TABLE `logdata` (
  `ID` int(11) NOT NULL,
  `Correo` varchar(225) DEFAULT NULL,
  `Clave` varchar(225) DEFAULT NULL,
  `reg_state` tinyint(1) NOT NULL DEFAULT '0',
  `num_rol_data` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `logdata`
--

INSERT INTO `logdata` (`ID`, `Correo`, `Clave`, `reg_state`, `num_rol_data`) VALUES
(1, 'folksan16@gmail.com', '$2y$10$KlEdvnOLJPBhcyvogTXGSuN2Ad1io7S2Wll5uVIcZ6Z6zglxuJD6C', 1, 1),
(2, 'declararconclara@gmail.com', '$2y$10$BVNWyoPDiB5/gVGhkG2nD.sDSuT8XxkTId5dNIJX/WaAcMx950iEG', 0, 3),
(3, 'yulee.delgado@gmail.com', '$2y$10$zwpZuVYC/F/dWA8vBq42U.dIc3pXMlgJPKwu.AF7ffOJmbFhlo6DW', 1, 2),
(4, 'damontes32@gmail.com', '$2y$10$qctB3FChRd4EqYAVImCppOsY0eWeu5nVr/NbK9Ztb.gD/ZKLVKoju', 1, 1),
(5, 'ydcontableve@gmail.com', '$2y$10$LHaI3PzbYFBW0dI8K1OGJO3T9Ey9cMtjzmprzbLC.xY1d2ns7ehKG', 1, 1),
(6, 'pattyurbina1023@gmail.com', '$2y$10$Ea9PMfp5QuGe.nsG1z40h.ZpoX4/O2AdYokK3EiFcagbIwTGKwfai', 1, 1),
(7, 'mendozaluisandre030@gmail.com', '$2y$10$IhR0mdf9IWgiDNxzxirAueK58/8nbGBXDV6ZinYxdmD8At8kViMX6', 1, 1),
(8, 'v28310477@gmail.com', '$2y$10$f12rumPZTv2pHaf78kUk.u3NDG8pIlEFsRR4P23x85tdXv/X4LQaa', 1, 1),
(9, 'ranchscoctel@gmail.com', '$2y$12$Noii/GifJNzHeLKlxpn1Cew4RVxbX1pDNnmB3DBQYJuT2B.HUym6y', 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresia_configuracion`
--

CREATE TABLE `membresia_configuracion` (
  `id_configuracion` tinyint(3) UNSIGNED NOT NULL,
  `precio_usd` decimal(10,2) NOT NULL DEFAULT '1.99',
  `meses_vigencia` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `meses_prueba_gratis` tinyint(3) UNSIGNED NOT NULL DEFAULT '3',
  `limite_empresas_no_premium` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `dias_recordatorio_vencimiento` tinyint(3) UNSIGNED NOT NULL DEFAULT '7',
  `minutos_recordar_oferta` smallint(5) UNSIGNED NOT NULL DEFAULT '60',
  `modo_pruebas` tinyint(1) NOT NULL DEFAULT '0',
  `minutos_prueba_gratis_test` smallint(5) UNSIGNED NOT NULL DEFAULT '3',
  `minutos_membresia_pagada_test` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `membresia_configuracion`
--

INSERT INTO `membresia_configuracion` (`id_configuracion`, `precio_usd`, `meses_vigencia`, `meses_prueba_gratis`, `limite_empresas_no_premium`, `dias_recordatorio_vencimiento`, `minutos_recordar_oferta`, `modo_pruebas`, `minutos_prueba_gratis_test`, `minutos_membresia_pagada_test`, `activo`, `created_at`, `updated_at`) VALUES
(1, 1.99, 1, 3, 1, 7, 60, 0, 3, 1, 1, '2026-09-09 11:09:58', '2026-11-03 16:30:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresia_metodos_pago`
--

CREATE TABLE `membresia_metodos_pago` (
  `id_metodo_pago` tinyint(3) UNSIGNED NOT NULL,
  `codigo` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  `tipo` varchar(20) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'MANUAL',
  `moneda_principal` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `requiere_comprobante` tinyint(1) NOT NULL DEFAULT '1',
  `requiere_referencia` tinyint(1) NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `orden` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `membresia_metodos_pago`
--

INSERT INTO `membresia_metodos_pago` (`id_metodo_pago`, `codigo`, `nombre`, `tipo`, `moneda_principal`, `requiere_comprobante`, `requiere_referencia`, `activo`, `orden`, `created_at`, `updated_at`) VALUES
(1, 'PAGO_MOVIL', 'Pago Móvil', 'MANUAL', 'VES', 1, 1, 1, 1, '2026-09-09 17:46:23', '2026-09-09 17:46:23'),
(2, 'TRANSFERENCIA', 'Transferencia', 'MANUAL', 'VES', 1, 1, 1, 2, '2026-09-09 17:46:23', '2026-09-09 17:46:23'),
(3, 'PAYPAL', 'PayPal', 'EXTERNO', 'USD', 1, 1, 1, 3, '2026-09-09 17:46:23', '2026-09-10 13:18:06'),
(4, 'BINANCE', 'Binance', 'EXTERNO', 'USDT', 1, 1, 1, 4, '2026-09-09 17:46:23', '2026-09-09 17:46:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresia_metodo_pago_configuracion`
--

CREATE TABLE `membresia_metodo_pago_configuracion` (
  `id_configuracion_pago` bigint(20) UNSIGNED NOT NULL,
  `id_metodo_pago` tinyint(3) UNSIGNED NOT NULL,
  `clave` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `etiqueta` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `valor` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `orden` smallint(5) UNSIGNED NOT NULL DEFAULT '1',
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresia_prueba_datos`
--

CREATE TABLE `membresia_prueba_datos` (
  `id_prueba` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `id_historial` bigint(20) UNSIGNED NOT NULL,
  `documento` varchar(30) NOT NULL,
  `nombre_completo` varchar(180) NOT NULL,
  `direccion` varchar(500) NOT NULL,
  `telefono` varchar(30) NOT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `membresia_prueba_datos`
--

INSERT INTO `membresia_prueba_datos` (`id_prueba`, `id_usuario`, `id_historial`, `documento`, `nombre_completo`, `direccion`, `telefono`, `fecha_solicitud`, `created_at`, `updated_at`) VALUES
(1, 4, 1, '20185451', 'Daniels José Montes Rodríguez', 'La candelaria', '04241825466', '2026-10-06 15:29:15', '2026-10-06 15:29:15', '2026-10-06 15:29:15'),
(2, 9, 10, '20185461', 'Ranchs Coctel', 'Candelaria', '04123376208', '2026-10-29 19:35:48', '2026-10-29 19:35:48', '2026-10-29 19:35:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `membresia_tasas_cambio`
--

CREATE TABLE `membresia_tasas_cambio` (
  `id_tasa` bigint(20) UNSIGNED NOT NULL,
  `moneda_base` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'USD',
  `moneda_destino` varchar(10) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'VES',
  `tasa` decimal(18,8) NOT NULL,
  `fuente` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `fecha_tasa` date NOT NULL,
  `fecha_consulta` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `activo` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `membresia_tasas_cambio`
--

INSERT INTO `membresia_tasas_cambio` (`id_tasa`, `moneda_base`, `moneda_destino`, `tasa`, `fuente`, `fecha_tasa`, `fecha_consulta`, `activo`, `created_at`, `updated_at`) VALUES
(1, 'USD', 'VES', 842.20670000, 'BCV via DolarApi', '2026-09-15', '2026-09-16 02:48:13', 1, '2026-09-16 02:48:13', '2026-09-16 02:48:13'),
(2, 'USD', 'VES', 847.44420000, 'BCV via DolarApi', '2026-09-17', '2026-09-17 19:07:20', 1, '2026-09-17 19:07:20', '2026-09-17 19:07:20'),
(3, 'USD', 'VES', 886.22710000, 'BCV via DolarApi', '2026-10-26', '2026-10-26 14:22:43', 1, '2026-10-26 14:22:43', '2026-10-26 14:22:43'),
(4, 'USD', 'VES', 888.11830000, 'BCV via DolarApi', '2026-10-28', '2026-10-28 18:51:31', 1, '2026-10-28 18:51:31', '2026-10-28 18:51:31'),
(5, 'USD', 'VES', 888.89090000, 'BCV via DolarApi', '2026-10-29', '2026-10-29 19:17:59', 1, '2026-10-29 19:17:59', '2026-10-29 19:17:59'),
(6, 'USD', 'VES', 893.21730000, 'BCV via DolarApi', '2026-11-04', '2026-11-04 12:51:42', 1, '2026-11-04 12:51:42', '2026-11-04 12:51:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_membresia`
--

CREATE TABLE `pagos_membresia` (
  `id_pago` bigint(20) UNSIGNED NOT NULL,
  `id_usuario` int(11) NOT NULL,
  `correo_usuario_snapshot` varchar(225) COLLATE utf8_unicode_ci NOT NULL,
  `nombre_usuario_snapshot` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_pago` datetime NOT NULL,
  `zona_horaria_pago` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'America/Caracas',
  `num_referencia` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `monto_pago` decimal(18,8) NOT NULL,
  `monto_requerido` decimal(18,8) DEFAULT NULL,
  `moneda` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `precio_usd_base` decimal(10,2) NOT NULL DEFAULT '1.99',
  `tasa_cambio_aplicada` decimal(18,8) DEFAULT NULL,
  `fuente_tasa` varchar(80) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_tasa` datetime DEFAULT NULL,
  `id_metodo_pago` tinyint(3) UNSIGNED NOT NULL,
  `metodo_pago_snapshot` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `banco_emisor` varchar(120) COLLATE utf8_unicode_ci DEFAULT NULL,
  `numero_cuenta_emisora` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `identificador_emisor` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nombre_titular` varchar(180) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_membresia_status` tinyint(3) UNSIGNED NOT NULL DEFAULT '2',
  `comprobante_archivo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `comprobante_nombre_original` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `comprobante_mime` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `comprobante_tamano` int(10) UNSIGNED NOT NULL,
  `comprobante_sha256` char(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
  `idempotency_key` char(36) CHARACTER SET ascii COLLATE ascii_bin DEFAULT NULL,
  `observacion_usuario` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `id_admin_revision` int(11) DEFAULT NULL,
  `observacion_admin` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `fecha_solicitud` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_revision` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `pagos_membresia`
--

INSERT INTO `pagos_membresia` (`id_pago`, `id_usuario`, `correo_usuario_snapshot`, `nombre_usuario_snapshot`, `fecha_pago`, `zona_horaria_pago`, `num_referencia`, `monto_pago`, `monto_requerido`, `moneda`, `precio_usd_base`, `tasa_cambio_aplicada`, `fuente_tasa`, `fecha_tasa`, `id_metodo_pago`, `metodo_pago_snapshot`, `banco_emisor`, `numero_cuenta_emisora`, `identificador_emisor`, `nombre_titular`, `id_membresia_status`, `comprobante_archivo`, `comprobante_nombre_original`, `comprobante_mime`, `comprobante_tamano`, `comprobante_sha256`, `idempotency_key`, `observacion_usuario`, `id_admin_revision`, `observacion_admin`, `fecha_solicitud`, `fecha_revision`, `created_at`, `updated_at`) VALUES
(1, 4, 'damontes32@gmail.com', 'Daniels Montes', '2026-10-28 18:51:30', 'America/Caracas', '01123568', 1767.36000000, 1767.36000000, 'VES', 1.99, 888.11830000, 'BCV via DolarApi', '2026-10-28 00:00:00', 1, 'PAGO_MOVIL', 'mercantil', NULL, '04123376208', 'Daniels Montes', 1, '351647c6b0a4364614e3195445c36cd1e0675097ba4f75d0.jpg', 'comprobante-fc88fc4f-26a3-49b2-b2d0-0ddb45453704.jpg', 'image/jpeg', 56003, '424146897ebb830e6135a2dee18716fdcd115463b8c89bf0fc9fd92698f45741', '1631fe6a-9b88-497c-8cb6-33f45f66ee99', 'pago', 2, 'prueba', '2026-10-28 19:14:23', '2026-10-28 19:19:12', '2026-10-28 19:14:23', '2026-10-28 19:19:12'),
(2, 4, 'damontes32@gmail.com', 'Daniels Montes', '2026-10-28 20:49:16', 'America/Caracas', '09785689', 1767.36000000, 1767.36000000, 'VES', 1.99, 888.11830000, 'BCV via DolarApi', '2026-10-28 00:00:00', 1, 'PAGO_MOVIL', 'Venezuela', NULL, '04241825466', 'Daniels Montes', 1, '1b7d70675eab5fc8ec571bf3927253e6a6cd186a54889a0d.jpg', 'comprobante-62bef5f9-7f4c-4792-9215-5b02a76d520c.jpg', 'image/jpeg', 56003, '424146897ebb830e6135a2dee18716fdcd115463b8c89bf0fc9fd92698f45741', 'cef5ef05-5873-4da8-81f3-b3277c1e6465', 'pago', 2, 'prueba 2', '2026-10-28 20:57:05', '2026-10-28 20:57:46', '2026-10-28 20:57:05', '2026-10-28 20:57:46'),
(3, 4, 'damontes32@gmail.com', 'Daniels Montes', '2026-10-28 20:59:19', 'America/Caracas', '567802367', 1767.36000000, 1767.36000000, 'VES', 1.99, 888.11830000, 'BCV via DolarApi', '2026-10-28 00:00:00', 1, 'PAGO_MOVIL', 'Mercantil', NULL, '04123376208', 'Daniels Montes', 1, 'ee08c568e31bb8b7bb0c896b569f4cac525531b6515671b8.jpg', 'comprobante-3b8b50a2-e674-4ed1-bbb1-ee1baefd8b12.jpg', 'image/jpeg', 56003, '424146897ebb830e6135a2dee18716fdcd115463b8c89bf0fc9fd92698f45741', 'e4755e74-485c-4f5f-bf9d-f776f2d3eb3f', 'pago', 2, 'prueba', '2026-10-28 20:59:59', '2026-10-28 21:00:24', '2026-10-28 20:59:59', '2026-10-28 21:00:24'),
(4, 4, 'damontes32@gmail.com', 'Daniels Montes', '2026-10-28 21:02:40', 'America/Caracas', '6780967', 1767.36000000, 1767.36000000, 'VES', 1.99, 888.11830000, 'BCV via DolarApi', '2026-10-28 00:00:00', 1, 'PAGO_MOVIL', 'Mercantil', NULL, '04123376208', 'Daniels Montes', 1, '600bce8f5611c2eeb42cc314a9c47dabe6e4533fc95f4806.jpg', 'comprobante-e3e2e23b-7aeb-4416-ace8-adf88eb76bfa.jpg', 'image/jpeg', 56003, '424146897ebb830e6135a2dee18716fdcd115463b8c89bf0fc9fd92698f45741', 'ece24be9-71d3-4468-90ae-9936ff52289f', 'pago', 2, NULL, '2026-10-28 21:03:11', '2026-10-28 21:03:25', '2026-10-28 21:03:11', '2026-10-28 21:03:25'),
(5, 4, 'damontes32@gmail.com', 'Daniels Montes', '2026-10-28 21:06:13', 'America/Caracas', '0978976', 1767.36000000, 1767.36000000, 'VES', 1.99, 888.11830000, 'BCV via DolarApi', '2026-10-28 00:00:00', 1, 'PAGO_MOVIL', 'Mercantil', NULL, '04123376208', 'daniels Montes', 1, 'fbec3dbeb42bc6950332839f68fa9203fa3b5e9688a20c98.jpg', 'comprobante-aaea77bd-b3b2-40ba-8cbd-c68b2106f60a.jpg', 'image/jpeg', 56003, '424146897ebb830e6135a2dee18716fdcd115463b8c89bf0fc9fd92698f45741', '22d5926b-4bfd-4633-b571-53e9e2acfdde', 'pago', 2, NULL, '2026-10-28 21:07:10', '2026-10-28 21:10:50', '2026-10-28 21:07:10', '2026-10-28 21:10:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `pagos_membresia_status`
--

CREATE TABLE `pagos_membresia_status` (
  `id_membresia_status` tinyint(3) UNSIGNED NOT NULL,
  `codigo_status` varchar(40) NOT NULL,
  `descripcion_status` varchar(120) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pagos_membresia_status`
--

INSERT INTO `pagos_membresia_status` (`id_membresia_status`, `codigo_status`, `descripcion_status`) VALUES
(0, 'RECHAZADO', 'Pago rechazado'),
(1, 'APROBADO', 'Pago aprobado'),
(2, 'PENDIENTE_APROBACION', 'Pendiente por aprobación');

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
  `attempts` int(11) NOT NULL DEFAULT '0',
  `max_attempts` int(11) NOT NULL DEFAULT '5',
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `purpose` enum('register','password_reset') NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `pending_email_verifications`
--

INSERT INTO `pending_email_verifications` (`id`, `email`, `name`, `password_hash`, `verification_code_hash`, `code_expires_at`, `attempts`, `max_attempts`, `verified`, `purpose`, `created_at`, `updated_at`) VALUES
(9, 'pattyurbina1023@gmail.com', '', '7e2e15c66f5077e18a8a8043599cfb5ae9074e9cec9adb464fbdf3333950f350', '', '2026-10-09 18:59:40', 0, 5, 1, 'password_reset', '2026-10-09 18:49:27', '2026-10-09 18:49:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `RET_ISLR_LOT`
--

CREATE TABLE `RET_ISLR_LOT` (
  `ID` int(11) NOT NULL,
  `enero` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `febrero` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marzo` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abril` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mayo` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `junio` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `julio` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agosto` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `septiembre` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `octubre` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `noviembre` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL,
  `diciembre` varchar(225) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `RET_ISLR_LOT`
--

INSERT INTO `RET_ISLR_LOT` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
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
-- Estructura de tabla para la tabla `RET_ISLR_RENT`
--

CREATE TABLE `RET_ISLR_RENT` (
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
-- Volcado de datos para la tabla `RET_ISLR_RENT`
--

INSERT INTO `RET_ISLR_RENT` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, 15, 9, 6, 10, 12, 5, 8, 7, 8, 9, 6, 9),
(1, 9, 10, 11, 7, 13, 11, 10, 6, 9, 6, 5, 7),
(2, 8, 5, 9, 8, 7, 10, 7, 12, 10, 7, 9, 4),
(3, 8, 5, 9, 8, 7, 10, 7, 12, 10, 7, 9, 4),
(4, 9, 10, 11, 7, 13, 11, 10, 6, 9, 6, 5, 7),
(5, 14, 6, 5, 9, 8, 9, 6, 11, 7, 8, 10, 10),
(6, 13, 11, 10, 6, 11, 4, 9, 10, 4, 13, 11, 8),
(7, 13, 11, 10, 6, 11, 4, 9, 10, 4, 13, 11, 8),
(8, 15, 9, 6, 10, 12, 5, 8, 7, 8, 9, 6, 9),
(9, 14, 6, 5, 9, 8, 9, 6, 11, 7, 8, 10, 10),
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
-- Estructura de tabla para la tabla `RET_IVA`
--

CREATE TABLE `RET_IVA` (
  `ID` int(11) NOT NULL,
  `enero` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `febrero` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `marzo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `abril` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mayo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `junio` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `julio` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `agosto` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `septiembre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `octubre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `noviembre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL,
  `diciembre` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Volcado de datos para la tabla `RET_IVA`
--

INSERT INTO `RET_IVA` (`ID`, `enero`, `febrero`, `marzo`, `abril`, `mayo`, `junio`, `julio`, `agosto`, `septiembre`, `octubre`, `noviembre`, `diciembre`) VALUES
(0, '15;28', '9;20', '6;25', '1;23', '6;20', '12;29', '8;27', '14;31', '14;29', '5;20', '13;27', '3;16'),
(1, '6;19', '10;23', '3;20', '14;27', '4;18', '11;26', '3;21', '13;25', '3;18', '14;28', '12;26', '15;29'),
(2, '8;21', '5;18', '9;24', '8;21', '14;29', '3;16', '14;30', '12;24', '10;24', '15;29', '2;17', '4;21'),
(3, '16;30', '12;18', '4;23', '16;30', '7;22', '10;18', '7;23', '5;18', '2;21', '7;23', '9;23', '11;28'),
(4, '9;23', '2;25', '11;26', '7;20', '13;21', '2;19', '10;28', '6;19', '9;30', '6;22', '5;20', '7;22'),
(5, '5;22', '13;27', '12;30', '9;22', '15;28', '8;17', '6;22', '3;21', '15;25', '8;30', '4;18', '10;17'),
(6, '13;20', '4;19', '10;27', '13;24', '5;19', '15;30', '9;20', '4;28', '11;28', '2;21', '11;25', '8;18'),
(7, '12;27', '11;24', '2;18', '6;17', '11;26', '4;22', '15;31', '10;20', '4;22', '13;27', '3;19', '2;18'),
(8, '7;26', '3;26', '13;31', '10;29', '12;27', '5;23', '2;17', '7;26', '8;17', '9;26', '6;24', '9;30'),
(9, '14;29', '6;27', '5;17', '15;28', '8;25', '9;25', '13;29', '11;27', '7;23', '1;19', '10;30', '14;23'),
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
  `id_rif` bigint(20) UNSIGNED NOT NULL,
  `ID_Foranea` int(11) NOT NULL,
  `RIF` varchar(225) NOT NULL,
  `tipo_de_cliente` int(1) NOT NULL,
  `nombre_empresa` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `rif`
--

INSERT INTO `rif` (`id_rif`, `ID_Foranea`, `RIF`, `tipo_de_cliente`, `nombre_empresa`, `created_at`) VALUES
(1, 1, 'J-65997848618', 2, 'Frangos Grill', '2026-10-01 14:01:00'),
(2, 3, '401740120', 2, 'dist m&a sacro, CA', '2026-10-06 14:35:36'),
(3, 5, '401740126', 2, 'distribuidora m&a sacro, c.a', '2026-10-06 15:19:01'),
(4, 4, '302792932', 2, 'Crucero Oriente Sur', '2026-10-06 15:21:05'),
(5, 4, '506928310', 2, 'Mi Pistacho', '2026-10-06 15:30:00'),
(6, 4, '23252526', 1, 'edwar', '2026-10-08 15:59:15'),
(7, 4, '65880239', 2, 'Adan', '2026-10-08 16:10:57'),
(8, 4, '258963888', 1, 'Daniels', '2026-10-08 16:13:10'),
(9, 6, '214142925', 1, 'Servicio Odontológico', '2026-10-09 18:51:08'),
(12, 7, 'J-306649190', 2, 'Soluciones Agrícolas Fernando C.A', '2026-10-13 01:50:19'),
(14, 8, 'J-158848788', 1, 'Juan Carlos C.A', '2026-10-19 23:19:58'),
(15, 4, 'J-2758222', 1, 'Daniels C.A', '2026-10-28 20:58:17'),
(16, 4, 'J-5866957', 1, 'Adan 9', '2026-10-28 21:00:55'),
(17, 4, 'J-5769517', 1, 'afg ca', '2026-10-28 21:03:53'),
(18, 4, 'J-8567541', 1, 'fght ca', '2026-10-28 21:04:20'),
(19, 9, 'J-1268569', 1, 'Ranchs Cóctel', '2026-10-29 19:28:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rif_tax_preferences`
--

CREATE TABLE `rif_tax_preferences` (
  `rif` varchar(225) NOT NULL,
  `anti_islr` tinyint(1) NOT NULL DEFAULT '1',
  `dd_islr` tinyint(1) NOT NULL DEFAULT '1',
  `dec_auto_entdes` tinyint(1) NOT NULL DEFAULT '1',
  `dec_def_islrei` tinyint(1) NOT NULL DEFAULT '1',
  `dec_gra_patri` tinyint(1) NOT NULL DEFAULT '1',
  `dec_igtf` tinyint(1) NOT NULL DEFAULT '1',
  `dec_iva` tinyint(1) NOT NULL DEFAULT '1',
  `dec_prot_pen` tinyint(1) NOT NULL DEFAULT '1',
  `envite_azar` tinyint(1) NOT NULL DEFAULT '1',
  `est_islr_rent` tinyint(1) NOT NULL DEFAULT '1',
  `ret_islr_lot` tinyint(1) NOT NULL DEFAULT '1',
  `ret_islr_rent` tinyint(1) NOT NULL DEFAULT '1',
  `ret_iva` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `rif_tax_preferences`
--

INSERT INTO `rif_tax_preferences` (`rif`, `anti_islr`, `dd_islr`, `dec_auto_entdes`, `dec_def_islrei`, `dec_gra_patri`, `dec_igtf`, `dec_iva`, `dec_prot_pen`, `envite_azar`, `est_islr_rent`, `ret_islr_lot`, `ret_islr_rent`, `ret_iva`) VALUES
('214142925', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('23252526', 0, 1, 0, 0, 0, 0, 1, 1, 1, 1, 1, 1, 1),
('258963888', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('302792932', 1, 1, 1, 1, 1, 1, 1, 1, 0, 1, 0, 1, 1),
('401740120', 0, 1, 0, 1, 1, 1, 1, 1, 0, 1, 0, 0, 1),
('401740126', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('506928310', 0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('65880239', 0, 1, 0, 0, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('J-1268569', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('J-158848788', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('J-2758222', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('J-306649190', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('J-5769517', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('J-5866957', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1),
('J-65997848618', 0, 1, 1, 1, 1, 1, 1, 1, 0, 1, 0, 1, 1),
('J-8567541', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `Num_Rol` int(1) NOT NULL,
  `Rol` varchar(225) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`Num_Rol`, `Rol`) VALUES
(1, 'No Premium'),
(2, 'Premium'),
(3, 'Admin');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_cliente`
--

CREATE TABLE `tipo_cliente` (
  `id_tipo_cliente` int(1) NOT NULL,
  `desc_tc` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Indices de la tabla `ANTI_ISLR`
--
ALTER TABLE `ANTI_ISLR`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `auth_sessions`
--
ALTER TABLE `auth_sessions`
  ADD PRIMARY KEY (`id_session`),
  ADD UNIQUE KEY `uq_auth_session_token_hash` (`token_hash`),
  ADD KEY `idx_auth_session_usuario` (`id_usuario`,`expires_at`,`revoked_at`);

--
-- Indices de la tabla `DD_ISLR`
--
ALTER TABLE `DD_ISLR`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `DEC_AUTO_ENTDES`
--
ALTER TABLE `DEC_AUTO_ENTDES`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `DEC_DEF_ISLREI`
--
ALTER TABLE `DEC_DEF_ISLREI`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `DEC_GRA_PATRI`
--
ALTER TABLE `DEC_GRA_PATRI`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `DEC_IGTF`
--
ALTER TABLE `DEC_IGTF`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `DEC_IVA`
--
ALTER TABLE `DEC_IVA`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `empresas_eliminadas`
--
ALTER TABLE `empresas_eliminadas`
  ADD PRIMARY KEY (`id_eliminada`),
  ADD KEY `idx_empresas_eliminadas_usuario` (`ID_Foranea`,`fecha_eliminacion`),
  ADD KEY `idx_empresas_eliminadas_rif` (`RIF`);

--
-- Indices de la tabla `ENVITE_AZAR`
--
ALTER TABLE `ENVITE_AZAR`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `EST_ISLR_RENT`
--
ALTER TABLE `EST_ISLR_RENT`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `historial_membresias`
--
ALTER TABLE `historial_membresias`
  ADD PRIMARY KEY (`id_historial`),
  ADD UNIQUE KEY `uq_historial_pago` (`id_pago`),
  ADD KEY `idx_historial_usuario_estado_fin` (`id_usuario`,`estado`,`fecha_fin`),
  ADD KEY `idx_historial_admin` (`id_admin`),
  ADD KEY `idx_historial_admin_revocacion` (`id_admin_revocacion`);

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
-- Indices de la tabla `membresia_configuracion`
--
ALTER TABLE `membresia_configuracion`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `membresia_metodos_pago`
--
ALTER TABLE `membresia_metodos_pago`
  ADD PRIMARY KEY (`id_metodo_pago`),
  ADD UNIQUE KEY `uq_membresia_metodo_codigo` (`codigo`);

--
-- Indices de la tabla `membresia_metodo_pago_configuracion`
--
ALTER TABLE `membresia_metodo_pago_configuracion`
  ADD PRIMARY KEY (`id_configuracion_pago`),
  ADD UNIQUE KEY `uq_metodo_config_clave` (`id_metodo_pago`,`clave`),
  ADD KEY `idx_metodo_config_orden` (`id_metodo_pago`,`activo`,`orden`);

--
-- Indices de la tabla `membresia_prueba_datos`
--
ALTER TABLE `membresia_prueba_datos`
  ADD PRIMARY KEY (`id_prueba`),
  ADD UNIQUE KEY `uq_prueba_usuario` (`id_usuario`),
  ADD UNIQUE KEY `uq_prueba_historial` (`id_historial`),
  ADD KEY `idx_prueba_documento` (`documento`),
  ADD KEY `idx_prueba_fecha` (`fecha_solicitud`);

--
-- Indices de la tabla `membresia_tasas_cambio`
--
ALTER TABLE `membresia_tasas_cambio`
  ADD PRIMARY KEY (`id_tasa`),
  ADD UNIQUE KEY `uq_tasa_dia_fuente` (`moneda_base`,`moneda_destino`,`fuente`,`fecha_tasa`),
  ADD KEY `idx_tasa_actual` (`moneda_base`,`moneda_destino`,`activo`,`fecha_tasa`);

--
-- Indices de la tabla `pagos_membresia`
--
ALTER TABLE `pagos_membresia`
  ADD PRIMARY KEY (`id_pago`),
  ADD UNIQUE KEY `uq_pago_metodo_referencia` (`id_metodo_pago`,`num_referencia`),
  ADD UNIQUE KEY `uq_pago_idempotency` (`idempotency_key`),
  ADD KEY `idx_pago_usuario_fecha` (`id_usuario`,`fecha_solicitud`),
  ADD KEY `idx_pago_status_fecha` (`id_membresia_status`,`fecha_solicitud`),
  ADD KEY `idx_pago_admin_revision` (`id_admin_revision`),
  ADD KEY `idx_pago_metodo` (`id_metodo_pago`);

--
-- Indices de la tabla `pagos_membresia_status`
--
ALTER TABLE `pagos_membresia_status`
  ADD PRIMARY KEY (`id_membresia_status`),
  ADD UNIQUE KEY `uq_pagos_membresia_status_codigo` (`codigo_status`);

--
-- Indices de la tabla `pending_email_verifications`
--
ALTER TABLE `pending_email_verifications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_email_purpose` (`email`,`purpose`);

--
-- Indices de la tabla `RET_ISLR_LOT`
--
ALTER TABLE `RET_ISLR_LOT`
  ADD PRIMARY KEY (`ID`);

--
-- Indices de la tabla `rif`
--
ALTER TABLE `rif`
  ADD PRIMARY KEY (`id_rif`),
  ADD UNIQUE KEY `RIF` (`RIF`),
  ADD KEY `ID_Foranea` (`ID_Foranea`),
  ADD KEY `tipo_de_cliente` (`tipo_de_cliente`),
  ADD KEY `idx_rif_usuario_orden` (`ID_Foranea`,`id_rif`);

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
-- AUTO_INCREMENT de la tabla `auth_sessions`
--
ALTER TABLE `auth_sessions`
  MODIFY `id_session` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `empresas_eliminadas`
--
ALTER TABLE `empresas_eliminadas`
  MODIFY `id_eliminada` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `historial_membresias`
--
ALTER TABLE `historial_membresias`
  MODIFY `id_historial` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `logdata`
--
ALTER TABLE `logdata`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `membresia_metodo_pago_configuracion`
--
ALTER TABLE `membresia_metodo_pago_configuracion`
  MODIFY `id_configuracion_pago` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `membresia_prueba_datos`
--
ALTER TABLE `membresia_prueba_datos`
  MODIFY `id_prueba` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `membresia_tasas_cambio`
--
ALTER TABLE `membresia_tasas_cambio`
  MODIFY `id_tasa` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `pagos_membresia`
--
ALTER TABLE `pagos_membresia`
  MODIFY `id_pago` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `pending_email_verifications`
--
ALTER TABLE `pending_email_verifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `rif`
--
ALTER TABLE `rif`
  MODIFY `id_rif` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `tipo_cliente`
--
ALTER TABLE `tipo_cliente`
  MODIFY `id_tipo_cliente` int(1) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `auth_sessions`
--
ALTER TABLE `auth_sessions`
  ADD CONSTRAINT `fk_auth_session_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `logdata` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `historial_membresias`
--
ALTER TABLE `historial_membresias`
  ADD CONSTRAINT `fk_historial_admin` FOREIGN KEY (`id_admin`) REFERENCES `logdata` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_historial_admin_revocacion` FOREIGN KEY (`id_admin_revocacion`) REFERENCES `logdata` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_historial_pago` FOREIGN KEY (`id_pago`) REFERENCES `pagos_membresia` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_historial_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `logdata` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `infoper`
--
ALTER TABLE `infoper`
  ADD CONSTRAINT `infoper_ibfk_1` FOREIGN KEY (`ID_Foranea`) REFERENCES `logdata` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `logdata`
--
ALTER TABLE `logdata`
  ADD CONSTRAINT `logdata_ibfk_1` FOREIGN KEY (`num_rol_data`) REFERENCES `roles` (`Num_Rol`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `membresia_metodo_pago_configuracion`
--
ALTER TABLE `membresia_metodo_pago_configuracion`
  ADD CONSTRAINT `fk_metodo_config_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `membresia_metodos_pago` (`id_metodo_pago`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `membresia_prueba_datos`
--
ALTER TABLE `membresia_prueba_datos`
  ADD CONSTRAINT `fk_prueba_historial` FOREIGN KEY (`id_historial`) REFERENCES `historial_membresias` (`id_historial`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_prueba_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `logdata` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `pagos_membresia`
--
ALTER TABLE `pagos_membresia`
  ADD CONSTRAINT `fk_pago_admin_revision` FOREIGN KEY (`id_admin_revision`) REFERENCES `logdata` (`ID`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pago_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `membresia_metodos_pago` (`id_metodo_pago`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pago_status` FOREIGN KEY (`id_membresia_status`) REFERENCES `pagos_membresia_status` (`id_membresia_status`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pago_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `logdata` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

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
