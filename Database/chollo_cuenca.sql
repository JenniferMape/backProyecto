-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 06-06-2024 a las 17:58:15
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `chollo_cuenca`
--
CREATE DATABASE IF NOT EXISTS `chollo_cuenca` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `chollo_cuenca`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name_category` varchar(50) NOT NULL,
  `description_category` varchar(100) DEFAULT NULL,
  `created_category` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_category` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE `comments` (
  `id` int(11) NOT NULL,
  `id_user_comment` int(11) NOT NULL,
  `id_offer_comment` int(11) NOT NULL,
  `id_response_comment` int(11) DEFAULT NULL,
  `message_comment` text NOT NULL,
  `is_read_comment` tinyint(1) NOT NULL DEFAULT 0,
  `created_comment` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_comment` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favorites`
--

DROP TABLE IF EXISTS `favorites`;
CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `id_user_favorite` int(11) NOT NULL,
  `id_offer_favorite` int(11) NOT NULL,
  `created_favorite` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_favorite` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `id_category_notification` int(11) DEFAULT NULL,
  `id_offer_notification` int(11) DEFAULT NULL,
  `id_user_notification` int(11) NOT NULL,
  `id_comment_notification` int(11) DEFAULT NULL,
  `type_notification` enum('COMMENT','REPLY','NEW_OFFER') NOT NULL,
  `is_read_notification` tinyint(1) NOT NULL DEFAULT 0,
  `created_notification` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_notification` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `offers`
--

DROP TABLE IF EXISTS `offers`;
CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `id_company_offer` int(11) NOT NULL,
  `id_category_offer` int(11) NOT NULL,
  `title_offer` varchar(100) NOT NULL,
  `description_offer` text NOT NULL,
  `start_date_offer` datetime NOT NULL,
  `end_date_offer` datetime NOT NULL,
  `discount_code_offer` varchar(50) DEFAULT NULL,
  `image_offer` varchar(255) DEFAULT NULL,
  `web_offer` varchar(255) DEFAULT NULL,
  `address_offer` varchar(255) DEFAULT NULL,
  `created_offer` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_offer` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `subscriptions`
--

DROP TABLE IF EXISTS `subscriptions`;
CREATE TABLE `subscriptions` (
  `id` int(11) NOT NULL,
  `id_user_subscription` int(11) NOT NULL,
  `id_category_subscription` int(11) NOT NULL,
  `created_subscription` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_subscription` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name_user` varchar(50) NOT NULL,
  `email_user` varchar(100) NOT NULL,
  `password_user` varchar(250) NOT NULL,
  `avatar_user` varchar(255) DEFAULT NULL,
  `type_user` enum('CLIENT','COMPANY') NOT NULL DEFAULT 'CLIENT',
  `cif_user` varchar(50) DEFAULT NULL,
  `created_user` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_user` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;


--
-- Disparadores `users`
--
DROP TRIGGER IF EXISTS `before_user_insert`;
DELIMITER $$
CREATE TRIGGER `before_user_insert` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.type_user = 'CLIENT' THEN
        SET NEW.cif_user = NULL;
    END IF;
END
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `before_user_update`;
DELIMITER $$
CREATE TRIGGER `before_user_update` BEFORE UPDATE ON `users` FOR EACH ROW BEGIN
    IF NEW.type_user = 'CLIENT' THEN
        SET NEW.cif_user = NULL;
    END IF;
END
$$
DELIMITER ;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_user_comment` (`id_user_comment`),
  ADD KEY `fk_id_offer_comment` (`id_offer_comment`),
  ADD KEY `fk_id_response_comment` (`id_response_comment`);

--
-- Indices de la tabla `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_user_favorite` (`id_user_favorite`),
  ADD KEY `fk_id_offer_favorite` (`id_offer_favorite`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_category_notification` (`id_category_notification`),
  ADD KEY `fk_id_offer_notification` (`id_offer_notification`),
  ADD KEY `fk_id_user_notification` (`id_user_notification`),
  ADD KEY `fk_id_comment_notification` (`id_comment_notification`);

--
-- Indices de la tabla `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_company_offer` (`id_company_offer`),
  ADD KEY `fk_id_category_offer` (`id_category_offer`);

--
-- Indices de la tabla `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_user_subscription` (`id_user_subscription`),
  ADD KEY `fk_id_category_subscription` (`id_category_subscription`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cif_user` (`cif_user`),
  ADD UNIQUE KEY `name_user` (`name_user`),
  ADD UNIQUE KEY `email_user` (`email_user`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_id_offer_comment` FOREIGN KEY (`id_offer_comment`) REFERENCES `offers` (`id`),
  ADD CONSTRAINT `fk_id_response_comment` FOREIGN KEY (`id_response_comment`) REFERENCES `comments` (`id`),
  ADD CONSTRAINT `fk_id_user_comment` FOREIGN KEY (`id_user_comment`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_id_offer_favorite` FOREIGN KEY (`id_offer_favorite`) REFERENCES `offers` (`id`),
  ADD CONSTRAINT `fk_id_user_favorite` FOREIGN KEY (`id_user_favorite`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_id_category_notification` FOREIGN KEY (`id_category_notification`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_id_comment_notification` FOREIGN KEY (`id_comment_notification`) REFERENCES `comments` (`id`),
  ADD CONSTRAINT `fk_id_offer_notification` FOREIGN KEY (`id_offer_notification`) REFERENCES `offers` (`id`),
  ADD CONSTRAINT `fk_id_user_notification` FOREIGN KEY (`id_user_notification`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `fk_id_category_offer` FOREIGN KEY (`id_category_offer`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_id_company_offer` FOREIGN KEY (`id_company_offer`) REFERENCES `users` (`id`);

--
-- Filtros para la tabla `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD CONSTRAINT `fk_id_category_subscription` FOREIGN KEY (`id_category_subscription`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_id_user_subscription` FOREIGN KEY (`id_user_subscription`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
