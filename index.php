<?php get_header(); ?>
<?php

	$currentChapterID = get_cat_ID( $chapterTitle );
	$currentChapterKey;

	// query chapter (category) and filter paragraphs by edition (tag)
	$queryParams = array( 
	
		'orderby' => 'title',
		'order' => 'ASC',
		'nopaging' => true,
		'category__in' => array( $currentChapterID )
	
	);
	
	if($edition === "-1") { $queryParams['post__in'] = $paragraphIDs; $queryParams['orderby'] = 'post__in'; }
	else if($edition) { $queryParams['tag_id'] = $edition->term_id; }
		
	$the_query = new WP_Query($queryParams);
	
	foreach($chapters as $key => $chapter) {
		
		if( $currentChapterID == $chapter->cat_ID ) {
			$currentChapterKey = $key;
		}
	}
	
?>

        <ul id="publish" class="pure-g">
	        <li class="pure-u-1-4"></li>
	        <li class="pure-u-1-4">		        
        		<form method="post" action="<?php bloginfo('url'); ?>">

					<input type="hidden" value="1" name="publish">
      			
					<input type="hidden" value="<?php echo str_replace('/wp-content/themes', '', get_theme_root()); ?>/wp-blog-header.php" name="rootpath">
					<?php wp_nonce_field(); ?>

					
		        	<input class="title" type="text" placeholder="Click to name your edition" required name="editionTitle">
		        	<input type="text" placeholder="Enter your name (optional)" name="author">
		        	<input type="email" placeholder="Enter your contact e-mail (optional)" name="email">
					<button type="submit" class="system">Publish as a new edition</button>
		        </form>
	        </li>
	        <li class="pure-u-1-12"></li>
	        <li class="pure-u-1-6">
				<form method="get" action="<?php strtok("http://".$_SERVER["HTTP_HOST"].$_SERVER["REQUEST_URI"],'?'); ?>">
					<input type="hidden" name="edition" value="-1">
					<button type="submit" class="view system">View</button>
				</form>
				<button type="submit" class="download system">Download</button>
				<form action="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
					<input type="hidden" name="edition" value="<?php echo $_REQUEST['edition']; ?>">
					<button type="submit" class="system clear">Clear my collection</button>
				</form>
	        </li>
        </ul>

        <ul id="wrapper">
        	        
        	<li id="chapter-<?php echo $currentChapterID; ?>" class="pure-g chapter">
        
        		<div class="pure-u img"><img src="<?php bloginfo('template_url'); ?>/img/illustration.gif"></div>
        		<div class="pure-u-1-4"></div>
        		<div class="pure-u-1-2 title">
        			<h2>Design for</h2>
        			<h1><?php echo $chapterTitle; ?></h1>
        		</div>

        	</li>

	        <li id="loc" class="pure-g system">
	        
	        	<div class="pure-u-1-12">1/233</div>
	        	
	        </li>

        	
        	<li id="content">
        	
        		<ul class="wrapper">
<?php 

// THE LOOP
if ( $the_query->have_posts() ) : while ( $the_query->have_posts() ) : $the_query->the_post();

