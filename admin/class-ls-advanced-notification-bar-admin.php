<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://levent-sali.gr
 * @since      1.0.0
 *
 * @package    Ls_Advanced_Notification_Bar
 * @subpackage Ls_Advanced_Notification_Bar/admin
 */

class Ls_Advanced_Notification_Bar_Admin {

    private string $plugin_name;
    private string $version;

    public function __construct( string $plugin_name, string $version ) {
        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        add_action( 'admin_init', [ $this, 'register_settings' ] );
        add_action( 'admin_init', [ $this, 'add_settings' ] );
        add_action( 'admin_menu', [ $this, 'admin_menu' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_color_picker' ] );
    }

    public function enqueue_styles(): void {
        wp_enqueue_style( 
            $this->plugin_name, 
            plugin_dir_url( __FILE__ ) . 'css/ls-advanced-notification-bar-admin.css', 
            [], 
            $this->version, 
            'all' 
        );
    }

    public function enqueue_scripts(): void {
        wp_enqueue_script( 
            $this->plugin_name, 
            plugin_dir_url( __FILE__ ) . 'js/ls-advanced-notification-bar-admin.js', 
            [ 'jquery' ], 
            $this->version, 
            false 
        );
    }

    public function enqueue_admin_color_picker( string $hook ): void {
        if ( 'settings_page_ls-advanced-notification-bar-settings-page' !== $hook ) {
            return;
        }
        // Color picker
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'ls-admin-color-init', plugin_dir_url( __FILE__ ) . 'js/color-picker-init.js', [ 'jquery', 'wp-color-picker' ], $this->version, true );

        // Select2
        wp_enqueue_style( 'select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css', [], '4.1.0' );
        wp_enqueue_script( 'select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', [ 'jquery' ], '4.1.0', true );
        
        // Inline JS
        $placeholder = esc_js( __( 'Search page...', 'ls-advanced-notification-bar' ) );
        wp_add_inline_script( 'select2-js', 'jQuery(document).ready(function($) { $("#ls_exclude_posts").select2({ placeholder: "' . $placeholder . '" }); });' );
    }

    public function register_settings(): void {
        register_setting(
            'ls_settings_group',
            'ls_settings',
            [ $this, 'sanitize' ]
        );
    }

    public function add_settings(): void {
        add_settings_section(
            'ls_settings_section',
            __( 'Advanced Notification Bar - Premium Settings', 'ls-advanced-notification-bar' ),
            [ $this, 'section_info' ],
            'ls-advanced-notification-bar-settings-page'
        );

        // 1. Enabled
        add_settings_field( 'ls_enabled', __( 'Enabled', 'ls-advanced-notification-bar' ), [ $this, 'enabled_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );
        
        // 2. Notification Text
        add_settings_field( 'ls_notification_text', __( 'Notification Text', 'ls-advanced-notification-bar' ), [ $this, 'notification_text_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // 3. Background Color
        add_settings_field( 'ls_bg_color', __( 'Background Color', 'ls-advanced-notification-bar' ), [ $this, 'bg_color_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // 4. Text Color
        add_settings_field( 'ls_text_color', __( 'Text Color', 'ls-advanced-notification-bar' ), [ $this, 'text_color_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // 5. Expiry Date
        add_settings_field( 'ls_expiry_date', __( 'Expiry Date', 'ls-advanced-notification-bar' ), [ $this, 'expiry_date_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // --- PREMIUM FIELDS ---
        // 6. Open/Close feature
        add_settings_field( 'ls_closeable', __( 'Close Button (X)', 'ls-advanced-notification-bar' ), [ $this, 'closeable_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // 7. Delay (milliseconds)
        add_settings_field( 'ls_delay', __( 'Display Delay (in ms)', 'ls-advanced-notification-bar' ), [ $this, 'delay_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // 8. Access Control
        add_settings_field( 'ls_access_control', __( 'Access Control', 'ls-advanced-notification-bar' ), [ $this, 'access_control_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // 9. Visibility (Exclude Post IDs)
        add_settings_field( 'ls_exclude_posts', __( 'Exclude Page/Post IDs', 'ls-advanced-notification-bar' ), [ $this, 'exclude_posts_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // 10. Positioning
        add_settings_field( 'ls_position', __( 'Bar Position', 'ls-advanced-notification-bar' ), [ $this, 'position_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // 11. Bottom Border
        add_settings_field( 'ls_border_color', __( 'Bottom Border Color', 'ls-advanced-notification-bar' ), [ $this, 'border_color_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // 12. Font Size
        add_settings_field( 'ls_font_size', __( 'Font Size (px)', 'ls-advanced-notification-bar' ), [ $this, 'font_size_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );

        // --- BUTTON & CUSTOM CSS FIELDS ---
        add_settings_field( 'ls_button_text', __( 'Button Text', 'ls-advanced-notification-bar' ), [ $this, 'button_text_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );
        add_settings_field( 'ls_button_url', __( 'Button Link (URL)', 'ls-advanced-notification-bar' ), [ $this, 'button_url_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );
        add_settings_field( 'ls_button_custom_css', __( 'Button Custom CSS', 'ls-advanced-notification-bar' ), [ $this, 'button_custom_css_callback' ], 'ls-advanced-notification-bar-settings-page', 'ls_settings_section' );
    }

    public function section_info(): void {
        esc_html_e( 'Full configuration of all advanced features and options.', 'ls-advanced-notification-bar' );
    }

    public function enabled_callback(): void {
        $options = get_option( 'ls_settings' );
        $checked = isset( $options['ls_enabled'] ) ? (int) $options['ls_enabled'] : 0;
        printf( '<input type="checkbox" id="ls_enabled" name="ls_settings[ls_enabled]" value="1" %s />', checked( 1, $checked, false ) );
    }

    public function notification_text_callback(): void {
        $options = get_option( 'ls_settings' );
        $text    = isset( $options['ls_notification_text'] ) ? esc_attr( $options['ls_notification_text'] ) : '';
        printf( '<input type="text" id="ls_notification_text" name="ls_settings[ls_notification_text]" value="%s" class="regular-text" />', $text );
    }

    public function bg_color_callback(): void {
        $options = get_option( 'ls_settings' );
        $color   = isset( $options['ls_bg_color'] ) ? esc_attr( $options['ls_bg_color'] ) : '#2271b1';
        printf( '<input type="text" id="ls_bg_color" name="ls_settings[ls_bg_color]" value="%s" class="ls-color-picker" />', $color );
    }

    public function text_color_callback(): void {
        $options = get_option( 'ls_settings' );
        $color   = isset( $options['ls_text_color'] ) ? esc_attr( $options['ls_text_color'] ) : '#ffffff';
        printf( '<input type="text" id="ls_text_color" name="ls_settings[ls_text_color]" value="%s" class="ls-color-picker" />', $color );
    }

    public function expiry_date_callback(): void {
        $options = get_option( 'ls_settings' );
        $date    = isset( $options['ls_expiry_date'] ) ? esc_attr( $options['ls_expiry_date'] ) : '';
        printf( '<input type="date" id="ls_expiry_date" name="ls_settings[ls_expiry_date]" value="%s" />', $date );
    }

    public function closeable_callback(): void {
        $options = get_option( 'ls_settings' );
        $checked = isset( $options['ls_closeable'] ) ? (int) $options['ls_closeable'] : 0;
        printf( '<input type="checkbox" id="ls_closeable" name="ls_settings[ls_closeable]" value="1" %s /> %s', checked( 1, $checked, false ), esc_html__( 'Enable close button', 'ls-advanced-notification-bar' ) );
    }

    public function delay_callback(): void {
        $options = get_option( 'ls_settings' );
        $delay   = isset( $options['ls_delay'] ) ? absint( $options['ls_delay'] ) : 0;
        printf( '<input type="number" id="ls_delay" name="ls_settings[ls_delay]" value="%d" class="small-text" /> ms (%s)', $delay, esc_html__( 'e.g. 2000 for 2 seconds', 'ls-advanced-notification-bar' ) );
    }

    public function access_control_callback(): void {
        $options      = get_option( 'ls_settings' );
        $access       = isset( $options['ls_access_control'] ) ? $options['ls_access_control'] : 'all';
        $options_list = [
            'all'   => __( 'All Users', 'ls-advanced-notification-bar' ),
            'guest' => __( 'Guests Only (Logged-out)', 'ls-advanced-notification-bar' ),
            'user'  => __( 'Logged-in Users Only', 'ls-advanced-notification-bar' ),
        ];
        echo '<select id="ls_access_control" name="ls_settings[ls_access_control]">';
        foreach ( $options_list as $key => $label ) {
            printf( '<option value="%s" %s>%s</option>', esc_attr( $key ), selected( $access, $key, false ), esc_html( $label ) );
        }
        echo '</select>';
    }

    public function exclude_posts_callback(): void {
        $options      = get_option( 'ls_settings' );
        $selected_ids = isset( $options['ls_exclude_posts'] ) ? (array) $options['ls_exclude_posts'] : [];

        $posts = get_posts([
            'post_type'      => [ 'post', 'page' ],
            'posts_per_page' => -1,
            'post_status'    => 'publish',
            'orderby'        => 'title',
            'order'          => 'ASC',
        ]);

        echo '<select id="ls_exclude_posts" name="ls_settings[ls_exclude_posts][]" multiple="multiple" style="width: 350px;" class="ls-select2">';
        echo '<option value="">' . esc_html__( '-- Select Pages/Posts --', 'ls-advanced-notification-bar' ) . '</option>';
        foreach ( $posts as $p ) {
            $selected = in_array( (string) $p->ID, $selected_ids, true ) ? 'selected="selected"' : '';
            printf( '<option value="%d" %s>%s (%s)</option>', esc_attr( $p->ID ), $selected, esc_html( $p->post_title ), esc_html( ucfirst( $p->post_type ) ) );
        }
        echo '</select>';
        echo '<p class="description">' . esc_html__( 'Select one or more pages/posts where the notification bar should not appear.', 'ls-advanced-notification-bar' ) . '</p>';
    }

    public function position_callback(): void {
        $options = get_option( 'ls_settings' );
        $pos     = isset( $options['ls_position'] ) ? $options['ls_position'] : 'top';
        ?>
        <select id="ls_position" name="ls_settings[ls_position]">
            <option value="top" <?php selected( $pos, 'top' ); ?>><?php esc_html_e( 'Top of Screen', 'ls-advanced-notification-bar' ); ?></option>
            <option value="bottom" <?php selected( $pos, 'bottom' ); ?>><?php esc_html_e( 'Bottom of Screen', 'ls-advanced-notification-bar' ); ?></option>
        </select>
        <?php
    }

    public function border_color_callback(): void {
        $options = get_option( 'ls_settings' );
        $color   = isset( $options['ls_border_color'] ) ? esc_attr( $options['ls_border_color'] ) : '';
        printf( '<input type="text" id="ls_border_color" name="ls_settings[ls_border_color]" value="%s" class="ls-color-picker" />', $color );
        echo '<p class="description">' . esc_html__( 'Leave empty if you do not want a border.', 'ls-advanced-notification-bar' ) . '</p>';
    }

    public function font_size_callback(): void {
        $options = get_option( 'ls_settings' );
        $size    = isset( $options['ls_font_size'] ) ? absint( $options['ls_font_size'] ) : 14;
        printf( '<input type="number" id="ls_font_size" name="ls_settings[ls_font_size]" value="%d" class="small-text" /> px', $size );
    }

    public function sanitize( array $input ): array {
        $clean = [];
        $clean['ls_enabled']           = isset( $input['ls_enabled'] ) ? absint( $input['ls_enabled'] ) : 0;
        $clean['ls_notification_text'] = isset( $input['ls_notification_text'] ) ? sanitize_text_field( $input['ls_notification_text'] ) : '';
        $clean['ls_bg_color']          = isset( $input['ls_bg_color'] ) ? sanitize_hex_color( $input['ls_bg_color'] ) : '#2271b1';
        $clean['ls_text_color']        = isset( $input['ls_text_color'] ) ? sanitize_hex_color( $input['ls_text_color'] ) : '#ffffff';
        $clean['ls_expiry_date']       = isset( $input['ls_expiry_date'] ) ? sanitize_text_field( $input['ls_expiry_date'] ) : '';
        
        $clean['ls_closeable']         = isset( $input['ls_closeable'] ) ? absint( $input['ls_closeable'] ) : 0;
        $clean['ls_delay']             = isset( $input['ls_delay'] ) ? absint( $input['ls_delay'] ) : 0;
        $clean['ls_access_control']    = isset( $input['ls_access_control'] ) ? sanitize_text_field( $input['ls_access_control'] ) : 'all';
        $clean['ls_exclude_posts']     = isset( $input['ls_exclude_posts'] ) ? array_map( 'absint', (array) $input['ls_exclude_posts'] ) : [];
        $clean['ls_position']          = isset( $input['ls_position'] ) ? sanitize_text_field( $input['ls_position'] ) : 'top';
        $clean['ls_border_color']      = isset( $input['ls_border_color'] ) ? sanitize_hex_color( $input['ls_border_color'] ) : '';
        $clean['ls_font_size']         = isset( $input['ls_font_size'] ) ? absint( $input['ls_font_size'] ) : 14;

        $clean['ls_button_text']       = isset( $input['ls_button_text'] ) ? sanitize_text_field( $input['ls_button_text'] ) : '';
        $clean['ls_button_url']        = isset( $input['ls_button_url'] ) ? esc_url_raw( $input['ls_button_url'] ) : '';
        $clean['ls_button_custom_css'] = isset( $input['ls_button_custom_css'] ) ? sanitize_textarea_field( $input['ls_button_custom_css'] ) : '';

        return $clean;
    }

    public function admin_menu(): void {
        add_options_page( 
            __( 'LS Advanced Notification Bar', 'ls-advanced-notification-bar' ), 
            __( 'LS Notification Bar', 'ls-advanced-notification-bar' ), 
            'manage_options', 
            'ls-advanced-notification-bar-settings-page', 
            [ $this, 'settings_page' ] 
        );
    }

    public function settings_page(): void {
        ?>
        <div class="wrap">
            <h1><?php esc_html_e( 'LS Advanced Notification Bar - Premium Settings', 'ls-advanced-notification-bar' ); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields( 'ls_settings_group' );
                do_settings_sections( 'ls-advanced-notification-bar-settings-page' );
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function button_text_callback(): void {
        $options = get_option( 'ls_settings' );
        $text    = isset( $options['ls_button_text'] ) ? esc_attr( $options['ls_button_text'] ) : '';
        printf( '<input type="text" id="ls_button_text" name="ls_settings[ls_button_text]" value="%s" class="regular-text" />', $text );
        echo '<p class="description">' . esc_html__( 'Leave empty if you do not want a button.', 'ls-advanced-notification-bar' ) . '</p>';
    }

    public function button_url_callback(): void {
        $options = get_option( 'ls_settings' );
        $url     = isset( $options['ls_button_url'] ) ? esc_url( $options['ls_button_url'] ) : '';
        printf( '<input type="url" id="ls_button_url" name="ls_settings[ls_button_url]" value="%s" class="regular-text" />', $url );
    }

    public function button_custom_css_callback(): void {
        $options = get_option( 'ls_settings' );
        $css     = isset( $options['ls_button_custom_css'] ) ? esc_textarea( $options['ls_button_custom_css'] ) : '';
        printf( '<textarea id="ls_button_custom_css" name="ls_settings[ls_button_custom_css]" rows="4" cols="50" class="large-text code">%s</textarea>', $css );
        echo '<p class="description">' . sprintf( esc_html__( 'Write custom CSS rules (e.g. %s).', 'ls-advanced-notification-bar' ), '<code>background: #ff5722; color: #fff; border-radius: 4px; padding: 5px 15px;</code>' ) . '</p>';
    }
}