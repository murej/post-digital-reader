<?php

function get_edition_URL($parameter, $url) {

	return add_query_arg( array("edition" => $parameter), $url );
	
}

function set_edition($parameter, $url) {
	
	Header("Location: " . get_edition_URL($parameter, $url));
	exit;

}

?>