<?php
defined( 'ABSPATH' ) || exit;

add_filter( 'et_builder_post_types',function( $post_types ){
  if( $post_types && is_array( $post_types ) ){
    $post_types[] = 'eos_dyn_content';
    $post_types = array_unique( $post_types );
    $GLOBALS['content_no_cache_public'] = true;
  }
  return $post_types;
} );

add_filter( 'et_builder_load_actions',function( $actions ) {
	$actions[] = 'eos_dyn_get_content';
	return $actions;
} );
