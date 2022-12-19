<?php
add_action(
	'widgets_init',
	function () {
		return register_widget( 'sunflower_follow_us' );
	}
);

class sunflower_follow_us extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'sunflower_follow_us',
			__( 'Folge uns auf ...' ),
			array(
				'description' => __( 'Show social media profiles defined in sunflower settings' ),
			)
		);
	}

	public function form( $instance ) {
		$defaults = array(
			'title' => __( 'Follow us' ),
		);
		$instance = wp_parse_args( (array) $instance, $defaults );

		$title = $instance['title'];

		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php echo 'Titel:'; ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		return array(
			'title' => strip_tags( $new_instance['title'] ),
		);
	}

	public function widget( $args, $instance ) {
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );

		echo $before_widget;

		if ( ! empty( $title ) ) {
			echo $before_title . $title . $after_title;
		}

		$output  = '<div class="widget-container">';
		$output .= get_sunflower_social_media_profiles();
		$output .= '</div>';

		echo $output;

		echo $after_widget;
	}
}