?>
        	
					<li id="paragraph-<?php the_title(); ?>" class="pure-g paragraph">
		        		
		        		<div class="pure-u-1-12 paragraph-num system"><a href="">#<?php the_title(); ?></a></div>
		        		<div class="pure-u-1-6"></div>
		        		
		        		<p class="pure-u-5-12 hyphenate"><?php echo do_shortcode( str_replace("<p>", "", str_replace("</p>", "", get_the_content('')) ) ); ?></p>
		
		        		<div class="pure-u-1-4 collection-count"><span class="system" title="Published in <?php 
		        		
		        			$publishedCount = count(get_the_tags());
		        		
							echo $publishedCount." edition";
		        		
		        			if($publishedCount > 1)
		        				echo "s";
		        		
		        		?>.">(<?php echo $publishedCount;?>x)<span></div>
	
		        		<ul class="pure-u-1-12 more system">
			        		<?php if($edition === "-1") { ?><li class="move"><span>&equiv; </span><a href="">MOVE</a></li><?php } ?>
			        		<li class="share"><span>&infin; </span><a href="">SHARE</a></li>
		        		</ul>
	
		        		<div class="link">
		        		
		        			<div class="pure-u-1-6"></div>
		        			<div class="pure-u-2-3 separator"></div>
		        			<div class="pure-u-1-6"></div>
	
		        			<div class="pure-u-1-4"></div>
			        		<div class="pure-u-1-2 content"><h2>Link to paragraph #<?php the_title(); ?></h2><form><input type="text" value="http://www.postdigitalreader.com/reactive-environments/?paragraph=3&edition=XXXXXXXX"></form></div>
		        			<div class="pure-u-1-4"></div>
	
		        			<div class="pure-u-1-6"></div>
		        			<div class="pure-u-2-3 separator"></div>
		        			<div class="pure-u-1-6"></div>
			        		
		        		</div>
		
		        	</li>

<?php wp_reset_postdata(); ?>
	        	
<?php endwhile; else: ?>
<!--
				<li class="pure-g paragraph">
					<div class="pure-u-1-4"></div>
					<p class="pure-u-5-12 hyphenate"><?php _e('Sorry, not much to see here.'); ?></p>
				</li>
-->
<?php endif; ?>

        		</ul>
	        
        	</li>
<?php if( $edition === "-1" ) { ?>    	
        	<li id="add-content" class="pure-g add-content">
        	
        		<ul id="adding-options">
        	
	        		<li class="pure-u-1-4"></li>
	        	
	        		<li class="pure-u-5-12 system">
		        		<a href="#writer" class="write">Write new</a> | <a href="">Add random</a>
	        		</li>
        		
        		</ul>

		        <div id="writer" class="pure-g">
		        
	        		<div class="pure-u-1-4"></div>
	        		
	        		<form method="post" class="pure-u-5-12" action="<?php bloginfo('template_url'); ?>/contribute.php">
	        			
						<input type="hidden" value="<?php echo $currentChapterID; ?>" name="chapter">
						<input type="hidden" value="<?php echo str_replace('/wp-content/themes', '', get_theme_root()); ?>/wp-blog-header.php" name="rootpath">
						<?php wp_nonce_field(); ?>
	        			
	        			<textarea name="paragraph" placeholder="Start typing and press enter to submit"></textarea>
<!--
	        			<div class="buttons">
	        				<button type="submit" class="system">Add paragraph</button><span class="system"> or <a href="#" class="cancel">cancel</a></span>
	        			</div>
-->
	        		
	        		</form>
				
		        </div>
        	
        	</li>

<?php } ?>
        	
<?php if( $currentChapterKey+1 <= count($chapters)-1  ) { ?>

        	<li id="ch-<?php echo $chapters[$currentChapterKey+1]->cat_ID; ?>" class="pure-g chapter next-chapter">
				
				<a href="<?php echo add_query_arg( array("edition" => $_GET["edition"]), get_category_link($chapters[$currentChapterKey+1]->cat_ID)); ?>"> 
				
	        		<div class="pure-u img"><img src="<?php bloginfo('template_url'); ?>/img/illustration.gif" alt="Illustration"></div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-2 title">
	        			<h2>Design for</h2>
	        			<h1><?php echo $chapters[$currentChapterKey+1]->name; ?></h1>
	        			<h2><a href="<?php echo add_query_arg( array("edition" => $_GET["edition"]), get_category_link($chapters[$currentChapterKey+1]->cat_ID)); ?>">Read next</a> &rarr;</h2>
	        		</div>
        		
				</a>

        	</li>

<?php } ?>

        </ul> 
        
        <div id="dimmer"></div>      

<?php get_footer(); ?>