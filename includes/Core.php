<?php

namespace GS\Conversion_To_Line_Notify;

/**
 * Class core
 * @package GS\Conversion_To_Line_Notify
 */
class core {

	/**
	 * core constructor.
	 */
	public function __construct() {
		$this->run();
	}

	/**
	 * Run.
	 */
	public function run() {
		$this->set_locale();
		$this->load_modules();
	}

	/**
	 * Load translated strings.
	 */
	public function set_locale() {
		load_plugin_textdomain(
			C2LN_TEXT_DOMAIN,
			false,
			plugin_basename(C2LN_PATH) . '/languages/'
		);
	}

	/**
	 * Load this plugin modules.
	 */
	public function load_modules() {
		if ( is_admin() ) {
			new Admin();
		}
//		new Order();
//		new Contact();
	}
}
