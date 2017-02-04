<?php
/**
 * Controller file for the second metabox "Datum sejmuti".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( ! function_exists( 'odwpdp_show_metabox_2' ) ) :
    /**
     * Render metabox "Datum sejmuti".
     * @global WP_Post $post
     */
    function odwpdp_show_metabox_2() {
        global $post;

        $value    = get_post_meta( $post->ID, 'odwpdp-metabox-2', true );
        $temp_str = ( ! empty( $value) ) ? date( 'Y-m-d', strtotime( $value ) ) : date( 'Y-m-d' );
        $temp_arr = explode( '-', $temp_str );
        $val      = array( 'd' => $temp_arr[2], 'm' => $temp_arr[1], 'y' => $temp_arr[0] );

        ob_start( function() {} );
        include_once ODWPDP_PATH . '/templates/metabox-2.phtml';
        $html = ob_get_flush();
        echo apply_filters( 'odwpdp-metabox-2', $html );
    }
endif;


if ( ! function_exists( 'odwpdp_save_metabox_2' ) ) :
    /**
     * Save metabox "Datum sejmuti".
     * @global WP_Post $post
     * @param integer $post_id
     * @return integer
     */
    function odwpdp_save_metabox_2( $post_id ) {
        global $post;

        $nonce = filter_input( INPUT_POST, 'odwpdp-metabox-2-nonce' );
        if ( ! wp_verify_nonce( $nonce, 'odwpdp-metabox2-nonce' ) ) {
            return $post_id;
        }

        if( ! current_user_can( 'edit_post', $post_id ) ) {
            return $post_id;
        }

        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        if( ODWPDP_CPT != $post->post_type) {
            return $post_id;
        }

        $date_d = filter_input( INPUT_POST, 'odwpdp-metabox-2-dd' );
        $date_m = filter_input( INPUT_POST, 'odwpdp-metabox-2-mm' );
        $date_y = filter_input( INPUT_POST, 'odwpdp-metabox-2-yy' );

        if ( is_null( $date_d ) ) {
            $date_d = date( 'd' );
        }

        if ( is_null( $date_m ) ) {
            $date_m = date( 'm' );
        }

        if ( is_null( $date_y ) ) {
            $date_y = date( 'y' );
        }

        $date_d = sprintf( '%02d', intval( $date_d ) );
        $date_m = sprintf( '%02d', intval( $date_m ) );

        $value  = "$date_y-$date_m-$date_d";

        update_post_meta( $post_id, 'odwpdp-metabox-2', $value );

        return $post_id;
    }
endif;
