<?php
namespace Frontend_WP;

use Elementor\Core\Base\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FEA_Legacy_Settings{
		/**
	* Redirect non-admin users to home page
	*
	* This function is attached to the ‘admin_init’ action hook.
	*/


	public function get_settings_fields( $field_keys ){
		$local_fields = array(
			'fea_legacy_elementor' => array(
				'label' => __( 'Show Elementor widgets with legacy settings', FEA_NS ),
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
		);

		return $local_fields;
	} 

	public function legacy_elementor() {	
        $value = get_option( 'frontend_admin_version', 0 );
        if( $value ){
			update_option( 'fea_legacy_elementor', 1 );
        }
       
    }

	public function __construct() {
        add_filter( FEA_PREFIX.'/legacy_fields', [ $this, 'get_settings_fields'] );
		add_action( 'init', [ $this, 'legacy_elementor'] );

	}
	
}

new FEA_Legacy_Settings( $this );