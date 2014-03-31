<?php get_header(); ?>
<?php

	$currentChapterID = get_cat_ID( $chapterTitle );
	$currentChapterKey;

	// query chapter (category) and filter paragraphs by edition (tag)
	$queryParams = array(

		'orderby' => 'date',
		'order' => 'ASC',
		'nopaging' => true,
		'category__in' => array( $currentChapterID )
	);

	// if edition is set (even my collection)
	if($edition) {

		$queryParams['post__in'] = $paragraphIDs;
		$queryParams['orderby'] = 'post__in';
	}

	// if edition is set, but is not my collection
	if($edition->slug !== "-1") {
		$queryParams['tag_id'] = $edition->term_id;
	}

	// query posts
	$the_query = new WP_Query($queryParams);

	//
	foreach($chapters as $key => $chapter) {

		if( $currentChapterID == $chapter->cat_ID ) {
			$currentChapterKey = $key;
		}
	}

?>
        <ul id="wrapper">

        	<li id="chapter-<?php echo $currentChapterID; ?>" class="pure-g chapter">

        		<div class="pure-u img"><img src="<?php echo get_bloginfo('template_url') . "/img/temp/" . get_term_by("id", $currentChapterID, "category")->slug . ".jpg"; ?>"></div>
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

	// get all editions where paragraph was published
	$editionsAsTags = get_the_tags();

?>

					<li id="paragraph-<?php the_title(); ?>" class="pure-g paragraph">

<?php if($editionsAsTags !== false) { ?>
		        		<div class="pure-u-1-12 paragraph-num system"><a href="#select" title="Collect/Remove">&para;<?php the_title(); ?></a></div>
<?php } else { ?>
		        		<div class="pure-u-1-12 paragraph-num system"><a href="#delete" title="Delete paragraph"><span style="font-size: 1.5em; padding: 0 0.25em;">&times;</span></a></div>
<?php } ?>
		        		<div class="pure-u-1-6"></div>

		        		<p class="pure-u-5-12 hyphenate"><?php
		        		
		        			echo do_shortcode( str_replace("<p>", "", str_replace("</p>", "", get_the_content('')) ) );
		        			
		        		?></p>

		        		<div class="pure-u-1-4 collection-count"><span class="system" title="<?php
														
							// if already published
							if( $editionsAsTags !== false ) {
							
								// count times published
			        			$publishedCount = count($editionsAsTags);
	
								// echo
								echo "Published in ".$publishedCount." edition";
	
								// if plural
			        			if($publishedCount > 1)
			        				echo "s";
			        		}
			        		// if not yet published
			        		else {
			        			
				        		$publishedCount = 0;
				        		echo "Never published";
			        		}

		        		?>.">(<?php echo $publishedCount;?>x)</span></div>

		        		<ul class="pure-u-1-12 more system">
			        		<?php if($edition->name === "-1") { ?><li class="move"><span>&equiv; </span><a href="">MOVE</a></li><?php } ?>
			        		<?php if($editionsAsTags !== false) { ?><li class="share"><span>&infin; </span><a href="">SHARE</a></li><?php } ?>
			        		<li class="link"><form><input type="text" value="<?php echo add_query_arg( array("source" => "share"), get_paragraph_permalink( get_the_title(), get_the_category()[0]->term_id, $edition ) ); ?>"></form></li>
		        		</ul>

		        	</li>

<?php wp_reset_postdata(); ?>

<?php endwhile; else: ?>

				<li class="pure-g paragraph">
					<div class="pure-u-1-4"></div>
					<p class="pure-u-5-12 hyphenate"><?php
					
						if($edition->name === "-1") {
							_e("You haven't collected any paragraphs in this chapter yet.");
						}
						else {
							_e("Sorry, there are no paragraphs in this chapter in your current edition.");
						}

					?></p>
				</li>

				<li class="pure-g paragraph">
					<div class="pure-u-1-4"></div>
					<p class="pure-u-5-12 system"><a href="#writer" class="write">Start writing</a> or <a href="#random" class="random">insert a random one</a></p>
				</li>

<?php endif; ?>

        		</ul>

        	</li>
<?php if( $edition->name === "-1" ) { ?>
        	<li id="add-content" class="pure-g add-content">
        	
<?php			if ( $the_query->have_posts() ) { ?>

        		<ul id="adding-options">

	        		<li class="pure-u-1-4"></li>

	        		<li class="pure-u-5-12 system">
		        		<a href="#writer" class="write">Write new</a> or <a href="#random" class="random">insert a random one</a>
	        		</li>

        		</ul>
        		
<?php 			} ?>

		        <div id="writer" class="pure-g">

	        		<div class="pure-u-1-4"></div>

	        		<form method="post" class="pure-u-5-12" action="<?php bloginfo('url'); ?>">

						<input type="hidden" value="1" name="contribute">

						<input type="hidden" value="<?php echo str_replace('/wp-content/themes', '', get_theme_root()); ?>/wp-blog-header.php" name="rootpath">
						<?php
						
							wp_nonce_field();
							wp_referer_field();
						?>

						<input type="hidden" value="<?php echo $currentChapterID; ?>" name="chapter">
	        			<div class="textarea" contenteditable></div>
	        			
	        			<input type="hidden" name="paragraph">
						
						<ul id="writing-instructions">
							<li id="add-reference-button" class="system"><a href="#reference">Add reference</a></li>
							<li id="instructions" class="system">ENTER to submit, ESC to cancel</li>
						</ul>
<!--
	        			<div class="buttons">
	        				<button type="submit" class="system">Add paragraph</button><span class="system"> or <a href="#" class="cancel">cancel</a></span>
	        			</div>
-->

	        		</form>

		        </div>
		        
				<div id="add-reference" class="pure-g">
				
					<div class="pure-u-1-4"></div>
					
					<form class="pure-u-1-2 shadow">
					
						<input name="link-or-isbn" type="text" placeholder="Enter ISBN (for books) or URL (for online resources)" required>
						
						<div>
							<span class="leftquote">&ldquo;</span>
							<textarea name="quote" placeholder="Enter quote (optional)" rows="1"></textarea>
							<span class="rightquote">&rdquo;</span>
						</div>
						
						<button type="submit" class="system">Add reference</button>
						
						<a href="#cancel" class="cancel system">cancel</a>

						<input class="reference-info" type="hidden" name="template" value="">
											
					</form>
				
				</div>

        	</li>

<?php
	
	}
	
	// find next chapter
	$nextChapterKey = find_first_available_chapter($currentChapterID);

	if( $nextChapterKey ) { ?>

        	<li id="ch-<?php echo $chapters[$nextChapterKey]->cat_ID; ?>" class="pure-g chapter next-chapter">
        		
				<a href="<?php
				
					if($_REQUEST["clear-edition"]) { echo add_query_arg( array("clear-edition" => 1), get_category_link($chapters[$nextChapterKey]->cat_ID)); }
					else { echo add_query_arg( array("edition" => $_GET["edition"]), get_category_link($chapters[$nextChapterKey]->cat_ID)); }
				
				?>">
					
					<div class="dimmer"></div>
					
	        		<div class="pure-u img"><img src="<?php echo get_bloginfo('template_url') . "/img/temp/" . $chapters[$nextChapterKey]->slug . ".jpg"; ?>" alt="Illustration"></div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-2 title">
	        			<h2>Design for</h2>
	        			<h1><?php echo $chapters[$nextChapterKey]->name; ?></h1>
	        			<h2>Read next &rarr;</h2>
	        		</div>

				</a>

        	</li>

<?php } ?>

        </ul>

        <div id="dimmer"></div>

<?php get_footer(); ?>