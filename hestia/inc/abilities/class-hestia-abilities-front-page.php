<?php
/**
 * Front page sections abilities. Wraps the theme mods behind the "Frontpage Sections" customizer panel.
 *
 * @package Inc/Abilities
 */

/**
 * Class Hestia_Abilities_Front_Page
 */
class Hestia_Abilities_Front_Page {

	/**
	 * Maximum number of repeater items accepted for one section.
	 */
	const MAX_ITEMS = 50;

	/**
	 * Keys of a repeater item, same as the ones the customizer repeater control saves.
	 *
	 * @var array
	 */
	private $item_keys = array( 'id', 'choice', 'icon_value', 'image_url', 'title', 'subtitle', 'text', 'text2', 'link', 'link2', 'color', 'color2', 'social_repeater', 'shortcode' );

	/**
	 * Get the sections map.
	 *
	 * Each field is described as array( theme mod, type ). The type selects the same sanitize callback
	 * the customizer setting uses.
	 *
	 * @return array
	 */
	private function get_sections() {
		$sections = array(
			'big_title'    => array(
				'priority'       => 0,
				'hidden_default' => false,
				'title'          => false,
				'items'          => 'hestia_slider_content',
				'fields'         => array(
					'slider_type'      => array( 'hestia_slider_type', 'slider_type' ),
					'alignment'        => array( 'hestia_slider_alignment', 'alignment' ),
					'disable_autoplay' => array( 'hestia_slider_disable_autoplay', 'boolean' ),
				),
			),
			'features'     => array(
				'priority' => 10,
				'items'    => 'hestia_features_content',
			),
			'about'        => array(
				'priority' => 15,
				'title'    => false,
				'fields'   => array(
					'background' => array( 'hestia_feature_thumbnail', 'url' ),
				),
			),
			'shop'         => array(
				'priority'  => 20,
				'available' => class_exists( 'WooCommerce', false ),
				'fields'    => array(
					'items_count' => array( 'hestia_shop_items', 'integer' ),
					'categories'  => array( 'hestia_shop_categories', 'list' ),
					'order'       => array( 'hestia_shop_order', 'text' ),
					'shortcode'   => array( 'hestia_shop_shortcode', 'text' ),
				),
			),
			'portfolio'    => array(
				'priority'  => 25,
				'available' => post_type_exists( 'jetpack-portfolio' ),
				'fields'    => array(
					'items_count' => array( 'hestia_portfolio_items', 'integer' ),
					'boxes_type'  => array( 'hestia_portfolio_boxes_type', 'boolean' ),
					'lightbox'    => array( 'hestia_enable_portfolio_lightbox', 'boolean' ),
				),
			),
			'team'         => array(
				'priority' => 30,
				'items'    => 'hestia_team_content',
			),
			'pricing'      => array(
				'priority'       => 35,
				'hidden_default' => true,
				'fields'         => array(),
			),
			'ribbon'       => array(
				'priority'       => 40,
				'hidden_default' => true,
				'title'          => false,
				'fields'         => array(
					'text'        => array( 'hestia_ribbon_text', 'html' ),
					'button_text' => array( 'hestia_ribbon_button_text', 'text' ),
					'button_url'  => array( 'hestia_ribbon_button_url', 'url' ),
					'background'  => array( 'hestia_ribbon_background', 'url' ),
				),
			),
			'testimonials' => array(
				'priority' => 45,
				'items'    => 'hestia_testimonials_content',
			),
			'clients_bar'  => array(
				'priority'       => 50,
				'hidden_default' => true,
				'title'          => false,
				'items'          => 'hestia_clients_bar_content',
			),
			'subscribe'    => array(
				'priority'       => 55,
				'hidden_default' => true,
				'fields'         => array(
					'background' => array( 'hestia_subscribe_background', 'url' ),
				),
			),
			'blog'         => array(
				'priority' => 60,
				'fields'   => array(
					'items_count' => array( 'hestia_blog_items', 'integer' ),
					'categories'  => array( 'hestia_blog_categories', 'list' ),
				),
			),
			'contact'      => array(
				'priority' => 65,
				'fields'   => array(
					'area_title'     => array( 'hestia_contact_area_title', 'text' ),
					'content'        => array( 'hestia_contact_content_new', 'contact_html' ),
					'form_shortcode' => array( 'hestia_contact_form_shortcode', 'text' ),
					'background'     => array( 'hestia_contact_background', 'url' ),
				),
			),
		);

		foreach ( array( 'one', 'two' ) as $table ) {
			$prefix = 'hestia_pricing_table_' . $table;

			$sections['pricing']['fields'][ 'table_' . $table . '_title' ]       = array( $prefix . '_title', 'text' );
			$sections['pricing']['fields'][ 'table_' . $table . '_price' ]       = array( $prefix . '_price', 'html' );
			$sections['pricing']['fields'][ 'table_' . $table . '_features' ]    = array( $prefix . '_features', 'html' );
			$sections['pricing']['fields'][ 'table_' . $table . '_button_url' ]  = array( $prefix . '_link', 'url' );
			$sections['pricing']['fields'][ 'table_' . $table . '_button_text' ] = array( $prefix . '_text', 'text' );
		}

		return $sections;
	}

