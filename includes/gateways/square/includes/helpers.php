<?php
/**
 * Helper Functions for Square Core.
 *
 * @package     Give
 * @sub-package Square Core
 * @copyright   Copyright (c) 2019, GiveWP
 * @license     https://opensource.org/licenses/gpl-license GNU Public License
 * @since       2.6.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Square uses it's own credit card form because the card fields are coming from iframe.
 *
 * @param int  $form_id Donation Form ID.
 * @param int  $args    Donation Form Arguments.
 * @param bool $echo    Status to display or not.
 *
 * @access public
 * @since  2.6.0
 *
 * @return string $form
 */
function give_square_credit_card_form( $form_id, $args, $echo = true ) {

	$id_prefix = ! empty( $args['id_prefix'] ) ? $args['id_prefix'] : '';

	$card_number_class = 'form-row form-row-two-thirds form-row-responsive give-square-cc-field-wrap';
	$card_cvc_class    = 'form-row form-row-one-third form-row-responsive give-square-cc-field-wrap';
	$card_name_class   = 'form-row form-row-two-thirds form-row-responsive give-square-cc-field-wrap';
	$card_exp_class    = 'form-row form-row-first form-row-responsive card-expiration give-square-cc-field-wrap';
	$card_zip_class    = 'form-row form-row-last form-row-responsive give-square-cc-field-wrap';

	if ( give_square_can_collect_billing_details() ) {
		$card_exp_class = 'card-expiration form-row form-row-one-third form-row-responsive give-square-cc-field-wrap';
		$card_zip_class = 'form-row form-row-one-third form-row-responsive give-square-cc-field-wrap';
	}

	ob_start();

	do_action( 'give_before_cc_fields', $form_id );
	?>

	<fieldset id="give_cc_fields" class="give-do-validate">
		<legend><?php esc_html_e( 'Credit Card Info', 'give' ); ?></legend>
        <?php
        $application_id = give_square_get_application_id();
        if ( empty( $application_id ) ) {

            // Show frontend notice when Square is not configured properly.
	        Give()->notices->print_frontend_notice(
                sprintf(
                    /* translators: 1. Text, 2. Link, 3. Link Text */
             '%1$s <a href="%2$s">%3$s</a>',
                    __( 'Square is not set up yet to accept payments. Please configure the gateway in order to accept donations. If you\'re having trouble please review', '' ),
                    esc_url( 'http://docs.givewp.com/addon-square' ),
                    __( 'Give\'s Square documentation.', 'give' )
                )
            );
	        return false;
        }

        // Show frontend notice when site not accessed with SSL.
        if ( ! is_ssl() ) {
	        Give()->notices->print_frontend_notice( __( 'This page requires a valid SSL certificate for secure donations. Please try accessing this page with HTTPS in order to load Credit Card fields.', 'give' ) );
	        return false;
        }
        ?>
        <div id="give_secure_site_wrapper">
            <span class="give-icon padlock"></span>
            <span>
                <?php esc_attr_e( 'This is a secure SSL encrypted payment.', 'give' ); ?>
            </span>
        </div>

		<div id="give-card-number-wrap" class="<?php echo esc_attr( $card_number_class ); ?>">
			<div>
				<label for="give-card-number-field-<?php echo esc_html( $id_prefix ); ?>" class="give-label">
					<?php esc_attr_e( 'Card Number', 'give' ); ?>
					<span class="give-required-indicator">*</span>
					<span class="give-tooltip give-icon give-icon-question"
						data-tooltip="<?php esc_attr_e( 'The (typically) 16 digits on the front of your credit card.', 'give' ); ?>"></span>
					<span class="card-type"></span>
				</label>
				<div id="give-card-number-field-<?php echo esc_html( $id_prefix ); ?>" class="input empty give-square-cc-field give-square-card-number-field"></div>
			</div>
		</div>

		<div id="give-card-cvc-wrap" class="<?php echo esc_attr( $card_cvc_class ); ?>">
			<div>
				<label for="give-card-cvc-field-<?php echo esc_html( $id_prefix ); ?>" class="give-label">
					<?php esc_attr_e( 'CVC', 'give' ); ?>
					<span class="give-required-indicator">*</span>
					<span class="give-tooltip give-icon give-icon-question"
						data-tooltip="<?php esc_attr_e( 'The 3 digit (back) or 4 digit (front) value on your card.', 'give' ); ?>"></span>
				</label>
				<div id="give-card-cvc-field-<?php echo esc_html( $id_prefix ); ?>" class="input empty give-square-cc-field give-square-card-cvc-field"></div>
			</div>
		</div>

        <?php
        if ( give_square_can_collect_billing_details() ) {
            ?>
            <div id="give-card-name-wrap" class="<?php echo esc_attr( $card_name_class ); ?>">
                <label for="card_name" class="give-label">
                    <?php esc_attr_e( 'Cardholder Name', 'give' ); ?>
                    <span class="give-required-indicator">*</span>
                    <span class="give-tooltip give-icon give-icon-question"
                          data-tooltip="<?php esc_attr_e( 'The name of the credit card account holder.', 'give' ); ?>"></span>
                </label>

                <input
                        type="text"
                        autocomplete="off"
                        id="card_name"
                        name="card_name"
                        class="card-name give-input required"
                        placeholder="<?php esc_attr_e( 'Cardholder Name', 'give' ); ?>"
                />
            </div>
            <?php
        }

        do_action( 'give_before_cc_expiration' );
        ?>

		<div id="give-card-expiration-wrap" class="<?php echo esc_attr( $card_exp_class ); ?>">
			<div>
				<label for="give-card-expiration-field-<?php echo esc_html( $id_prefix ); ?>" class="give-label">
					<?php esc_attr_e( 'Expiration', 'give' ); ?>
					<span class="give-required-indicator">*</span>
					<span class="give-tooltip give-icon give-icon-question"
						data-tooltip="<?php esc_attr_e( 'The date your credit card expires, typically on the front of the card.', 'give' ); ?>"></span>
				</label>

				<div id="give-card-expiration-field-<?php echo esc_html( $id_prefix ); ?>" class="input empty give-square-cc-field give-square-card-expiration-field"></div>
			</div>
		</div>

        <?php
        if ( ! give_square_can_collect_billing_details() ) {
            ?>
            <div id="give-card-zip-wrap" class="<?php echo esc_attr( $card_zip_class ); ?>">
                <label for="card_zip" class="give-label">
			        <?php esc_attr_e('Zip / Postal Code', 'give'); ?>
                    <span class="give-required-indicator">*</span>
			        <?php echo Give()->tooltips->render_help(__('The ZIP Code or postal code for your billing address.', 'give')); ?>
                </label>

                <div id="give-square-card-zip-<?php echo esc_html($id_prefix); ?>"
                     class="input empty give-square-cc-field give-square-card-expiration-field"></div>
            </div>
	        <?php
        }

		do_action( 'give_after_cc_expiration', $form_id );
		?>

	</fieldset>
	<?php

	// Remove Address Fields if user has option enabled.
	remove_action( 'give_after_cc_fields', 'give_default_cc_address_fields' );

	do_action( 'give_square_after_cc_fields', $form_id, $args );

	$form = ob_get_clean();

	if ( false !== $echo ) {
		echo $form;
	}

	return $form;
}

