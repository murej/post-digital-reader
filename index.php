<?php get_header(); ?>
<?php


	// query chapter (category) and filter paragraphs by edition (tag)
	$queryString = 'category__in=' . get_cat_ID( $chapterTitle );
	if($edition) { $queryString = $queryString . '&tag_id=' . $edition->term_id; }
	$the_query = new WP_Query($queryString);

	
?>

        <ul id="publish" class="pure-g">
	        <li class="pure-u-1-4"></li>
	        <li class="pure-u-1-4">		        
		        <form>
		        	<input class="title" type="text" placeholder="Click to name your edition" required>
		        	<input type="text" placeholder="Enter your name (optional)">
		        	<input type="email" placeholder="Enter your contact e-mail (optional)">
					<button type="submit" class="system">Publish as a new edition</button>
		        </form>
	        </li>
	        <li class="pure-u-1-12"></li>
	        <li class="pure-u-1-6">
				<button type="submit" class="system" name="view">View</button>
				<button type="submit" class="system" name="download">Download</button>
				<button type="submit" class="system clear">Clear my collection</button>
	        </li>
        </ul>

        <ul id="wrapper">
        	        
        	<li id="ch1" class="pure-g chapter">
        
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
        	
				<li id="paragraph-<?php the_ID(); ?>" class="pure-g paragraph">
	        		
	        		<div class="pure-u-1-12 paragraph-num system"><a href="">#<?php the_ID(); ?></a></div>
	        		<div class="pure-u-1-6"></div>
	        		
	        		<p class="pure-u-5-12 hyphenate"><?php echo str_replace("</p>", "", str_replace("<p>", "", get_the_content('')) ); ?></p>
	
	        		<div class="pure-u-1-4 collection-count system">(113x)</div>

	        		<ul class="pure-u-1-12 more system">
		        		<li><span>&equiv; </span><a href="">MOVE</a></li>
		        		<li><span>&infin; </span><a href="">SHARE</a></li>
	        		</ul>
	        		
	        		<div class="link">
	        		
	        			<div class="pure-u-1-6"></div>
	        			<div class="pure-u-2-3 separator"></div>
	        			<div class="pure-u-1-6"></div>

	        			<div class="pure-u-1-4"></div>
		        		<div class="pure-u-1-2 content"><h2>Link to paragraph #3</h2><form><input type="text" value="http://www.postdigitalreader.com/reactive-environments/?paragraph=3&edition=XXXXXXXX"></form></div>
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
        	
        	<li id="add-content" class="pure-g add-content">
        	
        		<ul id="adding-options">
        	
	        		<li class="pure-u-1-4"></li>
	        	
	        		<li class="pure-u-5-12 system">
		        		<a href="#writer" class="write">Write new</a> | <a href="">Add random</a>
	        		</li>
        		
        		</ul>

		        <div id="writer" class="pure-g">
		        
	        		<div class="pure-u-1-4"></div>
	        		
	        		<form class="pure-u-5-12">
	        				        			
	        			<textarea placeholder="Start typing here..."></textarea>
	        			<div class="buttons">
	        				<button type="submit" class="system">Add paragraph</button><span class="system"> or <a href="#" class="cancel">cancel</a></span>
	        			</div>
	        		
	        		</form>
				
		        </div>
        	
        	</li>

        	<li id="ch2" class="pure-g chapter next-chapter">
        
        		<div class="pure-u img"><img src="<?php bloginfo('template_url'); ?>/img/illustration.gif"></div>
        		<div class="pure-u-1-4"></div>
        		<div class="pure-u-1-2 title">
        			<h2>Design for</h2>
        			<h1>Language in/as<br>any form</h1>
        			<h2><a href="">Read next</a> &rarr;</h2>
        		</div>

        	</li>

        </ul> 
        
        <div id="dimmer"></div>      

<?php get_footer(); ?>