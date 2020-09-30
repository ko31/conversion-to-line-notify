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
		add_action( 'woocommerce_new_order', [ $this, 'woocommerce_new_order_line_notify' ], 10, 2 );
	}

	/**
	 * Send LINE Notify when when woocommerce_new_order action runs.
	 *
	 * @param int $order_id
	 * @param WC_Order $order Order object.
	 */
	public function woocommerce_new_order_line_notify( $order_id, $order ) {
		$mailer             = WC()->mailer();
		$template           = 'emails/plain/admin-new-order.php';
		$template_path      = '';
		$default_path       = untrailingslashit( C2LN_PATH ) . '/templates/';
		$heading            = sprintf( __( 'New order #%d', 'conversion-to-line-notify' ), $order_id );
		$additional_content = '';
		$message            = wc_get_template_html(
			$template,
			[
				'order'              => $order,
				'email_heading'      => $heading,
				'sent_to_admin'      => false,
				'plain_text'         => true,
				'email'              => $mailer,
				'additional_content' => $additional_content,
			],
			$template_path,
			$default_path
		);

		// Strip HTML tags
		$message = strip_tags( $message );

		// Decode entity tags
		$message = html_entity_decode( $message );

		/**
		 * Filters send order message.
		 *
		 * @param string $message
		 * @param int $order_id
		 * @param WC_Order $order Order object.
		 */
		$message = apply_filters( 'c2ln_order_notify_message', $message, $order_id, $order );

		$line = new Line();
		$line->notify( $message );
	}
}
