<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class QCW_Settings {
  public static function init() {
      add_action( 'admin_menu', [ __CLASS__, 'add_settings_page' ] );
      add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
  }

  public static function add_settings_page() {
      add_options_page(
          __( 'QuickClone WP Settings', 'quickclone-wp' ),
          __( 'QuickClone WP', 'quickclone-wp' ),
          'manage_options',
          'qcw-settings',
          [ __CLASS__, 'settings_page_html' ]
      );
  }

  public static function register_settings() {
      register_setting( 'qcw_settings_group', 'qcw_redirect_url' );
      register_setting( 'qcw_settings_group', 'qcw_include_meta' );
      register_setting( 'qcw_settings_group', 'qcw_include_featured' );
  }

  public static function settings_page_html() {
      ?>
      <div class="wrap">
          <h1><?php esc_html_e( 'QuickClone WP Settings', 'quickclone-wp' ); ?></h1>
          <form method="post" action="options.php">
              <?php
              settings_fields( 'qcw_settings_group' );
              do_settings_sections( 'qcw_settings_group' );
              $redirect_url = esc_url( get_option( 'qcw_redirect_url', admin_url( 'edit.php' ) ) );
              $include_meta = get_option( 'qcw_include_meta', true );
              $include_featured = get_option( 'qcw_include_featured', true );
              ?>
              <table class="form-table">
                  <tr valign="top">
                      <th scope="row"><?php esc_html_e( 'Redirect After Duplication', 'quickclone-wp' ); ?></th>
                      <td><input type="url" name="qcw_redirect_url" value="<?php echo $redirect_url; ?>" class="regular-text"></td>
                  </tr>
                  <tr valign="top">
                      <th scope="row"><?php esc_html_e( 'Include Post Meta', 'quickclone-wp' ); ?></th>
                      <td><input type="checkbox" name="qcw_include_meta" value="1" <?php checked( $include_meta ); ?>></td>
                  </tr>
                  <tr valign="top">
                      <th scope="row"><?php esc_html_e( 'Include Featured Image', 'quickclone-wp' ); ?></th>
                      <td><input type="checkbox" name="qcw_include_featured" value="1" <?php checked( $include_featured ); ?>></td>
                  </tr>
              </table>
              <?php submit_button(); ?>
          </form>
      </div>
      <?php
  }
}
