<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://levent-sali.gr
 * @since      1.0.0
 *
 * @package    Ls_Advanced_Notification_Bar
 * @subpackage Ls_Advanced_Notification_Bar/public
 */

class Ls_Advanced_Notification_Bar_Public {

    private string $plugin_name;
    private string $version;

    public function __construct( string $plugin_name, string $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        add_action( 'wp_body_open', [ $this, 'display_notification_bar' ] );
    }

    public function enqueue_styles(): void {
        wp_enqueue_style( 
            $this->plugin_name, 
            plugin_dir_url( __FILE__ ) . 'css/ls-advanced-notification-bar-public.css', 
            [], 
            $this->version, 
            'all' 
        );
    }

    public function enqueue_scripts(): void {
        wp_enqueue_script( 
            $this->plugin_name, 
            plugin_dir_url( __FILE__ ) . 'js/ls-advanced-notification-bar-public.js', 
            [ 'jquery' ], 
            $this->version, 
            false 
        );
    }

    public function display_notification_bar(): void {
        $options = get_option( 'ls_settings' );

        // 1. Check if Enabled
        $is_enabled = isset( $options['ls_enabled'] ) ? (int) $options['ls_enabled'] : 0;
        if ( 1 !== $is_enabled ) {
            return;
        }

        // 2. Check Expiry Date
        if ( ! empty( $options['ls_expiry_date'] ) ) {
            $expiry_date  = strtotime( $options['ls_expiry_date'] );
            $current_date = strtotime( current_time( 'Y-m-d' ) );
            if ( $current_date > $expiry_date ) {
                return;
            }
        }

        // 3. Access Control Check
        $access = isset( $options['ls_access_control'] ) ? $options['ls_access_control'] : 'all';
        if ( 'guest' === $access && is_user_logged_in() ) {
            return;
        }
        if ( 'user' === $access && ! is_user_logged_in() ) {
            return;
        }

        // 4. Exclude Post/Page IDs Check (Visibility)
        if ( ! empty( $options['ls_exclude_posts'] ) && is_array( $options['ls_exclude_posts'] ) ) {
            if ( is_singular() && in_array( (string) get_the_ID(), $options['ls_exclude_posts'], true ) ) {
                return;
            }
        }

        // 5. Read Notification Text
        $message = isset( $options['ls_notification_text'] ) ? esc_html( $options['ls_notification_text'] ) : '';

        if ( empty( $message ) ) {
            return;
        }

        // 6. Styles & Button Data
        $bg_color     = isset( $options['ls_bg_color'] ) ? esc_attr( $options['ls_bg_color'] ) : '#2271b1';
        $text_color   = isset( $options['ls_text_color'] ) ? esc_attr( $options['ls_text_color'] ) : '#ffffff';
        $font_size    = isset( $options['ls_font_size'] ) ? absint( $options['ls_font_size'] ) : 14;
        $position     = isset( $options['ls_position'] ) ? sanitize_text_field( $options['ls_position'] ) : 'top';
        $border_color = isset( $options['ls_border_color'] ) ? esc_attr( $options['ls_border_color'] ) : '';
        $closeable    = isset( $options['ls_closeable'] ) ? (int) $options['ls_closeable'] : 0;
        $delay        = isset( $options['ls_delay'] ) ? absint( $options['ls_delay'] ) : 0;

        $button_text  = isset( $options['ls_button_text'] ) ? sanitize_text_field( $options['ls_button_text'] ) : '';
        $button_url   = isset( $options['ls_button_url'] ) ? esc_url( $options['ls_button_url'] ) : '';
        $custom_css   = isset( $options['ls_button_custom_css'] ) ? wp_strip_all_tags( $options['ls_button_custom_css'] ) : '';

        // CSS Positioning & Borders
        $pos_style    = ( 'bottom' === $position ) ? 'position: fixed; bottom: 0; left: 0; width: 100%;' : 'position: relative;';
        $border_style = ! empty( $border_color ) ? 'border-bottom: 3px solid ' . $border_color . ';' : '';

        // 7. Output HTML & CSS
        ?>
        <?php if ( ! empty( $custom_css ) ) : ?>
            <style>
                #ls-notification-bar .ls-notification-btn {
                    <?php echo esc_html( $custom_css ); ?>
                }
            </style>
        <?php endif; ?>

        <div id="ls-notification-bar" data-delay="<?php echo esc_attr( $delay ); ?>" style="display: none; background-color: <?php echo esc_attr( $bg_color ); ?>; color: <?php echo esc_attr( $text_color ); ?>; font-size: <?php echo esc_attr( $font_size ); ?>px; padding: 12px 20px; text-align: center; font-weight: 500; <?php echo esc_attr( $pos_style ); ?> <?php echo esc_attr( $border_style ); ?> z-index: 99999;">
            <div class="ls-notification-content" style="display: inline-block; vertical-align: middle;">
                <?php echo $message; ?>
                
                <?php if ( ! empty( $button_text ) && ! empty( $button_url ) ) : ?>
                    <a href="<?php echo esc_url( $button_url ); ?>" class="ls-notification-btn" style="display: inline-block; margin-left: 15px; text-decoration: none; padding: 6px 14px; background: #ffffff; color: #2271b1; border-radius: 3px; font-weight: bold; transition: all 0.3s ease;">
                        <?php echo esc_html( $button_text ); ?>
                    </a>
                <?php endif; ?>
            </div>

            <?php if ( 1 === $closeable ) : ?>
                <button type="button" id="ls-close-notification" style="background: none; border: none; color: inherit; font-size: 16px; cursor: pointer; float: right; margin-right: 15px; font-weight: bold; vertical-align: middle;">&times;</button>
            <?php endif; ?>
        </div>
        <?php
    }
}