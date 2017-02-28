<?php
/**
 * Functions related to our custom post type "Soubory ke stažení".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-downloads_plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}



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
            'all_items'             => __( 'Všechny soubory', ODWPDP_SLUG ),
            'add_new_item'          => __( 'Přidej nový soubor ke stažení', ODWPDP_SLUG ),
            'add_new'               => __( 'Přidej nový', ODWPDP_SLUG ),
            'new_item'              => __( 'Nový soubor ke stažení', ODWPDP_SLUG ),
            'edit_item'             => __( 'Uprav soubor ke stažení', ODWPDP_SLUG ),
            'update_item'           => __( 'Aktualizuj soubor ke stažení', ODWPDP_SLUG ),
            'view_item'             => __( 'Zobraz soubor ke stažení', ODWPDP_SLUG ),
            'view_items'            => __( 'Zobraz soubory ke stažení', ODWPDP_SLUG ),
            'featured_image'        => __( 'Ikona souboru', ODWPDP_SLUG ),
            'set_featured_image'    => __( 'Nastavit ikonu souboru ke stažení', ODWPDP_SLUG ),
            'remove_featured_image' => __( 'Odebrat ikonu', ODWPDP_SLUG ),
            'use_featured_image'    => __( 'Použij jako ikonu souboru', ODWPDP_SLUG ),
        );
        $args = array(
            'label'                 => __( 'Soubor ke stažení', ODWPDP_SLUG ),
            'description'           => __( 'Soubory, které chcete poskytnout uživatelům ke stažení.', ODWPDP_SLUG ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'excerpt' ),
            'taxonomies'            => array(),
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

        $columns['odwpdp_file_column']   = __( 'Soubor', ODWPDP_SLUG );
        $columns['odwpdp_puton_column']  = __( 'Datum vyvěšení', ODWPDP_SLUG );
        $columns['odwpdp_putoff_column'] = __( 'Datum sejmutí', ODWPDP_SLUG );
        $columns['odwpdp_count_column']  = __( 'Počet stažení', ODWPDP_SLUG );

        return $columns;
    }
endif;



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

        $columns['odwpdp_file_column']   = 'odwpdp_file';
        $columns['odwpdp_puton_column']  = 'odwpdp_puton';
        $columns['odwpdp_putoff_column'] = 'odwpdp_putff';
        $columns['odwpdp_count_column']  = 'odwpdp_count';

        return $columns;
    }
endif;
 


if ( !function_exists( 'odwpdp_cpt_columns_content' ) ) :
    /**
     * Render content for logo cell in "odwpdp-cpt" list in WP admin.
     * @global string $post_type
     */
    function odwpdp_cpt_columns_content( $column_name, $post_id ) {
        if ( $column_name == 'odwpdp_file_column' ) {
            $file = odwpdp_get_file_info( $post_id );
            printf( '<span><img src="%s" class="odwpdp-file-icon"><code>%s</code></span>', $file['icon_16'], $file['file'] );
        }
        else if ( $column_name == 'odwpdp_puton_column' ) {
            $val = get_post_meta( $post_id, 'odwpdp-metabox-1', true );
            printf( '<span>%s</span>', $val );
        }
        else if ( $column_name == 'odwpdp_putoff_column' ) {
            $val = get_post_meta( $post_id, 'odwpdp-metabox-2', true );
            printf( '<span>%s</span>', $val );
        }
        else if ( $column_name == 'odwpdp_count_column' ) {
            $val = get_post_meta( $post_id, 'odwpdp-downloads-count', true );
            printf( '<span>%d</span>', $val );
        }
    }
endif;



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
                //['NUMERIC','BINARY','CHAR','DATE','DATETIME','DECIMAL','SIGNED','TIME','UNSIGNED']
                $query->set( 'meta_type', 'DATE' );
                break;

            case 'odwpdp_putoff_column':
                $query->set( 'meta_key', 'odwpdp-metabox-2' );
                $query->set( 'orderby', 'meta_value' );
                $query->set( 'meta_type', 'DATE' );
                break;

            case 'odwpdp_count_column':
                $query->set( 'meta_key', 'odwpdp-downloads-count' );
                $query->set( 'orderby', 'meta_value_num' );
                break;
      }
    }
endif;



if ( ! function_exists( 'odwpdp_cpt_set_counter_meta' ) ) :
    /**
     * Add meta value with download counter to our custom post type.
     * @param integer $post_id
     * @param WP_Post $post
     * @param boolean $update
     * @return integer
     */
    function odwpdp_cpt_set_counter_meta( $post_id, $post, $update ) {
        if( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        if( ODWPDP_CPT != $post->post_type) {
            return $post_id;
        }

        update_post_meta( $post_id, 'odwpdp-downloads-count', 0 );

        return $post_id;
    }
endif;



if ( ! function_exists( 'odwpdp_get_downloads_count' ) ):
    /**
     * @param WP_Post|integer $post Post object or post's ID.
     * @return integer Returns count of downloads for given cpt.
     */
    function odwpdp_get_downloads_count( $post ) {
        $post_id = ( $post instanceof WP_Post ) ? $post->ID : intval( $post );

        return intval( get_post_meta( $post_id, 'odwpdp-downloads-count', true ) );
    }
endif;



if ( ! function_exists( 'odwpdp_increase_downloads_count' ) ):
    /**
     * Increases downloads count for specified cpt.
     * @param WP_Post|integer $post Post object or post's ID.
     * @return integer Returns count of downloads for given cpt.
     */
    function odwpdp_increase_downloads_count( $post ) {
        $post_id = ( $post instanceof WP_Post ) ? $post->ID : intval( $post );
        $count   = odwpdp_get_downloads_count( $post_id ) + 1;

        update_post_meta( $post_id, 'odwpdp-downloads-count', $count );

        return $count;
    }
endif;



if ( is_admin() === true ) {
    // All related to custom post type listing (columns, sorting etc.)
    add_filter( 'manage_edit-odwpdp_cpt_columns', 'odwpdp_cpt_columns' );
    add_filter( 'manage_edit-odwpdp_cpt_sortable_columns', 'odwpdp_cpt_sortable_columns' );
    add_action( 'manage_posts_custom_column', 'odwpdp_cpt_columns_content', 10, 2 );
    add_action( 'pre_get_posts', 'odwpdp_pre_get_posts', 1 );
    // Set meta value with download counter
    add_action( 'save_post', 'odwpdp_cpt_set_counter_meta', 10, 3 );
}
