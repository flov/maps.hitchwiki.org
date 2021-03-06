<?php 
/*
 * Hitchwiki Maps: index.php
 * 2010
 *
 */
 
 
/*
 * Initialize Maps
 */
if(@is_file('config.php')) require_once "config.php";
else { $settings["non_maintenance_ip"] = array(); $settings["maintenance_page"] = true; }

/*
 * Put up a maintenance -sign
 * Set it up from config.php or test it from ./?maintenance
 */
if(isset($_GET["maintenance"])) { $settings["non_maintenance_ip"] = array(); $settings["maintenance_page"] = true; }
if($settings["maintenance_page"]===true && !in_array($_SERVER['REMOTE_ADDR'], $settings["non_maintenance_ip"])) { @include("maintenance_page.php"); exit; }



/*
 * Redirect to clean "rdfrom" away
 */
if(isset($_GET["rdfrom"])) {
	header("Location: ".$settings["base_url"]);
	exit;
}



/*
 * Returns an info-array about logged in user (or false if not logged in) 
 * With this we also check if user is logged in by every load
 * You should include this line to every .php where you need to know if user is logged in
 */
$user = current_user();



/*
 * Map settings
 */
// Zoom, lat, lon, layers
$zoom = (isset($_GET["zoom"]) && ctype_digit($_GET["zoom"])) ? $_GET["zoom"] : '4';

// Centered to Germany (51,9). Projection center would be '49','8.3'

$lat = (isset($_GET["lat"]) && is_numeric($_GET["lat"])) ? $_GET["lat"] : '51';
$lon = (isset($_GET["lon"]) && is_numeric($_GET["lon"])) ? $_GET["lon"] : '9';
#$layers = (isset($_GET["layers"]) && !empty($_GET["layers"])) ? strip_tags($_GET["layers"]) : 'B';
$layers = 'B';

if(!isset($_GET["lat"]) && !isset($_GET["lon"]) && !empty($user["country"])) {
	$countryinfo = country_info($user["country"]);
	if($countryinfo!==false) {
		$lat = $countryinfo["lat"];
		$lon = $countryinfo["lon"];
		if(!isset($_GET["zoom"])) $zoom = '5';
	}
}

// Markers visible -level
// Limit loading new markers only to this zoom level and deeper (bigger numbers = more zoom)
// Also hides markers-layer before this zoom level and show country places count -labels instead
$default_markersZoomLimit = '7';
$markersZoomLimit = (isset($_COOKIE[$settings["cookie_prefix"]."markersZoomLimit"]) && ctype_digit($_COOKIE[$settings["cookie_prefix"]."markersZoomLimit"])) ? $_COOKIE[$settings["cookie_prefix"]."markersZoomLimit"] : $default_markersZoomLimit;


if(isset($_GET["place"]) && $_GET["place"] != "" && preg_match ("/^([0-9]+)$/", $_GET["place"])) {
	$place = get_place($_GET["place"], true);
	if($place["error"]!==true) {
		$show_place = htmlspecialchars($_GET["place"]);
	}
	else {
		$show_place_error = true;
		unset($place);
	}
/* 
#Maybe here is a key to select one vector from a stack (JS)? (TODO)

sm = new GeoExt.grid.FeatureSelectionModel({layers: layers});
            t.ok(OpenLayers.Util.indexOf(layer.selectedFeatures,
272	                                         features[0]) < 0,
273	                 "click on row 0 does not select feature 0");
274	            
275	            // select feature 1
276	            // test that the second row is not selected
277	            sm.selectControl.select(features[1]);
278	            t.ok(!sm.isSelected(1),
279	                 "selecting feature 1 does not select row 1");

*/
}

/*
 *  Build a title, image, slogan and description
 */
 
// Title
// If place
if(isset($show_place) && !isset($show_place_error)) {
    $title .= _("a Hitchhiking spot in").' '; 

    // in city, country
    if(!empty($place["location"]["locality"])) $title .= $place["location"]["locality"].', ';

    $title .= $place["location"]["country"]["name"];
    $title .= ' - ';
}
$title .= 'Hitchwiki '._("Maps");
 
 
// Image
$website_img[] = $settings["base_url"].'/badge.png'; 
if(isset($show_place) && !isset($show_place_error)) $website_img[] = image_map($place["lat"],$place["lon"]);
			
// Slogan
$slogan = _("Find good places for hitchhiking and add your own");

