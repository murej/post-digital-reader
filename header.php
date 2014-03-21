<?php

	//importJSON();
	
	global $edition;
	global $allEditions;
	global $chapters;
	global $chapterTitle;
	global $paragraphIDs;

	$chapters = get_categories('exclude=1&hide_empty=0'); 
	$chapterTitle = single_cat_title('', false);
	
	$allEditions = get_tags('exclude='.get_term_by('slug','first-edition','post_tag')->term_id);

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
	
		// get sort order from saved cookie string
		$paragraphIDs = get_paragraphIDs( get_edition_data( $edition->term_id )["sort"] );

		// show first-edition edition if there is no edition set and it was not explicitly cleared
		if( $edition === false && !isset( $_COOKIE['clearedEditions'] ) ){
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
        <title><?php if(isset($chapterTitle)) { echo "Design for " . $chapterTitle . " &infin; "; } ?>Post-Digital Reader</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">

        <link rel="stylesheet" href="<?php echo get_stylesheet_uri() . '?t=' . filemtime( get_stylesheet_directory() . '/style.css' ); ?>">

        <!--[if lt IE 9]>
            <script src="<?php bloginfo('template_url'); ?>/js/vendor/html5-3.6-respond-1.1.0.min.js"></script>
        <![endif]-->

<?php wp_head(); ?>

    </head>
    
    <body class="<?php if( is_home() ) { echo "start cover"; } ?>">
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
        
<?php if( is_home() ) { ?>

        <div id="cover" class="shadow">
        	
        	<div class="pure-g">
        	
        		<div class="pure-u-1-4"></div>
				<div class="pure-u-1-2">
					
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
        
		<li id="collector" class="pure-u-1-12" title="Collection info"><form method="post" action="<?php bloginfo('url'); ?>?edition=-1#collection-info"><button type="submit"<?php if( is_home() ) { echo " disabled"; } ?>>0</button><span></span></form></li>
		<li class="pure-u-1-6 viewing system"><?php if( !is_home() ) { ?>+<a href="#writer" class="write">New</a> +<a href="#random" class="random">random</a><?php } ?></li>
<?php if($edition === "-1") { ?>
		<li class="pure-u-1-2">
			
			<h3>My collection</h3>
				
			<ul id="edition-options" class="system">
				<li><a href="<?php bloginfo('url'); ?>?edition=<?php echo $_REQUEST['edition']; ?>#collection-info">publish</a> /</li>
				<li><a href="<?php bloginfo('url'); ?>?generatePDF=1&amp;edition=<?php echo $_REQUEST['edition']; ?>" target="_blank">print</a> /</li>
				<li><a href="#change">change</a></li>
<!-- 				<li><a href="<?php if( is_home() ) { bloginfo('url'); } else { echo get_category_link( get_cat_ID( $chapterTitle ) ); } ?>" class="clear-edition">deselect</a></li> -->
			</ul>
				
		</li>
<?php } else if($edition) { 
	
	?>

		<li class="pure-u-1-2">

			<h3><?php echo $edition->name; ?></h3>
				
			<ul id="edition-options" class="system">
				<li><a href="<?php bloginfo('url'); ?>?generatePDF=1&amp;edition=<?php echo $_REQUEST['edition']; ?>" target="_blank">print</a> /</li>
				<li><a href="#change">change</a></li>
				<?php if($edition->slug !== "first-edition") { ?><li id="vote"><a href="#vote" class="vote">&#10084;</a> (6)</li><?php } ?>
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
				<a href="<?php if($edition) { echo add_query_arg( array("edition" => $_GET["edition"]), bloginfo('url')); } else { echo bloginfo('url'); } ?>#ch-<?php echo $chapters[0]->term_id; ?>">
					<span class="system">&equiv; </span>TOC
				</a>
			</h3>
		</li>
			        
	</ul>

	<div id="edition-selector" class="pure-g">

		<div class="pure-u-1-4"></div>				
		<h3 class="pure-u-5-12"><!-- Published editions of this book --></h3>
		<h3 id="close" class="pure-u-1-3"><span>&times;</span></h3>				

		<div class="pure-u-1-4"></div>				
		<div id="first-edition" class="pure-u-1-2">
			<h2><a href="<?php echo get_edition_URL("first-edition", get_bloginfo('url')); ?>">First edition</a></h2>
			<p class="system">by <a href="http://www.juremartinec.net/">Jure Martinec</a> on February 24, 2014</p>
		</div>		
		<div class="pure-u-1-4"></div>				

		<ul id="tabs" class="pure-u-1-6">
			<li><a href="#list-all" class="selected">All (<?php echo count($allEditions); ?>)</a></li>
			<li><a href="#list-liked">Liked (0)</a></li>
			<li><a href="#list-my">My (0)</a></li>
		</ul>

		<div class="pure-u-1-12"></div>
        
		<ul id="list-all" class="pure-u-5-12 list">
<?php 	foreach( $allEditions as $oneEdition ) { 
			
			$data = get_edition_data($oneEdition->term_id);
						
			if(!empty($data["author"])) {
				$author = $data["author"];
			}
			else {
				$author = "Anonymous";
			}
			
			if(!empty($data["email"])) {
				$author = '<a href="mailto:'.$data["email"].'">'.$author.'</a>';
			}
	
?>
			<li id="edition-<?php echo $oneEdition->term_id; ?>">
				<h2><a href="<?php echo get_edition_URL($oneEdition->slug, get_bloginfo('url')); ?>"><?php echo $oneEdition->name; ?></a></h2>
				<p class="system">by <?php echo $author; ?> on <?php echo date('F j, Y', $data["timestamp"] ); ?></p>
			</li>
<?php	} 
?>		</ul>

		<ul id="list-liked" class="pure-u-5-12 list">
			<li>liked</li>
		</ul>
		<ul id="list-my" class="pure-u-5-12 list">
			<li>my</li>
		</ul>

	</div>