add_action( 'give_square_cc_form', 'give_square_credit_card_form', 10, 3 );

/**
 * Outputs the default credit card address fields.
 *
 * @since 2.6.0
 *
 * @param int $form_id The form ID.
 *
 * @return void
 */
function give_square_default_cc_address_fields( $form_id, $args ) {

    // Bailout, if don't need to collect billing details.
    if ( ! give_square_can_collect_billing_details() ) {
        return;
    }

	$id_prefix = ! empty( $args['id_prefix'] ) ? $args['id_prefix'] : '';

	// Get user info.
	$give_user_info = _give_get_prefill_form_field_values( $form_id );

	if ( is_user_logged_in() ) {
		$user_address = give_get_donor_address( get_current_user_id() );
	}

	ob_start();
	?>
	<fieldset id="give_cc_address" class="cc-address">

		<legend>
			<?php echo apply_filters( 'give_billing_details_fieldset_heading', esc_html__( 'Billing Details', 'give' ) ); ?>
		</legend>

		<?php
		/**
		 * Fires while rendering credit card billing form, before address fields.
		 *
		 * @since 1.0.0
		 *
		 * @param int $form_id The form ID.
		 */
		do_action( 'give_cc_billing_top' );

		// For Country.
		$selected_country = give_get_country();
		if ( ! empty( $give_user_info['billing_country'] ) && '*' !== $give_user_info['billing_country'] ) {
			$selected_country = $give_user_info['billing_country'];
		}
		$countries = give_get_country_list();

		// For state.
		$selected_state = '';
		if ( $selected_country === give_get_country() ) {

			// Get default selected state by admin.
			$selected_state = give_get_state();
		}

		// Get the last payment made by user states.
		if ( ! empty( $give_user_info['card_state'] ) && '*' !== $give_user_info['card_state'] ) {
			$selected_state = $give_user_info['card_state'];
		}

		// Get the country code.
		if ( ! empty( $give_user_info['billing_country'] ) && '*' !== $give_user_info['billing_country'] ) {
			$selected_country = $give_user_info['billing_country'];
		}

		$label        = __( 'State', 'give' );
		$states_label = give_get_states_label();

		// Check if $country code exists in the array key for states label.
		if ( array_key_exists( $selected_country, $states_label ) ) {
			$label = $states_label[ $selected_country ];
		}
		$states = give_get_states( $selected_country );

		// Get the country list that do not have any states init.
		$no_states_country = give_no_states_country_list();

		// Get the country list that does not require states.
		$states_not_required_country_list = give_states_not_required_country_list();

		?>
		<p id="give-card-country-wrap" class="form-row form-row-wide">
			<label for="billing_country" class="give-label">
				<?php esc_html_e( 'Country', 'give' ); ?>
				<?php if ( give_field_is_required( 'billing_country', $form_id ) ) : ?>
					<span class="give-required-indicator">*</span>
				<?php endif; ?>
				<span class="give-tooltip give-icon give-icon-question"
						data-tooltip="<?php esc_attr_e( 'The country for your billing address.', 'give' ); ?>"></span>
			</label>

			<select
					name="billing_country"
					autocomplete="country-name"
					id="billing_country"
					class="billing-country billing_country give-select<?php echo( give_field_is_required( 'billing_country', $form_id ) ? ' required' : '' ); ?>"
				<?php echo( give_field_is_required( 'billing_country', $form_id ) ? ' required aria-required="true" ' : '' ); ?>
			>
				<?php
				foreach ( $countries as $country_code => $country ) {
					echo '<option value="' . esc_attr( $country_code ) . '"' . selected( $country_code, $selected_country, false ) . '>' . $country . '</option>';
				}
				?>
			</select>
		</p>

		<p id="give-card-address-wrap" class="form-row form-row-wide">
			<label for="card_address" class="give-label">
				<?php _e( 'Address 1', 'give' ); ?>
				<?php
				if ( give_field_is_required( 'card_address', $form_id ) ) :
				?>
					<span class="give-required-indicator">*</span>
				<?php endif; ?>
				<?php echo Give()->tooltips->render_help( __( 'The primary billing address for your credit card.', 'give' ) ); ?>
			</label>

			<input
					type="text"
					id="card_address"
					name="card_address"
					autocomplete="address-line1"
					class="card-address give-input<?php echo( give_field_is_required( 'card_address', $form_id ) ? ' required' : '' ); ?>"
					placeholder="<?php _e( 'Address line 1', 'give' ); ?>"
					value="<?php echo isset( $give_user_info['card_address'] ) ? $give_user_info['card_address'] : ''; ?>"
				<?php echo( give_field_is_required( 'card_address', $form_id ) ? '  required aria-required="true" ' : '' ); ?>
			/>
		</p>

		<p id="give-card-address-2-wrap" class="form-row form-row-wide">
			<label for="card_address_2" class="give-label">
				<?php _e( 'Address 2', 'give' ); ?>
				<?php if ( give_field_is_required( 'card_address_2', $form_id ) ) : ?>
					<span class="give-required-indicator">*</span>
				<?php endif; ?>
				<?php echo Give()->tooltips->render_help( __( '(optional) The suite, apartment number, post office box (etc) associated with your billing address.', 'give' ) ); ?>
			</label>

			<input
					type="text"
					id="card_address_2"
					name="card_address_2"
					autocomplete="address-line2"
					class="card-address-2 give-input<?php echo( give_field_is_required( 'card_address_2', $form_id ) ? ' required' : '' ); ?>"
					placeholder="<?php _e( 'Address line 2', 'give' ); ?>"
					value="<?php echo isset( $give_user_info['card_address_2'] ) ? $give_user_info['card_address_2'] : ''; ?>"
				<?php echo( give_field_is_required( 'card_address_2', $form_id ) ? ' required aria-required="true" ' : '' ); ?>
			/>
		</p>

		<p id="give-card-city-wrap" class="form-row form-row-wide">
			<label for="card_city" class="give-label">
				<?php _e( 'City', 'give' ); ?>
				<?php if ( give_field_is_required( 'card_city', $form_id ) ) : ?>
					<span class="give-required-indicator">*</span>
				<?php endif; ?>
				<?php echo Give()->tooltips->render_help( __( 'The city for your billing address.', 'give' ) ); ?>
			</label>
			<input
					type="text"
					id="card_city"
					name="card_city"
					autocomplete="address-level3"
					class="card-city give-input<?php echo( give_field_is_required( 'card_city', $form_id ) ? ' required' : '' ); ?>"
					placeholder="<?php _e( 'City', 'give' ); ?>"
					value="<?php echo isset( $give_user_info['card_city'] ) ? $give_user_info['card_city'] : ''; ?>"
				<?php echo( give_field_is_required( 'card_city', $form_id ) ? ' required aria-required="true" ' : '' ); ?>
			/>
		</p>

		<p id="give-card-state-wrap"
				class="form-row form-row-first form-row-responsive <?php echo ( ! empty( $selected_country ) && array_key_exists( $selected_country, $no_states_country ) ) ? 'give-hidden' : ''; ?> ">
			<label for="card_state" class="give-label">
				<span class="state-label-text"><?php echo $label; ?></span>
				<?php
				if ( give_field_is_required( 'card_state', $form_id ) ) :
					?>
					<span class="give-required-indicator <?php echo( array_key_exists( $selected_country, $states_not_required_country_list ) ? 'give-hidden' : '' ); ?> ">*</span>
				<?php endif; ?>
				<span class="give-tooltip give-icon give-icon-question"
						data-tooltip="<?php esc_attr_e( 'The state, province, or county for your billing address.', 'give' ); ?>"></span>
			</label>
			<?php

			if ( ! empty( $states ) ) :
			?>
				<select
						name="card_state"
						autocomplete="address-level4"
						id="card_state"
						class="card_state give-select<?php echo( give_field_is_required( 'card_state', $form_id ) ? ' required' : '' ); ?>"
					<?php echo( give_field_is_required( 'card_state', $form_id ) ? ' required aria-required="true" ' : '' ); ?>>
					<?php
					foreach ( $states as $state_code => $state ) {
						echo '<option value="' . $state_code . '"' . selected( $state_code, $selected_state, false ) . '>' . $state . '</option>';
					}
					?>
				</select>
			<?php else : ?>
				<input type="text" size="6" name="card_state" id="card_state" class="card_state give-input"
						placeholder="<?php echo $label; ?>" value="<?php echo $selected_state; ?>"/>
			<?php endif; ?>
		</p>

		<p id="give-card-zip-wrap" class="form-row form-row-last form-row-responsive">
			<label for="card_zip" class="give-label">
				<?php _e( 'Zip / Postal Code', 'give' ); ?>
				<?php if ( give_field_is_required( 'card_zip', $form_id ) ) : ?>
					<span class="give-required-indicator">*</span>
				<?php endif; ?>
				<?php echo Give()->tooltips->render_help( __( 'The ZIP Code or postal code for your billing address.', 'give' ) ); ?>
			</label>

			<input
					type="text"
					size="4"
					id="give-square-card-zip-<?php echo esc_html( $id_prefix ); ?>"
					name="card_zip"
					autocomplete="postal-code"
					class="card-zip give-input<?php echo( give_field_is_required( 'card_zip', $form_id ) ? ' required' : '' ); ?>"
					placeholder="<?php _e( 'Zip / Postal Code', 'give-square' ); ?>"
					value="<?php echo isset( $give_user_info['card_zip'] ) ? $give_user_info['card_zip'] : ''; ?>"
				<?php echo( give_field_is_required( 'card_zip', $form_id ) ? ' required aria-required="true" ' : '' ); ?>
			/>
			<input
					type="hidden"
					id="give-square-card-zip-hidden-<?php echo esc_html( $id_prefix ); ?>"
					name="card_zip"
			/>
		</p>
		<?php
		/**
		 * Fires while rendering credit card billing form, after address fields.
		 *
		 * @since 1.0.0
		 *
		 * @param int $form_id The form ID.
		 */
		do_action( 'give_cc_billing_bottom' );
		?>
	</fieldset>
	<?php
	echo ob_get_clean();
}

