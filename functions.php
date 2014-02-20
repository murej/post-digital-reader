<?php

function get_edition_URL($parameter, $url) {

	return add_query_arg( array("edition" => $parameter), $url );
	
}

function set_edition($parameter, $url) {
	
	Header("Location: " . get_edition_URL($parameter, $url));
	exit;

}

function get_paragraphIDs($cookieData) {
	return json_decode( urldecode( $cookieData ) );
}

function generate_PDF($edition, $chapters) {

	include('mpdf/mpdf.php');
	$stylesheet = file_get_contents(get_bloginfo('template_url')."/css/print.css");
	$footer = array (
		'odd' => array (
			'L' => array (
				'content' => '{PAGENO}',
				'font-size' => 12,
				'font-style' => 'R',
				'font-family' => 'helvetica',
				'color'=>'#000000'
			),
			'C' => array (
				'content' => '',
				'font-size' => 12,
				'font-style' => 'R',
				'font-family' => 'helvetica',
				'color'=>'#000000'
			),
			'R' => array (
				'content' => '',
				'font-size' => 12,
				'font-style' => 'R',
				'font-family' => 'helvetica',
				'color'=>'#000000'
			),
			'line' => 0,
			),
		'even' => array ()
	);
	
	
	
	// set document		
	$mpdf = new mPDF('utf-8');
	$mpdf->WriteHTML($stylesheet,1);
	
	// write cover page
	$mpdf->WriteHTML('<h1><span class="serif">P05T-D16174L</span><br> READER.<br> The <i class="strikethrough serif">form</i> role <br>of books in the <br>digital <i class="strikethrough serif">media</i> age</h1>');
	
	// if my collection
	if( $edition == "-1" ) {
		$editionName = "My Collection";
		// write info
		$mpdf->WriteHTML('<div id="edition"><h3 class="title">'.$editionName.'</h3><p class="info system">on '.date('d F Y').'</p><p class="link">'.get_bloginfo('url').'/?edition='.$edition.'</p></div>');
		$paragraphIDs = get_paragraphIDs( $_COOKIE["myCollection"] );
	}
	// if edition
	else {
		$editionName = get_term_by("slug", $edition, "post_tag")->name;
		// write info
		$mpdf->WriteHTML('<div id="edition"><h4 class="system edition">Edition:</h4><h3 class="title">'.$editionName.'</h3><p class="info system">by Jure Martinec on '.date('d F Y').'</p><p class="link">'.get_bloginfo('url').'/?edition='.$edition.'</p></div>');
	}
	
	// go through all chapters
	foreach($chapters as $chapter) {

		// if my collection
		if( $edition == "-1" ) {
				
			$queryParams = array( 'category__in' => array( $chapter->term_id ), 'post__in' => $paragraphIDs );
			$content = get_posts($queryParams);
		}
		// if edition
		else {
			$content = get_posts('tag='.$edition.'&cat='.$chapter->term_id);
		}
				
		if(!empty($content)) {

			$mpdf->AddPage();
			$mpdf->setFooter($footer);
			$mpdf->WriteHTML('<h3>Design for</h3><h2>'.$chapter->name.'</h2>',2);

			foreach($content as $paragraph) {
			
				$output = str_replace("</p>", "<span> #". $paragraph->ID ."</span></p>", $paragraph->post_content);
			
				$mpdf->WriteHTML($output,2);
			}
		}
		else {

			$mpdf->AddPage();
			$mpdf->setFooter($footer);
			$mpdf->WriteHTML('<h3>Design for</h3><h2 class="strikethrough">'.$chapter->name.'</h2>',2);
		}
	}

	$mpdf->SetTitle("Post-digital Reader: ".$editionName);
	//$mpdf->SetAuthor();

	$mpdf->Output("Post-digital Reader - ".$editionName.".pdf","I");
	exit;
}


?>