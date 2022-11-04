<?php

if( ! class_exists('acf_field_upload_files') ) :

class acf_field_upload_files extends acf_field {
	
	
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
		$this->name = 'upload_files';
		$this->label = __("Multiple Uploads",FEA_NS);
		$this->public = false;
		$this->defaults = array(
			'library'		=> 'all',
			'min'			=> 0,
			'max'			=> 0,
			'min_width'		=> 0,
			'min_height'	=> 0,
			'min_size'		=> 0,
			'max_width'		=> 0,
			'max_height'	=> 0,
			'max_size'		=> 0,
			'mime_types'	=> '',
            'insert'		=> 'append',
            'button_text'   => __( 'Add Images', FEA_NS ),
		);
		
		
		// actions
		add_action('wp_ajax_acf/fields/gallery/get_attachment',				array($this, 'ajax_get_attachment'));
        add_action('wp_ajax_nopriv_acf/fields/gallery/get_attachment',		array($this, 'ajax_get_attachment'));
		
		add_action('wp_ajax_acf/fields/gallery/update_attachment',			array($this, 'ajax_update_attachment'));
		add_action('wp_ajax_nopriv_acf/fields/gallery/update_attachment',	array($this, 'ajax_update_attachment'));
		
		add_action('wp_ajax_acf/fields/gallery/get_sort_order',				array($this, 'ajax_get_sort_order'));
		add_action('wp_ajax_nopriv_acf/fields/gallery/get_sort_order',		array($this, 'ajax_get_sort_order'));

		//add_filter
		add_filter( 'acf/prepare_field/type=gallery',  [ $this, 'prepare_gallery_field'], 5 );