add_action( 'give_square_after_cc_fields', 'give_square_default_cc_address_fields', 10, 2 );

/**
 * Generate Card Nonce.
 *
 * @since 2.6.0
 *
 * @return string
 */
function give_square_generate_card_nonce() {

	$card_nonce = '';

	if ( give_is_test_mode() ) {
		$card_nonce = '';
	}

	return $card_nonce;
}

/**
 * Prepare Donation Amount.
 *
 * @param float $amount Donation Amount.
 *
 * @since 2.6.0
 *
 * @return \SquareConnect\Model\Money
 */
function give_square_prepare_donation_amount( $amount ) {

	// Bailout, if donation amount is invalid.
	if ( ! is_numeric( $amount ) || empty( $amount ) ) {
		give_set_error( 'invalid_donation_amount', __( 'Invalid Donation Amount. Please try again', 'give-square' ) );
		give_square_send_back_to_checkout();
	}

	$currency = give_get_currency();
	$money    = new SquareConnect\Model\Money();

	$is_zero_decimal_currency = give_is_zero_based_currency( $currency );

	// If not zero decimal currency then multiple the amount with 100 to convert it to sub-units.
	if ( ! $is_zero_decimal_currency ) {
		$amount = $amount * 100;
	}

	$money->setAmount( $amount )->setCurrency( $currency );

	return $money;
}

