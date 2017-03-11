<?php
/**
 * Script through which are files downloaded.
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/od-downloads-plugin for the canonical source repository
 * @package odwp-downloads_plugin
 */

// Initialize WP
//defined( 'SHORTINIT' ) || define( 'SHORTINIT', true );
defined( 'WP_USE_THEMES' ) || define( 'WP_USE_THEMES', false );
require( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/wp-load.php' );

// Get cpt ID
$cpt_id = filter_input( INPUT_GET, 'post_id', FILTER_VALIDATE_INT );
if ( empty( $cpt_id ) || $cpt_id === false ) {
    header( 'HTTP/1.0 404 Not Found' );
    echo __( 'Musíte specifikovat soubor ke stažení!' ) . PHP_EOL;
    //wp_die();
    exit();
}

// Include our plugin
require( "od-downloads-plugin.php" );

// Get file info
$info = odwpdp_get_file_info( $cpt_id );

// File doesn't exist
if ( $info['exist'] === false || empty( $info['path'] ) || ! is_readable( $info['path'] ) ) {
    header( 'HTTP/1.0 404 Not Found' );
    echo __( 'Omlouváme se, ale soubor ke stažení nebyl nalezen!' ) . PHP_EOL;
    //wp_die();
    exit();
}

// Updates download counter
odwpdp_increase_downloads_count( $cpt_id );

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

header( 'Content-Disposition: attachment; filename="' . $info['file'] . '"' );
header( 'Expires: 0' );
header( 'Cache-Control: must-revalidate' );
header( 'Pragma: public' );
header( 'Content-Length: ' . filesize( $info['path'] ) );
readfile( $info['path'] );
//wp_die();