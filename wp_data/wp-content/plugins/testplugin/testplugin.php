<?php
/*
Plugin Name: Testplugin 01 
Description: A great plugin
*/
/* Start Adding Functions Below this Line */

function create_posttype() {
  
    register_post_type( 'idea',
    // CPT Options
        array(
            'labels' => array(
                'name' => __( 'Ideen' ),
                'singular_name' => __( 'Idee' ),
                'add_new'               => __( 'Idee anlegen', TRANSLATION_CONST ),
                'add_new_item'          => __( 'Idee anlegen', TRANSLATION_CONST ),
            ),
            'supports' => array( 
                'title' 
                //'editor'//, 
                // 'excerpt', 
                // 'thumbnail',  
                // 'revisions' 
            ),
            

            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'ideas'),
            'show_in_rest' => false,
            
  
        )
    );
}
// Hooking up our function to theme setup
add_action( 'init', 'create_posttype' );
/* Stop Adding Functions Below this Line */

/* Stop Adding Functions Below this Line */

?>