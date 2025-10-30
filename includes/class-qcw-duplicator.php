<?php
/**
 * Handles the duplication logic
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class QCW_Duplicator {

    /**
     * Duplicate a post, page, or custom post type.
     *
     * @param int $post_id ID of the post to duplicate.
     * @return int|false New post ID or false on failure.
     */
    public static function duplicate_post( $post_id ) {
        $post = get_post( $post_id );

        if ( ! $post ) {
            return false;
        }

        $new_post = [
            // translators: %s is replaced with the title of the original post.
            'post_title' => sprintf( __( 'Copy of %s', 'quickclone-wp' ), $post->post_title ),
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_status'  => 'draft',
            'post_type'    => $post->post_type,
            'post_author'  => get_current_user_id(),
            'post_parent'  => $post->post_parent,
        ];

        $new_post_id = wp_insert_post( $new_post );

        if ( is_wp_error( $new_post_id ) || ! $new_post_id ) {
            return false;
        }

        // Copy all taxonomies
        $taxonomies = get_object_taxonomies( $post->post_type );
        foreach ( $taxonomies as $taxonomy ) {
            $terms = wp_get_object_terms( $post_id, $taxonomy, [ 'fields' => 'ids' ] );
            if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
                wp_set_object_terms( $new_post_id, $terms, $taxonomy );
            }
        }

        // Copy post meta
        $meta_data = get_post_meta( $post_id );
        foreach ( $meta_data as $meta_key => $meta_values ) {
            // Skip WordPress internal fields
            if ( in_array( $meta_key, [ '_edit_lock', '_edit_last' ], true ) ) {
                continue;
            }
            foreach ( $meta_values as $meta_value ) {
                update_post_meta( $new_post_id, $meta_key, maybe_unserialize( $meta_value ) );
            }
        }

        // Copy featured image
        $thumb_id = get_post_thumbnail_id( $post_id );
        if ( $thumb_id ) {
            set_post_thumbnail( $new_post_id, $thumb_id );
        }

        return $new_post_id;
    }
}