/**
 * Get Application ID.
 *
 * @since 2.6.0
 *
 * @return mixed
 */
function give_square_get_application_id() {

	$application_id = give_get_option( 'give_square_live_application_id' );

	if ( give_is_test_mode() ) {
		$application_id = give_get_option( 'give_square_sandbox_application_id' );
	}

	return $application_id;

}

/**
 * Get Location ID.
 *
 * @since 2.6.0
 *
 * @return mixed
 */
function give_square_get_location_id() {

	$location_id = give_get_option( 'give_square_live_location_id' );

	// If Square is connected via OAuth api, the return location id from business location.
	if ( give_square_is_connected() ) {
		$location_id = give_get_option( 'give_square_business_location' );
	} elseif ( give_is_test_mode() ) {
		$location_id = give_get_option( 'give_square_sandbox_location_id' );
	}

	return $location_id;

}

/**
 * This function is a wrapper to use simply the function for checkout page redirection.
 *
 * @since 2.6.0
 *
 * @param array $args List of arguments.
 *
 * @return void
 */
function give_square_send_back_to_checkout( $args = array() ) {

	$defaults = array(
		'payment-mode' => 'square',
	);

	$args = wp_parse_args( $args, $defaults );

	give_send_back_to_checkout( $args );
}

/**
 * This function will check whether Square is connected or not?
 *
 * @since 2.6.0
 *
 * @return bool
 */
