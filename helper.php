<?php

function basic_seo_get_meta( $value ) {
	global $post;
	
	$field = get_post_meta( $post->ID, $value, true );
	if ( ! empty( $field ) ) {
		return is_array( $field ) ? stripslashes_deep( $field ) : stripslashes( wp_kses_decode_entities( $field ) );
	} else {
		return false;
	}
}

function glbs_is_tax() {
	return is_tax() || is_category() || is_tag() || is_archive() ? true : false;
}

