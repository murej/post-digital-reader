<?php

	//importJSON();
	
	global $edition;
	global $editionData;
	global $allEditions;
	global $chapters;
	global $chapterTitle;
	global $paragraphIDs;


	$chapters = get_categories('exclude=1&hide_empty=0'); 
	$chapterTitle = single_cat_title('', false);

	$allEditions = get_all_editions_sorted(); 

	// if PDF requested
	if($_GET['generatePDF']) {
	
		generate_PDF($_GET['edition'], $chapters);
	}
/*
	else if($_POST['clear']) {
		
		removeUnusedParagraphs();
	}
*/
	// if paragraph was written
	else if($_POST['contribute']) {
		
		contribute();
	}
	// if publish requested
	else if($_POST['publish']) {
	
		publish();
	}
	else if( $_GET["clear-edition"] ) {
		
		
	}
	// if my collection requested with already collected items
	else if( $_GET["edition"] === "-1" && isset($_COOKIE["myCollection"]) ) {
	
		// set edition as my collection
		$edition = "-1";
		$paragraphIDs = get_paragraphIDs( $_COOKIE["myCollection"] );
	}
/*
	// if my collection requested with nothing collected
	else if( $_GET["edition"] === "-1" && !isset($_COOKIE["myCollection"]) ) {
	
		// set default edition
		wp_redirect(get_bloginfo("url"));
	}
*/
	else {
		
		// try to get edition
		$edition = get_term_by('slug', $_GET["edition"], 'post_tag');
		
		if($edition) {

			// try to get stored edition data
			$editionData = get_edition_data($edition->term_id);
	
			// get sort order from saved cookie string
			$paragraphIDs = get_paragraphIDs( get_edition_data( $edition->term_id )["sort"] );		
		}
		// show first-edition edition if there is no edition set and it was not explicitly cleared
		else if( $edition === false && !isset( $_COOKIE['clearedEditions'] ) ){
			set_edition("first-edition", $_SERVER['REQUEST_URI']);		
		}
	}
				
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html lang="en" class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html lang="en" class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html lang="en" class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html lang="en" class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php if(isset($chapterTitle)) { echo "Design for " . $chapterTitle . " // "; } ?>Post-Digital Reader<?php if($edition) { echo " (".$edition->name.")"; } ?></title>
        <meta name="description" content="<?php bloginfo('description'); ?>">
        <meta name="viewport" content="width=device-width">

<!-- 		<meta property="og:url" content="<?php echo get_bloginfo("url"); ?>"> -->
		<meta property="og:image" content="<?php bloginfo("template_url"); ?>/img/icon512x512.png">
		<meta property="og:title" content="<?php echo get_bloginfo("name"); ?>">
		<meta property="og:description" content="<?php bloginfo('description'); ?>">

        <link rel="stylesheet" href="<?php echo get_stylesheet_uri() . '?t=' . filemtime( get_stylesheet_directory() . '/style.css' ); ?>">

		<link rel="shortcut icon" href="<?php echo bloginfo("template_url"); ?>/img/favicon.ico">

        <!--[if lt IE 9]>
            <script src="<?php bloginfo('template_url'); ?>/js/vendor/html5-3.6-respond-1.1.0.min.js"></script>
        <![endif]-->

<?php wp_head(); ?>

    </head>
    
    <body class="<?php if( is_home() ) { echo "start cover home"; } ?>">
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
        
