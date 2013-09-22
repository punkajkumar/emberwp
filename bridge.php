<?php

// Good references for XML-RPC in WP:
// http://codex.wordpress.org/XML-RPC_WordPress_API
// http://djzone.im/2011/04/simple-xml-rpc-client-to-wordpress-made-easy/
// http://jumping-duck.com/tutorial/using-xml-rpc-in-wordpress/

require("../../../wp-load.php");
include_once( ABSPATH . WPINC . '/class-IXR.php' );
include_once( ABSPATH . WPINC . '/class-wp-http-ixr-client.php' );

function getPosts() {
	// Create a file with your WP username/password stored as
	// $WP_USERNAME and $WP_PASSWORD
	require("./password.php");
	
	$client = new WP_HTTP_IXR_CLIENT( site_url() . '/xmlrpc.php' );
	//$client->debug = true;
	
	$filter['number'] = 10;
	$result = $client->query( 'wp.getPosts', array(
		0,
		$WP_USERNAME,
		$WP_PASSWORD,
		$filter,
		array("post_id", "post_title", "post_date", "post_author", "terms", "post_content", "post_name")
	));
	$posts = $client->getResponse();
	$result = $client->query( 'wp.getUser', array(
		0,
		$WP_USERNAME,
		$WP_PASSWORD,
		$posts[0]['post_author']
	));
	$author = $client->getResponse();
	foreach ($posts as &$post) {
		$post['post_content'] = wpautop( $post['post_content'] );
		$post['post_author'] = $author['nickname'];
		$post['post_category'] = $post['terms'][0]['name'];
	}
	
	echo json_encode($posts);
}

if ($_GET['action'] == 'getPosts') {
	getPosts();
} elseif ($_GET['action'] == 'getPost') {
	getPost($_GET['post_id']);
}

?>