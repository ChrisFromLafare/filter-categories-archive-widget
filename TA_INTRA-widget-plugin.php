<?php
/**
 * Plugin Name: TA Widget Archive  Plugin
 * Description: This plugin adds a widget allowing filtered archive lists.
 * Version: 1.0
 * Author: C. ARNAUD (christian.arnaud@technicatome.com)
 * License: GPL2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
*/

// The widget class

/**
 * Class TAINTRANET_FilterArchive_Widget
 *
 */
class TAINTRANET_FilterArchive_Widget extends WP_Widget {
	// Main constructor
	public function __construct() {
		parent::__construct(
			'TAINTRANET_FilterArchive_Widget',
			__( 'Filtered Archive lists widget', 'TAINTRANET_domain' ),
			array(
				'customize_selective_refresh' => true,
			)
		);
	}

	// The widget form (for the backend )
	public function form( $instance ) {

		// Set widget defaults
		$defaults = array(
			'title'    => '',
            'selected_categories' => '',
            'hierarchical' => 0,
            'count' => 0
		);
		
		// Parse current settings with defaults
        /**
         * @var string $title
         * @var array $selected_categories
         * @var bool $hierarchical
         * @var int $count
         */
		extract( wp_parse_args( ( array ) $instance, $defaults ) );
		$categories = get_categories();
		?>

		<?php // Widget Title ?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Widget Title', 'TAINTRANET_domain' ); ?></label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
        <?php // Widget hierarchy ?>
        <p>
            <input id="<?php echo esc_attr($this->get_field_id( 'hierarchical' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'hierarchical' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $hierarchical ); ?> />
            <label for="<?php echo esc_attr($this->get_field_id( 'hierarchical' ) ); ?>"><?php _e('Show hierarchy', 'TAINTRANET_domain'); ?></label>
        </p>
        <?php // Widget count ?>
        <p>
            <input id="<?php echo esc_attr($this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="checkbox" value="1" <?php checked( '1', $count ); ?> />
            <label for="<?php echo esc_attr($this->get_field_id( 'count' ) ); ?>"><?php _e('Show count', 'TAINTRANET_domain'); ?></label>
        </p>
        <p>
            <label <?php _e('Select categories to display', 'TAINTRANET_domain'); ?>></label>
        </p>
        <?php // a checkbox for each category
        foreach ($categories as $category) :
            $check_val = isset($selected_categories[$category->slug])?$selected_categories[$category->slug]:'0';?>
            <p>
                <input id="<?php echo esc_attr($this->get_field_id( $category->slug ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $category->slug ) ); ?>" type="checkbox" value="1" <?php checked( '1', $check_val ); ?> />
                <label for="<?php echo esc_attr($this->get_field_id( $category->slug ) ); ?>"><?php echo $category->name; ?></label>
            </p>
        <?php endforeach; ?>
	<?php }

	// Update widget settings
	public function update( $new_instance, $old_instance ) {
	    $instance=array();
	    foreach ($new_instance as $key => $value) {
	        switch ($key) {
                case 'title':
                    $instance['title'] = $value;
                    break;
                case 'hierarchical':
                    $instance['hierarchical'] = $value;
                    break;
                case 'count':
                    $instance['count'] = $value;
                    break;
                default:
                    $instance['selected_categories'][$key] = $value;
            }
        }
		return $instance;
	}

    /**
     * Display widget
     *
     * @param array $args
     * @param array $instance
     *
     */
	public function widget( $args, $instance ) {
        /**
         * @var string $before_widget
         * @var string $before_title
         * @var string $after_title
         * @var string $after_widget
        */

        extract( $args );

		// Check the widget options
		$title    = isset( $instance['title'] ) ? apply_filters( 'widget_title', $instance['title'] ) : '';
		$count    = isset( $instance['count'] ) ? $instance['count'] : 0;
		$hierarchical = isset( $instance['hierarchical'] ) ? $instance['hierarchical'] : 0;
		// prepare the list of categories id to be selected
        $categories_id = [];
        if (isset($instance['selected_categories'])) {
            foreach ($instance['selected_categories'] as $key => $value) {
                if ($cat_obj = get_category_by_slug($key))
                    $categories_id[] = $cat_obj->term_id;
            }
        }
		// WordPress core before_widget hook (always include )
        echo $before_widget;

		// Display the widget
		echo '<div class="widget-text wp_widget_plugin_box">';

			// Display widget title if defined
			if ( $title ) {
				echo $before_title . $title . $after_title;
			}

		    // Display the categories list
            if (!empty($categories_id)) {
                echo '<ul>';
                wp_list_categories(array(
                        'orderby' => 'name',
                        'include' => $categories_id,
                        'hierarchical' => $hierarchical,
                        'show_count' => $count,
                        'title_li' => ""
                ));
                echo '</ul>';
            }

		echo '</div>';

		// WordPress core after_widget hook (always include )
		echo $after_widget;

	}

}

// Register the widget
function TAINTRANET_FilterArchive_Widget() {
	register_widget( 'TAINTRANET_FilterArchive_Widget' );
}
/** @uses TAINTRANET_FilterArchive_Widget() */
add_action( 'widgets_init', 'TAINTRANET_FilterArchive_Widget' );