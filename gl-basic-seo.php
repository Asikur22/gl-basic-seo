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
Text Domain: glbs
*/

require_once plugin_dir_path( __FILE__ ) . 'helper.php';
include_once plugin_dir_path( __FILE__ ) . 'taxonomy-meta-box.php';

function basic_seo_add_meta_box_wrap() {
	function basic_seo_add_meta_box() {
		// $post_types = array( 'post', 'page', 'product' );
		$post_types = get_post_types( '', 'names' );
		
		add_meta_box(
			'basic_seo_metabox',
			__( 'Basic SEO', 'glbs' ),
			'basic_seo_html',
			$post_types,
			'normal',
			'high'
		);
	}
	
	add_action( 'add_meta_boxes', 'basic_seo_add_meta_box' );
}

add_action( 'init', 'basic_seo_add_meta_box_wrap', 99 );

function basic_seo_html( $post ) {
	wp_nonce_field( '_basic_seo_nonce', 'basic_seo_nonce' ); ?>
	
	<p>
		<label for="glbs_title"><?php _e( 'SEO Title', 'glbs' ); ?></label><br>
		<input class="large-text" type="text" name="glbs_title" id="glbs_title" value="<?php echo basic_seo_get_meta( 'glbs_title' ); ?>"><br>
		<small>Leave empty to use default title</small>
	</p>
	<p>
		<label for="glbs_keywords"><?php _e( 'SEO Keywords', 'glbs' ); ?></label><br>
		<input class="large-text" type="text" name="glbs_keywords" id="glbs_keywords" value="<?php echo basic_seo_get_meta( 'glbs_keywords' ); ?>"><br>
		<small>Enter the list of keywords separated by comma</small>
	</p>
	<p>
		<label for="glbs_description"><?php _e( 'SEO Description', 'glbs' ); ?></label><br>
		<textarea class="large-text" name="glbs_description" id="glbs_description" rows="5"><?php echo basic_seo_get_meta( 'glbs_description' ); ?></textarea><br>
		<small>Enter meta description for this page</small>
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
	
	if ( isset( $_POST['glbs_title'] ) ) {
		update_post_meta( $post_id, 'glbs_title', esc_attr( $_POST['glbs_title'] ) );
	}
	if ( isset( $_POST['glbs_keywords'] ) ) {
		update_post_meta( $post_id, 'glbs_keywords', esc_attr( $_POST['glbs_keywords'] ) );
	}
	if ( isset( $_POST['glbs_description'] ) ) {
		update_post_meta( $post_id, 'glbs_description', esc_attr( $_POST['glbs_description'] ) );
	}
}

add_action( 'save_post', 'basic_seo_save' );

function basic_seo_set_title( $title ) {
	global $post;
	if ( isset( $post ) ) {
		$id        = $post->ID;
		$seo_title = get_post_meta( $id, 'glbs_title', true );
		
		if ( ! empty( $seo_title ) ) {
			$title = $seo_title;
		}
	}
	
	return $title;
}

add_filter( 'pre_get_document_title', 'basic_seo_set_title' );
add_filter( 'wp_title', 'basic_seo_set_title' );

/*
 * Render Meta tag in document head
 */
function basic_seo_rendar_meta() {
	global $post;
	if ( isset( $post ) && ! glbs_is_tax() ) {
		$id      = $post->ID;
		$keyword = get_post_meta( $id, 'glbs_keywords', true );
		$desc    = get_post_meta( $id, 'glbs_description', true );
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

add_action( 'wp_head', 'basic_seo_rendar_meta' );

