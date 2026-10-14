<?php
/**
 * Royal Vision - per-product sale timer for Elementor.
 *
 * Adds two native Elementor pieces:
 * - "RV Sale Timer" widget: a "Sale ends in 17d 08h 14m 11s" bar.
 * - "RV Product Discount" dynamic tag: prints "-18%" for a product on sale.
 * - "RV Product Title" and "RV Product Price" dynamic tags: the product's live
 *   name and price (crossed-out regular price + sale price while on sale).
 *   Elementor Pro's own WooCommerce tags need a licence, so these are used instead.
 *
 * Both read the product's own WooCommerce data. The timer counts down to the
 * product's "Sale price dates -> to" (Edit product -> General -> Schedule), so
 * every product's timer is set from its own edit screen. When a product has no
 * end date, or the sale is over, the timer is not shown.
 */

defined( 'ABSPATH' ) || exit;

/**
 * Product for a widget/tag: the given ID, or the current post inside a loop.
 *
 * @param mixed $id Product ID from the control, may be empty.
 * @return WC_Product|null
 */
function rvt_st_product( $id ) {
	if ( ! function_exists( 'wc_get_product' ) ) {
		return null;
	}
	$id = absint( $id );
	if ( ! $id ) {
		$id = get_the_ID();
	}
	$product = $id ? wc_get_product( $id ) : null;
	return $product ? $product : null;
}

/**
 * Unix time the product's sale ends, or 0 when there is no running sale with an end date.
 *
 * @param WC_Product|null $product Product.
 * @return int
 */
function rvt_st_end( $product ) {
	if ( ! $product || ! $product->is_on_sale() ) {
		return 0;
	}
	$to = $product->get_date_on_sale_to();
	return ( $to && $to->getTimestamp() > time() ) ? $to->getTimestamp() : 0;
}

/**
 * "17d 08h 14m 11s" for a number of seconds.
 *
 * @param int $left Seconds left.
 * @return string
 */
function rvt_st_format( $left ) {
	$left = max( 0, (int) $left );
	return sprintf( '%dd %02dh %02dm %02ds', intdiv( $left, 86400 ), intdiv( $left % 86400, 3600 ), intdiv( $left % 3600, 60 ), $left % 60 );
}

