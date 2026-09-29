<?php
/**
 * Page layout ability. Wraps the "Page Settings" metabox options.
 *
 * @package Inc/Abilities
 */

/**
 * Class Hestia_Abilities_Page_Layout
 */
class Hestia_Abilities_Page_Layout {

	/**
	 * Checkbox options of the metabox: input name => post meta key.
	 *
	 * @var array
	 */
	private $toggles = array(
		'disable_navigation' => 'hestia_disable_navigation',
		'disable_footer'     => 'hestia_disable_footer',
		'disable_title'      => 'hestia_meta_disable_title',
		'transparent_header' => 'hestia_enable_transparent',
	);

	/**
	 * Options that need a valid license, same as in the metabox.
	 *
	 * @var array
	 */
	private $licensed = array( 'header_layout', 'disable_title', 'transparent_header' );

	/**
	 * Register the ability.
	 */
	public function register() {
		$fields = array(
			'sidebar_layout'     => array(
				'type'        => 'string',
				'enum'        => array( 'full-width', 'sidebar-left', 'sidebar-right' ),
				'description' => __( 'Sidebar layout of this post. Not available for portfolio items and products.', 'hestia' ),
			),
			'header_layout'      => array(
				'type'        => 'string',
				'enum'        => array( 'default', 'no-content', 'classic-blog' ),
				'description' => __( 'Header layout of this post. Products only accept no-content and classic-blog.', 'hestia' ),
			),
			'disable_navigation' => array( 'type' => 'boolean' ),
			'disable_footer'     => array( 'type' => 'boolean' ),
			'disable_title'      => array( 'type' => 'boolean' ),
			'transparent_header' => array( 'type' => 'boolean' ),
		);

		Hestia_Abilities::register_ability(
			'page-layout-update',
			array(
				'label'               => __( 'Update page layout options', 'hestia' ),
				'description'         => __( 'Reads and updates the Hestia per-post layout options (sidebar layout, header layout, disabled navigation, footer and title, transparent header). Call it with only post_id to read the current values.', 'hestia' ),
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array_merge(
						array(
							'post_id' => array(
								'type'        => 'integer',
								'minimum'     => 1,
								'description' => __( 'ID of the post, page, portfolio item or product.', 'hestia' ),
							),
						),
						$fields,
						array(
							'reset'   => array(
								'type'        => 'array',
								'description' => __( 'Options to reset so the post inherits the site-wide value.', 'hestia' ),
								'items'       => array(
									'type' => 'string',
									'enum' => array_keys( $fields ),
								),
							),
							'dry_run' => array(
								'type'        => 'boolean',
								'description' => __( 'Validate and return the resulting values without saving.', 'hestia' ),
							),
						)
					),
					'required'             => array( 'post_id' ),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'post_id'   => array( 'type' => 'integer' ),
						'post_type' => array( 'type' => 'string' ),
						'updated'   => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
						'dry_run'   => array( 'type' => 'boolean' ),
						'layout'    => array(
							'type'       => 'object',
							'properties' => array(
								'sidebar_layout'     => array( 'type' => 'string' ),
								'header_layout'      => array( 'type' => 'string' ),
								'disable_navigation' => array( 'type' => 'boolean' ),
								'disable_footer'     => array( 'type' => 'boolean' ),
								'disable_title'      => array( 'type' => 'boolean' ),
								'transparent_header' => array( 'type' => 'boolean' ),
							),
						),
					),
				),
				'execute_callback'    => array( $this, 'execute' ),
				'permission_callback' => array( $this, 'can_edit' ),
			),
			array(
				'readonly'    => false,
				'destructive' => false,
				'idempotent'  => true,
			)
		);
	}

	/**
	 * Permission check. Same capability as the metabox save routine, plus the object check core does on post edit.
	 *
	 * @param array $input Ability input.
	 *
	 * @return bool
	 */
	public function can_edit( $input = array() ) {
		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		if ( is_array( $input ) && ! empty( $input['post_id'] ) ) {
			return current_user_can( 'edit_post', absint( $input['post_id'] ) );
		}

		return true;
	}

	/**
	 * Get the options supported by a post type, same as the metabox controls.
	 *
	 * @param string $post_type Post type.
	 *
	 * @return array
	 */
	private function get_supported_options( $post_type ) {
		$toggles = array_keys( $this->toggles );
		if ( $post_type === 'product' ) {
			return array( 'header_layout' );
		}
		if ( $post_type === 'jetpack-portfolio' ) {
			return array_merge( array( 'header_layout' ), $toggles );
		}

		return array_merge( array( 'sidebar_layout', 'header_layout' ), $toggles );
	}

	/**
	 * Read the current values.
	 *
	 * @param int   $post_id Post id.
	 * @param array $pending Values that are not saved yet.
	 *
	 * @return array
	 */
	private function get_layout( $post_id, $pending = array() ) {
		$layout = array(
			'sidebar_layout' => (string) get_post_meta( $post_id, 'hestia_layout_select', true ),
			'header_layout'  => (string) get_post_meta( $post_id, 'hestia_header_layout', true ),
		);
		foreach ( $this->toggles as $name => $meta_key ) {
			$layout[ $name ] = get_post_meta( $post_id, $meta_key, true ) === 'on';
		}

		foreach ( $pending as $name => $value ) {
			if ( $value === null ) {
				$layout[ $name ] = array_key_exists( $name, $this->toggles ) ? false : '';
				continue;
			}
			$layout[ $name ] = $value;
		}

		return $layout;
	}

	/**
	 * Execute the ability.
	 *
	 * @param array $input Ability input.
	 *
	 * @return array|WP_Error
	 */
	public function execute( $input = array() ) {
		$input   = is_array( $input ) ? $input : array();
		$post_id = isset( $input['post_id'] ) ? absint( $input['post_id'] ) : 0;
		$post    = $post_id ? get_post( $post_id ) : null;
		if ( ! $post ) {
			return Hestia_Abilities::error( 'post_not_found', __( 'The post does not exist.', 'hestia' ), 404 );
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return Hestia_Abilities::error( 'forbidden', __( 'You are not allowed to edit this post.', 'hestia' ), 403 );
		}

		$post_types = apply_filters( 'hestia_metabox_post_types', array( 'post', 'page', 'jetpack-portfolio' ) );
		if ( ! in_array( $post->post_type, $post_types, true ) ) {
			return Hestia_Abilities::error( 'unsupported_post_type', __( 'Hestia layout options are not available for this post type.', 'hestia' ) );
		}

		$supported = $this->get_supported_options( $post->post_type );
		$changes   = array();

		if ( isset( $input['sidebar_layout'] ) ) {
			$choices = array( 'full-width', 'sidebar-left', 'sidebar-right' );
			if ( get_post_meta( $post_id, '_wp_page_template', true ) === 'page-templates/template-page-sidebar.php' ) {
				$choices = array( 'sidebar-left', 'sidebar-right' );
			}
			if ( ! in_array( $input['sidebar_layout'], $choices, true ) ) {
				return Hestia_Abilities::error( 'invalid_sidebar_layout', __( 'Invalid sidebar layout for this post.', 'hestia' ) );
			}
			$changes['sidebar_layout'] = $input['sidebar_layout'];
		}

		if ( isset( $input['header_layout'] ) ) {
			$choices = $post->post_type === 'product' ? array( 'no-content', 'classic-blog' ) : array( 'default', 'no-content', 'classic-blog' );
			if ( ! in_array( $input['header_layout'], $choices, true ) ) {
				return Hestia_Abilities::error( 'invalid_header_layout', __( 'Invalid header layout for this post.', 'hestia' ) );
			}
			$changes['header_layout'] = $input['header_layout'];
		}

		foreach ( $this->toggles as $name => $meta_key ) {
			if ( isset( $input[ $name ] ) ) {
				$changes[ $name ] = (bool) $input[ $name ];
			}
		}

		if ( ! empty( $input['reset'] ) && is_array( $input['reset'] ) ) {
			foreach ( $input['reset'] as $name ) {
				if ( $name === 'sidebar_layout' || $name === 'header_layout' || array_key_exists( $name, $this->toggles ) ) {
					$changes[ $name ] = null;
				}
			}
		}

		foreach ( array_keys( $changes ) as $name ) {
			if ( ! in_array( $name, $supported, true ) ) {
				/* translators: %s: option name */
				return Hestia_Abilities::error( 'unsupported_option', sprintf( __( 'The option %s is not available for this post type.', 'hestia' ), $name ) );
			}
			// The metabox offers these only in the Pro edition with a valid
			// license; a license record left behind in the free theme unlocks nothing.
			if ( in_array( $name, $this->licensed, true ) && ! defined( 'HESTIA_PRO_FLAG' ) ) {
				$upgrade_url = tsdk_utmify( 'https://themeisle.com/themes/hestia/upgrade/', 'abilities', 'mcp' );
				return Hestia_Abilities::error(
					'pro_required',
					/* translators: 1: option name, 2: upgrade URL */
					sprintf( __( 'The option %1$s needs Hestia Pro. Upgrade: %2$s', 'hestia' ), $name, $upgrade_url ),
					403,
					array( 'upgrade_url' => $upgrade_url )
				);
			}
			if ( in_array( $name, $this->licensed, true ) && ! hestia_is_license_valid() ) {
				/* translators: %s: option name */
				return Hestia_Abilities::error( 'license_required', sprintf( __( 'The option %s needs a valid Hestia Pro license.', 'hestia' ), $name ), 403 );
			}
		}

		$dry_run = ! empty( $input['dry_run'] );
		if ( ! $dry_run ) {
			foreach ( $changes as $name => $value ) {
				$this->save_option( $post_id, $name, $value );
			}
		}

		return array(
			'post_id'   => $post_id,
			'post_type' => $post->post_type,
			'updated'   => array_keys( $changes ),
			'dry_run'   => $dry_run,
			'layout'    => $this->get_layout( $post_id, $dry_run ? $changes : array() ),
		);
	}

	/**
	 * Save one option the way the metabox controls store it.
	 *
	 * @param int              $post_id Post id.
	 * @param string           $name    Option name.
	 * @param string|bool|null $value   Option value, null to reset.
	 */
	private function save_option( $post_id, $name, $value ) {
		if ( array_key_exists( $name, $this->toggles ) ) {
			// Checkbox controls store "on" and delete the meta for the default "off".
			if ( $value === true ) {
				update_post_meta( $post_id, $this->toggles[ $name ], 'on' );

				return;
			}
			delete_post_meta( $post_id, $this->toggles[ $name ] );

			return;
		}

		$meta_key = $name === 'sidebar_layout' ? 'hestia_layout_select' : 'hestia_header_layout';
		if ( $value === null ) {
			delete_post_meta( $post_id, $meta_key );

			return;
		}
		update_post_meta( $post_id, $meta_key, sanitize_text_field( $value ) );
	}
}
