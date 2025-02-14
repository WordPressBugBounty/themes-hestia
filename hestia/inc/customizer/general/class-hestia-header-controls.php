<?php
/**
 * Handle Header Controls.
 *
 * @package Hestia
 */

/**
 * Class Hestia_Header_Controls
 */
class Hestia_Header_Controls extends Hestia_Register_Customizer_Controls {

	/**
	 * Actions needed to run before the controls are added.
	 */
	public function before_add_controls() {
		$this->register_type( 'Hestia_Customize_Control_Radio_Image', 'control' );
	}

	/**
	 * Add the customizer controls.
	 */
	public function add_controls() {
		$this->add_main_panel();
		$this->add_top_bar_options();
		$this->add_navigation_options();
		$this->add_header_options();
		$this->add_mobile_menu_icon_options();
	}

	/**
	 * Add main panel for Header controls.
	 */
	private function add_main_panel() {
		$this->add_panel(
			new Hestia_Customizer_Panel(
				'hestia_header_options',
				array(
					'priority' => 35,
					'title'    => esc_html__( 'Header Options', 'hestia' ),
				)
			)
		);
	}

	/**
	 * Add customizer controls for the top bar area.
	 */
	private function add_top_bar_options() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_top_bar',
				array(
					'title'    => esc_html__( 'Very Top Bar', 'hestia' ),
					'panel'    => 'hestia_header_options',
					'priority' => 10,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_top_bar_hide',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => true,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Disable section', 'hestia' ),
					'section'  => 'hestia_top_bar',
					'priority' => 5,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_link_to_top_menu',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'container_class' => 'quick-links',
					'text_before'     => '<span class="dashicons dashicons-info" style="margin-right: 3px"></span>' . __( 'Customize the Very Top Bar Menu', 'hestia' ),
					'text_after'      => '.',
					'button_text'     => __( 'here', 'hestia' ),
					'is_button'       => false,
					'focus_type'      => 'section',
					'focus'           => 'menu_locations',
					'shortcut'        => true,
					'section'         => 'hestia_top_bar',
					'priority'        => 1000,
				),
				'Hestia_Button'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_link_to_top_widgets',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'container_class' => 'quick-links',
					'text_before'     => '<span class="dashicons dashicons-info" style="margin-right: 3px"></span>' . __( 'Edit the Very Top Bar Widgets', 'hestia' ),
					'text_after'      => '.',
					'button_text'     => __( 'here', 'hestia' ),
					'is_button'       => false,
					'focus_type'      => 'section',
					'focus'           => 'sidebar-widgets-sidebar-top-bar',
					'shortcut'        => true,
					'section'         => 'hestia_top_bar',
					'priority'        => 1000,
				),
				'Hestia_Button'
			)
		);

		if ( ! class_exists( 'Hestia_Addon_Manager' ) ) {
			$this->add_top_bar_options_upsell();
		}
	}

	/**
	 * Add top bar options upsell.
	 *
	 * @return void
	 */
	private function add_top_bar_options_upsell() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_very_top_bar_tabs',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'section'  => 'hestia_top_bar',
					'priority' => 1,
					'tabs'     => array(
						'general'    => array(
							'label' => esc_html__( 'General Settings', 'hestia' ),
						),
						'appearance' => array(
							'label' => esc_html__( 'Appearance Settings', 'hestia' ),
						),
					),
					'controls' => array(
						'general'    => array(
							'hestia_top_bar_hide'        => array(),
							'hestia_link_to_top_text_upgrade' => array(),
							'hestia_top_bar_alignment_upsell' => array(),
							'hestia_link_to_top_menu'    => array(),
							'hestia_link_to_top_widgets' => array(),
						),
						'appearance' => array(
							'hestia_link_to_top_text_upgrade'   => array(),
							'hestia_top_bar_text_color_upsell' => array(),
							'hestia_top_bar_link_color_upsell' => array(),
							'hestia_top_bar_link_color_hover_upsell' => array(),
							'hestia_top_bar_background_color_upsell' => array(),
						),
					),
				),
				'Hestia_Customize_Control_Tabs'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_top_bar_alignment_upsell',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Layout', 'hestia' ),
					'priority' => 25,
					'section'  => 'hestia_top_bar',
					'choices'  => array(
						'left'  => array(
							'url'   => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqCAMAAABpj1iyAAAAM1BMVEX///8Ahbojjr5mqMzU5O/f6/Pq8vf1+fs9l8NToMiGuNWjyN681ueVwNp3sNGwz+LI3evMEc51AAABPUlEQVR4Ae3RuYojSxhE4Ti5L1nL+z/tVdISNFwYY5hWy4jPCPjLOlTKzMzMzMzMzMzMzMzMzP61WvSJAllbjvETsxLof464fvUR8ysr6ylVSZGph5L1B3z3F/dL41KFuSdD0mq0A6QjECJR9QSOfba4Socwfz5r0rXYQxOkDDRAN7QAUZ12wvzKGpzjHVkFygmUwRSkSSgaoMEpBWKGlQZdkandeJX681nqXCGMx1AEaRClBF8VkXizvT7cAcJ6Q9YicN4Eup5/q+oATVq+IWa4UrqSIkPKZXX6G7IqsBT2CFKG0AHlwBbVCbFx64C2Qhsn4w1ZOqFq7BEkrQAdpHzEIzJUI3BWlQZzAL3oDUrKz1FKVVKuNSXpPNIFSw9J2nJ5zi+62XrVh0kzjktmZmZmZmZmZmZmZmZm9s1/51AJDRsfaTQAAAAASUVORK5CYII=',
							'label' => esc_html__( 'Left Sidebar', 'hestia' ),
						),
						'right' => array(
							'url'   => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqCAMAAABpj1iyAAAAM1BMVEX///8Ahbojjr5mqMzU5O/f6/Pq8vf1+fs9l8NToMiGuNWjyN681ueVwNp3sNGwz+LI3evMEc51AAABO0lEQVR4Ae3RuWodQRhE4Tq9Lz3L+z+tb6MrMFZm0EhBfUHBP9FhWmZmZmZmZmZmZmZmZmb2Uot+o0DWlmP8jVkJ9MUR148+Yv7MynpLVVJk6qVkPaBxqcLckyFpNdoB0hEIkah6Asc+W1ylQ5j6B3/7j/urSddiD02QMtAA3dACRHXaCfMja3COJ7IKlBMogylIk1A0QINTCsQMKw26IlO78Sr1+7PUuUIYr6EI0iBKCT4qIvFm+/xwBwjrgaxF4LwJdL3/VtUBmrR8Q8xwpXQlRYaUy+r0B7IqsBT2CFKG0AHlwBbVCbFx64C2Qhsn44EsnVA19giSVoAOUj7iERmqETirSoM5gF6eyCopv0cpVUm51pSk80gXLL0kacvlPT/oZutVv0yacVwyMzMzMzMzMzMzMzMzs+/yB9eOCQ0dpl58AAAAAElFTkSuQmCC',
							'label' => esc_html__( 'Right Sidebar', 'hestia' ),
						),
					),
				),
				'Hestia_Customize_Control_Radio_Image',
				array(
					'selector'        => '.hestia-top-bar',
					'settings'        => array( 'hestia_top_bar_alignment' ),
					'render_callback' => array( $this, 'top_bar_callback' ),
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_top_bar_background_color_upsell',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Background color', 'hestia' ),
					'section'  => 'hestia_top_bar',
					'priority' => 7,
				),
				'WP_Customize_Color_Control'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_top_bar_text_color_upsell',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Text', 'hestia' ) . ' ' . esc_html__( 'Color', 'hestia' ),
					'section'  => 'hestia_top_bar',
					'priority' => 10,
				),
				'WP_Customize_Color_Control'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_top_bar_link_color_upsell',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Link', 'hestia' ) . ' ' . esc_html__( 'Color', 'hestia' ),
					'section'  => 'hestia_top_bar',
					'priority' => 15,
				),
				'WP_Customize_Color_Control'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_top_bar_link_color_hover_upsell',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'label'    => esc_html__( 'Link color on hover', 'hestia' ),
					'section'  => 'hestia_top_bar',
					'priority' => 20,
				),
				'WP_Customize_Color_Control'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_link_to_top_text_upgrade',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'container_class' => 'upgrade-links',
					'text_before'     => sprintf(
						/* translators: %1$s is the Learn more link, %2$s is the section title */
						__( 'More Options Available for %1$s in the Pro version.', 'hestia' ),
						esc_html__( 'Very Top Bar', 'hestia' )
					),
					'link'            => tsdk_translate_link( tsdk_utmify( 'https://themeisle.com/themes/hestia-pro/upgrade/', 'very-top-bar' ), 'query' ),
					'button_text'     => __( 'Upgrade Now', 'hestia' ),
					'new_tab'         => true,
					'is_button'       => false,
					'shortcut'        => false,
					'section'         => 'hestia_top_bar',
					'focus_type'      => '',
					'priority'        => 6,
					'button_class'    => 'button button-primary',
				),
				'Hestia_Button'
			)
		);
	}

	/**
	 * Add customizer controls for the navigation.
	 */
	private function add_navigation_options() {
		$this->add_section(
			new Hestia_Customizer_Section(
				'hestia_navigation',
				array(
					'title'    => esc_html__( 'Navigation', 'hestia' ),
					'panel'    => 'hestia_header_options',
					'priority' => 15,
				)
			)
		);

		if ( ! class_exists( '\Hestia_Addon_Manager' ) ) {
			$description = sprintf(
				/* translators: %1$s is the Learn more link, %2$s is the section title */
				__( 'More Options Available for %1$s in the PRO version. %2$s', 'hestia' ),
				esc_html__( 'Navigation', 'hestia' ),
				/* translators: %s is the Learn more label*/
				sprintf(
					'<a class="button button-primary" target="_blank" href="' . tsdk_translate_link( tsdk_utmify( 'https://themeisle.com/themes/hestia-pro/upgrade/', 'navigation' ), 'query' ) . '" style="display: block; clear: both; width: fit-content; margin: 15px 0;">%s</a>',
					__( 'Upgrade to Unlock', 'hestia' )
				)
			);
			$this->add_control(
				new Hestia_Customizer_Control(
					'hestia_navigation_upsell_notice',
					array(
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'section'     => 'hestia_navigation',
						'description' => $description . '<hr style="margin-left: 0px; border-bottom: none;">',
						'priority'    => 1,
						'type'        => 'hidden',
					)
				)
			);
		}

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_navbar_transparent',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => apply_filters( 'hestia_navbar_transparent_default', true ),
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Transparent Navbar', 'hestia' ),
					'section'  => 'hestia_navigation',
					'priority' => 15,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_navbar_sticky',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => apply_filters( 'hestia_navbar_sticky_default', true ),
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Sticky Navbar', 'hestia' ),
					'section'  => 'hestia_navigation',
					'priority' => 16,
				)
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_search_in_menu',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Enable Search in Menu', 'hestia' ),
					'section'  => 'hestia_navigation',
					'priority' => 5,
				)
			)
		);

		if ( class_exists( '\WooCommerce', false ) ) {
			$this->add_control(
				new Hestia_Customizer_Control(
					'hestia_cart_icon_status',
					array(
						'sanitize_callback' => 'hestia_sanitize_checkbox',
						'default'           => false,
					),
					array(
						'type'     => 'checkbox',
						'label'    => esc_html__( 'Customize Mini Cart', 'hestia' ),
						'section'  => 'hestia_navigation',
						'priority' => 20,
					)
				)
			);
		}

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_header_alignment',
				array(
					'default'           => apply_filters( 'hestia_header_alignment_default', 'left' ),
					'sanitize_callback' => 'hestia_sanitize_alignment_options',
				),
				array(
					'label'    => esc_html__( 'Layout', 'hestia' ),
					'priority' => 45,
					'section'  => 'hestia_navigation',
					'choices'  => apply_filters(
						'hestia_nav_layout_choices',
						array(
							'left'   => array(
								'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAADFBMVEX////V1dUAhbo+yP8aB7nsAAAARklEQVRYhWNgGEGA/z8EHKC+MqZVq1ah4xGtjOLgHQWjYBSMgmENREOxgoDhp2xkASIDYxgoGwUUACJDdlTZKBgFo2DAAAAZAflVkm2NtwAAAABJRU5ErkJggg==',
							),
							'center' => array(
								'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAADFBMVEX////V1dUAhbo+yP8aB7nsAAAARElEQVRYR2NgGKngPwT8GVU2BJQNb6C1atUCfHjEKRsFo2BEANFQrCBg+CkbWYDIwBgGykYBBYDIkB1VNgpGwSgYMAAAzIYr4wYj4sEAAAAASUVORK5CYII=',
							),
							'right'  => array(
								'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAD1BMVEX////V1dXF7v8Ahbo+yP+vNfjRAAAAWUlEQVR42u3TsQ2AMAxFQRAswAaIFViB/WeKlI4KgUJMwl3j7lXfA3+xXVvfas3HmZaWVtw/1mrRjmnPnl6tDlsAEcblFq2PtuhLyS1oxbWgjpIL1dICgEYlsKfbvyzuWeMAAAAASUVORK5CYII=',
							),
						)
					),
				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_link_to_nav_widgets',
				array(
					'sanitize_callback' => 'sanitize_text_field',
				),
				array(
					'container_class' => 'quick-links',
					'text_before'     => '<span class="dashicons dashicons-info" style="margin-right: 3px"></span>' . __( 'Edit the Navigation Widgets', 'hestia' ),
					'text_after'      => '.',
					'button_text'     => __( 'here', 'hestia' ),
					'is_button'       => false,
					'focus_type'      => 'section',
					'focus'           => 'sidebar-widgets-header-sidebar',
					'shortcut'        => true,
					'section'         => 'hestia_navigation',
					'priority'        => 1000,
				),
				'Hestia_Button'
			)
		);
	}

	/**
	 *  Add customizer controls for the header area.
	 */
	private function add_header_options() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_header_image_sitewide',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Enable Header Image Sitewide', 'hestia' ),
					'section'  => 'header_image',
					'priority' => 25,
				)
			)
		);

		$sidebar_choices = apply_filters(
			'hestia_header_layout_choices',
			array(
				'default'      => array(
					'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAD1BMVEU+yP/////Y9P/G7//V1dUbjhlcAAAAW0lEQVR4Ae3SAQmAYAyE0V9NMDCBCQxh/0wKGGCAIJ7vC3DA28ZvkjRVo49vzVujoeYFbF15i32pu4CtlCTVc+Vu2VqPRi9ssWfPnj179uzZs2fPnj179uwzt07LZ+4ImOW7JwAAAABJRU5ErkJggg==',
				),
				'no-content'   => array(
					'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAElBMVEU+yP////88SFhjbXl1fonV1dUUDrn8AAAAXElEQVR4Ae3SMQ2AYAyEUSwAYOC3gAJE4N8KCztNKEPT9wm44eUmSZL0b3NeXbeWEaj41noEet/yCVs+cW7jqfjW12ztV6D8Lfbs2bNnz549e/bs2bNnz559060bqAJ8azq5sAYAAAAASUVORK5CYII=',
				),
				'classic-blog' => array(
					'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqBAMAAACsf7WzAAAAElBMVEX///88SFhjbXl1fok+yP/V1dWks4cUAAAAXElEQVR4Ae3SMQ2AQBBE0QNAwFlAASKwgH8rNNSwCdfs5j0BU/xMo6ypByTfmveAxmd7Wz5xLP2Rf4tf1jPAli1btl7YsmWL7QoYuoX22lelvfbaa6892mufifbcjgr1IbRYbwEAAAAASUVORK5CYII=',
				),
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_header_layout',
				array(
					'sanitize_callback' => 'sanitize_key',
					'default'           => 'default',
				),
				array(
					'label'    => esc_html__( 'Posts/Pages Layout', 'hestia' ),
					'section'  => 'header_image',
					'priority' => 10,
					'choices'  => $sidebar_choices,
				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);

		$this->add_selective_refresh_to_header_items();

		$product_layout_choices = array(
			'no-content'   => array(
				'url' => 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAADFBMVEXV1dX///8+yP88SFiChfYKAAAAf0lEQVR42u2VwQ2AIAxF8cBeLOESLuEqnN3HUTwrpk0viKnaxFT/u4DNy8NwIUwaMjRo0O5ofYsB2rmWAjOuzGyhaQ+VmFVt2Q2Tmq1WNsIfa0SjJuNaS0dj1F6oRZae1/xqgl+tfNeg5rqm1Oi1tdJs/+3SvXW0RB5Dg/YBbQNZMbMYOvhmhQAAAABJRU5ErkJggg==',
			),
			'classic-blog' => array(
				'url' => 'data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAJYAAABqAgMAAAAjP0ATAAAACVBMVEX////V1dU8SFgcXJ18AAAAZElEQVR42mNgGAVDD4iGQoHWKihooIYyEi1tYKCWaQsIKyPSNOoqgzOwahk1bdS0wW5awCDNWfRUNpwK/FC8KWTUtCFkGpEAUttSS9kApt4QCBVAoHwbVTaqbFTZyFE2CgY/AADFX3Gl4BVG6wAAAABJRU5ErkJggg==',
			),
		);
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_product_layout',
				array(
					'sanitize_callback' => array( $this, 'sanitize_product_layout' ),
					'default'           => 'no-content',
				),
				array(
					'label'           => esc_html__( 'Products', 'hestia' ) . ' ' . esc_html__( 'Layout', 'hestia' ),
					'section'         => 'header_image',
					'priority'        => 12,
					'choices'         => $product_layout_choices,
					'active_callback' => array( $this, 'check_if_woo' ),

				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_logo_display',
				array(
					'type'              => 'theme_mod',
					'sanitize_callback' => 'sanitize_text_field',
					'default'           => 'only-logo',
				),
				array(
					'type'     => 'select',
					'label'    => esc_html__( 'Display', 'hestia' ),
					'section'  => 'title_tagline',
					'priority' => 7,
					'choices'  => array(
						'only-logo'   => esc_html__( 'Only Logo', 'hestia' ),
						'right-text'  => esc_html__( 'Logo With Right Text', 'hestia' ),
						'left-text'   => esc_html__( 'Logo With Left Text', 'hestia' ),
						'bottom-text' => esc_html__( 'Logo With Bottom Text', 'hestia' ),
					),

				),
				'Hestia_Customize_Control_Radio_Image'
			)
		);

		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_transparent_header_logo',
				array(
					'sanitize_callback' => 'absint',
					'transport'         => $this->selective_refresh,
				),
				array(
					'label'           => esc_html__( 'Transparent Header Logo', 'hestia' ),
					'section'         => 'title_tagline',
					'priority'        => 9,
					'active_callback' => array( $this, 'hestia_transparent_header_logo_callback' ),
					'flex_width'      => true,
					'flex_height'     => true,
					'height'          => 100,
				),
				'WP_Customize_Cropped_Image_Control'
			)
		);
	}

	/**
	 * Add selective refresh to header logo and site name.
	 */
	private function add_selective_refresh_to_header_items() {
		$this->get_customizer_object( 'setting', 'blogname' )->transport = 'postMessage';

		$this->add_partial(
			new Hestia_Customizer_Partial(
				'blogname',
				array(
					'selector'        => '.navbar .navbar-brand p',
					'settings'        => array( 'blogname' ),
					'render_callback' => array( $this, 'blog_name_callback' ),
				)
			)
		);

		$this->add_partial(
			new Hestia_Customizer_Partial(
				'custom_logo',
				array(
					'selector'        => '.navbar-brand',
					'settings'        => 'custom_logo',
					'render_callback' => array( $this, 'logo_callback' ),
				)
			)
		);

		$this->add_partial(
			new Hestia_Customizer_Partial(
				'hestia_transparent_header_logo',
				array(
					'selector'        => '.navbar-brand',
					'settings'        => 'hestia_transparent_header_logo',
					'render_callback' => array( $this, 'logo_callback' ),
				)
			)
		);
	}

	/**
	 * Change customizer controls.
	 */
	public function change_controls() {

		$header_image_section = $this->get_customizer_object( 'section', 'header_image' );
		if ( ! empty( $header_image_section ) ) {
			$header_image_section->title    = esc_html__( 'Header Settings', 'hestia' );
			$header_image_section->panel    = 'hestia_header_options';
			$header_image_section->priority = 20;
		}

		$this->get_customizer_object( 'setting', 'custom_logo' )->transport = 'postMessage';

		$header_image_control = $this->get_customizer_object( 'control', 'header_image' );
		if ( ! empty( $header_image_control ) ) {
			$header_image_control->priority = 15;
		}

		$header_image_data_control = $this->get_customizer_object( 'control', 'header_image_data' );
		if ( ! empty( $header_image_data_control ) ) {
			$header_image_data_control->priority = 20;
		}
	}

	/**
	 * Blog name callback function
	 *
	 * @return void
	 */
	public function blog_name_callback() {
		bloginfo( 'name' );
	}

	/**
	 * Custom logo callback function.
	 *
	 * @return string
	 */
	public function logo_callback() {
		return Hestia_Header::logo();
	}

	/**
	 * Check if WooCommerce is installed.
	 *
	 * @return bool
	 */
	public function check_if_woo() {
		return class_exists( 'WooCommerce', false );
	}

	/**
	 * Sanitize product page layout.
	 *
	 * @param string $layout Product page layout.
	 *
	 * @return string
	 */
	public function sanitize_product_layout( $layout ) {
		$allowed_values = array( 'no-content', 'classic-blog' );
		if ( ! in_array( $layout, $allowed_values, true ) ) {
			return 'no-content';
		}

		return $layout;
	}

	/**
	 * Active callback for the transparent logo
	 */
	public function hestia_transparent_header_logo_callback() {
		$transparent_navbar = get_theme_mod( 'hestia_navbar_transparent' );
		return $transparent_navbar === true;
	}

	/**
	 * Add mobile menu icon options.
	 */
	public function add_mobile_menu_icon_options() {
		$this->add_control(
			new Hestia_Customizer_Control(
				'hestia_mobile_menu_icon_status',
				array(
					'sanitize_callback' => 'hestia_sanitize_checkbox',
					'default'           => false,
				),
				array(
					'type'     => 'checkbox',
					'label'    => esc_html__( 'Enable Mobile Menu Icon', 'hestia' ),
					'section'  => 'hestia_navigation',
					'priority' => 35,
				)
			)
		);
	}
}
