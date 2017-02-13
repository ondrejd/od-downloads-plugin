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
        // Get items to show
        $files = get_posts( array(
            'numberposts' => intval( $instance['count'] ),
            'post_type'   => ODWPDP_CPT,
        ) );

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

        $orderby_vals = array(
            'title'       => __( 'Názvu', ODWPDP_SLUG ),
            'puton_date'  => __( 'Data vystavení', ODWPDP_SLUG ),
            'putoff_date' => __( 'Data sejmutí', ODWPDP_SLUG ),
        );
?>
<p>
    <label for="<?php echo esc_attr( $title_id ); ?>"><?php esc_attr_e( 'Název:', ODWPDP_SLUG ); ?></label> 
    <input class="widefat" id="<?php echo esc_attr( $title_id ); ?>" name="<?php echo esc_attr( $title_name ); ?>" type="text" value="<?php echo esc_attr( $title_val ); ?>">
</p>
<p>
    <label for="<?php echo esc_attr( $count_id ); ?>"><?php esc_attr_e( 'Počet položek:', ODWPDP_SLUG ); ?></label> 
    <input class="tiny-text" id="<?php echo esc_attr( $count_id ); ?>" name="<?php echo esc_attr( $count_name ); ?>" type="number" value="<?php echo esc_attr( $count_val ); ?>" step="1" min="1" size="3">
</p>
<p>
    <label for="<?php echo esc_attr( $orderby_id ); ?>"><?php esc_attr_e( 'Řadit dle:', ODWPDP_SLUG ); ?></label> 
    <select id="<?php echo esc_attr( $orderby_id ); ?>" name="<?php echo esc_attr( $orderby_name ); ?>"  value="<?php echo esc_attr( $orderby_val ); ?>">
        <?php foreach ( $this->get_avail_orderby_vals() as $val => $name ) : ?>
        <option value="<?php echo $val; ?>" <?php selected( $orderby_val, $val ); ?>><?php echo $name; ?></option>
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

        if ( ! in_array( $instance['orderby'], array_keys( $this->get_avail_orderby_vals() ) ) ) {
            $instance['orderby'] = 'title';
        }

        return $instance;
    }

    /**
     * @return array Available values for "order by" field.
     */
    private function get_avail_orderby_vals() {
        return array(
            'title'       => __( 'Názvu', ODWPDP_SLUG ),
            'puton_date'  => __( 'Data vyvěšení', ODWPDP_SLUG ),
            'putoff_date' => __( 'Data sejmutí', ODWPDP_SLUG ),
        );
    }
}

// Register our widget
add_action( 'widgets_init', function() { register_widget( 'odwpdp_widget_1' ); } );
