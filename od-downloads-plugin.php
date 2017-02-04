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
defined( 'ODWPDP_SLUG' ) || define( 'ODWPDP_SLUG', 'odwp-downloads_plugin' );
defined( 'ODWPDP_FILE' ) || define( 'ODWPDP_FILE', __FILE__ );
defined( 'ODWPDP_PATH' ) || define( 'ODWPDP_PATH', dirname( ODWPDP_FILE ) );
defined( 'ODWPDP_CPT' ) || define( 'ODWPDP_CPT', 'odwpdp_cpt' );



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
            'supports'              => array( 'title', 'excerpt', 'custom-fields', 'thumbnail' ),
            'taxonomies'            => array( 'category', 'post_tag' ),
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
            'menu_icon'             => plugins_url( 'icon16.png', __FILE__ ),
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
     * @param array $defaults
     * @return array
     */
    function odwpdp_cpt_columns( $defaults ) {
        global $post_type;

        if ( $post_type != ODWPDP_CPT ) {
            return $defaults;
        }

        $defaults['odwpdp_thumbnail_column'] = __( 'Logo', ODWPDP_SLUG );

        return $defaults;
    }
endif;
add_filter( 'manage_posts_columns', 'odwpdp_cpt_columns' );
 


if ( !function_exists( 'odwpdp_cpt_columns_content' ) ) :
    /**
     * Render content for logo cell in "odwpdp-cpt" list in WP admin.
     * @global string $post_type
     */
    function odwpdp_cpt_columns_content( $column_name, $post_id ) {
        if ( $column_name == 'odwpdp_thumbnail_column' ) {
            $post_featured_image = odwpdp_get_thumbnail( $post_id );
            if ( $post_featured_image) {
                printf( '<img src="%s">', $post_featured_image );
            }
        }
    }
endif;
add_action( 'manage_posts_custom_column', 'odwpdp_cpt_columns_content', 10, 2 );



if ( !function_exists( 'odwpdp_admin_scripts' ) ) :
    /**
     * Append our CSS styles and Javascripts for the WP admin.
     */
    function odwpdp_admin_scripts() {
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
        wp_enqueue_style( 'odwpdp-admin-css', plugins_url( '/css/public.css', ODWPDP_FILE ) );
    }
endif;
add_action( 'wp_enqueue_scripts', 'odwpdp_public_scripts' );



if ( ! function_exists( 'odwpdp_activate_plugin' ) ) :
    /**
     * Activate plugin (create our database table).
     * @global wpdb $wpdb
     * @link https://premium.wpmudev.org/blog/creating-database-tables-for-plugins/
     */
    function odwpdp_activate_plugin() {
        // ...
    }
endif;
register_activation_hook( ODWPDP_FILE, 'odwpdp_activate_plugin' );



// Include shortcode "Tabulka společností"
//include_once( ODWPDP_PATH . '/src/shortcode-1.php' );
// Include shortcode "Přehled vlastností společnosti"
//include_once( ODWPDP_PATH . '/src/shortcode-2.php' );
// Include widget "Filtr společností"
//include_once( ODWPDP_PATH . '/src/widget-1.php' );
