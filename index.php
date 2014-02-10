<?php get_header(); ?>
        
        <ul id="nav" class="pure-g">
        
			<li id="collector" class="pure-u-1-12"><div>0</div><span></span></li>
			<li class="pure-u-1-6 viewing system">edition:</li>
			<li class="pure-u-1-2">
			
				<h3>Hitchcock's worst nightmare</h3>
				
				<ul id="edition-options" class="system">
					<li><a href="">change</a></li>
					<li><a href="">download</a></li>
					<li><a href="">reset</a></li>
					<li><a href="">clear</a></li>
				</ul>
				
			</li>
			<li class="pure-u-1-6"></li>
			<li class="pure-u-1-12">
				<h3 id="toc">
					<a href="index.php">
						<span class="system">&equiv; </span>TOC
					</a>
				</h3>
			</li>
			        
        </ul>
        
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
        			<h1><?php single_cat_title(); ?></h1>
        		</div>

        	</li>

	        <li id="loc" class="pure-g system">
	        
	        	<div class="pure-u-1-12">1/233</div>
	        	
	        </li>

        	
        	<li id="content">
        	
        		<ul class="wrapper">

<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
        	
				<li id="paragraph-1" class="pure-g paragraph">
	        		
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
	        	
<?php endwhile; else: ?>
<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>
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