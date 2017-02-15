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
//include_once( ODWPDP_PATH . '/src/custom_post_type-1.php' );


if ( ! function_exists( 'odwpdp_custom_post_type' ) ) :
    /**
     * Register our custom post type.
     */
    function odwpdp_custom_post_type() {
        $labels = array(
            'name'                  => _x( 'Soubory ke stažení', 'Post Type General Name', ODWPDP_SLUG ),
            'singular_name'         => _x( 'Soubor ke stažení', 'Post Type Singular Name', ODWPDP_SLUG ),
            'menu_name'             => __( 'Soubory', ODWPDP_SLUG ),
            'name_admin_bar'        => __( 'Vytvořit soubor ke stažení', ODWPDP_SLUG ),
            'archives'              => __( 'Archív souborů ke stažení', ODWPDP_SLUG ),
            'attributes'            => __( 'Atributy souboru ke stažení', ODWPDP_SLUG ),
            'parent_item_colon'     => __( '', ODWPDP_SLUG ),
            'all_items'             => __( 'Všechny soubory', ODWPDP_SLUG ),
            'add_new_item'          => __( 'Přidej nový soubor ke stažení', ODWPDP_SLUG ),
            'add_new'               => __( 'Přidej nový', ODWPDP_SLUG ),
            'new_item'              => __( 'Nový soubor ke stažení', ODWPDP_SLUG ),
            'edit_item'             => __( 'Uprav soubor ke stažení', ODWPDP_SLUG ),
            'update_item'           => __( 'Aktualizuj soubor ke stažení', ODWPDP_SLUG ),
            'view_item'             => __( 'Zobraz soubor ke stažení', ODWPDP_SLUG ),
            'view_items'            => __( 'Zobraz soubory ke stažení', ODWPDP_SLUG ),
            //'search_items'          => __( 'Search Item', ODWPDP_SLUG ),
            //'not_found'             => __( 'Not found', ODWPDP_SLUG ),
            //'not_found_in_trash'    => __( 'Not found in Trash', ODWPDP_SLUG ),
            'featured_image'        => __( 'Ikona souboru', ODWPDP_SLUG ),
            'set_featured_image'    => __( 'Nastavit ikonu souboru ke stažení', ODWPDP_SLUG ),
            'remove_featured_image' => __( 'Odebrat ikonu', ODWPDP_SLUG ),
            'use_featured_image'    => __( 'Použij jako ikonu souboru', ODWPDP_SLUG ),
            //'insert_into_item'      => __( 'Insert into item', ODWPDP_SLUG ),
            //'uploaded_to_this_item' => __( 'Uploaded to this item', ODWPDP_SLUG ),
            //'items_list'            => __( 'Items list', ODWPDP_SLUG ),
            //'items_list_navigation' => __( 'Items list navigation', ODWPDP_SLUG ),
            //'filter_items_list'     => __( 'Filter items list', ODWPDP_SLUG ),
        );
        $args = array(
            'label'                 => __( 'Soubor ke stažení', ODWPDP_SLUG ),
            'description'           => __( 'Soubory, které chcete poskytnout uživatelům ke stažení.', ODWPDP_SLUG ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'excerpt', /*'custom-fields', 'thumbnail'*/ ),
            'taxonomies'            => array( /*'category', 'post_tag'*/ ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 5,
            'show_in_admin_bar'     => true,
            'show_in_nav_menus'     => true,
            'can_export'            => true,
            'has_archive'           => true,        
            'exclude_from_search'   => false,
            'publicly_queryable'    => true,
            'capability_type'       => 'page',
            'menu_icon'             => plugins_url( 'icon16.png', ODWPDP_FILE ),
        );
        register_post_type( ODWPDP_CPT, $args );
    }
endif;
add_action( 'init', 'odwpdp_custom_post_type', 0 );



/**
 * @var array $odwpdp_metaboxes
 */
$odwpdp_metaboxes = array(
    0 => __( 'Datum vyvěšení', ODWPDP_SLUG ),
    1 => __( 'Datum sejmutí', ODWPDP_SLUG ),
    2 => __( 'Nahrát soubor', ODWPDP_SLUG ),
);



if ( !function_exists( 'odwpdp_get_thumbnail' ) ) :
    /**
     * Add new column in "odwpdp-cpt" list in WP admin.
     */
    function odwpdp_get_thumbnail( $post_id ) {
        $post_thumbnail_id = get_post_thumbnail_id( $post_id );

        if ( $post_thumbnail_id ) {
            $post_thumbnail_img = wp_get_attachment_image_src( $post_thumbnail_id );

            return $post_thumbnail_img[0];
        }
    }
endif;



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



if ( !function_exists( 'odwpdp_cpt_columns' ) ) :
    /**
     * Add new columns in "odwpdp-cpt" list in WP admin.
     * @global string $post_type
     * @param array $columns
     * @return array
     */
    function odwpdp_cpt_columns( $columns ) {
        global $post_type;

        if ( $post_type != ODWPDP_CPT ) {
            return $columns;
        }

        $columns['odwpdp_file_column'] = __( 'Soubor', ODWPDP_SLUG );
        $columns['odwpdp_puton_column'] = __( 'Datum vyvěšení', ODWPDP_SLUG );
        $columns['odwpdp_putoff_column'] = __( 'Datum sejmutí', ODWPDP_SLUG );

        return $columns;
    }
endif;
add_filter( 'manage_edit-odwpdp_cpt_columns', 'odwpdp_cpt_columns' );



if ( !function_exists( 'odwpdp_cpt_sortable_columns' ) ) :
    /**
     * Make some columns sortable.
     * @global string $post_type
     * @param array $columns
     * @return array
     */
    function odwpdp_cpt_sortable_columns( $columns ) {
        global $post_type;

        if ( $post_type != ODWPDP_CPT ) {
            return $columns;
        }

        $columns['odwpdp_file_column'] = __( 'Soubor', ODWPDP_SLUG );
        //$columns['odwpdp_puton_column'] = __( 'Datum vyvěšení', ODWPDP_SLUG );
        //$columns['odwpdp_putoff_column'] = __( 'Datum sejmutí', ODWPDP_SLUG );

        return $columns;
    }
endif;
add_filter( 'manage_edit-odwpdp_cpt_sortable_columns', 'odwpdp_cpt_sortable_columns' );



if ( !function_exists( 'odwpdp_pre_get_posts' ) ) :
    /**
     * @internal
     * @param WP_Query $query
     */
    function odwpdp_pre_get_posts( $query ) {
        $orderby = $query->get( 'orderby' );

        if ( ! $query->is_main_query() || empty( $orderby ) ) {
            return;
        }

        switch( $orderby ) {
            case 'odwpdp_file_column':
                $query->set( 'meta_key', 'odwpdp-metabox-3' );
                $query->set( 'orderby', 'meta_value' );
                break;

            case 'odwpdp_puton_column':
                $query->set( 'meta_key', 'odwpdp-metabox-1' );
                $query->set( 'orderby', 'meta_value' );
                break;

            case 'odwpdp_putoff_column':
                $query->set( 'meta_key', 'odwpdp-metabox-2' );
                $query->set( 'orderby', 'meta_value' );
                break;
      }
    }
endif;
add_action( 'pre_get_posts', 'odwpdp_pre_get_posts', 1 );
 


if ( !function_exists( 'odwpdp_cpt_columns_content' ) ) :
    /**
     * Render content for logo cell in "odwpdp-cpt" list in WP admin.
     * @global string $post_type
     */
    function odwpdp_cpt_columns_content( $column_name, $post_id ) {
        if ( $column_name == 'odwpdp_file_column' ) {
            $file = get_post_meta( $post_id, 'odwpdp-metabox-3', true );
            $info = odwpdp_get_file_info( $file );
            printf( '<span><img src="%s" class="odwpdp-file-icon"><code>%s</code></span>', $info['icon_16'], $file );
        }
        else if ( $column_name == 'odwpdp_puton_column' ) {
            $val = get_post_meta( $post_id, 'odwpdp-metabox-1', true );
            printf( '<span>%s</span>', $val );
        }
        else if ( $column_name == 'odwpdp_putoff_column' ) {
            $val = get_post_meta( $post_id, 'odwpdp-metabox-2', true );
            printf( '<span>%s</span>', $val );
        }
    }
endif;
add_action( 'manage_posts_custom_column', 'odwpdp_cpt_columns_content', 10, 2 );



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
        wp_enqueue_script( 'odwpdp-admin-js', plugins_url( '/js/admin.js', ODWPDP_FILE ) );
    }