	/**
	 * Get the keys of each section that the customizer locks without a valid license.
	 *
	 * Same settings as handleProFeatures() in inc/addons/assets/js/scripts-customizer-pro.js, keyed the way
	 * the update input is: enabled, title, subtitle, items and the field names.
	 *
	 * @param array $sections Sections map.
	 *
	 * @return array
	 */
	private function get_locked_keys( $sections ) {
		// Unlocked only in the Pro edition (its addons register these controls)
		// with a valid license. A license record left behind after switching to
		// the free theme unlocks nothing: the free theme has no such controls.
		if ( defined( 'HESTIA_PRO_FLAG' ) && hestia_is_license_valid() ) {
			return array();
		}

		$locked = array(
			'big_title' => array( 'disable_autoplay', 'items' ),
			'shop'      => array( 'categories', 'order', 'shortcode' ),
			'portfolio' => array( 'items_count', 'boxes_type', 'lightbox' ),
			'pricing'   => array_merge( array( 'enabled', 'title', 'subtitle' ), array_keys( $sections['pricing']['fields'] ) ),
			'blog'      => array( 'categories' ),
		);

		// The customizer also locks these sections when Orbit Fox is not active.
		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		if ( ! is_plugin_active( 'themeisle-companion/themeisle-companion.php' ) ) {
			$locked['features']     = array( 'enabled', 'title', 'subtitle', 'items' );
			$locked['team']         = array( 'enabled', 'title', 'subtitle', 'items' );
			$locked['testimonials'] = array( 'enabled', 'title', 'subtitle', 'items' );
			$locked['ribbon']       = array( 'enabled', 'text', 'button_text', 'button_url', 'background' );
			$locked['clients_bar']  = array( 'enabled', 'items' );
		}

		return $locked;
	}

