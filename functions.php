<?php


/********************************************************************/
/*	INITIALISING SCRIPTS											*/
/********************************************************************/

add_action( 'wp_enqueue_scripts', 'enqueue_all_scripts' );
function enqueue_all_scripts() {

	// this moves jquery loader to footer
	//wp_enqueue_script('jquery','/wp-includes/js/jquery/jquery.js','','',true);

	// adds built in dependencies
	$dependencies = array('jquery','jquery-ui-core','jquery-ui-sortable','jquery-effects-core','underscore');

	// loads main script
	wp_enqueue_script('main-script', get_bloginfo('template_url').'/js/main.js', $dependencies, '', true );

	// adds ajax object for ajax requests
	wp_localize_script( 'main-script', 'ajax_object', array( 'ajax_url' => admin_url('admin-ajax.php'), 'nonce' => wp_create_nonce('ajax-nonce') ) );
}

/********************************************************************/
/*	DISABLE ADMIN BAR												*/
/********************************************************************/

remove_action( 'wp_footer', 'wp_admin_bar_render', 1000 );
function remove_admin_bar_style_frontend() {
  echo '<style type="text/css" media="screen">
  html { margin-top: 0px !important; }
  * html body { margin-top: 0px !important; }
  </style>';
}
add_filter('wp_head','remove_admin_bar_style_frontend', 99);


/********************************************************************/
/*	RSS FEED														*/
/********************************************************************/

function create_referencefeed() {
	load_template( get_template_directory() . '/feed-references-rss2.php'); // You'll create a your-custom-feed.php file in your theme's directory
}
add_action('do_feed_references', 'create_referencefeed', 10, 1); // Make sure to have 'do_feed_customfeed'

function create_referencefeedtw() {
	load_template( get_template_directory() . '/feed-referencestw-rss2.php'); // You'll create a your-custom-feed.php file in your theme's directory
}
add_action('do_feed_referencestw', 'create_referencefeedtw', 10, 1); // Make sure to have 'do_feed_customfeed'

add_filter('transient_rewrite_rules','custom_feed_rewrite_rule');
add_filter('rewrite_rules_array','custom_feed_rewrite_rule');

function custom_feed_rewrite_rule($rules){

	$feed_rules = array(
		'feed/references' => 'index.php?feed=references',
		'references' => 'index.php?feed=references',
		'references.xml' => 'index.php?feed=references'
	);
	
	$rules = $feed_rules + $rules;
	return $rules;
}


/********************************************************************/
/*	MANAGING NEW CONTENT											*/
/********************************************************************/

add_action( 'wp_ajax_define_this', 'define_this' );
add_action( 'wp_ajax_nopriv_define_this', 'define_this' );

function define_this() {

	global $wpdb; // this is how you get access to the database

	// check for nonce security
	$nonce = $_POST['nonce'];

	if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
		die ( 'Busted!');

	// get the posted definition
	$definition = sanitize_text_field( $_POST["definition"] );
	
	// if there is one
	if(!empty($definition)) {

		// get all definitions
		$introID = get_page_by_title("Introduction")->ID;
		$allDefinitions = get_post_meta( $introID, "definitions", true );
		
		// add a new one
		$allDefinitions[] = $definition;
		//$allDefinitions = array_values(array_unique($allDefinitions));
		
		//$allDefinitions = [];
		
		// save it
		$return = update_post_meta( $introID, "definitions", $allDefinitions );

		// return true, false or _____
		echo json_encode( $return );
	}
	
	die(); // this is required to return a proper result
}


