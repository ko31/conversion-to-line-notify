<?php
/**
 * Plugin Name:     Conversion to LINE Nofify
 * Plugin URI:      https://github.com/ko31/conversion-to-line-notify
 * Description:     The plugin send conversions to LINE Notify.
 * Author:          ko31
 * Author URI:      https://go-sign.info
 * Text Domain:     conversion-to-line-notify
 * Domain Path:     /languages
 * Version:         1.2.0
 *
 * @package         Conversion_To_Line_Notify
 */

namespace GS\Conversion_To_Line_Notify;

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'C2LN_TEXT_DOMAIN', 'conversion-to-line-notify' );
define( 'C2LN_PATH', plugin_dir_path( __FILE__ ) );

require_once( dirname( __FILE__ ) . '/vendor/autoload.php' );

require_once( dirname( __FILE__ ) . '/functions.php' );

add_action( 'plugins_loaded', function () {
	new Core();
} );
