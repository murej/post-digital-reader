<?php

function check_and_set_edition($parameter, $url) {

	if(isset($parameter) === false){
		$editionURL = add_query_arg( array("edition" => "Original"), $url);
		
		Header("Location: " . $editionURL);
		exit;
	}

}

?>