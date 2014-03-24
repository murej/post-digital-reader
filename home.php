<?php get_header(); ?>

<audio>
  <source src="<?php bloginfo('template_url'); ?>/media/poof.wav" type="audio/wav">
</audio>
                
        <ul id="wrapper">
        
			<li class="pure-g introduction">
        
				<div class="pure-u-1-4"></div>
				<div class="pure-u-5-12">
					<h2>This is <span class="what-is" contenteditable>a publication</span>?</h2>
					<p class="hyphenate">You think water moves fast? You should see ice. It moves like it has a mind. Like it knows it killed the world once and got a taste for murder. After the avalanche, it took us a week to climb out. Now, I don't know exactly when we turned on each other, but I know that seven of us survived the slide... and only five made it out. Now we took an oath, that I'm breaking now. We said we'd say it was the snow that killed the other two, but it wasn't. Nature is lethal but it doesn't hold a candle to man. Yeah, I like animals better than people sometimes... Especially dogs. Dogs are the best. Every time you come home, they act like they haven't seen you in a year. And the good thing about dogs... is they got different dogs for different people. Like pit bulls. The dog of dogs. Pit bull can be the right man's best friend... or the wrong man's worst enemy. You going to give me a dog for a pet, give me a pit bull. Give me... Raoul. Right, Omar? Give me Raoul.<p>
					<p>&ndash; <a href="#author" class="author">Jure Martinec</a></p>
				</div>
				
			</li>

<?php
	foreach($chapters as $i => $chapter) {
	
		if($edition->name === "-1") { $checkForPosts = check_for_posts($chapter->cat_ID, $paragraphIDs, $edition->name); }
		else { $checkForPosts = check_for_posts($chapter->cat_ID, $paragraphIDs, $edition->slug); }

?>
        	<li id="ch-<?php echo $chapter->cat_ID; ?>" class="pure-g chapter<?php if(!$checkForPosts) { echo " empty"; } ?>">

<?php 	if($checkForPosts || $edition->name === "-1") { ?>
				<a href="<?php echo add_query_arg( array("edition" => $_GET["edition"]), get_category_link($chapter->cat_ID)); ?>"> 
<?php 	} ?>

	        		<div class="pure-u img"><img src="<?php echo get_bloginfo('template_url') . "/img/temp/" . $chapters[$i]->slug . ".jpg"; ?>" alt="Illustration"></div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-2 title">
	        			<h2>Design for</h2>
	        			<h1><?php echo $chapter->name; ?><?php echo '<span class="counter"> ('.count($checkForPosts).')</span>'; ?></h1>
	        		</div>

<?php 	if($checkForPosts || $edition->name === "-1") { ?>
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
	        			
	        		<form action="<?php bloginfo("url"); ?>">
	        			<input type="hidden" name="feed" value="references">
	        			<button type="submit" class="system light">Subscribe to RSS</button>
	        		</form>
	        			
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
	        		<form action="<?php
	        		
	        		$firstChapterKey = find_first_available_chapter();
	        		
	        		echo get_category_link( $chapters[$firstChapterKey]->term_id );
	        		
	        		?>">
						<input type="hidden" name="edition" value="<?php echo $_REQUEST['edition']; ?>">
	        			<button type="submit" class="system">Start collecting</button>
	        		</form>

	        		<form action="<?php echo get_category_link( $chapters[0]->term_id ); ?>#writer">
						<input type="hidden" name="edition" value="<?php echo $_REQUEST['edition']; ?>">
	        			<button type="submit" class="system" disabled>Write to collection</button>
	        		</form>
<?php } ?>

<?php if(isset($_COOKIE['myCollection'])) { ?>
					<form method="get" action="<?php echo get_bloginfo("url") . "#ch-".$chapters[0]->term_id; ?>" class="view">
						<input type="hidden" name="edition" value="-1">
						<button type="submit" class="system" <?php if($edition->name === "-1") { echo "disabled"; } ?>>Edit/write</button>
					</form>
<?php }?>
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
					<form action="<?php bloginfo("url") ?>" method="post">
						<input type="hidden" name="edition" value="<?php echo $_REQUEST['edition']; ?>">
						<input type="hidden" name="clear" value="1">
						<button type="submit" class="system clear">Clear collection</button>
					</form>
					
				</div>

<?php }?>

			</li>
        	
        </ul>

<?php get_footer(); ?>