CREATE TABLE `geolocation_countries` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `country` varchar(50) COLLATE 'utf8_czech_ci' NOT NULL,
  `country_code` varchar(10) COLLATE 'utf8_czech_ci' NOT NULL
) COMMENT='staty a jejich zkratky' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

CREATE TABLE `geolocation_regions` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_country` int unsigned NOT NULL,
  `region` varchar(50) COLLATE 'utf8_czech_ci' NOT NULL
) COMMENT='regiony a jejich prirazeni ke statu' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

CREATE TABLE `geolocation_cities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_country` int unsigned NOT NULL,
  `id_region` int unsigned NOT NULL,
  `city` varchar(50) COLLATE 'utf8_czech_ci' NOT NULL,
  `zip_code` varchar(50) COLLATE 'utf8_czech_ci' NOT NULL,
  `latitude` float unsigned NOT NULL,
  `longitude` float unsigned NOT NULL,
  `timezone` varchar(8) COLLATE 'utf8_czech_ci' NOT NULL
) COMMENT='mesta a jejich prirazeni ke statu a kraji' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

CREATE TABLE `geolocation_by_ip` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_country` int(10) unsigned NULL,
  `id_region` int(10) unsigned NULL,
  `id_city` int(10) unsigned NULL,
  `ip` varchar(15) COLLATE utf8_czech_ci NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ip` (`ip`)
) ENGINE='InnoDB' DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci COMMENT='ip prirazena ke statum, regionum a mestum';

ALTER TABLE `geolocation_regions`
	ADD CONSTRAINT `fk_gl_r_id_country_gl_countries_id`
	FOREIGN KEY (`id_country`)
	REFERENCES `geolocation_countries`(`id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE `geolocation_cities`
	ADD CONSTRAINT `fk_gl_cities_id_country_gl_countries_id`
	FOREIGN KEY (`id_country`)
	REFERENCES `geolocation_countries`(`id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE `geolocation_cities`
	ADD CONSTRAINT `fk_gl_cities_id_region_gl_regions_id`
	FOREIGN KEY (`id_region`)
	REFERENCES `geolocation_regions`(`id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE `geolocation_by_ip`
	ADD CONSTRAINT `fk_gl_ip_id_country_gl_countries_id`
	FOREIGN KEY (`id_country`)
	REFERENCES `geolocation_countries`(`id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE `geolocation_by_ip`
	ADD CONSTRAINT `fk_gl_ip_id_region_gl_regions_id`
	FOREIGN KEY (`id_region`)
	REFERENCES `geolocation_regions`(`id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;

ALTER TABLE `geolocation_by_ip`
	ADD CONSTRAINT `fk_gl_ip_id_city_gl_cities_id`
	FOREIGN KEY (`id_city`)
	REFERENCES `geolocation_cities`(`id`)
	ON DELETE CASCADE
	ON UPDATE CASCADE;
