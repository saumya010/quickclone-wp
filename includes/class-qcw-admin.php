<?php
/**
 * Admin UI and duplication triggers
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class QCW_Admin {

    /**
     * Initialize admin hooks.
     */
    public static function init() {
        add_filter( 'post_row_actions', [ __CLASS__, 'add_duplicate_link' ], 10, 2 );
        add_filter( 'page_row_actions', [ __CLASS__, 'add_duplicate_link' ], 10, 2 );
        add_action( 'admin_action_qcw_duplicate_post', [ __CLASS__, 'handle_duplicate_action' ] );
    }

    /**
     * Add a "Duplicate" link in post/page/CPT list table.
     */
    public static function add_duplicate_link( $actions, $post ) {
        if ( current_user_can( 'edit_post', $post->ID ) ) {
            $url = wp_nonce_url(
                admin_url( 'admin.php?action=qcw_duplicate_post&post=' . $post->ID ),
                'qcw-duplicate_' . $post->ID,
                'qcw_nonce'
            );
            $actions['duplicate'] = sprintf(
                '<a href="%1$s" title="%2$s">%3$s</a>',
                esc_url( $url ),
                esc_attr__( 'Duplicate this item', 'quickclone-wp' ),
                esc_html__( 'Duplicate', 'quickclone-wp' )
            );
        }
        return $actions;
    }

    /**
     * Handle the duplication process.
     */
    public static function handle_duplicate_action() {
        if ( empty( $_GET['post'] ) || ! isset( $_GET['qcw_nonce'] ) ) {
            wp_die( esc_html__( 'Invalid request.', 'quickclone-wp' ) );
        }

        $post_id = absint( $_GET['post'] );

        if ( ! wp_verify_nonce( $_GET['qcw_nonce'], 'qcw-duplicate_' . $post_id ) ) {
            wp_die( esc_html__( 'Security check failed.', 'quickclone-wp' ) );
        }

        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            wp_die( esc_html__( 'You do not have permission to duplicate this item.', 'quickclone-wp' ) );
        }

        $new_post_id = QCW_Duplicator::duplicate_post( $post_id );

        if ( ! $new_post_id ) {
            wp_die( esc_html__( 'Duplication failed. Please try again.', 'quickclone-wp' ) );
        }

        // Redirect to the edit screen for the new post
        wp_safe_redirect( admin_url( 'post.php?action=edit&post=' . $new_post_id ) );
        exit;
    }
}
