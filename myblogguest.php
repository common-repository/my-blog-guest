<?php
/*
	Plugin Name: MyBlogGuest
	Plugin URI: http://myblogguest.com
	Description:  <strong>MyBlogGuest official plugin lets you choose free unique guest articles to post on your blog without ever leaving your Wordpress dashboard.</strong> Please learn more about the Articles Gallery and the plugin <a href="http://myblogguest.com/blog/guest-blogging-wordpress-plugin/">here.</a>
	Version: 2.0.14
	Author:  Michael Tikhonin (MyBlogGuest)
	Author URI: http://myblogguest.com
	License: GPL2

    Copyright 2013-2017  MyBlogGuest  (info@myblogguest.com), partially based on code 3DoorDigital (http://www.3doordigital.com)

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


define( 'WPMBG_VERSION', '2.0.14' );

define( 'WPMBG_REQUIRED_WP_VERSION', '3.2' );

if ( ! defined( 'WPMBG_PLUGIN_BASENAME' ) )
	define( 'WPMBG_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

if ( ! defined( 'WPMBG_PLUGIN_NAME' ) )
	define( 'WPMBG_PLUGIN_NAME', trim( dirname( WPMBG_PLUGIN_BASENAME ), '/' ) );

if ( ! defined( 'WPMBG_SLUG' ) )
	define( 'WPMBG_SLUG', basename(dirname(__FILE__)));

if ( ! defined( 'WPMBG_PLUGIN_DIR' ) )
	define( 'WPMBG_PLUGIN_DIR', WP_PLUGIN_DIR . '/' . WPMBG_PLUGIN_NAME );

if ( ! defined( 'WPMBG_PLUGIN_URL' ) )
	define( 'WPMBG_PLUGIN_URL', WP_PLUGIN_URL . '/' . WPMBG_PLUGIN_NAME );

if ( ! defined( 'WPMBG_PLUGIN_INCLUDES_DIR' ) )
	define( 'WPMBG_PLUGIN_INCLUDES_DIR', WPMBG_PLUGIN_DIR . '/includes' );

require_once WPMBG_PLUGIN_DIR.'/common.php';

if ( ! defined( 'WPMBG_LOAD_JS' ) )
	define( 'WPMBG_LOAD_JS', true );

if ( ! defined( 'WPMBG_JS_URL' ) )
	define( 'WPMBG_JS_URL', WPMBG_PLUGIN_URL . '/js/wpmbg_process.js' );
	
if ( ! defined( 'WPMBG_LOAD_CSS' ) )
	define( 'WPMBG_LOAD_CSS', true );

if ( ! defined( 'WPMBG_CSS_URL' ) )
	define( 'WPMBG_CSS_URL', WPMBG_PLUGIN_URL . '/css/mbg.css' );

if ( ! defined( 'WPMBG_SHOW_DEBUG' ) )
	define( 'WPMBG_SHOW_DEBUG', false );

if ( ! defined( 'WPMBG_APP_AUTH_URL' ) )
	define( 'WPMBG_APP_AUTH_URL', WPMBG_BASE_URL.'/authorize?client_id=%APP_ID%&response_type=code&state=articles' );

if ( ! defined( 'WPMBG_USER_LOGIN_URL' ) )
	define( 'WPMBG_USER_LOGIN_URL', WPMBG_BASE_URL.'/authorize' );

if ( ! defined( 'WPMBG_REDIRECT_URI' ) )
	define( 'WPMBG_REDIRECT_URI', WPMBG_PLUGIN_URL . '/myblogguest_login.php' );

if ( ! defined( 'WPMBG_PREVIEW_URL' ) )
	define( 'WPMBG_PREVIEW_URL', WPMBG_PLUGIN_URL . '/preview_article.php' );

if ( ! defined( 'WPMBG_MAKE_OFFER_URL' ) )
	define( 'WPMBG_MAKE_OFFER_URL', WPMBG_PLUGIN_URL . '/make_offer.php' );

if ( ! defined( 'WPMBG_MAKE_IG_OFFER_URL' ) )
	define( 'WPMBG_MAKE_IG_OFFER_URL', WPMBG_PLUGIN_URL . '/make_ig_offer.php' );

if ( ! defined( 'WPMBG_MAKE_BOOK_OFFER_URL' ) )
	define( 'WPMBG_MAKE_BOOK_OFFER_URL', WPMBG_PLUGIN_URL . '/make_book_offer.php' );

if ( ! defined( 'WPMBG_DISPLAY_NAME' ) )
	define( 'WPMBG_DISPLAY_NAME', 'MyBlogGuest' );

if ( ! defined( 'WPMBG_IMGS' ) )
	define( 'WPMBG_IMGS', WPMBG_PLUGIN_URL . '/img' );

if ( ! defined( 'WPMBG_AJAX' ) )
	define( 'WPMBG_AJAX', get_admin_url() . 'admin-ajax.php' );

//update_option("wpmbg_version_message", '');

/* Loading Includes */

add_action( 'plugins_loaded', 'wpmbg_load_includes', 1 );

function wpmbg_load_includes() {
	$dir = WPMBG_PLUGIN_INCLUDES_DIR;

	if ( ! ( is_dir( $dir ) && $dh = opendir( $dir ) ) )
		return false;

	while ( ( $includes = readdir( $dh ) ) !== false ) {
		if ( substr( $includes, -4 ) == '.php' ) {
			include_once $dir . '/' . $includes;
			wpmbg_debug("Loaded Module: $includes");
		}			
	}
}

	
/* Show Debug Messages */
function wpmbg_debug($debug_message)
{
	if (WPMBG_SHOW_DEBUG) {
	echo ( "DEBUG: $debug_message\n<br>"); 
	}
}

//////////////////////Michael Tikhonin////////////////////////////////

register_activation_hook( __FILE__, 'mbg_activate' );
register_deactivation_hook( __FILE__, 'mbg_remove' );

function mbg_activate()
{

$fname = WPMBG_PLUGIN_DIR.'/init_options.php';

	if(file_exists($fname))
	{
	include_once $fname;
	@unlink($fname);
	}
	
	if(isset($init_options))
	{
	add_option('wpmbg_options', $init_options, '', 'yes');
	update_option('wpmbg_options', $init_options);
	}
	
}

function mbg_remove()
{
//delete_option('wpmbg_options');
}