function give_square_is_connected() {

	return give_get_option( 'give_square_is_connected', false );

}

/**
 * This function is used to set default Square configuration before making any API calls to Square.
 *
 * @since 2.6.0
 *
 * @return \SquareConnect\ApiClient
 */
function give_square_set_default_configuration() {

	// Set the Access Token prior to any API calls.
	$api_configuration = new \SquareConnect\Configuration();
	$api_configuration->setHost( give_square_get_host_url() );
	$api_configuration->setAccessToken( give_square_get_access_token() );

	// Return API client for authorizing API calls.
	return new \SquareConnect\ApiClient( $api_configuration );
}

/**
 * This function will help to get list of business locations.
 *
 * @since 2.6.0
 *
 * @return array
 */
function give_square_get_business_locations() {

	// Get locations cache.
	$locations = Give_Cache::get( 'give_cache_square_locations_list' );

	if ( give_square_is_connected() && false === $locations ) {

		$locations = array(
			'' => __( 'Select a Location', 'give-square' ),
		);

		try {

			$api_client     = give_square_set_default_configuration();
			$location_api   = new \SquareConnect\Api\LocationsApi( $api_client );
			$locations_list = $location_api->listLocations()->getLocations();

			foreach ( $locations_list as $location ) {

				// Add location to list only if credit card processing is enabled and status is active.
				if (
					is_array( $location->getCapabilities() ) &&
					'CREDIT_CARD_PROCESSING' === $location->getCapabilities()[0] &&
					'ACTIVE' === $location->getStatus()
				) {
					$locations[ $location->getId() ] = $location->getName();
				}
			}

			// Set locations cache.
			Give_Cache::set( 'give_cache_square_locations_list', $locations );

		} catch ( Exception $e ) {
			give_record_gateway_error(
				__( 'Square Location Error', 'give-square' ),
				sprintf(
					/* translators: 1. Exception Message. */
					__( 'Unable to fetch locations from Square Payment Gateway. Details: %1$s', 'give-square' ),
					$e->getMessage()
				)
			);
		}
	}

	return $locations;
}

