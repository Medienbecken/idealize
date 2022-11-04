<?php
/**
 * Osom Modal Login
 *
 * Plugin Name:       Osom Modal Login
 * Plugin URI:        https://osompress.com
 * Description:       Osom Modal Login lets you easily create a modal box displaying the WordPress login form. It automatically adds a menu item named "Login" at the end of the selected menu which will launch the login modal box one you click on it.
 * Version:           1.1.2
 * Author:            OsomPress
 * Author URI:        https://osompress.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       osom-ml
 * Domain Path:       /languages
 */

namespace osom\Osom_Modal;

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//  Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'OSOM_ML_VERSION', '1.1.2' );

/**
 * Load required files.
 */
function osom_ml_init() {

	require dirname( __FILE__ ) . '/inc/osom-admin.php';

}

add_action( 'init', __NAMESPACE__ . '\osom_ml_init' );

function osom_ml_load_textdomain() {
	load_plugin_textdomain( 'osom-ml', false, basename( __DIR__ ) . '/languages' );
}

add_action( 'init', __NAMESPACE__ . '\osom_ml_load_textdomain' );

function osom_ml_styles() {
	$plugin_url = plugin_dir_url( __FILE__ );
	wp_enqueue_style( 'osom-ml-style', $plugin_url . '/assets/css/osom-modal-login.css', OSOM_ML_VERSION, true );
	wp_enqueue_style( 'dashicons' );
}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\osom_ml_styles' );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\osom_ml_styles' );

function osom_ml_scripts() {
	$plugin_url = plugin_dir_url( __FILE__ );
	wp_enqueue_script( 'osom-ml-script', $plugin_url . '/assets/js/osom-modal-login.js', '', OSOM_ML_VERSION, true );

}

add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\osom_ml_scripts' );

// Redirect to plugin settings after activation
register_activation_hook( __FILE__, __NAMESPACE__ . '\osom_ml_activate' );
add_action( 'admin_init', __NAMESPACE__ . '\osom_ml_redirect' );

function osom_ml_activate() {
	add_option( 'osom_ml_do_activation_redirect', true );
}

function osom_ml_redirect() {
	if ( get_option( 'osom_ml_do_activation_redirect', false ) ) {
		delete_option( 'osom_ml_do_activation_redirect' );
		if ( ! isset( $_GET['activate-multi'] ) ) {
			wp_redirect( home_url() . '/wp-admin/admin.php?page=osom_ml_main_menu' );
		}
	}
}

// Create the login modal box
add_action( 'wp_footer', __NAMESPACE__ . '\osom_modal_login' );

function osom_modal_login() {

	$settings      = get_option( 'osom_ml_settings' );

	if ( ! isset( $settings['lostpassword'] ) ) {
		$settings['lostpassword'] = 0;
	}

	if ( ! isset( $settings['remember'] ) ) {
		$settings['remember'] = 0;
	}
	if ( ! isset( $settings['register'] ) ) {
		$settings['register'] = 0;
	}
	if ( ! isset( $settings['register'] ) ) {
		$settings['register'] = 0;
	}
	if ( ! isset( $settings['loginurl'] ) ) {
		$settings['loginurl'] = '';
	}
	if ( ! isset( $settings['tit'] ) ) {
		$settings['tit'] = '';
	}
	if ( ! isset( $settings['logouturl'] ) ) {
		$settings['logouturl'] = '';
	}
	if ( ! isset( $settings['register_url'] ) ) {
		$settings['register_url'] = '';
	}
	if ( ! isset( $settings['register_text'] ) ) {
		$settings['register_text'] = '';
	}

	$title         = $settings['tit'];
	$remember      = $settings['remember'];
	$lostpassword  = $settings['lostpassword'];
	$register      = $settings['register'];
	$register_url  = $settings['register_url'];
	$register_text = $settings['register_text'];

	if ( $settings['loginurl'] ) {

		$login_url = $settings['loginurl'];

	} elseif ( is_home() || is_404() ) {

		$login_url = '/';

	} else {

		$login_url = get_permalink();

	}

	if ( ! is_user_logged_in() ) :

		$args = array(
			'form_id'        => 'login',
			'echo'           => false,
			'label_username' => esc_html__( 'Username or Email Address' ),
			'password'       => esc_html__( 'Password' ),
			'label_log_in'   => esc_html__( 'Log In' ),
			'remember'       => $remember,
			'value_remember' => false,
			'redirect'       => $login_url,
		);

		$form = wp_login_form( $args );

		if ( $lostpassword ) {
			$form .= '<p class="login-forgot"><a href="' . wp_lostpassword_url() . '">' . esc_html__( 'Lost your password?' ) . '</a></p>';
		}

		if ( $register ) {
			$form .= '<a href="' . esc_url( $register_url ) . '">';
			$form .= esc_html( $register_text );
			$form .= '</a>';
		}
		?>

			<div class="login-modal-box" id="OMLlogin">
				<div class="modal-content">
					<span class="login-modal-close"><img src="<?php echo home_url();?>/wp-content/plugins/osom-modal-login/assets/img/close.svg"></span>
					<h4><?php echo esc_html( $title ); ?></h4>
					<?php
					if ( isset($_GET['login']) && 'failed' === $_GET['login'] ) {
						?>
						<div class="login-error">
							<?php

							echo esc_html__( 'Login failed: You have entered an incorrect Username or Password, please try again.', 'osom-ml' );
							?>
						</div>
					<?php } ?>
					<?php echo $form; ?>
				</div>
			</div>

			<?php
endif;
}

