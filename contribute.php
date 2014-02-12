<?php

//Get the submitted form
ob_start();
require_once($_POST["rootpath"]);

$paragraph = $_POST["paragraph"];
$chapter = $_POST["chapter"];
//$edition = $_POST["edition"];

$path = $_POST["rootpath"];
$nonce=$_POST["_wpnonce"];

//Load WordPress
require($path);

//Verify the form fields
if (! wp_verify_nonce($nonce) ) die('Security check'); 

   //Post Properties
    $new_post = array(
            'post_content'  => $paragraph,
            'post_category' => array($chapter),  // Usable for custom taxonomies too
            //'tags_input'    => array($edition),
            'post_status' => 'publish',           // Choose: publish, preview, future, draft, etc.
            'post_type' => 'post',  //'post',page' or use a custom post type if you want to
            'post_author' => 2 //Author ID
    );
    //save the new post
    $pid = wp_insert_post($new_post);
     
    // Insert Form data into Custom Fields
/*
    add_post_meta($pid, 'author', $author, true);
    add_post_meta($pid, 'author-email', $email, true);
    add_post_meta($pid, 'author-website', $site, true);
*/


header("Location: " . $_SERVER["HTTP_REFERER"]);
?>