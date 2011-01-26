<?php
/**
 * Plugin Name: WP Tumblr Widget
 * Version: 0.1
 * Plugin URI: http://blog.founddrama.net
 * Description: Plugin taps into the Tumblr API to suck in content from a Tumblr tumblog and display it in a sidebar widget.
 * Author: Rob Friesel
 * Author URI: http://blog.founddrama.net
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
		echo $before_widget;
		if ( $instance['tumblr_blog_name'] ) ?>
			<div id="<?php echo $instance['tumblr_blog_name'] ?>-widget"></div>
			<script type="text/javascript">
				if (!window.tumblr) { tumblr = {}; }
				if (!tumblr.buildTumbls) {
					tumblr.buildTumbls = function(json){
						var	posts		= json.posts,
							ulString	= '<ul>';

						while (posts.length > 0) {
							var p = posts.shift(),
								li = ['<li><a href="', p.url, '">'],
								txt;

							var shortnr = function(txt){
									if (txt.length > 100) {
										txt = txt.substr(0, 100);
										txt = txt.substr(0, txt.lastIndexOf(' ')) + '...';
									}
									return txt;
								};

							switch(p.type){
								case 'audio':
									txt = p['audio-caption'].replace(/<\/?[A-Za-z]+>/g, '') || '(audio)';
									break;
								case 'conversation':
									txt = p['conversation-title'];
									break;
								case 'link':
									txt = p['link-text'];
									break;
								case 'photo':
									txt = p['photo-caption'] || 'uncaptioned';
									break;
								case 'quote':
									txt = shortnr(p['quote-text']);
									break;
								case 'regular':
									txt = shortnr(p['regular-title'] || p['regular-body']);
									break;
								case 'video':
									txt = shortnr(p['video-caption'] || '(video)');
									break;
							}

							li.push(txt, '</a></li>');

							ulString += li.join('');
						}

						return ulString;
					}
				}
				tumblr.<?php echo $instance['tumblr_blog_name'] ?>Out = function(json){
					var divId   = '<?php echo $instance['tumblr_blog_name'] ?>-widget',
						blog	= json.tumblelog,
						posts	= tumblr.buildTumbls(json),
						widget	= $("#" + divId);

					widget.closest("li").children("h2")
						.addClass("tumblr-list")
						.empty().append(blog.title)
						.wrap('<a href="http://'+blog.name+'.tumblr.com/" />');
					widget.append(posts);
				};
			</script>
		<?php
		echo $after_widget;
		
		// TODO - the <script> tag needs to be at the end of the file!
			// something like this : 
				// add_action('wp_footer', array($thisInstance, 'insertscripts'));
		add_action('wp_footer', create_function('', 'echo \'<script type="text/javascript" src="http://' . $instance['tumblr_blog_name'] . '.tumblr.com/api/read/json?num=5&callback=tumblr.' . $instance['tumblr_blog_name'] . 'Out"></script>\';'));
		// TODO - each widget instance should check to see if tumblr.buildTumbls is already there
			// and only write the js if it hasn't already (do it w/ an include?)
	}
		
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['tumblr_blog_name'] = $new_instance['tumblr_blog_name'];
		return $instance;
	}
	
	function form($instance) {
		$tumblr_blog_name = esc_attr($instance['tumblr_blog_name']); ?>
			<p><label for="<?php echo $this->get_field_id('tumblr_blog_name'); ?>"><?php _e('Tumblr Blog Name:'); ?> <input class="widefat" id="<?php echo $this->get_field_id('tumblr_blog_name'); ?>" name="<?php echo $this->get_field_name('tumblr_blog_name'); ?>" type="text" value="<?php echo $tumblr_blog_name; ?>" /></label></p>
		<?php
	}
}

add_action('widgets_init', create_function('', 'return register_widget("WP_Tumblr_Widget");'));

?>