<?php if( is_home() ) { ?>

        <div id="cover" class="shadow">
        	
        	<div class="pure-g">
        	
        		<div class="pure-u-1-4"></div>
				<div class="pure-u-5-12">
					
					<h1>
						<span class="serif">P05T-D16174L</span><br>
						READER.<br>
						The <i class="strikethrough serif">form</i> role<br>
						of books in the<br>
						digital <i class="strikethrough serif">media</i> age<br>
						<span class="status">in everlasting beta</span>
					</h1>
					
				</div>
				
        		<div class="pure-u-1-12"></div>
        		<div class="pure-u-1-12"><div class="arrow"></div></div>
        		<div class="pure-u-1-12"></div>
				        	
        	</div>
        	
        </div>

<?php } ?>
        
	<ul id="nav" class="pure-g<?php if( is_home() ) { echo " home"; } ?><?php if($edition === "-1") { echo " mycollection"; } ?>">
        
		<li id="collector" class="pure-u-1-12" title="Collection info">
			<form method="get" action="<?php bloginfo('url'); ?>#collection-info">
			
				<input type="hidden" name="edition" value="<?php echo $_REQUEST["edition"]; ?>">
				<button type="submit"<?php if( is_home() ) { echo " disabled"; } ?>>0</button><span></span>
				
			</form></li>
		<li class="pure-u-1-6 viewing system"><?php
		
		if( !is_home() && $edition === "-1" ) { ?>+<a href="#writer" class="write">New</a> +<a href="#random" class="random">random</a><?php } 
		else if( !is_home() ) { ?><a href="<?php echo get_category_link( get_cat_ID( $chapterTitle ) ); ?>?edition=-1" class="write">Edit/write</a>
		<span href="<?php echo get_category_link( get_cat_ID( $chapterTitle ) ); ?>?edition=-1" class="disabled" title="Collect a paragraph first.">Edit/write</span><?php
		} 
			
		?></li>
<?php if($edition === "-1") { ?>
		<li class="pure-u-1-2">
			
			<h3>My collection</h3>
				
			<ul id="edition-options" class="system">
				<li><a href="<?php bloginfo('url'); ?>?edition=<?php echo $_REQUEST['edition']; ?>#collection-info">publish</a> /</li>
				<li><a href="<?php bloginfo('url'); ?>?generatePDF=1&amp;edition=<?php echo $_REQUEST['edition']; ?>" target="_blank">print</a> /</li>
				<li><a href="#change">select edition</a></li>
<!-- 				<li><a href="<?php if( is_home() ) { bloginfo('url'); } else { echo get_category_link( get_cat_ID( $chapterTitle ) ); } ?>" class="clear-edition">deselect</a></li> -->
			</ul>
				
		</li>
<?php } else if($edition) { 
	
	?>

		<li class="pure-u-1-2">

			<h3 class="edition"><?php echo $edition->name; ?></h3>
				
			<ul id="edition-options" class="system">
				<li><a href="<?php bloginfo('url'); ?>?generatePDF=1&amp;edition=<?php echo $_REQUEST['edition']; ?>" target="_blank">print</a> /</li>
				<li><a href="#change">change edition</a></li>
				<?php 
				
				if($edition->slug !== "first-edition") {
									
					$votedCheck = check_if_voted( get_client_ip(), $editionData["votes"] );
				
					// if not yet voted
					if( !$votedCheck ) {
				?><li id="vote" title="Like this edition."><a href="#vote-<?php echo $edition->term_id; ?>" class="vote">&#10084;</a> <span class="votes"><?php echo count($editionData["votes"]); ?></span><?php
					// if already voted
					} else if( !empty($votedCheck) ) {
				?><li id="vote" class="voted" title="You like it!"><a href="#vote-<?php echo $edition->term_id; ?>" class="vote">&#10084;</a> <span class="votes"><?php echo count($editionData["votes"]); ?></span><?php
					}
					
				?></li><?php
				
				} ?>
				
				
<!-- 				<li><a href="<?php if( is_home() ) { bloginfo('url'); } else { echo get_category_link( get_cat_ID( $chapterTitle ) ); } ?>" class="clear-edition">deselect</a></li> -->
			</ul>
				
		</li>

<?php } else { ?>
		<li class="pure-u-1-2 no-edition">
				
			<h3>Showing all editions</h3>
						
			<ul id="edition-options" class="system">
				<li><a href="#change">change</a></li>
			</ul>
				
		</li>
<?php } ?>
		<li class="pure-u-1-6"></li>
		<li class="pure-u-1-12">
			<h3 id="toc">
				<a href="<?php
					
					$firstNextChapterKey = find_first_available_chapter();
				
					if($edition) {
					
						echo add_query_arg( array("edition" => $_GET["edition"]), bloginfo('url'));
					}
					else { 
					
						echo bloginfo('url');
						
					} 
					
					echo "#ch-".$chapters[$firstNextChapterKey]->term_id;
					
				?>">
					<span class="system">&equiv; </span>TOC
				</a>
			</h3>
		</li>
			        
	</ul>

	<div id="edition-selector" class="pure-g">

		<div class="pure-u-1-4"></div>				
		<div class="pure-u-1-2">
				
			<h3>Published editions</h3>
						
			<ul id="show-all" class="system">
				<li><a href="<?php if( is_home() ) { bloginfo('url'); } else { echo get_category_link( get_cat_ID( $chapterTitle ) ); } ?>?clear-edition=1" class="clear-edition">show all</a></li>
			</ul>
				
		</div>
		<h3 id="close" class="pure-u-1-4"><span>&times;</span></h3>				

		<div class="pure-u-1-4"></div>				
		<div id="first-edition" class="pure-u-1-2">
			<h2><a href="<?php echo get_edition_URL("first-edition", get_bloginfo('url')); ?>">First edition.</a></h2>
			<p class="system">by <a href="http://www.juremartinec.net/">Jure Martinec</a> on February 24, 2014</p>
		</div>		
		<div class="pure-u-1-4"></div>				