		$multiple_files = array( 'gallery', 'upload_files', 'product_images' );
		foreach( $multiple_files as $type ){
			add_filter( 'acf/update_value/type=' .$type, [ $this, 'update_attachments_value'], 8, 3 );
			add_action( 'acf/render_field_settings/type=' .$type,  [ $this, 'upload_button_text_setting'] );	
		}
	}

	function prepare_gallery_field( $field ) {
		$uploader = acf_get_setting('uploader');
		// enqueue
		if( $uploader == 'basic' || ! empty( $field['button_text'] ) ) {
			$field['type'] = 'upload_files';
		}		
		if( $uploader == 'basic' ) {
			if( isset( $field['wrapper']['class'] ) ){ 
				$field['wrapper']['class'] .= ' acf-uploads';
			}else{
				$field['wrapper']['class'] = 'acf-uploads';
			}
		}
		$field['wrapper']['class'] .= ' image-field';

		return $field;
	}
	function prepare_field( $field ){
		$uploader = acf_get_setting('uploader');
		// enqueue
		if( $uploader == 'basic' ) {
			if( isset( $field['wrapper']['class'] ) ){ 
				$field['wrapper']['class'] .= ' acf-uploads';
			}else{
				$field['wrapper']['class'] = 'acf-uploads';
			}
		}
		$field['wrapper']['class'] .= ' image-field';
		
		return $field;
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
		   	'Add Image to Gallery'		=> __('Add Image to Gallery', FEA_NS),
			'Maximum selection reached'	=> __('Maximum selection reached', FEA_NS),
	   	));
	}
	function update_attachments_value( $value, $post_id = false, $field = false ){	
		if( ! is_array( $value ) ) return $value;
		
		if( is_numeric( $post_id ) && $value ){
			$post = get_post( $post_id );
			if( wp_is_post_revision( $post ) ){
				$post_id = $post->post_parent;
			}
		}
		$new_value = [];
		foreach( $value as $index => $attachment ){	
			if( ! empty( $attachment['id'] ) ){
				$attach_id = $attachment['id'];
	
				if( ! empty( $attachment['meta'] ) ){
					if( isset( $attachment['alt'] ) ) update_post_meta( $attach_id, '_wp_attachment_image_alt', $attachment['alt'] );
	
					$edit = ['ID' => $attach_id];
					if( ! empty( $attachment['title'] ) ){
						$edit['post_title'] = $attachment['title'];
					}
			
					if( isset( $attachment['description'] ) ) $edit['post_content'] = $attachment['description'];
					if( isset( $attachment['capt'] ) ) $edit['post_excerpt'] = $attachment['capt'];
					
					wp_update_post( $edit );
				}
				$attachment = $attach_id;
			}						
			$attachment = (int) $attachment;
			acf_connect_attachment_to_post( $attachment, $post_id );
			delete_post_meta( $attachment, 'hide_from_lib' );
			$new_value[] = $attachment;
		}
		
		return $new_value;
	}	
	
	/*
	*  ajax_get_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_get_attachment() {
	
		// options
   		$options = acf_parse_args( $_POST, array(
			'post_id'		=> 0,
			'attachment'	=> 0,
			'id'			=> 0,
			'field_key'		=> '',
			'nonce'			=> '',
		));
   		
		
		// validate
		if( !acf_verify_ajax() ) {
			die();
		}
		
		
		// bail early if no id
		if( !$options['id'] ) die();
		
		
		// load field
		$field = acf_get_field( $options['field_key'] );
		
		
		// bali early if no field
		if( !$field ) die();
		
		
		// render
		$this->render_attachment( $field, $options['id'] );
		die;
		
	}
	
	
    	
	/*
	*  ajax_update_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_update_attachment() {
		
		// validate nonce
		if( !wp_verify_nonce($_POST['nonce'], 'acf_nonce') ) {
		
			wp_send_json_error();
			
		}
		
		
		// bail early if no attachments
		if( empty($_POST['attachments']) ) {
		
			wp_send_json_error();
			
		}
		
		
		// loop over attachments
		foreach( $_POST['attachments'] as $id => $changes ) {
			
			if ( !current_user_can( 'edit_post', $id ) )
				wp_send_json_error();
				
			$post = get_post( $id, ARRAY_A );
		
			if ( 'attachment' != $post['post_type'] )
				wp_send_json_error();
		
			if ( isset( $changes['title'] ) )
				$post['post_title'] = $changes['title'];
		
			if ( isset( $changes['caption'] ) )
				$post['post_excerpt'] = $changes['caption'];
		
			if ( isset( $changes['description'] ) )
				$post['post_content'] = $changes['description'];
		
			if ( isset( $changes['alt'] ) ) {
				$alt = wp_unslash( $changes['alt'] );
				if ( $alt != get_post_meta( $id, '_wp_attachment_image_alt', true ) ) {
					$alt = wp_strip_all_tags( $alt, true );
					update_post_meta( $id, '_wp_attachment_image_alt', wp_slash( $alt ) );
				}
			}
			
			
			// save post
			wp_update_post( $post );
			
			
			/** This filter is documented in wp-admin/includes/media.php */
			// - seems off to run this filter AFTER the update_post function, but there is a reason
			// - when placed BEFORE, an empty post_title will be populated by WP
			// - this filter will still allow 3rd party to save extra image data!
			$post = apply_filters( 'attachment_fields_to_save', $post, $changes );
			
			
			// save meta
			acf_save_post( $id );
						
		}
		
		
		// return
		wp_send_json_success();
			
	}
	
	
	/*
	*  ajax_get_sort_order
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function ajax_get_sort_order() {
		
		// vars
		$r = array();
		$order = 'DESC';
   		$args = acf_parse_args( $_POST, array(
			'ids'			=> 0,
			'sort'			=> 'date',
			'field_key'		=> '',
			'nonce'			=> '',
		));
		
		
		// validate
		if( ! wp_verify_nonce($args['nonce'], 'acf_nonce') ) {
		
			wp_send_json_error();
			
		}
		
		
		// reverse
		if( $args['sort'] == 'reverse' ) {
		
			$ids = array_reverse($args['ids']);
			
			wp_send_json_success($ids);
			
		}
		
		
		if( $args['sort'] == 'title' ) {
			
			$order = 'ASC';
			
		}
		
		
		// find attachments (DISTINCT POSTS)
		$ids = get_posts(array(
			'post_type'		=> 'attachment',
			'numberposts'	=> -1,
			'post_status'	=> 'any',
			'post__in'		=> $args['ids'],
			'order'			=> $order,
			'orderby'		=> $args['sort'],
			'fields'		=> 'ids'		
		));
		
		
		// success
		if( !empty($ids) ) {
		
			wp_send_json_success($ids);
			
		}
		
		
		// failure
		wp_send_json_error();
		
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
	*  render_attachment
	*
	*  description
	*
	*  @type	function
	*  @date	13/12/2013
	*  @since	5.0.0
	*
	*  @param	$field (array)
	*  @param	$post_id (int)
	*/
	
	function render_attachment( $field, $id = 0 ) {
		
		// vars
		$attachment = wp_prepare_attachment_for_js( $id );
		$compat = get_compat_media_markup( $id );
		$compat = $compat['item'];
		$prefix = 'attachments[' . $id . ']';
		$thumb = '';
		$dimentions = '';
		
		
		// thumb
		if( isset($attachment['thumb']['src']) ) {
			
			// video
			$thumb = $attachment['thumb']['src'];
			
		} elseif( isset($attachment['sizes']['thumbnail']['url']) ) {
			
			// image
			$thumb = $attachment['sizes']['thumbnail']['url'];
			
		} elseif( $attachment['type'] === 'image' ) {
			
			// svg
			$thumb = $attachment['url'];
			
		} else {
			
			// fallback (perhaps attachment does not exist)
			$thumb = wp_mime_type_icon();
				
		}
		
		
		// dimentions
		if( $attachment['type'] === 'audio' ) {
			
			$dimentions = __('Length', FEA_NS) . ': ' . $attachment['fileLength'];
			
		} elseif( !empty($attachment['width']) ) {
			
			$dimentions = $attachment['width'] . ' x ' . $attachment['height'];
			
		}
		
		if( !empty($attachment['filesizeHumanReadable']) ) {
			
			$dimentions .=  ' (' . $attachment['filesizeHumanReadable'] . ')';
			
		}
		
		?>
		<div class="fea-uploads-side-info">
			<img src="<?php echo $thumb; ?>" alt="<?php echo $attachment['alt']; ?>" />
			<p class="filename"><strong><?php echo $attachment['filename']; ?></strong></p>
			<p class="uploaded"><?php echo $attachment['dateFormatted']; ?></p>
			<p class="dimensions"><?php echo $dimentions; ?></p>
			<p class="actions">
				<a href="#" class="fea-uploads-edit" data-id="<?php echo $id; ?>"><?php _e('Edit', FEA_NS); ?></a>
				<a href="#" class="fea-uploads-remove" data-id="<?php echo $id; ?>"><?php _e('Remove', FEA_NS); ?></a>
			</p>
		</div>
		<table class="form-table">
			<tbody>
				<?php 
				
				fea_instance()->form_display->render_field_wrap( array(
					//'key'		=> "{$field['key']}-title",
					'name'		=> 'title',
					'prefix'	=> $prefix,
					'type'		=> 'text',
					'label'		=> __('Title', FEA_NS),
					'value'		=> $attachment['title']
				), 'tr' );
				
				fea_instance()->form_display->render_field_wrap( array(
					//'key'		=> "{$field['key']}-caption",
					'name'		=> 'caption',
					'prefix'	=> $prefix,
					'type'		=> 'textarea',
					'label'		=> __('Caption', FEA_NS),
					'value'		=> $attachment['caption']
				), 'tr' );
				
				fea_instance()->form_display->render_field_wrap( array(
					//'key'		=> "{$field['key']}-alt",
					'name'		=> 'alt',
					'prefix'	=> $prefix,
					'type'		=> 'text',
					'label'		=> __('Alt Text', FEA_NS),
					'value'		=> $attachment['alt']
				), 'tr' );
				
				fea_instance()->form_display->render_field_wrap( array(
					//'key'		=> "{$field['key']}-description",
					'name'		=> 'description',
					'prefix'	=> $prefix,
					'type'		=> 'textarea',
					'label'		=> __('Description', FEA_NS),
					'value'		=> $attachment['description']
				), 'tr' );
				
				?>
			</tbody>
		</table>
		<?php
		
		echo $compat;
		
	}
	
	
	/*
	*  get_attachments
	*
	*  This function will return an array of attachments for a given field value
	*
	*  @type	function
	*  @date	13/06/2014
	*  @since	5.0.0
	*
	*  @param	$value (array)
	*  @return	$value
	*/
	
	function get_attachments( $value ) {	
		// bail early if no value
		if( empty($value) ) return false;
		
		if( is_array( $value ) ){
			$ids = [];
			foreach( $value as $attachment ){
				if( isset( $attachment['id'] ) ){
					$ids[] = $attachment['id'];
				}
			}
			if( ! $ids ) return false;
			$post__in = $ids;
		}else{
			// force value to array
			$post__in = acf_get_array( $value );
		}
				
		// get posts
		$posts = acf_get_posts(array(
			'post_type'	=> 'attachment',
			'post__in'	=> $post__in
		));
		
		
		// return
		return $posts;
				
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
		
		$uploader = acf_get_setting('uploader');

        if( $uploader == 'wp' ) acf_enqueue_uploader();
				
		// vars
		$atts = array(
			'id'				=> $field['id'],
			'class'				=> "fea-uploads {$field['class']}",
			'data-library'		=> $field['library'],
			'data-uploader'		=> $uploader,
			'data-min'			=> $field['min'],
			'data-max'			=> $field['max'],
			'data-mime_types'	=> $field['mime_types'],
			'data-insert'		=> $field['insert'],
			'data-columns'		=> 4
        );
        $button_text = ( isset( $field['button_text'] ) && $field['button_text'] ) ? $field['button_text'] : __( 'Add Images', FEA_NS );
		
        // get posts
        $value = $field['value'];
		if( isset( $value['{file-index}'] ) ) unset( $value['{file-index}'] );
		$default_icon = wp_mime_type_icon( 'application/pdf' );

        // set gallery height
        $max_height = ! $value ? 0 : 400;
		$height = acf_get_user_setting('gallery_height', $max_height);
		$height = max( $height, 200 ); // minimum height is 200
		$atts['style'] = "height:{$height}px";
	
		?>
<div <?php acf_esc_attr_e($atts); ?>>
	
	<div class="acf-hidden">
		<?php acf_hidden_input(array( 'name' => $field['name'], 'value' => '' )); ?>
	</div>
	
	<div class="fea-uploads-main">
		
		<div class="fea-uploads-attachments">
			
			<?php if( $value ): ?>
			
				<?php foreach( $value as $i => $v ): 
					$i++;
					// bail early if no value
					if( !$v ) continue;
					
					if( is_numeric( $v ) ){
						$v = acf_get_attachment( $v );
					
						// vars
						$a = array(
							'id' 		=> $v->ID,
							'title'		=> $v->post_title,
							'filename'	=> wp_basename($v->guid),
							'type'		=> acf_maybe_get(explode('/', $v->post_mime_type), 0),
						);
					}else{
						$a = $v; 
						$a['filename'] = wp_basename($a['title']);
						$a['type'] = '';
					}

					$a['class'] = 'fea-uploads-attachment';
					
					
					// thumbnail
					$thumbnail = acf_get_post_thumbnail($a['id'], 'medium');
					
					
					// remove filename if is image
					if( $a['type'] == 'image' ) $a['filename'] = '';
					
					
					// class
					$a['class'] .= ' -' . $a['type'];
					
					if( $thumbnail['type'] == 'icon' ) {
						
						$a['class'] .= ' -icon';
						
					}
					
					
					?>
					<div class="<?php echo $a['class']; ?>" data-id="<?php echo $a['id']; ?>">
						<?php acf_hidden_input(array( 'name' => $field['name'].'['.$a['id'].'][id]', 'value' => $a['id'] )); ?>
						<div class="margin">
							<div class="thumbnail">
								<img src="<?php echo $thumbnail['url']; ?>" alt="" title="<?php echo $a['title']; ?>"/>
							</div>
							<?php if( $a['filename'] ): ?>
							<div class="filename"><?php echo acf_get_truncated($a['filename'], 30); ?></div>	
							<?php endif; ?>
						</div>
						<div class="actions">
							<a class="acf-icon -cancel dark fea-uploads-remove" href="#" data-id="<?php echo $a['id']; ?>" title="<?php _e('Remove', FEA_NS); ?>"></a>
						</div>
						<?php
							$prefix = $field['prefix'] . '[' . $field['key'] . ']['.$a['id'].']';
							fea_instance()->form_display->render_meta_fields( $prefix, $a, false ); 
						?>
					</div>
				<?php endforeach; ?>
				
            <?php endif; ?>
			
		</div>

		<div class="image-preview-clone acf-hidden">
			<div class="margin">
				<div class="thumbnail">
					<img data-default="<?php echo $default_icon; ?>" src="" alt="" title=""/>
				</div>
			</div>
			<div class="actions">
				<a class="acf-icon -cancel dark fea-uploads-remove" href="#" title="<?php _e('Remove', FEA_NS); ?>"></a>
			</div>
			<?php if( $uploader == 'basic' ){ ?>
				<div class="uploads-progress"><div class="percent">0%</div><div class="bar"></div></div>
			<?php }	?>
		</div>

		<?php
			$prefix = $field['prefix'] . '[' . $field['key'] . '][{file-index}]';
			fea_instance()->form_display->render_meta_fields( $prefix, 'clone', false ); 
		?>
		
		<div class="fea-uploads-toolbar">
			
			<ul class="acf-hl">
                <?php if( $uploader == 'basic' ): ?>
                    <li>
                        <label class="acf-basic-uploader file-drop">
							<?php 
							$file_attrs = array( 'name' => 'upload_files_input', 'id' => $field['id'], 'class' => 'images-preview', 'multiple' => 'true' );
							if( $field['max'] && is_array( $value ) && count( $value ) >= $field['max'] ){
								$file_attrs['disabled'] = 'disabled';
							}
							if( $field['mime_types'] ){
								$file_attrs['accept'] = $field['mime_types']; 
							}
							acf_file_input( $file_attrs ); ?>
                            <div class="file-custom">
                                <div class="acf-button button fea-uploads-upload"><?php echo $button_text; ?></div>
                            </div>
                        </label>
                    </li>
                <?php else: ?>
                    <li>
                        <a href="#" class="acf-button button button-primary fea-uploads-add"><?php echo $button_text; ?></a>
                    </li>
                    <li class="acf-fr">
                        <select class="fea-uploads-sort">
                            <option value=""><?php _e('Bulk actions', FEA_NS); ?></option>
                            <option value="date"><?php _e('Sort by date uploaded', FEA_NS); ?></option>
                            <option value="modified"><?php _e('Sort by date modified', FEA_NS); ?></option>
                            <option value="title"><?php _e('Sort by title', FEA_NS); ?></option>
                            <option value="reverse"><?php _e('Reverse current order', FEA_NS); ?></option>
                        </select>
                    </li>
                <?php endif; ?>
			</ul>
			
		</div>
		
	</div>
	
	<div class="fea-uploads-side">
	<div class="fea-uploads-side-inner">
			
		<div class="fea-uploads-side-data"></div>
						
		<div class="fea-uploads-toolbar">
			
			<ul class="acf-hl">
				<li>
					<a href="#" class="acf-button button fea-uploads-close"><?php _e('Close', FEA_NS); ?></a>
				</li>
				<li class="acf-fr">
					<a class="acf-button button button-primary fea-uploads-update" href="#"><?php _e('Update', FEA_NS); ?></a>
				</li>
			</ul>
			
		</div>
		
	</div>	
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
			'min',
			'max',
			'min_width',
			'min_height',
			'min_size',
			'max_width',
			'max_height',
			'max_size'
		);
		
		foreach( $clear as $k ) {
			
			if( empty($field[$k]) ) $field[$k] = '';
			
		}
		
		
		// min
		acf_render_field_setting( $field, array(
			'label'			=> __('Minimum Selection',FEA_NS),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'min'
		));
		
		
		// max
		acf_render_field_setting( $field, array(
			'label'			=> __('Maximum Selection',FEA_NS),
			'instructions'	=> '',
			'type'			=> 'number',
			'name'			=> 'max'
		));
		
		
		// insert
		acf_render_field_setting( $field, array(
			'label'			=> __('Insert',FEA_NS),
			'instructions'	=> __('Specify where new attachments are added',FEA_NS),
			'type'			=> 'select',
			'name'			=> 'insert',
			'choices' 		=> array(
				'append'		=> __('Append to the end', FEA_NS),
				'prepend'		=> __('Prepend to the beginning', FEA_NS)
			)
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
			'prepend'		=> __('File size', FEA_NS),
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
			'prepend'		=> __('File size', FEA_NS),
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
	
/* 	function format_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) ) return false;
				
		// get posts
		$posts = $this->get_attachments($value);
		
		
		// update value to include $post
		foreach( array_keys($posts) as $i ) {
			
			$posts[ $i ] = acf_get_attachment( $posts[ $i ] );
			
		}
				
		
		// return
		return $posts;
		
	} */
	
	
	/*
	*  validate_value
	*
	*  description
	*
	*  @type	function
	*  @date	11/02/2014
	*  @since	5.0.0
	*
	*  @param	$post_id (int)
	*  @return	$post_id (int)
	*/
	
	function validate_value( $valid, $value, $field, $input ){
		
		if( empty($value) || !is_array($value) ) {
		
			$value = array();
			
		}
		
		
		if( is_array( $value ) && count($value) < $field['min'] ) {
		
			$valid = _n( '%s requires at least %s selection', '%s requires at least %s selections', $field['min'], 'acf' );
			$valid = sprintf( $valid, $field['label'], $field['min'] );
			
		}
		
				
		return $valid;
		
	}
	
	
	/*
	*  update_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_value( $value, $post_id, $field ) {
		
		// bail early if no value
		if( empty($value) || !is_array($value) ) return false;
		
		
		// loop
		foreach( $value as $i => $v ) {
			
			$value[ $i ] = $this->update_single_value( $v );
			
		}
				
		
		// return
		return $value;
		
	}
	
	
	/*
	*  update_single_value()
	*
	*  This filter is appied to the $value before it is updated in the db
	*
	*  @type	filter
	*  @since	3.6
	*  @date	23/01/13
	*
	*  @param	$value - the value which will be saved in the database
	*  @param	$post_id - the $post_id of which the value will be saved
	*  @param	$field - the field array holding all the field options
	*
	*  @return	$value - the modified value
	*/
	
	function update_single_value( $value ) {
		
		// numeric
		if( is_numeric($value) ) return $value;
		
		
		// array?
		if( is_array($value) && isset($value['ID']) ) return $value['ID'];
		
		
		// object?
		if( is_object($value) && isset($value->ID) ) return $value->ID;
		
		
		// return
		return $value;
		
	}

	
}


// initialize
acf_register_field_type( 'acf_field_upload_files' );

endif; // class_exists check

?>