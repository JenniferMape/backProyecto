-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 04-12-2024 a las 19:17:42
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `chollo_cuenca`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name_category` varchar(50) NOT NULL,
  `description_category` varchar(100) DEFAULT NULL,
  `created_category` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_category` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `categories`
--

INSERT INTO `categories` (`id`, `name_category`, `description_category`, `created_category`, `updated_category`) VALUES
(1, 'Alimentación', '\"Ofertas en productos de comida y bebidas, desde alimentos básicos hasta gourmet.\"', '2024-11-03 21:10:21', '2024-11-03 21:10:21'),
(2, 'Automoción', 'Descuentos en accesorios y servicios para vehículos, incluyendo repuestos, herramientas y más.', '2024-11-03 21:10:42', '2024-11-03 21:10:42'),
(3, 'Electrónica', 'Promociones en dispositivos tecnológicos, como teléfonos, computadoras, y equipos de audio.', '2024-11-03 21:11:07', '2024-11-03 21:11:07'),
(4, 'Hogar', 'Ofertas para el hogar, incluyendo muebles, electrodomésticos y decoración.', '2024-11-03 21:11:26', '2024-11-03 21:11:26'),
(5, 'Moda', 'Descuentos en ropa, calzado y accesorios de las mejores marcas y tiendas.', '2024-11-03 21:12:39', '2024-11-03 21:12:39'),
(6, 'Ocio y cultura', 'Promociones en entretenimiento, eventos culturales, libros, música y mucho más.', '2024-11-03 21:13:02', '2024-11-03 21:13:02'),
(7, 'Salud', 'Ofertas en productos de bienestar, incluyendo suplementos, productos de higiene y cuidado personal.', '2024-11-03 21:13:22', '2024-11-03 21:13:22'),
(8, 'Salud', 'Ofertas en productos de bienestar, incluyendo suplementos, productos de higiene y cuidado personal.', '2024-11-05 08:52:48', '2024-11-05 08:52:48'),
(9, 'Salud', 'Ofertas en productos de bienestar, incluyendo suplementos, productos de higiene y cuidado personal.', '2024-11-05 08:52:50', '2024-11-05 08:52:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `favorites`
--

CREATE TABLE `favorites` (
  `id` int(11) NOT NULL,
  `id_user_favorite` int(11) NOT NULL,
  `id_offer_favorite` int(11) NOT NULL,
  `created_favorite` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_favorite` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `favorites`
--

INSERT INTO `favorites` (`id`, `id_user_favorite`, `id_offer_favorite`, `created_favorite`, `updated_favorite`) VALUES
(6, 28, 49, '2024-11-24 13:52:42', '2024-11-24 13:52:42'),
(7, 18, 45, '2024-11-24 17:35:26', '2024-11-24 17:35:26'),
(20, 28, 40, '2024-11-24 18:18:57', '2024-11-24 18:18:57'),
(24, 28, 45, '2024-12-01 16:54:13', '2024-12-01 16:54:13'),
(25, 28, 51, '2024-12-01 20:11:27', '2024-12-01 20:11:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `offers`
--

CREATE TABLE `offers` (
  `id` int(11) NOT NULL,
  `id_company_offer` int(11) NOT NULL,
  `id_category_offer` int(11) NOT NULL,
  `title_offer` varchar(100) NOT NULL,
  `description_offer` text NOT NULL,
  `new_price_offer` decimal(11,2) NOT NULL,
  `original_price_offer` decimal(11,2) NOT NULL,
  `start_date_offer` datetime NOT NULL,
  `end_date_offer` datetime NOT NULL,
  `discount_code_offer` varchar(50) DEFAULT NULL,
  `image_offer` varchar(255) DEFAULT NULL,
  `web_offer` varchar(255) DEFAULT NULL,
  `address_offer` varchar(255) DEFAULT NULL,
  `created_offer` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_offer` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `offers`
--

INSERT INTO `offers` (`id`, `id_company_offer`, `id_category_offer`, `title_offer`, `description_offer`, `new_price_offer`, `original_price_offer`, `start_date_offer`, `end_date_offer`, `discount_code_offer`, `image_offer`, `web_offer`, `address_offer`, `created_offer`, `updated_offer`) VALUES
(40, 18, 6, 'Dragon Ball Gt - Serie Completa - Episodio 1 A 64', ' Un desastroso plan del villano Pilaf acaba con Goku convertido de nuevo en niño y unas misteriosas bolas de dragón oscuras esparcidas por todo el universo. Kaioh informa a nuestros amigos que si las bolas no son devueltas a la Tierra, ésta explotará. Así, Goku, Trunks y Pan se embarcan en una nueva aventura repleta de humor y acción a través del universo para reunir las infames bolas de dragón oscuras', 30.00, 60.00, '2024-11-18 00:00:00', '2024-11-24 00:00:00', 'BLACKMONTH10', 'http://chollocuenca.com/uploads/offers/18/40/673cebd657817.jpg', 'https://shop.selecta-vision.com/dragon-ball-gt-serie-completa-episodio-1-a-64', NULL, '2024-11-19 20:49:42', '2024-11-20 20:09:06'),
(41, 18, 3, 'WD Black SN770 SSD 1TB M.2 NVMe PCIe 4.0', 'SSD PCIe 4.0 de gama media.\r\nCaracterísticas:\r\nInterfaz: PCI Express 4.0\r\nVelocidad de lectura: 5150 MB/s\r\nVelocidad de escritura: 4900 MB/s\r\nFactor de forma de disco SSD: M.2\r\nTipo de memoria: 3D TLC\r\nCalificación TBW: 600\r\nDRAM: No\r\nLectura aleatoria (4KB): 740000 IOPS\r\nEscritura aleatoria (4KB): 800000 IOPS', 67.99, 74.99, '2024-11-04 00:00:00', '2024-11-26 00:00:00', 'BORTIPCC ', 'http://chollocuenca.com/uploads/offers/18/41/673ced7477694.jpg', 'https://www.pccomponentes.com/disco-duro-wd-black-sn770-1tb-disco-ssd-5150mb-s-nvme-pcie-40-m2-gen4-16gt-s', NULL, '2024-11-19 20:56:36', '2024-11-20 20:09:09'),
(42, 18, 5, 'ADIDAS Run 60s 3.0. Tallas 39 a 48', 'Compra una talla más grande\r\nHorma clásica\r\nCierre de cordones\r\nEmpeine textil\r\nForro textil\r\nMediasuela de EVA', 33.60, 60.00, '2024-11-18 00:00:00', '2024-11-26 00:00:00', NULL, 'http://chollocuenca.com/uploads/offers/18/42/673cedec4ce15.jpg', 'https://www.amazon.es/dp/B0CKS2TF2Q?ref_=cm_sw_r_apan_dp_ZWB9PQ0TEYE8D4X46EPJ&starsLeft=1&skipTwisterOG=1', NULL, '2024-11-19 20:58:36', '2024-11-20 20:09:12'),
(43, 18, 3, 'Lian Li Lancool 207 - Caja PC ATX (Negra o blanca) con 4 ventiladores incluidos', 'LANCOOL 207 reinventa el diseño ATX tradicional, que combina una compatibilidad potente y una refrigeración superior en un chasis elegante y compacto.\r\n\r\nDISPONIBLE EN 2 COLORES\r\nLANCOOL 207 está disponible en el clásico negro y en el refrescante blanco, lo que garantiza una combinación perfecta con tu estilo.\r\n\r\nPOTENCIA COMPACTA\r\nAunque es de tamaño compacto, el LANCOOL 207 ofrece una compatibilidad notable, ya que admite una placa base ATX, un radiador de 360 mm, una fuente de alimentación ATX y una GPU de hasta 375 mm, todo dentro de un elegante chasis de 45.5 L.\r\n\r\nIncluye cuatro ventiladores de alto rendimiento\r\nDos ventiladores de rendimiento ARGB gruesos brindan una excelente refrigeración y sorprendentes efectos de iluminación con el espejo infinito en el centro. Dos ventiladores de alto flujo de aire de 11 aspas en la parte inferior envían un flujo de aire directo a la GPU.', 60.70, 85.00, '2024-11-01 00:00:00', '2024-11-30 00:00:00', '', 'http://chollocuenca.com/uploads/offers/18/43/673e2bee1adfc.jpg', 'https://www.pcexpansion.es/lian-li-lancool-207-black.php?network=tradetracker&utm_source=affiliate&utm_medium=171764', 'Calle carretería,  27 local', '2024-11-20 19:35:26', '2024-12-01 20:12:02'),
(44, 18, 1, 'Gullón Galleta Dorada al Horno, sin Azúcar, 330g', 'Harina de trigo, aceite vegetal (aceite de girasol alto en oleico), edulcorantes (maltitol e isomalt), fibra vegetal, agentes elevadores (carbonato ácido de sodio y carbonato ácido de amonio), sal, decoración láctea (proteínas de la leche y dextrosa), aroma de vainilla.', 1.91, 2.95, '2024-11-20 00:00:00', '2024-12-08 00:00:00', 'GULLON', 'http://chollocuenca.com/uploads/offers/18/44/673e2ceca0c89.jpg', 'https://www.amazon.es/dp/B003NNUHZ4?smid=A1AT7YVPFBWXBL', NULL, '2024-11-20 19:39:40', '2024-11-20 20:09:15'),
(45, 18, 1, '2 x ColaCao Noir 0% Azúcares Añadidos, 300g', 'Añadir dos y se descuenta el 50% de la segunda unidad. Precio con compra recurrente\r\n\r\nEXPERIENCIA INTENSA CON 4 VARIEDADES DE CACAO: Disfruta de un ColaCao más intenso, con un delicioso sabor gracias a la combinación perfectamente equilibrada de 4 tipos diferentes de cacao, que aportan una riqueza de sabor única.\r\nCANTIDAD Y PORCIONES: El envase de 300 gramos ofrece 40 raciones, lo que lo convierte en una opción ideal para disfrutar durante un largo periodo de tiempo. Cada porción está diseñada para ofrecer un equilibrio perfecto entre sabor y nutrición, sin comprometer la calidad.\r\nDISFRUTA EN CUALQUIER MOMENTO: Sin azúcares añadidos. Ideal para el desayuno, la merienda, la cena o como snack.\r\nPROCESO TRADICIONAL DEL CACAO: Cacao natural recolectado a mano, secado al sol, tostado, prensado y mezclado de manera tradicional.\r\nPLACER Y NUTRICIÓN: Disfruta de un equilibrio perfecto entre sabor y beneficios nutricionales con el cacao natural que se mantiene fiel a sus raíces y a tus necesidades.', 5.73, 8.48, '2024-11-22 00:00:00', '2024-12-02 00:00:00', 'BLACKMONTH10', 'http://chollocuenca.com/uploads/offers/18/45/674c3f2717d81.jpg', 'https://www.amazon.es/dp/B07PTSK2FG?smid=A1AT7YVPFBWXBL&tag=pepperugc-21&ascsubtag=ppr-es-821329956', '', '2024-11-20 19:42:16', '2024-12-01 11:49:11'),
(46, 18, 4, 'Felpudo Entrada Harry Potter, Fibra de Coco, Antideslizante, Negro, 60 x 40 cm', 'FELPUDO HARRY POTTER. Estampado con un simpático mensaje que da la bienvenida a los muggles, este felpudo de Harry Potter sacará más de una sonrisa a todos tus invitados cuando lean \"Muggles Welcome\" a la entrada de tu casa.\r\nREGALO PARA FRIKIS Y FANS DE LA SAGA. Si quieres sorprender a fans de la saga de Harry Potter, este es tu regalo: un felpudo friki por su cumpleaños, su santo, en Navidad o cualquier ocasión que lo merezca.\r\nFIBRA DE COCO NATURAL Y VINILO ANTIDESLIZANTE. Esta alfombra para la entrada de casa está confeccionada con fibra de coco natural 60%, cuyas cerdas eliminan fácilmente la suciedad de los zapatos, y con vinilo antideslizante 40% que aísla la humedad y evita que se mueva.\r\nFELPUDO CASA 60 X 40 CM. Las dimensiones de este felpudo exterior son 60 x 40 cm, con 17 mm de espesor. Limpiarlo y moverlo es muy fácil, por su ligero peso', 16.95, 25.00, '2024-11-18 00:00:00', '2025-01-05 00:00:00', 'SD_TOYS', 'http://chollocuenca.com/uploads/offers/18/46/673e2ee9a8e70.jpg', 'https://www.amazon.es/dp/B0974Y73H3?ref=cm_sw_r_apan_dp_W9E8KZHDH68989XSGGBA&ref_=cm_sw_r_apan_dp_W9E8KZHDH68989XSGGBA&social_share=cm_sw_r_apan_dp_W9E8KZHDH68989XSGGBA&starsLeft=1&skipTwisterOG=1&tag=pepperegc0e-21&ascsubtag=ppr-es-821331025', NULL, '2024-11-20 19:48:09', '2024-11-20 20:08:45'),
(49, 18, 2, 'Benelli Leoncino 500', 'Benelli Leoncino 500cc, tanto la versión asfáltica como la trail.\r\n\r\nPotencia máxima para el A2 (47 cv), 46 Nm de par, horquilla y frenos delanteros sobredimensionados.\r\n\r\nSeguro que más de uno empezará con lo que es una moto china y tal pero vamos, si alguien tiene otra 500cc de 47 cv en el mercado con matriculación y seguro incluido por menos de 5000€ que la publique aquí. Y ya no digamos la versión Trail, con el mismo precio, suspension de más recorrido y llantas multirradios.\r\n\r\nLleva 6 años en el mercado y se sigue vendiendo. Además tiene bastantes piezas y accesorios aftermarket.', 4500.00, 5590.00, '2024-11-24 00:00:00', '2024-12-08 00:00:00', NULL, 'http://chollocuenca.com/uploads/offers/18/49/673e2fed80859.png', 'https://www.benelli.com/es-es/products/leoncino-500', NULL, '2024-11-20 19:52:29', '2024-11-23 18:58:04'),
(50, 18, 7, 'Neutrogena Hydro Boost Loción en Gel Ultraligera (400 ml)', 'Con ácido hialurónico: Esta loción para el cuidado corporal contiene un 17% de glicerina y ácido hialurónico, que retiene la hidratación y mantiene el equilibrio de la piel\r\nRefrescante e hidratante: Esta crema corporal hidratante Hydro Boost refresca al instante y proporciona una hidratación diaria esencial para una piel radiante y flexible\r\nUltraligera: Esta loción Neutrogena corporal en gel ultraligera es apta para todo tipo de pieles y se funde con la piel para que puedas vestirte inmediatamente\r\nDesarrollada por dermatólogos: La fórmula de alta tolerancia de esta crema Neutrogena hidrata intensamente durante 72 horas y ha sido probada clínicamente\r\nEnvase reciclable: Tanto el dosificador como la botella de 400 ml de esta crema hidratante corporal Hydro Boost de Neutrogena son 100% reciclables', 5.64, 9.90, '2024-11-04 00:00:00', '2024-12-08 00:00:00', '', 'http://chollocuenca.com/uploads/offers/18/50/673e308ae4c98.jpg', 'https://www.amazon.es/dp/B07CRNFR8D?smid=A1AT7YVPFBWXBL', '', '2024-11-20 19:55:06', '2024-11-24 17:05:57'),
(51, 18, 3, 'TV Oled 55\" LG', 'El TV LG OLED55C45LA te ofrecerá el único negro puro que aporta máximo realismo, ahora un 30% más brillante (Brightness Booster Max), y calidad de cine Dolby Vision y Atmos.\r\n\r\nProcesador inteligente que ofrece la verdadera experiencia OLED\r\nMáximo rendimiento con el único procesador de máxima potencia creado para OLED evo. Maximiza la calidad de imagen y sonido a través de IA (Procesador 4K α9).\r\n\r\nDolby Vision & Modo FILMMAKER. Vive la emoción de cada escena\r\nLleva la experiencia de cine a tu salón gracias a la imagen ultravívida de Dolby Vision. Además, puedes activar el modo FILMMAKER™ que optimiza la imagen para preservar la intención del director y que veas la película tal y como la creó.', 995.87, 1480.00, '2024-11-25 00:00:00', '2024-11-30 00:00:00', 'OLED55C45LA', 'http://chollocuenca.com/uploads/offers/18/51/673f73dbc12bd.jpeg', 'https://www.mediamarkt.es/es/product/_tv-oled-55-lg-oled55c45la-oled-4k-procesador-inteligente-4k-a9-gen7-smart-tv-dvb-t2-h265-negro-1572283.html', '', '2024-11-21 18:54:35', '2024-11-21 18:55:24');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name_user` varchar(50) NOT NULL,
  `email_user` varchar(100) NOT NULL,
  `password_user` varchar(250) NOT NULL,
  `avatar_user` varchar(255) DEFAULT NULL,
  `type_user` enum('CLIENT','COMPANY','ADMIN') NOT NULL DEFAULT 'CLIENT',
  `cif_user` varchar(50) DEFAULT NULL,
  `created_user` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_user` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name_user`, `email_user`, `password_user`, `avatar_user`, `type_user`, `cif_user`, `created_user`, `updated_user`) VALUES
(18, 'ASDFAD', 'probando@gmail.com', '$2y$10$kq1XtBwp/wcAdHtjwRN9XOXjmVDQi5BTos6WRs0s5qCu07CqN7PoS', 'http://chollocuenca.com/uploads/avatars/18.jpg', 'COMPANY', 'A12345678', '2024-09-12 12:04:31', '2024-11-30 20:35:54'),
(28, 'Domingo', 'moset@palomo.es', '$2y$10$lU5SsC6MryrstvkmUp3huOvb.AK6mS5domtySemSIizpp.GIfB7lK', 'http://chollocuenca.com/uploads/avatars/28.jpg', 'CLIENT', NULL, '2024-10-24 18:25:08', '2024-11-30 20:12:44');

--
-- Disparadores `users`
--
DELIMITER $$
CREATE TRIGGER `before_user_insert` BEFORE INSERT ON `users` FOR EACH ROW BEGIN
    IF NEW.type_user = 'CLIENT' THEN
        SET NEW.cif_user = NULL;
    END IF;
END
$$
DELIMITER ;
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
-- Indices de la tabla `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_user_favorite` (`id_user_favorite`),
  ADD KEY `fk_id_offer_favorite` (`id_offer_favorite`);

--
-- Indices de la tabla `offers`
--
ALTER TABLE `offers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_id_company_offer` (`id_company_offer`),
  ADD KEY `fk_id_category_offer` (`id_category_offer`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name_user` (`name_user`),
  ADD UNIQUE KEY `email_user` (`email_user`),
  ADD UNIQUE KEY `cif_user` (`cif_user`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `favorites`
--
ALTER TABLE `favorites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `offers`
--
ALTER TABLE `offers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_id_offer_favorite_unique` FOREIGN KEY (`id_offer_favorite`) REFERENCES `offers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_id_user_favorite_unique` FOREIGN KEY (`id_user_favorite`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `offers`
--
ALTER TABLE `offers`
  ADD CONSTRAINT `fk_id_category_offer` FOREIGN KEY (`id_category_offer`) REFERENCES `categories` (`id`),
  ADD CONSTRAINT `fk_id_company_offer_unique` FOREIGN KEY (`id_company_offer`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