function formatParagraphHTML($data) {

	// count times published
	$publishedCount = $data["publishedCount"];

	$publishCountTitle = "Published in ".$publishedCount." edition";

	// if plural
	if($publishedCount > 1)
		$publishCountTitle .= "s";


	return	'<li id="paragraph-'.$data["id"].'" class="pure-g paragraph">

			<div class="pure-u-1-12 paragraph-num system"><a href="#select">#'.$data["id"].'</a></div>
			<div class="pure-u-1-6"></div>

			<p class="pure-u-5-12 hyphenate">'.str_replace("<p>", "", str_replace("</p>", "", $data["content"]) ).'</p>

			<div class="pure-u-1-4 collection-count"><span class="system" title="'.$publishCountTitle.'.">('.$publishedCount.'x)<span></div>

			<ul class="pure-u-1-12 more system">
				<li class="move"><span>&equiv; </span><a href="">MOVE</a></li>
				<li class="share"><span>&infin; </span><a href="">SHARE</a></li>
				<li class="link"><form><input type="text" value="'.$data["link"].'"></form></li>
			</ul>

		</li>';
}

add_action( 'wp_ajax_insert_random_paragraph', 'insert_random_paragraph_callback' );
add_action( 'wp_ajax_nopriv_insert_random_paragraph', 'insert_random_paragraph_callback' );

function insert_random_paragraph_callback() {

	global $wpdb; // this is how you get access to the database

	// check for nonce security
	$nonce = $_POST['nonce'];

	if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
		die ( 'Busted!');

	$exclude = [];

	foreach( $_POST['collection'] as $titleID ) {

		$exclude[] = get_page_by_title($titleID, OBJECT, 'post')->ID;
	}

	$post = get_posts( array(

		"cat" => $_POST['catID'],
		"post_status" => "publish",
		"orderby" => "rand",
		"posts_per_page" => 1,
		"post__not_in" => $exclude

	))[0];

	if( empty($post) ) {
		echo "ERROR FETCHING";
		exit;
	}
	else {

		$data["id"] = $post->post_title;
		$data["content"] = do_shortcode($post->post_content);
		$data["publishCount"] = count( get_the_tags($post->ID) );
		$data["link"] = get_paragraph_permalink( $post->post_title, $_POST['catID'] );

		echo formatParagraphHTML($data);
	}

	die(); // this is required to return a proper result
}

add_action( 'wp_ajax_delete_paragraph', 'delete_paragraph_callback' );
add_action( 'wp_ajax_nopriv_delete_paragraph', 'delete_paragraph_callback' );

function delete_paragraph_callback() {

	global $wpdb; // this is how you get access to the database
	
	// check for nonce security
	$nonce = $_POST['nonce'];

	if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
		die ( 'Busted!');

	$return = wp_delete_post( get_page_by_title($_POST['paragraphID'], OBJECT, 'post')->ID, true );

	if($return === false) {
		echo "ERROR DELETING";
		exit;
	}

	die(); // this is required to return a proper result
}

function save_references( $references, $postID ) {

	// add each reference as a custom field array
	foreach($references as $i => $ref) {
		add_post_meta($postID, 'reference-'.($i+1), $ref, true);
	}
}

function contribute() {

	//Get the submitted form
	ob_start();
	require_once($_POST["rootpath"]);

	$paragraph = "<p>".sanitize_text_field( $_POST["paragraph"] )."</p>";
	$paragraphIDTitle = wp_count_posts('post')->publish + 1;	// paragraph IDs are gathered from post titles in order to have them humanly readable
	
	$chapter = $_POST["chapter"];
	//$edition = $_POST["edition"];
	$references = $_POST["references"];
	
	// delete unused (legacy) posted references
	foreach($references as $i => $ref) {

		if($i >= substr_count($paragraph, "[reference id=")) {
			unset($references[$i]);
		}
	}

	$http_referer = $_POST["_wp_http_referer"];
	$path = $_POST["rootpath"];
	$nonce = $_POST["_wpnonce"];

	//Load WordPress
	require($path);

	//Verify the form fields
	if (! wp_verify_nonce($nonce) ) die('Security check');
	
		if( !isset($_POST["paragraph"]) ) {
		
			wp_redirect( bloginfo('url') . $http_referer );	
		}
		else {
				
			// just in case to avoid duplicate titles (hope it works)
			while( get_page_by_title($paragraphIDTitle, OBJECT, 'post') !== NULL ) {
	
				//
				$paragraphIDTitle++; //= wp_count_posts('post')+1;
			}
	
			// post Properties
			$new_post = array(
				'post_title'	=>	$paragraphIDTitle,
				'post_content'  =>	$paragraph,
				'post_category' =>	array($chapter),	// Usable for custom taxonomies too
				//'tags_input'	 => array($edition),
				'post_status' 	=>	'publish',			  // Choose: publish, preview, future, draft, etc.
				'post_type'		=>	'post',	//'post',page' or use a custom post type if you want to
				'post_author'	=>	2 //Author ID
			);
	
			//save the new post
			$postID = wp_insert_post($new_post);
	
			//save reference info
			save_references( $references, $postID );
	
	
			if( isset($postID) ) {
		
				// mark paragraph as collected
				updateCollection( strval($paragraphIDTitle) );
				wp_redirect("http://" . $_SERVER["HTTP_HOST"].$http_referer . "#paragraph-" . $paragraphIDTitle);
			}
			else {
				echo "ERROR SUBMITTING!";
			}
		}
}

