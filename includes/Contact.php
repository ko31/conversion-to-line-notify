<?php

namespace GS\Conversion_To_Line_Notify;

/**
 * Class Contact
 * @package GS\Conversion_To_Line_Notify
 */
class Contact {

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
		if ( $this->is_enabled() ) {
			$this->add_filters();
		}
	}

	/**
	 * Check if notification is enabled.
	 */
	public function is_enabled() {
		return ( ! empty( $this->options['is_contact_enabled'] ) );
	}

	/**
	 * Add filter hook for contact form.
	 */
	public function add_filters() {
		// Get MW WP Form ids
		$posts    = get_posts( [
			'post_type'      => 'mw-wp-form',
			'posts_per_page' => - 1,
		] );
		$post_ids = array_map( function ( $post ) {
			return $post->ID;
		}, $posts );

		/**
		 * Filters target contact post ids.
		 *
		 * @param array $post_ids
		 */
		$post_ids = apply_filters( 'c2ln_contact_post_ids', $post_ids );

		foreach ( $post_ids as $post_id ) {
			add_filter( 'mwform_admin_mail_mw-wp-form-' . $post_id, [
				$this,
				'mwform_admin_mail_line_notify'
			], 10, 3 );
		}
	}

	/**
	 * Send LINE Notify when mwform_admin_mail_mw-wp-form-* action runs.
	 *
	 * @param object $Mail
	 * @param array $values
	 * @param MW_WP_Form_Data $Data
	 *
	 * @return mixed
	 *
	 * @link https://plugins.2inc.org/mw-wp-form/filter-hook/mwform_admin_mail/
	 */
	public function mwform_admin_mail_line_notify( $Mail, $values, $Data ) {
		$message = sprintf( "%s\n\n%s", $Mail->subject, $Mail->body );

		/**
		 * Filters send contact message.
		 *
		 * @param string $message
		 * @param object $Mail
		 * @param array $values
		 * @param MW_WP_Form_Data $Data
		 */
		$message = apply_filters( 'c2ln_contact_notify_message', $message, $Mail, $values, $Data );

		$line = new Line();
		$line->notify( $message );

		return $Mail;
	}
}