/**
 * This function is used to check whether manual api keys are enabled or not.
 *
 * @since 2.6.0
 *
 * @return bool
 */
function give_square_is_manual_api_keys_enabled() {
	return give_is_setting_enabled( give_get_option( 'square_api_keys', 'disabled' ) );
}

/**
 * Check if notice dismissed by admin user or not.
 *
 * @since 2.6.0
 *
 * @return bool
 */
function give_square_is_connect_notice_dismissed() {

	$current_user        = wp_get_current_user();
	$is_notice_dismissed = false;

	if ( get_transient( "give_hide_square_connect_notice_{$current_user->ID}" ) ) {
		$is_notice_dismissed = true;
	}

	return $is_notice_dismissed;
}

/**
 * This function prepares the square connect button for reusability.
 *
 * @since 2.6.0
 *
 * @return mixed
 */
function give_square_connect_button() {

	// Prepare Square Connect URL.
	$connect_url = add_query_arg(
		array(
			'action'     => 'connect',
			'return_uri' => site_url(),
		),
		'https://connect.givewp.com/square/connect.php'
	);

	$connect_button = sprintf(
		'<a href="%1$s" id="give-square-connect-btn" class="give-square-btn" title="%2$s"><img src="%3$s" alt="%4$s" /><span>%5$s</span></a>',
		esc_url_raw( $connect_url ),
		__( 'Connect your site with Square\'s easy onboarding process.', 'give-square' ),
		esc_url_raw( GIVE_PLUGIN_URL . 'assets/dist/images/admin/square.svg' ),
		__( 'Square Payment Gateway.', 'give-square' ),
		__( 'Connect to Square', 'give-square' )
	);

	return apply_filters( 'give_square_connect_button', $connect_button );

}

