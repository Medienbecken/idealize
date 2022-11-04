<?php
namespace Frontend_WP;

use Elementor\Core\Base\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class FEA_Dashboard_Settings{
		/**
	* Redirect non-admin users to home page
	*
	* This function is attached to the ‘admin_init’ action hook.
	*/
	public function redirect_non_admin_users() {
		$current_user = wp_get_current_user(); 
		if ( is_admin() && ! current_user_can( 'manage_options' ) && ! ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			$redirect_user = false;
			$hide_by_option = acf_decode_choices( get_option( 'frontend_admin_hide_by' ) );
			$login_redirect = get_option( 'frontend_admin_login_redirect' ); 
			if( ! $login_redirect ) $login_redirect = home_url();

			if( in_array( 'user', $hide_by_option ) && get_user_meta( $current_user->ID, 'hide_admin_area', true )  ){
				$redirect_user = true;
			}
			if( in_array( 'role', $hide_by_option ) ){
				$user_roles = $current_user->roles;
				$user_role = array_shift($user_roles);
				$redirect_roles = acf_decode_choices( get_option( 'frontend_admin_roles' ) ); 
				if( in_array( $user_role, $redirect_roles ) ){
					$redirect_user = true;
				}
			} 
			

			if( $redirect_user ){
				wp_redirect( $login_redirect );
				exit;
			}
		}
	}
	
	public function hide_admin_bar() {
		$current_user = wp_get_current_user(); 
		$hide_by_option = acf_decode_choices( get_option( 'frontend_admin_hide_by' ) );
		$hide_admin_bar = false; 

		if( in_array( 'user', $hide_by_option ) ){
			if( get_user_meta( $current_user->ID, 'hide_admin_area', true )  ){
				$hide_admin_bar = true;
			}
		}
		if( in_array( 'role', $hide_by_option ) ){
			$user_roles = $current_user->roles;
			$user_role = array_shift($user_roles);
			$redirect_roles = acf_decode_choices( get_option( 'frontend_admin_roles' ) ); 
			if( in_array( $user_role, $redirect_roles ) ){
				$hide_admin_bar = true;
			}
		} 

		if( $hide_admin_bar ) add_filter( 'show_admin_bar', '__return_false' );
	}
	
	function hide_admin_area_option( $user ) {
		$hide_by_option = acf_decode_choices( get_option( 'frontend_admin_hide_by' ) ); 

		if( ! in_array( 'user', $hide_by_option ) ) return;

		global $current_user; 
		$checked = ( isset ( $user->hide_admin_area ) && $user->hide_admin_area ) ? ' checked="checked"' : '';

		echo '<h3>' . __( 'Hide WordPress Admin Area', FEA_NS ) . '</h3>
			<table class="form-table">
				<tr>
					<th><label for="hide_admin_area">' . __( 'Hide Admin Area', FEA_NS ) . '</label></th>
					<td><input name="hide_admin_area" type="checkbox" id="hide_admin_area" value="1"' . $checked . '></td>
				</tr>
			</table>';		
	}
	

	function hide_admin_area_update_action($user_id) {
		$hide_admin = isset( $_POST['hide_admin_area'] );
	  	update_user_meta( $user_id, 'hide_admin_area', $hide_admin );
	}

	public function get_settings_fields( $field_keys ){
		$local_fields = array(
			'frontend_admin_hide_in_admin_bar' => array(
				'label' => __( 'Hide "Frontend Admin" in Admin Bar', FEA_NS ),
				'type' => 'true_false',
				'default_value' => 1,
				'ui' => 1,
				'instructions' => '',
			),
			'frontend_admin_dashboard_slug' => array(
				'label' => __( 'Frontend Dashboard Slug', FEA_NS ),
				'type' => 'text',
				'instructions' => '',
				'placeholder' => 'frontend-dashboard',
				'wrapper' => array(
					'class' => 'post-slug-field',
				),
			),
			'frontend_admin_hide_wp_dashboard' => array(
				'label' => __( 'Hide WP Dashboard', FEA_NS ),
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'only_front' => 0,
				'message' => '',
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			'frontend_admin_hide_by' => array(
				'label' => __( 'Hide by....', FEA_NS ),
				'type' => 'checkbox',
				'instructions' => __( 'If you choose "User", there will be a checkbox in each user\'s profile page to show/hide the WP dashboard', FEA_NS ),
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'frontend_admin_hide_wp_dashboard',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'only_front' => 0,
				'choices' => array(
					'user' => __( 'User', FEA_NS ),
					'role' => __( 'Role', FEA_NS ),
				),
				'allow_custom' => 0,
				'default_value' => array(),
				'layout' => 'horizontal',
				'toggle' => 0,
				'return_format' => 'value',
				'save_custom' => 0,
			),
			'frontend_admin_roles' => array(
				'label' => __( 'Roles', FEA_NS ),
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'frontend_admin_hide_wp_dashboard',
							'operator' => '==',
							'value' => '1',
						),
						array(
							'field' => 'frontend_admin_hide_by',
							'operator' => '==',
							'value' => 'role',
						),
					),
				),			
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'only_front' => 0,
				'choices' => feadmin_get_user_roles( ['administrator'] ),
				'allow_null' => 0,
				'multiple' => 1,
				'ui' => 1,
				'ajax' => 0,
				'return_format' => 'value',
				'placeholder' => '',
			),
			'frontend_admin_login_redirect' => array(
				'label' => __( 'Redirect to', FEA_NS ),
				'type' => 'url',
				'instructions' => __( 'Where to redirect users when logging in. Defaults to home.', FEA_NS ),
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'frontend_admin_hide_wp_dashboard',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'only_front' => 0,
				'placeholder' => get_site_url(),
			),
		);

		return $local_fields;
	} 
	
	public function __construct() {
		$this->redirect_non_admin_users();
		add_action( 'init', [ $this, 'hide_admin_bar'] );
		
		add_action( 'show_user_profile', [ $this, 'hide_admin_area_option'] );
		add_action( 'edit_user_profile', [ $this, 'hide_admin_area_option'] );
		
		add_action( 'personal_options_update', [ $this, 'hide_admin_area_update_action'] );
		add_action( 'edit_user_profile_update', [ $this, 'hide_admin_area_update_action'] );
		add_filter( FEA_PREFIX.'/dashboard_fields', [ $this, 'get_settings_fields'] );

	}
	
}
new FEA_Dashboard_Settings( $this );	