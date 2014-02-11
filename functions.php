<?php

function set_edition($parameter, $url) {

	$editionURL = add_query_arg( array("edition" => $parameter), $url );
	
	Header("Location: " . $editionURL);
	exit;

}

?>