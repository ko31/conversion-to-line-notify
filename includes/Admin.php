<?php

namespace GS\Conversion_To_Line_Notify;

/**
 * Class Admin
 * @package GS\Conversion_To_Line_Notify
 */
class Admin {

	/**
	 * Option values
	 * @var
	 */
	private $options;

	/**
	 * Option name
	 * @var string
	 */
	private $option_name = 'c2ln-setting';

	/**
	 * Option group
	 * @var string
	 */
	private $option_group = 'c2ln-group';

	/**
	 * Admin constructor.
	 */
	public function __construct() {
		$this->run();
	}

	/**
	 * Run
	 */
	public function run() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ] );
		add_action( 'admin_init', [ $this, 'admin_init' ] );
	}

	/**
	 * Fires when admin_menu action runs.
	 */
	public function admin_menu() {
		add_options_page(
			__( 'Conversion to LINE Notify', 'conversion-to-line-notify' ),
			__( 'Conversion to LINE Notify', 'conversion-to-line-notify' ),
			'manage_options',
			'c2ln-settings',
			[ $this, 'display_settings' ]
		);
	}

	/**
	 * Fires when admin_init action runs.
	 */
	public function admin_init() {
		register_setting(
			$this->option_group,
			$this->option_name
		);

		add_settings_section(
			'general_settings',
			__( 'General Settings', 'conversion-to-line-notify' ),
			null,
			$this->option_group
		);

		add_settings_field(
			'line_access_token',
			__( 'LINE access token', 'conversion-to-line-notify' ),
			[ $this, 'line_access_token_callback' ],
			$this->option_group,
			'general_settings'
		);

		add_settings_field(
			'line_access_token_checker',
			'',
			[ $this, 'line_access_token_checker_callback' ],
			$this->option_group,
			'general_settings'
		);

		add_settings_field(
			'is_order_enabled',
			__( 'Order', 'conversion-to-line-notify' ),
			[ $this, 'is_order_enabled_callback' ],
			$this->option_group,
			'general_settings'
		);

		add_settings_field(
			'is_contact_enabled',
			__( 'Contact', 'conversion-to-line-notify' ),
			[ $this, 'is_contact_enabled_callback' ],
			$this->option_group,
			'general_settings'
		);
	}

	/**
	 * Render line_access_token field.
	 */
	public function line_access_token_callback() {
		$line_access_token = isset( $this->options['line_access_token'] ) ? $this->options['line_access_token'] : '';
		?>
		<input name="<?php echo $this->option_name; ?>[line_access_token]" type="text" id="line_access_token"
		       value="<?php echo $line_access_token; ?>"
		       class="regular-text">
		<?php
	}

	/**
	 * Render line_access_token_checker field.
	 */
	public function line_access_token_checker_callback() {
		$line_access_token = isset( $this->options['line_access_token'] ) ? $this->options['line_access_token'] : '';
		if ( $line_access_token ) :
			$line = new Line();
			if ( $line->status() ) {
				echo '<p class="description">' . __( 'LINE access token is valid.', '' ) . '</p>';
			} else {
				echo '<p class="description"><strong>' . __( 'LINE access token is invalid! Please check again.', '' ) . '</strong></p>';
			}
		endif;
	}

	/**
	 * Render is_order_enabled field.
	 */
	public function is_order_enabled_callback() {
		$is_order_enabled = isset( $this->options['is_order_enabled'] ) ? $this->options['is_order_enabled'] : '';
		?>
		<label>
			<input
				type="checkbox"
				name="<?php echo $this->option_name; ?>[is_order_enabled]"
				value="1"
				<?php if ( $is_order_enabled ) : ?>
					checked="checked"
				<?php endif; ?>
			/>
			<?php _e( 'Check if you want to enable order notifications.', 'conversion-to-line-notify' ); ?>
		</label>
		<?php
	}

	/**
	 * Render is_contact_enabled field.
	 */
	public function is_contact_enabled_callback() {
		$is_contact_enabled = isset( $this->options['is_contact_enabled'] ) ? $this->options['is_contact_enabled'] : '';
		?>
		<label>
			<input
				type="checkbox"
				name="<?php echo $this->option_name; ?>[is_contact_enabled]"
				value="1"
				<?php if ( $is_contact_enabled ) : ?>
					checked="checked"
				<?php endif; ?>
			/>
			<?php _e( 'Check if you want to enable contact notifications.', 'conversion-to-line-notify' ); ?>
		</label>
		<?php
	}

	/**
	 * Render settings.
	 */
	public function display_settings() {
		$this->options = get_option( $this->option_name );
		?>
		<form action="options.php" method="post">
			<h1><?php _e( 'Conversion to LINE Notify Settings', 'conversion-to-line-notify' ); ?></h1>
			<?php
			settings_fields( $this->option_group );
			do_settings_sections( $this->option_group );
			submit_button();
			?>
		</form>
		<?php
	}
}
