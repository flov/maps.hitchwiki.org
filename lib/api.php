<?php
/**
 * @package    maps_api
 * @author     Mikael Korpela <mikael@ihminen.org>
 * @copyright  Copyright (c) 2010 {@link http://www.ihminen.org Mikael Korpela}
 * @license    http://creativecommons.org/licenses/by-sa/3.0/ Creative Commons Attribution-ShareAlike 3.0 Unported
 *
 * This is a simple API class to get Hitchwiki markers from {@link http://maps.hitchwiki.org/ Hitchwiki Maps} database.
 * 
 * Created: 2010-07-21
 *
 */

class maps_api
{

	public $format = 'json'; // json (default) | kml | array | string


	/*
	 * Construct
	 */
	public function __construct($format="json") {
	
		$this->set_format($format);
	
		start_sql(); // from lib/functions.php
		return true;
	}


	/**
	 * Set format
	 * format: json (default) | kml | array | string
	 */
	public function set_format($format='json') {
		if(strtolower($format) == 'json' 
			OR strtolower($format) == 'kml' 
			OR strtolower($format) == 'array' 
			OR strtolower($format) == 'string') {
				$this->format = strtolower($format);
			}
	}


	/*
	 * Function to stop API
	 */
	function API_error($msg="true", $error_format=false) {
		$error["error"] = strip_tags($msg);

		// You can use custom return format in errors if you want to
		if($error_format==false) $error_format = $this->format;

   		if($error_format=="string") return print_r($error,true);
   		elseif($error_format=="json") return json_encode($error);
   		elseif($error_format=="kml") return $this->array2kml($error);
   		else return $error;
   
		exit;
	}
	

	/*
	 * Output an API result:
	 * input: array
	 * output: array formated in wanted format (see $this->format)
	 */
	function output( $result = array() ) {

		if(empty($result)) return $this->API_error("empty");
   		elseif($this->format=="json") return json_encode($result);
   		elseif($this->format=="kml") return $this->array2kml($result);
   		elseif($this->format=="string") return print_r($result,true);
   		else return $result;
	}
	
	
	/*
	 * Get a place by ID
	 */
	function getMarker($id, $more=false) {
    	
    	// Get place
    	// Validate more: false | true
    	$place = ($more == true) ? get_place($id,true): get_place($id,false);

   		// Return
    	if($place===false) return $this->API_error("Illegal ID.");
    	else return $this->output($place);

	}


