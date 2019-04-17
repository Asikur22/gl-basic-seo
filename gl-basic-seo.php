<?php
/*
Plugin Name: GL Basic SEO
Plugin URI: https://www.greenlifeit.com/plugins/
Description: GL Basic SEO for adding meta tags
Tags: seo, search engine optimization, simple, simple seo
Version: 1.0.0
Stable tag: 1.0
Author: Asiqur Rahman
Author URI: https://www.asique.net/
License: GPLv2
*/

function basic_seo_add_meta_box() {
	$post_types = array( 'post', 'page', 'product' );
	
	add_meta_box(
		'basic_seo-basic-seo',
		__( 'Basic SEO', 'basic_seo' ),
		'basic_seo_html',
		$post_types,
		'normal',
		'high'
	);
}

add_action( 'add_meta_boxes', 'basic_seo_add_meta_box' );

function basic_seo_html( $post ) {
	wp_nonce_field( '_basic_seo_nonce', 'basic_seo_nonce' ); ?>
	
	<p>
		<label for="basic_seo_seo_title"><?php _e( 'SEO Title', 'basic_seo' ); ?></label><br>
		<input class="large-text" type="text" name="basic_seo_seo_title" id="basic_seo_seo_title" value="<?php echo basic_seo_get_meta( 'basic_seo_seo_title' ); ?>">
	</p>
	<p>
		<label for="basic_seo_seo_keywords"><?php _e( 'SEO Keywords', 'basic_seo' ); ?></label><br>
		<input class="large-text" type="text" name="basic_seo_seo_keywords" id="basic_seo_seo_keywords" value="<?php echo basic_seo_get_meta( 'basic_seo_seo_keywords' ); ?>">
	</p>
	<p>
		<label for="basic_seo_seo_description"><?php _e( 'SEO Description', 'basic_seo' ); ?></label><br>
		<textarea class="large-text" name="basic_seo_seo_description" id="basic_seo_seo_description" rows="5"><?php echo basic_seo_get_meta( 'basic_seo_seo_description' ); ?></textarea>
	</p>
	<?php
}

function basic_seo_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! isset( $_POST['basic_seo_nonce'] ) || ! wp_verify_nonce( $_POST['basic_seo_nonce'], '_basic_seo_nonce' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	
	if ( isset( $_POST['basic_seo_seo_title'] ) ) {
		update_post_meta( $post_id, 'basic_seo_seo_title', esc_attr( $_POST['basic_seo_seo_title'] ) );
	}
	if ( isset( $_POST['basic_seo_seo_keywords'] ) ) {
		update_post_meta( $post_id, 'basic_seo_seo_keywords', esc_attr( $_POST['basic_seo_seo_keywords'] ) );
	}
	if ( isset( $_POST['basic_seo_seo_description'] ) ) {
		update_post_meta( $post_id, 'basic_seo_seo_description', esc_attr( $_POST['basic_seo_seo_description'] ) );
	}
}

add_action( 'save_post', 'basic_seo_save' );

function basic_seo_set_title( $title ) {
	global $post;
	if ( isset( $post ) ) {
		$id        = $post->ID;
		$seo_title = get_post_meta( $id, 'basic_seo_seo_title', true );
		
		if ( ! empty( $seo_title ) ) {
			$title = $seo_title;
		}
	}
	
	return $title;
}

add_filter( 'pre_get_document_title', 'basic_seo_set_title' );

function basic_seo_rendar_meta() {
	global $post;
	if ( isset( $post ) ) {
		$id      = $post->ID;
		$keyword = get_post_meta( $id, 'basic_seo_seo_keywords', true );
		$desc    = get_post_meta( $id, 'basic_seo_seo_description', true );
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

add_action( 'wp_head', 'basic_seo_rendar_meta' );

