SET FOREIGN_KEY_CHECKS=0;



DROP TABLE IF EXISTS `_cfg_andreani`;

DROP TABLE IF EXISTS `_cfg_captcha`;

DROP TABLE IF EXISTS `_cfg_checkout`;

DROP TABLE IF EXISTS `_cfg_configheader`;

DROP TABLE IF EXISTS `_cfg_contacto`;

DROP TABLE IF EXISTS `_cfg_email`;

DROP TABLE IF EXISTS `_cfg_exportador_meli`;

DROP TABLE IF EXISTS `_cfg_hubspot`;

DROP TABLE IF EXISTS `_cfg_impuestos`;

DROP TABLE IF EXISTS `_cfg_marketing`;

DROP TABLE IF EXISTS `_cfg_mercadolibre`;

DROP TABLE IF EXISTS `_cfg_pagos`;

DROP TABLE IF EXISTS `_cfg_perfiles_ecommerce`;

DROP TABLE IF EXISTS `_cfg_redes`;

DROP TABLE IF EXISTS `_localidades`;

DROP TABLE IF EXISTS `_provincias`;

DROP TABLE IF EXISTS `area`;

DROP TABLE IF EXISTS `banners`;

DROP TABLE IF EXISTS `categorias`;

DROP TABLE IF EXISTS `comentarios`;

DROP TABLE IF EXISTS `contenidos`;

DROP TABLE IF EXISTS `descuentos`;

DROP TABLE IF EXISTS `detalle_pedidos`;

DROP TABLE IF EXISTS `envios`;

DROP TABLE IF EXISTS `envios_pedidos`;

DROP TABLE IF EXISTS `estados_pedidos`;

DROP TABLE IF EXISTS `favoritos`;

DROP TABLE IF EXISTS `idiomas`;

DROP TABLE IF EXISTS `imagenes`;

DROP TABLE IF EXISTS `landing`;

DROP TABLE IF EXISTS `landing_subs`;

DROP TABLE IF EXISTS `menu`;

DROP TABLE IF EXISTS `mercadolibre`;

DROP TABLE IF EXISTS `opciones`;

DROP TABLE IF EXISTS `opciones_valor`;

DROP TABLE IF EXISTS `pagos`;

DROP TABLE IF EXISTS `pagos_pedidos`;

DROP TABLE IF EXISTS `pedidos`;

DROP TABLE IF EXISTS `productos`;

DROP TABLE IF EXISTS `productos_relacionados`;

DROP TABLE IF EXISTS `productos_visitados`;

DROP TABLE IF EXISTS `promos`;

DROP TABLE IF EXISTS `roles`;

DROP TABLE IF EXISTS `roles_admin`;

DROP TABLE IF EXISTS `seo`;

DROP TABLE IF EXISTS `subcategorias`;

DROP TABLE IF EXISTS `tercercategorias`;

DROP TABLE IF EXISTS `token_ml`;

DROP TABLE IF EXISTS `usuarios`;

DROP TABLE IF EXISTS `usuarios_ip`;



