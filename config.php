<?php
/*
 * Hitchwiki Maps: config.php
 *
 */



/* 
 * SETTINGS you might want to adjust:
 */
$settings["debug"] = 					true;
$settings["google_maps_api_key"] = 		"";
$settings["yahoo_maps_appid"] = 		"";
$settings["email"] = 					"help@liftershalte.info";
$settings["default_language"] = 		"en_UK";
$settings["cookie_prefix"] = 			"hitchwiki_maps_";
$settings["valid_languages"] = 			array(
											"en_UK" => "In English", 
											"de_DE" => "Auf Deutsch", 
											"fi_FI" => "Suomeksi", 
											"es_ES" => "En Español", 
											"ru_RU" => "По-Pусский"
										);


/**** DO NOT EDIT FROM HERE ****/

/*
 * Select language
 * Sets $settings["language"]
 */
require_once "lib/language.php";

/*
 * MySQL settings
 * Comes with:
 
	$mysql_conf = array(
		"user"		=> 		'', 
		"password"	=> 		'',
		"host"		=> 		'',
		"database"	=> 		''
	);

 */
require_once "hitchwiki_maps_mysql_settings.php";

?>