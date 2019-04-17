<?php

/*
 * add extra fields to taxonomy edit form
 */
function basic_seo_edit_extra_category_fields( $term ) {    //check for existing featured ID
	$id = $term->term_id;
	
	$title = get_term_meta( $id, 'basic_seo_title', true );
	$key   = get_term_meta( $id, 'basic_seo_keywords', true );
	$desc  = get_term_meta( $id, 'basic_seo_description', true );
	?>
	<?php wp_nonce_field( '_basic_seo_nonce_tax', 'basic_seo_nonce_tax' ); ?>
	<table class="form-table">
		<tr class="form-field">
			<th scope="row"><label for="basic_seo_title"><?php _e( 'SEO Title', 'basic_seo' ); ?></label></th>
			<td>
				<input class="large-text" type="text" name="basic_seo_title" id="basic_seo_title" value="<?php echo $title ? $title : ''; ?>"><br/>
				<span class="description">Leave empty to use default title</span>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="basic_seo_keywords"><?php _e( 'SEO Keywords', 'basic_seo' ); ?></label></th>
			<td>
				<input class="large-text" type="text" name="basic_seo_keywords" id="basic_seo_keywords" value="<?php echo $key ? $key : ''; ?>"><br/>
				<span class="description">Enter the list of keywords separated by comma</span>
			</td>
		</tr>
		<tr class="form-field">
			<th scope="row"><label for="basic_seo_description"><?php _e( 'SEO Description', 'basic_seo' ); ?></label></th>
			<td>
				<textarea class="large-text" name="basic_seo_description" id="basic_seo_description" rows="5"><?php echo $desc ? $desc : ''; ?></textarea><br/>
				<span class="description">Enter meta description for this Term</span>
			</td>
		</tr>
	</table>
	<?php
}

function basic_seo_extra_category_fields( $term ) {    //check for existing featured ID
	$id = $term->term_id;
	
	$title = get_term_meta( $id, 'basic_seo_title', true );
	$key   = get_term_meta( $id, 'basic_seo_keywords', true );
	$desc  = get_term_meta( $id, 'basic_seo_description', true );
	?>
	<?php wp_nonce_field( '_basic_seo_nonce_tax', 'basic_seo_nonce_tax' ); ?>
	<div class="form-field">
		<label for="basic_seo_title"><?php _e( 'SEO Title', 'basic_seo' ); ?></label>
		<input class="large-text" type="text" name="basic_seo_title" id="basic_seo_title" value="<?php echo $title ? $title : ''; ?>"><br>
		<span class="description">Leave empty to use default title</span>
	</div>
	<div class="form-field">
		<label for="basic_seo_keywords"><?php _e( 'SEO Keywords', 'basic_seo' ); ?></label>
		<input class="large-text" type="text" name="basic_seo_keywords" id="basic_seo_keywords" value="<?php echo $key ? $key : ''; ?>"><br>
		<span class="description">Enter the list of keywords separated by comma</span>
	</div>
	<div class="form-field">
		<label for="basic_seo_description"><?php _e( 'SEO Description', 'basic_seo' ); ?></label>
		<textarea class="large-text" name="basic_seo_description" id="basic_seo_description" rows="5"><?php echo $desc ? $desc : ''; ?></textarea><br>
		<span class="description">Enter meta description for this Term</span>
	</div>
	<?php
}

$wptm_taxonomies = get_taxonomies( '', 'names' );
if ( is_array( $wptm_taxonomies ) ) {
	foreach ( $wptm_taxonomies as $wptm_taxonomy ) {
		add_action( $wptm_taxonomy . '_add_form_fields', 'basic_seo_extra_category_fields' );
		add_action( $wptm_taxonomy . '_edit_form', 'basic_seo_edit_extra_category_fields' );
	}
}

/*
 * save extra taxonomy extra fields
 */
function basic_seo_save_fileds( $term_id ) {
	if ( ! isset( $_POST['basic_seo_nonce_tax'] ) || ! wp_verify_nonce( $_POST['basic_seo_nonce_tax'], '_basic_seo_nonce_tax' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $term_id ) ) {
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

add_action( 'edit_term', 'basic_seo_save_fileds' );
add_action( 'created_term', 'basic_seo_save_fileds' );

/*
 * Render page title in document head
 */
function basic_seo_tax_set_title( $title ) {
	if ( basic_seo_is_tax() ) {
		$id        = get_queried_object_id();
		$seo_title = get_term_meta( $id, 'basic_seo_title', true );
		
		if ( ! empty( $seo_title ) ) {
			$title = $seo_title;
		}
	}
	
	return $title;
}

add_filter( 'pre_get_document_title', 'basic_seo_tax_set_title' );
add_filter( 'wp_title', 'basic_seo_tax_set_title' );

/*
 * Render Meta tag in document head
 */
function basic_seo_tax_rendar_meta() {
	if ( basic_seo_is_tax() ) {
		$id      = get_queried_object_id();
		$keyword = get_term_meta( $id, 'basic_seo_keywords', true );
		$desc    = get_term_meta( $id, 'basic_seo_description', true );
		?>
		<?php if ( ! empty( $keyword ) ) : ?>
			<!-- Basic SEO Keywords-->
			<meta name="keywords" content="<?php echo $keyword; ?>"/>
		<?php endif; ?>
		<?php if ( ! empty( $desc ) ) : ?>
			<!-- Basic SEO Description-->
			<meta name="description" content="<?php echo $desc; ?>"/>
		<?php endif; ?>
		<?php
	}
}

add_action( 'wp_head', 'basic_seo_tax_rendar_meta' );