CREATE TABLE `_cfg_andreani` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `contraseña` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `envio_sucursal` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `envio_domicilio` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `envio_urgente` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_captcha` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `captcha_key` longtext COLLATE utf8_unicode_ci NOT NULL,
  `captcha_secret` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_checkout` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `estado` int(11) NOT NULL DEFAULT '0',
  `mostrar_precio` int(11) NOT NULL DEFAULT '0',
  `envio` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `pago` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_configheader` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content_header` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_contacto` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `whatsapp` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `messenger` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `email` longtext COLLATE utf8_unicode_ci NOT NULL,
  `domicilio` longtext COLLATE utf8_unicode_ci NOT NULL,
  `localidad` longtext COLLATE utf8_unicode_ci NOT NULL,
  `provincia` longtext COLLATE utf8_unicode_ci NOT NULL,
  `pais` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `remitente` longtext COLLATE utf8_unicode_ci NOT NULL,
  `smtp` longtext COLLATE utf8_unicode_ci NOT NULL,
  `smtp_secure` longtext COLLATE utf8_unicode_ci NOT NULL,
  `puerto` int(11) NOT NULL,
  `email` longtext COLLATE utf8_unicode_ci NOT NULL,
  `password` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_exportador_meli` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `clasica` float NOT NULL DEFAULT '0',
  `premium` float NOT NULL DEFAULT '0',
  `calcular_envio` tinyint(1) DEFAULT '0',
  `link_json` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL,
  `carpeta_img` varchar(400) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_hubspot` (
  `api_key` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_impuestos` (
  `cod` varchar(10) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL,
  `valor` int(11) DEFAULT '0',
  `tipo` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_marketing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `google_data_studio_id` longtext COLLATE utf8_unicode_ci,
  `google_analytics` longtext COLLATE utf8_unicode_ci,
  `hubspot` longtext COLLATE utf8_unicode_ci,
  `mailrelay` longtext COLLATE utf8_unicode_ci,
  `onesignal` longtext COLLATE utf8_unicode_ci,
  `facebook_pixel` longtext COLLATE utf8_unicode_ci,
  `facebook_app_comment` longtext COLLATE utf8_unicode_ci,
  `facebook_access_token` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_mercadolibre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `app_id` longtext COLLATE utf8_unicode_ci NOT NULL,
  `app_secret` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable1` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variable2` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `variable3` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `empresa` longtext COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_cfg_perfiles_ecommerce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `activo` tinyint(1) DEFAULT '0',
  `minorista` tinyint(1) DEFAULT '1',
  `recargo_factura` float DEFAULT '0',
  `remarcado_productos` float DEFAULT '0',
  `mostrar_precios` tinyint(1) DEFAULT '0',
  `usar_stock` tinyint(1) DEFAULT '0',
  `mostrar_sin_stock` tinyint(1) DEFAULT '0',
  `saltar_checkout` varchar(255) NOT NULL,
  `estado_pedido` int(11) NOT NULL,
  `metodo_envio` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `metodo_pago` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `pedido_whatsapp` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_perfil_estado-pedido` (`estado_pedido`),
  KEY `fk_perfil_metodo-envio` (`metodo_envio`),
  KEY `fk_perfil_metodo-pago` (`metodo_pago`),
  CONSTRAINT `fk_perfil_estado-pedido` FOREIGN KEY (`estado_pedido`) REFERENCES `estados_pedidos` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_metodo-envio` FOREIGN KEY (`metodo_envio`) REFERENCES `envios` (`cod`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_perfil_metodo-pago` FOREIGN KEY (`metodo_pago`) REFERENCES `pagos` (`cod`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

CREATE TABLE `_cfg_redes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `facebook` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `twitter` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `instagram` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `linkedin` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `youtube` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `googleplus` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_localidades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `provincia_id` int(11) NOT NULL,
  `nombre` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `codigopostal` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22965 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `_provincias` (
  `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `area` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `titulo` longtext COLLATE utf8_unicode_ci NOT NULL,
  `archivo_area` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `archivo_individual` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unicos` (`cod`,`idioma`) USING BTREE,
  KEY `fgk_idioma_area` (`idioma`),
  CONSTRAINT `FK_idiomas_area` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `banners` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titulo` longtext COLLATE utf8_unicode_ci,
  `titulo_on` tinyint(1) NOT NULL DEFAULT '0',
  `subtitulo` longtext COLLATE utf8_unicode_ci,
  `subtitulo_on` tinyint(1) NOT NULL DEFAULT '0',
  `categoria` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` mediumtext COLLATE utf8_unicode_ci,
  `link_on` tinyint(1) NOT NULL DEFAULT '0',
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'es',
  `fecha` date DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Indice` (`cod`,`categoria`,`idioma`) USING BTREE,
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `FK_categorias_banners` (`categoria`,`idioma`) USING BTREE,
  KEY `FK_idiomas_banners` (`idioma`),
  CONSTRAINT `FK_categorias_banners` FOREIGN KEY (`categoria`, `idioma`) REFERENCES `categorias` (`cod`, `idioma`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_idiomas_banners` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `area` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `descripcion` longtext COLLATE utf8_unicode_ci,
  `orden` int(11) NOT NULL DEFAULT '0',
  `free_shipping` int(11) DEFAULT '0',
  `idioma` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `FK_idiomas_categorias` (`idioma`),
  KEY `Indice` (`cod`,`titulo`,`area`) USING BTREE,
  CONSTRAINT `FK_idiomas_categorias` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod_url` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `id_comentario` int(11) NOT NULL,
  `usuario` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `comentario` longtext COLLATE utf8_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fgk_usuario` (`usuario`),
  KEY `Indice` (`cod_url`,`id_comentario`,`usuario`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `contenidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titulo` longtext COLLATE utf8_unicode_ci,
  `subtitulo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `contenido` longtext COLLATE utf8_unicode_ci,
  `categoria` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subcategoria` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` longtext COLLATE utf8_unicode_ci,
  `description` longtext COLLATE utf8_unicode_ci,
  `link` longtext COLLATE utf8_unicode_ci,
  `area` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `fecha` date DEFAULT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  `destacado` tinyint(1) NOT NULL DEFAULT '0',
  `bloquear` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `Indice` (`subcategoria`,`categoria`,`area`) USING BTREE,
  KEY `FK_idiomas_contenidos` (`idioma`),
  KEY `FK_area_contenidos` (`area`,`idioma`),
  KEY `FK_categorias_contenidos` (`categoria`,`idioma`),
  KEY `FK_subcategorias_contenidos` (`subcategoria`,`idioma`),
  KEY `destacado` (`destacado`),
  CONSTRAINT `FK_area_contenidos` FOREIGN KEY (`area`, `idioma`) REFERENCES `area` (`cod`, `idioma`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_categorias_contenidos` FOREIGN KEY (`categoria`, `idioma`) REFERENCES `categorias` (`cod`, `idioma`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_idiomas_contenidos` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_subcategorias_contenidos` FOREIGN KEY (`subcategoria`, `idioma`) REFERENCES `subcategorias` (`cod`, `idioma`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `descuentos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `monto` double DEFAULT NULL,
  `categorias_cod` mediumtext COLLATE utf8_unicode_ci,
  `subcategorias_cod` mediumtext COLLATE utf8_unicode_ci,
  `productos_cod` mediumtext COLLATE utf8_unicode_ci,
  `tipo` tinyint(1) DEFAULT '0',
  `sector` int(11) NOT NULL DEFAULT '0',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `todos_productos` int(11) DEFAULT NULL,
  `todas_categorias` int(11) DEFAULT NULL,
  `todas_subcategorias` int(11) DEFAULT NULL,
  `acumular` tinyint(1) NOT NULL DEFAULT '0',
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `fgk_idiomas_descuentos` (`idioma`),
  CONSTRAINT `FK_idiomas_descuentos` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `detalle_pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT '1',
  `promo` int(11) DEFAULT NULL,
  `precio` float DEFAULT NULL,
  `precio_inicial` float DEFAULT NULL,
  `cod_producto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `producto_cod` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `producto` varchar(255) COLLATE utf8_unicode_ci DEFAULT '0',
  `tipo` longtext COLLATE utf8_unicode_ci,
  `descuento` longtext COLLATE utf8_unicode_ci,
  `cuotas` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_pedidos_detalle-pedidos` (`cod`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `envios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titulo` longtext COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8_unicode_ci,
  `opciones` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `peso` int(11) DEFAULT NULL,
  `precio` float NOT NULL,
  `estado` int(11) NOT NULL,
  `limite` float DEFAULT NULL,
  `localidad` text COLLATE utf8_unicode_ci,
  `tipo_usuario` int(11) NOT NULL DEFAULT '0',
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `FK_idiomas_envios` (`idioma`) USING BTREE,
  CONSTRAINT `FK_idiomas_envios` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `envios_pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `apellido` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `telefono` varchar(16) COLLATE utf8_unicode_ci NOT NULL,
  `celular` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `postal` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `provincia` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `localidad` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `calle` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `numero` varchar(7) COLLATE utf8_unicode_ci NOT NULL,
  `piso` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `similar` tinyint(1) DEFAULT '0',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `soft_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_estado_pedidos_usuario` (`usuario`),
  CONSTRAINT `fk_envios_pedidos_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `estados_pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `estado` enum('0','1','2','3') COLLATE utf8_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `asunto` longtext COLLATE utf8_unicode_ci,
  `mensaje` longtext COLLATE utf8_unicode_ci,
  `enviar` tinyint(1) NOT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `defecto` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`titulo`,`idioma`) USING BTREE,
  KEY `FK_idiomas_estados-pedidos` (`idioma`),
  KEY `Indice` (`estado`,`idioma`) USING BTREE,
  CONSTRAINT `FK_idiomas_estados-pedidos` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `favoritos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `producto` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`usuario`,`producto`,`idioma`) USING BTREE,
  KEY `FK_productos_favoritos` (`producto`) USING BTREE,
  KEY `FK_usuarios_favoritos` (`idioma`),
  CONSTRAINT `FK_idiomas_favoritos` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_productos_favoritos` FOREIGN KEY (`producto`) REFERENCES `productos` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_usuarios_favoritos` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `idiomas` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `titulo` mediumtext COLLATE utf8_unicode_ci NOT NULL,
  `default` enum('0','1') COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `habilitado` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `Indice` (`cod`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `imagenes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ruta` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`ruta`,`idioma`) USING BTREE,
  KEY `FK_idiomas_imagenes` (`idioma`) USING BTREE,
  KEY `cod` (`cod`,`idioma`),
  CONSTRAINT `FK_idiomas_imagenes` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `landing` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titulo` longtext COLLATE utf8_unicode_ci,
  `desarrollo` longtext COLLATE utf8_unicode_ci,
  `categoria` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `keywords` longtext COLLATE utf8_unicode_ci,
  `description` longtext COLLATE utf8_unicode_ci,
  `fecha` date DEFAULT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `categoria` (`categoria`),
  KEY `fk_landing_idioma` (`idioma`),
  CONSTRAINT `fk_landing_idioma` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `landing_subs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `landing_cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `apellido` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `telefono` int(11) DEFAULT NULL,
  `celular` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `dni` int(11) DEFAULT NULL,
  `cuit` int(11) DEFAULT NULL,
  `provincia` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `localidad` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pais` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `direccion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `empresa` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cargo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `razon_social` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `mensaje` text COLLATE utf8_unicode_ci,
  `fecha` datetime DEFAULT NULL,
  `ganador` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `padre` int(11) DEFAULT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `icono` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `link` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `target` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `area` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `orden` int(11) DEFAULT '0',
  `opciones` tinyint(4) NOT NULL DEFAULT '0',
  `habilitado` tinyint(4) NOT NULL DEFAULT '1',
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'es',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`link`,`idioma`) USING BTREE,
  KEY `FK_idiomas_menu` (`idioma`) USING BTREE,
  KEY `area` (`area`),
  CONSTRAINT `FK_idiomas_menu` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=202 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `mercadolibre` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `price` float NOT NULL,
  `stock` int(11) NOT NULL DEFAULT '0',
  `product` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_productos_mercadolibre` (`product`) USING BTREE,
  CONSTRAINT `FK_productos_mercadolibre` FOREIGN KEY (`product`) REFERENCES `productos` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `opciones` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `opciones` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `multiple` tinyint(1) NOT NULL DEFAULT '0',
  `filtro` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `area` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `categoria` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `idioma` varchar(2) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cod` (`cod`),
  UNIQUE KEY `cod_3` (`cod`,`idioma`),
  KEY `idioma` (`idioma`),
  KEY `fgk_categoria_opciones` (`categoria`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `opciones_valor` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `relacion_cod` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `opcion_cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `valor` text COLLATE utf8_unicode_ci NOT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `producto_cod` (`relacion_cod`),
  KEY `opcion_cod` (`opcion_cod`),
  KEY `fgk_idioma_valor` (`idioma`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `pagos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titulo` longtext COLLATE utf8_unicode_ci NOT NULL,
  `leyenda` longtext COLLATE utf8_unicode_ci,
  `estado` int(11) NOT NULL,
  `monto` int(11) DEFAULT '0',
  `defecto` int(11) NOT NULL,
  `estado_pendiente` int(11) NOT NULL,
  `estado_aprobado` int(11) NOT NULL,
  `estado_rechazado` int(11) NOT NULL,
  `tipo` varchar(11) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'ID de _cfg_pagos',
  `minimo` float NOT NULL DEFAULT '0',
  `maximo` float NOT NULL DEFAULT '0',
  `entrega` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'porcentaje a pagar de seña	',
  `cuotas` int(11) DEFAULT NULL,
  `tipo_usuario` int(11) NOT NULL DEFAULT '0',
  `acumular` tinyint(1) NOT NULL DEFAULT '0',
  `desc_usuario` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se esta usando',
  `desc_cupon` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'No se esta usando',
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `FK_idiomas_pagos` (`idioma`) USING BTREE,
  KEY `Indice` (`cod`,`estado`,`tipo`,`defecto`) USING BTREE,
  CONSTRAINT `FK_idiomas_pagos` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `pagos_pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `apellido` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `telefono` varchar(16) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `celular` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `documento` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `postal` varchar(5) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `provincia` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `localidad` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `calle` varchar(150) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `numero` varchar(7) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `piso` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `factura` tinyint(1) DEFAULT '0',
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `soft_delete` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `FK_pagos_pedidos_usuario` (`usuario`),
  CONSTRAINT `FK_pagos_pedidos_usuario` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

CREATE TABLE `pedidos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `estado` int(11) DEFAULT '0',
  `envio` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `envio_titulo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detalle_envio` int(11) DEFAULT NULL,
  `tracking` text COLLATE utf8_unicode_ci,
  `pago` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `pago_titulo` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `detalle_pago` int(11) DEFAULT NULL,
  `leyenda_pago` text COLLATE utf8_unicode_ci,
  `link_pago` text COLLATE utf8_unicode_ci,
  `observacion` text COLLATE utf8_unicode_ci,
  `entrega` float NOT NULL DEFAULT '0',
  `total` float NOT NULL DEFAULT '0',
  `usuario` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_update` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `visto` tinyint(1) DEFAULT '0',
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `FK_usuarios_pedidos` (`cod`,`usuario`) USING BTREE,
  KEY `FK_idiomas_pedidos` (`idioma`) USING BTREE,
  KEY `Unico` (`usuario`,`estado`,`pago`) USING BTREE,
  KEY `FK_envios-pedidos_pedidos` (`detalle_envio`),
  KEY `FK_pagos-pedidos_pedidos` (`detalle_pago`),
  KEY `FK_envios_pedidos` (`envio`),
  CONSTRAINT `FK_envios-pedidos_pedidos` FOREIGN KEY (`detalle_envio`) REFERENCES `envios_pedidos` (`id`),
  CONSTRAINT `FK_envios_pedidos` FOREIGN KEY (`envio`) REFERENCES `envios` (`cod`) ON DELETE SET NULL ON UPDATE SET NULL,
  CONSTRAINT `FK_pagos-pedidos_pedidos` FOREIGN KEY (`detalle_pago`) REFERENCES `pagos_pedidos` (`id`),
  CONSTRAINT `FK_usuarios_pedidos` FOREIGN KEY (`usuario`) REFERENCES `usuarios` (`cod`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `productos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `cod_producto` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `titulo` text COLLATE utf8_unicode_ci,
  `desarrollo` longtext COLLATE utf8_unicode_ci,
  `stock` int(11) DEFAULT '0',
  `precio` float DEFAULT '0',
  `precio_descuento` float DEFAULT '0',
  `precio_mayorista` float DEFAULT '0',
  `categoria` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `subcategoria` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `tercercategoria` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Tercer Categoria',
  `peso` float DEFAULT '0',
  `keywords` text COLLATE utf8_unicode_ci,
  `description` text COLLATE utf8_unicode_ci,
  `destacado` tinyint(1) DEFAULT '0',
  `envio_gratis` tinyint(1) DEFAULT '0',
  `mostrar_web` tinyint(1) DEFAULT '1',
  `fecha` date DEFAULT NULL,
  `meli` tinyint(1) DEFAULT '0',
  `url` text COLLATE utf8_unicode_ci,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `FK_subcategorias_productos` (`subcategoria`,`idioma`),
  KEY `FK_categorias_productos` (`categoria`,`idioma`),
  KEY `FK_idiomas_productos` (`idioma`) USING BTREE,
  KEY `Indice` (`cod_producto`,`destacado`,`mostrar_web`) USING BTREE,
  KEY `FK_tercercategorias_productos` (`tercercategoria`,`idioma`),
  KEY `idioma` (`idioma`),
  CONSTRAINT `FK_categorias_productos` FOREIGN KEY (`categoria`, `idioma`) REFERENCES `categorias` (`cod`, `idioma`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_idiomas_productos` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_subcategorias_productos` FOREIGN KEY (`subcategoria`, `idioma`) REFERENCES `subcategorias` (`cod`, `idioma`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_tercercategorias_productos` FOREIGN KEY (`tercercategoria`, `idioma`) REFERENCES `tercercategorias` (`cod`, `idioma`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `productos_relacionados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `productos_cod` longtext COLLATE utf8_unicode_ci NOT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `FK_idiomas_productos-relacionados` (`idioma`) USING BTREE,
  CONSTRAINT `FK_idiomas_productos-relacionados` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `productos_visitados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `usuario_ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `fecha` datetime NOT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `producto` (`producto`),
  KEY `usuario_ip` (`usuario_ip`),
  KEY `idioma` (`idioma`),
  CONSTRAINT `cod_producto_visitados` FOREIGN KEY (`producto`) REFERENCES `productos` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idioma` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `usuario_producto_visitado` FOREIGN KEY (`usuario_ip`) REFERENCES `usuarios_ip` (`ip`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `promos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `producto` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `lleva` int(11) DEFAULT NULL,
  `paga` int(11) DEFAULT NULL,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `producto` (`producto`,`idioma`),
  KEY `FK_idiomas_promos` (`idioma`),
  KEY `Indice` (`lleva`,`paga`) USING BTREE,
  CONSTRAINT `FK_idiomas_promos` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_productos_promos` FOREIGN KEY (`producto`, `idioma`) REFERENCES `productos` (`cod`, `idioma`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `nombre` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `crear` tinyint(4) NOT NULL DEFAULT '0',
  `editar` tinyint(4) NOT NULL DEFAULT '0',
  `eliminar` tinyint(4) NOT NULL DEFAULT '0',
  `permisos` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `cod_2` (`cod`,`permisos`),
  KEY `FK_menu_roles` (`permisos`)
) ENGINE=InnoDB AUTO_INCREMENT=1012 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `roles_admin` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rol` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `admin` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `rol` (`rol`,`admin`),
  KEY `admin_fk` (`admin`),
  KEY `rol_fk` (`rol`),
  CONSTRAINT `FK_admin_roles_admin` FOREIGN KEY (`admin`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=123 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `seo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `url` longtext COLLATE utf8_unicode_ci NOT NULL,
  `title` longtext COLLATE utf8_unicode_ci,
  `description` longtext COLLATE utf8_unicode_ci,
  `keywords` longtext COLLATE utf8_unicode_ci,
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `FK_idiomas_seo` (`idioma`) USING BTREE,
  CONSTRAINT `FK_idiomas_seo` FOREIGN KEY (`idioma`) REFERENCES `idiomas` (`cod`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `subcategorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `categoria` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  `free_shipping` int(11) DEFAULT '0',
  `idioma` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `FK_categorias_subcategorias` (`categoria`,`idioma`),
  KEY `FK_idiomas_subcategorias` (`idioma`) USING BTREE,
  KEY `Indice` (`cod`,`categoria`,`idioma`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `tercercategorias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `titulo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `subcategoria` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8_unicode_ci,
  `free_shipping` int(11) DEFAULT '0',
  `idioma` varchar(2) COLLATE utf8_unicode_ci NOT NULL,
  `orden` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `Unico` (`cod`,`idioma`) USING BTREE,
  KEY `FK_subcategorias_tercercategorias` (`subcategoria`,`idioma`),
  KEY `FK_idiomas_tercercategoria` (`idioma`) USING BTREE,
  KEY `Indice` (`cod`,`subcategoria`,`idioma`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `token_ml` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `access_token` longtext COLLATE utf8_unicode_ci,
  `refresh_token` longtext COLLATE utf8_unicode_ci,
  `expire_in` longtext COLLATE utf8_unicode_ci,
  `secret_request_id` longtext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cod` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `nombre` longtext COLLATE utf8_unicode_ci,
  `apellido` longtext COLLATE utf8_unicode_ci,
  `doc` longtext COLLATE utf8_unicode_ci,
  `email` longtext COLLATE utf8_unicode_ci,
  `password` longtext COLLATE utf8_unicode_ci,
  `calle` mediumtext COLLATE utf8_unicode_ci,
  `numero` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `piso` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `postal` longtext COLLATE utf8_unicode_ci,
  `localidad` longtext COLLATE utf8_unicode_ci,
  `provincia` longtext COLLATE utf8_unicode_ci,
  `pais` longtext COLLATE utf8_unicode_ci,
  `telefono` longtext COLLATE utf8_unicode_ci,
  `celular` longtext COLLATE utf8_unicode_ci,
  `minorista` int(11) DEFAULT '1',
  `invitado` int(11) DEFAULT '1',
  `descuento` float DEFAULT '0',
  `fecha` date DEFAULT NULL,
  `estado` int(11) DEFAULT '1',
  `admin` tinyint(4) NOT NULL DEFAULT '0',
  `idioma` varchar(2) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `Indice` (`cod`,`estado`,`minorista`) USING BTREE,
  KEY `FK_idiomas_usuarios` (`idioma`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `usuarios_ip` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `usuario` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `dispositivo` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `frecha_creacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `ultima_actualizacion` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`),
  KEY `ip_2` (`ip`),
  KEY `cod_usuario_ip` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



SET FOREIGN_KEY_CHECKS = 1;