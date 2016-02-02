<?php
/**
 * Plugin Name: WP Tumblr Widget
 * Version: 0.3
 * Plugin URI: https://github.com/founddrama/WP-Tumblr-Widget
 * Description: Plugin taps into the Tumblr API to suck in content from a
 * Tumblr tumblog and display it in a sidebar widget.  And unfortunately (for
 * now) it is dependent on jQuery.
 * Author: Rob Friesel
 * Author URI: https://blog.founddrama.net
*/

class WP_Tumblr_Widget extends WP_Widget {
	
	/** constructor */
	function WP_Tumblr_Widget() {
		$widget_ops = array('classname' => 'widget_wp_tumblr', 'description' => __('A simple Tumblr widget for WordPress blog sidebars.'));
		$control_ops = array('width' => 400, 'height' => 350);
		$this->WP_Widget('wp_tumblr', __('WP Tumblr Widget'), $widget_ops, $control_ops);
	}
	
	/** @see WP_Widget::widget */
	function widget($args, $instance) {
		extract($args);
		$tumblr_blog_name = $instance['tumblr_blog_name'];
		echo $before_widget;
		if ( $tumblr_blog_name ) {
			echo $before_title . $tumblr_blog_name . $after_title; ?>
			<div id="<?php echo $tumblr_blog_name; ?>-widget" class="tumblr-widget"></div>
			<script type="text/javascript">
				function tumblr_<?php echo $tumblr_blog_name; ?>Out(json){
					tumblr.writeTumblrList('<?php echo $tumblr_blog_name; ?>-widget', json);
				}
			</script>
		<?php
			add_action('wp_footer',
				create_function('', 'echo \'<script type="text/javascript" src="https://' . $tumblr_blog_name . '.tumblr.com/api/read/json?num=' .
					$instance['tumblr_post_limit'] . '&callback=tumblr_' . $tumblr_blog_name . 'Out"></script>\';')
			);
		}
		echo $after_widget;
	}
		
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['tumblr_blog_name'] = strip_tags($new_instance['tumblr_blog_name']);
		$instance['tumblr_post_limit'] = $new_instance['tumblr_post_limit'];
		return $instance;
	}
	
	function form($instance) {
		$tumblr_blog_name = strip_tags(esc_attr($instance['tumblr_blog_name']));
		$tumblr_post_limit = esc_attr($instance['tumblr_post_limit']);
		$title = $tumblr_blog_name; ?>
			<p>
				<label for="<?php echo $this->get_field_id('tumblr_blog_name'); ?>"><?php _e('Tumblr Blog Name:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('tumblr_blog_name'); ?>" name="<?php echo $this->get_field_name('tumblr_blog_name'); ?>" type="text" value="<?php echo $tumblr_blog_name; ?>" /></label>
				<label for="<?php echo $this->get_field_id('tumblr_post_limit'); ?>"><?php _e('Number of Posts:'); ?> <select class="widefat" id="<?php echo $this->get_field_id('tumblr_post_limit'); ?>" name="<?php echo $this->get_field_name('tumblr_post_limit'); ?>"><?php
					for ( $i = 1; $i <= 10; ++$i ) {
						echo "<option value=\"$i\" " . ( $tumblr_post_limit == $i ? 'selected="selected"' : '' ) . ">$i</option>";
					}
				?></select></label>
			</p>
		<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("WP_Tumblr_Widget");'));
add_action('wp_footer', create_function('', 'echo \'<script type="text/javascript" src="'.plugins_url('/js/tumblr-widget.js',__FILE__).'"></script>\';'));

?>