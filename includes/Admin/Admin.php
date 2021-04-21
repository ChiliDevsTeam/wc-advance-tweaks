<?php
/**
 * Admin class
 *
 * Manage all admin related functionality
 *
 * @package ChiliDevs\WCAdvanceTweaks
 */

declare(strict_types=1);

namespace ChiliDevs\WCAdvanceTweaks\Admin;

use WC_Coupon;

/**
 * Admin class.
 *
 * @package ChiliDevs\WCAdvanceTweaks\Admin
 */
class Admin {

	/**
	 * Load automatically when class initiate
	 *
	 * @return void
	 */
	public function __construct() {
		add_filter( 'woocommerce_settings_tabs_array', [ $this, 'add_tweak_tab' ], 50 );
		add_action( 'woocommerce_settings_tabs_tweak_tab', [ $this, 'add_settings_tab' ] );
		add_action( 'woocommerce_update_options_tweak_tab', [ $this, 'update_tweak_settings' ] );
		add_filter( 'woocommerce_get_sections_products' , [ $this, 'add_custom_sub_menu' ] );
		add_filter( 'woocommerce_get_settings_products' , [ $this, 'my_account_submenu_hide' ], 10, 2 );
		add_filter( 'woocommerce_get_settings_products' , [ $this, 'rename_my_account_menutab' ], 10, 2 );
	}

	/**
	 * Add a new tab
	 *
	 * @param array $settings_tab Settings array.
	 *
	 * @return $settings_tab
	*/
	public function add_tweak_tab ( $settings_tabs ) {
		$settings_tabs['tweak_tab'] = __( 'Miscellaneous', 'wc-advance-tweaks' );
		return $settings_tabs;
	}

	/**
	 * Add fields on custum tab
	*/
	public function add_settings_tab() {
		woocommerce_admin_fields( $this->get_settings() );
	}

