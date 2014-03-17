<?php

add_action( 'wp_enqueue_scripts', 'enqueue_all_scripts' );
function enqueue_all_scripts() {
	
	// this moves jquery loader to footer
	//wp_enqueue_script('jquery','/wp-includes/js/jquery/jquery.js','','',true);

	// adds built in dependencies
	$dependencies = array('jquery','jquery-ui-core','jquery-ui-sortable','jquery-ui-tabs','underscore');
	
	// loads main script
	wp_enqueue_script('main-script', get_bloginfo('template_url').'/js/main.js', $dependencies, '', true );	

	// adds ajax object for ajax requests
	wp_localize_script( 'main-script', 'ajax_object', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );
}

// disable admin bar
remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
function remove_admin_bar_style_frontend() { 
  echo '<style type="text/css" media="screen">
  html { margin-top: 0px !important; }
  * html body { margin-top: 0px !important; }
  </style>';
}
add_filter('wp_head','remove_admin_bar_style_frontend', 99);





//add extra fields to category edit form hook
add_action ( 'edit_tag_form_fields', 'extra_tag_fields');

//add extra fields to category edit form callback function
function extra_tag_fields( $tag ) {    //check for existing featured ID
    $t_id = $tag->term_id;
    $tag_meta = get_option( "post_tag_$t_id");
?>

<tr class="form-field">
	<th scope="row" valign="top"><label for="author"><?php _e('Author'); ?></label></th>
	<td>
		<input type="text" name="tag_meta[author]" id="tag_meta[author]" size="25" style="width:60%;" value="<?php echo $tag_meta['author'] ? $tag_meta['author'] : ''; ?>">
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="email"><?php _e('E-mail'); ?></label></th>
	<td>
		<input type="text" name="tag_meta[email]" id="tag_meta[email]" size="25" style="width:60%;" value="<?php echo $tag_meta['email'] ? $tag_meta['email'] : ''; ?>">
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="sort"><?php _e('Sort order string'); ?></label></th>
	<td>
		<input type="text" name="tag_meta[sort]" id="tag_meta[sort]" size="25" style="width:60%;" value="<?php echo $tag_meta['sort'] ? $tag_meta['sort'] : ''; ?>">
	</td>
</tr>
<tr class="form-field">
	<th scope="row" valign="top"><label for="timestamp"><?php _e('Timestamp'); ?></label></th>
	<td>
		<input type="text" name="tag_meta[timestamp]" id="tag_meta[timestamp]" size="25" style="width:60%;" value="<?php echo $tag_meta['timestamp'] ? $tag_meta['timestamp'] : ''; ?>">
	</td>
</tr>
<?php
}

// save extra post_tag extra fields hook
add_action ( 'edited_post_tag', 'save_extra_post_tag_fields');
//add_action ( 'create_post_tag', 'save_extra_post_tag_fields');

  // save extra post_tag extra fields callback function
function save_extra_post_tag_fields( $term_id, $data ) {

	if(!isset($data)) { $data = $_POST['tag_meta']; }

    if ( isset($data) ) {
        $t_id = $term_id;
        $tag_meta = get_option( "post_tag_$t_id" );
        $tag_keys = array_keys($data);
            foreach ($tag_keys as $key){
            if (isset($data[$key])){
                $tag_meta[$key] = $data[$key];
            }
        }
        //save the option array
        update_option( "post_tag_$t_id", $tag_meta );
    }
}

function removeCollection() {

	if(isset($_COOKIE["myCollection"])) {

		unset( $_COOKIE["myCollection"] );
		setcookie("myCollection", null, time()-3600, "/");
	}
}

function updateCollection($paragraphIDTitle) {

	$collection = json_decode( stripslashes($_COOKIE["myCollection"]), true );
	$collection[] = strval($paragraphIDTitle);

	removeCollection();
	setcookie("myCollection", json_encode($collection), time()+3650, "/");

}

function get_edition_URL($parameter, $url) {

	return add_query_arg( array("edition" => $parameter), $url );
}

function set_edition($parameter, $url) {
	
	Header("Location: " . get_edition_URL($parameter, $url));
	exit;
}

function get_paragraphIDs($cookieData) {

	$paragraphIDTitles = json_decode( stripslashes($cookieData) );
	$paragraphIDs = [];
	
	foreach( $paragraphIDTitles as $title ) {
		
		$paragraphIDs[] = get_page_by_title( $title, OBJECT, 'post' )->ID;
	}

	return $paragraphIDs;
}

// Add [reference-X] shortcode
function add_reference_to_post( $atts ) {

	global $post;

	extract( shortcode_atts(
		array(
			'id' => '',
			'post_id' => $post->ID,
		), $atts )
	);
	
	//get custom field array
	$data = get_post_meta($post_id, "reference-".$id, false);
		
	$return = '<sup><a href="'.$data[0]["link"].'" class="ref-link system" target="_blank">[&rarr;]</a></sup>';
	
	if( !empty($data[0]["quote"]) ) {
		$return = '<q>'.$data[0]["quote"].'</q>'.$return;
	}
	
	return $return;
}
add_shortcode( 'reference', 'add_reference_to_post' );

