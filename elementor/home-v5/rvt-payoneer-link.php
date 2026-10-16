<?php
/**
 * Royal Vision - "Visa / Mastercard (Payoneer link)" payment method for international orders.
 *
 * An offline WooCommerce payment method. The customer places the order, it waits
 * "On hold", and the shop sends a Payoneer "Request a Payment" link for the order
 * total. The customer pays by card on Payoneer; the shop then marks the order
 * Processing and ships. Shown only when the billing country is outside Pakistan,
 * so the Pakistan checkout (COD, JazzCash) is unchanged.
 *
 * Settings: WooCommerce -> Settings -> Payments -> "Visa / Mastercard (Payoneer link)".
 */

defined( 'ABSPATH' ) || exit;

// The sandbox loads before WooCommerce, so the class is defined once WooCommerce is in memory.
$rvt_pl_define = function () {
	if ( ! class_exists( 'WC_Payment_Gateway' ) || class_exists( 'RVT_Payoneer_Link_Gateway' ) ) {
		return;
	}

	class RVT_Payoneer_Link_Gateway extends WC_Payment_Gateway {

		/** @var string Text for the thank-you page and customer email. */
		public $instructions = '';

		public function __construct() {
			$this->id                 = 'rvt_payoneer_link';
			$this->has_fields         = false;
			$this->method_title       = __( 'Visa / Mastercard (Payoneer link)', 'rvt' );
			$this->method_description = __( 'International orders only. The order waits On hold; send the customer a Payoneer "Request a Payment" link for the order total, then mark the order Processing once paid.', 'rvt' );

			$this->init_form_fields();
			$this->init_settings();

			$this->title        = $this->get_option( 'title' );
			$this->description  = $this->get_option( 'description' );
			$this->instructions = $this->get_option( 'instructions' );

			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
			add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
			add_action( 'woocommerce_email_before_order_table', array( $this, 'email_instructions' ), 10, 3 );
		}

		public function init_form_fields() {
			$this->form_fields = array(
				'enabled'      => array(
					'title'   => __( 'Enable', 'rvt' ),
					'type'    => 'checkbox',
					'label'   => __( 'Offer card payment by Payoneer link to customers outside Pakistan', 'rvt' ),
					'default' => 'yes',
				),
				'title'        => array(
					'title'   => __( 'Title', 'rvt' ),
					'type'    => 'text',
					'default' => __( 'Visa / Mastercard (secure Payoneer link)', 'rvt' ),
				),
				'description'  => array(
					'title'   => __( 'Checkout text', 'rvt' ),
					'type'    => 'textarea',
					'default' => __( 'Place your order and we will email you a secure Payoneer payment link within a few hours. Pay with your Visa or Mastercard; your order ships as soon as payment is received.', 'rvt' ),
				),
				'instructions' => array(
					'title'   => __( 'Thank-you page and email text', 'rvt' ),
					'type'    => 'textarea',
					'default' => __( 'Thank you! Your order is reserved. We will email you a secure Payoneer payment link for the order total within a few hours (WhatsApp +92 322 778 2932 for questions). Your order ships as soon as payment is received.', 'rvt' ),
				),
			);
		}

		/** Only for billing addresses outside Pakistan. */
		public function is_available() {
			if ( ! parent::is_available() ) {
				return false;
			}
			$country = ( WC()->customer ) ? WC()->customer->get_billing_country() : '';
			return '' !== $country && 'PK' !== $country;
		}

		public function process_payment( $order_id ) {
			$order = wc_get_order( $order_id );
			// On hold reserves stock and sends the shop the New Order email.
			$order->update_status( 'on-hold', __( 'Awaiting card payment: send a Payoneer payment link for the order total.', 'rvt' ) );
			WC()->cart->empty_cart();
			return array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $order ),
			);
		}

		public function thankyou_page() {
			if ( $this->instructions ) {
				echo wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
			}
		}

		public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
			if ( $sent_to_admin || ! $this->instructions || $this->id !== $order->get_payment_method() || ! $order->has_status( 'on-hold' ) ) {
				return;
			}
			echo $plain_text ? esc_html( $this->instructions ) . PHP_EOL : wp_kses_post( wpautop( wptexturize( $this->instructions ) ) );
		}
	}
};
if ( did_action( 'plugins_loaded' ) ) {
	$rvt_pl_define();
} else {
	add_action( 'plugins_loaded', $rvt_pl_define, 11 );
}

add_filter( 'woocommerce_payment_gateways', function ( $gateways ) {
	if ( class_exists( 'RVT_Payoneer_Link_Gateway' ) ) {
		$gateways[] = 'RVT_Payoneer_Link_Gateway';
	}
	return $gateways;
} );
