<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$fields = array(	
    array(
        'key' => 'redirect',
        'label' => __( 'Redirect After Submit', FEA_NS ),
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'choices' => array(
            'current' => __( 'Reload Current Page', FEA_NS ),
            'custom_url' => __( 'Custom URL', FEA_NS ),
            'referer' => __( 'Referer', FEA_NS ),
            'post_url' => __( 'Post URL', FEA_NS ),
        ),
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => '',
    ),
    array(
        'key' => 'custom_url',
        'label' => __( 'Custom Url', FEA_NS ),
        'type' => 'url',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'redirect',
                    'operator' => '==',
                    'value' => 'custom_url',
                ),
            ),
        ),
        'placeholder' => '',
    ),
);

if( fea_instance()->is__premium_only() ){
    $fields[] = array(
        'key' => 'ajax_submit',
        'label' => __( 'No Page Reload', FEA_NS ),
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'redirect',
                    'operator' => '==',
                    'value' => 'current',
                ),
            ),
        ),
        'message' => '',
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
    );
    $fields[] =  array(
        'key' => 'redirect_action',
        'label' => __( 'After Reload', FEA_NS ),
        'type' => 'select',
        'instructions' => '',
        'required' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'choices' => array(
            'clear' => __( 'Clear Form', FEA_NS ),
            'edit' => __( 'Edit Form', FEA_NS ),
        ),
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'redirect',
                    'operator' => '==',
                    'value' => 'current',
                ),
                array(
                    'field' => 'ajax_submit',
                    'operator' => '==',
                    'value' => '1',
                ),
            ),
        ),
        'allow_null' => 0,
        'multiple' => 0,
        'ui' => 0,
        'return_format' => 'value',
        'ajax' => 0,
        'placeholder' => '',
    );
}

$fields = array_merge( $fields, array(
    array(
        'key' => 'show_update_message',
        'label' => __( 'Success Message', FEA_NS ),
        'type' => 'true_false',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'message' => '',
        'ui' => 1,
        'ui_on_text' => '',
        'ui_off_text' => '',
    ),
    array(
        'key' => 'update_message',
        'label' => '',
        'field_label_hide' => true,
        'type' => 'textarea',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => array(
            array(
                array(
                    'field' => 'show_update_message',
                    'operator' => '==',
                    'value' => '1',
                ),
            ),
        ),
        'placeholder' => '',
        'maxlength' => '',
        'rows' => '2',
        'new_lines' => '',
    ),
    array(
        'key' => 'error_message',
        'label' => '',
        'field_label_hide' => true,
        'type' => 'textarea',
        'instructions' => __( 'There shouldn\'t be any problems with the form submission, but if there are, this is what your users will see. If you are expeiencing issues, try and changing your cache settings and reach out to ', FEA_NS ) . 'support@frontendform.com',
        'required' => 0,
        'placeholder' => __( 'There has been an error. Form has been submitted successfully, but some actions might not have been completed.', FEA_NS ),
        'maxlength' => '',
        'rows' => '2',
        'new_lines' => '',
    ),
) );
    
if( fea_instance()->is__premium_only() ){
    $remote_actions = array();
    $action_layouts = array();
    foreach( fea_instance()->remote_actions as $name => $action ){
        $sub_fields = array( 
            array (
                'key' => 'action_id',
                'label' => __( 'Action Name', FEA_NS ),
                'name' => 'action_id',
                'type' => 'text',
                'instructions' => __( 'Give this action an identifier', FEA_NS ),
                'required' => 1,
                'default_value' => $action->get_label(),
                'conditional_logic' => 0,
                'wrapper' => array (
                    'width' => '70',
                    'class' => '',
                    'id' => '',
                ),
                'placeholder' => __( 'Action Name', FEA_NS ),
                'maxlength' => '100',
            ),
        );
        $sub_fields = array_merge( $sub_fields, $action->action_options() );
        $layouts[$name] = array(
            'key' => $name,
            'name' => $name,
            'label' => $action->get_label(),
            'display' => 'block',
            'sub_fields' => $sub_fields,
            'min' => '',
            'max' => '',
        );
    }	
    
    $default = array();
    global $form;
    if( ! empty( $form['emails'] ) ){
        foreach( $form['emails'] as $email ){
            $row = $email;
            $row['fea_block_structure'] = 'email';
            $row['action_id'] = $email['email_id'];
            $default[] = $row; 
        }
    }
    if( ! empty( $form['webhooks'] ) ){
        foreach( $form['webhooks'] as $webhook ){
            $row = $webhook;
            $row['fea_block_structure'] = 'webhook';
            $row['action_id'] = $webhook['webhook_id'];
            $default[] = $row; 
        }
    }

     $fields[] = array(
        'key' => 'submit_actions',
        'label' => __( 'Submit Actions', FEA_NS ),
        'type' => 'frontend_blocks',
        'instructions' => '',
        'required' => 0,
        'conditional_logic' => 0,
        'wrapper' => array(
            'width' => '',
            'class' => '',
            'id' => '',
        ),
        'layout_labels' => array(
            'remove' 	=> __( 'Remove Action', FEA_NS ),
            'add' 		=> __( 'Add Action', FEA_NS ),
            'duplicate'	=> __( 'Duplicate Action', FEA_NS ),
            'collapse' 	=> __( 'Click to Toggle', FEA_NS ),
            'button'	=> __( 'Add Action', FEA_NS ),
            'no_value'  => __( 'Click the button below to start adding actions', FEA_NS ),
        ),
        'frontend_admin_display_mode' => 'edit',
        'only_front' => 0,
        'default_value' => $default,
        'layouts' => $layouts,
        'button_label' => 'Add Row',
        'min' => '',
        'max' => '',
    );
}




return $fields;