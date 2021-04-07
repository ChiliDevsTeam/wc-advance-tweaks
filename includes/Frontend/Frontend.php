<?php
/**
 * Frontend class
 *
 * Manage all admin related functionality
 *
 * @package ChiliDevs\WCAdvanceTweaks
 */

declare(strict_types=1);

namespace ChiliDevs\WCAdvanceTweaks\Frontend;

/**
 * Frontend class.
 *
 * @package ChiliDevs\WCAdvanceTweaks\Admin
 */
class Frontend {

	/**
	 * Load automatically when class initiate
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'woocommerce_product_single_add_to_cart_text', [ $this, 'change_add_to_cart_single' ] );
		add_filter( 'woocommerce_product_add_to_cart_text', [ $this, 'change_add_to_cart_shop' ] );
		add_action( 'woocommerce_cart_coupon', [ $this, 'custom_woocommerce_empty_cart_button' ] );
		add_action( 'init', [ $this, 'custom_woocommerce_empty_cart_action' ] );
		add_filter( 'woocommerce_add_to_cart_redirect', [ $this, 'wc_get_cart_url' ], 99 );
	}

	/**
	 * update add to cart for single page
	*/
	public function change_add_to_cart_single() {
		$add_to_cart= get_option( 'wc_advance_tweaks_add_to_cart_button' );
		return __( $add_to_cart, 'woocommerce' );
	}

	/**
	* update add to cart button for shop page
	*/
	public function change_add_to_cart_shop() {
		global $product;
		$add_to_cart= get_option( 'wc_advance_tweaks_add_to_cart_button' );
		$variable_cart= get_option( 'wc_advance_tweaks_variation_button' );
		$product_type = $product->get_type();
		switch ( $product_type ) {
			case 'variable':
				return __($variable_cart, 'woocommerce');
			default:
				return __( $add_to_cart, 'woocommerce' );
		}
	}

	/**
	 * create empty cart button
	 */
	public function custom_woocommerce_empty_cart_button() {
		$is_enabled = get_option( 'wc_advance_tweaks_empty_button_checkbox' );
		if ( $is_enabled === 'yes' ) {
		echo '<a href="' . esc_url( add_query_arg( 'empty_cart', 'yes' ) ) . '
		" class="button" title="' . esc_attr( 'Empty Cart', 'woocommerce' ) . '">' .
		esc_html( 'Empty Cart', 'woocommerce' ) . '</a>';
		}
	}

	/**
	 * empty cart button function
	 */
	public function custom_woocommerce_empty_cart_action() {
		if ( isset( $_GET['empty_cart'] ) ) {
			WC()->cart->empty_cart();
		}
	}

	public function wc_get_cart_url( $url ) {
		return get_permalink(get_option( 'wc_advance_tweaks_redirect_button') );

		//$page_id =
		//var_dump(get_option( 'wc_advance_tweaks_redirect_button' ));
		//var_dump(get_permalink(get_option( 'wc_advance_tweaks_redirect_button') ));
		//return $page_url;
		//var_dump($page_url);
	}
}