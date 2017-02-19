<?php
/**
 * Plugin Name: odwp-downloads_plugin
 * Plugin URI: https://bitbucket.org/ondrejd/odwp-downloads_plugin
 * Description: 
 * Version: 1.0.0
 * Author: Ondřej Doněk
 * Author URI: 
 * License: GPLv3
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
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



/**
 * @var array $odwpdp_metaboxes
 */
$odwpdp_metaboxes = array(
    0 => __( 'Datum vyvěšení', ODWPDP_SLUG ),
    1 => __( 'Datum sejmutí', ODWPDP_SLUG ),
    2 => __( 'Nahrát soubor', ODWPDP_SLUG ),
);



if ( !function_exists( 'odwpdp_metaboxes' ) ) :
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



if ( !function_exists( 'odwpdp_add_metaboxes' ) ) :
    /**
     * Add our meta boxes.
     * @global array $odwpdp_metaboxes
     */
    function odwpdp_add_metaboxes() {
        global $odwpdp_metaboxes;

        // Include all meta boxes
        // Metabox "Datum vyveseni"
        include_once( ODWPDP_PATH . '/src/metabox-1.php' );
        // Metabox "Datum sejmuti"
        include_once( ODWPDP_PATH . '/src/metabox-2.php' );
        // Metabox "Nahrat soubor"
        include_once( ODWPDP_PATH . '/src/metabox-3.php' );

        add_action( 'add_meta_boxes', 'odwpdp_metaboxes' );
        
        for( $i = 1; $i <= count( $odwpdp_metaboxes ); $i++ ) {
            add_action( 'save_post', sprintf( 'odwpdp_save_metabox_%d', $i ), 10, 3 );
        }
    }
endif;

if ( is_admin() === true ) {
    // Load our meta boxes only on edit post page.
    add_action( 'load-post.php', 'odwpdp_add_metaboxes' );
    add_action( 'load-post-new.php', 'odwpdp_add_metaboxes' );
}



if ( !function_exists( 'odwpdp_admin_scripts' ) ) :
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



if ( !function_exists( 'odwpdp_public_scripts' ) ) :
    /**
     * Append our CSS styles and JavaScripts for the front-end.
     * @global WP_Scripts $wp_scripts
     */
    function odwpdp_public_scripts() {
        global $wp_scripts;
 
        // load jQuery UI
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-widget' );
        wp_enqueue_script( 'jquery-ui-mouse' );
        wp_enqueue_script( 'jquery-ui-accordion' );
        wp_enqueue_script( 'jquery-ui-autocomplete' );
        wp_enqueue_script( 'jquery-ui-slider' );
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jquery-ui-draggable' );
        wp_enqueue_script( 'jquery-ui-droppable' );
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'jquery-ui-resize' );
        wp_enqueue_script( 'jquery-ui-dialog' );
        wp_enqueue_script( 'jquery-ui-button' );
        wp_enqueue_script( 'jquery-effects-core' );

        // load jQuery.fileDownload
        wp_enqueue_script(
                'odwpdp-jquery-fileDownload',
                plugins_url( '/js/jquery.fileDownload.js', ODWPDP_FILE),
                array( 'jquery' )
        );

        // load our JavaScript
        wp_enqueue_script(
                'odwpdp-public-js',
                plugins_url( '/js/public.js', ODWPDP_FILE ),
                array( 'odwpdp-jquery-fileDownload' )
        );
        // and localize it...
        wp_localize_script( 'odwpdp-public-js', 'odwpdp_data', array(
            'ajax_download_url' => admin_url( 'admin-ajax.php' ),
            'ajax_nonce' => wp_create_nonce( 'updates' ),
            'l10n' => array()
        ) );

        // load the jquery ui theme
        $queryui = $wp_scripts->query( 'jquery-ui-core' );
        $protocol = is_ssl() ? 'https://' : 'http://';
        $url = "{$protocol}ajax.googleapis.com/ajax/libs/jqueryui/{$queryui->ver}/themes/smoothness/jquery-ui.css";

        wp_register_style( 'jquery-ui', $url );
        wp_enqueue_style( 'jquery-ui' );
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
            'title'       => __( 'Názvu', ODWPDP_SLUG ),
            'puton_date'  => __( 'Data vyvěšení', ODWPDP_SLUG ),
            'putoff_date' => __( 'Data sejmutí', ODWPDP_SLUG ),
        );
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



if ( ! function_exists( 'odwpdp_get_download_url' ) ):
    /**
     * @param WP_Post|integer $post Post object or post's ID.
     * @return XXX ...
     */
    function odwpdp_get_download_url( $post ) {
        $post_id = ( $post instanceof WP_Post ) ? $post->ID : intval( $post );
        // ...
    }
endif;



if ( ! function_exists( 'odwpdp_download_via_ajax' ) ):
    /**
     * Handle request for download.
     * @return void
     * @todo Add correct `Content-type` for more file types!
     * @todo Track unsuccessfull download attempts!
     */
    function odwpdp_download_via_ajax() {
        $data    = filter_input( INPUT_POST, 'data', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
        $post_id = false;

        if ( is_array( $data ) ) {
            if ( array_key_exists( 'post_id', $data ) ) {
                $post_id = (int) $data['post_id'];
            }
        }

        // Post's ID is not defined
        if ( $post_id === false || empty( $post_id ) ) {
            echo wp_json_encode( array(
                'message' => __( 'Nebylo zadáno ID souboru ke stažení!', ODWPDP_SLUG ),
            ) );
            wp_die();
        }

        // Get file name and more informations about it
        $file = get_post_meta( $post_id, 'odwpdp-metabox-3', true );
        $info = odwpdp_get_file_info( $file );

        // File doesn't exist
        if ( $info['exist'] !== true || empty( $info['path'] ) || ! is_readable( $info['path'] ) ) {
            echo wp_json_encode( array(
                'message' => __( 'Omlouváme se, ale soubor ke stažení nebyl nalezen!', ODWPDP_SLUG ),
            ) );
            wp_die();
        }

        // Updates download counter
        odwpdp_increase_downloads_count( $post_id );

        // Provide file for download
        header( 'Content-Description: File Transfer' );

        switch ( $info['ext'] ) {
            case 'pdf':
                header( 'Content-type: application/pdf' );
                break;

            default:
                header( 'Content-Type: application/octet-stream' );
                break;
        }

        header( 'Content-Disposition: attachment; filename="' . $file . '"' );
        header( 'Expires: 0' );
        header( 'Cache-Control: must-revalidate' );
        header( 'Pragma: public' );
        header( 'Content-Length: ' . filesize( $info['path'] ) );
        readfile( $info['path'] );
        wp_die();
    }
endif;
add_action( 'wp_ajax_odwpdp_download', 'odwpdp_download_via_ajax' );
