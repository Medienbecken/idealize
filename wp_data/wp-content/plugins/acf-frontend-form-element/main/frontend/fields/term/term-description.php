<?php

if( ! class_exists('acf_field_term_description') ) :

class acf_field_term_description extends acf_field_textarea {
	
	
	/*
	*  initialize
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
		$this->name = 'term_description';
		$this->label = __("Term Description",FEA_NS);
        $this->category = __( 'Term', FEA_NS );
		$this->defaults = array(
            'data_name'     => 'term_description',
			'default_value'	=> '',
			'new_lines'		=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'rows'			=> ''
		);
        add_filter( 'acf/update_value/type=' . $this->name,  [ $this, 'pre_update_value'], 9, 3 );      
		
	}

    function prepare_field( $field ){
        $field['type'] = 'textarea';
        return $field;
    }

    function load_value( $value, $post_id = false, $field = false ){
        if( strpos( $post_id, 'term_' ) !== false ){
            $term_id = explode( '_', $post_id )[1];
            $edit_term = get_term( $term_id );
            if( isset( $edit_term->description ) ){
                $value = $edit_term->description;
            }
        }
        return $value;
    }

  function load_field( $field ){
      $field['name'] = $field['type'];
      return $field;
}
  function pre_update_value( $value, $post_id = false, $field = false ){
        $term_id = explode( '_', $post_id )[1];
        $edit_term = get_term( $term_id );
        if( ! is_wp_error( $edit_term ) ){
            $update_args = array( 'description' => $value );
            remove_action( 'acf/save_post', '_acf_do_save_post' );
            wp_update_term( $term_id, $edit_term->taxonomy, $update_args );
            add_action( 'acf/save_post', '_acf_do_save_post' );
        }
     
        return $value;
    }

    public function update_value( $value, $post_id = false, $field = false ){
        return null;
    }

}

// initialize
acf_register_field_type( 'acf_field_term_description' );

endif;
	
?>