	/**
	 * Register the abilities.
	 */
	public function register() {
		$section_ids = array_keys( $this->get_sections() );

		$item_properties = array();
		foreach ( $this->item_keys as $key ) {
			$item_properties[ $key ] = array( 'type' => 'string' );
		}
		$items_schema = array(
			'type'        => 'array',
			'description' => __( 'Repeater items of the section (slides, features, team members, testimonials, client logos). Replaces the whole list.', 'hestia' ),
			'items'       => array(
				'type'       => 'object',
				'properties' => $item_properties,
			),
		);

		$section_schema = array(
			'type'       => 'object',
			'properties' => array(
				'id'        => array(
					'type' => 'string',
					'enum' => $section_ids,
				),
				'enabled'   => array( 'type' => 'boolean' ),
				'position'  => array( 'type' => 'integer' ),
				'orderable' => array( 'type' => 'boolean' ),
				'available' => array( 'type' => 'boolean' ),
				'title'     => array( 'type' => 'string' ),
				'subtitle'  => array( 'type' => 'string' ),
				'fields'    => array(
					'type'                 => 'object',
					'additionalProperties' => true,
				),
				'items'     => $items_schema,
				'locked'    => array(
					'type'        => 'array',
					'description' => __( 'Keys of this section that cannot be changed because they need a valid Hestia Pro license.', 'hestia' ),
					'items'       => array( 'type' => 'string' ),
				),
			),
		);

		Hestia_Abilities::register_ability(
			'get-front-page-sections',
			array(
				'label'               => __( 'Get front page sections', 'hestia' ),
				'description'         => __( 'Returns the Hestia front page sections in display order, with their visibility, titles, fields and repeater items.', 'hestia' ),
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'section' => array(
							'type'        => 'string',
							'enum'        => $section_ids,
							'description' => __( 'Return only this section.', 'hestia' ),
						),
					),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'front_page_active' => array( 'type' => 'boolean' ),
						'sections'          => array(
							'type'  => 'array',
							'items' => $section_schema,
						),
					),
				),
				'execute_callback'    => array( $this, 'get_front_page_sections' ),
				'permission_callback' => array( $this, 'can_customize' ),
			),
			array(
				'readonly'    => true,
				'destructive' => false,
				'idempotent'  => true,
			)
		);

		Hestia_Abilities::register_ability(
			'update-front-page-sections',
			array(
				'label'               => __( 'Update front page sections', 'hestia' ),
				'description'         => __( 'Updates the order, visibility and content of the Hestia front page sections. Use hestia/get-front-page-sections to see the fields each section supports.', 'hestia' ),
				'input_schema'        => array(
					'type'                 => 'object',
					'properties'           => array(
						'order'    => array(
							'type'        => 'array',
							'description' => __( 'Section ids in the wanted order. Sections left out keep their relative order after the listed ones. big_title is always first and cannot be ordered.', 'hestia' ),
							'items'       => array(
								'type' => 'string',
								'enum' => $section_ids,
							),
						),
						'sections' => array(
							'type'  => 'array',
							'items' => array(
								'type'                 => 'object',
								'properties'           => array(
									'id'       => array(
										'type' => 'string',
										'enum' => $section_ids,
									),
									'enabled'  => array( 'type' => 'boolean' ),
									'title'    => array( 'type' => 'string' ),
									'subtitle' => array( 'type' => 'string' ),
									'fields'   => array(
										'type'        => 'object',
										'description' => __( 'Section specific fields, keyed as returned by hestia/get-front-page-sections.', 'hestia' ),
										'additionalProperties' => true,
									),
									'items'    => $items_schema,
								),
								'required'             => array( 'id' ),
								'additionalProperties' => false,
							),
						),
						'dry_run'  => array(
							'type'        => 'boolean',
							'description' => __( 'Validate and return the theme mods that would change without saving.', 'hestia' ),
						),
					),
					'additionalProperties' => false,
				),
				'output_schema'       => array(
					'type'       => 'object',
					'properties' => array(
						'dry_run'  => array( 'type' => 'boolean' ),
						'updated'  => array(
							'type'  => 'array',
							'items' => array( 'type' => 'string' ),
						),
						'sections' => array(
							'type'  => 'array',
							'items' => $section_schema,
						),
					),
				),
				'execute_callback'    => array( $this, 'update_front_page_sections' ),
				'permission_callback' => array( $this, 'can_customize' ),
			),
			array(
				'readonly'    => false,
				'destructive' => false,
				'idempotent'  => true,
			)
		);
	}

	/**
	 * Permission check. The sections are customizer settings, which need edit_theme_options.
	 *
	 * @return bool
	 */
	public function can_customize() {
		return current_user_can( 'edit_theme_options' );
	}

	/**
	 * Get the saved sections order.
	 *
	 * @return array
	 */
	private function get_saved_order() {
		$order = json_decode( (string) get_theme_mod( 'sections_order' ), true );

		return is_array( $order ) ? $order : array();
	}

	/**
	 * Get the priority of each orderable section.
	 *
	 * @param array $sections Sections map.
	 * @param array $order    Saved order.
	 *
	 * @return array
	 */
	private function get_priorities( $sections, $order ) {
		$priorities = array();
		foreach ( $sections as $id => $section ) {
			if ( $id === 'big_title' ) {
				continue;
			}
			$priority = $section['priority'];
			if ( isset( $order[ 'hestia_' . $id ] ) ) {
				$priority = (int) $order[ 'hestia_' . $id ];
			}
			if ( $id === 'subscribe' && isset( $order['sidebar-widgets-subscribe-widgets'] ) ) {
				$priority = (int) $order['sidebar-widgets-subscribe-widgets'];
			}
			$priorities[ $id ] = $priority;
		}
		asort( $priorities );

		return $priorities;
	}

	/**
	 * Read a field value.
	 *
	 * @param string $theme_mod Theme mod name.
	 * @param string $type      Field type.
	 * @param array  $mods      Pending theme mods.
	 *
	 * @return mixed
	 */
	private function read_field( $theme_mod, $type, $mods ) {
		$value = array_key_exists( $theme_mod, $mods ) ? $mods[ $theme_mod ] : get_theme_mod( $theme_mod, '' );

		switch ( $type ) {
			case 'boolean':
				return (bool) $value;
			case 'integer':
				return (int) $value;
			case 'list':
				return is_array( $value ) ? array_values( array_map( 'strval', $value ) ) : array();
			default:
				return (string) $value;
		}
	}

	/**
	 * Format the sections.
	 *
	 * @param array  $mods  Pending theme mods, used for dry runs.
	 * @param string $only  Return only this section.
	 *
	 * @return array
	 */
	private function format_sections( $mods = array(), $only = '' ) {
		$sections = $this->get_sections();
		$locked   = $this->get_locked_keys( $sections );
		$order    = array_key_exists( 'sections_order', $mods ) ? json_decode( $mods['sections_order'], true ) : $this->get_saved_order();
		$ids      = array_merge( array( 'big_title' ), array_keys( $this->get_priorities( $sections, is_array( $order ) ? $order : array() ) ) );
		$result   = array();

		foreach ( $ids as $position => $id ) {
			if ( $only !== '' && $only !== $id ) {
				continue;
			}
			$section   = $sections[ $id ];
			$hide_mod  = 'hestia_' . $id . '_hide';
			$is_hidden = array_key_exists( $hide_mod, $mods ) ? $mods[ $hide_mod ] : get_theme_mod( $hide_mod, ! empty( $section['hidden_default'] ) );

			$formatted = array(
				'id'        => $id,
				'enabled'   => ! $is_hidden,
				'position'  => $position,
				'orderable' => $id !== 'big_title',
				'available' => ! isset( $section['available'] ) || $section['available'],
			);

			if ( ! isset( $section['title'] ) || $section['title'] !== false ) {
				$formatted['title']    = $this->read_field( 'hestia_' . $id . '_title', 'html', $mods );
				$formatted['subtitle'] = $this->read_field( 'hestia_' . $id . '_subtitle', 'html', $mods );
			}

			if ( ! empty( $section['fields'] ) ) {
				$formatted['fields'] = array();
				foreach ( $section['fields'] as $name => $field ) {
					$formatted['fields'][ $name ] = $this->read_field( $field[0], $field[1], $mods );
				}
			}

			if ( ! empty( $section['items'] ) ) {
				$items = array_key_exists( $section['items'], $mods ) ? $mods[ $section['items'] ] : get_theme_mod( $section['items'], '' );
				$items = json_decode( (string) $items, true );

				$formatted['items'] = array();
				if ( is_array( $items ) ) {
					foreach ( $items as $item ) {
						if ( is_array( $item ) ) {
							$formatted['items'][] = array_map( 'strval', array_intersect_key( $item, array_flip( $this->item_keys ) ) );
						}
					}
				}
			}

			if ( ! empty( $locked[ $id ] ) ) {
				$formatted['locked'] = array_values( $locked[ $id ] );
			}

			$result[] = $formatted;
		}

		return $result;
	}

	/**
	 * Get the front page sections.
	 *
	 * @param array $input Ability input.
	 *
	 * @return array
	 */
	public function get_front_page_sections( $input = array() ) {
		$only = is_array( $input ) && isset( $input['section'] ) ? sanitize_key( $input['section'] ) : '';

		return array(
			'front_page_active' => get_option( 'show_on_front' ) === 'page' && ! get_theme_mod( 'disable_frontpage_sections', false ),
			'sections'          => $this->format_sections( array(), $only ),
		);
	}

	/**
	 * Sanitize a field with the callback its customizer setting uses.
	 *
	 * @param string $name  Field name.
	 * @param string $type  Field type.
	 * @param mixed  $value Field value.
	 *
	 * @return mixed|WP_Error
	 */
	private function sanitize_field( $name, $type, $value ) {
		if ( $type !== 'list' && $type !== 'boolean' && ! is_scalar( $value ) ) {
			/* translators: %s: field name */
			return Hestia_Abilities::error( 'invalid_field', sprintf( __( 'Invalid value for the field %s.', 'hestia' ), $name ) );
		}

		switch ( $type ) {
			case 'boolean':
				return hestia_sanitize_checkbox( $value );
			case 'integer':
				return absint( $value );
			case 'url':
				return esc_url_raw( $value );
			case 'html':
				return wp_kses_post( $value );
			case 'contact_html':
				return Hestia_Contact_Controls::sanitize_contact_field( $value );
			case 'list':
				if ( ! is_array( $value ) ) {
					$value = array( $value );
				}

				return hestia_sanitize_array( array_filter( $value, 'is_scalar' ) );
			case 'slider_type':
				if ( ! in_array( $value, array( 'image', 'parallax', 'video' ), true ) ) {
					return Hestia_Abilities::error( 'invalid_field', __( 'slider_type must be image, parallax or video.', 'hestia' ) );
				}

				return hestia_sanitize_big_title_type( $value );
			case 'alignment':
				// Checked here because hestia_sanitize_alignment_options() ends the request on invalid values.
				if ( ! in_array( $value, array( 'left', 'center', 'right' ), true ) ) {
					return Hestia_Abilities::error( 'invalid_field', __( 'alignment must be left, center or right.', 'hestia' ) );
				}

				return hestia_sanitize_alignment_options( $value );
			default:
				return sanitize_text_field( $value );
		}
	}

	/**
	 * Build the repeater value the way the customizer control saves it.
	 *
	 * @param string $section_id Section id.
	 * @param mixed  $items      Items.
	 *
	 * @return string|WP_Error
	 */
	private function sanitize_items( $section_id, $items ) {
		if ( ! is_array( $items ) || count( $items ) > self::MAX_ITEMS ) {
			/* translators: %d: maximum number of items */
			return Hestia_Abilities::error( 'invalid_items', sprintf( __( 'Items must be a list of at most %d entries.', 'hestia' ), self::MAX_ITEMS ) );
		}

		$clean = array();
		foreach ( array_values( $items ) as $index => $item ) {
			if ( ! is_array( $item ) ) {
				return Hestia_Abilities::error( 'invalid_items', __( 'Each item must be an object.', 'hestia' ) );
			}
			$entry = array();
			foreach ( $this->item_keys as $key ) {
				if ( isset( $item[ $key ] ) && is_scalar( $item[ $key ] ) ) {
					$entry[ $key ] = (string) $item[ $key ];
				}
			}
			if ( empty( $entry['id'] ) ) {
				$entry['id'] = 'customizer_repeater_' . $section_id . '_' . ( $index + 1 );
			}
			$clean[] = $entry;
		}

		return hestia_repeater_sanitize( wp_json_encode( $clean ) );
	}

	/**
	 * Build the sections_order theme mod.
	 *
	 * @param array $requested Section ids in the wanted order.
	 *
	 * @return string|WP_Error
	 */
	private function build_order( $requested ) {
		if ( ! class_exists( 'Hestia_Section_Ordering' ) ) {
			return Hestia_Abilities::error( 'ordering_unavailable', __( 'Section ordering is not available.', 'hestia' ), 500 );
		}

		$sections = $this->get_sections();
		$current  = array_keys( $this->get_priorities( $sections, $this->get_saved_order() ) );
		$ordered  = array();

		foreach ( (array) $requested as $id ) {
			$id = sanitize_key( $id );
			if ( $id === 'big_title' ) {
				return Hestia_Abilities::error( 'invalid_order', __( 'big_title is always the first section and cannot be ordered.', 'hestia' ) );
			}
			if ( ! isset( $sections[ $id ] ) ) {
				/* translators: %s: section id */
				return Hestia_Abilities::error( 'invalid_order', sprintf( __( 'Unknown section: %s.', 'hestia' ), $id ) );
			}
			if ( ! in_array( $id, $ordered, true ) ) {
				$ordered[] = $id;
			}
		}
		$ordered = array_merge( $ordered, array_diff( $current, $ordered ) );

		// Same priorities the customizer ordering script gives: 10, 15, 20...
		$order = array();
		foreach ( $ordered as $index => $id ) {
			$order[ 'hestia_' . $id ] = ( $index + 2 ) * 5;
		}

		$ordering = new Hestia_Section_Ordering();
		$value    = $ordering->sanitize_order( wp_json_encode( $order ) );
		if ( empty( $value ) ) {
			return Hestia_Abilities::error( 'invalid_order', __( 'Invalid sections order.', 'hestia' ) );
		}

		return $value;
	}

	/**
	 * Update the front page sections.
	 *
	 * @param array $input Ability input.
	 *
	 * @return array|WP_Error
	 */
	public function update_front_page_sections( $input = array() ) {
		$input    = is_array( $input ) ? $input : array();
		$sections = $this->get_sections();
		$locked   = $this->get_locked_keys( $sections );
		$mods     = array();

		if ( ! empty( $input['order'] ) ) {
			$order = $this->build_order( $input['order'] );
			if ( is_wp_error( $order ) ) {
				return $order;
			}
			$mods['sections_order'] = $order;
		}

		$updates = isset( $input['sections'] ) && is_array( $input['sections'] ) ? $input['sections'] : array();
		if ( count( $updates ) > count( $sections ) ) {
			return Hestia_Abilities::error( 'too_many_sections', __( 'Too many sections.', 'hestia' ) );
		}

		foreach ( $updates as $update ) {
			$id = is_array( $update ) && isset( $update['id'] ) ? sanitize_key( $update['id'] ) : '';
			if ( ! isset( $sections[ $id ] ) ) {
				/* translators: %s: section id */
				return Hestia_Abilities::error( 'invalid_section', sprintf( __( 'Unknown section: %s.', 'hestia' ), $id ) );
			}
			$section   = $sections[ $id ];
			$has_title = ! isset( $section['title'] ) || $section['title'] !== false;

			// Same lock the customizer puts on these settings without a valid license.
			if ( ! empty( $locked[ $id ] ) ) {
				$requested = array();
				foreach ( array( 'enabled', 'title', 'subtitle', 'items' ) as $name ) {
					if ( isset( $update[ $name ] ) ) {
						$requested[] = $name;
					}
				}
				if ( isset( $update['fields'] ) && is_array( $update['fields'] ) ) {
					$requested = array_merge( $requested, array_map( 'strval', array_keys( $update['fields'] ) ) );
				}
				$needs_license = array_intersect( $requested, $locked[ $id ] );
				if ( ! empty( $needs_license ) ) {
					if ( ! defined( 'HESTIA_PRO_FLAG' ) ) {
						$upgrade_url = tsdk_utmify( 'https://themeisle.com/themes/hestia/upgrade/', 'abilities', 'mcp' );
						return Hestia_Abilities::error(
							'pro_required',
							/* translators: 1: option names, 2: section id, 3: upgrade URL */
							sprintf( __( 'The option %1$s of the section %2$s needs Hestia Pro. Upgrade: %3$s', 'hestia' ), implode( ', ', $needs_license ), $id, $upgrade_url ),
							403,
							array( 'upgrade_url' => $upgrade_url )
						);
					}
					/* translators: 1: option names, 2: section id */
					return Hestia_Abilities::error( 'license_required', sprintf( __( 'The option %1$s of the section %2$s needs a valid Hestia Pro license.', 'hestia' ), implode( ', ', $needs_license ), $id ), 403 );
				}
			}

			if ( isset( $update['enabled'] ) ) {
				$mods[ 'hestia_' . $id . '_hide' ] = hestia_sanitize_checkbox( ! $update['enabled'] );
			}

			foreach ( array( 'title', 'subtitle' ) as $name ) {
				if ( ! isset( $update[ $name ] ) ) {
					continue;
				}
				if ( ! $has_title ) {
					/* translators: 1: field name, 2: section id */
					return Hestia_Abilities::error( 'invalid_field', sprintf( __( 'The field %1$s is not available for the section %2$s.', 'hestia' ), $name, $id ) );
				}
				$value = $this->sanitize_field( $name, 'html', $update[ $name ] );
				if ( is_wp_error( $value ) ) {
					return $value;
				}
				$mods[ 'hestia_' . $id . '_' . $name ] = $value;
			}

			if ( isset( $update['fields'] ) && is_array( $update['fields'] ) ) {
				foreach ( $update['fields'] as $name => $raw ) {
					if ( empty( $section['fields'][ $name ] ) ) {
						/* translators: 1: field name, 2: section id */
						return Hestia_Abilities::error( 'invalid_field', sprintf( __( 'The field %1$s is not available for the section %2$s.', 'hestia' ), $name, $id ) );
					}
					$field = $section['fields'][ $name ];
					$value = $this->sanitize_field( $name, $field[1], $raw );
					if ( is_wp_error( $value ) ) {
						return $value;
					}
					$mods[ $field[0] ] = $value;
				}
			}

			if ( isset( $update['items'] ) ) {
				if ( empty( $section['items'] ) ) {
					/* translators: %s: section id */
					return Hestia_Abilities::error( 'invalid_items', sprintf( __( 'The section %s has no items.', 'hestia' ), $id ) );
				}
				$items = $this->sanitize_items( $id, $update['items'] );
				if ( is_wp_error( $items ) ) {
					return $items;
				}
				$mods[ $section['items'] ] = $items;
			}
		}

		if ( empty( $mods ) ) {
			return Hestia_Abilities::error( 'nothing_to_update', __( 'Nothing to update.', 'hestia' ) );
		}

		$dry_run = ! empty( $input['dry_run'] );
		if ( ! $dry_run ) {
			foreach ( $mods as $theme_mod => $value ) {
				set_theme_mod( $theme_mod, $value );
			}
		}

		return array(
			'dry_run'  => $dry_run,
			'updated'  => array_keys( $mods ),
			'sections' => $this->format_sections( $dry_run ? $mods : array() ),
		);
	}
}
