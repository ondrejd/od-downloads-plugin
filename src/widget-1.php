<?php
/**
 * Widget "Soubory ke stažení".
 *
 * @author  Ondřej Doněk, <ondrejd@gmail.com>
 * @license https://www.gnu.org/licenses/gpl-3.0.en.html GNU General Public License 3.0
 * @package odwp-downloads_plugin
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * Widget "Soubory ke stažení".
 */
class odwpdp_widget_1 extends WP_Widget {
    /**
     * @var array $fields
     */
    protected $fields;

    /**
     * Constructor.
     */
    public function __construct() {
        $base_id = 'odwpdp_widget_1';
        $title = __( 'Soubory ke stažení', ODWPDP_SLUG );
        // Widget options. See wp_register_sidebar_widget() for information
        // on accepted arguments.
        $widget_opts = array(
            'classname' => 'odwpdp_widget_1',
            'description' => __( 'Widget se soubory ke stažení', ODWPDP_SLUG ),
        );
        // Widget control options. See wp_register_widget_control() for
        // information on accepted arguments.
        $control_opts = array();

        // Initialize WP_Widget
        parent::__construct( $base_id, $title, $widget_opts, $control_opts );
    }

    /**
     * Renders the widget.
     * @param array $args
     * @param array $instance
     * @todo Implement "order_by"!
     */
    public function widget( $args, $instance ) {
        // WP Query arguments
        $query_args = array(
            'numberposts' => intval( $instance['count'] ),
            'post_type' => ODWPDP_CPT,
        );

        if ( array_key_exists( 'orderby', $instance ) ) {
            if ( ! in_array( $instance['orderby'], array_keys( odwpdp_get_avail_orderby_vals() ) ) ) {
                $instance['orderby'] = 'title';
            }

            if ( $instance['orderby'] == 'title' ) {
                $query_args['orderby'] = 'title';
            } else {
                $key  = odwpdp_get_orderby_meta_key( $instance['orderby'] );
                $type = ( $key == 'downloads_count' ) ? 'NUMERIC' : 'DATE';

                $query_args['orderby'] = 'meta_value';
                $query_args['meta_query']   = array();
                $query_args['meta_query'][] = array(
                    // Custom field key.
                    'key'  => $key,
                    // Custom field type. Possible values are 'NUMERIC',
                    // 'BINARY', 'CHAR', 'DATE', 'DATETIME', 'DECIMAL',
                    // 'SIGNED', 'TIME', 'UNSIGNED'. Default value is 'CHAR'.
                    'type' => $type,
                    // (string) - Operator to test. Possible values are
                    // '=', '!=', '>', '>=', '<', '<=', 'LIKE', 'NOT LIKE', 'IN',
                    // 'NOT IN', 'BETWEEN', 'NOT BETWEEN'. Default value is '='.
                    'compare' => '=',
                );
            }

            if ( ! array_key_exists( 'order', $instance ) ) {
                $query_args['order'] = 'DESC';
            }
            else {
                if ( in_array( $instance['order'], array_keys( odwpdp_get_avail_order_vals() ) ) ) {
                    $query_args['order'] = $instance['order'];
                } else {
                    $query_args['order'] = 'DESC';
                }
            }
        }

        // Get items to show
        $files = get_posts( $query_args );

        // Render template
        ob_start( function() {} );
        include( ODWPDP_PATH . '/templates/widget-1.phtml' );
        echo ob_get_flush();
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     * @todo Try again to use external PHTML file with template of the form!
     */
    public function form( $instance ) {
        $orderby_vals = odwpdp_get_avail_orderby_vals();
        $order_vals   = odwpdp_get_avail_order_vals();

        $title_id     = $this->get_field_id( 'title' );
        $title_name   = $this->get_field_name( 'title' );
        $title_val    = ! empty( $instance['title'] ) ? $instance['title'] : '';

        $desc_id      = $this->get_field_id( 'description' );
        $desc_name    = $this->get_field_name( 'description' );
        $desc_val     = ! empty( $instance['description'] ) ? $instance['description'] : '';

        $count_id     = $this->get_field_id( 'count' );
        $count_name   = $this->get_field_name( 'count' );
        $count_val    = ! empty( $instance['count'] ) ? $instance['count'] : 5;

        $orderby_id   = $this->get_field_id( 'orderby' );
        $orderby_name = $this->get_field_name( 'orderby' );
        $orderby_val  = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'title';
        $orderby_val  = in_array( $orderby_val, array_keys( $orderby_vals ) ) ? $orderby_val : 'title';

        $order_id     = $this->get_field_id( 'order' );
        $order_name   = $this->get_field_name( 'order' );
        $order_val    = ! empty( $instance['order'] ) ? $instance['order'] : 'ASC';
        $order_val    = in_array( $order_val, array_keys( $order_vals ) ) ? $order_val : 'ASC';

        $show_icon_id    = $this->get_field_id( 'show_icon' );
        $show_icon_name  = $this->get_field_name( 'show_icon' );
        $show_icon_val   = array_key_exists( 'show_count', $instance ) ? (bool) $instance['show_icon'] : true;

        $show_size_id    = $this->get_field_id( 'show_size' );
        $show_size_name  = $this->get_field_name( 'show_size' );
        $show_size_val   = array_key_exists( 'show_count', $instance ) ? (bool) $instance['show_size'] : true;

        $show_dates_id   = $this->get_field_id( 'show_dates' );
        $show_dates_name = $this->get_field_name( 'show_dates' );
        $show_dates_val  = array_key_exists( 'show_dates', $instance ) ? (bool) $instance['show_dates'] : true;

        $show_count_id   = $this->get_field_id( 'show_count' );
        $show_count_name = $this->get_field_name( 'show_count' );
        $show_count_val  = array_key_exists( 'show_count', $instance ) ? (bool) $instance['show_count'] : false;

        // Render template
        ob_start( function() {} );
        include( ODWPDP_PATH . '/templates/widget-form-1.phtml' );
        echo ob_get_flush();
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title']       = ! empty( $new_instance['title'] ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['description'] = ! empty( $new_instance['description'] ) ? strip_tags( $new_instance['description'] ) : '';
        $instance['count']       = ! empty( $new_instance['count'] ) ? intval( $new_instance['count'] ) : 5;
        $instance['orderby']     = ! empty( $new_instance['orderby'] ) ? strip_tags( $new_instance['orderby'] ) : 'title';
        $instance['order']       = ! empty( $new_instance['order'] ) ? strip_tags( $new_instance['order'] ) : 'ASC';
        $instance['show_icon']   = (bool) $new_instance['show_icon'];
        $instance['show_size']   = (bool) $new_instance['show_size'];
        $instance['show_dates']  = (bool) $new_instance['show_dates'];
        $instance['show_count']  = (bool) $new_instance['show_count'];

        if ( ! in_array( $instance['orderby'], array_keys( odwpdp_get_avail_orderby_vals() ) ) ) {
            $instance['orderby'] = 'title';
        }

        if ( ! in_array( $instance['order'], array_keys( odwpdp_get_avail_order_vals() ) ) ) {
            $instance['order'] = 'ASC';
        }

        return $instance;
    }
}

// Register our widget
add_action( 'widgets_init', function() { register_widget( 'odwpdp_widget_1' ); } );