<!--
		<ul id="tabs" class="pure-u-1-6">
			<li><a href="#list-all" class="selected">All (<?php echo count($allEditions); ?>)</a></li>
			<li><a href="#list-liked">Liked (0)</a></li>
			<li><a href="#list-my">My (0)</a></li>
		</ul>
-->

		<div class="pure-u-1-12"></div>
        
		<ul id="list-all" class="pure-u-1-2 list">

<?php 	foreach( $allEditions as $oneEdition ) { 
			
			$editionData = get_edition_data($oneEdition->term_id);
						
			if(!empty($editionData["author"])) {
				$author = $editionData["author"];
			}
			else {
				$author = "Anonymous";
			}
			
			if(!empty($editionData["email"])) {
				$author = '<a href="mailto:'.$editionData["email"].'">'.$author.'</a>';
			}
						
			$votedCheck = check_if_voted( get_client_ip(), $editionData["votes"] );
						
?>
			<li>
				<h2><a <?php 
				
					if($oneEdition->name === $edition->name){ echo 'class="highlight" '; }
				
				?>href="<?php echo get_edition_URL($oneEdition->slug, get_bloginfo('url')); ?>"><?php echo $oneEdition->name; ?></a></h2>
				<p class="system">by <?php echo $author; ?> on <?php echo date('F j, Y', $editionData["timestamp"] ); ?></p>
				
<?php
				
				// if not yet voted
				if( !$votedCheck ) {
				
?>				<div class="vote system" title="Like this edition.">
					<a href="#vote-<?php echo $oneEdition->term_id; ?>" class="vote">&#10084;</a> <span class="votes"><?php echo count( $editionData["votes"] ); ?></span>
				</div>
<?php			}	
				// if already voted
				else if( !empty($votedCheck) ) {
				
?>				<div class="vote voted system" title="You like it!">
					<a href="#vote-<?php echo $oneEdition->term_id; ?>" class="vote">&#10084;</a> <span class="votes"><?php echo count( $editionData["votes"] ); ?></span>
				</div>
<?php			}?>
			</li>
<?php	} 
?>		</ul>

<!--
		<ul id="list-liked" class="pure-u-5-12 list">
			<li>liked</li>
		</ul>
		<ul id="list-my" class="pure-u-5-12 list">
			<li>my</li>
		</ul>
-->

	</div>