<?php get_header(); ?>

        <div id="cover" class="shadow">
        	
        	<div class="pure-g">

        		<div class="pure-u-1-6"></div>
        		<div class="pure-u-1-6"><div class="arrow"></div></div>
				<div class="pure-u-1-2">
					
					<h1>
						<span class="serif">P05T-D16174L</span><br>
						READER.<br>
						The <i class="strikethrough serif">form</i> role<br>
						of the book<br>
						<i class="strikethrough serif">in</i> with digital media<br>
					</h1>
					
					<iframe class="facebook" src="//www.facebook.com/plugins/like.php?href=http%3A%2F%2Fwww.postdigitalreader.com%2F&amp;width&amp;layout=button_count&amp;action=recommend&amp;show_faces=true&amp;share=false&amp;height=21" scrolling="no" frameborder="0" style="border:none; overflow:hidden; height:21px;" allowTransparency="true"></iframe>
					
				</div>
        	
        	</div>
        	
        </div>
        
        <ul id="nav" class="pure-g start">
        
			<li class="pure-u-1-4 viewing system">edition:</li>
			<li class="pure-u-1-2">
			
				<h3>Original</h3>
				
				<ul id="edition-options" class="system">
					<li><a href="">change</a></li>
					<li><a href="">download</a></li>
					<li><a href="">clear</a></li>
				</ul>
				
			</li>
        
        </ul>
        
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
	$chapters = get_categories(); 
	
	foreach($chapters as $counter=>$chapter) {
?>
        	<li id="ch<?php echo $counter+1; ?>" class="pure-g chapter">
				
				<a href="<?php echo get_category_link($chapter->cat_ID); ?>"> 
				
	        		<div class="pure-u img"><img src="<?php bloginfo('template_url'); ?>/img/illustration.gif"></div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-2 title">
	        			<h2>Design for</h2>
	        			<h1><?php echo $chapter->name; ?></h1>
	        		</div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-4"></div>
	        		<div class="pure-u-1-2 separator"></div>
        		
				</a>

        	</li>
<?php
	}
?>
        	
        	<li id="subscribe" class="pure-g">
        	
        		<div class="pure-u-1-4"></div>
				<div class="pure-u-1-2">        		
	        		<h2>Reference list</h2>
				</div>
        		<div class="pure-u-1-4"></div>
        		<div class="pure-u-1-4"></div>
        		<p class="pure-u-1-6 hyphenate">My money's in that office, right? If she start giving me some bullshit about it ain't there, and we got to go someplace else and get it, I'm gonna shoot you in the head then and there. Then I'm gonna shoot that bitch in the kneecaps, find out where my goddamn money is.</p>
				<div class="pure-u-1-12"></div>
				<div class="pure-u-1-4">
	        		
	        		<form action="http://twitter.com/postdigitreader"><button type="submit" class="system light">Follow on Twitter</button></form>
	        			
	        		<forn action="#RSS-link"><button type="submit" class="system light">Get RSS feed</button></div>
	        			
				</div>
        	
        	</li>
        	
        </ul>

<?php get_footer(); ?>