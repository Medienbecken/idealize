<?php

if( ! class_exists('acf_field_save_progress') ) :

class acf_field_save_progress extends acf_field {
	
	
	/*
	*  __construct
	*
	*  This function will setup the field type data
	*
	*  @type	function
	*  @date	5/03/2014
	*  @since	5.0.0
	*
	*  @param	n/a
	*  @return	n/a
	*/
	
	function initialize() {
		
		// vars
		$this->name = 'save_progress';
		$this->label = __("Save Progress",FEA_NS);
		$this->category = __( 'Form', FEA_NS );
		$this->defaults = array(
			'button_text' => __( 'Save Progress', FEA_NS ),
            'field_label_hide' => 1,
		);
		
	}
	
	
	/*
	*  render_field()
	*
	*  Create the HTML interface for your field
	*
	*  @param	$field - an array holding all the field's data
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*/
	
	function render_field( $field ) {		

			if( ! empty( $field['show_success_message'] ) && $field['success_message'] ){
				$success = ' data-message="'.$field['success_message'].'"';
			}else{
				$success = '';
			}
			// vars
			$m = '<button type="button" class="fea-submit-button button button-primary" data-state="save"'.$success.'>' .$field['button_text']. '</button>';
					
			// wptexturize (improves "quotes")
			$m = wptexturize( $m );

			echo $m;
	}
	
	
	/*
	*  load_field()
	*
	*  This filter is appied to the $field after it is loaded from the database
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$field - the field array holding all the field options
	*/
	function load_field( $field ) {
		// remove name to avoid caching issue
		$field['name'] = '';
		
		// remove instructions
		$field['instructions'] = '';
		
		// remove required to avoid JS issues
		$field['required'] = 0;
		
		// set value other than 'null' to avoid ACF loading / caching issue
		$field['value'] = false;

		$field['field_label_hide'] = 1;

		if( empty( $field['button_text'] ) ){
			$field['button_text'] = $field['label'];
		}
		
		// return
		return $field;
	}

	function render_field_settings( $field ){
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Button Text', FEA_NS ),
			'type'			=> 'text',
			'name'			=> 'button_text',
			'class'			=> 'update-label',
		));
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Show Success Message', FEA_NS ),
			'type'			=> 'true_false',
			'ui' 			=> 1,
			'name'			=> 'show_success_message',
			'default_value' => 1,
		));
		acf_render_field_setting( $field, array(
			'label'			=> __( 'Success Message', FEA_NS ),
			'type'			=> 'textarea',
			'name'			=> 'success_message',
			'rows'			=> 3,
			'default_value' => __( 'Progress Saved', FEA_NS ),
			'conditions' => array(
                array(
                    array(
                        'field'     => 'show_success_message',
                        'operator'  => '==',
                        'value'     => 1,
                    ),
                ),
            )
		));
	}
	
}


// initialize
acf_register_field_type( 'acf_field_save_progress' );

endif; // class_exists check

?>