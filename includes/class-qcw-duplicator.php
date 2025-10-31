<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class QCW_Duplicator {

    public static function init() {
        add_action( 'admin_action_qcw_duplicate_post', [ __CLASS__, 'duplicate_post' ] );
    }

    /**
     * Duplicate post logic
     */
    public static function duplicate_post() {
        // Verify nonce
        if ( ! isset( $_GET['qcw_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_GET['qcw_nonce'] ), 'qcw_duplicate_post' ) ) {
            wp_die( __( 'Invalid nonce specified', 'quickclone-wp' ) );
        }

        if ( ! isset( $_GET['post'] ) || ! is_numeric( $_GET['post'] ) ) {
            wp_die( __( 'No post to duplicate has been supplied!', 'quickclone-wp' ) );
        }

        $post_id = intval( $_GET['post'] );
        $post = get_post( $post_id );

        if ( empty( $post ) ) {
            wp_die( __( 'Post not found!', 'quickclone-wp' ) );
        }

        // Check permissions
        if ( ! current_user_can( 'edit_posts', $post_id ) ) {
            wp_die( __( 'You do not have permission to duplicate this post.', 'quickclone-wp' ) );
        }

        // Create duplicate post
        $new_post = [
            'post_author' => get_current_user_id(),
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_status'  => 'draft',
            'post_title'   => $post->post_title . ' (Copy)',
            'post_type'    => $post->post_type,
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
        ];

        $new_post_id = wp_insert_post( $new_post );

        // Copy post meta if enabled
        $include_meta = get_option( 'qcw_include_meta', true );
        if ( $include_meta ) {
            $meta = get_post_meta( $post_id );
            foreach ( $meta as $key => $values ) {
                foreach ( $values as $value ) {
                    add_post_meta( $new_post_id, $key, maybe_unserialize( $value ) );
                }
            }
        }

        // Copy featured image if enabled
        $include_thumb = get_option( 'qcw_include_featured', true );
        if ( $include_thumb ) {
            $thumbnail_id = get_post_thumbnail_id( $post_id );
            if ( $thumbnail_id ) {
                set_post_thumbnail( $new_post_id, $thumbnail_id );
            }
        }

        // Redirect to edit screen
        $redirect_url = get_option( 'qcw_redirect_url', admin_url( 'edit.php' ) );
        wp_redirect( add_query_arg( [ 'post' => $new_post_id, 'action' => 'edit' ], $redirect_url ) );
        exit;
    }
}
