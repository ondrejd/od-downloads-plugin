<?php
/**
 * Controller file for the first metabox "Nahrat soubor".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-compare_filter_search
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}


if ( ! function_exists( 'odwpdp_show_metabox_3' ) ) :
    /**
     * Render metabox "Nahrat soubor".
     * @global WP_Post $post
     */
    function odwpdp_show_metabox_3() {
        global $post;

        $value    = get_post_meta( $post->ID, 'odwpdp-metabox-3', true );
        $temp_str = ( ! empty( $value) ) ? date( 'Y-m-d', strtotime( $value ) ) : date( 'Y-m-d' );
        $temp_arr = explode( '-', $temp_str );
        $val      = array( 'd' => $temp_arr[2], 'm' => $temp_arr[1], 'y' => $temp_arr[0] );

        ob_start( function() {} );
        include_once ODWPDP_PATH . '/templates/metabox-3.phtml';
        $html = ob_get_flush();
        echo apply_filters( 'odwpdp-metabox-3', $html );
    }
endif;



if ( ! function_exists( 'odwpdp_save_metabox_3' ) ) :
    /**
     * Save metabox "Nahrat soubor".
     * @global WP_Post $post
     * @param integer $post_id
     * @return integer
     */
    function odwpdp_save_metabox_3( $post_id ) {
        global $post;

        $nonce = filter_input( INPUT_POST, 'odwpdp-metabox-3-nonce' );
        if ( ! wp_verify_nonce( $nonce, 'odwpdp-metabox3-nonce' ) ) {
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

        $value = filter_input( INPUT_POST, 'odwpdp-metabox-3-input' );

        update_post_meta( $post_id, 'odwpdp-metabox-3', $value );

        return $post_id;
    }
endif;
