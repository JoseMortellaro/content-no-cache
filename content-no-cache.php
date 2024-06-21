<?php
/*
Plugin Name: Content No Cache
Description: Prevent page cache from loading dynamic content
Author: Jose Mortellaro
Author URI: https://josemortellaro.com
Domain Path: /languages/
Text Domain: content-no-cache
Version: 0.1.1
*/
/*  This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.
*/

defined( 'ABSPATH' ) || exit; // Exit if accessed directly

//Definitions
define( 'EOS_CONTENT_NO_CACHE_PLUGIN_DIR', untrailingslashit( dirname( __FILE__ ) ) );

add_action( 'after_setup_theme',function(){
  //add costum post non-cachable content
  $public =
    ( isset( $_GET['et_fb'] ) && 1 === absint( $_GET['et_fb'] ) )
    || ( isset( $_GET['cnc'] ) && md5( ABSPATH ) === sanitize_text_field( $_GET['cnc'] ) );
  register_post_type( 'eos_dyn_content', array(
    'label' => __( 'Content No Cache','exclude-content-from-cache' ),
    'labels' => array(
      'singular_name' => __( 'Content No Cache','exclude-content-from-cache' ),
      'add_new_item' => __( 'Add a new non-cachable content','exclude-content-from-cache' ),
      'edit_item' => __( 'Edit non-cachable content','exclude-content-from-cache' ),
      'new_item' => __( 'New non-cachable content','exclude-content-from-cache' ),
      'view_item' => __( 'Show','exclude-content-from-cache' ),
      'search_items' => __( 'Search for non-cachable content','exclude-content-from-cache' ),
      'not_found' => __( 'No non-cachable contents were found','exclude-content-from-cache' ),
      'not_found_in_trash' => __( 'No non-cachable contents were found in Trash','exclude-content-from-cache' ),
    ),
    'public' => $public,
    'show_ui' => true,
    'show_in_admin_bar' => true,
    'menu_icon' => 'dashicons-tagcloud',
    'show_in_nav_menus' => false,
    'capability_type' => 'post',
    'capabilities' => array(
      'publish_posts' => 'delete_others_pages',
      'edit_posts' => 'delete_others_pages',
      'edit_others_posts' => 'delete_others_pages',
      'delete_posts' => 'delete_others_pages',
      'delete_others_posts' => 'delete_others_pages',
      'read_private_posts' => 'delete_others_pages',
      'edit_post' => 'delete_others_pages',
      'delete_post' => 'delete_others_pages',
      'read_post' => 'delete_others_pages',
    ),
    'has_archive' => false,
    'exclude_from_search' => true,
    'rewrite' => array(
      'slug' => 'eos_dyn_content'
    ),
    'query_var' => false,
    'publicly_queryable'  => $public,
    'supports' => array(
      'title',
      'editor',
      'revisions',
    )
  ) );
},10 );

if( wp_doing_ajax() && isset( $_REQUEST['action'] ) && false !== strpos( sanitize_text_field( $_REQUEST['action'] ),'eos_dyn_') ){
  require_once EOS_CONTENT_NO_CACHE_PLUGIN_DIR.'/inc/eos-dyn-ajax.php';
}

add_shortcode( 'content_no_cache','eos_dyn_content_shortcode' );
add_shortcode( 'no_cache_content','eos_dyn_content_shortcode' );
//Shortcode output
function eos_dyn_content_shortcode( $atts ){
  if( !isset( $atts['id'] ) ) return;
  $output = '<div class="eos-dyn-content eos-dyn-content-'.esc_attr( $atts['id'] ).'" data-id="'.esc_attr( $atts['id'] ).'"></div>';
  $output .= eos_dyn_inline_script( esc_attr( $atts['id'] ), isset( $atts['current_user'] ) && in_array( $atts['current_user'],array( 'true','yes','1' ) ), isset( $atts['vars'] ) ? $atts['vars'] : false );
  $output .= '<script>';
  $output .= 'var id="'.esc_js( esc_attr( $atts['id'] ) ) . '"';
  $output .= isset( $atts['request'] ) ? ',request="'.esc_js( esc_attr( $atts['request'] ) ).'"' : ',request="content"';
  $output .= isset( $atts['vars'] ) ? ',vars="'.esc_js( esc_attr( $atts['vars'] ) ).'";' : ',vars=false;';
  $output .= 'eos_dyn_get_content(id,document.getElementsByClassName("eos-dyn-content-" + id),request);</script>';
  return $output;
}