// Description
if(isset($show_place) && !isset($show_place_error) && !empty($place["description"]["en_UK"]["description"])) $description = htmlspecialchars(strip_tags($place["description"]["en_UK"]["description"]));	
else $description = $slogan;


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html 
	xmlns="http://www.w3.org/1999/xhtml" 
	xmlns:og="http://opengraphprotocol.org/schema/" 
	<?php 
	// Load schema only if FB-tags are filled in config
	if(!empty($settings["fb"])): ?>xmlns:fb="http://developers.facebook.com/schema/" <?php endif; ?>
	dir="ltr" 
	lang="<?php echo langcode(); ?>">
	<head profile="http://gmpg.org/xfn/11">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title><?php echo $title; ?></title>
		<link href="static/css/ui-lightness/jquery-ui.css" media="all" rel="stylesheet" type="text/css" />
		<?php
		
		/*
		 * Map Services
		 * You need to enable these from init_map() in static/js/main.js 
		 * Set API keys and such to the config.php
		 */
		 
		 // Google maps
		if(!empty($settings["google_maps_api_key"])) {
			if($user["logged_in"]===true && empty($user["map_google"])) $print_map_google = false;
			else $print_map_google = true;
			
			if($print_map_google) echo '<script src="http://maps.google.com/maps?file=api&l='.shortlang().'&v=2&key='.$settings["google_maps_api_key"].'"></script>'."\n\t\t";
		}

		// Yahoo
		if(!empty($settings["yahoo_maps_appid"])) {
			if($user["logged_in"]===true && empty($user["map_yahoo"])) $print_map_yahoo = false;
			else $print_map_yahoo = true;
			
			if($print_map_yahoo) echo '<script src="http://api.maps.yahoo.com/ajaxymap?v=3.0&appid='.$settings["yahoo_maps_appid"].'" type="text/javascript"></script>'."\n\t\t";
		}
		
		// MS VirtualEarth
		if($settings["ms_virtualearth"]===true) {
			if($user["logged_in"]===true && empty($user["map_vearth"])) $print_map_vearth = false;
			else $print_map_vearth = true;
			
			if($print_map_vearth) echo '<script src="http://dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=6.1&mkt='.str_replace("_", "-", $settings["language"]).'" type="text/javascript"></script>'."\n\t\t";
		}
		
		?><script src="http://openlayers.org/api/OpenLayers.js" type="text/javascript"></script>

		<link href="ajax/js-translation.json.php?c=<?php echo $settings["cache_buster"]; ?>&amp;lang=<?php echo $settings["language"]; ?>" lang="<?php echo $settings["language"]; ?>" rel="gettext"/>
		<script type="text/javascript">
		//<![CDATA[

			/*
			 * Misc settings
			 */
			var ip = "<?php echo htmlspecialchars($_SERVER['REMOTE_ADDR']); ?>";
			var geolocation = "ajax/geolocation_ip_proxy.php";
			var cookie_prefix = "<?php echo $settings["cookie_prefix"]; ?>";
			var geolocation_cookiename = "<?php echo $settings["cookie_prefix"]; ?>_geolocation";
			var geolocation_cookieoptions = { path: '/', expires: 6 }; // expires: hours
			var locale = "<?php echo $settings["language"]; ?>";
			var google_analytics = <?php echo (!empty($settings["google_analytics_id"]) ? 'true' : 'false'); ?>;
			var piwik_analytics = <?php echo (!empty($settings["piwik_id"]) ? 'true' : 'false'); ?>;
			var private_location = <?php echo (!empty($user["private_location"]) ? 'true' : 'false'); ?>;

			/*
			 * Loaded Map layers
			 */
			var layer_google = <?php echo (!empty($settings["google_maps_api_key"]) && $print_map_google===true) ? "true": "false"; ?>;
			var layer_yahoo  = <?php echo (!empty($settings["yahoo_maps_appid"]) && $print_map_yahoo===true) ? "true": "false"; ?>;
			var layer_vearth = <?php echo ($settings["ms_virtualearth"]===true && $print_map_vearth===true) ? "true": "false"; ?>;
			var layer_default = "<?php echo (isset($user["map_default_layer"]) && !empty($user["map_default_layer"])) ? htmlspecialchars($user["map_default_layer"]): 'mapnik'; ?>";

			/*
			 * Default map settings
			 */
			var lat = <?php echo $lat; ?>;
			var lon = <?php echo $lon; ?>;
			var layers = '<?php echo $layers; ?>';
			var zoom = <?php echo $zoom; ?>;
			var markersZoomLimit = <?php echo $markersZoomLimit; ?>; 

		//]]>
		</script>
		
		<?php /*$settings["debug"]==true*/if(!isset($_GET["min.js"])): ?>
		<!-- in production, these are combined (combined.js.php) -->
		<script src="static/js/jquery.min.js?c=<?php echo $settings["cache_buster"]; ?>" type="text/javascript"></script>
		<script src="static/js/jquery-ui.min.js?c=<?php echo $settings["cache_buster"]; ?>" type="text/javascript"></script>
		<script src="static/js/jquery.json-2.2.min.js" type="text/javascript"></script>
		
		<!-- in production, these are minified (min.js) -->
		<script src="static/js/jquery.cookie.js" type="text/javascript"></script>
		<script src="static/js/jquery.gettext.js" type="text/javascript"></script>
		<script src="static/js/jquery.itoggle-min.js" type="text/javascript"></script>
		<script src="static/js/main.js?c=<?php 
			if($settings["debug"]==true) echo date("jnYHis"); 
			else echo $settings["cache_buster"];
		?>" type="text/javascript"></script>
		<?php else: ?>
		<script src="static/js/combined.js.php" type="text/javascript"></script>
		<script src="static/js/min.js" type="text/javascript"></script>
		<?php endif; ?>

		<!-- Keep main stylesheet here after min.js/main.js -->
		<link rel="stylesheet" type="text/css" href="static/css/main.css?c=<?php 
			if($settings["debug"]==true) echo date("jnYHis"); 
			else echo $settings["cache_buster"];
		?>" media="all" />
		
		<script type="text/javascript">
		//<![CDATA[
			<?php
			
        	/*
        	 * Open JS-pages requested by GET 'page'
        	 */
        	 
        	// Allowed page names
			$pages = array(
				"help", 
				"hitchability",
				"statistics", 
				"complete_statistics", 
				"public_transport", 
				"add_public_transport", 
				"why_register", 
				"translate", 
				"countries", 
				"lost_password", 
				"api", 
				"news", 
				"settings", 
				"profile",
				"users",
				"about",
				"beta",
				"trips",
				"trips_import",
				"log_all",
				"log_trips",
				"log_user"
			);
			?>
			$(document).ready(function() {

				<?php // Open page
				if(isset($_GET["page"]) && in_array($_GET["page"], $pages)): ?>

					open_page("<?php echo htmlspecialchars($_GET["page"]); ?>");

				<?php endif; ?>
				
				<?php // Open marker
				if(isset($show_place)): ?>

					showPlacePanel("<?php echo $show_place; ?>", true);

				<?php // Place asked, but didn't exist
				elseif(isset($show_place_error)): ?>

					info_dialog("<?php echo _("Sorry, but the place cannot be found.")."<br /><br />"._("The place you are looking for might have been removed or is temporarily unavailable."); ?>", "<?php echo _("The place cannot be found"); ?>", true);

				<?php endif; ?>
				
				<?php // Perform search
				if(isset($_GET["q"]) && !empty($_GET["q"])): ?>

					search("<?php echo strip_tags($_GET["q"]); ?>");

				<?php endif; ?>
				
				<?php // Show welcome text after a maintenance break
				if(isset($_GET["post_maintenance"])): ?>
				
					info_dialog('<?php echo _('Sorry about that!').'<br /><br />'._('If something on the website seems wrong to you, please use "contact us" link at the bottom-left of the page.'); ?>', '<?php echo _("The maintenance break is now over"); ?>', false);

				<?php endif; ?>
				
			});
		//]]>
		</script>
		<link rel="shortcut icon" href="<?php echo $settings["base_url"]; ?>/favicon.png" type="image/png" />
		<link rel="bookmark icon" href="<?php echo $settings["base_url"]; ?>/favicon.png" type="image/png" />
		<?php foreach($website_img as $img): ?><link rel="image_src" href="<?php echo $img; ?>" /><?php endforeach; ?>
		<link rel="apple-touch-icon" href="<?php echo $settings["base_url"]; ?>/static/gfx/badge-57x57.png" />
		<meta name="description" content="<?php echo $description; ?>" />
		
		<!-- The Open Graph Protocol - http://opengraphprotocol.org/ -->
		<meta property="og:title" content="<?php echo $title; ?>" />
		<meta property="og:site_name" content="Hitchwiki.org" />
		<meta property="og:description" content="<?php echo $description; ?>" />
		<meta property="og:url" content="<?php echo $settings["base_url"]; ?>/"/>
		<meta property="og:type" content="website" />
		<?php foreach($website_img as $img): ?><meta property="og:image" content="<?php echo $img; ?>" /><?php endforeach; ?>
	<?php /*<meta property="og:email" content="<?php echo $settings["email"]; ?>" /> */ ?>
	<?php if(isset($place)): ?>
		<meta property="og:latitude" content="<?php echo $place["lat"]; ?>" />
		<meta property="og:longitude" content="<?php echo $place["lon"]; ?>" />
		<meta property="og:locality" content="<?php echo $place["location"]["locality"]; ?>" />
		<meta property="og:country-name" content="<?php echo $place["location"]["country"]["name"]; ?>" />
		<meta name="geo.position" content="<?php echo $place["lat"].','.$place["lon"]; ?>" />
	<?php endif; ?>

		<?php 
			if(isset($settings["fb"]["admins"]) && !empty($settings["fb"]["admins"])) echo '<meta property="fb:admins" content="'.$settings["fb"]["admins"].'" />'."\n";
			
			if(isset($settings["fb"]["page_id"]) && !empty($settings["fb"]["page_id"])) echo '<meta property="fb:page_id" content="'.$settings["fb"]["page_id"].'" />'."\n";
			
			if(isset($settings["fb"]["app"]["id"]) && !empty($settings["fb"]["app"]["id"])) echo '<meta property="fb:app_id" content="'.$settings["fb"]["app"]["id"].'" />'."\n";
		?>
		<link rel="home" href="<?php echo $settings["base_url"]; ?>/" title="Hitchwiki <?php echo _("Maps"); ?>" />
		<link rel="help" href="<?php echo $settings["base_url"]; ?>/?page=about" title="Hitchwiki <?php echo htmlspecialchars(_("Help & About")); ?>" />
		<link rel="search" type="application/opensearchdescription+xml" href="<?php echo $settings["base_url"]; ?>/opensearch/" title="Hitchwiki <?php echo _("Maps"); ?>" />
		<?php
		/*
		 * Language versions of the frontpage
		 */ 
		foreach($settings["valid_languages"] as $code => $name) {
			// Don't print current in-use-language page
			if($settings["language"] != $code) echo '<link type="text/html" rel="alternate" hreflang="'.shortlang($code).'" href="'.$settings["base_url"].'/?lang='.$code.'" title="'.$name.'" />'."\n";
		}
		?>
		
		<?php if(isset($settings["fb"]["app"]["id"]) && !empty($settings["fb"]["app"]["id"])): ?>
		<div id="fb-root"></div>
		<script>
		/*
		 * Load Facebook JavaScript SDK
		 * http://developers.facebook.com/docs/reference/javascript/
		 */
		  window.fbAsyncInit = function() {
		    FB.init({appId: '<?php echo $settings["fb"]["app"]["id"]; ?>', status: true, cookie: true,
		             xfbml: true});
		  };
		  (function() {
		    var e = document.createElement('script'); e.async = true;
		    e.src = document.location.protocol +
		      '//connect.facebook.net/<?php
		      	
		      	// Localization + a little language fix
		      	if($settings["language"] == "en_UK") echo 'en_US';
		      	else echo $settings["language"]; 
		      	
		      ?>/all.js';
		    document.getElementById('fb-root').appendChild(e);
		  }());
		</script>
		<?php endif; ?>
				
		<!--[if lt IE 7]>
		<style type="text/css"> 
    	    .png,
    	    .icon
    	     { behavior: url(static/js/iepngfix.htc); }
		</style>
		<link rel="shortcut icon" href="<?php echo $settings["base_url"]; ?>/favicon.ico" type="image/x-icon" />
		<link rel="bookmark icon" href="<?php echo $settings["base_url"]; ?>/favicon.ico" type="image/x-icon" />
		<![endif]-->
		
	<?php // Google analytics
	if(isset($settings["google_analytics_id"]) && !empty($settings["google_analytics_id"])): ?>
	
	<script type="text/javascript">
	
	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', '<?php echo $settings["google_analytics_id"]; ?>']);
	  _gaq.push(['_trackPageview']);
	
	  (function() {
	    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();
	
	</script>
	<?php endif; ?>

    </head>
    <body class="<?php echo $settings["language"]; ?>">
	<iframe src="http://hitchwiki.org/en/index.php?title=Maps.hitchwiki.org&redirect=no&action=render&ctype=text/plain" frameborder="0" width="0" height="0" scrolling="no" style="display: block; width: 0; height: 0; border:0; position: absolute; top:-100px; left: -100px;" id="loginRefresh" name="loginRefresh"></iframe>
		<div id="Content">
	
		<div id="Header">
			<div id="Logo">
				<h1><a href="http://www.hitchwiki.org/"><span>Hitchwiki</span></a></h1>
				<h2><?php echo _("Maps"); ?></h2>

				<div class="Navigation">
					<a href="<?php echo _("http://hitchwiki.org/en/"); ?>"><?php echo _("Wiki"); ?></a> | <a href="http://hitchwiki.org/community/"><?php echo _("Community"); ?></a> | <a href="http://hitchwiki.org/planet/"><?php echo _("Planet"); ?></a>
				</div>

				<h3><?php echo $slogan; ?></h3>

			<!-- /Logo -->
			</div>
			
			<div id="LoginNavi" class="<?php
				if($user["logged_in"]===true) echo 'logged_in';
				else echo 'logged_out';
				
			?>">
				
					<ul class="align_right" id="loginSidemenu">
						<li><a href="#" id="toggleLanguages" title="<?php echo _("Choose language"); ?>">Language</a></li>
						
						<?php // User is logged in:
						if($user["logged_in"]===true): ?>
				
						<li><a href="./?page=settings" id="settings" class="pagelink"><?php echo _("Settings"); ?></a></li>
						<li><a href="./?page=profile" id="profile" class="pagelink"><?php echo _("Profile"); ?></a></li>
						<!--
						<li><a href="http://hitchwiki.org/en/index.php?title=Special:UserLogout&returnto=Maps.hitchwiki.org" id="logout"><?php echo _("Logout"); ?></a></li>
						-->
						<?php else: ?>
						
						<li><a href="./?page=why_register" id="why_register" class="pagelink"><?php echo _("Why register?"); ?></a></li>
						<li><a href="http://hitchwiki.org/en/index.php?title=Special:UserLogin&amp;type=signup&amp;returnto=Maps.hitchwiki.org" id="register"><?php echo _("Register!"); ?></a></li>
					
						<?php endif; ?>
					</ul>
					
					<div id="loginHellomenu">
					<?php 
					// User is logged in:
					if($user["logged_in"]===true): ?>
						
						<span id="Hello"><span class="icon <?php
						
						/*
						 * Icon
						 */
						if($settings["language"]=='pirate') echo 'skull'; // Yarr!
						elseif($user["admin"]===true) echo 'tux'; // ;-)
						else echo 'user_orange'; // default
						
						echo '"'; //end class
						
						// Gravatar
						//if($user["allow_gravatar"]=="1" && !empty($user["email"])) echo ' style="background-image: url(http://www.gravatar.com/avatar/'.md5($user["email"]).'/?s=16);"';
						
						echo '>'; //end tag
						
						
						/*
						 * Pick one random hello
						 */
						$hello = array(
							"Hi!" => "GB",
							"Hello!" => "GB",
							"Tere!" => "EE",
							"Hei!" => "FI",
							"Moi!" => "FI",
							"¡Hola!" => "ES",
							"Shalom!" => "IL",
							"Namaste!" => "NP",
							"Namaste!" => "IN",
							"Labas!" => "LT",
							"Mambo!" => "CG",
							"Bok!" => "HR",
							"Hallo!" => "NL",
							"Hallo!" => "DE",
							"Moin!" => "DE",
							"Servus!" => "DE",
							"Grüß Gott!" => "AU",
							"Hej!" => "DK",
							"Hej!" => "SE",
							"Hejsan!" => "SE",
							"Ciào!" => "IT",
							"Sveiki!" => "LV",
							"Moïen!" => "LU",
							"Salamaleikum," => "SN",
							"Čau!" => "SK",
							"Hoezit!" => "ZA",
							"Jambo!" => "KE",
							"Selam!" => "TR"
						);
						$hello_greeting = array_rand($hello,1);
						
						?><span title="<?php printf(_("Hello from %s"), ISO_to_country($hello[$hello_greeting])); ?>"><?php echo $hello_greeting; ?></span> <a href="./?page=profile" id="profile" class="pagelink"><?php echo $user["name"]; ?></a></span></span>
						<small class="logout align_right"><a href="http://hitchwiki.org/en/index.php?title=Special:UserLogout&returnto=Maps.hitchwiki.org" id="logout">&rsaquo; <?php echo _("Logout"); ?></a></small>
					
					<?php 
					// User is NOT logged in:
					else: ?>
					
						<a href="http://hitchwiki.org/en/index.php?title=Special:UserLogin&amp;returnto=Maps.hitchwiki.org" id="loginOpener" class="icon lock align_right"><?php echo _("Login"); ?></a>
					
					<?php endif; ?>
					</div>
			<!-- /Login -->
			</div>
		
		<!-- /Header -->
		</div>
		<div id="Login">
				<?php /* By submitting this with JS, you can reload this page and map will be as it was, if you fill lat/lon/zoom inputs and change post->get */ ?>
				<form method="post" action="./" id="reloadPage" class="hidden">
				    <input type="submit" />
				</form>

</div>
			
			<div id="Sidebar">
			
				<ul id="Navigation" role="navigation">
				
					<!-- 1st block -->
					<li>
						<ul>
							<li><h3><?php echo _("Find places"); ?></h3></li>
							<li id="search">
								<form method="get" action="#" id="search_form" name="search" role="search">
									<div class="ui-widget">
									<input type="text" value="" id="q" name="q" />
									<button type="submit" class="search_submit button" title="<?php echo _("Search"); ?>"> <span class="icon magnifier">&nbsp;</span><span class="hidden"><?php echo _("Search"); ?></span></button>
									<div class="clear"></div>
									</div>
								</form>
								
							</li>
							
							<li id="nearby" style="display:none;">
								<span class="icon map_magnify"><?php echo _("Nearby places from"); ?>:</span><br />
								<ul>
									<li class="locality" style="display:none;"><a href="#" title="<?php echo _("Show the city on the map"); ?>"></a></li>
									<li class="state" style="display:none;"><a href="#" title="<?php echo _("Show the state on the map"); ?>"></a></li>
									<li class="country" style="display:none;"><a href="#" title="<?php echo _("Show the country on the map"); ?>"></a></li>
								</ul>
							</li>
						</ul>
					</li>

					<!-- 2nd block -->
					<li>
						<ul>
							<li><a href="#" id="add_place" class="icon add"><?php echo _("Add place"); ?></a></li>
							<li><a href="./?page=public_transport" id="public_transport" class="icon pagelink underground"><?php echo _("Public transport"); ?></a></li>
							<li><a href="./?page=countries" id="countries" class="icon world pagelink"><?php echo _("Countries"); ?></a></li>
							<li><a href="#" id="tools" class="icon lorry"><?php echo _("Tools"); ?></a></li>
							<li><a href="#" id="download" class="icon page_white_put cardlink"><?php echo _("Download"); ?></a></li>
							<li><a href="#" id="link_here" class="icon link cardlink"><?php echo _("Link here"); ?></a></li>

							<?php if($user["logged_in"]===true): ?>
							<li><a href="./?page=users" id="users" class="icon user pagelink"><?php echo _("Members"); ?></a></li>
							<?php endif; ?>

							<li><a href="./?page=about" id="about" class="icon help pagelink"><?php echo htmlspecialchars(_("Help & About")); ?></a></li>
							<li><a href="./?page=statistics" id="statistics" class="icon chart_bar pagelink"><?php echo _("Statistics"); ?></a></li>
							<li><a href="./?page=news" id="news" class="icon new pagelink"><?php echo _("News"); ?></a></li>

			    			<?php // Visible only for admins
			    			if($user["admin"]===true): ?>
						</ul>
					</li>
					<li>
						<ul>
			    			<li><a href="./admin/" class="icon tux"><?php echo _("Admins"); ?></a></li>
							<li><a href="./?page=log" id="log_all" class="icon page_white_text pagelink"><?php echo _("Log"); ?></a></li>
							<li><a href="#" id="streetview" class="icon eye cardlink"><?php echo _("Street view"); ?></a></li>
							<li><a href="#" id="trips" class="icon flag_green pagelink"><?php echo _("My trips"); ?></a></li>
							<?php endif; ?>

						</ul>
					</li>
									
				</ul>


			<div id="Footer">
			    <ul>
			    	<li>
			    		<a rel="license" href="<?php echo _("http://creativecommons.org/licenses/by-sa/3.0/"); ?>" title="<?php echo _("Licensed under a Creative Commons Attribution-ShareAlike 3.0 Unported License"); ?>"><img alt="<?php echo _("Creative Commons License"); ?>" src="static/gfx/cc-by-sa.png" width="67" height="20"/></a>
			    		&nbsp;
			    		<a href="http://www.facebook.com/Hitchwiki" class="icon facebook" style="margin: 2px 0 0 3px; padding-top: 3px; display: block; float: right;">Facebook</a>
			    	</li>

			    	<li id="developers">
			    		<a href="#" class="cardlink" id="contact"><?php echo _("Contact us!"); ?></a>
			    		&bull; <a href="http://github.com/Hitchwiki"><?php echo _("Developers"); ?></a>
			    		<br />
			    		<a href="#" id="api" class="pagelink"><?php echo _("API"); ?></a>
			    		<?php if($settings["debug"] == true) { echo '&bull; <a href="#" class="toggle_log">'._("Toggle log").'</a>'; } ?>
			    	</li>
			    </ul>

			<!-- /Footer -->
			</div>

			<!-- /Sidebar -->
			</div>
	        
	        
	        <!-- Adding a alace panel -->
	       <div id="AddPlacePanel">
	       		<h4 class="icon add"><?php echo _("Add place"); ?></h4>
	       	</div>
	        <!-- /Adding a alace panel -->
	        
	        
			<!-- AJAX Content Area for pages-->
			<div id="pages">
				<a href="#" class="close ui-button ui-corner-all ui-state-default ui-icon ui-icon-closethick" title="<?php echo _("Close"); ?>"><?php echo _("Close"); ?></a>
				<div class="page">
					<div class="content"> </div>
				</div>
			</div>
			<!-- /pages -->
	        
	        
			<!-- cards -->
			<div id="cards"></div>
			<!-- /pages -->
	        
	        
	        <!-- The Map -->
	        <div id="map">
	        	<br /><br />
	        	<?php echo _("Turn JavaScript on from your browser."); ?>
			</div>
	       <!-- /map -->
	       
	       
	        <!-- The Place panel -->
	       <div id="PlacePanel"></div>
	       <!-- /Place panel -->
	       
	       
	       <!-- Tools -->
	       <div id="toolsPanel" class="floatingPanel draggable hidden">
	       		<h4 class="icon lorry">
	       			<?php echo _("Tools"); ?>
	       			<a href="#" class="close ui-icon ui-icon-closethick align_right" title="<?php echo _("Close"); ?>"><?php echo _("Close"); ?></a>
	       		</h4>
				<div id="controlToggle">
				
				        <span class="icon cursor">
				        	<input type="radio" name="type" value="none" id="noneToggle" onclick="toggleControl(this);" checked="checked" />
				        	<label for="noneToggle"><?php echo _("Navigate"); ?></label>
				        </span><br />
				        
				        <span class="icon vector">
                			<input type="radio" name="type" value="line" id="lineToggle" onclick="toggleControl(this);" />
                			<label for="lineToggle"><?php echo _("Measure distance"); ?></label>
				        </span><br />
				        
				        <span class="icon shape_handles">
				        	<input type="radio" name="type" value="polygon" id="polygonToggle" onclick="toggleControl(this);" />
				        	<label for="polygonToggle"><?php echo _("Measure area"); ?></label>
				        </span><br />
				        
				        <?php /* 
				        Note that the geometries drawn are planar geometries and the metrics returned by the measure control are planar 
				        measures by default. If your map is in a geographic projection or you have the appropriate projection definitions 
				        to transform your geometries into geographic coordinates, you can set the "geodesic" property of the control to 
				        true to calculate geodesic measures instead of planar measures.
				        
				        <input type="checkbox" name="geodesic" checked="checked" id="geodesicToggle" onclick="toggleGeodesic(this);" />
				        <label for="geodesicToggle"><?php echo _("Use geodesic measures"); ?></label>
				        */ ?>
				        
				    	<div class="align_right clear"><?php echo _("Measure"); ?>: <span id="toolOutput">-</span></div>
				    	
				    	<hr />
				    	
				    	<label class="icon zoom"><?php echo _("Show markers after zoom level"); ?>:</label>
				    	<div id="zoom_slider"></div>
				    	
				    	<span class="align_left"><?php echo _("Default"); ?>: <?php echo $default_markersZoomLimit; ?></span>
				    	<span class="align_right">
				    		<b id="zoom_slider_amount"></b><span id="zoomlevel">
				    											<span class="z_continent hidden"> - <?php echo _("Continent level"); ?></span>
				    											<span class="z_country hidden"> - <?php echo _("Country level"); ?></span>
				    											<span class="z_city hidden"> - <?php echo _("City level"); ?></span>
				    											<span class="z_streets hidden"> - <?php echo _("Street level"); ?></span>
				    										</span>
				    	</span>

				</div>
	       </div>
	       <!-- /tools -->
	       
	       
	       <!-- languages -->
	       
	       <div id="languagePanel" class="floatingPanel hidden">
	       		<h4 class="icon world">
	       			<?php echo _("Choose language"); ?>
	       			<a href="#" class="close ui-icon ui-icon-closethick align_right" title="<?php echo _("Close"); ?>"><?php echo _("Close"); ?></a>
	       		</h4>
				<div id="controlToggle">
				
				    <ul>
				    	<?php
				    	    // Print out available languages
				    	    foreach($settings["valid_languages"] as $code => $name) {
				    	    	?>
				    	    	<li>
				    	    		<span class="icon" style="background-image: url(static/gfx/flags/<?php echo strtolower(shortlang($code, 'country')); ?>.png);">	
				    	    			<?php
				    	    			echo '<a href="./?lang='.$code.'"';
				    	    			if($code == $settings["language"]) echo ' class="selected"';
				    	    			echo ' title="'.$settings["languages_in_english"][$code].'">'.$name.'</a>';
				    	    			?>
				    	    		</span>
				    	    	</li>
				    	    	<?php
				    	    }
				    	?>
					</ul>
					<a href="./?page=translate" id="translate" class="pagelink"><small class="light"><?php echo _("Help us with translating!"); ?></small></a>
				    						        
				</div>
	       </div>
	       <!-- /languages -->
		

	       <!-- Placeholder for simple error/info -dialog. see info_dialog(); from main.js for more. -->
	       <div id="dialog-message"></div>
	       
	       
	       <!-- Loading -bar -->
	       <div id="loading-bar"><small class="title"></small></div>
	       

			<div id="map_selector">
				<button id="selected_map" class="ui-corner-bottom"><?php echo _("Map"); ?>: <span class="map_name">Open Street Map</span></button>

				<div id="maplist" class="ui-corner-bottom">
				<ul>
					<li class="first"><a href="#" name="mapnik" class="icon icon-osm<?php if($user["map_default_layer"]=='mapnik' OR empty($user["map_default_layer"]) OR !isset($user["map_default_layer"])) { echo ' selected'; } ?>"><?php echo $map_layers["osm"]["mapnik"]; ?></a></li>
					<li><a href="#" name="osmarender" class="icon icon-osm<?php if($user["map_default_layer"]=='osmarender') { echo ' selected'; } ?>"><?php echo $map_layers["osm"]["osmarender"]; ?></a></li>
					<?php
					
					// Google
					if(!empty($settings["google_maps_api_key"]) && $print_map_google===true) {
						foreach($map_layers["google"] as $map => $name) {
				    		echo '<li><a href="#" name="'.$map.'" class="icon icon-google';
				    		if($user["map_default_layer"]==$map) echo ' selected';
				    		echo '">'.$name.'</a></li>';
						}
					}
					
					// Yahoo
					if(!empty($settings["yahoo_maps_appid"]) && $print_map_yahoo===true) {
						foreach($map_layers["yahoo"] as $map => $name) {
				    		echo '<li><a href="#" name="'.$map.'" class="icon icon-yahoo';
				    		if($user["map_default_layer"]==$map) echo ' selected';
				    		echo '">'.$name.'</a></li>';
						}
					}
					
					// Virtual Earth
					if($settings["ms_virtualearth"]===true && $print_map_vearth===true) {
						foreach($map_layers["vearth"] as $map => $name) {
				    		echo '<li><a href="#" name="'.$map.'" class="icon icon-bing';
				    		if($user["map_default_layer"]==$map) echo ' selected';
				    		echo '">'.$name.'</a></li>';
						}
					}
					
				    ?>
				</ul>
				</div>
			</div>

	       
		<!-- /Content -->
		</div>
		
		<!-- for debugging -->
		<div id="log" class="hidden">
			<b class="handle">
				<?php echo _("Log"); ?>
	       		<a href="#" class="close ui-icon ui-icon-closethick align_right" title="<?php echo _("Close"); ?>"><?php echo _("Close"); ?></a>
			</b>
			<ol><li>Hitchwiki Maps log started <?php echo date("r"); ?></li></ol>
		</div>


<?php // Piwik analytics
if(isset($settings["piwik_id"]) && !empty($settings["piwik_id"])): ?>
<!-- Piwik -->
<script type="text/javascript">
/* <![CDATA[ */
var pkBaseURL = (("https:" == document.location.protocol) ? "https://piwik.guaka.org/" : "http://piwik.guaka.org/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
/* ]]> */
</script>
<script type="text/javascript">
/* <![CDATA[ */
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", <?php echo $settings["piwik_id"]; ?>);
piwikTracker.setDocumentTitle("Hitchwiki Maps");
piwikTracker.setDownloadClasses("download");
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
/* ]]> */
</script><noscript><p><img src="http://piwik.guaka.org/piwik.php?idsite=<?php echo $settings["piwik_id"]; ?>" style="border:0" alt=""/></p></noscript>
<!-- /Piwik -->
<?php endif; ?>

    </body>
</html>