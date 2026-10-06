<?php
// Shortcode function
add_shortcode('gem_hotspot', 'thegem_hotspot_shortcode');

function thegem_hotspot_shortcode($atts) {
	$atts = shortcode_atts(array(
		'image_id' => '',
		'image_size' => 'full',
		'tooltip_trigger' => 'hover',
		'tooltip_animation' => 'fade',
		'pulsating_effect' => '',
		
		// Image styles
		'image_border_radius' => '',
		'enable_shadow' => '',
		'shadow_color' => 'rgba(0, 0, 0, 0.15)',
		'shadow_horizontal' => '0',
		'shadow_vertical' => '0',
		'shadow_blur' => '5',
		'shadow_spread' => '0',

		// Marker styles
		'marker_size' => 20,
		'icon_size' => 10,
		'icon_gap' => 5,
		'marker_border_width' => '',
		'marker_border_color' => '',
		'marker_border_radius' => '',
		'marker_padding_top' => '',
		'marker_padding_bottom' => '',
		'marker_padding_left' => '',
		'marker_padding_right' => '',
		'label_text_preset' => '',
		'label_text_weight' => '',
		'marker_color' => '#ffffff',
		'marker_border_color' => '',
		'marker_icon_color' => '',
		'marker_label_color' => '',
		'marker_color_hover' => '',
		'marker_border_color_hover' => '',
		'marker_icon_color_hover' => '',
		'marker_label_color_hover' => '',
		'marker_icon_rotate_hover' => '',
		
		// Tooltip styles
		'tooltip_width' => 200,
		'tooltip_width_tablet' => '',
		'tooltip_width_mobile' => '',
		'tooltip_padding_top' => '',
		'tooltip_padding_bottom' => '',
		'tooltip_padding_left' => '',
		'tooltip_padding_right' => '',
		'tooltip_padding_tablet_top' => '',
		'tooltip_padding_tablet_bottom' => '',
		'tooltip_padding_tablet_left' => '',
		'tooltip_padding_tablet_right' => '',
		'tooltip_padding_mobile_top' => '',
		'tooltip_padding_mobile_bottom' => '',
		'tooltip_padding_mobile_left' => '',
		'tooltip_padding_mobile_right' => '',
		'tooltip_border_radius' => '',
		'tooltip_bg_color' => '#333333cc',
		'tooltip_text_color' => '#ffffff',
		'tooltip_title_text_preset' => '',
		'tooltip_title_font_weight' => '',
		'tooltip_enable_shadow' => '',
		'tooltip_shadow_color' => 'rgba(0, 0, 0, 0.15)',
		'tooltip_shadow_horizontal' => '0',
		'tooltip_shadow_vertical' => '0',
		'tooltip_shadow_blur' => '5',
		'tooltip_shadow_spread' => '0',
		'hotspot_items' => '',
		'el_class' => ''
	), $atts, 'gem_hotspot');

	// Get image URL
	$image_url = '';
	if (!empty($atts['image_id'])) {
		$image_url = wp_get_attachment_image_url($atts['image_id'], $atts['image_size']);
	}

	if (empty($image_url)) {
		return '<div class="bordered-box centered-box styled-subtitle">' . esc_html__('Please select an image for the Hotspot.', 'thegem') . '</div>';
	}

	$unique_id = 'thegem-hotspot-' . uniqid();

	// Parse hotspot items from param_group
	$hotspot_items = array();
	if (!empty($atts['hotspot_items'])) {
		$hotspot_items = vc_param_group_parse_atts($atts['hotspot_items']);
		if (!is_array($hotspot_items)) {
			$hotspot_items = array();
		}
	}

	// Generate inline CSS
	$inline_css = thegem_generate_hotspot_css($unique_id, $atts, $hotspot_items);

	ob_start();
	
	// Enqueue styles and scripts
	wp_enqueue_style('thegem-hotspot');
	wp_enqueue_script('thegem-hotspot');
	
	// Output CSS
	if (!empty($inline_css)) {
		echo '<style type="text/css">' . $inline_css . '</style>';
	}
	?>
	
	<div id="<?php echo esc_attr($unique_id); ?>" 
		class="thegem-hotspot-widget thegem-hotspot-trigger-<?php echo esc_attr($atts['tooltip_trigger']); ?> thegem-hotspot-animation-<?php echo esc_attr($atts['tooltip_animation']); ?> <?php echo ($atts['pulsating_effect'] === 'yes' ? 'thegem-hotspot-pulsating' : ''); ?> <?php echo esc_attr($atts['el_class']); ?>"
		data-trigger="<?php echo esc_attr($atts['tooltip_trigger']); ?>">
		
		<div class="thegem-hotspot-image-wrap">
			<img src="<?php echo esc_url($image_url); ?>" alt="<?php echo esc_attr__('Hotspot Image', 'thegem'); ?>">

			<?php if (!empty($hotspot_items)) : ?>
				<?php foreach ($hotspot_items as $index => $item) : 
					$item_id = 'hotspot-item-' . $index;
					$item_classes = array('thegem-hotspot-item');
					
					if (!empty($item['tooltip_position'])) {
						$item_classes[] = 'thegem-hotspot-position-' . esc_attr($item['tooltip_position']);
					}
					
					if (!empty($item['hotspot_css_class'])) {
						$item_classes[] = esc_attr($item['hotspot_css_class']);
					}
					
					// Marker Animation
					if (!empty($item['marker_animation']) && $item['marker_animation'] !== 'none') {
						$item_classes[] = 'thegem-hotspot-animated thegem-hotspot-animation-' . esc_attr($item['marker_animation']);
					}
					
					// Build attributes array
					$item_attrs = array();
					$item_attrs['class'] = implode(' ', $item_classes);
					
					if (!empty($item['hotspot_css_id'])) {
						$item_attrs['id'] = esc_attr($item['hotspot_css_id']);
					}
					
					// Position and animation styles
					$style_parts = array();
					$position_x = !empty($item['hotspot_position_x']) ? floatval($item['hotspot_position_x']) : 50;
					$position_y = !empty($item['hotspot_position_y']) ? floatval($item['hotspot_position_y']) : 50;
					$style_parts[] = "left: {$position_x}%";
					$style_parts[] = "top: {$position_y}%";
					
					// Animation Delay
					$delay = !empty($item['marker_animation_delay']) ? intval($item['marker_animation_delay']) : 0;
					if ($delay > 0) {
						$style_parts[] = "animation-delay: {$delay}ms";
					}
					
					if (!empty($style_parts)) {
						$item_attrs['style'] = implode('; ', $style_parts) . ';';
					}
					
					// Marker Content
					$marker_content = '';
					$is_link = !empty($item['hotspot_link']);

					if (isset($item['marker_icon_pack']) && isset($item['marker_icon_' . str_replace("-", "", $item['marker_icon_pack'])]) && $item['marker_icon_' . str_replace("-", "", $item['marker_icon_pack'])] != '') {
						wp_enqueue_style('icons-' . $item['marker_icon_pack']);
						$marker_content .= '<span class="thegem-hotspot-icon">' . thegem_build_icon($item['marker_icon_pack'], $item['marker_icon_' . str_replace("-", "", $item['marker_icon_pack'])]) . '</span>';
					}

					if (!empty($item['hotspot_label'])) {
						$label_classes = 'thegem-hotspot-label';
						if (!empty($atts['label_text_preset'])) {
							$label_classes .= ' ' . $atts['label_text_preset'];
						} else {
							$label_classes .= ' text-style-default';
						}
						if (!empty($atts['label_text_weight'])) {
							$label_classes .= ' ' . $atts['label_text_weight'];
						}
						$marker_content .= '<span class="' . esc_attr($label_classes) . '">' . esc_html($item['hotspot_label']) . '</span>';
					}
					
					// Tooltip Content
					$tooltip_content = '';
					if (!empty($item['hotspot_title'])) {
						$title_classes = 'thegem-hotspot-tooltip-title';
						if (!empty($atts['tooltip_title_text_preset'])) {
							$title_classes .= ' ' . $atts['tooltip_title_text_preset'];
						} else {
							$title_classes .= ' text-style-default';
						}
						if (!empty($atts['tooltip_title_font_weight'])) {
							$title_classes .= ' ' . $atts['tooltip_title_font_weight'];
						}
						$tooltip_content .= '<div class="' . esc_attr($title_classes) . '">' . esc_html($item['hotspot_title']) . '</div>';
					}

					if ($item['tooltip_content_type'] === 'template' && !empty($item['tooltip_template'])) {
						$section_content = do_shortcode('[gem_template id="'.intval($item['tooltip_template']).'"]');
						if ($section_content) {
							$tooltip_content .= '<div class="thegem-hotspot-tooltip-content">' . $section_content . '</div>';
						}
					} elseif(!empty($item['hotspot_content'])) {
						$tooltip_content .= '<div class="thegem-hotspot-tooltip-content">' . wp_kses_post($item['hotspot_content']) . '</div>';
					}

					?>
					
					<div <?php echo thegem_build_html_attributes($item_attrs); ?>>
						<?php if ($is_link) : ?>
							<a href="<?php echo esc_url($item['hotspot_link']); ?>" class="thegem-hotspot-marker-link" 
								<?php echo (!empty($item['hotspot_link_target']) ? 'target="_blank"' : ''); ?>
								<?php echo (!empty($item['hotspot_link_nofollow']) ? 'rel="nofollow"' : ''); ?>>
								<div class="thegem-hotspot-marker">
									<?php echo $marker_content; ?>
								</div>
							</a>
						<?php else : ?>
							<div class="thegem-hotspot-marker">
								<?php echo $marker_content; ?>
							</div>
						<?php endif; ?>

						<?php if (!empty($tooltip_content)) : ?>
							<div class="thegem-hotspot-tooltip">
								<?php echo $tooltip_content; ?>
							</div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

// Helper function to build HTML attributes from array
function thegem_build_html_attributes($attributes) {
	$output = '';
	foreach ($attributes as $key => $value) {
		if (!empty($value)) {
			$output .= ' ' . esc_attr($key) . '="' . esc_attr($value) . '"';
		}
	}
	return $output;
}

// Helper function to generate CSS
function thegem_generate_hotspot_css($unique_id, $atts, $hotspot_items) {
	$css = '';
	
	// Image styles
	if (!empty($atts['image_border_radius'])) {
		$radius = $atts['image_border_radius'];
		$css .= "#$unique_id .thegem-hotspot-image-wrap, #$unique_id .thegem-hotspot-image-wrap > img {";
		$css .= "border-radius: {$radius}px;";
		$css .= "}";
	}

	if (!empty($atts['enable_shadow'])) {
		$box_shadow = array(
			'shadow_horizontal' => intval($atts['shadow_horizontal']) . 'px',
			'shadow_vertical' => intval($atts['shadow_vertical']) . 'px',
			'shadow_blur' => intval($atts['shadow_blur']) . 'px',
			'shadow_spread' => intval($atts['shadow_spread']) . 'px',
			'shadow_color' => !empty($atts['shadow_color']) ? esc_attr($atts['shadow_color']) : 'rgba(0, 0, 0, 0.15)',
		);
		$css .= "#$unique_id .thegem-hotspot-image-wrap {";
		$css .= 'box-shadow: ' . implode(' ', $box_shadow) . ';';
		$css .= "}";
	}

	// Marker styles
	if (!empty($atts['marker_size'])) {
		$css .= "#$unique_id .thegem-hotspot-marker {";
		$css .= "min-width: " . intval($atts['marker_size']) . "px;";
		$css .= "min-height: " . intval($atts['marker_size']) . "px;";
		$css .= "}";
	}
	
	if (!empty($atts['icon_size'])) {
		$css .= "#$unique_id .thegem-hotspot-icon i {";
		$css .= "font-size: " . intval($atts['icon_size']) . "px;";
		$css .= "}";
		$css .= "#$unique_id .thegem-hotspot-marker svg {";
		$css .= "width: " . intval($atts['icon_size']) . "px;";
		$css .= "height: " . intval($atts['icon_size']) . "px;";
		$css .= "}";
	}
	
	if (!empty($atts['icon_gap'])) {
		$css .= "#$unique_id .thegem-hotspot-marker {";
		$css .= "gap: " . intval($atts['icon_gap']) . "px;";
		$css .= "}";
	}
	
	// Marker border
	if (!empty($atts['marker_border_width'])) {
		if (!empty($atts['marker_border_width'])) {
			$css .= "#$unique_id .thegem-hotspot-marker {";
			$css .= "border-width: {$atts['marker_border_width']}px;";
			$css .= "border-style: solid;";
			$css .= "}";
		}
	}
	
	if (!empty($atts['marker_border_radius']) || $atts['marker_border_radius'] === '0') {
		$css .= "#$unique_id .thegem-hotspot-marker {";
		$css .= "border-radius: {$atts['marker_border_radius']}px;";
		$css .= "}";
	}

	// Marker padding
	$marker_padding_css = '';
	if (!empty($atts['marker_padding_top'])) {
		$marker_padding_css .= 'padding-top: ' . esc_attr($atts['marker_padding_top']) . 'px;';
	}
	if (!empty($atts['marker_padding_bottom'])) {
		$marker_padding_css .= 'padding-bottom: ' . esc_attr($atts['marker_padding_bottom']) . 'px;';
	}
	if (!empty($atts['marker_padding_left'])) {
		$marker_padding_css .= 'padding-left: ' . esc_attr($atts['marker_padding_left']) . 'px;';
	}
	if (!empty($atts['marker_padding_right'])) {
		$marker_padding_css .= 'padding-right: ' . esc_attr($atts['marker_padding_right']) . 'px;';
	}

	if (!empty($marker_padding_css)) {
		$css .= "#$unique_id .thegem-hotspot-marker {";
		$css .= $marker_padding_css;
		$css .= "}";
	}
	
	// Marker colors - normal
	if (!empty($atts['marker_color'])) {
		$css .= "#$unique_id .thegem-hotspot-marker {";
		$css .= "background-color: " . esc_attr($atts['marker_color']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['marker_border_color'])) {
		$css .= "#$unique_id .thegem-hotspot-marker {";
		$css .= "border-color: " . esc_attr($atts['marker_border_color']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['marker_icon_color'])) {
		$css .= "#$unique_id .thegem-hotspot-marker i {";
		$css .= "color: " . esc_attr($atts['marker_icon_color']) . ";";
		$css .= "}";
		$css .= "#$unique_id .thegem-hotspot-marker svg {";
		$css .= "fill: " . esc_attr($atts['marker_icon_color']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['marker_label_color'])) {
		$css .= "#$unique_id .thegem-hotspot-label {";
		$css .= "color: " . esc_attr($atts['marker_label_color']) . ";";
		$css .= "}";
	}
	
	// Marker colors - hover
	if (!empty($atts['marker_color_hover'])) {
		$css .= "#$unique_id .thegem-hotspot-marker:hover {";
		$css .= "background-color: " . esc_attr($atts['marker_color_hover']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['marker_border_color_hover'])) {
		$css .= "#$unique_id .thegem-hotspot-marker:hover {";
		$css .= "border-color: " . esc_attr($atts['marker_border_color_hover']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['marker_icon_color_hover'])) {
		$css .= "#$unique_id .thegem-hotspot-marker:hover i {";
		$css .= "color: " . esc_attr($atts['marker_icon_color_hover']) . ";";
		$css .= "}";
		$css .= "#$unique_id .thegem-hotspot-marker:hover svg {";
		$css .= "fill: " . esc_attr($atts['marker_icon_color_hover']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['marker_label_color_hover'])) {
		$css .= "#$unique_id .thegem-hotspot-marker:hover .thegem-hotspot-label {";
		$css .= "color: " . esc_attr($atts['marker_label_color_hover']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['marker_icon_rotate_hover'])) {
		$css .= "#$unique_id .thegem-hotspot-marker:hover .thegem-hotspot-icon {";
		$css .= "transform: rotate(" . intval($atts['marker_icon_rotate_hover']) . "deg);";
		$css .= "}";
	}
	
	// Tooltip styles
	if (!empty($atts['tooltip_width'])) {
		$css .= "#$unique_id .thegem-hotspot-tooltip {";
		$css .= "max-width: " . intval($atts['tooltip_width']) . "px;";
		$css .= "}";
	}
	if (!empty($atts['tooltip_width_tablet'])) {
		$css .= "@media (max-width: 1024px) { #$unique_id .thegem-hotspot-tooltip {";
		$css .= "max-width: " . intval($atts['tooltip_width_tablet']) . "px;";
		$css .= "}}";
	}
	if (!empty($atts['tooltip_width_mobile'])) {
		$css .= "@media (max-width: 768px) { #$unique_id .thegem-hotspot-tooltip {";
		$css .= "max-width: " . intval($atts['tooltip_width_mobile']) . "px;";
		$css .= "}}";
	}

	// Tooltip padding
	$tooltip_padding_css = '';
	if (!empty($atts['tooltip_padding_top']) || $atts['tooltip_padding_top'] === '0') {
		$tooltip_padding_css .= 'padding-top: ' . esc_attr($atts['tooltip_padding_top']) . 'px;';
	}
	if (!empty($atts['tooltip_padding_bottom']) || $atts['tooltip_padding_bottom'] === '0') {
		$tooltip_padding_css .= 'padding-bottom: ' . esc_attr($atts['tooltip_padding_bottom']) . 'px;';
	}
	if (!empty($atts['tooltip_padding_left']) || $atts['tooltip_padding_left'] === '0') {
		$tooltip_padding_css .= 'padding-left: ' . esc_attr($atts['tooltip_padding_left']) . 'px;';
	}
	if (!empty($atts['tooltip_padding_right']) || $atts['tooltip_padding_right'] === '0') {
		$tooltip_padding_css .= 'padding-right: ' . esc_attr($atts['tooltip_padding_right']) . 'px;';
	}

	if (!empty($tooltip_padding_css)) {
		$css .= "#$unique_id .thegem-hotspot-tooltip {";
		$css .= $tooltip_padding_css;
		$css .= "}";
	}

	$tooltip_padding_tablet_css = '';
	if (!empty($atts['tooltip_padding_tablet_top']) || $atts['tooltip_padding_tablet_top'] === '0') {
		$tooltip_padding_tablet_css .= 'padding-top: ' . esc_attr($atts['tooltip_padding_tablet_top']) . 'px;';
	}
	if (!empty($atts['tooltip_padding_tablet_bottom']) || $atts['tooltip_padding_tablet_bottom'] === '0') {
		$tooltip_padding_tablet_css .= 'padding-bottom: ' . esc_attr($atts['tooltip_padding_tablet_bottom']) . 'px;';
	}
	if (!empty($atts['tooltip_padding_tablet_left']) || $atts['tooltip_padding_tablet_left'] === '0') {
		$tooltip_padding_tablet_css .= 'padding-left: ' . esc_attr($atts['tooltip_padding_tablet_left']) . 'px;';
	}
	if (!empty($atts['tooltip_padding_tablet_right']) || $atts['tooltip_padding_tablet_right'] === '0') {
		$tooltip_padding_tablet_css .= 'padding-right: ' . esc_attr($atts['tooltip_padding_tablet_right']) . 'px;';
	}

	if (!empty($tooltip_padding_tablet_css)) {
		$css .= "@media (max-width: 1024px) { #$unique_id .thegem-hotspot-tooltip {";
		$css .= $tooltip_padding_tablet_css;
		$css .= "}}";
	}

	$tooltip_padding_mobile_css = '';
	if (!empty($atts['tooltip_padding_mobile_top']) || $atts['tooltip_padding_mobile_top'] === '0') {
		$tooltip_padding_mobile_css .= 'padding-top: ' . esc_attr($atts['tooltip_padding_mobile_top']) . 'px;';
	}
	if (!empty($atts['tooltip_padding_mobile_bottom']) || $atts['tooltip_padding_mobile_bottom'] === '0') {
		$tooltip_padding_mobile_css .= 'padding-bottom: ' . esc_attr($atts['tooltip_padding_mobile_bottom']) . 'px;';
	}
	if (!empty($atts['tooltip_padding_mobile_left']) || $atts['tooltip_padding_mobile_left'] === '0') {
		$tooltip_padding_mobile_css .= 'padding-left: ' . esc_attr($atts['tooltip_padding_mobile_left']) . 'px;';
	}
	if (!empty($atts['tooltip_padding_mobile_right']) || $atts['tooltip_padding_mobile_right'] === '0') {
		$tooltip_padding_mobile_css .= 'padding-right: ' . esc_attr($atts['tooltip_padding_mobile_right']) . 'px;';
	}

	if (!empty($tooltip_padding_mobile_css)) {
		$css .= "@media (max-width: 768px) { #$unique_id .thegem-hotspot-tooltip {";
		$css .= $tooltip_padding_mobile_css;
		$css .= "}}";
	}

	if (!empty($atts['tooltip_border_radius']) || $atts['tooltip_border_radius'] === '0') {
		$css .= "#$unique_id .thegem-hotspot-tooltip {";
		$css .= "border-radius: {$atts['tooltip_border_radius']}px;";
		$css .= "}";
	}
	
	if (!empty($atts['tooltip_bg_color'])) {
		$css .= "#$unique_id .thegem-hotspot-tooltip {";
		$css .= "background-color: " . esc_attr($atts['tooltip_bg_color']) . ";";
		$css .= "}";
		// Tooltip arrows
		$css .= "#$unique_id .thegem-hotspot-item.thegem-hotspot-position-top .thegem-hotspot-tooltip::before {";
		$css .= "border-top-color: " . esc_attr($atts['tooltip_bg_color']) . ";";
		$css .= "}";
		$css .= "#$unique_id .thegem-hotspot-item.thegem-hotspot-position-bottom .thegem-hotspot-tooltip::before {";
		$css .= "border-bottom-color: " . esc_attr($atts['tooltip_bg_color']) . ";";
		$css .= "}";
		$css .= "#$unique_id .thegem-hotspot-item.thegem-hotspot-position-left .thegem-hotspot-tooltip::before {";
		$css .= "border-left-color: " . esc_attr($atts['tooltip_bg_color']) . ";";
		$css .= "}";
		$css .= "#$unique_id .thegem-hotspot-item.thegem-hotspot-position-right .thegem-hotspot-tooltip::before {";
		$css .= "border-right-color: " . esc_attr($atts['tooltip_bg_color']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['tooltip_text_color'])) {
		$css .= "#$unique_id .thegem-hotspot-tooltip, #$unique_id .thegem-hotspot-tooltip a {";
		$css .= "color: " . esc_attr($atts['tooltip_text_color']) . ";";
		$css .= "}";
	}

	if (!empty($atts['tooltip_enable_shadow'])) {
		$box_shadow = array(
			'shadow_horizontal' => intval($atts['tooltip_shadow_horizontal']) . 'px',
			'shadow_vertical' => intval($atts['tooltip_shadow_vertical']) . 'px',
			'shadow_blur' => intval($atts['tooltip_shadow_blur']) . 'px',
			'shadow_spread' => intval($atts['tooltip_shadow_spread']) . 'px',
			'shadow_color' => !empty($atts['tooltip_shadow_color']) ? esc_attr($atts['tooltip_shadow_color']) : 'rgba(0, 0, 0, 0.15)',
		);
		$css .= "#$unique_id .thegem-hotspot-tooltip {";
		$css .= 'box-shadow: ' . implode(' ', $box_shadow) . ';';
		$css .= "}";
	}

	return $css;
}

// WPBakery settings
function thegem_hotspot_shortcode_settings($shortcodes) {
	$shortcodes['gem_hotspot'] = array(
		'name' => esc_html__('Hotspot', 'thegem'),
		'base' => 'gem_hotspot',
		'description' => esc_html__('Interactive image with hotspot markers', 'thegem'),
		'icon' => 'thegem-icon-wpb-ui-hotspot',
		'category' => esc_html__('TheGem', 'thegem'),
		'params' => array(
			// Image Section
			array(
				'type' => 'thegem_delimeter_heading',
				'heading' => esc_html__('Image', 'thegem'),
				'param_name' => 'image_head',
				'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding vc_column vc_col-sm-12',
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'attach_image',
				'heading' => esc_html__('Image', 'thegem'),
				'param_name' => 'image_id',
				'description' => esc_html__('Select the background image for hotspots.', 'thegem'),
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Image Size', 'thegem'),
				'param_name' => 'image_size',
				'value' => array(
					__('Full Size', 'thegem') => 'full',
					__('WordPress Thumbnail', 'thegem') => 'thumbnail',
					__('WordPress Medium', 'thegem') => 'medium',
					__('WordPress Medium Large', 'thegem') => 'medium_large',
					__('WordPress Large', 'thegem') => 'large',
					__('1536x1536', 'thegem') => '1536x1536',
					__('2048x2048', 'thegem') => '2048x2048',
				),
				'std' => 'full',
				'group' => esc_html__('General', 'thegem'),
			),

			// Global Settings
			array(
				'type' => 'thegem_delimeter_heading',
				'heading' => esc_html__('Hotspots & Settings', 'thegem'),
				'param_name' => 'settings_head',
				'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Tooltip Trigger', 'thegem'),
				'param_name' => 'tooltip_trigger',
				'value' => array(
					esc_html__('On Hover', 'thegem') => 'hover',
					esc_html__('On Click', 'thegem') => 'click',
					esc_html__('Show Always', 'thegem') => 'show-always',
				),
				'std' => 'hover',
				'description' => esc_html__('On mobile devices, "On Click" is always used (unless "Show Always").', 'thegem'),
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Tooltip Animation', 'thegem'),
				'param_name' => 'tooltip_animation',
				'value' => array(
					esc_html__('None', 'thegem') => 'none',
					esc_html__('Fade', 'thegem') => 'fade',
					esc_html__('Slide Up', 'thegem') => 'slide-up',
					esc_html__('Zoom In', 'thegem') => 'zoom-in',
				),
				'std' => 'fade',
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__('Pulsating Effect', 'thegem'),
				'param_name' => 'pulsating_effect',
				'value' => array(esc_html__('Yes', 'thegem') => 'yes'),
				'std' => '',
				'description' => esc_html__('Enable a subtle pulsating animation on the marker.', 'thegem'),
				'group' => esc_html__('General', 'thegem'),
			),

			// Hotspot Items (would need custom param type in real implementation)
			// Hotspot Items
			array(
				'type' => 'param_group',
				'heading' => esc_html__('Hotspot Items', 'thegem'),
				'param_name' => 'hotspot_items',
				'value' => rawurlencode( wp_json_encode(array(
					array(
						'hotspot_title' => esc_html__('Hotspot 1', 'thegem'),
						'hotspot_position_x' => '50',
						'hotspot_position_y' => '50',
						'tooltip_position' => 'top',
						'marker_icon_pack' => 'fontawesome',
						'marker_icon_fontawesome' => 'f111',
					),
				))),
				'params' => array(
					// Content
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => esc_html__('Content', 'thegem'),
						'param_name' => 'content_head',
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level vc_column vc_col-sm-12',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__('Hotspot Title', 'thegem'),
						'param_name' => 'hotspot_title',
						'admin_label' => true,
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__('Link URL', 'thegem'),
						'param_name' => 'hotspot_link',
						'description' => esc_html__('Enter URL if you want this hotspot to be a link.', 'thegem'),
					),
					array(
						'type' => 'checkbox',
						'heading' => esc_html__('Open in new tab', 'thegem'),
						'param_name' => 'hotspot_link_target',
						'value' => array(esc_html__('Yes', 'thegem') => 'yes'),
						'std' => '',
					),
					array(
						'type' => 'checkbox',
						'heading' => esc_html__('Add nofollow', 'thegem'),
						'param_name' => 'hotspot_link_nofollow',
						'value' => array(esc_html__('Yes', 'thegem') => 'yes'),
						'std' => '',
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Icon pack', 'thegem'),
						'param_name' => 'marker_icon_pack',
						'value' => array_merge(array(
							__('Elegant', 'thegem') => 'elegant',
							__('Material Design', 'thegem') => 'material',
							__('FontAwesome', 'thegem') => 'fontawesome',
							__('Header Icons', 'thegem') => 'thegem-header',
							__('Additional', 'thegem') => 'thegemdemo')
						),
						'std' => 'fontawesome',
						'save_always' => true,
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'marker_icon_elegant',
						'icon_pack' => 'elegant',
						'dependency' => array(
							'element' => 'marker_icon_pack',
							'value' => array('elegant')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'marker_icon_material',
						'icon_pack' => 'material',
						'dependency' => array(
							'element' => 'marker_icon_pack',
							'value' => array('material')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'marker_icon_fontawesome',
						'icon_pack' => 'fontawesome',
						'dependency' => array(
							'element' => 'marker_icon_pack',
							'value' => array('fontawesome')
						),
						'value' => 'f111',
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'marker_icon_thegemdemo',
						'icon_pack' => 'thegemdemo',
						'dependency' => array(
							'element' => 'marker_icon_pack',
							'value' => array('thegemdemo')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'thegem_icon',
						'heading' => __('Icon', 'thegem'),
						'param_name' => 'marker_icon_thegemheader',
						'icon_pack' => 'thegem-header',
						'dependency' => array(
							'element' => 'marker_icon_pack',
							'value' => array('thegem-header')
						),
						'edit_field_class' => 'vc_column vc_col-sm-6',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__('Label', 'thegem'),
						'param_name' => 'hotspot_label',
						'description' => esc_html__('Text label for the marker', 'thegem'),
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Tooltip Content Type', 'thegem'),
						'param_name' => 'tooltip_content_type',
						'value' => array(
							__('Text', 'thegem') => 'text',
							__('Global Section', 'thegem') => 'template',
						),
						'std' => 'default',
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'description' => sprintf(__('You can use global section template to display in offcanvas area by selecting "Global Section". <a href="%s" target="_blank">Learn more</a>'), 'https://docs.codex-themes.com/category/191-section-template')
					),
					array(
						'type' => 'textarea',
						'heading' => esc_html__('Tooltip Content', 'thegem'),
						'param_name' => 'hotspot_content',
						'description' => esc_html__('Content that appears in the tooltip', 'thegem'),
						'dependency' => array(
							'element' => 'tooltip_content_type',
							'value' => 'text',
						),
					),
					array(
						'type' => 'thegem_template_selector',
						'heading' => __('Select Global Section', 'thegem'),
						'param_name' => 'tooltip_template',
						'value' => array_flip(thegem_get_section_templates_list(true)),
						'edit_field_class' => 'vc_column vc_col-sm-6 thegem-select-template-field',
						'group' => __('General', 'thegem'),
						'save_always' => true,
						'create_template' => 'content',
						'dependency' => array(
							'element' => 'tooltip_content_type',
							'value' => 'template',
							'callback' => 'thegem_template_select_callback',
						),
					),
					// Position
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => esc_html__('Position', 'thegem'),
						'param_name' => 'position_head',
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__('Position X (%)', 'thegem'),
						'param_name' => 'hotspot_position_x',
						'std' => '50',
						'description' => esc_html__('Horizontal position (0-100)', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__('Position Y (%)', 'thegem'),
						'param_name' => 'hotspot_position_y',
						'std' => '50',
						'description' => esc_html__('Vertical position (0-100)', 'thegem'),
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__('Tooltip Position', 'thegem'),
						'param_name' => 'tooltip_position',
						'value' => array(
							esc_html__('Top', 'thegem') => 'top',
							esc_html__('Bottom', 'thegem') => 'bottom',
							esc_html__('Left', 'thegem') => 'left',
							esc_html__('Right', 'thegem') => 'right',
						),
						'std' => 'top',
					),

					// Animation
					array(
						'type' => 'thegem_delimeter_heading_two_level',
						'heading' => esc_html__('Animation', 'thegem'),
						'param_name' => 'animation_head',
						'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
					),
					array(
						'type' => 'dropdown',
						'heading' => esc_html__('Marker Animation', 'thegem'),
						'param_name' => 'marker_animation',
						'value' => array(
							esc_html__('None', 'thegem') => 'none',
							esc_html__('Fade In', 'thegem') => 'fade',
							esc_html__('Scale Up', 'thegem') => 'scale',
							esc_html__('Slide Up', 'thegem') => 'slide-up',
							esc_html__('Slide Down', 'thegem') => 'slide-down',
							esc_html__('Slide Left', 'thegem') => 'slide-left',
							esc_html__('Slide Right', 'thegem') => 'slide-right',
							esc_html__('Bounce In', 'thegem') => 'bounce',
							esc_html__('Zoom Out', 'thegem') => 'zoom-out',
							esc_html__('Rotate In', 'thegem') => 'rotate',
							esc_html__('Flip In', 'thegem') => 'flip',
						),
						'std' => 'none',
					),
					array(
						'type' => 'textfield',
						'heading' => esc_html__('Animation Delay (ms)', 'thegem'),
						'param_name' => 'marker_animation_delay',
						'value' => 0,
						'description' => esc_html__('Delay before animation starts', 'thegem'),
					),
				),
				'group' => esc_html__('General', 'thegem'),
			),

			// Image Styles
			array(
				'type' => 'thegem_delimeter_heading',
				'heading' => esc_html__('Image', 'thegem'),
				'param_name' => 'image_styles_head',
				'edit_field_class' => 'thegem-param-delimeter-heading vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Border Radius', 'thegem'),
				'param_name' => 'image_border_radius',
				'description' => esc_html__('CSS border-radius values', 'thegem'),
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => __('Box Shadow', 'thegem'),
				'param_name' => 'image_shadow_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'checkbox',
				'heading' => __('Enable Box Shadow', 'thegem'),
				'param_name' => 'enable_shadow',
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => __('Shadow color', 'thegem'),
				'param_name' => 'shadow_color',
				'dependency' => array(
					'element' => 'enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-8',
				'std' => 'rgba(0, 0, 0, 0.15)',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => __('Horizontal', 'thegem'),
				'param_name' => 'shadow_horizontal',
				'dependency' => array(
					'element' => 'enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'std' => '0',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => __('Vertical', 'thegem'),
				'param_name' => 'shadow_vertical',
				'dependency' => array(
					'element' => 'enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'std' => '0',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => __('Blur', 'thegem'),
				'param_name' => 'shadow_blur',
				'dependency' => array(
					'element' => 'enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'std' => '5',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => __('Spread', 'thegem'),
				'param_name' => 'shadow_spread',
				'dependency' => array(
					'element' => 'enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'std' => '0',
				'group' => esc_html__('Styles', 'thegem'),
			),

			// Marker Styles
			array(
				'type' => 'thegem_delimeter_heading',
				'heading' => esc_html__('Hotspot Marker', 'thegem'),
				'param_name' => 'marker_styles_main_head',
				'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Marker Styles', 'thegem'),
				'param_name' => 'marker_styles_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Marker Size (px)', 'thegem'),
				'param_name' => 'marker_size',
				'value' => 20,
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Icon Size (px)', 'thegem'),
				'param_name' => 'icon_size',
				'value' => 10,
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Icon Gap (px)', 'thegem'),
				'param_name' => 'icon_gap',
				'value' => 5,
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Border Width', 'thegem'),
				'param_name' => 'marker_border_width',
				'description' => esc_html__('CSS border-width values', 'thegem'),
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Border Radius', 'thegem'),
				'param_name' => 'marker_border_radius',
				'description' => esc_html__('CSS border-radius values', 'thegem'),
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Marker Paddings', 'thegem'),
				'param_name' => 'marker_paddings_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Top', 'thegem'),
				'param_name' => 'marker_padding_top',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Bottom', 'thegem'),
				'param_name' => 'marker_padding_bottom',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Left', 'thegem'),
				'param_name' => 'marker_padding_left',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Right', 'thegem'),
				'param_name' => 'marker_padding_right',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),

			// Marker Colors
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Marker Colors - Normal', 'thegem'),
				'param_name' => 'marker_colors_normal_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Background Color', 'thegem'),
				'param_name' => 'marker_color',
				'value' => '#ffffff',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Border Color', 'thegem'),
				'param_name' => 'marker_border_color',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Icon Color', 'thegem'),
				'param_name' => 'marker_icon_color',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Label Color', 'thegem'),
				'param_name' => 'marker_label_color',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem'),
			),

			// Marker Colors - Hover
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Marker Colors - Hover', 'thegem'),
				'param_name' => 'marker_colors_hover_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Background Color', 'thegem'),
				'param_name' => 'marker_color_hover',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Border Color', 'thegem'),
				'param_name' => 'marker_border_color_hover',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Icon Color', 'thegem'),
				'param_name' => 'marker_icon_color_hover',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Label Color', 'thegem'),
				'param_name' => 'marker_label_color_hover',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Icon Rotate (deg)', 'thegem'),
				'param_name' => 'marker_icon_rotate_hover',
				'group' => esc_html__('Styles', 'thegem'),
			),

			// Text Style
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Label Style', 'thegem'),
				'param_name' => 'text_style_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top border-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Label Text Style', 'thegem'),
				'param_name' => 'label_text_preset',
				'value' => array(
					esc_html__('Default', 'thegem') => '',
					esc_html__('Title H1', 'thegem') => 'title-h1',
					esc_html__('Title H2', 'thegem') => 'title-h2',
					esc_html__('Title H3', 'thegem') => 'title-h3',
					esc_html__('Title H4', 'thegem') => 'title-h4',
					esc_html__('Title H5', 'thegem') => 'title-h5',
					esc_html__('Title H6', 'thegem') => 'title-h6',
					esc_html__('Title xLarge', 'thegem') => 'title-xlarge',
					esc_html__('Styled Subtitle', 'thegem') => 'styled-subtitle',
					esc_html__('Main Menu', 'thegem') => 'main-menu-item',
					esc_html__('Body', 'thegem') => 'text-body',
					esc_html__('Tiny Body', 'thegem') => 'text-body-tiny',
				),
				'std' => '',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Label Text Weight', 'thegem'),
				'param_name' => 'label_text_weight',
				'value' => array(
					esc_html__('Default', 'thegem') => '',
					esc_html__('Bold', 'thegem') => 'bold',
					esc_html__('Light', 'thegem') => 'light',
				),
				'std' => '',
				'group' => esc_html__('Styles', 'thegem'),
			),

			// Tooltip Styles
			array(
				'type' => 'thegem_delimeter_heading',
				'heading' => esc_html__('Tooltip', 'thegem'),
				'param_name' => 'tooltip_styles_main_head',
				'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Tooltip Styles', 'thegem'),
				'param_name' => 'tooltip_styles_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Max Width (px)', 'thegem'),
				'param_name' => 'tooltip_width',
				'std' => 200,
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Tablet', 'thegem'),
				'param_name' => 'tooltip_width_tablet',
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Mobile', 'thegem'),
				'param_name' => 'tooltip_width_mobile',
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Border Radius', 'thegem'),
				'param_name' => 'tooltip_border_radius',
				'description' => esc_html__('CSS border-radius values', 'thegem'),
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Background Color', 'thegem'),
				'param_name' => 'tooltip_bg_color',
				'value' => '#333333cc',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Text Color', 'thegem'),
				'param_name' => 'tooltip_text_color',
				'value' => '#ffffff',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Tooltip Paddings', 'thegem'),
				'param_name' => 'tooltip_paddings_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top border-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Desktop (px)', 'thegem'),
				'param_name' => 'tooltip_paddings_desktop_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Top', 'thegem'),
				'param_name' => 'tooltip_padding_top',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Bottom', 'thegem'),
				'param_name' => 'tooltip_padding_bottom',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Left', 'thegem'),
				'param_name' => 'tooltip_padding_left',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Right', 'thegem'),
				'param_name' => 'tooltip_padding_right',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),

			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Tablet (px)', 'thegem'),
				'param_name' => 'tooltip_paddings_tablet_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Top', 'thegem'),
				'param_name' => 'tooltip_padding_tablet_top',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Bottom', 'thegem'),
				'param_name' => 'tooltip_padding_tablet_bottom',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Left', 'thegem'),
				'param_name' => 'tooltip_padding_tablet_left',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Right', 'thegem'),
				'param_name' => 'tooltip_padding_tablet_right',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),

			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Mobile (px)', 'thegem'),
				'param_name' => 'tooltip_paddings_mobile_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Top', 'thegem'),
				'param_name' => 'tooltip_padding_mobile_top',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Bottom', 'thegem'),
				'param_name' => 'tooltip_padding_mobile_bottom',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Left', 'thegem'),
				'param_name' => 'tooltip_padding_mobile_left',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Right', 'thegem'),
				'param_name' => 'tooltip_padding_mobile_right',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),

			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => __('Box Shadow', 'thegem'),
				'param_name' => 'tooltip_shadow_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'checkbox',
				'heading' => __('Enable Box Shadow', 'thegem'),
				'param_name' => 'tooltip_enable_shadow',
				'edit_field_class' => 'vc_column vc_col-sm-4',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => __('Shadow color', 'thegem'),
				'param_name' => 'tooltip_shadow_color',
				'dependency' => array(
					'element' => 'tooltip_enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-8',
				'std' => 'rgba(0, 0, 0, 0.15)',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => __('Horizontal', 'thegem'),
				'param_name' => 'tooltip_shadow_horizontal',
				'dependency' => array(
					'element' => 'enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'std' => '0',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => __('Vertical', 'thegem'),
				'param_name' => 'tooltip_shadow_vertical',
				'dependency' => array(
					'element' => 'enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'std' => '0',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => __('Blur', 'thegem'),
				'param_name' => 'tooltip_shadow_blur',
				'dependency' => array(
					'element' => 'enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'std' => '5',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => __('Spread', 'thegem'),
				'param_name' => 'tooltip_shadow_spread',
				'dependency' => array(
					'element' => 'enable_shadow',
					'not_empty' => true,
				),
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'std' => '0',
				'group' => esc_html__('Styles', 'thegem'),
			),

			// Tooltip Text Style
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Tooltip Text Style', 'thegem'),
				'param_name' => 'tooltip_text_style_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top border-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Title Text Style', 'thegem'),
				'param_name' => 'tooltip_title_text_preset',
				'value' => array(
					esc_html__('Default', 'thegem') => '',
					esc_html__('Title H1', 'thegem') => 'title-h1',
					esc_html__('Title H2', 'thegem') => 'title-h2',
					esc_html__('Title H3', 'thegem') => 'title-h3',
					esc_html__('Title H4', 'thegem') => 'title-h4',
					esc_html__('Title H5', 'thegem') => 'title-h5',
					esc_html__('Title H6', 'thegem') => 'title-h6',
					esc_html__('Title xLarge', 'thegem') => 'title-xlarge',
					esc_html__('Styled Subtitle', 'thegem') => 'styled-subtitle',
					esc_html__('Main Menu', 'thegem') => 'main-menu-item',
					esc_html__('Body', 'thegem') => 'text-body',
					esc_html__('Tiny Body', 'thegem') => 'text-body-tiny',
				),
				'std' => '',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Title Text Weight', 'thegem'),
				'param_name' => 'tooltip_title_font_weight',
				'value' => array(
					esc_html__('Default', 'thegem') => '',
					esc_html__('Bold', 'thegem') => 'bold',
					esc_html__('Light', 'thegem') => 'light',
				),
				'std' => '',
				'group' => esc_html__('Styles', 'thegem'),
			),

			// Extra class
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Extra class name', 'thegem'),
				'param_name' => 'el_class',
				'description' => esc_html__('If you wish to style particular content element differently, then use this field to add a class name and then refer to it in your css file.', 'thegem'),
				'group' => esc_html__('General', 'thegem'),
			),
		),
	);
	return $shortcodes;
}
add_filter('thegem_shortcodes_array', 'thegem_hotspot_shortcode_settings');