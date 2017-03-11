<?php
/**
 * Plugin Name: Simple Downloads Plugin
 * Plugin URI: https://github.com/ondrejd/od-downloads-plugin
 * Description: Plugin that allows to manage files that you want to offer to your users for download. Allows to use either sidebar widget, shortcode with responsive table or using regular theme (downloads are <em>custom post types</em> so you can make templates for them). Visit my homepage to see my other WordPress plugins or use <a href="https://www.paypal.me/ondrejd">this link</a> to make a donation if you are satisfied with this plugin.
 * Version: 1.0.0
 * Author: ondrejd
 * Author URI: http://ondrejd.com/
 * License: GPLv3
 * Requires at least: 3.7
 * Tested up to: 4.7.3
 *
 * Text Domain: odwp-downloads_plugin
 * Domain Path: /languages
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/od-downloads-plugin for the canonical source repository
 * @package odwp-downloads_plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

// Constants
defined( 'ODWPDP_SLUG' )    || define( 'ODWPDP_SLUG', 'odwp-downloads_plugin' );
defined( 'ODWPDP_FILE' )    || define( 'ODWPDP_FILE', __FILE__ );
defined( 'ODWPDP_PATH' )    || define( 'ODWPDP_PATH', dirname( ODWPDP_FILE ) );
defined( 'ODWPDP_CPT' )     || define( 'ODWPDP_CPT', 'odwpdp_cpt' );
defined( 'ODWPDP_UPLOADS' ) || define( 'ODWPDP_UPLOADS', sprintf( "%s/%s/%s", WP_CONTENT_DIR, defined( 'UPLOADS' ) ? UPLOADS : 'uploads', ODWPDP_SLUG ) );
defined( 'ODWPDP_AJAX_UP' ) || define( 'ODWPDP_AJAX_UP', 'odwpdp_upload_file' );
defined( 'ODWPDP_ICON_16' ) || define( 'ODWPDP_ICON_16', '16x16' );
defined( 'ODWPDP_ICON_32' ) || define( 'ODWPDP_ICON_32', '32x32' );


// Register custom post types
// Custom post type "Soubor ke stažení"
include_once( ODWPDP_PATH . '/src/custom_post_type-1.php' );



if ( !function_exists( 'odwpdp_load_textdomain' ) ) :
    /**
     * Load plugin textdomain.
     */
    function odwpdp_load_textdomain() {
        load_plugin_textdomain( ODWPDP_SLUG, false, 'od-downloads-plugin/languages' );
    }
endif;
add_action( 'init', 'odwpdp_load_textdomain' );



/**
 * @var array $odwpdp_metaboxes
 */
$odwpdp_metaboxes = array(
    0 => __( 'Datum vyvěšení', ODWPDP_SLUG ),
    1 => __( 'Datum sejmutí', ODWPDP_SLUG ),
    2 => __( 'Nahrát soubor', ODWPDP_SLUG ),
);



if ( ! function_exists( 'odwpdp_metaboxes' ) ) :
    /**
     * Meta boxes for our `odwpdp_cpt` custom post type.
     * @global array $odwpdp_metaboxes
     */
    function odwpdp_metaboxes() {
        global $odwpdp_metaboxes;
        
        for( $i = 1; $i <= count( $odwpdp_metaboxes ); $i++ ) {
            add_meta_box(
               sprintf( 'odwpdp-metabox-%d', $i ),
               $odwpdp_metaboxes[$i - 1],
               sprintf( 'odwpdp_show_metabox_%d', $i ),
               ODWPDP_CPT,
               'normal',
               'high'
            );
        }
    }
endif;



if ( ! function_exists( 'odwpdp_add_metaboxes' ) ) :
    /**
     * Add our meta boxes.
     * @global array $odwpdp_metaboxes
     */
    function odwpdp_add_metaboxes() {
        global $odwpdp_metaboxes;

        // Include all meta boxes
        for (
                $i = 1;
                $i <= count( $odwpdp_metaboxes );
                include_once( ODWPDP_PATH . '/src/metabox-' . $i++ . '.php' )
        );

        add_action( 'add_meta_boxes', 'odwpdp_metaboxes' );
        
        for( $i = 1; $i <= count( $odwpdp_metaboxes ); $i++ ) {
            add_action( 'save_post', sprintf( 'odwpdp_save_metabox_%d', $i ), 10, 3 );
        }
    }
endif;

if ( is_admin() === true && count( $odwpdp_metaboxes ) > 0 ) {
    // Load our meta boxes only on edit post page.
    add_action( 'load-post.php', 'odwpdp_add_metaboxes' );
    add_action( 'load-post-new.php', 'odwpdp_add_metaboxes' );
}



