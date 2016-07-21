<?php
/*
Plugin Name: Sailenseo
Plugin URI: https://github.com//shivaacharjee/sailenseo
Author: Shiva Acharjee
Author URI: https://www.shivaacharjee.com
Description: One sollution for SEO with Rich Snippets along with the Open Graph Protocol
Version: 1.0.0
Text Domain: rich-snippets
License: GPL2
*/
/*  Copyright 2016 sailenseo (email : contact@bshivaacharjee.com)
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    You should have received a copy of the GNU General Public License
    along with this program.
*/
if ( !class_exists( "savp_sailenseo" ) )
{
	class savp_sailenseo
	{
		function __construct() // Constructor
		{
			wp_cache_delete ( 'alloptions', 'options' ); //clearing option cache 6/9/2016 Shiva Acharjee
			register_activation_hook(__FILE__, array($this, 'register_savp_sailen_settings'));
//			add_action( 'admin_notices', array($this, 'display_message') );
			add_action( 'admin_head', array( $this, 'savp_sailen_star_icons') );
			// Add Admin Menu
			add_action('admin_menu', array( $this, 'savp_sailen_register_custom_menu_page') );
			add_action( 'admin_init', array( $this, 'savp_sailen_set_styles' ));

			add_action( 'admin_init', array( $this, 'savp_sailen_color_scripts' ));
//			add_action( 'init', array( $this, 'register_savp_sailen_settings' ));

			add_filter('plugins_loaded', array( $this, 'rich_snippet_translation'));
			add_action( 'admin_enqueue_scripts', array( $this, 'savp_sailen_post_enqueue') );
			add_action( 'admin_enqueue_scripts', array( $this, 'savp_sailen_post_new_enqueue') );
			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this,'savp_sailen_settings_link') );
			add_action( 'wp_ajax_savp_sailen_submit_request', array( $this, 'savp_sailen_submit_request') );

			add_action( 'wp_ajax_savp_sailen_submit_color', array( $this, 'savp_sailen_submit_color') );
			// Admin bar menu
			add_action( 'admin_bar_menu', array( $this, "savp_sailen_admin_bar" ),100 );
		}
		// admin bar menu
		function savp_sailen_admin_bar()
		{
			global $wp_admin_bar;
			$actual_link = "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
			if ( ! is_super_admin() || ! is_admin_bar_showing() )
			  return;
			if(!is_admin())
			{
				$wp_admin_bar->add_menu( array(
				  'id' => 'aiosrs',
				  'title' => 'Test Rich Snippets',
				  'href' => 'http://www.google.com/webmasters/tools/richsnippets?q='.$actual_link,
				  'meta' => array('target' => '_blank'),
				) );
			}
		}
		function savp_sailen_register_custom_menu_page()
		{
			require_once(plugin_dir_path( __FILE__ ).'admin/index.php');
			$page = add_menu_page('savp_sailenseo Dashboard', 'SailenSeo', 'administrator', 'savp_sailen_rich_snippet_dashboard', 'savp_sailen_rich_snippet_dashboard', 'div');
			//Call the function to print the stylesheets and javascripts in only this plugins admin area
			add_action( 'admin_print_styles-' . $page, 'savp_sailen_admin_styles' );
			add_action('admin_print_scripts-' . $page, array( $this, 'savp_sailen_iris_enqueue_scripts' ) );
		}
		// Add settings link on plugin page
		function savp_sailen_settings_link($links) {
		  $settings_link = '<a href="admin.php?page=savp_sailen_rich_snippet_dashboard">Settings</a>';
		  array_unshift($links, $settings_link);
		  return $links;
		}
		//print the star rating style on post edit page
		function savp_sailen_post_enqueue($hook) {
			if( 'post.php' != $hook )
				return;
		//	wp_enqueue_script( 'savp_sailen_jquery' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'savp_sailen_jquery_star' );
			
			wp_enqueue_script( 'savp_sailen_toggle' );
			wp_enqueue_style( 'star_style' );
			wp_register_script( 'savp_sailen-scripts', savp_sailen_META_BOX_URL . 'js/cmb.js','', '0.9.1' );
			wp_enqueue_script( 'savp_sailen-scripts' );
			wp_register_script( 'savp_sailen-scripts-media', savp_sailen_META_BOX_URL . 'js/media.js', '', '1.0' );
			wp_enqueue_script( 'savp_sailen-scripts-media' );
			wp_enqueue_script('jquery-ui-datepicker');
			if(!function_exists('vc_map'))
			wp_enqueue_style('jquery-style', plugin_dir_url(__FILE__) . 'css/jquery-ui.css');
		}
		function savp_sailen_post_new_enqueue($hook) {
			if('post-new.php' != $hook )
				return;
		//	wp_enqueue_script( 'savp_sailen_jquery' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'savp_sailen_jquery_star' );
					
			wp_enqueue_script( 'savp_sailen_toggle' );
			wp_enqueue_style( 'star_style' );
			wp_register_script( 'savp_sailen-scripts', savp_sailen_META_BOX_URL . 'js/cmb.js', '', '0.9.1' );
			wp_enqueue_script( 'savp_sailen-scripts' );
			wp_register_script( 'savp_sailen-scripts-media', savp_sailen_META_BOX_URL . 'js/media.js', '', '1.0' );
			wp_enqueue_script( 'savp_sailen-scripts-media' );
			wp_enqueue_script('jquery-ui-datepicker');
			if(!function_exists('vc_map'))
				wp_enqueue_style('jquery-style', plugin_dir_url(__FILE__) . 'css/jquery-ui.css');
		}
		//Initialize the metabox class
		function wp_initialize_savp_sailen_meta_boxes() {
			if ( ! class_exists( 'savp_sailen_Meta_Box' ) )
				require_once(plugin_dir_path( __FILE__ ) . 'init.php');
		}
		function savp_sailen_set_styles() {
			wp_register_style( 'star_style', plugins_url('/css/jquery.rating.css', __FILE__) );
			wp_register_style( 'meta_style', plugins_url('admin/css/style.css', __FILE__) );

			wp_register_style( 'admin_style', plugins_url('admin/css/admin.css', __FILE__) );
			
			wp_register_script( 'savp_sailen_jquery_star', plugins_url('/js/jquery.rating.min.js', __FILE__) );
			wp_register_script( 'savp_sailen_toggle', plugins_url('/js/toggle.js', __FILE__) );
		}
		// Define icon styles for the custom post type
		function savp_sailen_star_icons() {
		?>
		<style>
			#toplevel_page_savp_sailen_rich_snippet_dashboard .wp-menu-image {
				background: url(<?php echo plugins_url('/images/star.png',__FILE__); ?>) no-repeat !important;
			}
			#toplevel_page_savp_sailen_rich_snippet_dashboard:hover .wp-menu-image, #toplevel_page_rich_snippet_dashboard.wp-has-current-submenu .wp-menu-image {
				background: url(<?php echo plugins_url('/images/star.png',__FILE__); ?>) no-repeat 0 -32px !important;
			}
			#toplevel_page_savp_sailen_rich_snippet_dashboard .current .wp-menu-image, #toplevel_page_rich_snippet_dashboard.wp-has-current-submenu .wp-menu-image {
				background: url(<?php echo plugins_url('/images/star.png',__FILE__); ?>) no-repeat 0 -32px !important;
			}
			#star-icons-32.icon32 {background: url(<?php echo plugins_url('/images/gray-32.png',__FILE__); ?>) no-repeat;}
		</style>
		<?php }
		/* Translation */
		function rich_snippet_translation()
		{
			// Load Translation File
			load_plugin_textdomain('rich-snippets', false, basename( dirname( __FILE__ ) ) . '/lang/' );
		}
		function register_savp_sailen_settings() {
			require_once(plugin_dir_path( __FILE__ ).'settings.php');
			savp_sailen_add_review_option();
			savp_sailen_add_event_option();
			savp_sailen_add_person_option();
			savp_sailen_add_product_option();
			savp_sailen_add_recipe_option();
			savp_sailen_add_software_option();
			savp_sailen_add_video_option();
			savp_sailen_add_article_option();
			savp_sailen_add_service_option();
			savp_sailen_add_color_option();
		}
		function savp_sailen_submit_request()
		{
			$to = "Shiva Acharjee <contact@shivaacharjee.com>";
			$from = sanitize_email($_POST['email']);
			$site = esc_url($_POST['site_url']);
			$sub = sanitize_text_field($_POST['subject']);
			$message = sanitize_text_field($_POST['message']);
			$name = sanitize_text_field($_POST['name']);
			$post_url = esc_url($_POST['post_url']);

			if($sub == "question")
				$subject = "[AIOSRS] New question received from ".$name;
			else if($sub == "bug")
				$subject = "[AIOSRS] New bug found by ".$name;
			else if($sub == "help")
				$subject = "[AIOSRS] New help request received from ".$name;
			else if($sub == "professional")
				$subject = "[AIOSRS] New service quote request received from ".$name;
			else if($sub == "contribute")
				$subject = "[AIOSRS] New development contribution request by ".$name;
			else if($sub == "other")
				$subject = "[AIOSRS] New contact request received from ".$name;

			$html = '
			<html>
				<head>
				  <title>savp_sailenseo</title>
				</head>
				<body>
					<table width="100%" cellpadding="10" cellspacing="10">
						<tr>
							<th colspan="2"> savp_sailenseo Support</th>
						</tr>
						<tr>
							<td width="22%"> Name : </td>
							<td width="78%"> <strong>'.$name.' </strong></td>
						</tr>
						<tr>
							<td> Email : </td>
							<td> <strong>'.$from.' </strong></td>
						</tr>
						<tr>
							<td> Website : </td>
							<td> <strong>'.$site.' </strong></td>
						</tr>
						<tr>
							<td> Ref. Post URL : </td>
							<td> <strong>'.$post_url.' </strong></td>
						</tr>
						<tr>
							<td colspan="2"> Message : </td>
                        </tr>
                        <tr>
							<td colspan="2"> '.$message.' </td>
						</tr>
					</table>
				</body>
			</html>
			';
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers .= 'From:'.$name.'<'.$from.'>' . "\r\n";
			$headers .= 'Cc: Prasant Rai <pkr.prasant1990@gmail.com>' . "\r\n";
			echo mail($to,$subject,$html,$headers) ? "Thank you!" : "Something went wrong!";

			die();
		}
		function savp_sailen_submit_color()
		{

			$snippet_box_bg = sanitize_text_field($_POST['snippet_box_bg']);
			$snippet_title_bg = sanitize_text_field($_POST['snippet_title_bg']);
			$border_color = sanitize_text_field($_POST['snippet_border']);
			$title_color = sanitize_text_field($_POST['snippet_title_color']);
			$box_color = sanitize_text_field($_POST['snippet_box_color']);
			$color_opt = array(
				'snippet_box_bg'	   =>	$snippet_box_bg,
				'snippet_title_bg'	 =>	$snippet_title_bg,
				'snippet_border'	   =>	$border_color,
				'snippet_title_color'  =>	$title_color,
				'snippet_box_color'	=>	$box_color,
			);
			echo update_option('savp_sailen_custom',$color_opt) ? 'Settings saved !' : 'Error occured. Satings were not saved !' ;

			die();
		}
		function savp_sailen_iris_enqueue_scripts()
		{
				wp_enqueue_script( 'wp-color-picker' );
				// load the minified version of custom script
				wp_enqueue_script( 'cp_custom', plugins_url( 'js/cp-script.min.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '1.1', true );
				wp_enqueue_style( 'wp-color-picker' );
		}
		function savp_sailen_color_scripts()
		{
			global $wp_version;
			$savp_sailen_script_array = array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'media-upload', 'thickbox' );

			// styles required for cmb
			$savp_sailen_style_array = array( 'thickbox' );

			// if we're 3.5 or later, user wp-color-picker
			if ( 3.5 <= $wp_version ) {

				$savp_sailen_script_array[] = 'wp-color-picker';
				$savp_sailen_style_array[] = 'wp-color-picker';

			} else {

				// otherwise use the older 'farbtastic'
				$savp_sailen_script_array[] = 'farbtastic';
				$savp_sailen_style_array[] = 'farbtastic';

			}
		}
	}
}
	require_once(plugin_dir_path( __FILE__ ).'functions.php');
	add_filter( 'savp_sailen_meta_boxes', 'savp_sailen_metaboxes' );
// Instantiating the Class
if (class_exists("savp_sailenseo")) {
	$savp_sailenseo= new savp_sailenseo();
}
?>