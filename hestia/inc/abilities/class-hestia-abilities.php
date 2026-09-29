<?php
/**
 * Registers Hestia abilities with the WordPress Abilities API.
 *
 * @package Inc/Abilities
 */

/**
 * Class Hestia_Abilities
 */
class Hestia_Abilities extends Hestia_Abstract_Main {

	/**
	 * Ability category and ability name prefix.
	 */
	const CATEGORY = 'hestia';

	/**
	 * Ability provider classes.
	 *
	 * @var array
	 */
	private $providers = array(
		'Hestia_Abilities_Page_Layout',
		'Hestia_Abilities_Custom_Layouts',
		'Hestia_Abilities_Front_Page',
	);

	/**
	 * Initialize the feature.
	 */
	public function init() {
		// The SDK keys its filters by the theme directory: "hestia" or "hestia_pro".
		$product_key = str_replace( '-', '_', strtolower( basename( get_template_directory() ) ) );
		add_filter( $product_key . '_ai_connect_metadata', array( $this, 'get_ai_connect_metadata' ) );

		if ( ! function_exists( 'wp_register_ability' ) ) {
			return;
		}

		add_action( 'wp_abilities_api_categories_init', array( $this, 'register_category' ) );
		add_action( 'wp_abilities_api_init', array( $this, 'register_abilities' ) );
	}

	/**
	 * Get the data for the SDK "Connect your AI agent" module.
	 *
	 * @hooked {theme}_ai_connect_metadata
	 *
	 * @return array
	 */
	public function get_ai_connect_metadata() {
		if ( class_exists( 'Ti_White_Label_Markup' ) && Ti_White_Label_Markup::is_theme_whitelabeld() ) {
			return array();
		}

		$cases   = array(
			__( 'reorder your front page sections', 'hestia' ),
			__( 'set page layouts', 'hestia' ),
		);
		$prompts = array(
			__( 'Move testimonials above the team section on my Hestia front page and hide the ribbon section.', 'hestia' ),
			__( 'Make my Contact page full width with no sidebar.', 'hestia' ),
		);

		// Custom layouts are a Pro module; the free theme has no such ability to promise.
		if ( class_exists( 'Hestia_Abilities_Custom_Layouts' ) ) {
			$cases[]   = __( 'build custom layouts', 'hestia' );
			$prompts[] = __( 'Add a free shipping banner below the header on all product pages.', 'hestia' );
		} else {
			$cases[]   = __( 'change the front page content', 'hestia' );
			$prompts[] = __( 'Change my big title to "Fresh bread every morning" and point its button to /shop/.', 'hestia' );
		}

		return array(
			'name'           => 'Hestia',
			'notice_cases'   => $cases,
			'prompts'        => $prompts,
			'ability_prefix' => 'hestia',
		);
	}

	/**
	 * Register the ability category.
	 */
	public function register_category() {
		if ( ! function_exists( 'wp_register_ability_category' ) ) {
			return;
		}

		wp_register_ability_category(
			self::CATEGORY,
			array(
				'label'       => __( 'Hestia', 'hestia' ),
				'description' => __( 'Read and configure Hestia page layout options, custom layouts and front page sections.', 'hestia' ),
			)
		);
	}

	/**
	 * Register the abilities.
	 */
	public function register_abilities() {
		foreach ( $this->providers as $provider_class ) {
			if ( ! class_exists( $provider_class ) ) {
				continue;
			}
			$provider = new $provider_class();
			$provider->register();
		}
	}

	/**
	 * Register one ability.
	 *
	 * @param string $slug        Ability slug, without the prefix.
	 * @param array  $args        Ability arguments.
	 * @param array  $annotations Ability annotations.
	 */
	public static function register_ability( $slug, $args, $annotations ) {
		$args['category'] = self::CATEGORY;
		$args['meta']     = array(
			'annotations'  => $annotations,
			'show_in_rest' => true,
		);

		wp_register_ability( self::CATEGORY . '/' . $slug, $args );
	}

	/**
	 * Build an error.
	 *
	 * @param string $code    Error code.
	 * @param string $message Error message.
	 * @param int    $status  HTTP status.
	 * @param array  $data    Extra error data (an upgrade_url, for instance).
	 *
	 * @return WP_Error
	 */
	public static function error( $code, $message, $status = 400, $data = array() ) {
		return new WP_Error( 'hestia_' . $code, $message, array_merge( (array) $data, array( 'status' => $status ) ) );
	}
}