function eos_dyn_inline_script( $cnc_id, $current_user = false, $vars = false ){
  $output = '<style id="cnc-css">.eos-dyn-content{min-height:120px;background-image:url('.includes_url( '/images/spinner.gif' ).');background-repeat:no-repeat;background-size:32px 32px;background-position:center}</style>';
  $output .='<script id="eos-dyn-js">';
  $output .= 'window.cnc_evt = new Event("content_no_cache_added");';
  $output .= 'if(typeof(eos_dyn_get_content) !== "function") {';
  $output .= 'function eos_dyn_get_content(id,els,request){';
  $output .= 'var req = new XMLHttpRequest(),fd=new FormData();';
  $output .= 'req.withCredentials = true;';
  $output .= 'req.onload = function(e){';
  $output .= 'if(this.readyState === 4 && "" !== e.target.responseText){';
  $output .= 'document.body.className = document.body.className.replace(" cnc-added","") + " cnc-added";';
  $output .= 'for(i in els){';
  $output .= 'els[i].outerHTML = e.target.responseText;';
  $output .= 'document.dispatchEvent(window.cnc_evt);';
  $output .= '}}};';
  $output .= 'fd.append("id",id);';
  if( $vars && ! empty( $vars ) ) {
    foreach( explode( ',', $vars ) as $var ) {
      $output .= 'fd.append("' . esc_js( esc_attr( $var ) ) . '", "' . sanitize_text_field( apply_filters( 'content_no_cache_var_' . sanitize_key( $var ), '', $cnc_id ) ) . '");';
    }
  }
  $output .= 'fd.append("request",request);';
  if( $current_user ){
    $output .= 'fd.append("current_user","true");';
    $output .= 'fd.append("headers",'.wp_json_encode( getallheaders() ).');';
  }
  $output .= 'req.open("POST","'.esc_js( admin_url( 'admin-ajax.php' ) ).'?action=eos_dyn_get_content",true);';
  $output .= 'req.send(fd);';
  $output .= '}';
  $output .= '}';
  $output .= '</script>';
  return $output;
}

if( is_admin() ){
  add_filter('manage_eos_dyn_content_posts_columns', 'eos_dyn_columns_head');
  add_action( 'manage_eos_dyn_content_posts_custom_column', 'eos_dyn_columns_content', 10, 2 );
  add_filter( 'plugin_action_links_content-no-cache/content-no-cache.php', 'eos_dyn_plugin_add_settings_link' );
  //Register Meta box for shortcode
  add_action( 'add_meta_boxes', function() {
    add_meta_box( 'eos-content-no-cache',esc_html__( 'Shortcode','content-no-cache' ),'eos_dyn_metabox','eos_dyn_content','normal','high' );
  } );
  add_filter( 'eos_dp_integration_action_plugins','eos_dyn_add_fdp_integration' );
}

//It adds a settings link to the action links in the plugins page
function eos_dyn_plugin_add_settings_link( $links ) {
    $settings_link = '<a class="eos-cnc-setts" href="'.esc_attr( admin_url( 'post-new.php?post_type=eos_dyn_content' ) ).'">'.esc_html__( 'Create no-cache content','content-no-cache' ).'</a>';
    $settings_link .= ' | <a class="eos-cnc-upgrade" style="color:#B07700;font-weight:bold" target="_cnc_pro" rel="noopener" href="https://shop.josemortellaro.com/downloads/content-no-cache/">'. __( 'Upgrade','content-no-cache' ). ' <span style="position:relative;top:-10px;' . ( is_rtl() ? 'right' : 'left' ) . ':-6px;display:inline-block">👑</span></a>';
    array_push( $links, $settings_link );
    return $links;
}

//Set the content for the added column in the form table lists
function eos_dyn_columns_content( $column_name,$post_ID ){
  if( $column_name == 'shortcode' ){
    echo ' [content_no_cache id="'.esc_attr( $post_ID ).'"]';
  }
}

//Add new column to forms table list
function eos_dyn_columns_head( $columns ){
  return array_merge( $columns,array(
    'shortcode' => esc_html__( 'Shortcode','content-no-cache' ),
  ) );
  return $columns;
}

//Callback to display the shortcode in the metabox field
function eos_dyn_metabox( $post){
  ?>
  <p><?php esc_html_e( 'Use the following shortcode to include the content:','content-no-cache' ); ?></p>
  <strong style="font-size:30px"><?php eos_dyn_columns_content( 'shortcode',$post->ID ); ?></strong>
  <?php
}

//It adds custom ajax actions to the FDP Actions Settings Pages
function eos_dyn_add_fdp_integration( $args ){
  $args['content-no-cache'] = array(
      'is_active' => defined( 'EOS_CONTENT_NO_CACHE_PLUGIN_DIR' ),
      'ajax_actions' => array( 'eos_dyn_get_content' => array( 'description' => __( 'Getting content','content-no-cache' ) ) )
  );
  return $args;
}

if( isset( $_GET['cnc'] ) && md5( ABSPATH ) === sanitize_text_field( $_GET['cnc'] ) ){
  add_filter( 'template_include',function( $template ){
    $cnc_template = EOS_CONTENT_NO_CACHE_PLUGIN_DIR.'/inc/eos-dyn-template.php';
    if( file_exists( $cnc_template ) ){
      return $cnc_template;
    }
    return $template;
  } );
}

add_action( 'after_setup_theme',function() {
  if( defined( 'ET_BUILDER_THEME' ) || defined( 'ET_BUILDER_PLUGIN_DIR' ) ){
    require EOS_CONTENT_NO_CACHE_PLUGIN_DIR.'/integrations/cnc-divi.php';
  }
},20 );