add_action( 'elementor/widgets/register', function ( $widgets_manager ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	class RVT_Sale_Timer_Widget extends \Elementor\Widget_Base {

		public function get_name() {
			return 'rv-sale-timer';
		}

		public function get_title() {
			return esc_html__( 'RV Sale Timer', 'rvt' );
		}

		public function get_icon() {
			return 'eicon-countdown';
		}

		public function get_categories() {
			return array( 'woocommerce-elements', 'general' );
		}

		public function get_keywords() {
			return array( 'sale', 'timer', 'countdown', 'product', 'woocommerce' );
		}

		protected function register_controls() {
			$this->start_controls_section( 'content', array( 'label' => esc_html__( 'Sale timer', 'rvt' ) ) );
			$this->add_control( 'product_id', array(
				'label'       => esc_html__( 'Product ID', 'rvt' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Leave empty inside a product loop or on a product page. The end time comes from the product\'s sale schedule.', 'rvt' ),
				'dynamic'     => array( 'active' => true ),
			) );
			$this->add_control( 'label', array(
				'label'   => esc_html__( 'Label', 'rvt' ),
				'type'    => \Elementor\Controls_Manager::TEXT,
				'default' => esc_html__( 'Sale ends in', 'rvt' ),
			) );
			$this->end_controls_section();

			$this->start_controls_section( 'style', array( 'label' => esc_html__( 'Bar', 'rvt' ), 'tab' => \Elementor\Controls_Manager::TAB_STYLE ) );
			$this->add_control( 'bar_bg', array(
				'label'     => esc_html__( 'Background', 'rvt' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#1A1A1A',
				'selectors' => array( '{{WRAPPER}} .rv-sale-timer' => 'background-color: {{VALUE}};' ),
			) );
			$this->add_control( 'label_color', array(
				'label'     => esc_html__( 'Label colour', 'rvt' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#FFFFFF',
				'selectors' => array( '{{WRAPPER}} .rv-sale-timer' => 'color: {{VALUE}};' ),
			) );
			$this->add_control( 'time_color', array(
				'label'     => esc_html__( 'Time colour', 'rvt' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'default'   => '#F5B82E',
				'selectors' => array( '{{WRAPPER}} .rv-st-time, {{WRAPPER}} .rv-sale-timer svg' => 'color: {{VALUE}};' ),
			) );
			$this->add_group_control( \Elementor\Group_Control_Typography::get_type(), array(
				'name'     => 'typography',
				'selector' => '{{WRAPPER}} .rv-sale-timer',
			) );
			$this->add_responsive_control( 'padding', array(
				'label'      => esc_html__( 'Padding', 'rvt' ),
				'type'       => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .rv-sale-timer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
			) );
			$this->add_control( 'radius', array(
				'label'      => esc_html__( 'Corner radius', 'rvt' ),
				'type'       => \Elementor\Controls_Manager::SLIDER,
				'size_units' => array( 'px' ),
				'selectors'  => array( '{{WRAPPER}} .rv-sale-timer' => 'border-radius: {{SIZE}}{{UNIT}};' ),
			) );
			$this->end_controls_section();
		}

		protected function render() {
			static $assets_printed = false;

			$settings = $this->get_settings_for_display();
			$product  = rvt_st_product( $settings['product_id'] ?? '' );
			$end      = rvt_st_end( $product );

			if ( ! $end ) {
				if ( \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
					echo '<div class="rv-sale-timer rv-st-empty">' . esc_html__( 'No sale end date on this product', 'rvt' ) . '</div>';
				}
				return;
			}

			if ( ! $assets_printed ) {
				$assets_printed = true;
				?>
				<style>
				.rv-sale-timer{display:flex;align-items:center;justify-content:center;gap:8px;padding:10px 12px;background:#1A1A1A;color:#FFFFFF;font-size:13px;font-weight:700;line-height:1.2;letter-spacing:.06em;text-transform:uppercase;white-space:nowrap;font-variant-numeric:tabular-nums}
				.rv-sale-timer svg{width:12px;height:12px;flex:none;color:#F5B82E;fill:currentColor}
				.rv-st-time{color:#F5B82E;text-transform:none;letter-spacing:.02em}
				.rv-st-empty{opacity:.6}
				</style>
				<script>
				(function(){if(window.rvSaleTimer)return;window.rvSaleTimer=1;function p(n){return(n<10?'0':'')+n}
				function tick(){var now=Date.now()/1000;document.querySelectorAll('.rv-sale-timer[data-end]').forEach(function(el){var l=Math.floor(el.getAttribute('data-end')-now);
				if(l<=0){el.style.display='none';return}var o=el.querySelector('.rv-st-time');if(o){o.textContent=Math.floor(l/86400)+'d '+p(Math.floor(l%86400/3600))+'h '+p(Math.floor(l%3600/60))+'m '+p(l%60)+'s'}})}
				if(document.readyState!=='loading')tick();else document.addEventListener('DOMContentLoaded',tick);setInterval(tick,1000)})();
				</script>
				<?php
			}

			$label = $settings['label'] ?? '';
			printf(
				'<div class="rv-sale-timer" data-end="%1$d" role="timer" aria-label="%2$s"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 4 14h7l-1 8 9-12h-7z"/></svg><span class="rv-st-label">%3$s</span><span class="rv-st-time">%4$s</span></div>',
				(int) $end,
				esc_attr( $label . ' ' . wp_date( 'j M Y, g:i a', $end ) ),
				esc_html( $label ),
				esc_html( rvt_st_format( $end - time() ) )
			);
		}
	}

	$widgets_manager->register( new RVT_Sale_Timer_Widget() );
} );

add_action( 'elementor/dynamic_tags/register', function ( $dynamic_tags ) {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	class RVT_Product_Discount_Tag extends \Elementor\Core\DynamicTags\Tag {

		public function get_name() {
			return 'rv-product-discount';
		}

		public function get_title() {
			return esc_html__( 'RV Product Discount', 'rvt' );
		}

		public function get_group() {
			return 'woocommerce';
		}

		public function get_categories() {
			return array( \Elementor\Modules\DynamicTags\Module::TEXT_CATEGORY );
		}

		protected function register_controls() {
			$this->add_control( 'product_id', array(
				'label'       => esc_html__( 'Product ID', 'rvt' ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'description' => esc_html__( 'Leave empty inside a product loop.', 'rvt' ),
			) );
		}

		public function render() {
			$product = rvt_st_product( $this->get_settings( 'product_id' ) );
			if ( ! $product || ! $product->is_on_sale() ) {
				return;
			}
			$regular = (float) $product->get_regular_price();
			$sale    = (float) $product->get_sale_price();
			if ( $regular <= 0 || $sale <= 0 || $sale >= $regular ) {
				return;
			}
			echo esc_html( '-' . round( ( 1 - $sale / $regular ) * 100 ) . '%' );
		}
	}

	class RVT_Product_Title_Tag extends RVT_Product_Discount_Tag {

		public function get_name() {
			return 'rv-product-title';
		}

		public function get_title() {
			return esc_html__( 'RV Product Title', 'rvt' );
		}

		public function render() {
			$product = rvt_st_product( $this->get_settings( 'product_id' ) );
			if ( $product ) {
				echo esc_html( $product->get_name() );
			}
		}
	}

	class RVT_Product_Price_Tag extends RVT_Product_Discount_Tag {

		public function get_name() {
			return 'rv-product-price';
		}

		public function get_title() {
			return esc_html__( 'RV Product Price', 'rvt' );
		}

		public function render() {
			$product = rvt_st_product( $this->get_settings( 'product_id' ) );
			if ( $product ) {
				echo wp_kses_post( $product->get_price_html() );
			}
		}
	}

	$dynamic_tags->register( new RVT_Product_Discount_Tag() );
	$dynamic_tags->register( new RVT_Product_Title_Tag() );
	$dynamic_tags->register( new RVT_Product_Price_Tag() );
} );