/**
 * Add shortcode
 *
 * @param [type] $atts
 * @param string $content text within link.
 * @return login link
 */
function osom_ml_shortcode( $atts, $content = null ) {

	return '<span class="alogin"><a href="#login" title="login" >' . $content . '</a></span>';

}

add_shortcode( 'osom-login', __NAMESPACE__ . '\osom_ml_shortcode' );

/**
 * If username or password is wrong redirect
 *
 * @param [type] $username
 * @return void
 */
function osom_login_fail( $username ) {

	$referrer = '';
	
	if( isset( $_SERVER['HTTP_REFERER'] )) { 
		$referrer = $_SERVER['HTTP_REFERER'];
	}

	if ( ! empty( $referrer ) && ! strstr( $referrer, 'wp-login' ) && ! strstr( $referrer, 'wp-admin' ) ) {

		$referrer = $_SERVER['HTTP_REFERER'];

		wp_safe_redirect( home_url() . '/?login=failed#login' );

		exit;
	}
}

add_action( 'wp_login_failed', __NAMESPACE__ . '\osom_login_fail' );

add_filter( 'wp_nav_menu_items', __NAMESPACE__ . '\osom_add_login', 10, 2 );

/**
 * Add login logout button.
 *
 * @param [type] $menu
 * @param [type] $args
 * @return void
 */
function osom_add_login( $menu, $args ) {

	$settings          = get_option( 'osom_ml_settings' );

	if ( ! isset( $settings['position'] ) ) {
		$settings['position'] = '';
	}
	if ( ! isset( $settings['logouturl'] ) ) {
		$settings['logouturl'] = '';
	}
	if ( ! isset( $settings['loginmenulabel'] ) ) {
		$settings['loginmenulabel'] = '';
	}
	if ( ! isset( $settings['logoutmenulabel'] ) ) {
		$settings['logoutmenulabel'] = '';
	}

	$login_location    = $settings['position'];
	$redirect_url      = $settings['logouturl'];
	$login_menu_label  = $settings['loginmenulabel'];
	$logout_menu_label = $settings['logoutmenulabel'];
	if ( '' === $redirect_url ) {
		$redirect_url = home_url();
	}

	$args           = (array) $args;
	$login_location = (array) $login_location;
	if ( ! in_array( $args['theme_location'], $login_location, true ) ) {
		return $menu;
	}

	$logout = '<li class="menu-item logout"><a href="' . wp_logout_url( $redirect_url ) . '" title="Logout">' . esc_attr( $logout_menu_label ) . '</a></li>';

	if ( is_home() ) {

		$login = '<li class="menu-item osmlogin"><a href="#login" title="Login">' . esc_attr( $login_menu_label ) . '</a></li>';

	} else {

		$login = '<li class="menu-item osmlogin"><a href="' . get_the_permalink() . '#login" title="Login">' . esc_attr( $login_menu_label ) . '</a></li>';
	}

	if ( has_filter( 'osom_add_logout_filter' ) ) {

		$logout = apply_filters( 'osom_add_logout_filter', $logout );

	}

	if ( has_filter( 'osom_add_login_filter' ) ) {

		$login = apply_filters( 'osom_add_login_filter', $login );

	}

	if ( is_user_logged_in() ) {

		return $menu . $logout;

	} else {

		return $menu . $login;
	}
}

// Uninstall plugin
register_uninstall_hook( __FILE__, 'osom_ml_uninstall_plugin' );

function osom_ml_uninstall_plugin() {

	$settings = get_option( 'osom_ml_settings' );
	delete_option( 'osom_ml_settings' );

}
