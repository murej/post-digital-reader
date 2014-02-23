<?php

function get_edition_URL($parameter, $url) {

	return add_query_arg( array("edition" => $parameter), $url );
	
}

function set_edition($parameter, $url) {
	
	Header("Location: " . get_edition_URL($parameter, $url));
	exit;

}

function get_paragraphIDs($cookieData) {
	return json_decode( urldecode( $cookieData ) );
}

// Add [reference-X] shortcode
function add_reference_to_post( $atts ) {

	global $post;

	extract( shortcode_atts( array(
		'id'	 => '',
		'postID' => $post->ID
	), $atts));
		
	//get custom field array
	$data = get_post_meta($postID, "reference-".$id, false);
	
	$return = '<sup><a href="'.$data[0]["link"].'" class="ref-link system" target="_blank">[&rarr;]</a></sup>';
	
	if( !empty($data[0]["quote"]) ) {
		$return = '<q>'.$data[0]["quote"].'</q>'.$return;
	}
	
	return $return;
}
add_shortcode( 'reference', 'add_reference_to_post' );

/*
// this happens when WP Import All creates a post
add_action('pmxi_saved_post', 'post_saved', 10, 1);
 
function post_saved($id) {

	$references = [];

	// get post content
	$content = get_post_field('post_content', $id);
	
	// try to find first reference to import
	$start = strpos($content, "[import-reference");
	
	// as long as there are references to import
	while( $start !== false ) {
		
		// find end of it
		$end = strpos($content, "[/import-reference]", $start)+19;

		// get whole shortcode
		$shortcode = substr($content, $start, ($end-$start));
		
		// parse it
		$references[] = json_decode( do_shortcode($shortcode) );
		
		// prepare it to be saved
		// = array( "link" => $atts["link"], "quote" => $atts["quote"] );
		
		// remove it from content
		$content = str_replace($shortcode, "", $content);
		
		// try to find next reference
		$start = strpos($content, "[import-reference");

	}

	// add each reference as a custom field array
	foreach($references as $i => $ref) {
		add_post_meta($id, 'reference-'.($i+1), $ref, true);
	}

	$content = trim($content);

	// update post with removed links
	$my_post = array(
		'ID'           => $id,
		'post_content' => $content
	);
	wp_update_post( $my_post );

}
// Add Shortcode
function import_reference( $atts , $content = null ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'link' => '',
		), $atts )
	);
		
	return '{ "link" : "'.$link.'", "quote" : "'.$content.'" }';
}
add_shortcode( 'import-reference', 'import_reference' );
*/


function generate_PDF($edition, $chapters) {

	include('mpdf/mpdf.php');
	$stylesheet = file_get_contents(get_bloginfo('template_url')."/css/print.css");
	$footer = array (
		'odd' => array (
			'L' => array (
				'content' => '{PAGENO}',
				'font-size' => 12,
				'font-style' => 'R',
				'font-family' => 'helvetica',
				'color'=>'#000000'
			),
			'C' => array (
				'content' => '',
				'font-size' => 12,
				'font-style' => 'R',
				'font-family' => 'helvetica',
				'color'=>'#000000'
			),
			'R' => array (
				'content' => '',
				'font-size' => 12,
				'font-style' => 'R',
				'font-family' => 'helvetica',
				'color'=>'#000000'
			),
			'line' => 0,
			),
		'even' => array ()
	);
	
	
	
	// set document		
	$mpdf = new mPDF('utf-8');
	$mpdf->WriteHTML($stylesheet,1);
	
	// write cover page
	$mpdf->WriteHTML('<h1><span class="serif">P05T-D16174L</span><br> READER.<br> The <i class="strikethrough serif">form</i> role <br>of books in the <br>digital <i class="strikethrough serif">media</i> age</h1>');
	
	// if my collection
	if( $edition == "-1" ) {
		$editionName = "My Collection";
		// write info
		$mpdf->WriteHTML('<div id="edition"><h3 class="title">'.$editionName.'</h3><p class="info system">on '.date('d F Y').'</p><p class="link">'.get_bloginfo('url').'/?edition='.$edition.'</p></div>');
		$paragraphIDs = get_paragraphIDs( $_COOKIE["myCollection"] );
	}
	// if edition
	else {
		$editionName = get_term_by("slug", $edition, "post_tag")->name;
		// write info
		$mpdf->WriteHTML('<div id="edition"><h4 class="system edition">Edition:</h4><h3 class="title">'.$editionName.'</h3><p class="info system">by Jure Martinec on '.date('d F Y').'</p><p class="link">'.get_bloginfo('url').'/?edition='.$edition.'</p></div>');
	}
	
	// go through all chapters
	foreach($chapters as $chapter) {

		// if my collection
		if( $edition == "-1" ) {
				
			$queryParams = array( 'nopaging' => true, 'category__in' => array( $chapter->term_id ), 'post__in' => $paragraphIDs );
			$content = get_posts($queryParams);
		}
		// if edition
		else {
			$content = get_posts('nopaging=true&tag='.$edition.'&cat='.$chapter->term_id);
		}
				
		if(!empty($content)) {

			$mpdf->AddPage();
			$mpdf->setFooter($footer);
			$mpdf->WriteHTML('<h3>Design for</h3><h2>'.$chapter->name.'</h2>',2);

			foreach($content as $paragraph) {
			
				$output = str_replace("</p>", "<span> #". $paragraph->post_title ."</span></p>", do_shortcode($paragraph->post_content));
			
				$mpdf->WriteHTML($output,2);
			}
		}
		else {

			$mpdf->AddPage();
			$mpdf->setFooter($footer);
			$mpdf->WriteHTML('<h3>Design for</h3><h2 class="strikethrough">'.$chapter->name.'</h2>',2);
		}
	}

	$mpdf->SetTitle("Post-digital Reader: ".$editionName);
	//$mpdf->SetAuthor();

	$mpdf->Output("Post-digital Reader - ".$editionName.".pdf","I");
	exit;
}

