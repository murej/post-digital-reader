<?php get_header(); ?>
                
        <ul id="wrapper">
        
			<li class="pure-g introduction">
        
				<div class="pure-u-1-4"></div>
				<div class="pure-u-5-12">
					<h2>This is <form><input type="text" value="a book" size="14"></form></h2>
					<p class="hyphenate">It's a theoretical exploration of how digital is affecting the reader of books. The writing process formed linear text and resulted in a set of possible design principles for the future (digital) book. This online version flips the traditional form and generates structure in relation to those principles in hope of creating new meaning, alternate interpretations and subsequently new ways of thinking about the role of the future book. Consider this not as a finished frame of work, not as a set of rules nor methods of creation for the ideal book of the future. It’s a starting point for a conversation, with gaps, mistakes and provocations, ready to be dissected, interpreted, filled in and corrected by the reading audience.<p>
					<p>&ndash; <a href="#author" class="author">Jure Martinec</a></p>
				</div>
				
			</li>

<?php
	foreach($chapters as $i => $chapter) {
	
		$postQuery['category'] = $chapter->cat_ID;
		if($edition === "-1") { $postQuery['post__in'] = $paragraphIDs; }
		else if($edition) { $postQuery['tag'] = $_REQUEST['edition']; }
		$checkForPosts = get_posts($postQuery);
		
?>
        	<li id="ch-<?php echo $chapter->cat_ID; ?>" class="pure-g chapter<?php if(!$checkForPosts) { echo " empty"; } ?>">

<?php 	if($checkForPosts || $edition === "-1") { ?>
				<a href="<?php echo add_query_arg( array("edition" => $_GET["edition"]), get_category_link($chapter->cat_ID)); ?>"> 
<?php 	} ?>

	        		<div class="pure-u img"><img src="<?php echo get_bloginfo('template_url') . "/img/temp/" . ($i+1) . ".jpg"; ?>" alt="Illustration"></div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-2 title">
	        			<h2>Design for</h2>
	        			<h1><?php echo $chapter->name; ?></h1>
	        		</div>

<?php 	if($checkForPosts || $edition === "-1") { ?>
				</a>
<?php 	} ?>

        	</li>
<?php
		wp_reset_postdata();
	}
	
?>
        	
        	<li id="subscribe" class="pure-g action">

	        	<div class="pure-u-1-4"></div>
	        	<div class="pure-u-1-2 separator"></div>
	        	<div class="pure-u-1-4"></div>
        	
        		<div class="pure-u-1-4"></div>
				<div class="pure-u-1-2">        		
	        		<h2>Reference feed.</h2>
				</div>
        		<div class="pure-u-1-4"></div>
        		<div class="pure-u-1-4"></div>
        		<p class="pure-u-1-6 hyphenate">My money's in that office, right? If she start giving me some bullshit about it ain't there, and we got to go someplace else and get it, I'm gonna shoot you in the head then and there. Then I'm gonna shoot that bitch in the kneecaps, find out where my goddamn money is.</p>
				<div class="pure-u-1-12"></div>
				<div class="pure-u-1-4">
	        		
	        		<form action="http://twitter.com/postdigitreader"><button type="submit" class="system light">Follow on Twitter</button></form>
	        			
	        		<form action="#RSS-link"><button type="submit" class="system light">Get RSS feed</button></form>
	        			
				</div>
				
	        	<div class="pure-u-1-4"></div>
	        	<div class="pure-u-1-4"></div>
	        	<div class="pure-u-1-2 separator"></div>
        	
        	</li>

			<li id="collection-info" class="pure-g action">
			
        		<div class="pure-u-1-4"></div>

<?php if(isset($_COOKIE['myCollection'])) { ?>
				<div class="pure-u-1-4"> 
				     		
					<form method="post" action="<?php bloginfo('url'); ?>">
	
						<input type="hidden" value="1" name="publish">
	      			
						<input type="hidden" value="<?php echo str_replace('/wp-content/themes', '', get_theme_root()); ?>/wp-blog-header.php" name="rootpath">
						<?php wp_nonce_field(); ?>
						<input class="title" type="text" placeholder="Enter edition title..." required name="editionTitle">
						<input type="text" placeholder="Enter your name (optional)" name="author">
						<input type="email" placeholder="Enter your contact e-mail (optional)" name="email">
						<button type="submit" class="system publish">Publish as a new edition</button>
					</form>
					
				</div>
				
				<div class="pure-u-1-12"></div>
				<div class="pure-u-1-6">
<?php } else { ?>

				<div class="pure-u-1-4 left">
	        		<form action="<?php echo get_category_link( $chapters[0]->term_id ); ?>">
						<input type="hidden" name="edition" value="<?php echo $_REQUEST['edition']; ?>">
	        			<button type="submit" class="system">Start collecting</button>
	        		</form>
<?php } ?>
					<form method="get" action="<?php bloginfo('url'); ?>" class="view">
						<input type="hidden" name="edition" value="-1">
						<button type="submit" class="system"<?php if(!isset($_COOKIE['myCollection'])) { ?> disabled <?php } ?>>Edit collection</button>
					</form>

					<form method="get" action="<?php bloginfo('url'); ?>">
						<input type="hidden" name="edition" value="-1">
						<input type="hidden" name="generatePDF" value="1">
						<button type="submit" class="system"<?php if(!isset($_COOKIE['myCollection'])) { ?> disabled <?php } ?>>Print collection</button>
					</form>
<?php if(!isset($_COOKIE['myCollection'])) { ?>
	        		<form action=""><button type="submit" class="system publish" disabled>Publish as a new edition</button></form>
				</div>
				
				<div class="pure-u-1-12"></div>
				<div class="pure-u-1-6">      		
	        		<h2>It's your book.</h2>
					<p class="hyphenate">Throughout chapters you have the option to collect each paragraph in any of the editions, add some of your own and finally publish the whole package as a unique edition.</p>
				</div>

<?php } else { ?>
					<form action="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]"; ?>">
						<input type="hidden" name="edition" value="<?php echo $_REQUEST['edition']; ?>">
						<button type="submit" class="system clear">Clear collection</button>
					</form>
					
				</div>

<?php }?>

			</li>
        	
        </ul>

<?php get_footer(); ?>