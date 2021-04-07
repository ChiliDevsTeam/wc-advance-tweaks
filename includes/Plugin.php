<?php
/**
 * Main plugin class.
 *
 * @package ChiliDevs\WCAdvanceTweaks
 * @since 1.0.0
 */

declare(strict_types=1);

namespace ChiliDevs\WCAdvanceTweaks;

use ChiliDevs\WCAdvanceTweaks\Admin\Admin;
use ChiliDevs\WCAdvanceTweaks\Frontend\Frontend;

/**
 * Class Plugin.
 *
 * @package ChiliDevs\WCAdvanceTweaks
 */
class Plugin {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Plugin directory path.
	 *
	 * @var string
	 */
	public $path;

	/**
	 * Plugin's url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Assets directory path.
	 *
	 * @var string
	 */
	public $assets_dir;

	/**
	 * Fire the plugin initialization step.
	 *
	 * @return void
	 */
	public function run(): void {
		$this->path       = dirname( __FILE__, 2 );
		$this->url        = plugin_dir_url( trailingslashit( dirname( __FILE__, 2 ) ) . 'wc-advance-tweaks.php' );
		$this->assets_dir = trailingslashit( $this->path ) . 'assets/';
		new Admin();
		new Frontend();

	}

	/**
	 * Run the activator from installer
	 *
	 * @return void
	 */
	public function activator(): void {
        // phpcs:ignore;
		// register_activation_hook( dirname( __FILE__, 2 ) . '/wc-advance-tweaks.php', [ Installer::class, 'activation' ] );
	}

	/**
	 * Run the deactivator from installer
	 *
	 * @return void
	 */
	public function deactivator(): void {
        // phpcs:ignore;
		// register_deactivation_hook( dirname( __FILE__, 2 ) . '/wc-advance-tweaks.php', [ Installer::class, 'activation' ] );
	}
}
