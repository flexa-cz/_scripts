<?php
/**
 * Description of geo_location_by_ip
 *
 * @author milan
 */
class GeolocationByIp
{
	private $ip;
	private $country_code;
	private $region;
	private $city;

	private $id_country;
	private $id_region;
	private $id_city;

	/**
	 * muj osobni api klic
	 * kdyby toto nekdo chtel pouzit, at si prosim necha vygenerovat vlastni
	 * http://ipinfodb.com/
	 * @var string
	 */
	private $ip_info_db_api_key='a85781576da06e8c5fb97204b4e76156ae101f26ae8617c4016e4b03444c4e3b';
	private $ip_info_db_api_file;
	private $ip_info_db_detail;
	private $ip_info_db_object;

	private $db;
	private $errors=array();
	
	/**
	 * kolik dni se maji vysledky cachovat
	 * @var integer
	 */
	private $days_of_cache=7;

	/* ************************************************************************ */
	/* magic methods																														*/
	/* ************************************************************************ */

	/**
	 * @param string $ip
	 * @param resource $db pripojeni k db
	 */
	public function __construct($ip=false,$db=false)
	{
		$this->ip_info_db_api_file=_ROOT.'geo_location_by_ip/ip2locationlite.class.php';
		$this->db=$db;
		if($ip){
			$this->setIp($ip);
		}
	}

	/* ************************************************************************ */
	/* public methods																														*/
	/* ************************************************************************ */

	/**
	 * nastavi ip z parametru
	 *
	 * @param string $ip
	 * @return \GeolocationByIp
	 *
	 * @since 11.4.12 9:06
	 * @author Vlahovic
	 */
	public final function setIp($ip){
		$this->country_code=null;
		$this->region=null;
		$this->city=null;
		if(!preg_match('/^(\d{1,3}\.){3}\d{1,3}$/',$ip)){
			$ip=false;
		}
		$this->ip=$ip;
		return $this;
	}

	/**
	 * zjisti mesto a region podle ip
	 *
	 * @return \GeolocationByIp
	 *
	 * @since 11.4.12 9:05
	 * @author Vlahovic
	 */
	public final function checkIp(){
		$this->checkIpDb()->checkIpOnline();
		return $this;
	}

	/**
	 * @return string
	 *
	 * @since 11.4.12 15:07
	 * @return Vlahovic
	 */
	public final function getCountryCode(){
		return $this->country_code;
	}

	/**
	 * @return string
	 *
	 * @since 11.4.12 15:07
	 * @return Vlahovic
	 */
	public final function getRegion(){
		return $this->region;
	}

	/**
	 * @return string
	 *
	 * @since 11.4.12 15:07
	 * @return Vlahovic
	 */
	public final function getCity(){
		return $this->city;
	}

	/**
	 * vrati id nejvice aktivniho regionu ze zadane zeme
	 *
	 * @param string $country_code [optional] default 'CZ'
	 * @return integer
	 *
	 * @since 12.4.12 10:45
	 * @author Vlahovic
	 */
	public final function mostActiveRegionId($country_code='CZ'){
		$id=false;
		if($country_code){
			$this->mostActiveRegion($country_code);
			if(isset($this->most_active_region[$country_code]['id'])){
				$id=$this->most_active_region[$country_code]['id'];
			}
		}
		return $id;
	}

	/**
	 * vrati nazev nejvice aktivniho regionu ze zadane zeme
	 *
	 * @param string $country_code [optional] default 'CZ'
	 * @return string
	 *
	 * @since 12.4.12 10:45
	 * @author Vlahovic
	 */
	public final function mostActiveRegionName($country_code='CZ'){
		$name=false;
		if($country_code){
			$this->mostActiveRegion($country_code);
			if(isset($this->most_active_region[$country_code]['name'])){
				$name=$this->most_active_region[$country_code]['name'];
			}
		}
		return $name;
	}

	/* ************************************************************************ */
	/* private methods																													*/
	/* ************************************************************************ */

