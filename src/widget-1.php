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
     * Constructor.
     */
    public function __construct() {
        $opts = array(
            'classname' => 'odwpdp_widget_1',
            'description' => __( 'Widget se soubory ke stažení', ODWPDP_SLUG ),
        );
        parent::__construct( 'odwpdp_widget_1', __( 'Soubory ke stažení', ODWPDP_SLUG ), $opts );
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
                $query_args['orderby'] = 'meta_value';//meta_value_num
                $query_args['meta_key'] = $instance['orderby'];
                $query_args['meta_type'] = 'DATE';//['NUMERIC','BINARY','CHAR','DATE','DATETIME','DECIMAL','SIGNED','TIME','UNSIGNED']
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
        include_once( ODWPDP_PATH . '/templates/widget-1.phtml' );
        $html = ob_get_flush();
        echo apply_filters( 'odwpdp-widget-1', $html );
    }

    /**
     * Outputs the options form on admin
     *
     * @param array $instance The widget options
     * @todo Try again to use external PHTML file with template of the form!
     */
    public function form( $instance ) {
        $title_id     = $this->get_field_id( 'title' );
        $title_name   = $this->get_field_name( 'title' );
        $title_val    = ! empty( $instance['title'] ) ? $instance['title'] : esc_html__( 'Soubory ke stažení', ODWPDP_SLUG );

        $count_id     = $this->get_field_id( 'count' );
        $count_name   = $this->get_field_name( 'count' );
        $count_val    = ! empty( $instance['count'] ) ? $instance['count'] : '5';

        $orderby_id   = $this->get_field_id( 'orderby' );
        $orderby_name = $this->get_field_name( 'orderby' );
        $orderby_val  = ! empty( $instance['orderby'] ) ? $instance['orderby'] : 'title';
        $orderby_val  = in_array( $orderby_val, array_keys( odwpdp_get_avail_orderby_vals() ) ) ? $orderby_val : 'title';

        $order_id     = $this->get_field_id( 'order' );
        $order_name   = $this->get_field_name( 'order' );
        $order_val    = ! empty( $instance['order'] ) ? $instance['order'] : 'ASC';
        $order_val    = in_array( $order_val, array_keys( odwpdp_get_avail_order_vals() ) ) ? $order_val : 'ASC';
?>
<p>
    <label for="<?php echo esc_attr( $title_id ); ?>"><?php esc_attr_e( 'Název:', ODWPDP_SLUG ); ?></label> 
    <input class="widefat" id="<?php echo esc_attr( $title_id ); ?>" name="<?php echo esc_attr( $title_name ); ?>" type="text" value="<?php echo esc_attr( $title_val ); ?>">
</p>
<p>
    <label for="<?php echo esc_attr( $count_id ); ?>" class="short-label"><?php esc_attr_e( 'Počet položek:', ODWPDP_SLUG ); ?></label> 
    <input class="tiny-text" id="<?php echo esc_attr( $count_id ); ?>" name="<?php echo esc_attr( $count_name ); ?>" type="number" value="<?php echo esc_attr( $count_val ); ?>" step="1" min="1" size="3">
</p>
<p>
    <label for="<?php echo esc_attr( $orderby_id ); ?>" class="short-label"><?php esc_attr_e( 'Řadit dle:', ODWPDP_SLUG ); ?></label> 
    <select id="<?php echo esc_attr( $orderby_id ); ?>" name="<?php echo esc_attr( $orderby_name ); ?>"  value="<?php echo esc_attr( $orderby_val ); ?>">
        <?php foreach ( odwpdp_get_avail_orderby_vals() as $val => $name ) : ?>
        <option value="<?php echo $val; ?>" <?php selected( $orderby_val, $val ); ?>><?php echo $name; ?></option>
        <?php endforeach; ?>
    </select>
</p>
<p>
    <label for="<?php echo esc_attr( $order_id ); ?>" class="short-label"><?php esc_attr_e( 'Řadit:', ODWPDP_SLUG ); ?></label> 
    <select id="<?php echo esc_attr( $order_id ); ?>" name="<?php echo esc_attr( $order_name ); ?>"  value="<?php echo esc_attr( $order_val ); ?>">
        <?php foreach ( odwpdp_get_avail_order_vals() as $val => $name ) : ?>
        <option value="<?php echo $val; ?>" <?php selected( $order_val, $val ); ?>><?php echo $name; ?></option>
        <?php endforeach; ?>
    </select>
</p>
<?php
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
        $instance['title']   = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['count']   = ( ! empty( $new_instance['count'] ) ) ? intval( $new_instance['count'] ) : 5;
        $instance['orderby'] = ( ! empty( $new_instance['orderby'] ) ) 
                ? strip_tags( $new_instance['orderby'] ) 
                : 'title';
        $instance['order']   = ( ! empty( $new_instance['order'] ) ) 
                ? strip_tags( $new_instance['order'] ) 
                : 'ASC';

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
