<?php
/*
* Plugin Name: Playing with Shortcodes
* Plugin URI: http://nba.com
* Description: Demonstrating how shortcodes work. 
* Author: Zubin Khan
* Author URI: http://nba.com
* Version: 1.0
*/

//Calls the CSS file to the plugin
function plugin_enqueue_scripts (){
		wp_enqueue_style ('plugin', plugins_url ('new-plugins/css/style.css')); 
	} 
add_action( 'wp_enqueue_scripts','plugin_enqueue_scripts' );

//Changes the font color
function shortcode_div_fontcolor( $atts , $content=null ) {
	extract( shortcode_atts(
		array('color'=>'black',
		), $atts )
	);
return'<div class="text '.$color.'">'.$content.'</div>';

}
add_shortcode( 'shortcode_div_fontcolor', 'shortcode_div_fontcolor' );

//Calls the button
function link_nbabutton( $atts , $content=null ) {
	extract( shortcode_atts(
		array(
			'link'=>'http://nba.com',
			'linktxt'=>'Click Here!',
		), $atts )
	);
return'<div class="text"><div class="link-txt">'.do_shortcode($content) .'
</div><p><a href="'.$link.'" class="the-link">'.$linktxt.'</a></p></div>';
	
}
add_shortcode('link_nbabutton', 'link_nbabutton');

?>