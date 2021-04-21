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
		add_action( 'woocommerce_checkout_before_customer_details', [ $this, 'wc_coupon_show_checkout_checkbox' ] );
		add_action( 'woocommerce_account_menu_items', [ $this, 'woocommerce_hide_my_account_menu' ], 10, 2 );
		add_filter( 'woocommerce_account_menu_items', [ $this, 'rename_my_account' ] , 9999 );
		add_filter( 'woocommerce_endpoint_order-received_title', [ $this, 'thank_you_title_edited' ] );
		add_filter( 'woocommerce_thankyou_order_received_text', [ $this,'thank_you_text_edited' ], 20, 2 );
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
		echo "<p><strong>" . 'You can use the following discount codes on your cart' . "</strong><p>";
        ?>
			<div class="flex-container" style="display:flex";>
		<?php
		foreach ( $coupons as $coupon ) {
            $coupon = new WC_Coupon( $coupon );
			?>
				<div class="copon-box" style="background-color:#85C1E9; display:inline-block; width:350px; margin-right:20px; padding:10px; border-radius:5px">
					<p><?php echo 'Coupon: ' . '<strong style="color:white; background-color:orange;padding:5px; border-radius:20%">' . $coupon->get_code() . '</strong>'; ?></p>
					<p><?php echo 'Amount: ' . $coupon->get_amount(); ?></p>
					<p><?php echo $coupon->get_description(); ?></p>
				</div>
				</br>
			<?php
		}
		?>
			</div>
		<?php
	}

	/**
	 * checkbox for woocommerce checkout page to show coupon
	 */
	public function wc_coupon_show_checkout_checkbox() {
		$showcouponcheckout = get_option('wc_advance_tweaks_coupon_checkoutpage_checkbox');
		if ( $showcouponcheckout == 'yes' ) {
			$this->woocommerce_coupon_show_field();
		}
	}

	/**
	 *hide menu from my-account for customer
	 */
	public function woocommerce_hide_my_account_menu( $menu_links ) {
		$menus = get_option('wc_advance_tweaks_coupon_select_field');
		foreach( $menus as $menu ) {
			unset( $menu_links[$menu] );
		}
		return $menu_links;
	}

	/**
	 *
	 */
	public function rename_my_account( $items ) {
		$dashboard_rename      = get_option( 'wc_advance_tweaks_dashboard_rename', 'dashboard' );
		$orders_rename         = get_option( 'wc_advance_tweaks_orders_rename' );
		$downloads_rename      = get_option( 'wc_advance_tweaks_downloads_rename' );
		$editaddress_rename    = get_option( 'wc_advance_tweaks_addresses_rename' );
		$paymentmethods_rename = get_option( 'wc_advance_tweaks_paymentmethods_rename' );
		$editaccount_rename    = get_option( 'wc_advance_tweaks_accountdetails_rename' );
		$logout_rename         = get_option('wc_advance_tweaks_logout_rename');

		$items['dashboard']       = ! empty( $dashboard_rename ) ? $dashboard_rename : __( 'Dashboard', 'wc-advance-tweaks' );
		$items['orders']          = ! empty( $orders_rename ) ? $orders_rename : __( 'Orders', 'wc-advance-tweaks' );
		$items['downloads']       = ! empty( $downloads_rename ) ? $downloads_rename : __( 'Downloads', 'wc-advance-tweaks' );
		$items['edit-address']    = ! empty( $editaddress_rename ) ? $editaddress_rename : __( 'Addresses', 'wc-advance-tweaks' );
		$items['payment-method']  = ! empty( $paymentmethods_rename ) ? $paymentmethods_rename : __( 'Payment methods', 'wc-advance-tweaks' );
		$items['edit-account']    = ! empty( $editaccount_rename ) ? $editaccount_rename : __( 'Account details', 'wc-advance-tweaks' );
		$items['customer-logout'] = ! empty( $logout_rename ) ? $logout_rename : __( 'Logout', 'wc-advance-tweaks' );

   		return $items;
	}

	/**
	 * thank you title changed text
	 */
	public function thank_you_title_edited( $old_title ) {
		$thankyou_title = get_option('wc_advance_tweaks_update_thank_you_title');
		return ! empty( $thankyou_title ) ? $thankyou_title : __( 'Order Received', 'wc-advance-tweaks' );
	}

	/**
	 * thank you page changed text
	 */
	public function thank_you_text_edited( $thank_you_title, $order ){
		$thankyou_text = get_option('wc_advance_tweaks_update_thank_you_text');
		return ! empty( $thankyou_text ) ? $thankyou_text : __( 'Thank You. Your order has been received.', 'wc-advance-tweaks' );

	}


}