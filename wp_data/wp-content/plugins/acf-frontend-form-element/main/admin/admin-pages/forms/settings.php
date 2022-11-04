<?php
	
	
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
/**
 * Handles the admin part of the forms
 *
 * @since 1.0.0
 *
 */
class Frontend_Forms_UI {

	
	/**
	 * Adds a form key to a form if one doesn't exist
	 * 
	 * @since 1.0.0
	 *
	 */
	function save_post( $form_id, $post ) {

		 // do not save if this is an auto save routine
		if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) {
			return $form_id;
		} 
		
		// bail early if not acff form
		if( $post->post_type !== 'admin_form' && $post->post_type !== 'admin_template' ) {
			return $form_id;
		}
			// only save once! WordPress save's a revision as well.
			if( wp_is_post_revision($form_id) ) {
				return $form_id;
			}

			// verify nonce
			if( !acf_verify_nonce('admin_form') ) {
				return $form_id;
			}

			// disable filters to ensure ACF loads raw data from DB
			acf_disable_filters();
			
			// save fields
			if( !empty($_POST['acf_fields']) ) {
				
				// loop
				foreach( $_POST['acf_fields'] as $field ) {
					
					// vars
					$specific = false;
					$save = acf_extract_var( $field, 'save' );
					
					
					// only saved field if has changed
					if( $save == 'meta' ) {
						$specific = array(
							'menu_order',
							'post_parent',
						);
					}
					
					// set parent
					if( !$field['parent'] ) {
						$field['parent'] = $form_id;
					}

					// save field
					$field = acf_update_field( $field, $specific );

					
				}
			}
			
			
			// delete fields
			if( !empty( $_POST['_acf_delete_fields'] ) ) {
				
				// clean
				$ids = explode('|', $_POST['_acf_delete_fields']);
				$ids = array_map( 'intval', $ids );
				
				
				// loop
				foreach( $ids as $id ) {
					
					// bai early if no id
					if( !$id ) continue;
					
					
					// delete
					acf_delete_field( $id );
					
				}
				
			}

