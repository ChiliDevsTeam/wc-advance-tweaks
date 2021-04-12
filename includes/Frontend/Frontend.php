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

use WC_Coupon;

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
		add_filter( 'gettext', [ $this, 'change_update_cart_text' ], 20, 3 );
		add_action( 'woocommerce_cart_coupon', [ $this, 'custom_woocommerce_empty_cart_button' ] );
		add_action( 'init', [ $this, 'custom_woocommerce_empty_cart_action' ] );
		add_filter( 'woocommerce_add_to_cart_redirect', [ $this, 'wc_get_cart_url' ], 99 );
		add_filter( 'woocommerce_cart_item_quantity', [ $this, 'wc_hide_quantity' ], 12, 3 );
		add_action( 'woocommerce_cart_collaterals', [ $this, 'woocommerce_coupon_show_field' ] );
		add_action( 'woocommerce_account_menu_items', [ $this, 'woocommerce_my_account_menu' ], 10, 2 );

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
	 * update cart on cart button change text
	 */
	public function change_update_cart_text( $translated, $text, $domain ) {
		if( is_cart() && $translated == 'Update cart' ){
			$translated = get_option( 'wc_advance_tweaks_update_cart_button' );
		}
		return $translated;
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

	/**
	 * add to cart redirect function
	 */
	public function wc_get_cart_url( $url ) {
		return get_permalink(get_option( 'wc_advance_tweaks_redirect_button') );
	}

	/**
	 * remove quantity option while checked
	 */
	 public function wc_hide_quantity( $product_quantity, $cart_item_key, $cart_item ) {
		$hide = get_option( 'wc_advance_tweaks_hide_quantity_checkbox' );
		if ( $hide === 'yes' ) {
			return $cart_item['quantity'];
		}
		return $product_quantity;
	}

	/**
	 * dislay available coupon detail on cart page
	 */
	public function woocommerce_coupon_show_field() {
		$coupons = get_option( 'wc_advance_tweaks_coupon_select_field' );
		echo '<div class="wc-available-coupons">';
		echo "<b>" . 'You can use the following discount codes on your cart' . "</b>";
        foreach ( $coupons as $coupon ) {
            $coupon = new WC_Coupon( $coupon );
			?>
				<div classe="copon-box">
					<br><p><?php echo 'Code: ' . "<b>" . $coupon->get_code() . "</b>"; ?></p>
					<p><?php echo 'Amount: ' . $coupon->get_amount(); ?></p>
					<p><?php echo $coupon->get_description(); ?></p>
				</div>
			<?php
		}
	}

	/**
	 *hide menu from my-account for customer
	 */
	public function woocommerce_my_account_menu( $menu_links ) {
		$menus = get_option('wc_advance_tweaks_coupon_select_field');
		foreach( $menus as $menu ) {
			unset( $menu_links[$menu] );
		}
		return $menu_links;
	}
}