function importJSON() {
	
	$allPosts = json_decode( file_get_contents( content_url() . "/book.json" ) );
	
	$chapters = array(
	
		1 => "Reactive environments",
		2 => "Language in/as any form",
		3 => "Recontextualisation",
		4 => "Focus",
		5 => "Ambiguity",
		6 => "Uniqueness and hybridity of media"
	
	);

	removeMyPosts();
		
	foreach($allPosts as $par) {

		$postCount++;

		$checkExisting = get_page_by_title( $par->id, OBJECT, 'post' );

		$postData = array(
			//'ID'             => [ <post id> ] // Are you updating an existing post?
			'post_content'   => $par->content, // The full text of the post.
			//'post_name'      => [ <string> ] // The name (slug) for your post
			'post_title'     => $par->id, // The title of your post.
			'post_status'    => 'publish', // Default 'draft'.
			//'post_type'      => [ 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type ] // Default 'post'.
			'post_author'    => 1, // The user ID number of the author. Default is the current user ID.
			'ping_status'    => 'closed', // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
			//'post_parent'    => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
			//'menu_order'     => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
			//'to_ping'        => // Space or carriage return-separated list of URLs to ping. Default empty string.
			//'pinged'         => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
			//'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
			//'guid'           => // Skip this and let Wordpress handle it, usually.
			//'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
			//'post_excerpt'   => [ <string> ] // For all your post excerpt needs.
			//'post_date'      => [ Y-m-d H:i:s ] // The time post was made.
			//'post_date_gmt'  => [ Y-m-d H:i:s ] // The time post was made, in GMT.
			'comment_status' => 'closed', // Default is the option 'default_comment_status', or 'closed'.
			'post_category'  => array( get_cat_ID( $chapters[$par->chapter] ) ), // Default empty.
			'tags_input'     => 'original' // Default empty.
			//'tax_input'      => [ array( <taxonomy> => <array | string> ) ] // For custom taxonomies. Default empty.
			//'page_template'  => [ <string> ] // Default empty.
		);
		
		// iterate through references
		foreach( $par->references as $ref ) {
		
			$references[] = array(
				"link" => $ref->link,
				"quote" => $ref->quote
			);
		}	
		
		// insert post
		$postID = wp_insert_post( $postData );

		// if failed at one
		if( $postID === 0 ) { echo "ERROR POSTING"; exit; }

		// add each reference as a custom field array
		foreach($references as $i => $ref) {
			add_post_meta($postID, 'reference-'.($i+1), $ref, true);
		}

		// clear reference array
		$references = [];
		
	}
	
	echo "IMPORTED ".$postCount." POSTS.";	
	exit;
}

function removeMyPosts() {

	$posts = get_posts("author=1&numberposts=-1");
	
	foreach($posts as $post) {
		$postCount++;
	
		$return = wp_delete_post($post->ID, true);
		
		if($return === false) {
			echo "ERROR DELETING";
			exit;
		}
	}
	
	echo "DELETED ".$postCount." ENTRIES.\n\n";
}

















?>