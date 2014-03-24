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

	$posts = query_posts("post_status=publish&post_type=post&orderby=date&posts_per_page=50");

	foreach($posts as $post) {

		$references = get_post_meta($post->ID);
		
		$counter = 0;

		if( !empty($references) ) {

			foreach( $references as $i => $ref ) {
	
				if( strpos($i, "reference-") === 0 ) {
	
					$ref = get_post_meta($post->ID, $i, true);
					$counter++;
									
					?>
					<item>
						<title><?php echo "¶".get_the_title()." [".$counter."]"; ?></title>
						<link><?php echo htmlentities( $ref["link"] ); ?></link>
						<pubDate><?php echo mysql2date('D, d M Y H:i:s +0000', get_post_time('Y-m-d H:i:s', true), false); ?></pubDate>
						<dc:creator><![CDATA[<?php echo the_author_meta( 'display_name' , $post->post_author ); ?>]]></dc:creator>
						<guid isPermaLink="true"><?php echo htmlentities( $ref["link"] ); ?></guid>
						<description><![CDATA[<?php echo urldecode( "%E2%80%9C". $ref["quote"] ."%E2%80%9D" ); ?>]]></description>
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
			}
		
		}

	} // end if
?>
</channel>
</rss>