endif;
add_action( 'admin_enqueue_scripts', 'odwpdp_admin_scripts' );



if ( !function_exists( 'odwpdp_public_scripts' ) ) :
    /**
     * Append our CSS styles and JavaScripts for the front-end.
     */
    function odwpdp_public_scripts() {
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



if ( ! function_exists( 'odwpdp_get_file_url' ) ) :
    /**
     * @internal
     * @param string $file File name.
     * @return string URL of download link.
     */
    function odwpdp_get_file_url( $file ) {
        if ( empty( $file ) ) {
            return '#';
        }

        return content_url( ODWPDP_UPLOADS . "/{$file}" );
    }
endif;



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
     *   <li><code>file</code>; <i>string</i>, file name</li>
     *   <li><code>icon_16</code>; <i>string</i>, 16x16 icon by file type</li>
     *   <li><code>icon_32</code>; <i>string</i>, 32x32 icon by file type</li>
     *   <li><code>path</code>; <i>string</i>, full file path</li>
     *   <li><code>size</code>; <i>integer</i>, size of file</li>
     *   <li><code>url</code>; <i>string</i>,  URL for download link</li>
     * </ul>
     *
     * @param string $file
     * @return array Array with file info.
     * @uses odwpdp_get_file_url()
     * @uses odwpdp_get_file_icon()
     */
    function odwpdp_get_file_info( $file ) {
        $info = array(
            'exist'     => false,
            'file'      => $file,
            'icon_16'   => null,
            'icon_32'   => null,
            'path'      => null,
            'size'      => null,
            'url'       => odwpdp_get_file_url( $file ),
        );
        $upload_dir      = wp_upload_dir();
        $info['path']    = sprintf( '%s/%s/%s', $upload_dir['basedir'], ODWPDP_SLUG, $file );
        $info['exist']   = file_exists( $info['path'] );
        $info['size']    = size_format( filesize( $info['path'] ), 2);
        $file_ext_arr    = strpos( $file, '.' ) !== false ? explode( '.', $file ) : array();
        $file_ext        = count( $file_ext_arr ) == 0 ? '' : $file_ext_arr[count( $file_ext_arr ) - 1];
        $info['icon_16'] = odwpdp_get_file_icon( $file_ext );
        $info['icon_32'] = odwpdp_get_file_icon( $file_ext, ODWPDP_ICON_32 );

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