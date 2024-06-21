<?php
defined( 'ABSPATH' ) || exit;

add_action( 'wp_ajax_eos_dyn_get_content','eos_dyn_get_content' );
add_action( 'wp_ajax_nopriv_eos_dyn_get_content','eos_dyn_get_content' );
//Ajax function to retrieve the dynamic content
function eos_dyn_get_content(){
  if( !isset( $_POST['id'] ) || empty( $_POST['id'] ) ){
    die();
    exit;
  }
  if( absint( $_POST['id'] ) > 0 ) {
    if( isset( $_POST['request'] ) && 'remote' === $_POST['request'] ){
      $args = array( 'timeout' => 5,'sslverify' => false );
      if( isset( $_POST['current_user'] ) && in_array( $_POST['current_user'],array( 'true','yes','1' ) ) && isset( $_POST['headers'] ) ){
        $args = eos_dyn_user_headers( $args,true );
      }
      $response = wp_remote_get(
        add_query_arg( array( 'cnc' => sanitize_text_field( md5( ABSPATH ) ),'cncpid' => absint( $_POST['id'] ) ),get_the_permalink( absint( $_POST['id'] ) ) )
        ,$args
      );
      if( !is_wp_error( $response ) ){
        do_action( 'content_no_cache_before_sending_content',sanitize_text_field( $_POST['id'] ), $_POST );
        echo apply_filters( 'content_no_cache_output', wp_remote_retrieve_body( $response ), absint( $_POST['id'] ) );
      }
    }
    else{
      $post = get_post( absint( $_POST['id'] ) );
      if( $post ){
        if( class_exists( 'WPBMap' ) ) WPBMap::addAllMappedShortcodes();
        if( class_exists( 'EOSBMap' ) ) EOSBMap::addAllMappedShortcodes();
        if( class_exists("\\Elementor\\Plugin" ) ) {
            $contentElementor = "";
            $pluginElementor = \Elementor\Plugin::instance();
            $contentElementor = $pluginElementor->frontend->get_builder_content_for_display( absint( $_POST['id'] ),true );
            echo $contentElementor;
        }
        else{
          do_action( 'content_no_cache_before_sending_content',sanitize_text_field( $_POST['id'] ), $_POST );
          echo apply_filters( 'content_no_cache_output', do_shortcode( apply_filters( 'the_content',$post->post_content ) ), absint( $_POST['id'] ) );
        }
      }
    }
  }
  elseif( function_exists( sanitize_key( $_POST['id'] ) ) ) {
    call_user_func( sanitize_key( $_POST['id'] ) );
  }
  die();
  exit;
}

function eos_dyn_user_headers( $args,$admin = true ){
	$cookies = array();
	if( $admin ){
		foreach ( $_COOKIE as $name => $value ) {
			$cookies[sanitize_key( $name )] = sanitize_text_field( $value );
		}
	}
	$headers = false;
	if( isset( $_POST['headers'] ) && !empty( $_POST['headers'] ) ){
		$headers = json_decode( sanitize_text_field( stripslashes( $_POST['headers'] ) ),true );
	}
	if( $headers ){
		$args['headers'] = $headers;
	}
	else{
		if( isset( $_SERVER['HTTP_AUTHORIZATION'] ) && !empty( $_SERVER['HTTP_AUTHORIZATION'] ) ){
			$args['headers'] = array(
					'Authorization' => sanitize_text_field( $_SERVER['HTTP_AUTHORIZATION'] )
			);
		}
		elseif( isset( $_SERVER['PHP_AUTH_USER'] ) && isset( $_SERVER['PHP_AUTH_PW'] ) && !empty( $_SERVER['PHP_AUTH_USER'] ) ){
			$credentials = base64_encode( $_SERVER['PHP_AUTH_USER'].':'.$_SERVER['PHP_AUTH_PW'] );
			$args['headers'] = array(
				'Authorization' => sanitize_text_field( 'Basic '.$credentials )
			);
		}
	}
	$args['headers']['Accept-Encoding'] = 'gzip, deflate';
	$args['cookies'] = $cookies;
	return $args;
}