if ( ! function_exists( 'odwpdp_admin_scripts' ) ) :
    /**
     * Append our CSS styles and Javascripts for the WP admin.
     */
    function odwpdp_admin_scripts() {
        if ( get_post_type() == ODWPDP_CPT ) {
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script( 'jquery-form' );
            wp_enqueue_script( 'jquery-ui-core' );
            wp_enqueue_script( 'jquery-ui-tabs' );
            wp_enqueue_script( 'jquery-ui-datepicker' );
            wp_enqueue_script( 'jquery-ui-slider' );
            wp_enqueue_script( 'jquery-ui-sortable' );
            
            wp_enqueue_script( 'thickbox' );
            wp_enqueue_style( 'thickbox' );
            wp_enqueue_script( 'media-upload' );
            wp_enqueue_media();
        }

        wp_enqueue_style( 'odwpdp-admin-css', plugins_url( '/css/admin.css', ODWPDP_FILE ) );
        wp_enqueue_script( 'odwpdp-admin-js', plugins_url( '/js/admin.js', ODWPDP_FILE ), array( 'jquery' ) );
    }
endif;
add_action( 'admin_enqueue_scripts', 'odwpdp_admin_scripts' );



if ( ! function_exists( 'odwpdp_public_scripts' ) ) :
    /**
     * Append our CSS styles and JavaScripts for the front-end.
     */
    function odwpdp_public_scripts() {
        // load our JavaScript
        wp_enqueue_script( 'odwpdp-public-js', plugins_url( '/js/public.js', ODWPDP_FILE ), array( 'jquery' ) );
        // and localize it...
        wp_localize_script( 'odwpdp-public-js', 'odwpdp_data', array(
            'ajax_download_url' => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce( 'updates' ),
            'l10n' => array()
        ) );

        wp_enqueue_style( 'odwpdp-public-css', plugins_url( '/css/public.css', ODWPDP_FILE ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'odwpdp_public_scripts' );



if ( ! function_exists( 'odwpdp_activate_plugin' ) ) :
    /**
     * Activate plugin.
     * @global wpdb $wpdb
     * @link https://premium.wpmudev.org/blog/creating-database-tables-for-plugins/
     */
    function odwpdp_activate_plugin() {
        // Create upload directory
        wp_mkdir_p( ODWPDP_UPLOADS );
    }
endif;
register_activation_hook( ODWPDP_FILE, 'odwpdp_activate_plugin' );



if ( ! function_exists( 'odwpdp_upload_file' ) ) :
    /**
     * Upload file.
     * @todo We need {@see check_ajax_referer()} get working.
     * @todo Delete file if already exists?
     * @todo Limit count of uploaded files just to one!
     */
    function odwpdp_upload_file() {
        //check_ajax_referer( ODWPDP_AJAX_UP );

        if ( ! current_user_can( 'upload_files' ) ) {
            die( '-2' );
        }

        if ( file_exists( ODWPDP_UPLOADS . '/' . $_FILES['package_file']['name'] ) ) {
            //@unlink( ODWPDP_UPLOADS . $_FILES['package_file']['name'] );
            die( '-1' );
        }

        $filename = $_FILES['package_file']['name'];

        move_uploaded_file( $_FILES['package_file']['tmp_name'], ODWPDP_UPLOADS . '/' . $filename );

        printf( "|||%s|||", $filename );
        exit;
    }
endif;

if ( is_admin() ) {
    add_action( sprintf( 'wp_ajax_%s', ODWPDP_AJAX_UP ), 'odwpdp_upload_file' );
}


// Include widget "Soubory ke stažení"
include_once( ODWPDP_PATH . '/src/widget-1.php' );
// Include shortcode "Soubory ke stažení"
include_once( ODWPDP_PATH . '/src/shortcode-1.php' );



if ( ! function_exists( 'odwpdp_get_file_icon' ) ):
    /**
     * @internal
     * @param string $ext (Optional.)
     * @param string $size (Optional.) Defaultly "16x16".
     * @return string URL to file type icon.
     */
    function odwpdp_get_file_icon( $ext = null, $size = ODWPDP_ICON_16 ) {
        if ( ! in_array( $size, array( ODWPDP_ICON_16, ODWPDP_ICON_32 ) ) ) {
            $size = ODWPDP_ICON_16;
        }

        if ( empty( $ext ) ) {
            $path = "img/{$size}/document_empty.png";
        }
        else {
            $path = "img/{$size}/file_extension_{$ext}.png";
        }

        $full_path = ODWPDP_PATH . '/' . $path;

        if ( ! file_exists( $full_path ) ) {
            $path = "img/{$size}/document_red.png";
        }

        $full_url = plugins_url( $path, ODWPDP_FILE );

        return apply_filters( 'odwpdp-file-icon', $full_url, $ext, $size );
    }
endif;



if ( ! function_exists( 'odwpdp_get_file_info' ) ):
    /**
     * Returns array with info about given file.
     *
     * <p>Info array contains these keys:</p>
     * <ul>
     *   <li><code>exist</code>; <i>boolean</i>, <i>true</i> if file exists</li>
     *   <li><code>ext</code>; <i>string</i>, file's extension</li>
     *   <li><code>file</code>; <i>string</i>, file's name</li>
     *   <li><code>icon_16</code>; <i>string</i>, 16x16 icon by file type</li>
     *   <li><code>icon_32</code>; <i>string</i>, 32x32 icon by file type</li>
     *   <li><code>path</code>; <i>string</i>, full file path</li>
     *   <li><code>size</code>; <i>integer</i>, size of file</li>
     *   <li><code>url</code>; <i>string</i>,  URL for download link</li>
     * </ul>
     *
     * @param integer $post_id
     * @return array Array with file info.
     * @uses odwpdp_get_file_url()
     * @uses odwpdp_get_file_icon()
     */
    function odwpdp_get_file_info( $post_id ) {
        $file = get_post_meta( $post_id, 'odwpdp-metabox-3', true );
        $info = array(
            'exist'     => false,
            'ext'       => null,
            'file'      => $file,
            'icon_16'   => null,
            'icon_32'   => null,
            'path'      => null,
            'size'      => null,
            'url'      => plugins_url( 'download-file.php', ODWPDP_FILE ) . '?post_id=' . $post_id,
        );
        $upload_dir      = wp_upload_dir();
        $info['path']    = empty( $file ) ? '' : sprintf( '%s/%s/%s', $upload_dir['basedir'], ODWPDP_SLUG, $file );
        $info['exist']   = file_exists( $info['path'] );
        $info['size']    = size_format( filesize( $info['path'] ), 2);
        $file_ext_arr    = strpos( $file, '.' ) !== false ? explode( '.', $file ) : array();
        $info['ext']     = count( $file_ext_arr ) == 0 ? '' : $file_ext_arr[count( $file_ext_arr ) - 1];
        $info['icon_16'] = odwpdp_get_file_icon( $info['ext'] );
        $info['icon_32'] = odwpdp_get_file_icon( $info['ext'], ODWPDP_ICON_32 );

        return apply_filters( 'odwpdp-file-info', $info, $file );
    }
endif;



if ( ! function_exists( 'odwpdp_get_avail_orderby_vals' ) ):
    /**
     * @internal
     * @return array Available values for "order by" field.
     */
    function odwpdp_get_avail_orderby_vals() {
        return array(
            'title'           => __( 'Názvu', ODWPDP_SLUG ),
            'puton_date'      => __( 'Data vyvěšení', ODWPDP_SLUG ),
            'putoff_date'     => __( 'Data sejmutí', ODWPDP_SLUG ),
            'downloads_count' => __( 'Počtu stažení', ODWPDP_SLUG ),
        );
    }
endif;



if ( ! function_exists( 'odwpdp_get_orderby_meta_key' ) ) :
    /**
     * @internal
     * @param string $orderby One of orderby values except <code>title</code> ({@see odwpdp_get_avail_orderby_vals()}).
     * @return string
     */
    function odwpdp_get_orderby_meta_key( $orderby ) {
        $meta_key = '';
        switch( $orderby ) {
            case 'puton_date'      :
            case 'putoff_date'     :
            case 'downloads_count' :
                $meta_key = "odwpdp-{$orderby}";
                break;
        }
        return $meta_key;
    }
endif;



if ( ! function_exists( 'odwpdp_get_avail_order_vals' ) ):
    /**
     * @internal
     * @return array Available values for "order" field.
     */
    function odwpdp_get_avail_order_vals() {
        return array(
            'DESC' => __( 'Sestupně', ODWPDP_SLUG ),
            'ASC'  => __( 'Vzestupně', ODWPDP_SLUG ),
        );
    }
endif;



if ( ! function_exists( 'odwpdp_odwpdp_print_order_links_links' ) ) :
    /**
     * @internal Prints order arrows in files table head.
     * @param string $url
     * @param array  $atrs
     * @param string $cur
     * @return void
     */
    function odwpdp_print_order_links( $url, $atrs, $cur ) {
?>
<span class="order-icons">
    <?php if ( $atrs['orderby'] == $cur && $atrs['order'] == 'ASC' ) : ?>
    <span title="<?php _e( 'Seřazeno vzestupně', ODWPDP_SLUG ); ?>" class="arrow-up used-order"></span>
    <?php  else : ?>
    <a href="<?php echo $url . '&amp;odwpdp_orderby=' . $cur . '&amp;odwpdp_order=ASC'; ?>" title="<?php _e( 'Seřadit vzestupně', ODWPDP_SLUG ); ?>" class="arrow-up"></a>
    <?php endif; ?>
    <?php if ( $atrs['orderby'] == $cur && $atrs['order'] == 'DESC' ) : ?>
    <span title="<?php _e( 'Seřazeno sestupně', ODWPDP_SLUG ); ?>" class="arrow-down used-order"></span>
    <?php else : ?>
    <a href="<?php echo $url . '&amp;odwpdp_orderby=' . $cur . '&amp;odwpdp_order=DESC'; ?>" title="<?php _e( 'Seřadit sestupně', ODWPDP_SLUG ); ?>" class="arrow-down"></a>
    <?php endif; ?>
</span>
<?php
    }
endif;