	/**
	 * zjisti nejaktivnejsi region zadane zeme
	 *
	 * @param string $country_code
	 * @return \GeolocationByIp
	 *
	 * @since 12.4.12 10:47
	 * @author Vlahovic
	 */
	private function mostActiveRegion($country_code){
		if($country_code && !isset($this->most_active_region[$country_code])){
			$query="
				SELECT
					DISTINCT `ip1`.`id_region` AS `id`,
					r1.region AS `name`,
					(SELECT COUNT(*) FROM geolocation_by_ip AS `ip2` WHERE ip2.id_region=ip1.id_region) AS `count`
				FROM geolocation_by_ip AS `ip1`

				LEFT JOIN geolocation_countries AS c1 ON c1.id=ip1.id_country
				LEFT JOIN geolocation_regions AS r1 ON r1.id=ip1.id_region

				WHERE c1.country_code='".$country_code."'
				ORDER BY `count` DESC
				LIMIT 1
				";
			$res=$this->query($query);
			if($res && mysql_num_rows($res)){
				$row=mysql_fetch_assoc($res);
				$this->most_active_region[$country_code]['id']=$row['id'];
				$this->most_active_region[$country_code]['name']=$row['name'];
			}
		}
		return $this;
	}



	/**
	 * zjisti jestli neni uz ulozeno v db
	 * bere jen zaznamy mladsi nez tyden
	 *
	 * @return \GeolocationByIp
	 *
	 * @since 11.4.12 9:04
	 * @author Vlahovic
	 */
	private function checkIpDb(){
		if($this->ip){
			$query="
				SELECT
					`country`.`country_code`,
					`city`.`city`,
					`region`.`region`
				FROM `geolocation_by_ip` AS `ip`
				LEFT JOIN geolocation_countries AS `country` ON `country`.`id`=`ip`.`id_country`
				LEFT JOIN geolocation_regions AS `region` ON `region`.`id`=`ip`.`id_region`
				LEFT JOIN geolocation_cities AS `city` ON `city`.`id`=`ip`.`id_city`
				WHERE
					`ip`.`ip`='".$this->ip."' AND
					`ip`.`created` > DATE_SUB(NOW(), INTERVAL ".$this->days_of_cache." DAY)
				LIMIT 1";
			$res=$this->query($query);
			if($res && mysql_num_rows($res)){
				$row=mysql_fetch_assoc($res);
				$this->country_code=$row['country_code'];
				$this->region=$row['region'];
				$this->city=$row['city'];
			}
			$this->errors['select_from_db']=mysql_error();
		}
		return $this;
	}

	/**
	 * zjisti z webu
	 *
	 * @return \GeolocationByIp
	 *
	 * @since 11.4.12 9:08
	 * @author Vlahovic
	 */
	private function checkIpOnline(){
		if(!$this->country_code && !$this->region && !$this->city && $this->ip){
			// ipinfodb
			if(!$this->ip_info_db_object){
				include($this->ip_info_db_api_file);
				// Load the class
				$this->ip_info_db_object = new ip2location_lite;
				$this->ip_info_db_object->setKey($this->ip_info_db_api_key);
			}


			// Get locations
			$locations = $this->ip_info_db_object->getCity($this->ip);

			if($locations['statusCode']=='OK'){
				$this->country_code=$locations['countryCode'];
				$this->city=$locations['cityName'];
				$this->region=$locations['regionName'];
				$this->ip_info_db_detail=$locations;
				// ulozi do db
				$this->saveIpInfo();
			}
		}
	}


	/**
	 * zjisti a vrati id posledniho zaznamu vlozeneho do databaze
	 *
	 * @return integer
	 *
	 * @since 12.4.12 9:03
	 * @author Vlahovic
	 */
	private function lastInsertId(){
		$last_insert_id=false;
		$query="SELECT LAST_INSERT_ID()";
		$res=$this->query($query);
		if($res && mysql_num_rows($res)){
			$last_insert_id=mysql_result($res, 0);
		}
		return $last_insert_id;
	}

	/**
	 * ulozi zemi, pokud jeste neni v databazi
	 * jeji id doplni do vlastnosti
	 *
	 * @return \GeolocationByIp
	 *
	 * @since 12.4.12 9:07
	 * @author Vlahovic
	 */
	private function saveCountry(){
		$id_country=null;
		// jen pokud je zname
		if($this->ip_info_db_detail['countryCode']){
			// zjisti jestli uz neni v db
			$query="SELECT id FROM geolocation_countries WHERE country_code='".$this->ip_info_db_detail['countryCode']."' LIMIT 1";
			$res=$this->query($query);
			if($res && mysql_num_rows($res)){
				$id_country=mysql_result($res, 0);
			}
			// pokud neni tak ulozi
			if(!$id_country && $this->ip_info_db_detail['countryCode'] && $this->ip_info_db_detail['countryCode']!='-'){
				$query="INSERT INTO geolocation_countries SET
					`country`='".$this->ip_info_db_detail['countryName']."',
					`country_code`='".$this->ip_info_db_detail['countryCode']."'
						";
				$res=$this->query($query);
				if($res){
					$id_country=$this->lastInsertId();
				}
			}
		}
		$this->id_country=$id_country;
		return $this;
	}