/*
function removeUnusedParagraphs() {

	$paragraphIDs = get_paragraphIDs( $_COOKIE["myCollection"] );

	$queryParams = array(
		'nopaging' => true,
		'tag_id' => array(),
		'post__in' => $paragraphIDs,
	);

	$unusedPosts = get_posts($queryParams);

	foreach($unusedPosts as $par) {

		if( !get_the_tags($par->ID) ) {
			// remove from db
			wp_delete_post( $par->ID );
		}
	}
}
*/


/********************************************************************/
/*	PARSING CONTENT FOR DISPLAY										*/
/********************************************************************/

// parse [reference-X] shortcode
function add_reference_to_post( $atts ) {

	global $post;

	extract( shortcode_atts(
		array(
			'id' => '',
			'post_id' => $post->ID,
		), $atts )
	);

	//get custom field array
	$data = get_post_meta($post_id, "reference-".$id, true);

/* 	$decoded = '<span><a href="'.htmlentities($data["link"]).'" class="ref-link system" target="_blank">&#9758;</a></span>'; */

	if( !empty($data["quote"]) ) {
		$decoded = '<q><a href="'.htmlentities($data["link"]).'" class="ref-link" target="_blank">'.urldecode($data["quote"]).'</a></q>';
	}
	else {
		$decoded = '<a href="'.htmlentities($data["link"]).'" class="ref-link" target="_blank">&#9758;</a>';
	}

/* 	$decoded = '<a href="'.htmlentities($data["link"]).'" class="ref-link" target="_blank">'.$content.'</a>'; */

	return $decoded;
}
add_shortcode( 'reference', 'add_reference_to_post' );


/********************************************************************/
/*	EDITION METADATA												*/
/********************************************************************/

//add extra fields to category edit form hook
add_action ( 'edit_tag_form_fields', 'extra_tag_fields');

//add extra fields to category edit form callback function
function extra_tag_fields( $tag ) {	   //check for existing featured ID
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
		
		//return json_encode($tag_meta);
		
		//save the option array
		return update_option( "post_tag_$t_id", $tag_meta );
	}
}


/********************************************************************/
/*	PUBLISHING EDITIONS												*/
/********************************************************************/