function generate_PDF($editionSlug, $chapters) {

	$pdfPath = "wp-content/editions/".$editionSlug.".pdf";
	$niceName = "Post-digital Reader (".get_term_by('slug', $editionSlug, 'post_tag')->name.").pdf";

	// IF PDF ALREDY EXISTS
	if( filesize($pdfPath) !== false ) {
	
		// fetch from wp-content
		header('Content-Description: File Transfer');
		header('Content-Type: application/pdf');
		header('Content-Length: ' . filesize($pdfPath));
		// to open in browser
		header('Content-Disposition: inline; filename=' . basename($niceName));
		readfile($pdfPath);
		
	// IF PDF NOT YET GENERATED
	} else {

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
		
		$editionData = get_edition_data( get_term_by("slug", $editionSlug, "post_tag")->term_id );
		
		$title = $editionData["editionTitle"];
		$author = $editionData["author"];
		//$email = $editionData["email"];
		$sortString = $editionData["sort"];
		$timestamp = $editionData["timestamp"];
		
		if(empty($author)) {
			
			$author = "Anonymous";
		}
		
		// set document		
		$mpdf = new mPDF('utf-8');
		$mpdf->WriteHTML($stylesheet,1);
		
		// write cover page
		$mpdf->WriteHTML('<h1><span class="serif">P05T-D16174L</span><br> READER.<br> The <i class="strikethrough serif">form</i> role <br>of books in the <br>digital <i class="strikethrough serif">media</i> age</h1>');
		
		// if my collection
		if( $editionSlug == "-1" ) {
			// write info
			$mpdf->WriteHTML('<div id="edition"><h3 class="title">My Collection</h3><p class="info system">'.date('d F Y').'</p></div>');
			$paragraphIDs = get_paragraphIDs( $_COOKIE["myCollection"] );
		}
		// if edition
		else {
			// write info
			$mpdf->WriteHTML('<div id="edition"><h4 class="system edition">Edition:</h4><h3 class="title">'.$title.'</h3><p class="info system">by '.$author.' on '.date('d F Y', $timestamp).'</p><p class="link">'.get_bloginfo('url').'/?edition='.$editionSlug.'</p></div>');
			$paragraphIDs = get_paragraphIDs( $sortString );
		}
		
		// go through all chapters
		foreach($chapters as $chapter) {
	
			// if my collection
			if( $editionSlug === "-1" ) {
					
				$queryParams = array(
					'nopaging' => true,
					'category__in' => array( $chapter->term_id ),
					'post__in' => $paragraphIDs,
					'orderby' => 'post__in'
				);
				$book = get_posts($queryParams);
			}
			// if edition
			else {
			
				$queryParams = array(
					'nopaging' => true,
					'tag' => $editionSlug,
					'category__in' => array( $chapter->term_id ),
					'post__in' => $paragraphIDs,
					'orderby' => 'post__in'
				);
				$book = get_posts($queryParams);
			}
			
			// if there is content
			if(!empty($book)) {
	
				$mpdf->AddPage();
				$mpdf->setFooter($footer);
				$mpdf->WriteHTML('<h3>Design for</h3><h2>'.$chapter->name.'</h2>',2);
	
				foreach($book as $paragraph) {
					
					// get content
					$output = $paragraph->post_content;
					// insert current postID to shortcodes
					$output = str_replace(']', ' post_id='.$paragraph->ID.']', $output);
					// add paragraph number at the end
					$output = str_replace("</p>", "<span> #". $paragraph->post_title ."</span></p>", $output );
					// execute shortcodes
					$output = do_shortcode( $output );
					// add quotation marks (fallback for unsupported CSS)
					$output = str_replace('<q>', '<q>&ldquo;', $output);
					$output = str_replace('</q>', '&rdquo;</q>', $output);
					// write to PDF					
					$mpdf->WriteHTML($output,2);
				}
			}
			// if there is no content
			else {
	
				$mpdf->AddPage();
				$mpdf->setFooter($footer);
				$mpdf->WriteHTML('<h3>Design for</h3><h2 class="strikethrough">'.$chapter->name.'</h2>',2);
			}
		}
	
		// set metadata
		$mpdf->SetTitle("Post-digital Reader (".$title.")");
		$mpdf->SetAuthor($author);
		
		// if my collection
		if($editionSlug === "-1") {
			// show PDF
			$mpdf->Output($pdfPath,"I");
			exit;
		// if a regular edition
		} else {
			// save PDF
			$mpdf->Output($pdfPath,"F");
			// attempt to show it
			generate_PDF($editionSlug, $chapters);		
		}		
	}
}

function get_edition_data($chapterID) {
	
	return get_option('post_tag_'.$chapterID);
}

add_action( 'wp_ajax_delete_paragraph', 'delete_paragraph_callback' );
add_action( 'wp_ajax_nopriv_delete_paragraph', 'delete_paragraph_callback' );

