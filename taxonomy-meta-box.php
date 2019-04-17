<?php

/*
 * add extra fields to taxonomy edit form
 */
function glbs_edit_extra_tax_fields( $term ) {    //check for existing featured ID
	$id = $term->term_id;
	
	$title = get_term_meta( $id, 'basic_seo_title', true );
	$key   = get_term_meta( $id, 'basic_seo_keywords', true );
	$desc  = get_term_meta( $id, 'basic_seo_description', true );
	?>
	<?php wp_nonce_field( '_glbs_nonce_tax', 'glbs_nonce_tax' ); ?>
	<table class="form-table">
		<tr class="form-field">
			<th scope="row"><label for="basic_seo_title"><?php _e( 'SEO Title', 'glbs' ); ?></label></th>
			<td>
				<input class="large-text" type="text" name="basic_seo_title" id="basic_seo_title" value="<?php echo $title ? $title : ''; ?>"><br/>
				<span class="description">Leave empty to use default title</span>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="basic_seo_keywords"><?php _e( 'SEO Keywords', 'glbs' ); ?></label></th>
			<td>
				<input class="large-text" type="text" name="basic_seo_keywords" id="basic_seo_keywords" value="<?php echo $key ? $key : ''; ?>"><br/>
				<span class="description">Enter the list of keywords separated by comma</span>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="basic_seo_description"><?php _e( 'SEO Description', 'glbs' ); ?></label></th>
			<td>
				<textarea class="large-text" name="basic_seo_description" id="basic_seo_description" rows="5"><?php echo $desc ? $desc : ''; ?></textarea><br/>
				<span class="description">Enter meta description for this Term</span>
			</td>
		</tr>
	</table>
	<?php
}

function glbs_add_extra_tax_fields( $term ) {    //check for existing featured ID
	$id = $term->term_id;
	
	$title = get_term_meta( $id, 'basic_seo_title', true );
	$key   = get_term_meta( $id, 'basic_seo_keywords', true );
	$desc  = get_term_meta( $id, 'basic_seo_description', true );
	?>
	<?php wp_nonce_field( '_glbs_nonce_tax', 'glbs_nonce_tax' ); ?>
	<div class="form-field">
		<label for="basic_seo_title"><?php _e( 'SEO Title', 'glbs' ); ?></label>
		<input class="large-text" type="text" name="basic_seo_title" id="basic_seo_title" value="<?php echo $title ? $title : ''; ?>"><br>
		<span class="description">Leave empty to use default title</span>
	</div>
	<div class="form-field">
		<label for="basic_seo_keywords"><?php _e( 'SEO Keywords', 'glbs' ); ?></label>
		<input class="large-text" type="text" name="basic_seo_keywords" id="basic_seo_keywords" value="<?php echo $key ? $key : ''; ?>"><br>
		<span class="description">Enter the list of keywords separated by comma</span>
	</div>
	<div class="form-field">
		<label for="basic_seo_description"><?php _e( 'SEO Description', 'glbs' ); ?></label>
		<textarea class="large-text" name="basic_seo_description" id="basic_seo_description" rows="5"><?php echo $desc ? $desc : ''; ?></textarea><br>
		<span class="description">Enter meta description for this Term</span>
	</div>
	<?php
}

function glbs_tax_add_metabox_wrap() {
	$taxonomies = get_taxonomies( '', 'names' );
	if ( is_array( $taxonomies ) ) {
		foreach ( $taxonomies as $taxonomy ) {
			add_action( $taxonomy . '_add_form_fields', 'glbs_add_extra_tax_fields' );
			add_action( $taxonomy . '_edit_form', 'glbs_edit_extra_tax_fields' );
		}
	}
}

add_action( 'init', 'glbs_tax_add_metabox_wrap', 99 );


/*
 * save extra taxonomy extra fields
 */
function glbs_save_extra_tax_fileds( $term_id ) {
	if ( ! isset( $_POST['glbs_nonce_tax'] ) || ! wp_verify_nonce( $_POST['glbs_nonce_tax'], '_glbs_nonce_tax' ) ) {
		return;
	}
	
	if ( isset( $_POST['basic_seo_title'] ) ) {
		update_term_meta( $term_id, 'basic_seo_title', esc_attr( $_POST['basic_seo_title'] ) );
	}
	if ( isset( $_POST['basic_seo_keywords'] ) ) {
		update_term_meta( $term_id, 'basic_seo_keywords', esc_attr( $_POST['basic_seo_keywords'] ) );
	}
	if ( isset( $_POST['basic_seo_description'] ) ) {
		update_term_meta( $term_id, 'basic_seo_description', esc_attr( $_POST['basic_seo_description'] ) );
	}
}

add_action( 'edit_term', 'glbs_save_extra_tax_fileds' );
add_action( 'created_term', 'glbs_save_extra_tax_fileds' );

/*
 * Render page title in document head
 */
function glbs_tax_seo_title( $title ) {
	if ( glbs_is_tax() ) {
		$id        = get_queried_object_id();
		$seo_title = get_term_meta( $id, 'basic_seo_title', true );
		
		if ( ! empty( $seo_title ) ) {
			$title = $seo_title;
		}
	}
	
	return $title;
}

add_filter( 'pre_get_document_title', 'glbs_tax_seo_title' );
add_filter( 'wp_title', 'glbs_tax_seo_title' );

/*
 * Render Meta tag in document head
 */
function glbs_tax_seo_meta() {
	if ( glbs_is_tax() ) {
		$id      = get_queried_object_id();
		$keyword = get_term_meta( $id, 'basic_seo_keywords', true );
		$desc    = get_term_meta( $id, 'basic_seo_description', true );
		?>
		<?php if ( ! empty( $keyword ) ) : ?>
			<!-- Basic SEO Keywords-->
			<meta name="keywords" content="<?php echo $keyword; ?>">
		<?php endif; ?>
		<?php if ( ! empty( $desc ) ) : ?>
			<!-- Basic SEO Description-->
			<meta name="description" content="<?php echo $desc; ?>">
		<?php endif; ?>
		<?php
	}
}

add_action( 'wp_head', 'glbs_tax_seo_meta' );

