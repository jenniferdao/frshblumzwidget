<?php
	
/*
* Plugin Name: Flower Post by Team Panda JZ
* Description: A widget to display the yearly archives.
* Plugin URI: http://phoenix.sheridanc.on.ca/~ccit3430
* Author: Jennifer Dao & Zubin Khan
* Author URI: http://phoenix.sheridanc.on.ca/~ccit3430
* Version: v1.0
*/
//Calls the CSS file to the plugin
function teampandajz_plugin_enqueue_scripts (){
		wp_enqueue_style ('plugin', plugins_url ('frshblumz/css/freshblumzstyles.css')); 
	} style.
add_action( 'wp_enqueue_scripts','teampandajz_plugin_enqueue_scripts' );
/*
*Register the custom post type for the slider. This will also add an array of settings to the new custom post type.
*/
add_action('init','register_my_post');
function register_my_post() {
	register_post_type('flowerpost',
		array(
			'labels' => array(
				'name' => ('flowerpost'),
				'singular_name' => 'Flowers',
				'add_new' => 'Add New Flower',
				'add_new_item' => 'Add New Post',
				'edit_item' => 'Edit',
				'new_item' => 'New',
				'all_items' => 'All',
				'view_items' => 'View',
				'search_items' => 'Search',
				'not_found' => 'Not found',
				'not_found_in_trash' => 'None in Trash',
				'parent_item_colon' => '',
				),
			'public' => true,
			'exclude_from_search' => true,
			//Enables the post to support a title, thumbnail and editor.
			'supports' => array(
				'title',
				'thumbnail',
				'editor'
				)
			)
		);
}

class teampandajz_widget extends WP_Widget {

	function __construct() {
		parent::__construct('teampandajz_widget', __('teampandajz Widget', 'teampandajz_widget_domain'), array( 'description' => __( 'teampandajz widget', 'teampandajz_widget_domain' ), )
		);
	}

	// Creating widget front-end
	public function widget( $args, $instance ) {
	
		$title = apply_filters( 'widget_title', $instance['title'] );
		$number = get_option('flowerpost_show_settype',0) == 0 ? $instance['number'] : get_option('flowerpost_show_number',3);
		
		// before and after widget arguments are defined by themes
		echo $args['before_widget'];
		if ( ! empty( $title ) )
			echo $args['before_title'] . $title . $args['after_title'];
		
		// Display Post
		echo "teampandajz: ";
			$args = array('post_type'=>'flowerpost', 'posts_per_page'=>$number);
			$query = new WP_Query($args);
			if ($query ->have_posts() ) {
				echo '<div style="height:20px">&nbsp;</div><div  style="margin-left:50px"><ul class="teampandajz_widget">';
				$ct=0;
				$col = 4;
				$col = max(min($col, $number, $query->found_posts), 1);
				while($query->have_posts() && $ct++ < $number) {
					// Will concatenate the variables and output the 'eventItem' string.
					$query->the_post();
					$image = (has_post_thumbnail($post->ID)) ? get_the_post_thumbnail($post->ID) : '<div class="thumbnail not found"></div>';
					if($ct % $col == 0)
					{
						$typeflower = '<li style=" width:'.intval(100/$col).'%;display:block;float:right;text-align: center;">';
					}
					else
						$typeflower = '<li style="width:'.intval(100/$col).'%; display:inline-block;float:left;text-align: center;">';
					$typeflower .= '<div style=" margin:0px 40px; padding:2px;background-color:#e080e0;"><a href="' . get_permalink() . '">'. $image.'<br />';
					$typeflower .= '<div style="height:60px;"><p style="vertical-align:middle;margin-bottom:0px;">' .get_the_title() . '</p></div></a></div></li>';

					echo $typeflower;
				}
				echo '<li style=" width:' . intval(100/$col).'%;display:block;float:right;text-align: center;">&nbsp;<br />';
				echo '</ul></div><div class="clear">&nbsp;</div>';
				wp_reset_query();
				wp_reset_postdata();
			}
		}

