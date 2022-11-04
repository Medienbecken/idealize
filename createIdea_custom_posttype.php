<?php
/*
 Plugin Name: Create Idea Custom Post Type
 Plugin URI: https://www.hs-heilbronn.de/de/induko/idealize
 Description: Field to create an idea.
 Version: 1.0.o
 Author: Amela Grabus
 */


function createIdea_custom_posttype(){
    register_post_type('Example',
        array(
        'labels'=>array(
            'name'=>__('Create Idea'),
            'singular_name'=>__('Create Idea'),
            'add_new'=>__( 'Add New Idea'),
            'add_new'=>__( 'Add New Idea'),
            'add_new_item'=>__( 'Add New Idea'),
            'edit_item'=>__( 'Edit Idea'),
            'search_items'=>__('Search Idea')
            ),
        'menu_position'=>5,
        'public'=>true,
        'exclude_from_search'=>true,
        'has_archive'=>false,
        'register_meta_box_cb'=>'example_metabox',  
        'supports'=>array('title','editor','thumbnail')
        )
    );
}
add_action('init','createIdea_custom_posttype');

function example_metabox(){
    add_meta_box('example_metabox_customfields', 'Example Custom Fields', 'example_metabox_display','example','normal','high');
}

function example_metabox_display(){
    global $post;
    $sub_title = get_post_meta($post->ID, 'sub_title', true);
    $author_name = get_post_meta($post->ID, 'author_name', true);

    ?>
    <label>Sub Titel</label>
    <input type="text" name="sub_title" placeholder="Sub Title" class="wiedefat" value="<?php print $sub_title; ?>" />
    <br /><br />
    <label>Authors Name</label>
    <input type="text" name="author_name" placeholder="Authors Name" class="wiedefat" value="<?php print $author_name; ?>" />
    <?php
    }

function example_posttype_save($post_id)
{
   $is_autosave = wp_is_post_autosave($post_id);
   $is_revision = wp_is_post_revision($post_id);

   if($is_autosaven || $is_revision){
       return;
   }

   $post = get_post($post_id);
   if($post->post_type == "example"){
       if(array_key_exists('sub_title',$_POST)){
           update_post_meta($post_id,'sub_title',$_POST['sub_title']);
       }
       if(array_key_exists('author_name',$_POST)){
           update_post_meta($post_id,'author_name',$_POST['author_name']);
       }
   }
}
add_action('save_post','example_posttype_save');

function get_example_post_types(){
    $args = array(
            'postp_per_page' => -1,
            'post_type' => 'example'
            );
    $ourPosts = get_posts($args);
    print_r($ourPosts);


}
add_shortcode('get_example_posts','get_example_post_types');


remove_role( 'Example' );
?>