function publish() {

	// get the submitted form
	ob_start();
	require_once($_POST["rootpath"]);

	$editionTitle = sanitize_text_field( $_POST["editionTitle"] );
	$data["author"] = sanitize_user( $_POST["author"] );
	$data["email"] = sanitize_email( $_POST["email"] );
	$data["sort"] = stripslashes( $_COOKIE["myCollection"] ); // REMOVE STRIPSLASHES (IF IT BREAKS THE CODE)
	$data["timestamp"] = time();
	$data["votes"] = array();
	$path = $_POST["rootpath"];
	$nonce = $_POST["_wpnonce"];
	
	$referer = $_POST["_wp_http_referer"];

	$paragraphIDs = get_paragraphIDs($_COOKIE["myCollection"]);

	//Load WordPress
	require($path);

	//Verify the form fields
	if (! wp_verify_nonce($nonce) ) die('Security check');
	
		// if edition by the same name exists OR matches reserved titles OR email is invalid
		if( get_term_by('name', $editionTitle, 'post_tag') !== false || $editionTitle === "-1" || $editionTitle === "My collection" || ( !empty($data["email"]) && !is_email($data["email"]) ) ) {
			
			// return an error
			wp_redirect( add_query_arg( array( "error" => "1" ), bloginfo('url').$referer."#collection-info") );
		}
		else {
		
			// for each paragraph in edition
			foreach( $paragraphIDs as $parID ) {
			
				// set that edition
				wp_set_post_tags($parID, $editionTitle, true);
			}

			// save edition info
			$edition = get_term_by('slug', sanitize_title( $editionTitle ), 'post_tag');			
			save_extra_post_tag_fields( $edition->term_id, $data );

			// remove cookie
			removeCollection();

			wp_redirect(bloginfo('url') . "?edition=" . $edition->slug);
		}
}

add_action('wp_ajax_nopriv_validate_edition', 'validate_edition');
add_action('wp_ajax_validate_edition', 'validate_edition');

function validate_edition() {
	
	global $wpdb;
	
	// check for nonce security
	$nonce = $_POST['nonce'];

	if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
		die ( 'Busted!');
	
	$editionTitle = $_POST["editionTitle"];

	// if edition by the same name exists or matches reserved titles
	if( get_term_by('name', $editionTitle, 'post_tag') !== false || $editionTitle === "-1" || $editionTitle === "My collection" ) {
		
		// return true
		echo json_encode(true);
	}
	else {
		
		// return false
		echo json_encode(false);
	}
	
	die;
}


/********************************************************************/
/*	VOTING															*/
/********************************************************************/

function removeVotedCookies() {

	if(isset($_COOKIE["myVotedIDs"])) {

		unset( $_COOKIE["myVotedIDs"] );
		setcookie("myVotedIDs", null, time()-3600, "/");
	}

	if(isset($_COOKIE["myVotingIPs"])) {

		unset( $_COOKIE["myVotingIPs"] );
		setcookie("myVotingIPs", null, time()-3600, "/");
	}
}

function addVote($ip, $editionID, $voteData) {
	
	$votedIDs = json_decode( stripslashes($_COOKIE["myVotedIDs"]), true );
	$votedIDs[] = strval($editionID);
	
	$votingIPs = json_decode( stripslashes($_COOKIE["myVotingIPs"]), true );
	$votingIPs[] = strval($ip);
	$votingIPs = array_values(array_unique($votingIPs));

	removeVotedCookies();
	setcookie("myVotedIDs", json_encode($votedIDs), time()+(10 * 365 * 24 * 60 * 60), "/");
	setcookie("myVotingIPs", json_encode($votingIPs), time()+(10 * 365 * 24 * 60 * 60), "/");

	$voteData[] = $ip;
	
	return array_values($voteData);
}

function removeVote($ip, $editionID, $voteData) {
	
	$votedIDs = json_decode( stripslashes($_COOKIE["myVotedIDs"]), true );
	$votingIPs = json_decode( stripslashes($_COOKIE["myVotingIPs"]), true );

	if( ($key = array_search($editionID, $votedIDs) ) !== false) {
    	unset($votedIDs[$key]);
	}

	if( empty($votedIDs) && in_array($ip, $votingIPs) ) {
    	$votingIPs = [];
	}

	if( ($key = array_search($ip, $voteData) ) !== false) {
    	unset($voteData[$key]);
	}
	
	removeVotedCookies();
	setcookie("myVotedIDs", json_encode(array_values($votedIDs)), time()+(10 * 365 * 24 * 60 * 60), "/");
	setcookie("myVotingIPs", json_encode(array_values($votingIPs)), time()+(10 * 365 * 24 * 60 * 60), "/");
	
	return array_values($voteData);
}

