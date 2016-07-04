<?php
/*
Plugin Name: Salenseo
Plugin URI: https://github.com//shivaacharjee/sailenseo
Author: Shiva Acharjee
Author URI: https://www.shivaacharjee.com
Description: One sollution for SEO with Rich Snippets along with the Open Graph Protocol
Version: 1.0.0
Text Domain: Sailenseo
License: GPL2
*/
/*  Copyright 2016 Sailenseo (email : contact@bshivaacharjee.com)
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
if ( !class_exists( "Sailenseo" ) )
{
	class Sailenseo
	{
		function __construct() // Constructor
		{
			wp_cache_delete ( 'alloptions', 'options' ); //clearing option cache 6/9/2016 Shiva Acharjee
			register_activation_hook(__FILE__, array($this, 'register_sailen_settings'));
//			add_action( 'admin_notices', array($this, 'display_message') );
			add_action( 'admin_head', array( $this, 'star_icons') );
			// Add Admin Menu
			add_action('admin_menu', array( $this, 'register_custom_menu_page') );
			add_action( 'admin_init', array( $this, 'set_styles' ));

			add_action( 'admin_init', array( $this, 'sailen_color_scripts' ));
//			add_action( 'init', array( $this, 'register_sailen_settings' ));

			add_filter('plugins_loaded', array( $this, 'rich_snippet_translation'));
			add_action( 'admin_enqueue_scripts', array( $this, 'post_enqueue') );
			add_action( 'admin_enqueue_scripts', array( $this, 'post_new_enqueue') );
			$plugin = plugin_basename(__FILE__);
			add_filter("plugin_action_links_$plugin", array( $this,'sailen_settings_link') );
			add_action( 'wp_ajax_sailen_submit_request', array( $this, 'sailen_submit_request') );

			add_action( 'wp_ajax_sailen_submit_color', array( $this, 'sailen_submit_color') );
			// Admin bar menu
			add_action( 'admin_bar_menu', array( $this, "aiosrs_admin_bar" ),100 );
		}
		// admin bar menu
		function aiosrs_admin_bar()
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
		function register_custom_menu_page()
		{
			require_once(plugin_dir_path( __FILE__ ).'admin/index.php');
			$page = add_menu_page('Sailenseo Dashboard', 'Sailenseo', 'administrator', 'sailen_rich_snippet_dashboard', 'sailen_rich_snippet_dashboard', 'div');
			//Call the function to print the stylesheets and javascripts in only this plugins admin area
			add_action( 'admin_print_styles-' . $page, 'sailen_admin_styles' );
			add_action('admin_print_scripts-' . $page, array( $this, 'sailen_iris_enqueue_scripts' ) );
		}
		// Add settings link on plugin page
		function sailen_settings_link($links) {
		  $settings_link = '<a href="admin.php?page=sailen_rich_snippet_dashboard">Settings</a>';
		  array_unshift($links, $settings_link);
		  return $links;
		}
		//print the star rating style on post edit page
		function post_enqueue($hook) {
			if( 'post.php' != $hook )
				return;
		//	wp_enqueue_script( 'sailen_jquery' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'sailen_jquery_star' );
			wp_enqueue_script( 'sailen_toggle' );
			wp_enqueue_style( 'star_style' );
			wp_register_script( 'sailen-scripts', sailen_META_BOX_URL . 'js/cmb.js','', '0.9.1' );
			wp_enqueue_script( 'sailen-scripts' );
			wp_register_script( 'sailen-scripts-media', sailen_META_BOX_URL . 'js/media.js', '', '1.0' );
			wp_enqueue_script( 'sailen-scripts-media' );
			wp_enqueue_script('jquery-ui-datepicker');
			if(!function_exists('vc_map'))
				wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		}
		function post_new_enqueue($hook) {
			if('post-new.php' != $hook )
				return;
		//	wp_enqueue_script( 'sailen_jquery' );
			wp_enqueue_script( 'jquery' );
			wp_enqueue_script( 'sailen_jquery_star' );
			wp_enqueue_script( 'sailen_toggle' );
			wp_enqueue_style( 'star_style' );
			wp_register_script( 'sailen-scripts', sailen_META_BOX_URL . 'js/cmb.js', '', '0.9.1' );
			wp_enqueue_script( 'sailen-scripts' );
			wp_register_script( 'sailen-scripts-media', sailen_META_BOX_URL . 'js/media.js', '', '1.0' );
			wp_enqueue_script( 'sailen-scripts-media' );
			wp_enqueue_script('jquery-ui-datepicker');
			if(!function_exists('vc_map'))
				wp_enqueue_style('jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');
		}
		//Initialize the metabox class
		function wp_initialize_sailen_meta_boxes() {
			if ( ! class_exists( 'sailen_Meta_Box' ) )
				require_once(plugin_dir_path( __FILE__ ) . 'init.php');
		}
		function set_styles() {
			wp_register_style( 'star_style', plugins_url('/css/jquery.rating.css', __FILE__) );
			wp_register_style( 'meta_style', plugins_url('admin/css/style.css', __FILE__) );

			wp_register_style( 'admin_style', plugins_url('admin/css/admin.css', __FILE__) );
			wp_register_script( 'sailen_jquery_ui', plugins_url('/js/jquery-ui.js', __FILE__) );
			wp_register_script( 'sailen_jquery_star', plugins_url('/js/jquery.rating.min.js', __FILE__) );
			wp_register_script( 'sailen_toggle', plugins_url('/js/toggle.js', __FILE__) );
		}
		// Define icon styles for the custom post type
		function star_icons() {
		?>
		<style>
			#toplevel_page_sailen_rich_snippet_dashboard .wp-menu-image {
				background: url(<?php echo plugins_url('/images/star.png',__FILE__); ?>) no-repeat !important;
			}
			#toplevel_page_sailen_rich_snippet_dashboard:hover .wp-menu-image, #toplevel_page_rich_snippet_dashboard.wp-has-current-submenu .wp-menu-image {
				background: url(<?php echo plugins_url('/images/star.png',__FILE__); ?>) no-repeat 0 -32px !important;
			}
			#toplevel_page_sailen_rich_snippet_dashboard .current .wp-menu-image, #toplevel_page_rich_snippet_dashboard.wp-has-current-submenu .wp-menu-image {
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
		function register_sailen_settings() {
			require_once(plugin_dir_path( __FILE__ ).'settings.php');
			sailen_add_review_option();
			sailen_add_event_option();
			sailen_add_person_option();
			sailen_add_product_option();
			sailen_add_recipe_option();
			sailen_add_software_option();
			sailen_add_video_option();
			sailen_add_article_option();
			sailen_add_service_option();
			sailen_add_color_option();
		}
		function sailen_submit_request()
		{
			$to = "Shiva Acharjee <contact@shivaacharjee.com>";
			$from = $_POST['email'];
			$site = $_POST['site_url'];
			$sub = $_POST['subject'];
			$message = $_POST['message'];
			$name = $_POST['name'];
			$post_url = $_POST['post_url'];

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
				  <title>Sailenseo</title>
				</head>
				<body>
					<table width="100%" cellpadding="10" cellspacing="10">
						<tr>
							<th colspan="2"> Sailenseo Support</th>
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
			$headers .= 'Cc: Prasant Rai <pkr.prasant@gmail.com>' . "\r\n";
			echo mail($to,$subject,$html,$headers) ? "Thank you!" : "Something went wrong!";

			die();
		}
		function sailen_submit_color()
		{

			$snippet_box_bg = $_POST['snippet_box_bg'];
			$snippet_title_bg = $_POST['snippet_title_bg'];
			$border_color = $_POST['snippet_border'];
			$title_color = $_POST['snippet_title_color'];
			$box_color = $_POST['snippet_box_color'];
			$color_opt = array(
				'snippet_box_bg'	   =>	$snippet_box_bg,
				'snippet_title_bg'	 =>	$snippet_title_bg,
				'snippet_border'	   =>	$border_color,
				'snippet_title_color'  =>	$title_color,
				'snippet_box_color'	=>	$box_color,
			);
			echo update_option('sailen_custom',$color_opt) ? 'Settings saved !' : 'Error occured. Satings were not saved !' ;

			die();
		}
		function sailen_iris_enqueue_scripts()
		{
				wp_enqueue_script( 'wp-color-picker' );
				// load the minified version of custom script
				wp_enqueue_script( 'cp_custom', plugins_url( 'js/cp-script.min.js', __FILE__ ), array( 'jquery', 'wp-color-picker' ), '1.1', true );
				wp_enqueue_style( 'wp-color-picker' );
		}
		function sailen_color_scripts()
		{
			global $wp_version;
			$sailen_script_array = array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker', 'media-upload', 'thickbox' );

			// styles required for cmb
			$sailen_style_array = array( 'thickbox' );

			// if we're 3.5 or later, user wp-color-picker
			if ( 3.5 <= $wp_version ) {

				$sailen_script_array[] = 'wp-color-picker';
				$sailen_style_array[] = 'wp-color-picker';

			} else {

				// otherwise use the older 'farbtastic'
				$sailen_script_array[] = 'farbtastic';
				$sailen_style_array[] = 'farbtastic';

			}
		}
	}
}
	require_once(plugin_dir_path( __FILE__ ).'functions.php');
	add_filter( 'sailen_meta_boxes', 'sailen_metaboxes' );
// Instantiating the Class
if (class_exists("Sailenseo")) {
	$Sailenseo= new Sailenseo();
}
?>