<?php
namespace Frontend_WP;

use Elementor\Core\Base\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FEA_Google_API_Settings{
		/**
	* Redirect non-admin users to home page
	*
	* This function is attached to the ‘admin_init’ action hook.
	*/


	public function get_settings_fields( $field_keys ){
		$local_fields = array(
            'frontend_admin_google_maps_api' => array(
                'label' => __( 'Google Maps API Key', FEA_NS ),
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'wrapper' => array(
                    'width' => '50.1',
                    'class' => '',
                    'id' => '',
                ),
            ),
        );

        $site_key = get_option( 'frontend_admin_google_recaptcha_site' );
        $secret_key = get_option( 'frontend_admin_google_recaptcha_secret' );

        if ( fea_instance()->is__premium_only() ) {
            $local_fields = apply_filters( FEA_PREFIX . '/api_settings', $local_fields );
        }

        $local_fields = array_merge( $local_fields, array(
            'google_recaptcha_message' => array(
                'label' => '',
                'type' => 'message',
                'message' => sprintf( __( '<a href="%s" target="_blank">reCAPTCHA</a> is a free service by Google that protects your website from spam and abuse. It does this while letting your valid users pass through with ease.', 'elementor-pro' ), 'https://www.google.com/recaptcha/intro/v3.html' ),
                'instructions' => '',
                'required' => 0,
                'wrapper' => array(
                    'width' => '50.1',
                    'class' => '',
                    'id' => '',
                ),
            ),

            'fea_recapthca_V2' => array(
                'label' => __( 'V2', FEA_NS ),
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            'frontend_admin_recaptcha_site_v2' => array(
                'label' => __( 'Google reCaptcha Site Key', FEA_NS ),
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'default_value' => $site_key,
                'wrapper' => array(
                    'width' => '50.1',
                    'class' => '',
                    'id' => '',
                ),
            ),
            'frontend_admin_recaptcha_secret_v2' => array(
                'label' => __( 'Google reCaptcha Secret Key', FEA_NS ),
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'default_value' => $secret_key,
                'wrapper' => array(
                    'width' => '50.1',
                    'class' => '',
                    'id' => '',
                ),
            ),
            'fea_recapthca_V3' => array(
                'label' => __( 'V3', FEA_NS ),
                'type' => 'tab',
                'instructions' => '',
                'required' => 0,
                'wrapper' => array(
                    'width' => '',
                    'class' => '',
                    'id' => '',
                ),
                'placement' => 'top',
                'endpoint' => 0,
            ),
            'frontend_admin_recaptcha_site_v3' => array(
                'label' => __( 'Google reCaptcha Site Key', FEA_NS ),
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'default_value' => $site_key,
                'wrapper' => array(
                    'width' => '50.1',
                    'class' => '',
                    'id' => '',
                ),
            ),
            'frontend_admin_recaptcha_secret_v3' => array(
                'label' => __( 'Google reCaptcha Secret Key', FEA_NS ),
                'type' => 'text',
                'instructions' => '',
                'required' => 0,
                'default_value' => $secret_key,
                'wrapper' => array(
                    'width' => '50.1',
                    'class' => '',
                    'id' => '',
                ),
            ),
        ) );
        
		return $local_fields;
	} 
	public function frontend_admin_update_maps_api() {	
        acf_update_setting( 'google_api_key', get_option( 'frontend_admin_google_maps_api' ) );
    }

	public function __construct() {
        add_filter( FEA_PREFIX.'/apis_fields', [ $this, 'get_settings_fields'] );
        
        add_action( 'acf/init', [ $this, 'frontend_admin_update_maps_api'] );
	}
	
}

new FEA_Google_API_Settings( $this );