function check_if_voted($currentVoterIP, $editionID, $voteData) {
	
	$voterCheck = array();
	
	// get voted IDs
	$myVotedIDs = json_decode( stripslashes( $_COOKIE["myVotedIDs"] ) );
	$editionID = strval($editionID);
	
	// get voting IPs
	$votingIPs = json_decode( stripslashes( $_COOKIE["myVotingIPs"] ) );
	$votingIPs[] = $currentVoterIP;
	$votingIPs = array_values(array_unique($votingIPs));
	
	// go through all voter's IPs
	foreach($votingIPs as $ip) {
		
		// if it matches one that already voted
		if( in_array( $ip, $voteData ) ) {
			
			// store it
			$voterCheck[] = $ip;
		}
	}

	// if voting IPs and voted IDs are matching
	if( !empty($voterCheck) && in_array( $editionID, $myVotedIDs ) ) {

		// return voting IPs (as voted)
		return $voterCheck;
	} else {
	
		// return as not voted
		return false;
	}
}

add_action('wp_ajax_nopriv_vote_edition', 'vote_edition');
add_action('wp_ajax_vote_edition', 'vote_edition');

function vote_edition() {
	
	// check for nonce security
	$nonce = $_POST['nonce'];

	if ( ! wp_verify_nonce( $nonce, 'ajax-nonce' ) )
		die ( 'Busted!');
	
	// get edition
	$editionID = $_POST['tag_id'];

	// get edition data
	$editionData = get_edition_data($editionID);

	// get current IP
	$currentVoterIP = get_client_ip();

	// check if already voted
	$voterCheck = check_if_voted($currentVoterIP, $editionID, $editionData["votes"]);
	
	// if not yet voted
	if( empty($voterCheck) ) {
	
		// add new vote
		$editionData["votes"] = addVote($currentVoterIP, $editionID, $editionData["votes"]);
	}
	// if already voted
	else {
	
		// remove vote
		$editionData["votes"] = removeVote($currentVoterIP, $editionID, $editionData["votes"]);
	}
	
	//$editionData["votes"] = [];
	// save new data	
	save_extra_post_tag_fields( $editionID, $editionData );

	// get votes count for current edition
	$count = count( $editionData["votes"] );
	
	// display count (ie jQuery return value)
	echo $count;
	
	die();
}

/*
function hasAlreadyVoted($post_id) 
{
	$timebeforerevote = 120; // in minutes

	// Retrieve post votes IPs
	$meta_IP = get_post_meta($post_id, "voted_IP");
	$voted_IP = $meta_IP[0];

	if(!is_array($voted_IP))
		$voted_IP = array();

	// Retrieve current user IP
	$ip = $_SERVER['REMOTE_ADDR'];

	// If user has already voted
	if(in_array($ip, array_keys($voted_IP)))
	{
		$time = $voted_IP[$ip];
		$now = time();

		// Compare between current time and vote time
		if(round(($now - $time) / 60) > $timebeforerevote)
			return false;

		return true;
	}

	return false;
}
*/


/********************************************************************/
/*	FREQUENTLY USED SUPPORT FUNCTIONS								*/
/********************************************************************/

function get_client_ip() {
     $ipaddress = '';
     if ($_SERVER['HTTP_CLIENT_IP'])
         $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
     else if($_SERVER['HTTP_X_FORWARDED_FOR'])
         $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
     else if($_SERVER['HTTP_X_FORWARDED'])
         $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
     else if($_SERVER['HTTP_FORWARDED_FOR'])
         $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
     else if($_SERVER['HTTP_FORWARDED'])
         $ipaddress = $_SERVER['HTTP_FORWARDED'];
     else if($_SERVER['REMOTE_ADDR'])
         $ipaddress = $_SERVER['REMOTE_ADDR'];
     else
         $ipaddress = 'UNKNOWN';

     return $ipaddress; 
}

function get_all_editions_sorted() {
	
	$allEditions = get_tags('exclude='.get_term_by('slug','original','post_tag')->term_id);
	
	// resort editions according to votes
	foreach($allEditions as $oneEdition) {
		$votes[] = count( get_edition_data($oneEdition->term_id)["votes"] );
	}

	arsort($votes);

	foreach(array_keys($votes) as $i) $out[$i] = $allEditions[$i];

	return $out;

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

	wp_redirect(get_edition_URL($parameter, $url));
}

