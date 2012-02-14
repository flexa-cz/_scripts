CREATE DATABASE `sazka` COLLATE 'utf8_czech_ci';

CREATE TABLE `addresses` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `address` varchar (250) COLLATE 'utf8_czech_ci' NOT NULL COMMENT 'adresa strany s vysledky daneho dne',
  `score_date` date NOT NULL COMMENT 'datum z jakeho jsou vysledky',
  `created_datetime` datetime NOT NULL COMMENT 'kdy byl zaznam vytvoren'
) COMMENT='adresy s vysledky jednotlivych dnu' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

ALTER TABLE `addresses`
ADD UNIQUE `score_date` (`score_date`);

CREATE TABLE `scores` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `score` tinyint unsigned NOT NULL COMMENT 'tazne cislo',
  `id_address` int(10) unsigned NOT NULL COMMENT 'den kdy se losovalo',
  FOREIGN KEY (`id_address`) REFERENCES `addresses` (`id`) ON DELETE CASCADE
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

ALTER TABLE `scores`
ADD INDEX `score` (`score`);

ALTER TABLE `addresses`
ADD `score_saved` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'jestli uz byla cisla z tohoto dne ulozena do db' AFTER `created_datetime`,
COMMENT='adresy s vysledky jednotlivych dnu'
REMOVE PARTITIONING;

ALTER TABLE `scores`
ADD `type` enum('cislo','dodatkove') COLLATE 'utf8_czech_ci' NOT NULL DEFAULT 'cislo' COMMENT 'typ cisla' AFTER `id_address`,
COMMENT=''
REMOVE PARTITIONING;

ALTER TABLE `scores`
ADD `tah` enum('první','druhý','šance') COLLATE 'utf8_czech_ci' NOT NULL AFTER `id_address`,
COMMENT=''
REMOVE PARTITIONING;

CREATE TABLE `scores_resume` (
  `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_address` int(10) unsigned NOT NULL,
  `tah` enum('první','druhý','šance') COLLATE 'utf8_czech_ci' NOT NULL,
  `numbers` varchar(200) COLLATE 'utf8_czech_ci' NOT NULL,
  FOREIGN KEY (`id_address`) REFERENCES `addresses` (`id`) ON DELETE CASCADE
) COMMENT='' ENGINE='InnoDB' COLLATE 'utf8_czech_ci';

ALTER TABLE `addresses`
ADD `year` year NOT NULL AFTER `score_saved`,
ADD `week` smallint unsigned NOT NULL AFTER `year`,
COMMENT='adresy s vysledky jednotlivych dnu'
REMOVE PARTITIONING;

update addresses set
`year`=date_format(`score_date`,'%Y'),
`week`=date_format(`score_date`,'%v');

ALTER TABLE `addresses`
ADD `first_second` enum('first','second') COLLATE 'utf8_czech_ci' NOT NULL COMMENT 'zda je v danem tydnu o pvni, nebo druhy tah' AFTER `week`,
COMMENT='adresy s vysledky jednotlivych dnu'
REMOVE PARTITIONING;