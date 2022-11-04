<?php
/*
 Plugin Name: Create Idea
 Description: Erste entwickelt Plugin, nur wie Test benutzt!
 Version: 1.0
 Author: Amela
 */

// add_action( $hook, $function_to_add, $priority, $accepted_args );

function create_idea()
{
    $content = '';

    $content .= '<form method= "post" action="http://localhost:8000/thank-you/">';

    $content .= '<input type="text" name="title" placeholder="Title of your idea" />';
    $content .= '<br />';

    $content .= '<input type="text" name="category" placeholder="Category" />';
    $content .= '<br />';

    $content .= '<input type="text" name="Subcategories" placeholder="Subcategories" />';
    $content .= '<br />';

    $content .= '<input type="text" name="short_description" placeholder="Short Description" />';
    $content .= '<br />';

    $content .= '<input type="text" name="long_description" placeholder="Long Description" />';
    $content .= '<br />';

    $content .= '<input type="submit" name="create_idea_submit_form" value="CREATE"/>';

    $content .= '</form>';

    return $content;

}
add_shortcode('create_idea_form', 'create_idea');

function set_html_content_type(){
    return 'text/html';
}


function create_idea_capture()
{
    global $post,$wpdb;

    if(array_key_exists('create_idea_submit_form', $_POST))
    {

       /* $body = '';
        $to = "amela_muzaferija@hotmail.com"; */

        $title = $_POST['title']; /*'<br />' */
        $category = $_POST['category'];
        $subcategories = $_POST['Subcategories'];
        $short_description = $_POST['short_description'];
        $long_description =  $_POST['long_description'];

        add_filter('wp_mail_content_type', 'set_html_content_type');

       /* wp_mail($to,$body); */

        remove_filter('wp_mail_content_type', 'set_html_content_type');

        /* Insert the information into a comment

        $time = current_time('mysql');

        $data = array(
        'comment_post_ID' => $post->ID,
        'comment_content' => $body,
        'comment_author_IP' => $_SERVER['REMOTE_ADDR'],
        'comment_date' => $time,
        'comment_approved' => 1,
        );

        wp_insert_comment($data);*/
        $insertData = $wpdb->get_results(" INSERT INTO ".$wpdb->prefix."form_submissions (title, category, subcategories, short_description, long_description) VALUES ('".$title."', '".$category."', '".$subcategories."', '".$short_description."' ,'".$long_description."')");
        /*$insertData1 = $wpdb->get_results(" INSERT INTO ".$wpdb->prefix."form_submissions (category) VALUES ('".$category."')"); */
       /* echo "<pre>";print_r($insertData);echo "</pre>";*/

    }

}
add_action('wp_head', 'create_idea_capture');