function get_paragraphIDs($cookieData) {

	$paragraphIDTitles = json_decode( stripslashes($cookieData) );
	$paragraphIDs = [];
	
	foreach( $paragraphIDTitles as $title ) {

		$paragraphIDs[] = get_page_by_title( $title, OBJECT, 'post' )->ID;
	}

	return $paragraphIDs;
}

function get_paragraph_permalink( $titleID, $categoryID, $edition ) {

	$link = get_category_link( $categoryID );

	if($edition && $edition !== "-1") {
		$link = add_query_arg( array( "edition" => $edition->slug ), $link );
	}
	else {
		$link = add_query_arg( array( "clear-edition" => 1 ), $link );
	}

	$link .= "#paragraph-" . $titleID;

	return $link;

};

function get_edition_data($editionID) {

	return get_option('post_tag_'.$editionID);
}

function check_for_posts($chapterID, $paragraphIDs, $editionSlug) {
	
	$postquery = [];
	
	$postQuery['category'] = $chapterID;
	$postQuery['nopaging'] = true;
	if($editionSlug === "-1") { $postQuery['post__in'] = $paragraphIDs; }
	else if($editionSlug) { $postQuery['tag'] = $editionSlug; }
		
	return get_posts($postQuery);
}

function find_first_available_chapter($currentChapterID) {
	
	global $edition;
	global $chapters;
	global $paragraphIDs;
		
	// if current chapter not provided
	if( empty($currentChapterID) ) {
	
		// set it as first chapter
		$currentChapterID = intval( $chapters[0]->term_id );
		
		// and check it too
		$checkforposts = check_for_posts( $chapters[$i+$j]->cat_ID, $paragraphIDs, $edition->slug );

		if( !empty($checkforposts) ) {
			
			return 0;
		}
	}
	
	// go through all chapters
	foreach( $chapters as $i => $chapter ) {
			
		// find which chapter is current
		if( intval($chapter->term_id) === $currentChapterID ) {
		
			// go through all following chapters
			for($j=1; $chapters[$i+$j]; $j++) {
				
				// check if it has content
				$checkforposts = check_for_posts( $chapters[$i+$j]->cat_ID, $paragraphIDs, $edition->slug );

				// if it has content
				if( !empty($checkforposts) ) {
					
					// return appropriate array key
					return $i+$j;
					break;
				}
			}
			break;
		}
	}
	
	return false;
}

function get_volume($single, $edition) {
	
	// $single = $chapter in $edition

	return count($single)/count($edition);
}

function get_activity($chapterID) {

	//$chapter / sum(count in each tag)
	
	$chapter = count( get_posts("nopaging=true&category__in=".$chapterID) );
	$book = 0;

	$editions = get_tags();
	
	foreach($editions as $edition) {
		
		$book = $book + $edition->count;
	}

	return $chapter/$book;
	//return $book;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function map($x, $in_min, $in_max, $out_min, $out_max) {

	return ($x - $in_min) * ($out_max - $out_min) / ($in_max - $in_min) + $out_min;
}


/********************************************************************/
/*	OUTPUTING PDF													*/
/********************************************************************/

