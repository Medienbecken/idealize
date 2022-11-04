<?php

if( ! class_exists('acf_field_upload_image') ) :

class acf_field_upload_image extends acf_field_image {
	
	
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
		$this->name = 'upload_image';
		$this->label = __("Upload Image",FEA_NS);
		$this->public = false;
		$this->defaults = array(
			'return_format'	=> 'array',
			'preview_size'	=> 'thumbnail',
			'library'		=> 'all',
			'min_width'		=> 0,
			'min_height'	=> 0,
			'min_size'		=> 0,
			'max_width'		=> 0,
			'max_height'	=> 0,
			'max_size'		=> 0,
			'mime_types'	=> '',
            'button_text'   => __( 'Add Image', FEA_NS ),
			'no_file_text'  => __( 'No Image selected', FEA_NS ),
		);

		// filters
		add_filter('get_media_item_args',				array($this, 'get_media_item_args'));


	}


	/*
	*  input_admin_enqueue_scripts
	*
	*  description
	*
	*  @type	function
	*  @date	16/12/2015
	*  @since	5.3.2
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function input_admin_enqueue_scripts() {
		
		// localize
		acf_localize_text(array(
		   	'Select Image'	=> __('Select Image', FEA_NS),
			'Edit Image'	=> __('Edit Image', FEA_NS),
			'Update Image'	=> __('Update Image', FEA_NS),
			'All images'	=> __('All', FEA_NS),
	   	));
	}
	
	function upload_button_text_setting( $field ) {
		acf_render_field_setting( $field, array(
			'label'			=> __('Button Text'),
			'name'			=> 'button_text',
			'type'			=> 'text',
			'placeholder'	=> __( 'Add Image', FEA_NS ),
		) );
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
		if( empty( $field['field_type'] ) ){
			$field['field_type'] = 'image';
		}
		if( empty( $field['preview_size'] ) ) $field['preview_size'] = 'thumbnail';

		// vars
		$uploader = acf_get_setting('uploader');
		
		$_value = $field['value'];
		if( isset( $_value['id'] ) ){
			$value = $_value['id'];
		}else{
			$value = $_value;
		}

		// enqueue
		if( $uploader == 'wp' ) {
			acf_enqueue_uploader();
		}
		
		// vars
		$url = '';
		$alt = '';
		$div = array(
			'class'					=> 'acf-'.$field['field_type'].'-uploader',
			'data-preview_size'		=> $field['preview_size'],
			'data-library'			=> $field['library'],
			'data-mime_types'		=> $field['mime_types'],
			'data-uploader'			=> $uploader,
		);
		if( ! empty( $field['button_text'] ) ){
			$button_text = $field['button_text'];
		}else{
			$button_text = __( 'Add Image', FEA_NS );	
				
		}

		// has value?
		if( $value ) {			
			// update vars
			$url = wp_get_attachment_image_src($value, $field['preview_size']);
			$alt = get_post_meta($value, '_wp_attachment_image_alt', true);
						
			// url exists
			if( $url ) $url = $url[0];
			
			
			// url exists
			if( $url ) {
				$div['class'] .= ' has-value';
			}
						
		}else{
			$url = wp_mime_type_icon( 'image/png' );
		}
		
		
		// get size of preview value
		$size = acf_get_image_size($field['preview_size']);
		
?>
<div <?php acf_esc_attr_e( $div ); ?>>
	<?php	
		if( $uploader == 'basic' ){
			acf_hidden_input(array( 'data-name' => 'id', 'name' => $field['name'].'[id]', 'value' => $value )); 
			acf_hidden_input(array( 'data-name' => 'file', 'name' => $field['name'].'[file]', 'value' => '' ));
		}else{
			acf_hidden_input(array( 'data-name' => 'id', 'name' => $field['name'], 'value' => $value )); 
		}
	?>
	<div class="show-if-value image-wrap" <?php if( $size['width'] ): ?>style="<?php echo esc_attr('max-width: '.$size['width'].'px'); ?>"<?php endif; ?>>
		<?php 
			if( $uploader != 'basic' ){
				$edit = 'edit';
			}else{
				?><div class="frontend-admin-hidden uploads-progress"><div class="percent">0%</div><div class="bar"></div></div><?php
				$edit = 'edit-preview';
			}
		?>
		<img data-name="image" src="<?php echo esc_url($url); ?>" alt="<?php echo esc_attr($alt); ?>"/>
		<!-- <div class="frontend-admin-hidden uploads-progress"><div class="percent">0%</div><div class="bar"></div></div> -->
		<div class="acf-actions -hover">
			<a class="acf-icon -pencil dark" data-name="<?php echo $edit; ?>" href="#" title="<?php _e('Edit', FEA_NS); ?>"></a>
			<a class="acf-icon -cancel dark" data-name="remove" href="#" title="<?php _e('Remove', FEA_NS); ?>"></a>
		</div>
	</div>
	<div class="hide-if-value">
		<?php 
		$empty_text =  __( 'No file selected', FEA_NS );
		if( isset( $field['no_file_text'] ) ){
			$empty_text = $field['no_file_text'];
		}
		if( $uploader == 'basic' ): ?>
			<label class="acf-basic-uploader file-drop">
                <?php 
				$input_args = array( 'name' => 'file_input', 'id' => $field['id'], 'class' => 'image-preview' );
				if( $field['field_type'] == 'image' ){
					$input_args['accept'] = "image/*"; 
				} 
				if( $field['mime_types'] ){
					$input_args['accept'] = $field['mime_types']; 
				}
				acf_file_input( $input_args ); ?>
                <div class="file-custom">
					<?php echo $empty_text; ?>
					<div class="acf-button button">
						<?php echo $button_text; ?>
					</div>
				</div>
			</label>
			<?php 
			$prefix = $field['prefix'] . '[' . $field['key'] . ']';
			fea_instance()->form_display->render_meta_fields( $prefix, $_value ); 
			?>
		<?php else: ?>
			<p><?php echo $empty_text; ?> <a data-name="add" class="acf-button button" href="#"><?php echo $button_text; ?></a></p>
			
		<?php endif; ?>
			
	</div>
</div>
<?php
		
	}

	
	
	/*
	*  render_field_settings()
	*
	*  Create extra options for your field. This is rendered when editing a field.
	*  The value of $field['name'] can be used (like bellow) to save extra data to the $field
	*
	*  @type	action
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$field	- an array holding all the field's data
	*/
	
	function render_field_settings( $field ) {
		
		// clear numeric settings
		$clear = array(
			'min_width',
			'min_height',
			'min_size',
			'max_width',
			'max_height',
			'max_size'
		);
		
		foreach( $clear as $k ) {
			
			if( empty($field[$k]) ) {
				
				$field[$k] = '';
				
			}
			
		}
		
		
		// return_format
		acf_render_field_setting( $field, array(
			'label'			=> __('Return Value',FEA_NS),
			'instructions'	=> __('Specify the returned value on front end',FEA_NS),
			'type'			=> 'radio',
			'name'			=> 'return_format',
			'layout'		=> 'horizontal',
			'choices'		=> array(
				'array'			=> __("Image Array",FEA_NS),
				'url'			=> __("Image URL",FEA_NS),
				'id'			=> __("Image ID",FEA_NS)
			)
		));
		
		
		// preview_size
		acf_render_field_setting( $field, array(
			'label'			=> __('Preview Size',FEA_NS),
			'instructions'	=> __('Shown when entering data',FEA_NS),
			'type'			=> 'select',
			'name'			=> 'preview_size',
			'choices'		=> acf_get_image_sizes()
		));
		
		
		// library
		acf_render_field_setting( $field, array(
			'label'			=> __('Library',FEA_NS),
			'instructions'	=> __('Limit the media library choice',FEA_NS),
			'type'			=> 'radio',
			'name'			=> 'library',
			'layout'		=> 'horizontal',
			'choices' 		=> array(
				'all'			=> __('All', FEA_NS),
				'uploadedTo'	=> __('Uploaded to post', FEA_NS)
			)
		));
		
		
		// min
		acf_render_field_setting( $field, array(
			'label'			=> __('Minimum',FEA_NS),
			'instructions'	=> __('Restrict which images can be uploaded',FEA_NS),
			'type'			=> 'text',
			'name'			=> 'min_width',
			'prepend'		=> __('Width', FEA_NS),
			'append'		=> 'px',
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_height',
			'prepend'		=> __('Height', FEA_NS),
			'append'		=> 'px',
			'_append' 		=> 'min_width'
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'min_size',
			'prepend'		=> __('Image size', FEA_NS),
			'append'		=> 'MB',
			'_append' 		=> 'min_width'
		));	
		
		
		// max
		acf_render_field_setting( $field, array(
			'label'			=> __('Maximum',FEA_NS),
			'instructions'	=> __('Restrict which images can be uploaded',FEA_NS),
			'type'			=> 'text',
			'name'			=> 'max_width',
			'prepend'		=> __('Width', FEA_NS),
			'append'		=> 'px',
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_height',
			'prepend'		=> __('Height', FEA_NS),
			'append'		=> 'px',
			'_append' 		=> 'max_width'
		));
		
		acf_render_field_setting( $field, array(
			'label'			=> '',
			'type'			=> 'text',
			'name'			=> 'max_size',
			'prepend'		=> __('Image size', FEA_NS),
			'append'		=> 'MB',
			'_append' 		=> 'max_width'
		));	
		
		
		// allowed type
		acf_render_field_setting( $field, array(
			'label'			=> __('Allowed file types',FEA_NS),
			'instructions'	=> __('Comma separated list. Leave blank for all types',FEA_NS),
			'type'			=> 'text',
			'name'			=> 'mime_types',
		));
		
	}

	
	/*
	*  format_value()
	*
	*  This filter is appied to the $value after it is loaded from the db and before it is returned to the template
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value (mixed) the value which was loaded from the database
	*  @param	$post_id (mixed) the $post_id from which the value was loaded
	*  @param	$field (array) the field array holding all the field options
	*
	*  @return	$value (mixed) the modified value
	*/
	
	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) return false;
		
		
		// bail early if not numeric (error message)
		if( !is_numeric($value) ) return false;
		
		
		// convert to int
		$value = intval($value);
		
		
		// format
		if( $field['return_format'] == 'url' ) {
		
			return wp_get_attachment_url( $value );
			
		} elseif( $field['return_format'] == 'array' ) {
			
			return acf_get_attachment( $value );
			
		}
		
		
		// return
		return $value;
		
	}
	
			
}


// initialize
acf_register_field_type( 'acf_field_upload_image' );

endif; // class_exists check

?>