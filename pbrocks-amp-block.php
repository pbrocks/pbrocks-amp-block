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
		$plugin_to_check_for = 'AMP';
	?>
	<div class="notice notice-warning">
		<p>
			<?php
			echo wp_kses_post(
				sprintf(
					__( 'For the <code>%1$s</code> plugin to run, the <code>%2$s</code> plugin also needs to be installed and activated.', 'pbrocks-amp-block' ),
					'PBrocks\PBrocksAMPBlock',
					$plugin_to_check_for
				)
			);
			?>
		</p>
	</div>
	<?php
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
