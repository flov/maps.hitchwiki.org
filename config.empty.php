<?php
/*
 * Hitchwiki Maps: config.empty.php
 *
 * Copy this file to "config.php"
 */

/* 
 * SETTINGS you might want to adjust:
 */
 
// Tools for devs:
$settings["cache_buster"] = 			''; // Force user's browser to refresh JS and CSS files by changing this to something new
$settings["debug"] = 					false;
$settings["allow_admins"] = 			false; // Set false to temporarily take off all admin priviliges
$settings["maintenance_page"] = 		false; // Set true to close down visible page
$settings["maintenance_api"] = 			false; // Set true to close down API
$settings["non_maintenance_ip"] = 		array(); // Add IP addresses to whom show a normal page while in maintenance mode.

// API-keys:
$settings["google_maps_api_key"] = 		""; // API key to enable
$settings["yahoo_maps_appid"] = 		""; // APP ID to enable
$settings["ms_virtualearth"] = 			false; // false|true to enable

// OAuth 2 Credentials for Google Latitude API
$settings["google_latitude_client_id"] = 		"";
$settings["google_latitude_client_secret"] = 	"";
$settings["google_latitude_access_key"] = 		"";

// Analytic tools
$settings["google_analytics_id"] =		""; // ID to enable
$settings["piwik_id"] = 				""; // ID to enable

// fb:admins or fb:app_id - A comma-separated list of either the Facebook IDs of page administrators or a Facebook Platform application ID. At a minimum, include only your own Facebook ID.
$settings["fb"]["admins"] = 			"";
$settings["fb"]["page_id"] = 			"";

$settings["fb"]["app"]["id"] = 			"";
$settings["fb"]["app"]["api"] = 		"";
$settings["fb"]["app"]["secret"] = 		"";

$settings["email"] = 					"maps@hitchwiki.org";
$settings["cookie_prefix"] = 			"hitchwiki_maps_";
$settings["hitchability_colors"] = 		array('ffffff','00ad00','96ad00','ffff00','ff8d00','ff0000'); // Rating = key (0-5)

// Languages
// See ./admin/ to set up new languages
$settings["default_language"] = 		"en_UK"; // Fall back and default language
$settings["valid_languages"] = 			array( // Remember to add language cells to your database too!
		"en_UK" => "In English", 
		"de_DE" => "Auf Deutsch", 
		"es_ES" => "En Español", 
		"ru_RU" => "По-Pусский",
		"fi_FI" => "Suomeksi", 
		"lt_LT" => "Lietuvių"
);

$settings["languages_in_english"] = 	array(
		"en_UK" => "English", 
		"de_DE" => "German", 
		"es_ES" => "Spanish", 
		"ru_RU" => "Russian",
		"fi_FI" => "Finnish", 
		"lt_LT" => "Lithuanian"
); 


// Usually you don't need to edit this, but you can set it manually, too. No ending "/".
$settings["base_url"] = "http://hitchwiki.org/maps";
$settings["base_url_demo"] 	= "http://hitchwiki.org/devmaps";
#TODO, automate this. "http" . ((!empty($_SERVER['HTTPS'])) ? "s" : "") . "://".$_SERVER['SERVER_NAME'].dirname($_SERVER['REQUEST_URI']);

/*
 * MySQL settings
 */
$mysql_conf = array(
	"user"		=> 		'', 
	"password"	=> 		'',
	"host"		=> 		'',
	"database"	=> 		'',
	"mediawiki_db" => 	'' // the DB where Maps will look for the users info, eg. email
);



/**** DO NOT EDIT FROM HERE ****/

session_name('pg_hitchwiki_en_session');
session_start();

/*
 * Select language (sets $settings["language"])
 * Load common functions
 * Load Maps API
 * Load Markdown
 */
require_once "lib/language.php";
require_once "lib/functions.php";
require_once("lib/api.php");
require_once "lib/markdown.php";


/*
 * Map layer translated names
 * You cannot add/remove used layers from here, but you need also edit static/js/main.js
 */
$map_layers = array(
    "osm" => array(
    		"mapnik" => "Open Street map",
    		"osmarender" => "Open Street map - Tiles@Home"
    ),
    "google" => array(
    		"gsat" => "Google "._("Satellite"),
    		"ghyb" => "Google "._("Hybrid"),
    		"gmap" => "Google "._("Streets"),
    		"gphy" => "Google "._("Physical")
    ),
    "yahoo" => array(
    		"yahoohyb" => "Yahoo "._("Hybrid"),
    		"yahoosat" => "Yahoo "._("Satellite"),
    		"yahoo" => "Yahoo "._("Street")
    ),
    "vearth" => array(
    		"vehyb" => "Virtual Earth "._("Hybrid"),
    		"veaer" => "Virtual Earth "._("Aerial"),
    		"veroad" => "Virtual Earth "._("Roads")
    )
);

?>