<?php
/**
 * Router class.
 *
 * @package PBrocksAMPBlock
 */

namespace PBrocks\PBrocksAMPBlock;

/**
 * Plugin Router.
 */
class Router {

	/**
	 * Plugin interface.
	 *
	 * @var Plugin
	 */
	protected $plugin;

	/**
	 * Setup the plugin instance.
	 *
	 * @param Plugin $plugin Instance of the plugin abstraction.
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
	}

	/**
	 * Hook into WP.
	 *
	 * @return void
	 */
	public function init() {
		add_action( 'enqueue_block_editor_assets', [ $this, 'enqueue_editor_assets' ] );
		add_action( 'enqueue_block_assets', [ $this, 'enqueue_amp_block_assets' ] );
		add_action( 'init', [ $this, 'built_with_php_init' ] );
	}

	/**
	 * Load our block assets.
	 *
	 * @return void
	 */
	public function enqueue_editor_assets() {
		wp_enqueue_script(
			'pbrocks-amp-block-js',
			$this->plugin->asset_url( 'js/dist/editor.js' ),
			[
				'lodash',
				'react',
				'wp-block-editor',
			],
			$this->plugin->asset_version()
		);

		wp_enqueue_style(
			'pbrocks-amp-editor-block',
			$this->plugin->asset_url( 'css/amp-info/editor.css' ),
			array(),
			$this->plugin->asset_version()
		);
	}

	/**
	 * [enqueue_amp_block_assets]
	 *
	 * @return [type] [description]
	 */
	public function enqueue_amp_block_assets() {
		wp_enqueue_style(
			'pbrocks-amp-block',
			$this->plugin->asset_url( 'css/amp-info/style.css' ),
			array(),
			$this->plugin->asset_version()
		);
	}

	/**
	 * [get_number_of_valid_urls description]
	 *
	 * @return [type] [description]
	 */
	public function get_number_of_valid_urls() {
		$post_type = 'amp_validated_url';
		$count     = wp_count_posts( $post_type );
		return $count->publish;
	}

	/**
	 * [get_number_of_errors description]
	 *
	 * @return [type] [description]
	 */
	public function get_number_of_errors() {
		$taxonomy = 'amp_validation_error';

		$num_terms = wp_count_terms(
			$taxonomy,
			[]
		);
		return $num_terms;
	}

	/**
	 * [built_with_php_init description]
	 *
	 * @return [type] [description]
	 */
	public function built_with_php_init() {
		register_block_type(
			'pbrocks-amp-block/amp-info',
			[
				'attributes'      =>
				[
					'valid_amp_urls' => [
						'type'    => 'string',
						'default' => 0,
					],
					'amp_url_errors' => [
						'type'    => 'string',
						'default' => 0,
					],

				],

				'render_callback' => [ $this, 'render_amp_info_with_php' ],
			]
		);

	}

	/**
	 * [render_amp_info_with_php]
	 *
	 * @return string [description]
	 */
	public function render_amp_info_with_php( $attributes ) {

		if ( post_type_exists( 'amp_validated_url' ) ) {
			$valid = ( $this->get_number_of_valid_urls() ?: $attributes['valid_amp_urls'] );
		} else {
			$valid = $attributes['valid_amp_urls'];
		}
		if ( taxonomy_exists( 'amp_url_errors' ) ) {
			$num_terms = ( $this->get_number_of_errors() ?: $attributes['amp_url_errors'] );
		} else {
			$num_terms = $attributes['amp_url_errors'];
		}

		$descriptor1 = 'Validated AMP URLs';
		$descriptor2 = 'AMP URL Errors';

		$output = '<div class="wp-block-pbrocks-amp-block-amp-info">';

		$output .= wp_kses_post(
			sprintf(
				__(
					'<h3>%1$s %2$s</h3>
					<h3 class="pbrx-negative">%3$s %4$s</h3>',
					'pbrocks-amp-block'
				),
				$descriptor1,
				$valid,
				$descriptor2,
				$num_terms
			)
		);
		$output .= '</div>';
		return $output;
	}

}
