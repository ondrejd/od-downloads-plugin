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
            'count'           => 5,
            'title'           => __( 'Soubory ke stažení', ODWPDP_SLUG ),
            'show_title'      => 1,
            'show_pagination' => 1,
            'orderby'         => 'title',
            'order'           => 'ASC',
            'enable_sort'     => 1,
            'enable_ajax'     => 1,
        ), $atts );

        // Prepare arguments for the query
        $query_args  = array( 'post_type' => ODWPDP_CPT );
        // Number of items
        if ( $attrs['count'] > 0 ) {
            $query_args['numberposts'] = $attrs['count'];
        }
        // Ordering
        if ( array_key_exists( 'orderby', $instance ) ) {
            if ( ! in_array( $instance['orderby'], array_keys( odwpdp_get_avail_orderby_vals() ) ) ) {
                $instance['orderby'] = 'title';
            }

            if ( $instance['orderby'] == 'title' ) {
                $query_args['orderby'] = 'title';
            } else {
                $query_args['orderby'] = 'meta_value';//meta_value_num
                $query_args['meta_key'] = $instance['orderby'];
            }

            if ( ! array_key_exists( 'order', $instance ) ) {
                $query_args['order'] = 'DESC';
            }
            else {
                if ( in_array( $instance['order'], array_keys( odwpdp_get_avail_order_vals() ) ) ) {
                    $query_args['order'] = $instance['order'];
                } else {
                    $query_args['order'] = 'DESC';
                }
            }
        }

        // XXX Pagination
        $total_count = 0;
        $page_count = 0;
        $current_page = 0;

        // Query items to show
        $files = get_posts( $query_args );

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



if ( ! function_exists( 'odwpdp_add_shortcode_help_1' ) ) :
/**
 * Adds the help tabs to the current page
 */
function odwpdp_add_shortcode_help_1() {
    $screen = get_current_screen();

    if ( ! in_array( $screen->post_type, array( 'post', 'page' ) ) ) {
        return;
    }

    $screen->add_help_tab( array(
        'title'    => __( 'Soubory ke stažení', ODWPDP_SLUG ),
        'id'       => 'odwpdp-shortcode-help-1',
        'callback' => 'odwpdp_shortcode_help_1'
    ) );

    $screen->set_help_sidebar( sprintf( '<a href="%s">%s</a>', '#', __( 'Nápověda na wiki projektu', ODWPDP_SLUG ) ) );
}
endif;



if ( ! function_exists( 'odwpdp_shortcode_help_1' ) ) :
/**
 * Outputs the content for the 'More About Books' Help Tab
 */
function odwpdp_shortcode_help_1() {
    ob_start( function() {} );
    include_once( ODWPDP_PATH . '/templates/shortcode-help-1.phtml' );
    $html = ob_get_flush();
    echo apply_filters( 'odwpdp-shortcode-help-1', $html );
}
endif;

// Register our new help tab
add_action( 'load-post-new.php', 'odwpdp_add_shortcode_help_1' );
add_action( 'load-post.php', 'odwpdp_add_shortcode_help_1' );
