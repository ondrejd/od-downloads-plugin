<?php
/**
 * Dasboard widget "Soubory ke stažení".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @link https://github.com/ondrejd/od-downloads-plugin for the canonical source repository
 * @package odwp-downloads_plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}



if ( ! class_exists( 'Odwpdp_Dashboard_Widget_1' ) ) :

/**
 * Dashboard widget "Soubory ke stažení.
 * @since 1.0.0
 */
class Odwpdp_Dashboard_Widget_1 {
    /**
     * @var Slug of the widget.
     */
    const SLUG = 'odwpdp-dashboard_widget_1';
    const OPTIONS = [
        'number' => 5,
    ];

    /**
     * Inits plugin.
     * @return void
     */
    public static function init() {
        self::update_options( self::OPTIONS, true );

        wp_add_dashboard_widget(
                self::SLUG,
                __( 'Soubory ke stažení', ODWPDP_SLUG ),
                array( 'Odwpdp_Dashboard_Widget_1', 'render' ),
                array( 'Odwpdp_Dashboard_Widget_1', 'render_config' )
        );
    }

    /**
     * @render void
     */
    public static function render() {
        $number = self::get_option( 'number', true );

        ob_start( function() {} );
        include_once( ODWPDP_PATH . '/templates/dashboard-widget-1.phtml' );
        $html = ob_get_flush();
        echo apply_filters( self::SLUG, $html );
    }

    /**
     * @render void
     */
    public static function render_config() {
        $number = self::get_option( 'number', true );

        if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
            $number = (int) filter_input( INPUT_POST, 'number' );

            // If not set, set default value
            if ( empty( $number ) ) {
                $number = self::OPTIONS['number'];
            }

            self::update_options( array( 'number' => $number ) );
        }

        ob_start( function() {} );
        include_once( ODWPDP_PATH . '/templates/dashboard-widget-config-1.phtml' );
        $html = ob_get_flush();
        echo apply_filters( self::SLUG, $html );
    }

    /**
     * @return mixed
     */
    public static function get_options() {
        $opts = get_option( 'dashboard_widget_options' );

        if ( array_key_exists( self::SLUG, $opts ) ) {
            return $opts[self::SLUG];
        }

        return false;
    }

    /**
     * @param string $key
     * @param boolean $default (Optional.)
     * @return mixed
     */
    public static function get_option( $key, $default = false ) {
        $opts = self::get_options();
        $ret = ( array_key_exists( $key, self::OPTIONS ) && $default === true )
                ? self::OPTIONS[$key]
                : false;

        if ( $opts === false || ! array_key_exists( $key, $opts ) ) {
            return $ret;
        }

        return $opts[$key];
    }

    /**
     * @param array $options
     * @param boolean $add_only (Optional.)
     * @return boolean
     */
    public static function update_options( $options, $add_only = false ) {
        $opts = get_option( 'dashboard_widget_options' );
        $w_opts = array_key_exists( self::SLUG, $opts ) ? $opts[self::SLUG] : array();

        if ( $add_only ) {
            $opts[self::SLUG] = array_merge( $options, $w_opts );
        } else {
            $opts[self::SLUG] = array_merge( $w_opts, $options );
        }

        return update_option( 'dashboard_widget_options', $opts );
    }
}

endif;

add_action( 'wp_dashboard_setup', array( 'Odwpdp_Dashboard_Widget_1', 'init' ) );
