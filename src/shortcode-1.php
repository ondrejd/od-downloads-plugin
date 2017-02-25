<?php
/**
 * Shortcode "Soubory ke stažení".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-downloads_plugin
 * @link https://www.gavick.com/blog/wordpress-tinymce-custom-buttons
 * @todo Labels in TinyMCE button should be localized properly!
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( ! function_exists( 'odwpdp_add_shortcode_1' ) ) :
    /**
     * Register shortcode "Soubory ke stažení".
     * @link https://developer.wordpress.org/reference/classes/wp_query/
     * @link https://www.smashingmagazine.com/2013/01/using-wp_query-wordpress/
     * @link http://www.billerickson.net/code/wp_query-arguments/
     * @param array $atts
     * @param string $content (Optional.)
     * @return string
     * 
     * @todo Default count should be set via user preferences!
     */
    function odwpdp_add_shortcode_1( $atts, $content = null ) {    
        global $wp;

        // Collect attributes
        $attrs = shortcode_atts( array(
            'count'           => 5,
            'title'           => __( 'Soubory ke stažení', ODWPDP_SLUG ),
            'show_title'      => 1,
            'show_pagination' => 1,
            'orderby'         => 'title',
            'order'           => 'ASC',
            'enable_sort'     => 1,
        ), $atts );

        // Sanitize attributes
        $attrs['count'] = (int) $attrs['count'] <= 0 ? -1 : (int) $attrs['count'];
        $attrs['show_title'] = (bool) $attrs['show_title'];
        $attrs['show_pagination'] = (bool) $attrs['show_pagination'];
        $attrs['enable_sort'] = (bool) $attrs['enable_sort'];

        $orderby = filter_input( INPUT_GET, 'odwpdp_orderby' );
        $order = filter_input( INPUT_GET, 'odwpdp_order' );
        
        $attrs['orderby'] = isset( $_GET['odwpdp_orderby'] ) ? $_GET['odwpdp_orderby'] : $attrs['orderby'];
        $attrs['order'] = isset( $_GET['odwpdp_order'] ) ? $_GET['odwpdp_order'] : $attrs['order'];
        $attrs['orderby'] = ! in_array( $attrs['orderby'], array_keys( odwpdp_get_avail_orderby_vals() ) ) ? 'title' : $attrs['orderby'];
        $attrs['order'] = ! in_array( $attrs['order'], array_keys( odwpdp_get_avail_order_vals() ) ) ? 'DESC' : $attrs['order'];

        // Prepare query arguments
        $query_args = array();
        $query_args['post_type'] = ODWPDP_CPT;
        $query_args['nopaging']  = ! $attrs['show_pagination'];
        $query_args['posts_per_page'] = $attrs['count'];
        $query_args['order'] = $attrs['order'];

        if ( $attrs['orderby'] == 'title' ) {
            $query_args['orderby'] = 'title';
        }
        else {
            $query_args['meta_query'] = array();
            $query_args['meta_query'][] = array(
                'key' => 'odwpdp-metabox-1',
                'type' => 'DATE'
            );
        }

        $odwpdp_paged = (int) filter_input( INPUT_GET, 'odwpdp_paged', FILTER_VALIDATE_INT );
        $query_args['paged'] = max( 1, $odwpdp_paged );

        // Create query
        $query = new WP_Query( $query_args );

        // Current URL
        $current_url = home_url( add_query_arg( array(), $wp->request ) );

        // Render template
        ob_start( function() {} );
        include_once( ODWPDP_PATH . '/templates/shortcode-1.phtml' );
        $out = ob_get_flush();
        $html = apply_filters( 'odwpdp-shortcode-1', $out );

        // Reset WP query and post data
        wp_reset_postdata();

        return $html;
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



if ( ! function_exists( 'odwpdp_register_tinymce_plugin_locales_shortcode_1' ) ) :
    /**
     * @internal Load localization for our TinyMCE plugin.
     * @param array $locales
     * @return array
     */
    function odwpdp_register_tinymce_plugin_locales_shortcode_1( $locales ) {
        $locales['odwpdp'] = ODWPDP_PATH . '/js/tinymce-plugins-locales.php';
        return $locales;
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
if ( is_admin() ) {
    add_filter( 'mce_external_languages', 'odwpdp_register_tinymce_plugin_locales_shortcode_1', 10, 1 );
    add_action( 'load-post-new.php', 'odwpdp_add_shortcode_help_1' );
    add_action( 'load-post.php', 'odwpdp_add_shortcode_help_1' );
}