	/*
	 * Get places by boundingbox coordinates
	 * Square corners, eg. 60.0066276,60.3266276,24.783508,25.103508 (Helsinki, Finland)
	 */
	function getMarkersByBound($lt, $lb, $rt, $rb, $description=false) {
    	global $settings;


		// Get description with markers?
		if(!isset($settings["valid_languages"][$description])) $description = false;

    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating`";
    	
    	if($description!=false) $query .= ",`".$description."`";
    	
    	$query .= " FROM `t_points` WHERE 
					`type` = 2 AND 
					`lat` > ".mysql_real_escape_string($lt)." AND 
					`lat` < ".mysql_real_escape_string($lb)." AND 
					`lon` > ".mysql_real_escape_string($rt)." AND 
					`lon` < ".mysql_real_escape_string($rb);

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    
   		    if($description!=false) {
   		    	$result[$i]["description"] = $r[$description];
   		    	//$result[$i]["description_language"] = $description;
   		    }
   		    
   		    $i++;
   		}
   	
   	
   		// Return
   		return $this->output($result);
    	
	}


	/*
	 * Get places by city
	 */
	function getMarkersByCity($city) {
    	
    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating`,`city` FROM `t_points` WHERE 
					`type` = 2 AND 
					`city` = '".mysql_real_escape_string($city)."'";

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    $i++;
   		}
   		
   		
   		// Return
   		return $this->output($result);
    }


	/*
	 * Get places by country
	 */
	function getMarkersByCountry($country) {
    	
    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating`,`country` FROM `t_points` WHERE 
					`type` = 2 AND 
					`country` = '".mysql_real_escape_string($country)."'";

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    $i++;
   		}
   		
   		
   		// Return
   		return $this->output($result);
    }


	/*
	 * Get places by continent
	 */
	function getMarkersByContinent($continent) {
    	
    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating`,`continent` FROM `t_points` WHERE 
					`type` = 2 AND 
					`continent` = '".mysql_real_escape_string($continent)."'";

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    $i++;
   		}
   		
   		
   		// Return
   		return $this->output($result);
    }



	/*
	 * Get continents
	 * all: true | false (default)
	 * coordinates: true | false (default)
	 */
	function getCountries($all=false, $coordinates=false) {

    	// List all countries, or just ones with places?
    	// Validate as "true" or "false"
    	if(is_bool($all) === false) $all = false;
   
    	// List with coordinates
    	// Validate as "true" or "false"
    	if(is_bool($coordinates) === false) $coordinates = false;

    	// Get a list of countries
    	$result = list_countries("array", "name", false, true, $all, $coordinates);

   		// Return
   		return $this->output($result);
    }


	/*
	 * Get continents
	 */
	function getContinents() {
    	
    	// Get list of continents
    	$result = list_continents("array", true);
    	
   		// Return
   		return $this->output($result);
    }


    
	/*
	 * Get all markers
	 */
	function getAll() {
    	
    	// Build a query
    	$query = "SELECT `id`,`type`,`lat`,`lon`,`rating` FROM `t_points` WHERE 
					`type` = 2";

	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		$i=0;
		while($r = mysql_fetch_array($res, MYSQL_ASSOC)) {
   		    $result[$i]["id"] = $r["id"];
   		    $result[$i]["lat"] = $r["lat"];
   		    $result[$i]["lon"] = $r["lon"];
   		    $result[$i]["rating"] = $r["rating"];
   		    $i++;
   		}

   		// Return
   		return $this->output($result);
    }

	
	
	/* 
	 * Remove comment
	 */
	function removeComment($id=false) {
	
		// ID
		if($id===false OR empty($id) OR !is_numeric($id)) return $this->API_error("Invalid ID.");
		
		// Remove it
   		$res = mysql_query("DELETE FROM `t_comments` WHERE `id` = ".mysql_real_escape_string($id)." LIMIT 1");

   		if(!$res) return $this->API_error("Query failed!");
   		
   		if(mysql_affected_rows() >= 1) return $this->output( array("success"=>true) );
   		else return $this->API_error("Comment ID not found.");
	
	}
	
	
	/*
	 * Add comment
	 * Comment be an array including:
	 * - place_id (required)
	 * - comment (required)
	 * - user_id (optional)
	 * - user_nick (optional)
	 */
	function addComment($comment=array()) {
	
		// Place ID
		if(!isset($comment["place_id"]) OR empty($comment["place_id"]) OR !is_numeric($comment["place_id"])) return $this->API_error("Invalid place ID.");
	
		// Comment
		if(!isset($comment["comment"]) OR empty($comment["comment"])) return $this->API_error("Comment missing.");
		else {
			$comment["comment"] = htmlspecialchars($comment["comment"]);
		}
	
		// User ID
		if(isset($comment["user_id"])) {
			if(!is_numeric($comment["user_id"]) OR empty($comment["user_id"])) return $this->API_error("Invalid user ID.");
			else $user_id = $comment["user_id"];
		} else {
			$user_id = "NULL";
		}
		
		
		// User nick
		if(isset($comment["user_nick"]) && !empty($comment["user_nick"]) && available_nick($comment["user_nick"])) $nick = "'".mysql_real_escape_string($comment["user_nick"])."'";
		else {
			$nick = "NULL";
			
			// If nick was empty but user_id no, produce nick out from it to send back to the user 
			if($user_id != "NULL") $comment["user_nick"] = username($user_id);
			else $comment["user_nick"] = _("Anonymous");
		}
	
		// Build a query
		$query = "	INSERT INTO `t_comments` (`id`, `fk_place`, `fk_user`, `nick`, `comment`, `datetime`) 
						VALUES (NULL, 
								'".mysql_real_escape_string($comment["place_id"])."', 
								".mysql_real_escape_string($user_id).", 
								".$nick.", 
								'".mysql_real_escape_string($comment["comment"])."', 
								NOW())";
	
	    // Build an array
   		$res = mysql_query($query);
   		if(!$res) return $this->API_error("Query failed!");
   		
   		$comment["date"] = date("j.n.Y");
   		$comment["time"] = date("H:i:s");
   		$comment["date_r"] = date("r");
   		$comment["comment"] = Markdown(stripslashes($comment["comment"]));
   		
   		$comment["id"] = mysql_insert_id();
   		$comment["success"] = true;
   		
   		// Return
   		return $this->output($comment);
	
	}

	/*
	 * Get all comments for a place
	 * 
	 */
	function getComments($id=false, $limit=false) {
	
		
		$result = get_comments($id, $limit);
		
   		if($result===false) return $this->API_error("Query failed!");
		else return $this->output($result);
	
	}

	/*
	 * Rate a place
	 * TODO: flood blocking by IP?
	 */
	 function rate($rating, $point_id, $user_id=false) {
	 	
	 	
	 	// Validating values
		if(empty($point_id) OR !is_numeric($point_id)) return $this->API_error("Rating Failed. Wrong place ID.");
		
		if(empty($rating) OR !is_numeric($rating)) return $this->API_error("Rating Failed. Rate must be between 1-5.");
	
		if($user_id!==false) {
	 		$user = current_user();
	 	
			if(empty($user_id) OR !is_numeric($user_id)) return $this->API_error("Rating Failed. Wrong user ID.");
			elseif($user_id != $user["id"]) return $this->API_error("Rating Failed. You need to be logged in. ".$user["id"]);
			
			$user = mysql_real_escape_string($user_id);
		}
		else $user = "NULL";

		// Check if user has already rated this spot (then we'll just old record)
		// any old records there? -check
		if($user != "NULL") {
		
			$res4 = mysql_query("SELECT `id`,`fk_user`,`fk_point` FROM `t_ratings` WHERE `fk_user` = ".$user." AND `fk_point` = ".mysql_real_escape_string($point_id)." LIMIT 1");
   			if(!$res4) return $this->API_error("Query failed! (4)");
			
			// If we have a result
			if(mysql_num_rows($res4) > 0) {
				// Get an ID of row we need to just update
				while($r = mysql_fetch_array($res4, MYSQL_ASSOC)) {
					$update_old = $r["id"];
				}
			}

		}
		
		// Since we had a result on "any old records there?"-check, perform just an update
		if(isset($update_old)) {
		
   			$res3 = mysql_query("UPDATE `t_ratings` SET `rating` = '".mysql_real_escape_string($rating)."',`datetime` = NOW() WHERE `id` = ".mysql_real_escape_string($update_old).";");
   			if(!$res3) return $this->API_error("Query failed! (3)");
   			
   			$result["success"] = true;
   			$result["updated"] = true;
		
		} 
		// No result on "any old records there?"-check, so just add new record
		else {

			// Build a query
			$query = "INSERT INTO `t_ratings` (`id`,`fk_user`,`fk_point`,`rating`,`datetime`,`ip`) 
							VALUES (NULL, 
									".$user.", 
									".mysql_real_escape_string($point_id).", 
									".mysql_real_escape_string($rating).", 
									NOW(),
									'".$_SERVER['REMOTE_ADDR']."')";

   			$res = mysql_query($query);
   			if(!$res) return $this->API_error("Query failed! (1)");
   			
   			$result["success"] = true;
   			
   		}
   		
   		$result["point_id"] = $point_id;
   		$result["rating_stats"] = rating_stats($point_id);
   		
   		// Update "quick access info" to the t_points
   		$res2 = mysql_query("UPDATE `t_points` SET `rating` = '".mysql_real_escape_string(round($result["rating_stats"]["exact_rating"]))."',`rating_count` = '".mysql_real_escape_string($result["rating_stats"]["rating_count"])."' WHERE `id` = ".mysql_real_escape_string($point_id).";");
   		if(!$res2) return $this->API_error("Query failed! (2)");
   			
   		return $this->output($result);
	 }


	/*
	 * Output data in KML format
	 * TODO!
	 */
	function array2kml($array) {
		$array["format"] = "kml";
		return print_r($array,true);
	}


	/*
	 * Output a string in zipped format
	 *
	 * TODO! http://php.net/manual/en/book.zip.php
	 *
	 * gmz: true | false (default) - use for "kml"-files: uses "gmz" file-extension instead of "zip"
	 */
	function zipPackage($string, $gmz=false) {
		/*
		$zip = new ZipArchive();
		$filename = "./test112.zip";
		
		if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {
		    exit("cannot open <$filename>\n");
		}
		
		$zip->addFromString("testfilephp.txt" . time(), "#1 This is a test string added as testfilephp.txt.\n");
		$zip->addFromString("testfilephp2.txt" . time(), "#2 This is a test string added as testfilephp2.txt.\n");
		$zip->addFile($thisdir . "/too.php","/testfromfile.php");
		echo "numfiles: " . $zip->numFiles . "\n";
		echo "status:" . $zip->status . "\n";
		$zip->close();
		*/
		return false;
	}


	/*
	 * Get a list of available languages
	 */
	function getLanguages() {
		global $settings;

    	foreach($settings["valid_languages"] as $code => $lang) {
    		$result[$code]["code"] = $code;
    		$result[$code]["name"] = _($settings["languages_in_english"][$code]);
    		$result[$code]["name_original"] = $lang;
    	}
    	
   		// Return
   		return $this->output($result);
    }


}
?>