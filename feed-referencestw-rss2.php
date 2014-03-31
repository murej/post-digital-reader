<?php
/**
 * RSS2 Feed Template for displaying RSS2 Posts feed.
 *
 * @package WordPress
 */

header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);
$more = 1;

echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; ?>

<rss version="2.0"
	xmlns:content="http://purl.org/rss/1.0/modules/content/"
	xmlns:wfw="http://wellformedweb.org/CommentAPI/"
	xmlns:dc="http://purl.org/dc/elements/1.1/"
	xmlns:atom="http://www.w3.org/2005/Atom"
	xmlns:sy="http://purl.org/rss/1.0/modules/syndication/"
	xmlns:slash="http://purl.org/rss/1.0/modules/slash/"
	<?php
	/**
	 * Fires at the end of the RSS root to add namespaces.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_ns' );
	?>
>

<channel>
	<title><?php bloginfo_rss('name'); wp_title_rss(); ?></title>
	<atom:link href="<?php self_link(); ?>" rel="self" type="application/rss+xml" />
	<link><?php bloginfo_rss('url'); ?></link>
	<description><?php bloginfo_rss("description"); ?></description>
	<lastBuildDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_lastpostmodified('GMT'), false); ?></lastBuildDate>
	<language><?php bloginfo_rss( 'language' ); ?></language>
	<?php
	$duration = 'hourly';
	/**
	 * Filter how often to update the RSS feed.
	 *
	 * @since 2.1.0
	 *
	 * @param string $duration The update period.
	 *							Default 'hourly'. Accepts 'hourly', 'daily', 'weekly', 'monthly', 'yearly'.
	 */
	?>
	<sy:updatePeriod><?php echo apply_filters( 'rss_update_period', $duration ); ?></sy:updatePeriod>
	<?php
	$frequency = '1';
	/**
	 * Filter the RSS update frequency.
	 *
	 * @since 2.1.0
	 *
	 * @param string $frequency An integer passed as a string representing the frequency
	 *							 of RSS updates within the update period. Default '1'.
	 */
	?>
	<sy:updateFrequency><?php echo apply_filters( 'rss_update_frequency', $frequency ); ?></sy:updateFrequency>
	<?php
	/**
	 * Fires at the end of the RSS2 Feed Header.
	 *
	 * @since 2.0.0
	 */
	do_action( 'rss2_head');

	$posts = query_posts("post_status=publish&post_type=post&orderby=date&order=asc&nopaging=true");//&posts_per_page=50");

	foreach($posts as $post) {

		$references = get_post_meta($post->ID);
		
		$counter = 0;

		// if it will be in the feed
		if( !empty($references) ) {

			// get edition from first tag
			$edition = array_values( get_the_tags($post->ID) )[0];

			// get post category
			$category = get_the_category($post->ID)[0];
			
			// get paragraph ID (post title)
			$title = $post->post_title;

			if( $edition ) {
		
				$author = get_edition_data($edition->term_id)["author"];
				$link = get_category_link($category->term_id) . "?edition=" . $edition->slug . "#paragraph-" . $title;
			}
			else {
				
				$author = "Anonymous";
				$link = get_category_link($category->term_id) . "?clear-edition=1#paragraph-" . $title;
			}
			
			foreach( $references as $i => $ref ) {
	
				if( strpos($i, "reference-") === 0 ) {
	
					$ref = get_post_meta($post->ID, $i, true);
					$counter++;

					$link = html_entity_decode( remove_query_arg( array("reference"), $link ) );
					$link = htmlentities( add_query_arg( array( "reference" => $counter, "source" => "twitterfeed", "medium" => "twitter" ), $link ) );
					
					//$guid = htmlentities( add_query_arg( array("uniquifier" => generateRandomString(30) ), $ref["link"] ) );
					$guid = htmlentities( add_query_arg( array("referrer" => $title."_".$counter ), $ref["link"] ) );	// TA MORE BIT NEGENERIRAN UNIQUE

					if( !empty($ref["quote"]) ) {
						$quote = urldecode( "%C2%BB". $ref["quote"] ."%C2%AB" );
					}
												
					?>
					<item>
						<title><?php echo "¶".$title." [".$counter."]"; ?></title>
						<link><?php echo $link; ?></link>
						<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
						<dc:creator><![CDATA[<?php echo $author ?>]]></dc:creator>
						<guid isPermalink="true"><?php echo $guid; ?></guid>
						<description><![CDATA[<?php echo $quote; ?>]]></description>
						<?php rss_enclosure(); ?>
					<?php
					/**
					 * Fires at the end of each RSS2 feed item.
					 *
					 * @since 2.0.0
					 */
					do_action( 'rss2_item' );
					?>
					</item>
<?php
				}
				//break;
			}
			//break;
		}
	}
?>
</channel>
</rss>
