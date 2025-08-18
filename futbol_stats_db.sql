-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 19-08-2025 a las 00:05:43
-- Versión del servidor: 10.4.25-MariaDB
-- Versión de PHP: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `futbol_stats_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `matches`
--

CREATE TABLE `matches` (
  `match_id` int(11) NOT NULL,
  `match_date` date NOT NULL,
  `rival_team` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `location` enum('Local','Visitante') COLLATE utf8mb4_unicode_ci NOT NULL,
  `strategy` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `goals_for` int(11) DEFAULT 0,
  `goals_against` int(11) DEFAULT 0,
  `substitutions` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `match_observations` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `reported_injuries` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `matches`
--

INSERT INTO `matches` (`match_id`, `match_date`, `rival_team`, `location`, `strategy`, `goals_for`, `goals_against`, `substitutions`, `match_observations`, `reported_injuries`, `created_at`) VALUES
(1, '2025-08-20', 'Real Club Deportivo', 'Local', 'Presión alta y control del mediocampo.', 3, 1, 'Min 75: Entra Pérez, sale González', 'El equipo mostró una gran solidez defensiva y fue letal al contraataque.', 'Rodríguez sufrió una leve torcedura de tobillo.', '2025-08-18 22:05:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `match_lineups`
--

CREATE TABLE `match_lineups` (
  `lineup_id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `is_starter` tinyint(1) DEFAULT 1,
  `estimated_minutes` int(11) DEFAULT 90
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `match_lineups`
--

INSERT INTO `match_lineups` (`lineup_id`, `match_id`, `player_id`, `is_starter`, `estimated_minutes`) VALUES
(1, 1, 1, 1, 90),
(2, 1, 2, 1, 90),
(3, 1, 3, 1, 90);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `players`
--

CREATE TABLE `players` (
  `player_id` int(11) NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `position` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jersey_number` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `players`
--

INSERT INTO `players` (`player_id`, `name`, `position`, `jersey_number`, `created_at`) VALUES
(1, 'García', 'Portero', 1, '2025-08-18 22:05:33'),
(2, 'Rodríguez', 'Defensa', 2, '2025-08-18 22:05:33'),
(3, 'López', 'Defensa', 3, '2025-08-18 22:05:33'),
(4, 'Martínez', 'Defensa', 4, '2025-08-18 22:05:33'),
(5, 'Sánchez', 'Defensa', 5, '2025-08-18 22:05:33'),
(6, 'Fernández', 'Centrocampista', 6, '2025-08-18 22:05:33'),
(7, 'González', 'Centrocampista', 7, '2025-08-18 22:05:33'),
(8, 'Pérez', 'Centrocampista', 8, '2025-08-18 22:05:33'),
(9, 'Ruiz', 'Delantero', 9, '2025-08-18 22:05:33'),
(10, 'Díaz', 'Delantero', 10, '2025-08-18 22:05:33'),
(11, 'Torres', 'Delantero', 11, '2025-08-18 22:05:33');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `player_match_stats`
--

CREATE TABLE `player_match_stats` (
  `stat_id` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `player_id` int(11) NOT NULL,
  `goals` int(11) DEFAULT 0,
  `assists` int(11) DEFAULT 0,
  `yellow_cards` int(11) DEFAULT 0,
  `red_cards` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `player_match_stats`
--

INSERT INTO `player_match_stats` (`stat_id`, `match_id`, `player_id`, `goals`, `assists`, `yellow_cards`, `red_cards`) VALUES
(1, 1, 9, 2, 0, 0, 0),
(2, 1, 10, 1, 1, 1, 0),
(3, 1, 7, 0, 1, 0, 0);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `matches`
--
ALTER TABLE `matches`
  ADD PRIMARY KEY (`match_id`);

--
-- Indices de la tabla `match_lineups`
--
ALTER TABLE `match_lineups`
  ADD PRIMARY KEY (`lineup_id`),
  ADD UNIQUE KEY `match_id` (`match_id`,`player_id`),
  ADD KEY `player_id` (`player_id`);

--
-- Indices de la tabla `players`
--
ALTER TABLE `players`
  ADD PRIMARY KEY (`player_id`),
  ADD UNIQUE KEY `jersey_number` (`jersey_number`);

--
-- Indices de la tabla `player_match_stats`
--
ALTER TABLE `player_match_stats`
  ADD PRIMARY KEY (`stat_id`),
  ADD UNIQUE KEY `match_id` (`match_id`,`player_id`),
  ADD KEY `player_id` (`player_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `matches`
--
ALTER TABLE `matches`
  MODIFY `match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `match_lineups`
--
ALTER TABLE `match_lineups`
  MODIFY `lineup_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `players`
--
ALTER TABLE `players`
  MODIFY `player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `player_match_stats`
--
ALTER TABLE `player_match_stats`
  MODIFY `stat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `match_lineups`
--
ALTER TABLE `match_lineups`
  ADD CONSTRAINT `match_lineups_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `match_lineups_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `player_match_stats`
--
ALTER TABLE `player_match_stats`
  ADD CONSTRAINT `player_match_stats_ibfk_1` FOREIGN KEY (`match_id`) REFERENCES `matches` (`match_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `player_match_stats_ibfk_2` FOREIGN KEY (`player_id`) REFERENCES `players` (`player_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
