<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <title>404 // <?php bloginfo('name'); ?></title>
        <meta name="description" content="<?php bloginfo('description'); ?>">
        <meta name="viewport" content="width=device-width">

		<link rel="shortcut icon" href="<?php echo bloginfo("template_url"); ?>/img/favicon.ico">

        <style>
        	body {
	        	text-align: center;
        	}
        
        	p {
        		position: absolute;
        		top: 50%;
        		
	        	font-family: "Courier New", monospace;
	        	font-size: 0.875em;
	        	
	        	width: 100%;
        	}
        </style>
        
        <script>
        	setTimeout(function() {
	        	window.location.replace(<?php echo '"'.get_bloginfo('url').'"'; ?>);
        	}, 3000);
        </script>
    </head>
    <body>
        <div class="container">
			<p>Nothing here, <a href="<?php bloginfo('url'); ?>">go home</a>.</p>
			
        </div>

		<script>
		
			(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			ga('create', 'UA-25602894-2', 'postdigitalreader.com');
			ga('send', 'pageview');
		
		</script>

    </body>
</html>
