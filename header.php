<?php 

	global $edition;
	global $chapters;
	global $chapterTitle;
	
	$edition = get_term_by('slug', $_GET["edition"], 'post_tag');
	$chapters = get_categories(); 
	$chapterTitle = single_cat_title('', false);

	// show original edition if there is no edition set and it was not explicitly cleared
	if( !isset( $_GET["edition"] ) && !isset( $_COOKIE['clearedEditions'] ) ){
		set_edition("original", $_SERVER['REQUEST_URI']);		
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

<?php //wp_head(); ?>

    </head>
    
    <body>
        <!--[if lt IE 7]>
            <p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to improve your experience.</p>
        <![endif]-->
        
<?php if( is_home() ) { ?>

        <div id="cover" class="shadow">
        	
        	<div class="pure-g">

        		<div class="pure-u-1-6"></div>
        		<div class="pure-u-1-6"><div class="arrow"></div></div>
				<div class="pure-u-1-2">
					
					<h1>
						<span class="serif">P05T-D16174L</span><br>
						READER.<br>
						The <i class="strikethrough serif">form</i> role<br>
						of books in the<br>
						digital <i class="strikethrough serif">media</i> age<br>
					</h1>
					
					<iframe class="facebook" src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.postdigitalreader.com%2F&amp;width&amp;layout=button_count&amp;action=recommend&amp;show_faces=true&amp;share=false&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>
					
				</div>
				        	
        	</div>
        	
        </div>

<?php } ?>
        
        <ul id="nav" class="pure-g <?php if( is_home() ) { echo "home start"; } ?>">
        
			<li id="collector" class="pure-u-1-12"><div>0</div><span></span></li>
<?php if($edition) { ?>
			<li class="pure-u-1-6 viewing system">edition:</li>
			<li class="pure-u-1-2">
			
				<h3><?php echo $edition->name; ?></h3>
				
				<ul id="edition-options" class="system">
					<li><a href="">change</a></li>
					<li><a href="">download</a></li>
					<li><a href="">reset</a></li>
					<li><a href="<?php if( is_home() ) { bloginfo('url'); } else { echo get_category_link( get_cat_ID( $chapterTitle ) ); } ?>" class="clear-edition">clear</a></li>
				</ul>
				
			</li>
<?php } else { ?>
			<li class="pure-u-1-6 viewing system"></li>
			<li class="pure-u-1-2 no-edition">
				
				<h3>Viewing all editions </h3>
						
				<ul id="edition-options" class="system">
					<li><a href="">change</a></li>
				</ul>
				
			</li>
<?php } ?>
			<li class="pure-u-1-6"></li>
			<li class="pure-u-1-12">
				<h3 id="toc">
					<a href="<?php if($edition) { echo add_query_arg( array("edition" => $_GET["edition"]), bloginfo('url')); } else { echo bloginfo('url'); } ?>">
						<span class="system">&equiv; </span>TOC
					</a>
				</h3>
			</li>
			        
        </ul>

<!--
        <ul id="edition-selector">
        
        	
        
        </ul>
-->