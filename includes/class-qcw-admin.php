<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class QCW_Admin {

    public static function init() {
        add_filter( 'post_row_actions', [ __CLASS__, 'add_duplicate_link' ], 10, 2 );
        add_filter( 'page_row_actions', [ __CLASS__, 'add_duplicate_link' ], 10, 2 );
    }

    public static function add_duplicate_link( $actions, $post ) {
        if ( current_user_can( 'edit_posts', $post->ID ) ) {
            $url = wp_nonce_url(
                admin_url( 'admin.php?action=qcw_duplicate_post&post=' . $post->ID ),
                'qcw_duplicate_post',
                'qcw_nonce'
            );
            $actions['qcw_duplicate'] = '<a href="' . esc_url( $url ) . '">' . esc_html__( 'Duplicate', 'quickclone-wp' ) . '</a>';
        }
        return $actions;
    }
}
