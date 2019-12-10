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

		add_filter( 'gettext', [ $this, 'change_admin_cpt_text_filter' ], 20, 3 );
		add_action( 'init', [ $this, 'built_with_php_init' ] );
		add_action( 'admin_menu', [ $this, 'build_admin_menu' ] );
		add_action( 'add_to_build_dash', [ $this, 'adding_to_pbrocks_amp_dash' ] );
	}
	public function build_admin_menu() {
		$slug  = preg_replace( '/_+/', '-', __FUNCTION__ );
		$label = ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) );
		add_dashboard_page( __( $label, 'pbrocks-amp-block' ), __( $label, 'pbrocks-amp-block' ), 'manage_options', $slug . '.php', [ $this, 'build_dashboard_page' ] );
	}
	/**
	 * Debug Information
	 *
	 * @since 1.0.0
	 *
	 * @param bool $html Optional . return as HTML or not
	 *
	 * @return string
	 */
	public function build_dashboard_page() {
		global $wpdb;
		echo '<div class="wrap">';
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		$screen         = get_current_screen();
		$site_theme     = wp_get_theme();
		$site_prefix    = $wpdb->prefix;
		$prefix_message = '$site_prefix = ' . $site_prefix;
		if ( is_multisite() ) {
			$network_prefix  = $wpdb->base_prefix;
			$prefix_message .= '<br>$network_prefix = ' . $network_prefix;
			$blog_id         = get_current_blog_id();
			$prefix_message .= '<br>$site_prefix = ' . $network_prefix . $blog_id . '_';
		}

		echo '<div class="add-to-build-dash" style="background:aliceblue;padding:1rem 2rem;">';
		do_action( 'add_to_build_dash' );
		echo '</div>';

		echo '<h4 style="color:rgba(250,128,114,.7);">Current Screen is <span style="color:rgba(250,128,114,1);">' . $screen->id . '</span></h4>';

		echo 'Your WordPress version is ' . get_bloginfo( 'version' ) . '<br>';
		echo 'DB prefix is ' . $site_prefix . '<br>';
		echo 'PHP version is ' . phpversion() . '<br>';

		$site_theme = wp_get_theme();
		echo '<h4>Theme is ' . sprintf(
			__( '%1$s and is version %2$s', 'text-domain' ),
			$site_theme->get( 'Name' ),
			$site_theme->get( 'Version' )
		) . '</h4>';
		echo '<h4>Templates found in ' . get_template_directory() . '</h4>';
		echo '<h4>Stylesheet found in ' . get_stylesheet_directory() . '</h4>';
		echo '</div>';
	}

	/**
	 * Debug Information
	 *
	 * [adding_to_pbrocks_amp_dash description]
	 *
	 * @return [type] [description]
	 */
	public function adding_to_pbrocks_amp_dash() {
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';

		echo __FILE__ . ' FFS';
		if ( isset( $_REQUEST['faction'] ) && 'show_constant_info' === $_REQUEST['faction'] ) {
			echo '<h4>To hide Debug Info <a href="' . esc_url( remove_query_arg( 'faction' ) ) . '"><button>Click Here</button></a></h4>';
			$this->printing_validated_amp_posts();
		} else {
			echo '<h4>To show Debug Info <a href="' . esc_url( add_query_arg( 'faction', 'show_constant_info' ) ) . '"><button>Click Here</button></a></h4>';
		}
		echo '</pre>';
	}
	public function printing_validated_amp_posts() {
		if ( post_type_exists( 'amp_validated_url' ) ) {
				$args = array(
					'post_type' => 'amp_validated_url',
				);

				$postslist = get_posts( $args );
				echo '<pre>amp_validated_url ';
				print_r( $postslist );
				echo '</pre>';
		} else {
			echo 'amp_validated_url post_type does not exist';
		}
	}
	public function printing_defined_constants() {
		echo '<h2>' . ucwords( preg_replace( '/_+/', ' ', __FUNCTION__ ) ) . '</h2>';
		echo '<pre>';
		print_r( get_defined_constants( true )['user'] );
		echo '</pre>';
	}

	public function printing_global_variables() {
		echo '<h3>Global Variables</h3>';
		echo plugin_dir_path( __FILE__ ) . 'template/';
		echo '<pre> $_SERVER ';
		print_r( $_SERVER );
		echo '</pre>';
	}

	public function search_something_for_build() {
		$current_url = home_url( add_query_arg( null, null ) );
		echo '<h4>$current_url = ' . $current_url . '</h4>';
		$add_query_arg = esc_url( add_query_arg( 'foo', 'bar' ) );
		echo '<h4>$add_query_arg = ' . $add_query_arg . '</h4>';

		echo '<br>We should have a button <a href="' . esc_url( add_query_arg( 'action', 'show_info' ) ) . '"><button>Click to add query arguments</button></a><br>';

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
	 * [get_number_
	 * of_valid_urls description]
	 *
	 * @return [type] [description]
	 */
	public function get_number_of_valid_urls() {
		$post_type = 'amp_validated_url';
		$count     = wp_count_posts( $post_type );
		return $count->publish;
	}

	/**
	 * [get_number_of_valid_urls description]
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
	 * [get_number_of_valid_urls description]
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

		$output  = '<div class="wp-block-pbrocks-amp-block-amp-info">';
		$output .= wp_kses_post(
			sprintf(
				__( '<h3>' . 'Validated AMP URLs %1$s', 'pbrocks-amp-block' ) . '</h3><h3 style="color:salmon;">' . __( 'AMP URL Errors %2$s', 'pbrocks-amp-block' ) . '<h3>',
				$valid,
				$num_terms
			)
		);
		$output .= '</div>';
		return $output;
	}
	/**
	 * Change the text in the admin for my custom post type
	 */
	function change_admin_cpt_text_filter22( $translated_text, $untranslated_text, $domain ) {

		global $typenow;

		// if ( is_admin() && 'MY_CPT' == $typenow ) {
			// make the changes to the text
		switch ( $untranslated_text ) {

			case 'Validated AMP URLs':
				$translated_text =
				__( 'NEW FEATURED IMAGE TEXT', 'pbrocks-amp-block' );
				break;

			case 'AMP URL Errors':
				$translated_text = __( 'NEW TITLE COPY', 'pbrocks-amp-block' );
				break;

			// add more items
		}
		// }
		return $translated_text;
	}

	function change_admin_cpt_text_filter( $output_text, $input_text, $domain ) {
		if ( 'pbrocks-amp-block' === $domain ) {
			$output_text = str_replace( 'Validated AMP URLs', 'Membership Level text changed here: ' . basename( __FILE__ ) . ' on Line ' . __LINE__, $output_text );
		}
		return $output_text;
	}
}
