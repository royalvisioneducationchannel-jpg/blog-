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

/* ---------------------------------------------------------------
   International checkout
   Pakistan orders go through the COD form (rvt-cod-order.php and
   rvt-cod-cart.php), which redirects the WooCommerce checkout. A
   visitor who follows an "Ordering from outside Pakistan?" link gets
   the standard WooCommerce checkout instead, where outside Pakistan
   only the card (Payoneer link) method is offered.
--------------------------------------------------------------- */

/** Whether this visitor chose the international checkout. */
function rvt_intl_active() {
	return function_exists( 'WC' ) && WC()->session && WC()->session->get( 'rvt_intl' );
}

// ?rvt_intl=1 marks the session, before WooCommerce handles ?add-to-cart (which may redirect).
add_action( 'wp_loaded', function () {
	if ( empty( $_GET['rvt_intl'] ) || ! function_exists( 'WC' ) || ! WC()->session ) { // phpcs:ignore WordPress.Security.NonceVerification
		return;
	}
	if ( ! WC()->session->has_session() ) {
		WC()->session->set_customer_session_cookie( true );
	}
	WC()->session->set( 'rvt_intl', 1 );
}, 5 );

// An international "add to cart" link lands on the checkout, not the cart page.
add_filter( 'woocommerce_add_to_cart_redirect', function ( $url ) {
	return empty( $_GET['rvt_intl'] ) ? $url : add_query_arg( 'rvt_intl', 1, wc_get_checkout_url() ); // phpcs:ignore WordPress.Security.NonceVerification
}, 20 );

// Let the international visitor through to the WooCommerce checkout.
add_action( 'template_redirect', function () {
	if ( rvt_intl_active() ) {
		remove_action( 'template_redirect', 'rvt_cod_checkout_redirect', 5 );
	}
}, 4 );

// No country preselected on the international checkout.
add_filter( 'default_checkout_billing_country', function ( $country ) {
	return rvt_intl_active() ? '' : $country;
} );

// Outside Pakistan: card only. Cash on Delivery and JazzCash stay Pakistan-only.
add_filter( 'woocommerce_available_payment_gateways', function ( $gateways ) {
	if ( is_admin() || ! WC()->customer ) {
		return $gateways;
	}
	$country = WC()->customer->get_billing_country();
	if ( '' !== $country && 'PK' !== $country ) {
		unset( $gateways['cod'], $gateways['bacs'] );
	}
	return $gateways;
} );

// The order is placed; later visits use the Pakistan form again.
add_action( 'woocommerce_thankyou', function () {
	if ( rvt_intl_active() ) {
		WC()->session->set( 'rvt_intl', null );
	}
} );

// "Ordering from outside Pakistan?" link above the COD order form.
add_filter( 'do_shortcode_tag', function ( $output, $tag ) {
	if ( 'rvt_order_form' !== $tag || ! function_exists( 'wc_get_checkout_url' ) ) {
		return $output;
	}
	$url = add_query_arg( 'rvt_intl', 1, wc_get_checkout_url() );
	$pid = isset( $_GET['rvt_pid'] ) ? absint( $_GET['rvt_pid'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
	if ( $pid ) {
		$product = wc_get_product( $pid );
		if ( ! $product || ! $product->is_purchasable() ) {
			return $output;
		}
		// Simple products go straight into the cart; others go back to the product page to pick options.
		$url = $product->is_type( 'simple' )
			? add_query_arg( array( 'add-to-cart' => $pid, 'rvt_intl' => 1 ), wc_get_checkout_url() )
			: add_query_arg( 'rvt_intl', 1, $product->get_permalink() );
	}
	$note = '<p class="rvt-intl-note" style="margin:0 0 14px;padding:12px 14px;border:1px solid #E4DDF5;border-radius:10px;background:#F7F4FE;font-size:14.5px;line-height:1.5;color:#1A1A1A">'
		. esc_html__( 'Ordering from outside Pakistan (UK, USA, Canada, Australia)?', 'rvt' ) . ' '
		. '<a href="' . esc_url( $url ) . '" style="color:#5B24D6;font-weight:600;text-decoration:underline">' . esc_html__( 'Pay by Visa / Mastercard here', 'rvt' ) . '</a></p>';
	return $note . $output;
}, 10, 2 );
