<?php

class TheGem_Template_Element_Loop_Product_Add_To_Cart extends TheGem_Single_Post_Template_Element {
	public function __construct() {
	}

	public function get_name() {
		return 'thegem_te_loop_product_add_to_cart';
	}

	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'loop-item') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'loop-item') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'loop-item') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'loop-item') {
				$activate = true;
			}
			if(!empty($_REQUEST['action']) && !empty($_REQUEST['post_ID']) && $_REQUEST['action'] === 'editpost') {
				$activate = true;
			}
			if(!empty($_REQUEST['action']) && $_REQUEST['action'] === 'thegem_importer_get_import_step_finalize') {
				$activate = true;
			}
			if($activate) {
				$shortcodes[$this->get_name()] = $this->shortcode_settings();
			}
		}
		return $shortcodes;
	}

	public function shortcode_output($atts, $content = '') {
		if(!is_array($atts)) $atts =array();
		// General params
		$params = shortcode_atts(array_merge(array(
			'add_to_cart_type' => 'buttons',
			'button_alignment' => 'center',
			'cart_button_show_icon' => 1,
			'cart_button_text' => __('Add To Cart', 'thegem'),
			'cart_button_icon_pack' => 'elegant',
			'cart_button_icon_elegant' => '',
			'select_options_button_text' => __('Select Options', 'thegem'),
			'select_options_icon_pack' => 'elegant',
			'select_options_icon_elegant' => '',
			'button_text_style' => '',
			'button_text_weight' => 'normal'
		), $atts), 'thegem_te_loop_product_add_to_cart');

		// Init Design Options Params
		$uniqid = uniqid('thegem-custom-').rand(1,9999);

		// Init Price
		ob_start();
		$product = thegem_templates_init_product();

		if (empty($product)) {
			ob_end_clean();
			return thegem_templates_close_product($this->get_name(), $this->shortcode_settings(), '');
		}

		?>

		<div <?php if (!empty($params['element_id'])): ?>id="<?=esc_attr($params['element_id']); ?>"<?php endif;?>
			 class="thegem-te-loop-product-add-to-cart <?= esc_attr($uniqid); ?>"
			 <?=thegem_data_editor_attribute($uniqid.'-editor')?>>
			<?php if ($product->is_in_stock()) { $this->thegem_get_add_to_cart($product, '', $params, false); } ?>
		</div>

		<?php
		//Custom Styles
		$customize = '.thegem-te-loop-product-add-to-cart.'.$uniqid;
		$custom_css = '';

		$button_text_desktop_styles = array();
		$button_text_tablet_styles = array();
		$button_text_mobile_styles = array();

		if(isset($atts['button_text_size']) && $atts['button_text_size'] !== '') {
			$button_text_desktop_styles['font-size'] = intval($atts['button_text_size']).'px';
		}
		if(isset($atts['button_text_size_tablet']) && $atts['button_text_size_tablet'] !== '') {
			$button_text_tablet_styles['font-size'] = intval($atts['button_text_size_tablet']).'px';
		}
		if(isset($atts['button_text_size_mobile']) && $atts['button_text_size_mobile'] !== '') {
			$button_text_mobile_styles['font-size'] = intval($atts['button_text_size_mobile']).'px';
		}

		if(isset($atts['button_letter_spacing']) && $atts['button_letter_spacing'] !== '') {
			$button_text_desktop_styles['letter-spacing'] = $atts['button_letter_spacing'].'px';
		}

		if(isset($atts['button_text_transform']) && $atts['button_text_transform'] !== '') {
			$button_text_desktop_styles['text-transform'] = $atts['button_text_transform'];
		}

		if($button_text_desktop_styles && function_exists('thegem_generate_css')) {
			$custom_css .= thegem_generate_css(array('rules' => array(array(
 				'selector' => $customize . ' .cart.type_button .button .button-text, ' . $customize . ' .cart.type_button .button i',
 				'styles' => $button_text_desktop_styles
			))));
		}
		if($button_text_tablet_styles && function_exists('thegem_generate_css')) {
			$custom_css .= thegem_generate_css(array('media' => '(max-width: 1023px)', 'rules' => array(array(
				'selector' => $customize . ' .cart.type_button .button .button-text, ' . $customize . ' .cart.type_button .button i',
				'styles' => $button_text_tablet_styles
			))));
		}
		if($button_text_mobile_styles && function_exists('thegem_generate_css')) {
			$custom_css .= thegem_generate_css(array('media' => '(max-width: 767px)', 'rules' => array(array(
				'selector' => $customize . ' .cart.type_button .button .button-text, ' . $customize . ' .cart.type_button .button i',
				'styles' => $button_text_mobile_styles
			))));
		}

		$button_spacing_desktop_styles = array();
		$button_spacing_tablet_styles = array();
		$button_spacing_mobile_styles = array();

		if(isset($atts['button_icon_spacing']) && $atts['button_icon_spacing'] !== '') {
			$button_spacing_desktop_styles['width'] = intval($atts['button_icon_spacing']).'px';
		}
		if(isset($atts['button_icon_spacing_tablet']) && $atts['button_icon_spacing_tablet'] !== '') {
			$button_spacing_tablet_styles['width'] = intval($atts['button_icon_spacing_tablet']).'px';
		}
		if(isset($atts['button_icon_spacing_mobile']) && $atts['button_icon_spacing_mobile'] !== '') {
			$button_spacing_mobile_styles['width'] = intval($atts['button_icon_spacing_mobile']).'px';
		}

		if($button_spacing_desktop_styles && function_exists('thegem_generate_css')) {
			$custom_css .= thegem_generate_css(array('rules' => array(array(
				'selector' => $customize . ' .cart.type_button .button .space',
				'styles' => $button_spacing_desktop_styles
			))));
		}
		if($button_spacing_tablet_styles && function_exists('thegem_generate_css')) {
			$custom_css .= thegem_generate_css(array('media' => '(max-width: 1023px)', 'rules' => array(array(
				'selector' => $customize . ' .cart.type_button .button .space',
				'styles' => $button_spacing_tablet_styles
			))));
		}
		if($button_spacing_mobile_styles && function_exists('thegem_generate_css')) {
			$custom_css .= thegem_generate_css(array('media' => '(max-width: 767px)', 'rules' => array(array(
				'selector' => $customize . ' .cart.type_button .button .space',
				'styles' => $button_spacing_mobile_styles
			))));
		}

		$button_desktop_styles = array();
		$button_tablet_styles = array();
		$button_mobile_styles = array();

		if(isset($atts['button_border_width']) && $atts['button_border_width'] !== '') {
			$button_desktop_styles['border-width'] = intval($atts['button_border_width']).'px';
		}
		if(isset($atts['button_border_width_tablet']) && $atts['button_border_width_tablet'] !== '') {
			$button_tablet_styles['border-width'] = intval($atts['button_border_width_tablet']).'px';
		}
		if(isset($atts['button_border_width_mobile']) && $atts['button_border_width_mobile'] !== '') {
			$button_mobile_styles['border-width'] = intval($atts['button_border_width_mobile']).'px';
		}
		if(isset($atts['button_border_radius']) && $atts['button_border_radius'] !== '') {
			$button_desktop_styles['border-radius'] = intval($atts['button_border_radius']).'px';
		}
		if(isset($atts['button_border_radius_tablet']) && $atts['button_border_radius_tablet'] !== '') {
			$button_tablet_styles['border-radius'] = intval($atts['button_border_radius_tablet']).'px';
		}
		if(isset($atts['button_border_radius_mobile']) && $atts['button_border_radius_mobile'] !== '') {
			$button_mobile_styles['border-radius'] = intval($atts['button_border_radius_mobile']).'px';
		}

		foreach(array('left', 'right', 'top', 'bottom') as $dir) {
			if(isset($atts['button_padding_'.$dir]) && $atts['button_padding_'.$dir] !== '') {
				$button_desktop_styles['padding-'.$dir] = intval($atts['button_padding_'.$dir]).'px';
			}
			if(isset($atts['button_padding_'.$dir.'_tablet']) && $atts['button_padding_'.$dir.'_tablet'] !== '') {
				$button_tablet_styles['padding-'.$dir] = intval($atts['button_padding_'.$dir.'_tablet']).'px';
			}
			if(isset($atts['button_padding_'.$dir.'_mobile']) && $atts['button_padding_'.$dir.'_mobile'] !== '') {
				$button_mobile_styles['padding-'.$dir] = intval($atts['button_padding_'.$dir.'_mobile']).'px';
			}
		}

		if($button_desktop_styles && function_exists('thegem_generate_css')) {
			$custom_css .= thegem_generate_css(array('rules' => array(array(
				'selector' => $customize . ' .cart.type_button .button',
				'styles' => $button_desktop_styles
			))));
		}
		if($button_tablet_styles && function_exists('thegem_generate_css')) {
			$custom_css .= thegem_generate_css(array('media' => '(max-width: 1023px)', 'rules' => array(array(
				'selector' => $customize . ' .cart.type_button .button',
				'styles' => $button_tablet_styles
			))));
		}
		if($button_mobile_styles && function_exists('thegem_generate_css')) {
			$custom_css .= thegem_generate_css(array('media' => '(max-width: 767px)', 'rules' => array(array(
				'selector' => $customize . ' .cart.type_button .button',
				'styles' => $button_mobile_styles
			))));
		}

		if (isset($params['button_cart_color_normal']) && $params['button_cart_color_normal'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_simple { color: " . $params['button_cart_color_normal'] . " }";
		}

		if (isset($params['button_cart_color_hover']) && $params['button_cart_color_hover'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_simple:hover { color: " . $params['button_cart_color_hover'] . " }";
		}

		if (isset($params['button_cart_background_color_normal']) && $params['button_cart_background_color_normal'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_simple { background-color: " . $params['button_cart_background_color_normal'] . " }";
		}

		if (isset($params['button_cart_background_color_hover']) && $params['button_cart_background_color_hover'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_simple:hover { background-color: " . $params['button_cart_background_color_hover'] . " }";
		}

		if (isset($params['button_cart_border_color_normal']) && $params['button_cart_border_color_normal'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_simple { border-color: " . $params['button_cart_border_color_normal'] . " }";
		}

		if (isset($params['button_cart_border_color_hover']) && $params['button_cart_border_color_hover'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_simple:hover { border-color: " . $params['button_cart_border_color_hover'] . " }";
		}

		if (isset($params['button_options_color_normal']) && $params['button_options_color_normal'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_variable { color: " . $params['button_options_color_normal'] . " }";
		}

		if (isset($params['button_options_color_hover']) && $params['button_options_color_hover'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_variable:hover { color: " . $params['button_options_color_hover'] . " }";
		}

		if (isset($params['button_options_background_color_normal']) && $params['button_options_background_color_normal'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_variable { background-color: " . $params['button_options_background_color_normal'] . " }";
		}

		if (isset($params['button_options_background_color_hover']) && $params['button_options_background_color_hover'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_variable:hover { background-color: " . $params['button_options_background_color_hover'] . " }";
		}

		if (isset($params['button_options_border_color_normal']) && $params['button_options_border_color_normal'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_variable { border-color: " . $params['button_options_border_color_normal'] . " }";
		}

		if (isset($params['button_options_border_color_hover']) && $params['button_options_border_color_hover'] != '') {
			$custom_css .= $customize . " .cart.type_button .button.product_type_variable:hover { border-color: " . $params['button_options_border_color_hover'] . " }";
		}

		if (isset($params['icon_color_normal']) && $params['icon_color_normal'] != '') {
			$custom_css .= $customize . " .cart.icon a { color: " . $params['icon_color_normal'] . " }";
		}

		if (isset($params['icon_color_hover']) && $params['icon_color_hover'] != '') {
			$custom_css .= $customize . " .cart.icon a:hover { color: " . $params['icon_color_hover'] . " }";
		}

		if (isset($params['icon_background_color_normal']) && $params['icon_background_color_normal'] != '') {
			$custom_css .= $customize . " .cart.icon a { background-color: " . $params['icon_background_color_normal'] . " }";
		}

		if (isset($params['icon_background_color_hover']) && $params['icon_background_color_hover'] != '') {
			$custom_css .= $customize . " .cart.icon a:hover { background-color: " . $params['icon_background_color_hover'] . " }";
		}

		if (isset($params['icon_border_color_normal']) && $params['icon_border_color_normal'] != '') {
			$custom_css .= $customize . " .cart.icon a { border-color: " . $params['icon_border_color_normal'] . " }";
		}

		if (isset($params['icon_border_color_hover']) && $params['icon_border_color_hover'] != '') {
			$custom_css .= $customize . " .cart.icon a:hover { border-color: " . $params['icon_border_color_hover'] . " }";
		}

		if (isset($params['icon_border_width']) && $params['icon_border_width'] != '') {
			$custom_css .= $customize . " .cart.icon a { border-width: " . $params['icon_border_width'] . "px; border-style: solid }";
		}

		$return_html = trim(preg_replace('/\s\s+/', ' ', ob_get_clean()));

		// Print custom css
		$css_output = '';
		if(!empty($custom_css)) {
			$css_output = '<style>'.$custom_css.'</style>';
		}

		$return_html = $css_output.$return_html;
		return thegem_templates_close_product($this->get_name(), $this->shortcode_settings(), $return_html);
	}

	public function thegem_get_add_to_cart_icon_text($params, $type = 'simple') {
		$icon = '';
		if ($params['add_to_cart_type'] == 'icon' || $params['cart_button_show_icon'] == 'yes' || $params['cart_button_show_icon'] == '1') {
			$pack = $type == 'variable' ? 'select_options_icon_pack' : 'cart_button_icon_pack';
			$type_icon = $type == 'variable' ? 'select_options_icon_' : 'cart_button_icon_';
			if (isset($params[$pack]) && !empty($params[$type_icon . $params[$pack]])) {
				$icon = thegem_build_icon($params[$pack], $params[$type_icon . $params[$pack]]);
			} else {
				$icon = '<i class="default"></i>';
			}
			if ($params['add_to_cart_type'] == 'buttons') {
				$icon .= '<span class="space"></span>';
			}
		}
		$text = esc_html($type == 'variable' ? $params['select_options_button_text'] : $params['cart_button_text']);
		if ($params['add_to_cart_type'] == 'buttons') {
			$button_text_class = implode(' ', array('button-text', $params['button_text_style'], $params['button_text_weight']));
			$text = '<span class="' . esc_attr($button_text_class) . '">' .$text . '</span>';
		}

		return $icon . $text;
	}

	public function thegem_get_add_to_cart_link($product, $params, $add_to_cart_args) {
		if (!isset($params['cart_hook']) || $params['cart_hook'] == '1' || $params['cart_hook'] == 'yes') {
			woocommerce_template_loop_add_to_cart($add_to_cart_args);
		} else {
			$defaults = array(
				'quantity'   => 1,
				'class'      => implode(
					' ',
					array_filter(
						array(
							'button',
							wc_wp_theme_get_element_class_name( 'button' ), // escaped in the template.
							'product_type_' . $product->get_type(),
							$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
							$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
						)
					)
				),
				'attributes' => array(
					'data-product_id'  => $product->get_id(),
					'data-product_sku' => $product->get_sku(),
					'aria-label'       => $product->add_to_cart_description(),
					'rel'              => 'nofollow',
				),
			);
			$args = wp_parse_args( $add_to_cart_args, $defaults );
			if (!empty($args['attributes']['aria-describedby'])) {
				$args['attributes']['aria-describedby'] = wp_strip_all_tags($args['attributes']['aria-describedby']);
			}
			if (isset($args['attributes']['aria-label'])) {
				$args['attributes']['aria-label'] = wp_strip_all_tags($args['attributes']['aria-label']);
			}
			echo sprintf(
				'<a href="%s" data-quantity="%s" class="%s" %s>%s</a>',
				esc_url( $product->add_to_cart_url() ),
				esc_attr( isset( $args['quantity'] ) ? $args['quantity'] : 1 ),
				esc_attr( isset( $args['class'] ) ? $args['class'] : 'button' ),
				isset( $args['attributes'] ) ? wc_implode_html_attributes( $args['attributes'] ) : '',
				isset( $args['text'] ) ? $args['text'] : esc_html($product->add_to_cart_text())
			);
		}
	}

	public function thegem_get_add_to_cart($product, $add_to_cart_class, $params, $show_swatches) {
		$add_to_cart_args = [];
		$align = 'center';
		if($params['add_to_cart_type'] === 'icon' && isset($params['icon_alignment'])) {
			$align = $params['icon_alignment'];
		}
		if($params['add_to_cart_type'] === 'buttons' && isset($params['button_alignment'])) {
			$align = $params['button_alignment'];
		}
		$button_classes = implode(
			' ',
			array_filter(
				array(
					'cart',
					$params['add_to_cart_type'] == 'icon' ? 'icon' : 'type_button',
					$add_to_cart_class,
					'alignment-'.$align
				)
			)
		);

		if (in_array($product->get_type(), ['variable', 'external', 'grouped'])) {
			if ($product->get_type() === 'variable') {
				$add_to_cart_args['text'] = $this->thegem_get_add_to_cart_icon_text($params, 'variable');
			} ?>
			<span class="variable-type-button <?php echo esc_attr($button_classes); ?>">
				<?php $this->thegem_get_add_to_cart_link($product, $params, $add_to_cart_args); ?>
			</span>
			<?php if ($show_swatches) {
				$button_classes .= ' swatches-button';
			}
		}

		if ($product->get_type() === 'simple' || ($show_swatches && $product->get_type() === 'variable') || !in_array($product->get_type(), ['simple', 'variable', 'external', 'grouped'])) {
			$add_to_cart_args['text'] = $this->thegem_get_add_to_cart_icon_text($params); ?>
			<span class="simple-type-button <?php echo esc_attr($button_classes); ?>">
				<?php $this->thegem_get_add_to_cart_link($product, $params, $add_to_cart_args); ?>
			</span>
		<?php }
	}

	public function shortcode_settings() {

		return array(
			'name' => __('Product Add To Cart', 'thegem'),
			'base' => 'thegem_te_loop_product_add_to_cart',
			'icon' => 'thegem-icon-wpb-ui-element-product-add-to-cart',
			'category' => __('Loop Item Builder', 'thegem'),
			'description' => __('Product Add To Cart (Loop Item Builder)', 'thegem'),
			'params' => array_merge(
				array(
					array(
						'type' => 'dropdown',
						'heading' => __('Add To Card Type', 'thegem'),
						'param_name' => 'add_to_cart_type',
						'value' => array(
							__('Icon', 'thegem') => 'icon',
							__('Button', 'thegem') => 'buttons',
						),
						'std' => 'buttons',
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'checkbox',
						'heading' => __('Show Icon', 'thegem'),
						'param_name' => 'cart_button_show_icon',
						'value' => array(__('Yes', 'thegem') => '1'),
						'std' => '1',
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Alignment', 'thegem'),
						'param_name' => 'button_alignment',
						'value' => array(
							__('Left', 'thegem') => 'left',
							__('Center', 'thegem') => 'center',
							__('Right', 'thegem') => 'right',
							__('FullWidth', 'thegem') => 'fullwidth',
						),
						'std' => 'center',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Alignment', 'thegem'),
						'param_name' => 'icon_alignment',
						'value' => array(
							__('Left', 'thegem') => 'left',
							__('Center', 'thegem') => 'center',
							__('Right', 'thegem') => 'right',
						),
						'std' => 'center',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('icon')
						),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Add To Cart Button Text', 'thegem'),
						'param_name' => 'cart_button_text',
						'value' => __('Add To Cart', 'thegem'),
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'textfield',
						'heading' => __('Select Options Button Text', 'thegem'),
						'param_name' => 'select_options_button_text',
						'value' => __('Select Options', 'thegem'),
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Add To Card Icon pack', 'thegem'),
						'param_name' => 'cart_button_icon_pack',
						'value' => array_merge(array(
							__('Elegant', 'thegem') => 'elegant',
							__('Material Design', 'thegem') => 'material',
							__('FontAwesome', 'thegem') => 'fontawesome',
							__('Header Icons', 'thegem') => 'thegem-header',
							__('Additional', 'thegem') => 'thegemdemo'),
							thegem_userpack_to_dropdown()
						),
						'std' => 'elegant',
						'dependency' => array(
							'element' => 'cart_button_show_icon',
							'not_empty' => true
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Select Options Icon pack', 'thegem'),
						'param_name' => 'select_options_icon_pack',
						'value' => array_merge(array(
							__('Elegant', 'thegem') => 'elegant',
							__('Material Design', 'thegem') => 'material',
							__('FontAwesome', 'thegem') => 'fontawesome',
							__('Header Icons', 'thegem') => 'thegem-header',
							__('Additional', 'thegem') => 'thegemdemo'),
							thegem_userpack_to_dropdown()
						),
						'std' => 'elegant',
						'dependency' => array(
							'element' => 'cart_button_show_icon',
							'not_empty' => true
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'cart_button_icon_elegant',
						'icon_pack' => 'elegant',
						'dependency' => array(
							'element' => 'cart_button_icon_pack',
							'value' => array('elegant')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'cart_button_icon_material',
						'icon_pack' => 'material',
						'dependency' => array(
							'element' => 'cart_button_icon_pack',
							'value' => array('material')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'cart_button_icon_fontawesome',
						'icon_pack' => 'fontawesome',
						'dependency' => array(
							'element' => 'cart_button_icon_pack',
							'value' => array('fontawesome')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'cart_button_icon_thegemheader',
						'icon_pack' => 'thegem-header',
						'dependency' => array(
							'element' => 'cart_button_icon_pack',
							'value' => array('thegem-header')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'cart_button_icon_thegemdemo',
						'icon_pack' => 'thegemdemo',
						'dependency' => array(
							'element' => 'cart_button_icon_pack',
							'value' => array('thegemdemo')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
				),
				thegem_userpack_to_shortcode(array(
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'cart_button_icon_userpack',
						'icon_pack' => 'userpack',
						'dependency' => array(
							'element' => 'cart_button_icon_pack',
							'value' => array('userpack')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
				)),
				array(
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'select_options_icon_elegant',
						'icon_pack' => 'elegant',
						'dependency' => array(
							'element' => 'select_options_icon_pack',
							'value' => array('elegant')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'select_options_icon_material',
						'icon_pack' => 'material',
						'dependency' => array(
							'element' => 'select_options_icon_pack',
							'value' => array('material')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'select_options_icon_fontawesome',
						'icon_pack' => 'fontawesome',
						'dependency' => array(
							'element' => 'select_options_icon_pack',
							'value' => array('fontawesome')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'select_options_icon_thegemheader',
						'icon_pack' => 'thegem-header',
						'dependency' => array(
							'element' => 'select_options_icon_pack',
							'value' => array('thegem-header')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'select_options_icon_thegemdemo',
						'icon_pack' => 'thegemdemo',
						'dependency' => array(
							'element' => 'select_options_icon_pack',
							'value' => array('thegemdemo')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
				),
				thegem_userpack_to_shortcode(array(
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'select_options_icon_userpack',
						'icon_pack' => 'userpack',
						'dependency' => array(
							'element' => 'select_options_icon_pack',
							'value' => array('userpack')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
				)),
				array(
					array(
						'type' => 'thegem_delimeter_heading',
						'heading' => __('Button Text Style', 'thegem'),
						'param_name' => 'button_text_styles_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading vc_column vc_col-sm-12 capitalize',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Text style', 'thegem'),
						'param_name' => 'button_text_style',
						'value' => array(
							__('Default', 'thegem') => '',
							__('Title H1', 'thegem') => 'title-h1',
							__('Title H2', 'thegem') => 'title-h2',
							__('Title H3', 'thegem') => 'title-h3',
							__('Title H4', 'thegem') => 'title-h4',
							__('Title H5', 'thegem') => 'title-h5',
							__('Title H6', 'thegem') => 'title-h6',
							__('Title xLarge', 'thegem') => 'title-xlarge',
							__('Styled Subtitle', 'thegem') => 'styled-subtitle',
							__('Main Menu', 'thegem') => 'title-main-menu',
							__('Body', 'thegem') => 'title-text-body',
							__('Tiny Body', 'thegem') => 'title-text-body-tiny',
						),
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Text weight', 'thegem'),
						'param_name' => 'button_text_weight',
						'value' => array(__('Normal', 'thegem') => 'normal', __('Thin', 'thegem') => 'light'),
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Letter Spacing', 'thegem'),
						'param_name' => 'button_letter_spacing',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Text Transform', 'thegem'),
						'param_name' => 'button_text_transform',
						'value' => array(
							__('Default', 'thegem') => '',
							__('Capitalize', 'thegem') => 'capitalize',
							__('Lowercase', 'thegem') => 'lowercase',
							__('Uppercase', 'thegem') => 'uppercase',
							__('None', 'thegem') => 'none',
						),
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => __('Text Size', 'thegem'),
						'param_name' => 'button_text_size_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Size on Desktop (px)', 'thegem'),
						'param_name' => 'button_text_size',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Size on Tablet (px)', 'thegem'),
						'param_name' => 'button_text_size_tablet',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Size on Mobile (px) ', 'thegem'),
						'param_name' => 'button_text_size_mobile',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => __('Icon Spacing', 'thegem'),
						'param_name' => 'button_icon_spacing_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Desktop (px)', 'thegem'),
						'param_name' => 'button_icon_spacing',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Tablet (px)', 'thegem'),
						'param_name' => 'button_icon_spacing_tablet',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Mobile (px) ', 'thegem'),
						'param_name' => 'button_icon_spacing_mobile',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading',
						'heading' => __('Button Border Style', 'thegem'),
						'param_name' => 'button_border_styles_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12 capitalize',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => __('Border Width', 'thegem'),
						'param_name' => 'button_border_width_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Desktop (px)', 'thegem'),
						'param_name' => 'button_border_width',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Tablet (px)', 'thegem'),
						'param_name' => 'button_border_width_tablet',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Mobile (px) ', 'thegem'),
						'param_name' => 'button_border_width_mobile',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => __('Border Radius', 'thegem'),
						'param_name' => 'button_border_radius_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Desktop (px)', 'thegem'),
						'param_name' => 'button_border_radius',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Tablet (px)', 'thegem'),
						'param_name' => 'button_border_radius_tablet',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Mobile (px) ', 'thegem'),
						'param_name' => 'button_border_radius_mobile',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading',
						'heading' => __('Button Paddings', 'thegem'),
						'param_name' => 'button_paddings_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12 capitalize',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => __('Desktop', 'thegem'),
						'param_name' => 'button_paddings_desktop_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Left', 'thegem'),
						'param_name' => 'button_padding_left',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Right', 'thegem'),
						'param_name' => 'button_padding_right',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Top', 'thegem'),
						'param_name' => 'button_padding_top',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Bottom', 'thegem'),
						'param_name' => 'button_padding_bottom',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => __('Tablet', 'thegem'),
						'param_name' => 'button_paddings_tablet_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Left', 'thegem'),
						'param_name' => 'button_padding_left_tablet',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Right', 'thegem'),
						'param_name' => 'button_padding_right_tablet',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Top', 'thegem'),
						'param_name' => 'button_padding_top_tablet',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Bottom', 'thegem'),
						'param_name' => 'button_padding_bottom_tablet',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => __('Mobile', 'thegem'),
						'param_name' => 'button_paddings_mobile_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Left', 'thegem'),
						'param_name' => 'button_padding_left_mobile',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Right', 'thegem'),
						'param_name' => 'button_padding_right_mobile',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Top', 'thegem'),
						'param_name' => 'button_padding_top_mobile',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Bottom', 'thegem'),
						'param_name' => 'button_padding_bottom_mobile',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-3',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading',
						'heading' => __('Button Colors', 'thegem'),
						'param_name' => 'button_colors_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12 capitalize',
						'group' => __('Style', 'thegem'),
					),
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => __('Add to Cart button', 'thegem'),
						'param_name' => 'add_to_cart_colors_sub_delim_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Text Color', 'thegem'),
						'param_name' => 'button_cart_color_normal',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Text Color on hover', 'thegem'),
						'param_name' => 'button_cart_color_hover',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Background Color', 'thegem'),
						'param_name' => 'button_cart_background_color_normal',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Background Color on hover', 'thegem'),
						'param_name' => 'button_cart_background_color_hover',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Border Color', 'thegem'),
						'param_name' => 'button_cart_border_color_normal',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Border Color on hover', 'thegem'),
						'param_name' => 'button_cart_border_color_hover',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => __('Select Options button', 'thegem'),
						'param_name' => 'select_options_colors_sub_delim_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Text Color', 'thegem'),
						'param_name' => 'button_options_color_normal',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Text Color on hover', 'thegem'),
						'param_name' => 'button_options_color_hover',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Background Color', 'thegem'),
						'param_name' => 'button_options_background_color_normal',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Background Color on hover', 'thegem'),
						'param_name' => 'button_options_background_color_hover',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Border Color', 'thegem'),
						'param_name' => 'button_options_border_color_normal',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Border Color on hover', 'thegem'),
						'param_name' => 'button_options_border_color_hover',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('buttons')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'thegem_delimeter_heading',
						'heading' => __('Icon Style', 'thegem'),
						'param_name' => 'icon_style_sub_delim_head',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('icon')
						),
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level vc_column vc_col-sm-12',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Icon Color', 'thegem'),
						'param_name' => 'icon_color_normal',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('icon')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Icon Color on hover', 'thegem'),
						'param_name' => 'icon_color_hover',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('icon')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Icon Background Color', 'thegem'),
						'param_name' => 'icon_background_color_normal',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('icon')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Icon Background Color on hover', 'thegem'),
						'param_name' => 'icon_background_color_hover',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('icon')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Icon Border Color', 'thegem'),
						'param_name' => 'icon_border_color_normal',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('icon')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Icon Border Color on hover', 'thegem'),
						'param_name' => 'icon_border_color_hover',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('icon')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
					array(
						'type' => 'textfield',
						'heading' => __('Border Width', 'thegem'),
						'param_name' => 'icon_border_width',
						'dependency' => array(
							'element' => 'add_to_cart_type',
							'value' => array('icon')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => __('Style', 'thegem')
					),
				)
			),
		);
	}
}

$templates_elements['thegem_te_loop_product_add_to_cart'] = new TheGem_Template_Element_Loop_Product_Add_To_Cart();
$templates_elements['thegem_te_loop_product_add_to_cart']->init();