	// Widget Backend
	public function form( $instance ) {
		if ( isset( $instance[ 'title' ] ) ) {
			$title = $instance[ 'title' ];
		}
		else {
			$title = __( 'New title', 'teampandajz_widget_domain' );
		}

		$number = get_option('flowerpost_show_settype',0) == 0 ? $instance['number'] : get_option('flowerpost_show_number',3);

	// Widget admin form
?>

<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
<label for="<?php echo $this->get_field_id( 'number' ); ?>"><?php _e( 'Number:' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'number' ); ?>" name="<?php echo $this->get_field_name( 'number' ); ?>" type="text" value="<?php echo esc_attr( $number ); ?>" />
</p>

<?php
	}

	// Updating widget replacing old instances with new
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
		$instance['number'] = ( ! empty( $new_instance['number'] ) ) ? strip_tags( $new_instance['number'] ) : '';
		update_option('flowerpost_show_number', $instance['number']);
		update_option('flowerpost_show_settype', 0);
		return $instance;
	}
} // Class teampandajz_widget ends here



// Register and load the widget

function teampandajz_load_widget() {
    register_widget( 'teampandajz_widget' );
}

add_action( 'widgets_init', 'teampandajz_load_widget' );


add_action( 'admin_init', 'flowerpost_plugin_settings' );
function flowerpost_plugin_settings() {
	register_setting( 'flowerpost-plugin-settings', 'flowerpost_show_number' );
	register_setting( 'flowerpost-plugin-settings', 'flowerpost_show_settype' );
}

add_action('admin_menu', 'flowerpost_plugin_menu');
function flowerpost_plugin_menu() {
	add_submenu_page ( get_admin_page_parent ( $parent = '' ), 'flowerpost Plugin Settings', 'Settings', 'administrator', 'flowerpost-plugin-settings', 'flowerpost_plugin_settings_page');
}

function flowerpost_plugin_settings_page() {

?>

	<div class="wrap">
	<h2>flowerpost Settings</h2>
	
	<form method="post" action="options.php">
	    <?php settings_fields( 'flowerpost-plugin-settings' ); ?>
	    <?php do_settings_sections( 'flowerpost-plugin-settings' ); ?>
	    <table class="form-table">
	        <tr valign="top">
	        <th scope="row">Post Number</th>
	        <td><input type="text" name="flowerpost_show_number" value="<?php echo esc_attr( get_option('flowerpost_show_number') ); ?>" />
            <input type="hidden" name="flowerpost_show_settype" value="1" />
            </td>
	        </tr>
	    </table>
	    
	    <?php submit_button(); ?>
	
	</form>
	</div>


<?php  

}

add_shortcode('flowerpost_shortcode', 'flowerpost_shortcode' );
function flowerpost_shortcode($stts){
	$number = isset($stts['number']) ? $stts['number'] : get_option('flowerpost_show_number',3);
	$col = isset($stts['colnum']) ? $stts['colnum'] : 4;
	$col = max(min($col, $number, $query->found_posts), 1);

	// Display Post
	$args = array('post_type'=>'flowerpost', 'posts_per_page'=>$number);
	$query = new WP_Query($args);
	if ($query ->have_posts() ) {
		echo '<div style="height:20px">&nbsp;</div><div  style="margin-left:50px"><ul class="teampandajz_widget">';
		$ct=0;
		while($query->have_posts() && $ct++ < $number) {
			// Will concatenate the variables and output the 'eventItem' string.
			$query->the_post();
			$image = (has_post_thumbnail($post->ID)) ? get_the_post_thumbnail($post->ID) : '<div class="thumbnail not found"></div>';
			if($ct % $col == 0)
			{
				$typeflower = '<li style=" width:'.intval(100/$col).'%;display:block;float:right;text-align: center;">';
			}
			else
				$typeflower = '<li style="width:'.intval(100/$col).'%; display:inline-block;float:left;text-align: center;">';
			$typeflower .= '<div style=" margin:0px 40px; padding:2px;background-color:#e080e0;"><a href="' . get_permalink() . '">'. $image.'<br />';
			$typeflower .= '<div style="height:60px;"><p style="vertical-align:middle;margin-bottom:0px;">' .get_the_title() . '</p></div></a></div></li>';

			echo $typeflower;
		}
		echo '<li style=" width:' . intval(100/$col).'%;display:block;float:right;text-align: center;">&nbsp;<br />';
		echo '</ul></div><div class="clear">&nbsp;</div>';
		wp_reset_query();
		wp_reset_postdata();
	}
}

?>