<?php get_header(); ?>

<audio>
  <source src="<?php bloginfo('template_url'); ?>/media/poof.wav" type="audio/wav">
</audio>
                
        <ul id="wrapper">
        
			<li class="pure-g introduction">	
<?php
				$introduction = get_page_by_title("Introduction");
				$definitions = get_post_meta( $introduction->ID, "definitions", true );	
?>
				<div class="pure-u-1-4"></div>
				<div class="pure-u-5-12 hyphenate">
					<h2>Hey, you're reading <span class="what-is" contenteditable><?php echo $definitions[ mt_rand( 0, count($definitions)-1 ) ]; ?></span><span class="light">!</span></h2>					
					<?php echo $introduction->post_content; ?>
					<p>&ndash; <a href="#author" class="author">Jure Martinec</a></p>
				</div>
				
			</li>

<?php
	foreach($chapters as $i => $chapter) {
	
		if($edition->name === "-1") { $checkForPosts = check_for_posts($chapter->cat_ID, $paragraphIDs, $edition->name); }
		else { $checkForPosts = check_for_posts($chapter->cat_ID, $paragraphIDs, $edition->slug); }
		
		if(!$checkForPosts) { $empty = "-bw"; }
		else { $empty = ""; }

?>
        	<li id="ch-<?php echo $chapter->cat_ID; ?>" class="pure-g chapter<?php if(!$checkForPosts) { echo " empty"; } ?>"
        	
        		data-volume="<?php echo get_volume($checkForPosts, $paragraphIDs); ?>"
        		data-activity="<?php echo get_activity($chapter->term_id); ?>">

<?php 	if($checkForPosts || $edition->name === "-1") { ?>
				<a href="<?php 
				
					if($_REQUEST["clear-edition"]) { echo add_query_arg( array("clear-edition" => 1), get_category_link($chapter->cat_ID)); }
					else { echo add_query_arg( array("edition" => $_GET["edition"]), get_category_link($chapter->cat_ID)); }
				
				?>"> 
<?php 	} ?>

	        		<div class="pure-u img" style="opacity:"><img src="<?php echo get_bloginfo('template_url') . "/img/temp/" . $chapters[$i]->slug . $empty . ".jpg"; ?>" alt="Illustration"></div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-2 title">
	        			<h2>Design for</h2>
	        			<h1><span class="headline"><?php echo $chapter->name; ?></span><span class="counter">&nbsp;(<?php echo count($checkForPosts); ?>)</span></h1>
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
        		<p class="pure-u-1-6">Through active participation the knowledgebase keeps growing. Stay on track with the conversation by subscribing to the list of references, updated as soon as someone submits some referenced thoughts.</p>
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
				     		
					<form id="publish-form" method="post" action="<?php bloginfo('url'); ?>">
	
						<input type="hidden" value="1" name="publish">
	      			
						<input type="hidden" value="<?php echo str_replace('/wp-content/themes', '', get_theme_root()); ?>/wp-blog-header.php" name="rootpath">
						<?php wp_nonce_field(); ?>
						<input class="title" type="text" placeholder="Enter edition title..." required name="editionTitle" maxlength="70">
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
<?php 				if($edition) {	?>
						<input type="hidden" name="edition" value="<?php echo $_GET['edition']; ?>">
<?php 				} else if( $_GET["clear-edition"] ) {	?>
						<input type="hidden" name="clear-edition" value="1">
<?php 				}	?>
	        			<button type="submit" class="system">Start collecting</button>
	        		</form>

	        		<form action="<?php echo get_category_link( $chapters[0]->term_id ); ?>#writer">
						<input type="hidden" name="edition" value="<?php echo $_REQUEST['edition']; ?>">
	        			<button type="submit" class="system" disabled>Write new paragraphs</button>
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
					<p>All editions are ready to be downloaded, printed and stored. Furthermore, paragraphs can be collected throughout chapters in any of the editions, you can add some of your own, share quotes, references and finally publish the whole package as a unique version of the book, ready to be tackled by the willing community.</p>
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