<?php
/*
Plugin Name: Mumble Channel Viewer
Plugin URI: http://CommandChannel.com/Downloads/Wordpress-Mumble-Viewer.aspx
Description: Shows you who is logged in to your Mumble server and their status.
Version: 2.0.2
Author: Mike Johnson
Author URI: http://CommandChannel.com
License: GPLv2
*/

/*  Copyright 2010 - 2011 Command Channel Corporation

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

add_action( 'widgets_init', 'mumble_channel_viewer_load_widget' );

/**
 * Register our widget.
 *
 * @since 1.0
 */
function mumble_channel_viewer_load_widget() {
	register_widget( 'WP_MumbleChannelViewer' );
	wp_register_style( "mumble-channel-viewer", plugins_url( 'mumble-channel-viewer.css', __FILE__ ) );
	add_option('mumble_channel_viewer_data_uri');
	add_option('mumble_channel_viewer_data_format');
}

/**
 * Widget class.
 *
 * @since 1.0
 */
class WP_MumbleChannelViewer extends WP_Widget {
	function WP_MumbleChannelViewer() {
		/* Widget settings. */
		$widget_ops = array( 'classname' => 'Mumble Channel Viewer', 'description' => 'Shows you who is logged in to your Mumble server and their status.' );

		/* Create the widget. */
		$this->WP_Widget( 'mumble-channel-viewer', 'Mumble Channel Viewer', $widget_ops );
		
		add_action( 'wp_head', array( &$this, 'wp_head' ), 1 );
	}
	
	function wp_head() {
		wp_enqueue_style( "mumble-channel-viewer" );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		/* User-selected settings. */
		$title = apply_filters('widget_title', $instance['title'] );
		$dataUri = $instance['mumble_channel_viewer_data_uri'];
		$dataFormat = $instance['mumble_channel_viewer_data_format'];
		
		/* Before widget (defined by themes). */
		echo $before_widget;

		/* Title of widget (before and after defined by themes). */
		if ( $title )
			echo $before_title . $title . $after_title;

		echo '<div id="mumbleViewer">';
		if ( $dataUri && $dataFormat ) {
			require_once( 'class-mumble-channel-viewer.php' );
			echo MumbleChannelViewer::render( $dataUri, $dataFormat );
		}
		echo '</div>';

		/* After widget (defined by themes). */
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		/* Strip tags (if needed) and update the widget settings. */
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['mumble_channel_viewer_data_uri'] = strip_tags( $new_instance['mumble_channel_viewer_data_uri'] );
		$instance['mumble_channel_viewer_data_format'] = strip_tags( $new_instance['mumble_channel_viewer_data_format'] );

		return $instance;
	}
	
	function form( $instance ) {
		/* Set up some default widget settings. */
		$defaults = array( 'title' => __('Mumble Server'), 'mumble_channel_viewer_data_uri' => '', 'mumble_channel_viewer_data_format' => 'json' );
		$instance = wp_parse_args( (array) $instance, $defaults );
	?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" class="widefat" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id( 'mumble_channel_viewer_data_uri' ); ?>"><?php _e( 'Data URI' ); ?>:</label>
			<input id="<?php echo $this->get_field_id( 'mumble_channel_viewer_data_uri' ); ?>" name="<?php echo $this->get_field_name( 'mumble_channel_viewer_data_uri' ); ?>" value="<?php echo $instance['mumble_channel_viewer_data_uri']; ?>" class="widefat" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id( 'mumble_channel_viewer_data_format' ); ?>"><?php _e( 'Data Format' ); ?>:</label>
			<select id="<?php echo $this->get_field_id( 'mumble_channel_viewer_data_format' ); ?>" name="<?php echo $this->get_field_name( 'mumble_channel_viewer_data_format' ); ?>" class="widefat">
				<option value="json"<?php selected( $instance['mumble_channel_viewer_data_format'], 'json' ); ?>>JSON</option>
				<option value="xml"<?php selected( $instance['mumble_channel_viewer_data_format'], 'xml' ); ?>>XML</option>
			</select>
		</p>
	<?php
	}
}
?>
