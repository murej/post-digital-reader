<?php get_header(); ?>
                
        <ul id="wrapper">
        
			<li class="pure-g introduction">
        
				<div class="pure-u-1-4"></div>
				<div class="pure-u-5-12">
					<h2>Hello, you.</h2>
					<p class="hyphenate">This is a theoretical exploration of how digital is affecting the reader of books. The writing process formed linear text and resulted in a set of possible design principles for the future (digital) book. This online version flips the traditional form and generates structure in relation to those principles in hope of creating new meaning, alternate interpretations and subsequently new ways of thinking about the role of the future book. Consider this not as a finished frame of work, not as a set of rules nor methods of creation for the ideal book of the future. It’s a starting point for a conversation, with gaps, mistakes and provocations, ready to be dissected, interpreted, filled in and corrected by the reading audience.<p>
					<p>&ndash; <a href="#author" class="author">Jure Martinec</a></p>
				</div>
				
			</li>

<?php
	foreach($chapters as $chapter) {
	
		$postQuery['category'] = $chapter->cat_ID;
		if($edition === "-1") { $postQuery['post__in'] = $paragraphIDs; }
		else if($edition) { $postQuery['tag'] = $_REQUEST['edition']; }
		$checkForPosts = get_posts($postQuery);
		
?>
        	<li id="ch-<?php echo $chapter->cat_ID; ?>" class="pure-g chapter<?php if(!$checkForPosts) { echo " empty"; } ?>">

<?php 	if($checkForPosts || $edition === "-1") { ?>
				<a href="<?php echo add_query_arg( array("edition" => $_GET["edition"]), get_category_link($chapter->cat_ID)); ?>"> 
<?php 	} ?>

	        		<div class="pure-u img"><img src="<?php bloginfo('template_url'); ?>/img/illustration.gif" alt="Illustration"></div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-2 title">
	        			<h2>Design for</h2>
	        			<h1><?php echo $chapter->name; ?></h1>
	        		</div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-2 separator"></div>

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

			<li id="publish" class="pure-g action">
			
        		<div class="pure-u-1-4"></div>
				<div id="collector" class="pure-u-1-12"><div>0</div><span></span></div>
				<div class="pure-u-1-6">        		
	        		<h2>It's your book.</h2>
				</div>
        		<p class="pure-u-1-4 hyphenate">Throughout chapters you have the option to collect each paragraph in any of the editions, add some of your own and finally publish the whole package as a unique edition.</p>
				<div class="pure-u-1-4"></div>
				<div class="pure-u-1-4"></div>
				<div class="pure-u-1-4 padded">
	        		
					<form method="get" action="<?php bloginfo('url'); ?>">
					
						<input type="hidden" name="edition" value="-1">
						<button type="submit" class="system light"<?php if(!isset($_COOKIE['myCollection']) || $edition === '-1') { ?> disabled <?php } ?>>View collected</button>
					</form>
					
				</div>
				
				<div class="pure-u-1-4">

	        		<form action=""><button type="submit" class="system light"<?php if(!isset($_COOKIE['myCollection'])) { ?> disabled <?php } ?>>Publish as a new edition</button></form>

				</div>
	        		
			</li>
        	
        </ul>

<?php get_footer(); ?>