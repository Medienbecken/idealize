<?php
/*
Plugin Name: SVG Uploads 
Description: Adds SVG support
Version: 1.0
Author: Benjamin Gierlich
Plugin URI: https://digital-leap.de/wordpress-svg-upload/
*/

/**
 * Add svg support
 **/
add_filter( 'wp_check_filetype_and_ext', function( $data, $file, $filename, $mimes) {
      global $wp_version;
      if( $wp_version == '4.7' || ( (float) $wp_version < 4.7 ) ) {
      return $data;
    }
    $filetype = wp_check_filetype( $filename, $mimes );
      return [
      'ext'             => $filetype['ext'],
      'type'            => $filetype['type'],
      'proper_filename' => $data['proper_filename']
    ];
}, 10, 4 );

function dl_mime_types( $mimes ){
   $mimes['svg'] = 'image/svg+xml';
   return $mimes;
}
add_filter( 'upload_mimes', 'dl_mime_types' );

function dl_fix_svg() {
  echo '<style type="text/css">.attachment-266x266, .thumbnail img { width: 100% !important; height: auto !important;} </style>';
}
add_action( 'admin_head', 'dl_fix_svg' );

?>