	/**
	 * get all the page title
	 */
	public function get_pages() {
        $pages = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ($pages as $page) {
                $pages_options[$page->ID] = $page->post_title;
            }
        }
        return $pages_options;
    }

	/**
	 * get all the coupon title
	 */
	public function get_coupon() {
		$args = array(
			'posts_per_page'   => -1,
			'orderby'          => 'title',
			'order'            => 'asc',
			'post_type'        => 'shop_coupon',
			'post_status'      => 'publish',
		);
		$coupons = get_posts( $args );
		$coupon_names = array();
		foreach ( $coupons as $coupon ) {
			$coupon = new WC_Coupon( $coupon->ID );
			$coupon_name[$coupon->get_id()] = $coupon->get_code();
		}
		return $coupon_name;
	}

	/**
	 * Add custom fields
	*/
	public function get_settings() {
		$settings = array(
			'section_title' => array(
				'name'     => __( 'Advanced Tweaks', 'wc-advance-tweaks' ),
				'type'     => 'title',
				'desc'     => '',
				'id'       => 'wc_advance_tweaks_section_title'
			),
			'add-to-cart-button-text' => array(
				'name' => __( 'Add to cart button text', 'wc-advance-tweaks' ),
				'type' => 'textarea',
				'desc' => __( 'This will replace woocommerce add to cart button text value', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_add_to_cart_button'
			),
			'variation-button-text' => array(
				'name' => __( 'Vartiation button text', 'wc-advance-tweaks' ),
				'type' => 'textarea',
				'desc' => __( 'This will replace woocommerce variation button text value ', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_variation_button'
			),
			'update-cart-button-text' => array(
				'name' => __( 'Update cart button text', 'wc-advance-tweaks' ),
				'type' => 'textarea',
				'desc' => __( 'This will replace woocommerce update cart button in cart page text value', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_update_cart_button'
			),
			'redirect-to' => array(
				'name'    => __( 'Redirect on clicking add to cart', 'wc-advance-tweaks' ),
				'type'    => 'select',
				'desc'    => __( 'Select preffered page to redirect while customer click add to cart button', 'wc-advance-tweaks' ),
				'id'      => 'wc_advance_tweaks_redirect_button',
				'options' => $this->get_pages(),
			),
			'empty-cart-button-check' => array(
				'name' => __( 'Empty Cart Button', 'wc-advance-tweaks' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this box to show empty cart button', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_empty_button_checkbox'
			),
			'hide-quantity-in-cart-check' => array(
				'name' => __( 'Hide quantity field in cart', 'wc-advance-tweaks' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this box to disallow changing quantity in cart page', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_hide_quantity_checkbox'
			),
			'coupon-show' => array(
				'name'    => __( 'Choose coupons to offer', 'wc-advance-tweaks' ),
				'type'    => 'multiselect',
				'desc'    => __( 'Select the coupons for customers to show', 'wc-advance-tweaks' ),
				'class'   => 'wc-enhanced-select',
				'id'      => 'wc_advance_tweaks_coupon_select_field',
				'options' => $this->get_coupon(),
			),
			'show-coupon-checkoutpage-check' => array(
				'name' => __( 'Show coupon in checkout page', 'wc-advance-tweaks' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this box to allow customers see the coupons in checkout page', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_coupon_checkoutpage_checkbox'
			),
			'update-thank-you-title' => array(
				'name' => __( 'Thank You title', 'wc-advance-tweaks' ),
				'type' => 'textarea',
				'desc' => __( 'This will replace woocommerce thank you page title', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_update_thank_you_title'
			),
			'update-thank-you-text' => array(
				'name' => __( 'Thank You text', 'wc-advance-tweaks' ),
				'type' => 'textarea',
				'desc' => __( 'This will replace woocommerce thank you page text', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_update_thank_you_text'
			),
			'section_end' => array(
				'type'    => 'sectionend',
				'id'      => 'wc-advance-tweaks_section_end'
			)
		);
		return apply_filters( 'wc_settings_tab_demo_setting', $settings );
	}

	/**
	 * update advance tweaks settings
	 */
	public function update_tweak_settings () {
		woocommerce_update_options( $this->get_settings() );
	}

	/**
	 * add new submenu
	 */
	public function add_custom_sub_menu ( $settings_tab ) {
		$settings_tab['new_submenu'] = __( 'MiscTweaks', 'wc-advance-tweaks');
		$settings_tab['rename_myaccount_menu'] = __( 'MyAccountMenu', 'wc-advance-tweaks');
     	return $settings_tab;
	}

	/**
	 * add settings to submenu
	 */
	public function my_account_submenu_hide( $settings, $current_section ) {
		$custom_settings = array();
		if ( 'new_submenu' == $current_section ) {
			 $custom_settings =  array(
				'section_title' => array(
					'name'     => __( 'MiscTweaks', 'wc-advance-tweaks' ),
					'type'     => 'title',
					'desc'     => '',
					'id'       => 'wc_advance_tweaks_my_account_menu'
				),
				'my-account-show-menu' => array(
					'name'    => __( 'Choose menu to hide', 'wc-advance-tweaks' ),
					'type'    => 'multiselect',
					'desc'    => __( 'Select the menus for customers to hide', 'wc-advance-tweaks' ),
					'class'   => 'wc-enhanced-select',
					'id'      => 'wc_advance_tweaks_coupon_select_field',
					'options' => wc_get_account_menu_items(),
				),
				'section_end' => array(
					'type'    => 'sectionend',
					'id'      => 'wc-advance-tweaks_section_endd'
				)
	   		);
		  		return $custom_settings;
	  	} else {
		   		return $settings;
	  		}
	}

	/**
	 * renamee woocommerce customer my account menu
	 */
	public function rename_my_account_menutab( $settings, $current_section ) {
		$custom_settings = array();
		if ( 'rename_myaccount_menu' == $current_section ) {
			 $custom_settings =  array(
				'section_title' => array(
					'name'     => __( 'My-account menu rename', 'wc-advance-tweaks' ),
					'type'     => 'title',
					'desc'     => '',
					'id'       => 'wc_advance_tweaks_my_account_menu_rename'
				),
				'my-account-menutab-dashboard-rename' => array(
					'name' => __( 'Dashboard', 'wc-advance-tweaks' ),
					'type' => 'text',
					'desc' => __( 'This will rename dashboard menu tab name in my account', 'wc-advance-tweaks' ),
					'id'   => 'wc_advance_tweaks_dashboard_rename'
				),
				'my-account-menutab-orders-rename' => array(
					'name' => __( 'Orders', 'wc-advance-tweaks' ),
					'type' => 'text',
					'desc' => __( 'This will rename orders menu tab name in my account', 'wc-advance-tweaks' ),
					'id'   => 'wc_advance_tweaks_orders_rename'
				),
				'my-account-menutab-downloads-rename' => array(
					'name' => __( 'Downloads', 'wc-advance-tweaks' ),
					'type' => 'text',
					'desc' => __( 'This will rename dashboard menu tab name in my account', 'wc-advance-tweaks' ),
					'id'   => 'wc_advance_tweaks_downloads_rename'
				),
				'my-account-menutab-addresses-rename' => array(
					'name' => __( 'Addresses', 'wc-advance-tweaks' ),
					'type' => 'text',
					'desc' => __( 'This will rename dashboard menu tab name in my account', 'wc-advance-tweaks' ),
					'id'   => 'wc_advance_tweaks_addresses_rename'
				),
				'my-account-menutab-paymentmethods-rename' => array(
					'name' => __( 'Payment Methods', 'wc-advance-tweaks' ),
					'type' => 'text',
					'desc' => __( 'This will rename dashboard menu tab name in my account', 'wc-advance-tweaks' ),
					'id'   => 'wc_advance_tweaks_paymentmethods_rename',
				),
				'my-account-menutab-accountdetails-rename' => array(
					'name' => __( 'Account details', 'wc-advance-tweaks' ),
					'type' => 'text',
					'desc' => __( 'This will rename dashboard menu tab name in my account', 'wc-advance-tweaks' ),
					'id'   => 'wc_advance_tweaks_accountdetails_rename'

				),
				'my-account-menutab-logout-rename' => array(
					'name' => __( 'Logout', 'wc-advance-tweaks' ),
					'type' => 'text',
					'desc' => __( 'This will rename dashboard menu tab name in my account', 'wc-advance-tweaks' ),
					'id'   => 'wc_advance_tweaks_logout_rename'
				),

				'section_end' => array(
					'type'    => 'sectionend',
					'id'      => 'wc-advance-tweaks_section_end'
				)
	   		);
		  		return $custom_settings;
	  	} else {
		   		return $settings;
	  		}
	}
}
