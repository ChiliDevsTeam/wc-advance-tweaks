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
		woocommerce_admin_fields( $this-> get_settings() );
	}

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
	 * Add custom fields
	*/
	public function get_settings() {
		$settings = array(
			'section_title' => array(
				'name'     => __( 'Advanced Tweaks', 'wc-advance-tweaks' ),
				'type'     => 'title',
				'desc'     => '',
				'id'       => 'wc-advance-tweaks_section_title'
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
			'redirect-to' => array(
				'name' => __( 'Select Pages', 'wc-advance-tweaks' ),
				'type' => 'select',
				'desc' => __( 'Select preffered pages to show ', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_redirect_button',
				'options' => $this->get_pages(),


			),
			'empty-cart-button-check' => array(
				'name' => __( 'Empty Cart Button', 'wc-advance-tweaks' ),
				'type' => 'checkbox',
				'desc' => __( 'Check this box to show empty cart button', 'wc-advance-tweaks' ),
				'id'   => 'wc_advance_tweaks_empty_button_checkbox'
			),
			'section_end' => array(
				 'type' => 'sectionend',
				 'id' => 'wc-advance-tweaks_section_end'
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
}
