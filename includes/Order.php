<?php

namespace GS\Conversion_To_Line_Notify;

/**
 * Class Order
 * @package GS\Conversion_To_Line_Notify
 */
class Order {

	/**
	 * Option name
	 * @var string
	 */
	private $option_name = 'c2ln-setting';

	/**
	 * Order constructor.
	 */
	public function __construct() {
		$this->run();
	}

	/**
	 * Run
	 */
	public function run() {
		$this->options = get_option( $this->option_name );
		if ( $this->is_enabled() ) {
			$this->add_filters();
		}
	}

	/**
	 * Check if notification is enabled.
	 */
	public function is_enabled() {
		return ( ! empty( $this->options['is_order_enabled'] ) );
	}

	/**
	 * Add filter hook for contact form.
	 */
	public function add_filters() {
		add_action( 'woocommerce_checkout_order_processed', [
			$this,
			'woocommerce_checkout_order_processed_line_notify'
		], 10, 3 );
	}

	/**
	 * Send LINE Notify when when woocommerce_checkout_order_processed action runs.
	 *
	 * @param int $order_id
	 * @param mixed $posted_data
	 * @param WC_Order $order Order object.
	 */
	public function woocommerce_checkout_order_processed_line_notify( $order_id, $posted_data, $order ) {
		$wc_emails = WC()->mailer()->get_emails();

		// Adjust properties to avoid sending email.
		$wc_emails['WC_Email_New_Order']->enabled = 'no';
		$wc_emails['WC_Email_New_Order']->recipient = '';

		array_unshift( $wc_emails['WC_Email_New_Order']->plain_search, '/&yen;/i' );
		array_unshift( $wc_emails['WC_Email_New_Order']->plain_replace, '¥' );

		/**
		 * Filters plain search strings.
		 *
		 * @param array $plain_search
		 */
		$wc_emails['WC_Email_New_Order']->plain_search = apply_filters( 'c2ln_plain_search_strings', $wc_emails['WC_Email_New_Order']->plain_search );

		/**
		 * Filters plain replace strings.
		 *
		 * @param array $plain_replace
		 */
		$wc_emails['WC_Email_New_Order']->plain_replace = apply_filters( 'c2ln_plain_replace_strings', $wc_emails['WC_Email_New_Order']->plain_replace );

		$wc_emails['WC_Email_New_Order']->email_type = 'plain';
		$wc_emails['WC_Email_New_Order']->object     = $order;
		$wc_emails['WC_Email_New_Order']->trigger( $order_id );
		$message = $wc_emails['WC_Email_New_Order']->get_content();

		/**
		 * Filters send order message.
		 *
		 * @param string $message
		 * @param int $order_id
		 * @param mixed $posted_data
		 * @param WC_Order $order Order object.
		 */
		$message = apply_filters( 'c2ln_order_notify_message', $message, $order_id, $posted_data, $order );

		$line = new Line();
		$line->notify( $message );
	}
}
