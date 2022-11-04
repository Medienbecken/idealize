<?php

global $post, $form, $fea_instance;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$data_types = array(
    'none' => __( 'Submission Only', FEA_NS ),
    'post' => __( 'Post', FEA_NS ),
    'user' => __( 'User', FEA_NS ),
    'term' => __( 'Term', FEA_NS ),
    'options' => __( 'Site Options', FEA_NS ),
);
if ( class_exists( 'woocommerce' ) ){
    $data_types['product'] = __( 'Product', FEA_NS );
}

$form_fields = array();

$args = array(
    'post_type' => 'acf-field',
    'posts_per_page' => '-1',
    'post_parent' => $post->ID,
    'fields' => 'ids',
    'orderby' => 'menu_order', 
    'order' => 'ASC'
);

$fields_query = get_posts( $args );

if ( $fields_query ) {
    foreach( $fields_query as $field ){
        $form_fields[] = acf_get_field( $field );
    }
}else{
    $form_type = get_post_meta( $post->ID, 'admin_form_type', true );
    if( $form_type != 'general' ){
        $create_fields = explode( '_', $form_type );
        if( ! empty( $create_fields[1] ) ){
            $action = $create_fields[0];
            $data_type = $create_fields[1];
        }
    }
    if( ! empty( $data_type ) ){
        $form_fields = $fea_instance->local_actions[$data_type]->get_default_fields( $post->ID, $action );
    }
}
global $frontend_admin_field_types;

// get fields
$view = array(
    'fields'	=> $form_fields,
    'parent'	=> 0
);

ob_start();
$fea_instance->form_builder->get_view('form-field-objects', $view);
$field_objects = ob_get_contents();
ob_end_clean();	

$fields = array(
    array(
        'key' => 'custom_fields_wrapper',
        'field_label_hide' => 1,
        'type' => 'message',
        'instructions' => '',
        'new_lines' => '',
        'message' => '<div class="inside">'.$field_objects.'</div>',
        'php_code' => '1',
        'wrapper' => array(
            'width' => '',
            'class' =>'',
            'id' => 'acf-field-group-fields'
        )
    ),
    
);    

$fields = array_merge( $fields, array(
    array(
        'key' => 'default_submit_button',
        'label' => __( 'Submit Button', FEA_NS ),
        'type' => 'true_false',
        'required' => 0,
        'instructions' => '',
        'default_value' => 1,
        'conditional_logic' => 0,
        'message' => '',
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
    ),
    array(
        'key' => 'submit_value',
        'label' => '',
        'field_label_hide' => true,
        'type' => 'text',
        'instructions' => __( 'Will not show if a submit button field exists', FEA_NS ),
        'required' => 0,
        'default_value' => __( 'Submit', FEA_NS ),
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'default_submit_button',
                    'operator' => '==',
                    'value' => '1',
                ),
            ),
        ),
        'placeholder' => '',
    ),
    array(
        'key' => 'custom_fields_save',
        'label' => __( 'Save Custom Fields to...', FEA_NS ),
        'field_label_hide' => 0,
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'choices' => $data_types,
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => '',
        'wrapper' => array(
            'width' => '75',
            'class' =>'',
            'id' => ''
        )
    )
) );

return $fields;