<?php

//Get the submitted form
ob_start();
require_once($_POST["rootpath"]);

$paragraphIDs = json_decode( urldecode( $_COOKIE["myCollection"] ) );

$editionTitle = $_POST["editionTitle"];
//$author = $_POST["author"];
//$email = $_POST["email"];

$path = $_POST["rootpath"];
$nonce = $_POST["_wpnonce"];
$referer = $_POST["_wp_http_referer"];

//Load WordPress
require($path);

//Verify the form fields
if (! wp_verify_nonce($nonce) ) die('Security check'); 

	foreach( $paragraphIDs as $parID ) {
		wp_set_post_tags($parID, $editionTitle, true);
	}

// remove cookie
if(isset($_COOKIE["myCollection"])) {
	unset( $_COOKIE["myCollection"] );
	setcookie("myCollection", null, time()-3600, "/");
}

header("Location: " . $referer);

?>