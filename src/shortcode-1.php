<?php
/**
 * Shortcode "Soubory ke stažení".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-downloads_plugin
 * 
 * @link https://www.gavick.com/blog/wordpress-tinymce-custom-buttons
 * @todo Labels in TinyMCE button should be localized properly!
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( ! function_exists( 'odwpdp_add_shortcode_1' ) ) :
    /**
     * Register shortcode "Soubory ke stažení".
     * @param array $atts
     * @param string $content (Optional.)
     * @return string
     */
    function odwpdp_add_shortcode_1( $atts, $content = null ) {
        // Collect attributes
        $attrs = shortcode_atts( array(
            'count'      => 0,
            'title'      => __( 'Soubory ke stažení', ODWPDP_SLUG ),
            'show_title' => 1,
            'orderby'    => 'title',
        ), $atts );

        // Get items to show
        $files = get_posts( array(
            'numberposts' => intval( $instance['count'] ),
            'post_type'   => ODWPDP_CPT,
        ) );

        // Render template
        ob_start( function() {} );
        include_once( ODWPDP_PATH . '/templates/shortcode-1.phtml' );
        $html = ob_get_flush();
        return apply_filters( 'odwpdp-shortcode-1', $html );
    }
endif;
add_shortcode( 'soubory_ke_stazeni', 'odwpdp_add_shortcode_1' );



// Functions below are for TinyMCE button for this shortcode:

if ( ! function_exists( 'odwpdp_add_tinymce_btn_shortcode_1' ) ) :
    /**
     * Enable easy access to our shortcode via TinyMCE button.
     * @global string $typenow
     */
    function odwpdp_add_tinymce_btn_shortcode_1() {
        global $typenow;

        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) ) {
            return;
        }
        // verify the post type
        if ( ! in_array( $typenow, array( 'post', 'page' ) ) ) {
            return;
        }
        // check if WYSIWYG is enabled
        if ( get_user_option( 'rich_editing' ) == 'true' ) {
            add_filter( 'mce_external_plugins', 'odwpdp_register_tinymce_plugin_shortcode_1' );
            add_filter( 'mce_buttons', 'odwpdp_register_tinymce_btn_shortcode_1' );
        }
    }
    endif;
add_action( 'admin_head', 'odwpdp_add_tinymce_btn_shortcode_1' );



if ( ! function_exists( 'odwpdp_register_tinymce_btn_shortcode_1' ) ) :
    /**
     * Register new TinyMCE buttons.
     * @param array $buttons
     * @return array
     */
    function odwpdp_register_tinymce_btn_shortcode_1( $buttons ) {
       array_push( $buttons, 'separator', 'odwpdp_shortcode_1' );
       return $buttons;
    }
endif;



if ( ! function_exists( 'odwpdp_register_tinymce_plugin_shortcode_1' ) ) :
    /**
     * Register our TinyMCE plugin.
     * @param array $plugins
     * @return array
     */
    function odwpdp_register_tinymce_plugin_shortcode_1( $plugins ) {
        $plugins['odwpdp_shortcode_1'] = plugins_url( '/js/tinymce-plugin-shortcode-1.js', ODWPDP_FILE );
        return $plugins;
    }
endif;