function generate_PDF($editionSlug, $chapters) {

	$pdfPath = str_replace(get_bloginfo("url")."/", "", content_url("/editions/".$editionSlug.".pdf"));
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

		$title = get_term_by("slug", $editionSlug, "post_tag")->name;
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
			$mpdf->WriteHTML('<div id="edition"><h3 class="title">My Collection</h3><p class="info system">'.date('F j, Y').'</p></div>');
			$paragraphIDs = get_paragraphIDs( $_COOKIE["myCollection"] );
		}
		// if edition
		else {
			// write info
			$mpdf->WriteHTML('<div id="edition"><h4 class="system edition">Edition:</h4><h3 class="title">'.$title.'</h3><p class="info system">by '.$author.' on '.date('F j, Y', $timestamp).'</p><p class="link">'.get_bloginfo('url').'/?edition='.$editionSlug.'</p></div>');
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
					$output = str_replace("</p>", "<span> &para;". $paragraph->post_title ."</span></p>", $output );
					// execute shortcodes
					$output = do_shortcode( $output );
					// add quotation marks (fallback for unsupported CSS)
					$output = str_replace('<q>', '<q>&raquo;', $output);
					$output = str_replace('</q>', '&laquo;</q>', $output);
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
		2 => "Language in/as any&nbsp;form",
		3 => "Recontextualisation",
		4 => "Focus",
		5 => "Ambiguity",
		6 => "Uniqueness and hybridity&nbsp;of&nbsp;media"

	);
	
	// add all chapters (categories)
	foreach($chapters as $name) {
		
		if( get_cat_ID($name) === 0 ) {
			
			wp_insert_term($name, "category", array('description' => ""));
		}
	}

	removeMyPosts();

	foreach($allPosts as $par) {

		$postCount++;
		$sortArray[] = strval($par->id);

		$checkExisting = get_page_by_title( $par->id, OBJECT, 'post' );

		$postData = array(
			//'ID'			  => [ <post id> ] // Are you updating an existing post?
			'post_content'	=> $par->content, // The full text of the post.
			//'post_name'	  => [ <string> ] // The name (slug) for your post
			'post_title'		=> $par->id, // The title of your post.
			'post_status'	=> 'publish', // Default 'draft'.
			//'post_type'	  => [ 'post' | 'page' | 'link' | 'nav_menu_item' | custom post type ] // Default 'post'.
			'post_author'	=> 1, // The user ID number of the author. Default is the current user ID.
			'ping_status'	=> 'closed', // Pingbacks or trackbacks allowed. Default is the option 'default_ping_status'.
			//'post_parent'	  => [ <post ID> ] // Sets the parent of the new post, if any. Default 0.
			//'menu_order'	  => [ <order> ] // If new post is a page, sets the order in which it should appear in supported menus. Default 0.
			//'to_ping'		  => // Space or carriage return-separated list of URLs to ping. Default empty string.
			//'pinged'		  => // Space or carriage return-separated list of URLs that have been pinged. Default empty string.
			//'post_password'  => [ <string> ] // Password for post, if any. Default empty string.
			//'guid'			  => // Skip this and let Wordpress handle it, usually.
			//'post_content_filtered' => // Skip this and let Wordpress handle it, usually.
			//'post_excerpt'	  => [ <string> ] // For all your post excerpt needs.
			//'post_date'	  => [ Y-m-d H:i:s ] // The time post was made.
			//'post_date_gmt'  => [ Y-m-d H:i:s ] // The time post was made, in GMT.
			'comment_status' => 'closed', // Default is the option 'default_comment_status', or 'closed'.
			'post_category'	=> array( get_cat_ID( $chapters[$par->chapter] ) ), // Default empty.
			'tags_input'		=> 'Original' //, Default empty.
			//'tax_input'	  => [ array( <taxonomy> => <array | string> ) ] // For custom taxonomies. Default empty.
			//'page_template'  => [ <string> ] // Default empty.
		);

		// iterate through references
		foreach( $par->references as $ref ) {

			$references[] = array(
				"link" => $ref->link,
				"quote" => urlencode($ref->quote)
			);
		}

		// TODO: if new post

			// insert post
			$postID = wp_insert_post( $postData );

			// if failed at one
			if( $postID === 0 ) { echo "ERROR POSTING"; exit; }

			save_references( $references, $postID );

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
		
/*
		// make 1s gaps between imports to have different timestamp
		echo "Imported ".$postCount.". Sleeping 1s...\n";
		sleep(1);

*/
	}

	// save edition info
	$edition = get_term_by('name', 'Original', 'post_tag');
	$editionData = array(
		"author" => "Jure Martinec",
		"email" => "jure.martinec@gmail.com",
		"sort" => json_encode($sortArray),
		"timestamp" => time(),
		"votes" => array()

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