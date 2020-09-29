<?php

namespace GS\Conversion_To_Line_Notify;

/**
 * Class Line
 * @package GS\Conversion_To_Line_Notify
 */
class Line {

	/**
	 * LINE Notify send endpoint
	 * @var string
	 */
	private $line_endpoint_notify = 'https://notify-api.line.me/api/notify';

	/**
	 * LINE Notify check status endpoint
	 * @var string
	 */
	private $line_endpoint_status = 'https://notify-api.line.me/api/status';

	/**
	 * Option name
	 * @var string
	 */
	private $option_name = 'c2ln-setting';

	/**
	 * Contact constructor.
	 */
	public function __construct() {
		$this->run();
	}

	/**
	 * Run
	 */
	public function run() {
		$this->options = get_option( $this->option_name );
	}

	/**
	 * Check LINE access token status.
	 *
	 * @return bool
	 */
	public function status() {
		$access_token = $this->options['line_access_token'];
		if ( ! $access_token ) {
			return false;
		}

		$response = wp_remote_post( $this->line_endpoint_status, [
			'method'  => 'GET',
			'headers' => [
				'Authorization' => 'Bearer ' . $access_token,
			],
		] );

		// Check response status
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// Check response body status
		// @link https://notify-bot.line.me/doc/ja/
		$response_body = json_decode( wp_remote_retrieve_body( $response ) );
		if ( $response_body->status != 200 ) {
			return false;
		}

		return true;
	}

	/**
	 * Send LINE Notify.
	 *
	 * @param $message
	 *
	 * @return mixed
	 */
	public function notify( $message ) {
		$access_token = $this->options['line_access_token'];
		if ( ! $access_token ) {
			return false;
		}

		$response = wp_remote_post( $this->line_endpoint_notify, [
			'method'  => 'POST',
			'headers' => [
				'Authorization' => 'Bearer ' . $access_token,
			],
			'body'    => [
				'message' => $message,
			],
		] );

		// Check response status
		if ( is_wp_error( $response ) ) {
			return false;
		}

		// Check response body status
		// @link https://notify-bot.line.me/doc/ja/
		$response_body = json_decode( wp_remote_retrieve_body( $response ) );
		if ( $response_body->status != 200 ) {
			return false;
		}

		return true;
	}
}