	/**
	 * ulozi kraj, pokud jeste neni v databazi
	 * jeho id doplni do vlastnosti
	 *
	 * @return \GeolocationByIp
	 *
	 * @since 12.4.12 9:07
	 * @author Vlahovic
	 */
	private function saveRegion(){
		$id_region=null;
		// jen pokud je znamy
		if($this->ip_info_db_detail['regionName']){
			// zjisti jestli uz neni v db
			$query="SELECT id FROM geolocation_regions WHERE region='".$this->ip_info_db_detail['regionName']."' LIMIT 1";
			$res=$this->query($query);
			if($res && mysql_num_rows($res)){
				$id_region=mysql_result($res, 0);
			}
			// pokud neni tak ulozi
			if(!$id_region && $this->id_country && $this->ip_info_db_detail['regionName'] && $this->ip_info_db_detail['regionName']!='-'){
				$query="INSERT INTO geolocation_regions SET
					`id_country`=".$this->id_country.",
					`region`='".$this->ip_info_db_detail['regionName']."'
						";
				$res=$this->query($query);
				if($res){
					$id_region=$this->lastInsertId();
				}
			}
		}
		$this->id_region=$id_region;
		return $this;
	}

	/**
	 * ulozi mesto, pokud jeste neni v databazi
	 * jeho id doplni do vlastnosti
	 *
	 * @return \GeolocationByIp
	 *
	 * @since 12.4.12 9:07
	 * @author Vlahovic
	 */
	private function saveCity(){
		$id_city=null;
		// jen pokud je znamy
		if($this->ip_info_db_detail['cityName']){
			// zjisti jestli uz neni v db
			$query="SELECT id FROM geolocation_cities WHERE city='".$this->ip_info_db_detail['cityName']."' LIMIT 1";
			$res=$this->query($query);
			if($res && mysql_num_rows($res)){
				$id_city=mysql_result($res, 0);
			}
			// pokud neni tak ulozi
			if(!$id_city && $this->id_country && $this->id_region && $this->ip_info_db_detail['cityName'] && $this->ip_info_db_detail['cityName']!='-'){
				$query="INSERT INTO geolocation_cities SET
					`id_country`=".$this->id_country.",
					`id_region`=".$this->id_region.",
					`city`='".$this->ip_info_db_detail['cityName']."',
					`zip_code`='".($this->ip_info_db_detail['zipCode'] && $this->ip_info_db_detail['zipCode']!='-' ? $this->ip_info_db_detail['zipCode'] : false)."',
					`latitude`='".($this->ip_info_db_detail['latitude'] && $this->ip_info_db_detail['latitude']!='-' ? $this->ip_info_db_detail['latitude'] : false)."',
					`longitude`='".($this->ip_info_db_detail['longitude'] && $this->ip_info_db_detail['longitude']!='-' ? $this->ip_info_db_detail['longitude'] : false)."',
					`timezone`='".($this->ip_info_db_detail['timeZone'] && $this->ip_info_db_detail['timeZone']!='-' ? $this->ip_info_db_detail['timeZone'] : false)."'
						";
				$res=$this->query($query);
				if($res){
					$id_city=$this->lastInsertId();
				}
			}
		}
		$this->id_city=$id_city;
		return $this;
	}

	private function saveIpInfo(){
		$this->saveCountry()->saveRegion()->saveCity();
		$query="
			INSERT INTO geolocation_by_ip SET
			`ip`='".$this->ip_info_db_detail['ipAddress']."',
			`id_country`=".($this->id_country ? $this->id_country : 'NULL').",
			`id_region`=".($this->id_region ? $this->id_region : 'NULL').",
			`id_city`=".($this->id_city ? $this->id_city : 'NULL').",
			`created`=NOW()
			ON DUPLICATE KEY UPDATE
			`id_country`=".($this->id_country ? $this->id_country : 'NULL').",
			`id_region`=".($this->id_region ? $this->id_region : 'NULL').",
			`id_city`=".($this->id_city ? $this->id_city : 'NULL').",
			`created`=NOW()
			";
		$this->errors['insert_to_db']=mysql_error();
		$this->query($query);
	}




	/**
	 * jen polozi db dotaz, ale rozhodne jesli do nej nacpat db resource, nebo ne
	 *
	 * @param string $query
	 * @return resource
	 */
	private function query($query){
		if($this->db){
			$res=mysql_query($query,$this->db);
		}
		else{
			$res=mysql_query($query);
		}
		return $res;
	}
}
/*

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

 */
?>