function delete_paragraph_callback() {
	
	global $wpdb; // this is how you get access to the database

	$return = wp_delete_post( get_page_by_title($_POST['paragraphID'], OBJECT, 'post')->ID, true );
	
	if($return === false) {
		echo "ERROR DELETING";
		exit;
	}

	die(); // this is required to return a proper result
}

function contribute() {

	//Get the submitted form
	ob_start();
	require_once($_POST["rootpath"]);
	
	$paragraph = $_POST["paragraph"];
	$paragraphIDTitle = wp_count_posts('post')+1;	// paragraph IDs are gathered from post titles in order to have them humanly readable 
	$chapter = $_POST["chapter"];
	//$edition = $_POST["edition"];
	
	$http_referer = $_POST["_wp_http_referer"];
	$path = $_POST["rootpath"];
	$nonce = $_POST["_wpnonce"];
	
	//Load WordPress
	require($path);
	
	//Verify the form fields
	if (! wp_verify_nonce($nonce) ) die('Security check'); 
		
		// just in case to avoid duplicate titles (hope it works)
		while( get_page_by_title($paragraphIDTitle, OBJECT, 'post') !== NULL ) {
			
			// 
			$paragraphIDTitle++; //= wp_count_posts('post')+1;
		}
		
		// post Properties
		$new_post = array(
			'post_title'	=>	$paragraphIDTitle,
			'post_content'  =>	$paragraph,
			'post_category' =>	array($chapter),  // Usable for custom taxonomies too
			//'tags_input'    => array($edition),
			'post_status' 	=>	'publish',           // Choose: publish, preview, future, draft, etc.
			'post_type'		=>	'post',  //'post',page' or use a custom post type if you want to
			'post_author'	=>	2 //Author ID
		);
		
		//save the new post
		$pid = wp_insert_post($new_post);
	     
	    // Insert Form data into Custom Fields
	/*
	    add_post_meta($pid, 'author', $author, true);
	    add_post_meta($pid, 'author-email', $email, true);
	    add_post_meta($pid, 'author-website', $site, true);
	*/
	
	if( isset($pid) ) {

		// mark paragraph as collected
		updateCollection( strval($paragraphIDTitle) );
		header("Location: http://" . $_SERVER["HTTP_HOST"].$http_referer . "#paragraph-" . $paragraphIDTitle);
	}
	else {
		echo "ERROR SUBMITTING!";
	}
}

function publish() {
	
	// get the submitted form
	ob_start();
	require_once($_POST["rootpath"]);
		
	$data["editionTitle"] = $_POST["editionTitle"];
	$data["author"] = $_POST["author"];
	$data["email"] = $_POST["email"];
	$data["sort"] = $_COOKIE["myCollection"];
	$data["timestamp"] = time(); // TODO: zakaj to posebi, če je tkoaltko shranjen?
	$path = $_POST["rootpath"];
	$nonce = $_POST["_wpnonce"];

	$paragraphIDs = get_paragraphIDs($_COOKIE["myCollection"]);
	
	//Load WordPress
	require($path);
	
	//Verify the form fields
	if (! wp_verify_nonce($nonce) ) die('Security check'); 
	
		// for each paragraph in edition
		foreach( $paragraphIDs as $parID ) {
			// set that edition
			wp_set_post_tags($parID, $data["editionTitle"], true);
		}
		
		// save edition info
		$edition = get_term_by('name', $data["editionTitle"], 'post_tag');
		save_extra_post_tag_fields( $edition->term_id, $data);
	
	// remove cookie
	removeCollection();
		
	header("Location: " . bloginfo('url') . "?edition=" . $edition->slug);
	
}

/********************************************/
/*											*/
/*	BATCH IMPORTING AND DELETING CONTENT	*/
/*											*/
/*	Warning: first deletes all your posted	*/
/*			 content and everything			*/
/*			 associated with it	(tags etc.)	*/
/*											*/
/********************************************/

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
		$sortArray[] = strval($par->id);

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
			'tags_input'     => 'original' //, Default empty.
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
		
		// TODO: if new post
		
			// insert post
			$postID = wp_insert_post( $postData );
	
			// if failed at one
			if( $postID === 0 ) { echo "ERROR POSTING"; exit; }
	
			// add each reference as a custom field array
			foreach($references as $i => $ref) {
				add_post_meta($postID, 'reference-'.($i+1), $ref, true);
			}

		// TODO: if existing post
			
/*
			// insert post
			$postID = wp_update_post( $postData );
	
			// if failed at one
			if( $postID === 0 ) { echo "ERROR POSTING"; exit; }
	
			// add each reference as a custom field array
			foreach($references as $i => $ref) {
				update_post_meta($postID, 'reference-'.($i+1), $ref, true);
			}
*/


		// clear reference array
		$references = [];
		
	}
	
	// save edition info
	$edition = get_term_by('name', 'original', 'post_tag');
	$editionData = array(
		"editionTitle" => "Original",
		"author" => "Jure Martinec",
		"email" => "jure.martinec@gmail.com",
		"sort" => rawurlencode( json_encode($sortArray) ),
		"timestamp" => time()

	);
	save_extra_post_tag_fields($edition->term_id, $editionData);
	
	echo "IMPORTED " . $postCount . " POSTS.";	
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