/**
 * This function will be used to set the input styles.
 *
 * @since 2.6.0
 *
 * @return array
 */
function give_square_get_input_styles() {

    // Default Styles.
    $default_styles = wp_json_encode(
	    array(
		    'fontSize' => '1.1em',
	    )
    );

    // Get input styles from DB.
	$input_styles = give_get_option(
		'square_styles',
		$default_styles
	);

	// If input styles are empty then use default styles.
	if ( '{}' === $input_styles ) {
		$input_styles = $default_styles;
    }

	/**
	 * This filter will be used to modify the input styles array.
	 *
	 * @param array $input_styles List of input styles accepted by Square.
	 *
	 * @since 2.6.0
	 */
	return apply_filters( 'give_square_get_input_styles', json_decode( $input_styles ) );
}

/**
 * This function will check whether we need to collect billing details or not.
 *
 * @since 2.6.0
 *
 * @return bool
 */
function give_square_can_collect_billing_details() {

    return give_is_setting_enabled( give_get_option( 'give_square_collect_billing_details' ) );

}

/**
 * This function is used to get the unique key to encrypt/decrypt the plain text into AES encrypted text and vice versa.
 *
 * @since 2.6.0
 *
 * @return string
 */
function give_square_get_unique_key() {
    return '|7vC>PawiP IAUY7Hh@Ts-^srIjpD)Gw29p(?Ni-%YPy=[nFfdFDt(Y-#E&`PEco'; // It should be unique random key.
}

/**
 * This function is used to encrypt the plain text to AES encrypted string.
 *
 * @param string $text Plain Text.
 *
 * @since 2.6.0
 *
 * @return string
 */
function give_square_encrypt_string( $text ) {

    $key = give_square_get_unique_key();

    // Remove the base64 encoding from our key.
	$encryption_key = base64_decode( $key );

	// Generate an initialization vector.
	$iv = openssl_random_pseudo_bytes( openssl_cipher_iv_length( 'aes-256-cbc' ) );

	// Encrypt the data using AES 256 encryption in CBC mode using our encryption key and initialization vector.
	$encrypted = openssl_encrypt( $text, 'aes-256-cbc', $encryption_key, 0, $iv );

	// The $iv is just as important as the key for decrypting, so save it with our encrypted data using a unique separator (::)
	return base64_encode( $encrypted . '::' . $iv );

}

/**
 * This function is used to decrypt the AES encrypted string to plain text.
 *
 * @param string $text Encrypted Text.
 *
 * @since 2.6.0
 *
 * @return string
 */
function give_square_decrypt_string( $text ) {
	if( ! $text ) {
		return '';
	}

	$key = give_square_get_unique_key();

    // Remove the base64 encoding from our key.
	$encryption_key = base64_decode( $key );

	// To decrypt, split the encrypted data from our IV - our unique separator used was "::".
	list( $encrypted_data, $iv ) = explode( '::', base64_decode( $text ), 2 );

	return openssl_decrypt( $encrypted_data, 'aes-256-cbc', $encryption_key, 0, $iv );

}

/**
 * This function is used to get the access token provided by Square.
 *
 * @since 2.6.0
 *
 * @return string
 */
function give_square_get_access_token() {

	$access_token = give_square_decrypt_string( give_get_option( 'give_square_live_access_token' ) );

	// If Test Mode enabled & Square OAuth API not connect, use sandbox access token.
	if ( give_is_test_mode() && ! give_square_is_connected() ) {
		$access_token = give_square_decrypt_string( give_get_option( 'give_square_sandbox_access_token' ) );
	}

	return $access_token;
}

/**
 * This function is used to get host.
 *
 * @since 2.6.0
 *
 * @return string
 */
function give_square_get_host() {

	$host = 'squareup.com';

	// For test mode.
	if ( give_is_test_mode() ) {
		$host = 'squareupsandbox.com';
	}

	return $host;
}

/**
 * This function is used to get host URL.
 *
 * @since 2.6.0
 *
 * @return string
 */
function give_square_get_host_url() {

	$host = give_square_get_host();

	return "https://connect.{$host}";
}