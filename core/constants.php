<?php
/**
 * spolecna nastaveni pro vsechny testovaci skripty
 *
 * @since 28.11.11 9:57
 * @author Vlahovic
 */


// pokud neni definovana uz ze souboru include.php
if(!defined('_ROOT')){
	$root=str_replace(array('\\','core/constants.php'),array('/',false),__FILE__);
	/**
	*  absolutni cesta do rootu domeny
	 * vcetne lomitka na konci
	*/
	define('_ROOT',$root);
}

define('_N',"\r\n");
define('_T',"\t");
define('_N_T',"\r\n\t");
define('_N_T_T',"\r\n\t\t");
define('_N_T_T_T',"\r\n\t\t\t");
define('_N_T_T_T_T',"\r\n\t\t\t\t");