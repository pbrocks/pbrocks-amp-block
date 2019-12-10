<?php
/**
 * Plugin Name: PBrocks AMP Block
 * Description: XWP Block Scaffolding for WordPress adapted for PBrocks AMP Block plugin.
 * Version: 1.0.0
 * Author: pbrocks
 * Author URI: https://github.com/pbrocks/pbrocks-amp-block
 * Text Domain: pbrocks-amp-block
 *
 * @package PBrocksAMPBlock
 */

namespace PBrocks\PBrocksAMPBlock;

// Support for site-level autoloading.
if ( file_exists( __DIR__ . '/vendor/autoload.php' ) ) {
	require_once __DIR__ . '/vendor/autoload.php';
}

if ( defined( 'AMP__FILE__' ) ) {
	$router = new Router( new Plugin( __FILE__ ) );
	add_action( 'plugins_loaded', [ $router, 'init' ] );
} else {
	add_action( 'admin_notices', __NAMESPACE__ . '\amp_check_for_plugin_admin_notice' );
}


/**
 * Print admin notice if AMP plugin is not installed and activated.
 *
 * @since 1.0
 */
function amp_check_for_plugin_admin_notice() {
	$class      = 'notice notice-warning';
	$distress   = __( 'Uh-oh!', 'pbrocks-amp-block' );
	$plugin     = __( 'PBrocks AMP Block plugin', 'pbrocks-amp-block' );
	$message    = __( 'requires that you also install and activate', 'pbrocks-amp-block' );
	$dependency = __( 'the AMP plugin.', 'pbrocks-amp-block' );
	$amp_url    = esc_url(
		add_query_arg(
			[
				's'    => 'amp',
				'tab'  => 'search',
				'type' => 'term',
			],
			admin_url( 'plugin-install.php' )
		)
	);

	printf( '<div class="%1$s"><p><b>%2$s <em>%3$s</em></b> %4$s <b><em><a href="%6$s">%5$s</a></em></b></p></div>', esc_attr( $class ), esc_html( $distress ), esc_html( $plugin ), esc_html( $message ), esc_html( $dependency ), $amp_url );
}

/**
 * Setup WordPress localization support
 *
 * @since 1.0
 */
function pbrocks_amp_block_load_textdomain() {
	load_plugin_textdomain( 'pbrocks-amp-block', false, basename( dirname( __FILE__ ) ) . '/languages' );
}
add_action( 'plugins_loaded', __NAMESPACE__ . '\pbrocks_amp_block_load_textdomain' );
