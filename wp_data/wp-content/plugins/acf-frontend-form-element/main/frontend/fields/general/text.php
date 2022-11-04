<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists('acf_frontend_text_field') ) :

	class acf_frontend_text_field {

        public function update_dynamic_value( $value, $post_id = false, $field = false ){
            if( empty( $field['default_value'] ) || ! is_string( $field['default_value'] ) ) return $value;

            if( ! $field['value'] && isset( $field['default_value'] ) && is_string( $field['default_value'] ) && strpos( $field['default_value'], '[' ) !== false ){
				$dynamic_value = fea_instance()->dynamic_values->get_dynamic_values( $field['default_value'] );
                if( $dynamic_value ) $value = $dynamic_value;
			}  

			return $value;
		}


		public function __construct() {
            add_filter( 'acf/update_value', array( $this, 'update_dynamic_value' ), 17, 3 );
        
		}
	}

	new acf_frontend_text_field();

endif;

