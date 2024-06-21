<?php
defined( 'ABSPATH' ) || exit;
global $post;
if( !$post || !is_object( $post ) ){
    $post = get_post( absint( $_REQUEST['cncpid'] ) );
}
echo do_shortcode( apply_filters( 'the_content',$post->post_content ) );