			if( ! empty( $_POST['form'] ) ){
				$form_key = get_post_meta( $form_id, 'form_key', 1 );
				if( ! $form_key ){
					$form_key = uniqid( 'form_' );
					update_post_meta( $form_id, 'form_key', $form_key );
				}
				if( isset( $_POST['form']['admin_form_type'] ) ){
					update_post_meta( $form_id, 'admin_form_type', $_POST['form']['admin_form_type'] );
				}

				$_POST['form']['ID'] = $form_id;
				$_POST['form']['title'] = $_POST['post_title'];
				$_POST['form']['status'] = $_POST['post_status'];
				$_POST['form']['slug'] = $_POST['post_name'];

				if( ! empty( $_POST['post_password'] ) ){
					$_POST['form']['password'] = $_POST['post_password'];
				}
				$this->update_form_post( $_POST['form'] );
			}				
	}

	public function update_form_post( $data = array() ) {		
		if( isset( $data['id'] ) ){
			$data_id = $data['id'];
		}else{
			$data_id = uniqid( 'form_' );
		}
		unset( $data['emails_to_send'][0] );

		if( ! empty( $data['submit_actions'] ) ){
			$submit_actions = array();
			$i = 0;
			if( is_array( $data['submit_actions'] ) ){
				foreach( $data['submit_actions'] as $key => $action ){
					$submit_actions[$i] = $action;
					$i++;
				}
			}
			$data['submit_actions'] = $submit_actions;
		}

		// may have been posted. Remove slashes
		$data = wp_unslash( $data );
		
		// parse types (converts string '0' to int 0)
		$data = acf_parse_types( $data );
		
		// extract some args
		$extract = acf_extract_vars($data, array(
			'ID',
			'id',
			'title',
			'status',
			'password',
			'slug',
			'menu_order',
			'fields',
			'active',
			'_valid'
		));
				
		// vars
		$data = maybe_serialize( $data );	
		
		if( isset( $extract['slug'] ) ){
			$slug = $extract['slug'];
		}else{
			$slug = sanitize_title($extract['title']);
		}
		
		// save
		$save = array(
			'ID'			=> $extract['ID'],
			'post_status'	=> $extract['status'],
			'post_password'	=> $extract['password'],
			'post_title'	=> $extract['title'],
			'post_excerpt'	=> $extract['ID'],
			'post_type'		=> 'admin_form',
			'post_name'		=> $slug,
			'post_content'	=> $data,
			'menu_order'	=> $extract['menu_order'],
		);
		
		// slash data
		// - WP expects all data to be slashed and will unslash it (fixes '\' character issues)
		$save = wp_slash( $save );
		
		
		// update the field group and update the ID
		if( ! empty( $data['ID'] ) ) {			
			wp_update_post( $save );
			$form_id = $data['ID'];
		} else {			
			$form_id = wp_insert_post( $save );
			
		}

		$form_key = get_post_meta( $form_id, 'form_key', 1 );
		if( ! $form_key ){
			update_post_meta( $form_id, 'form_key', $data_id );
		}
		
		// return
		return $save;
		
	}

	
	/**
	 * Displays the form key after the title
	 *
	 * @since 1.0.0
	 *
	 */
	function display_shortcode() {
		
		global $post;

		$type = $post->post_type;
		$form_id = $post->ID;

		if ( 'admin_form' == $type || 'admin_template' == $type ){
			$form_type = get_post_meta( $form_id, 'admin_form_type', true );
			if( ! $form_type ) {
				fea_instance()->form_display->render_field_wrap(
					array(
						'label'			   => __( 'Select Type', FEA_NS ),
						'name' 			   => 'admin_form_type',
						'key' 			   => 'admin_form_type',
						//'required' 		   => true,
						'prefix'		   => 'form',
						'type'			   => 'select',
						'choices'		   => feadmin_form_types(),
						'wrapper'		   => [
							'width' => 25
						]
					)
					);
				return;
			}
			echo '<div class="copy-shortcode-box">';
        
			foreach( array( 'form', 'template' ) as $content ){	
				if( 'admin_'.$content != $type ) continue;	

				echo '<div class="shortcode-copy">';
				// Show shortcode
				$form_shortcode = sprintf( '<code>[%s %s="%d"]</code>', 'frontend_admin', $content, $form_id );
							
				echo sprintf('%s: <code>%s</code> ',
				__( ucwords( $content ) . ' Shortcode', FEA_NS ), $form_shortcode
				);

				//Save icon location
				$icon_path = '<span class="dashicons dashicons-admin-page"></span>';
			
				echo sprintf( 
					'<button type="button" class="copy-shortcode" data-prefix="%s %2$s" data-%2$s="%3$s">%4$s %5$s</button>',
					'frontend_admin',
					$content,
					$form_id,
					$icon_path,
					__( 'Copy Code', FEA_NS )
				);
				echo '</div>';

			}
			echo '</div>';
		}

	}
	/**
	 * Displays the form key after the title
	 *
	 * @since 1.0.0
	 *
	 */
	function post_type_form_data() {

		global $post, $form;

		if ( 'admin_form' != $post->post_type && $post->post_type !== 'admin_template' ) return;	

		// render post data
		acf_form_data(array(
			'screen'		=> $post->post_type,
			'post_id'		=> $post->ID,
			'delete_fields'	=> 0,
			'validation'	=> 0
		));

		$form_type = get_post_meta( $post->ID, 'admin_form_type', true );
		if( ! $form_type ) {
			return;
		}
		
		$sub_tabs = array( 
			'fields' => __("Fields",'acf'),
		);

		if( fea_instance()->is__premium_only() ){
			$sub_tabs['steps'] = __( 'Multi Step', FEA_NS );
		}

		$sub_tabs['submissions'] = __( 'Submissions', FEA_NS );
		
		$sub_tabs = array_merge( $sub_tabs, array(
			'actions' =>  __( 'Actions', FEA_NS ),
			'permissions' =>  __( 'Permissions', FEA_NS ),
			'modal' =>  __( 'Modal Window', FEA_NS ),
			'post' => __( 'Post', FEA_NS ),
			'user' =>  __( 'User', FEA_NS ),
			'term' =>  __( 'Term', FEA_NS ),
		) );

		if( fea_instance()->is__premium_only() ){
			if ( class_exists( 'woocommerce' ) ){
				$sub_tabs['product'] = __( 'Product', FEA_NS );
			}
		} 

		?> <div class="frontend-form-fields">
			<div class="tabs">
			<?php
			fea_instance()->form_display->render_field_wrap( array(
				'name' 			   => 'admin_form_tabs',
				'key' 			   => 'admin_form_tabs',
				'value'			   => 'fields',	
				'field_label_hide' => 1,
				'type'			   => 'button_group',
				'choices'		   => $sub_tabs,
				'layout'		   => 'vertical',
			) );
			?>
			</div>
			<div class="sections acf-fields">
				<?php
				foreach( $sub_tabs as $type => $label ){
					$this->show_fields( $type );
				}
				?>
			</div></div> <?php
	
}
	
	/**
	 * Adds custom columns to the listings page
	 *
	 * @since 1.0.0
	 *
	 */
	function manage_columns( $columns ) {
		
		$new_columns = array(
			'shortcode'		=> __( 'Shortcode', FEA_NS ),
			//'fields' 	=> __( 'Fields', FEA_NS ),
		);

		// Remove date column
		unset( $columns['date'] );
		
		return array_merge( array_splice( $columns, 0, 2 ), $new_columns, $columns );
		
	}
	
	
	/**
	 * Outputs the content for the custom columns
	 *
	 * @since 1.0.0
	 *
	 */
	function columns_content( $column, $form_id ) {
		
		//$form = fea_instance()->form_display->get_form( $form_id );
		$content = str_replace( 'admin_', '', get_post_type( $form_id ) );
		if ( 'shortcode' == $column ) {
      
			// Show shortcode
			echo sprintf( '<code>[%s %s="%d"]</code>', 'frontend_admin', $content, $form_id );
			
			$icon_path = '<span class="dashicons dashicons-admin-page"></span>';
			//Save icon location
			echo sprintf( 
				'<button type="button" class="copy-shortcode" data-prefix="%s %2$s" data-%2$s="%3$s">%4$s %5$s</button>',
				'frontend_admin',
				$content,
				$form_id,
				$icon_path,
				__( 'Copy Code', FEA_NS )
			);

		} 
		
	}



	/**
	 * Hides the months filter on the forms listing page.
	 *
	 * @since 1.6.5
	 *
	 */
	function disable_months_dropdown( $disabled, $post_type ) {
        if ( 'admin_form' != $post_type && $post_type !== 'admin_template' ) {
        return $disabled;
        }

        return true;
    }


	/*  mb_post
	*
	*  This function will render the HTML for the medtabox 'Post'
	*
	*/
	function show_fields( $type ) {
		global $form;
		if( isset( fea_instance()->local_actions[$type] ) ){
			$fields = fea_instance()->local_actions[$type]->get_form_builder_options( $form );
		}else{
			$fields = require_once( __DIR__ . "/sections/$type.php" );
		}		
		$this->render_fields( $fields, $form, $type );
	}
	
	function get_view( $path = '', $args = array() ) {

		// allow view file name shortcut
		if ( substr( $path, -4 ) !== '.php' ) {
	
			$path = __DIR__ . "/views/{$path}.php";
	
		}
	
		// include
		if ( file_exists( $path ) ) {
	
			extract( $args );
			include $path;
	
		}
	
	}

	function render_fields( $fields, $form, $type ){
		foreach( $fields as $field ){
			$field['prefix'] = 'form';
			$field['name'] = $field['key'];
			if( empty( $field['conditional_logic'] ) ){
				$field['conditional_logic'] = 0;
			}
			$field['wrapper']['data-form-tab'] = $type; 
			
			if( isset( $form[$field['key']] ) ) {
				if( empty( $field['value'] ) ){
					$field['value'] = $form[$field['key']];
				}
			}
			elseif( isset( $field['default_value'] ) ){
				$field['value'] = $field['default_value'];
			}
			fea_instance()->form_display->render_field_wrap( $field, 'div', 'field' );
		}
	}


	function admin_head() {
		
		// global
		global $post, $form;

		if( empty( $post->ID ) ) return;
				
		// set global var
		$form = $this->get_form_data( $post );
		
	}

	function get_form_data( $post ){
		if( is_int( $post ) ){
			$post = get_post( $post );
		}
		$form = maybe_unserialize( $post->post_content );
		
		if( ! $form ) $form = array();

		$form_type = get_post_meta( $post->ID, 'admin_form_type', true );

		if( ! $form_type || $form_type == 'general' ){
			$custom_fields_save = 'post';
		}else{
			$custom_fields_save = str_replace( array( 'status_', 'delete_', 'new_', 'edit_', 'duplicate_' ), '', $form_type );
		}
        
        
		switch( $form_type ){
			case 'new_post':
				$success_msg = 'Your post has been published successfully.';
				break;
			case 'delete_post':
				$success_msg = 'Your post has been deleted successfully.';
				break;
			case 'edit_post':
				$success_msg = 'The post has been updated successfully.';
				break;
			case 'duplicate_post':
				$success_msg = 'The post has been duplicated successfully.';
				break;
			case 'new_user':
				$success_msg = 'Your profile has been created successfully.';
				break;
			case 'edit_user':
				$success_msg = 'Your profile has been updated successfully.';
				break;
			case 'new_term':
				$success_msg = 'The term has been created successfully.';
				break;
			case 'edit_term':
				$success_msg = 'The term has been updated successfully.';
				break;
			case 'new_product':
				$success_msg = 'The product has been created successfully.';
				break;
			case 'edit_product':
				$success_msg = 'The product has been updated successfully.';
				break;
			case 'duplicate_product':
				$success_msg = 'The product has been duplicated successfully.';
				break;
			case 'edit_options':
				$success_msg = 'Site options have been updated successfully.';
				break;
			default:
				$success_msg = 'The form has been submitted successfully.';
		}
       
        
		$form = feadmin_parse_args( $form, array(
			'redirect' => 'current',
			'custom_url' => '',
			'show_update_message' => 1,
			'update_message' => __( $success_msg, FEA_NS ),
			'custom_fields_save' => $custom_fields_save,
			'by_role' => array( 'administrator' ),
			'admin_form_type' => $form_type,
			'modal_button_text' => __( 'Open Form', FEA_NS ),
			'steps_display' => array( 'tabs' ),
			'steps_tabs_display' => array( 'desktop', 'tablet' ),
			'steps_counter_display' => array( 'desktop', 'tablet' ),
			'counter_text' => sprintf( __( 'Step %s/%s', FEA_NS ), '[current_step]', '[total_steps]' ),
		) );

		foreach( array( 'post', 'user', 'term', 'product' ) as $type ){
			if( empty( $form['save_to_' . $type] ) ){
				if( $form['admin_form_type'] != 'general' && $type == $custom_fields_save ){
					$form['save_to_' . $type] = $form['admin_form_type'];
				}else{
					$form['save_to_' . $type] = 'edit_' . $type;
				}
			}			
		}
	
		return $form;
	}

	function admin_enqueue_scripts() {
		
		// no autosave
		wp_dequeue_script('autosave');
		
		// localize text
		acf_localize_text(array(
			'The string "field_" may not be used at the start of a field name'	=> __('The string "field_" may not be used at the start of a field name', 'acf'),
			'This field cannot be moved until its changes have been saved'		=> __('This field cannot be moved until its changes have been saved', 'acf'),
			'Field group title is required'										=> __('Form title is required', FEA_NS),
			'Move to trash. Are you sure?'										=> __('Move to trash. Are you sure?', 'acf'),
			'No toggle fields available'										=> __('No toggle fields available', 'acf'),
			'Move Custom Field'													=> __('Move Custom Field', 'acf'),
			'Checked'															=> __('Checked', 'acf'),
			'(no label)'														=> __('(no label)', 'acf'),
			'(this field)'														=> __('(this field)', 'acf'),
			'copy'																=> __('copy', 'acf'),
			'or'																=> __('or', 'acf'),
			'Null'																=> __('Null', 'acf'),
		));
		
		// localize data
		acf_localize_data(array(
		   	'fieldTypes' => acf_get_field_types_info()
	   	));
	   	
				
	}

	function current_screen() {
		// validate screen
		$current_screen = get_current_screen();

		if( 'admin_form' != $current_screen->post_type && $current_screen->post_type !== 'admin_template' ) return;

		// disable filters to ensure ACF loads raw data from DB
		acf_disable_filters();
		remove_all_actions( 'user_admin_notices' );
        remove_all_actions( 'admin_notices' );
		
		// enqueue scripts
		acf_enqueue_scripts();
		$this->enqueue_admin_scripts();
		add_action('admin_enqueue_scripts',		array($this, 'admin_enqueue_scripts'));
		add_action('acf/input/admin_head', 					array($this, 'admin_head'));
	}

	
	function enqueue_admin_scripts() {
		
        $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '-min';
		
		wp_enqueue_style( 'fea-form-builder' );
		wp_enqueue_script( 'fea-form-builder' );

		wp_enqueue_style( 'fea-modal' );	

	}
	function admin_form_display( $content ){
		global $post;
		global $form_preview;

		if( $post->post_type == 'admin_form' ){
			$form_preview = true;
			$content = '[frontend_admin form="'.$post->ID.'"]';
		}
		if( $post->post_type == 'admin_template' ){
			$content = '[frontend_admin template="'.$post->ID.'"]';
		}

		return $content;
	}

	public function deleted_post( $form_id ) {
		if ( get_post_type( $form_id ) === 'admin_form' ) {
			// Delete fields.
			$fields_args = array(
				'post_type' => 'acf-field',
				'posts_per_page' => '-1',
				'post_parent' => $form_id,
				'orderby' => 'menu_order', 
				'order' => 'ASC'
			);
			$multi = false;

			$fields = get_posts( $fields_args );
			if( $fields ){
				foreach( $fields as $index => $field ){
					$object = acf_get_field( $field );
					if ( $object ) {
						acf_delete_field( $object['ID'] );
					}
				}
			}
		
			// Delete post.
			wp_delete_post( $form_id, true );
				
			/**
			 * Fires immediately after a field group has been deleted.
			 *
			 * @date    12/02/2014
			 * @since   5.0.0
			 *
			 * @param   array $field_group The field group array.
			 */
			do_action( 'fea/delete_form', $form_id );
		
			// Return true.
			return true;
		}
	}
	

    function __construct() {	
        require_once( __DIR__ . '/post-types.php' );
        require_once( __DIR__ . '/tools/tool.php' );
        require_once( __DIR__ . '/tools/export.php' );
        require_once( __DIR__ . '/tools/import.php' );
		add_filter( 'the_content', array( $this, 'admin_form_display' ) );	

		add_action( 'edit_form_top', array( $this, 'display_shortcode' ), 12, 0 );
		add_action( 'edit_form_after_title', array( $this, 'post_type_form_data' ), 11, 0 );

		add_action( 'save_post', array( $this, 'save_post' ), 10, 2 );
		add_action( 'deleted_post', array( $this, 'deleted_post' ) );

		add_action( 'current_screen', array($this, 'current_screen' ) );
		
		add_filter( 'manage_admin_form_posts_columns', array( $this, 'manage_columns' ), 10, 1 );
		add_action( 'manage_admin_form_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );
		add_filter( 'manage_admin_template_posts_columns', array( $this, 'manage_columns' ), 10, 1 );
		add_action( 'manage_admin_template_posts_custom_column', array( $this, 'columns_content' ), 10, 2 );
		add_filter( 'disable_months_dropdown', array( $this, 'disable_months_dropdown' ), 10, 2 );		

		//add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 10, 0 );

		add_action( 'acf/prepare_field', array( $this, 'dynamic_value_insert' ), 15, 1 );
		add_action( 'media_buttons', array( $this, 'add_dynamic_value_button' ), 15, 1 );
	}

	function dynamic_value_insert( $field ) {
		if( empty( $field['dynamic_value_choices'] ) ) return $field;
		$field['wrapper']['data-dynamic_values'] = '1';
		if( $field['type'] == 'text' ){
			$field['type'] = 'text_input';
			$field['no_autocomplete'] = 1;		
		}
		return $field;
	}
	
	function add_dynamic_value_button( $editor ) {
		
		global $post;
		
		if ( empty( $post->post_type ) || ( $post->post_type != 'admin_form' && $post->post_type !== 'admin_template' ) ){
			return;
		}
		if ( is_string( $editor ) && 'acf-editor' == substr($editor, 0, 10) ) {
			echo '<a class="dynamic-value-options button">' . __( 'Dynamic Value', FEA_NS ) . '</a>';
		}
		
	}


	function render_shortcode_option( $field, $parents = array() ) {
		$insert_value = '';
		if ( empty( $parents ) ) {
			$insert_value = sprintf( '[form:%s]', $field['name'] );
		} else {
			$hierarchy = array_merge( $parents, array( $field['name'] ) );
			$top_level_name = array_shift( $hierarchy );
			$insert_value = sprintf( '[form:%s[%s]]', $top_level_name, join( '][', $hierarchy ) );
		}
		
		$label = wp_strip_all_tags( $field['label'] );
		$type = acf_get_field_type_label( $field['type'] );
	
		echo sprintf( '<div class="field-option" data-insert-value="%s" role="button">', $insert_value );
		echo sprintf( '<span class="field-name">%s</span><span class="field-type">%s</span>', $label, $type );
		echo '</div>';
	
		// Append options for sub fields if they exist (and we are dealing with a group or clone field)
		$parent_field_types = array( 'group', 'clone' );
		if ( in_array( $field['type'], $parent_field_types ) && isset( $field['sub_fields'] ) ) {
			array_push( $parents, $field['name'] );
	
			echo '<div class="sub-fields-wrapper">';
			foreach ( $field['sub_fields'] as $sub_field ) {
				$this->render_shortcode_option( $sub_field, $parents );
			}
			echo '</div>';
		}
	}
	
}

fea_instance()->form_builder =  new Frontend_Forms_UI();
