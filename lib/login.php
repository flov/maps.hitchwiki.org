<?php
/* Hitchwiki Maps - login.php
 * Login script
 */
 
 
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header("Content-type: application/json");

#{login:"true"}
#{login:"false"}

print_r($_POST);

?>