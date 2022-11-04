<?php
namespace Frontend_WP;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if( ! class_exists( 'Frontend_WP_Gutenberg' ) ) :

	class Frontend_WP_Gutenberg{

	public function register_blocks() {
		$blocks = [ 'admin-form' => 'form',
					'admin-form-submissions' => 'submissions'			
				];

		foreach( $blocks as $block => $name ){
			register_block_type( __DIR__ . "/src/blocks/$block", [
				'render_callback' => [ $this, 'render_' . $name ],
			] );
		}
		
	}

	public function render_form($attr, $content) {
		$render = '';
		if ( $attr['formID'] == 0 ){
			return $render;
		}
		if ( get_post_type( $attr['formID'] ) == 'admin_form' ){
			ob_start();
			if( is_admin() ){
				$attr['editMode'] = true;
			}else{
				$attr['editMode'] = false;
			}
			fea_instance()->form_display->render_form( $attr['formID'], $attr['editMode'] );
			$render = ob_get_contents();
			ob_end_clean();	
		}
		return $render;
	}
	public function render_submissions($attr, $content) {
		$render = '';
		if ( $attr['formID'] == 0 ){
			return $render;
		}
		if ( get_post_type( $attr['formID'] ) == 'admin_form' ){
			ob_start();
			if( is_admin() ) $attr['editMode'] = true;
			fea_instance()->form_display->render_submissions( $attr['formID'], $attr['editMode'] );
			$render = ob_get_contents();
			ob_end_clean();	
		}
		if( ! $render ){
			return __( 'No Submissions Found', FEA_NS );
		}
		return $render;
	}
	public function render_text_field($attr, $content) {
		$render = '';
		$field = acf_get_valid_field( $attr );
	
		ob_start();
		fea_instance()->form_display->render_field_wrap( $field );
		$render = ob_get_contents();
		ob_end_clean();	
		return $render;
	}


	function add_block_categories( $block_categories ) {
		return array_merge(
			$block_categories,
			[
				[
					'slug'  => 'frontend-admin',
					'title' => esc_html__( FEA_TITLE, FEA_NS ),
					'icon'  => 'feedback', 
				],
			]
		);
	}

	public function __construct() {
		add_filter( 'block_categories_all', array( $this, 'add_block_categories' ) );
		add_action( 'init', array( $this, 'register_blocks' ) );
	}
}

fea_instance()->gutenberg = new Frontend_WP_Gutenberg();

endif;	