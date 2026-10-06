<?php
// Shortcode function
add_shortcode('gem_image_compare', 'thegem_image_compare_shortcode');

function thegem_image_compare_shortcode($atts) {
	$atts = shortcode_atts(array(
		'image_before_id' => '',
		'image_after_id' => '',
		'label_before' => esc_html__('Before', 'thegem'),
		'label_after' => esc_html__('After', 'thegem'),
		'image_size' => 'full',
		'initial_offset' => '50',
		'orientation' => 'horizontal',
		'interaction_mode' => 'drag',
		'images_border_radius' => '',
		
		// Handle styles
		'handle_line_color' => '#ffffff',
		'handle_line_width' => 2,
		'handle_box_size' => 40,
		'handle_box_background' => 'rgba(255,255,255,0.2)',
		'handle_box_border_width' => 3,
		'handle_box_border_color' => '#ffffff',
		'handle_box_border_radius' => 50,
		'handle_arrows_color' => '#ffffff',
		'handle_arrows_size' => 8,
		'handle_shadow' => '',
		
		// Label styles
		'label_before_color' => '#ffffff',
		'label_before_background' => 'rgba(0,0,0,0.5)',
		'label_after_color' => '#ffffff',
		'label_after_background' => 'rgba(0,0,0,0.5)',
		'label_before_position' => 'top',
		'label_after_position' => 'bottom',
		'labels_padding_top' => '',
		'labels_padding_bottom' => '',
		'labels_padding_left' => '',
		'labels_padding_right' => '',
		'labels_show_on_hover' => 'no',
		'label_text_preset' => '',
		'label_text_weight' => '',
		
		// Image filters
		'css_filters_before' => '',
		'css_filters_after' => '',
		
		'el_class' => '',
	), $atts, 'gem_image_compare');

	// Get image URLs
	$image_before_url = $atts['image_before_id'] ? wp_get_attachment_image_url($atts['image_before_id'], $atts['image_size']) : '';
	$image_after_url = $atts['image_after_id'] ? wp_get_attachment_image_url($atts['image_after_id'], $atts['image_size']) : '';

	if (empty($image_before_url) || empty($image_after_url)) {
		return '<div class="bordered-box centered-box styled-subtitle">' . esc_html__('Please select images (image before and image after)', 'thegem') . '</div>';
	}

	$unique_id = 'thegem-image-compare-' . uniqid();
	$initial_offset_ratio = floatval($atts['initial_offset']) / 100;

	// Parse CSS filters
	$filters_before = thegem_parse_css_filters($atts['css_filters_before']);
	$filters_after = thegem_parse_css_filters($atts['css_filters_after']);

	// Label classes
	$label_text_classes = !empty($atts['label_text_preset']) ? $atts['label_text_preset'] : 'text-style-default';
	if (!empty($atts['label_text_weight'])) {
		$label_text_classes .= ' ' . $atts['label_text_weight'];
	}

	// Generate inline CSS
	$inline_css = thegem_generate_image_compare_css($unique_id, $atts);

	ob_start();
	
	// Enqueue styles and scripts
	wp_enqueue_style('thegem-image-compare');
	wp_enqueue_script('thegem-image-compare');
	
	// Output CSS
	if (!empty($inline_css)) {
		echo '<style type="text/css">' . $inline_css . '</style>';
	}
	?>
	
	<div id="<?php echo esc_attr($unique_id); ?>" 
		class="thegem-image-compare-wrapper thegem-image-compare-orientation-<?php echo esc_attr($atts['orientation']); ?> thegem-image-compare-mode-<?php echo esc_attr($atts['interaction_mode']); ?> thegem-image-compare-labels-hover-<?php echo esc_attr($atts['labels_show_on_hover']); ?> <?php echo esc_attr($atts['el_class']); ?>"
		data-offset="<?php echo esc_attr($initial_offset_ratio); ?>"
		data-orientation="<?php echo esc_attr($atts['orientation']); ?>"
		data-interaction-mode="<?php echo esc_attr($atts['interaction_mode']); ?>"
		dir="<?php echo is_rtl() ? 'rtl' : 'ltr'; ?>">
		
		<div class="thegem-image-compare-viewport">
			<div class="thegem-image-compare-img-before-wrapper">
				<img class="thegem-image-compare-img thegem-image-compare-img-before" 
					src="<?php echo esc_url($image_before_url); ?>" 
					alt="<?php echo esc_attr($atts['label_before']); ?>"
					style="<?php echo esc_attr($filters_before); ?>">
				<?php if (!empty($atts['label_before'])) : ?>
					<span class="thegem-image-compare-label thegem-image-compare-label-before <?php echo esc_attr($label_text_classes); ?>"><?php echo esc_html($atts['label_before']); ?></span>
				<?php endif; ?>
			</div>
			
			<div class="thegem-image-compare-img-after-wrapper">
				<img class="thegem-image-compare-img thegem-image-compare-img-after" 
					src="<?php echo esc_url($image_after_url); ?>" 
					alt="<?php echo esc_attr($atts['label_after']); ?>"
					style="<?php echo esc_attr($filters_after); ?>">
				<?php if (!empty($atts['label_after'])) : ?>
					<span class="thegem-image-compare-label thegem-image-compare-label-after <?php echo esc_attr($label_text_classes); ?>"><?php echo esc_html($atts['label_after']); ?></span>
				<?php endif; ?>
			</div>
			
			<div class="thegem-image-compare-handle<?php echo empty($atts['handle_shadow']) ? '' : ' with-shadow'; ?>">
				<div class="thegem-image-compare-handle-inner">
					<div class="thegem-image-compare-arrow thegem-image-compare-arrow-left"></div>
					<div class="thegem-image-compare-arrow thegem-image-compare-arrow-right"></div>
				</div>
			</div>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

// Helper function to parse CSS filters
function thegem_parse_css_filters($filters_string) {
	if (empty($filters_string)) {
		return '';
	}
	
	$filters = explode('|', $filters_string);
	$css_filters = array();
	
	foreach ($filters as $filter) {
		if (strpos($filter, ':') !== false) {
			list($property, $value) = explode(':', $filter, 2);
			$css_filters[] = $property . '(' . $value . ')';
		}
	}
	
	return !empty($css_filters) ? 'filter: ' . implode(' ', $css_filters) . ';' : '';
}

// Helper function to generate CSS
function thegem_generate_image_compare_css($unique_id, $atts) {
	$css = '';

	if (!empty($atts['images_border_radius']) || $atts['images_border_radius'] === '0') {
		$radius = is_numeric($atts['images_border_radius']) ? $atts['images_border_radius'] . 'px' : $atts['images_border_radius'];
		$css .= "#$unique_id .thegem-image-compare-img {";
		$css .= "border-radius: " . esc_attr($radius) . ";";
		$css .= "}";
	}

	// Handle line color
	if (!empty($atts['handle_line_color'])) {
		$css .= "#$unique_id .thegem-image-compare-handle::before,";
		$css .= "#$unique_id .thegem-image-compare-handle::after {";
		$css .= "background-color: " . esc_attr($atts['handle_line_color']) . ";";
		$css .= "}";
	}
	// Handle line width
	if (!empty($atts['handle_line_width']) || $atts['handle_line_width'] === '0') {
		$line_width = intval($atts['handle_line_width']) . 'px';
		$css .= "#$unique_id.thegem-image-compare-orientation-horizontal .thegem-image-compare-handle::before,";
		$css .= "#$unique_id.thegem-image-compare-orientation-horizontal .thegem-image-compare-handle::after {";
		$css .= "width: " . $line_width . ";";
		$css .= "}";
		$css .= "#$unique_id.thegem-image-compare-orientation-vertical .thegem-image-compare-handle::before,";
		$css .= "#$unique_id.thegem-image-compare-orientation-vertical .thegem-image-compare-handle::after {";
		$css .= "height: " . $line_width . ";";
		$css .= "}";
	}
	
	// Handle box size
	if (!empty($atts['handle_box_size'])) {
		$box_size = intval($atts['handle_box_size']) . 'px';
		$css .= "#$unique_id .thegem-image-compare-handle-inner {";
		$css .= "width: " . $box_size . ";";
		$css .= "height: " . $box_size . ";";
		$css .= "}";
		$css .= "#$unique_id.thegem-image-compare-orientation-horizontal .thegem-image-compare-handle::before,";
		$css .= "#$unique_id.thegem-image-compare-orientation-horizontal .thegem-image-compare-handle::after {";
		$css .= "height: calc(50% - " . $box_size . " / 2);";
		$css .= "}";
		$css .= "#$unique_id.thegem-image-compare-orientation-vertical .thegem-image-compare-handle::before,";
		$css .= "#$unique_id.thegem-image-compare-orientation-vertical .thegem-image-compare-handle::after {";
		$css .= "width: calc(50% - " . $box_size . " / 2);";
		$css .= "}";
	}
	
	// Handle box background
	if (!empty($atts['handle_box_background'])) {
		$css .= "#$unique_id .thegem-image-compare-handle-inner {";
		$css .= "background-color: " . esc_attr($atts['handle_box_background']) . ";";
		$css .= "}";
	}
	
	// Handle box border
	if (!empty($atts['handle_box_border_width']) || $atts['handle_box_border_width'] === '0') {
		$css .= "#$unique_id .thegem-image-compare-handle-inner {";
		$css .= "border-width: " . intval($atts['handle_box_border_width']) . "px;";
		$css .= "border-style: solid;";
		$css .= "border-color: " . esc_attr($atts['handle_box_border_color']) . ";";
		$css .= "}";
	}
	
	// Handle box border radius
	if (!empty($atts['handle_box_border_radius']) || $atts['handle_box_border_radius'] === '0') {
		$radius = is_numeric($atts['handle_box_border_radius']) ? $atts['handle_box_border_radius'] . 'px' : $atts['handle_box_border_radius'];
		$css .= "#$unique_id .thegem-image-compare-handle-inner {";
		$css .= "border-radius: " . esc_attr($radius) . ";";
		$css .= "}";
	}
	
	// Handle arrows
	if (!empty($atts['handle_arrows_color'])) {
		$css .= "#$unique_id {";
		$css .= "--thegem-image-compare-arrow-color: " . esc_attr($atts['handle_arrows_color']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['handle_arrows_size'])) {
		$css .= "#$unique_id .thegem-image-compare-arrow {";
		$css .= "--thegem-image-compare-arrow-size: " . intval($atts['handle_arrows_size']) . "px;";
		$css .= "}";
	}
	
	// Label before
	if (!empty($atts['label_before_color'])) {
		$css .= "#$unique_id .thegem-image-compare-label-before {";
		$css .= "color: " . esc_attr($atts['label_before_color']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['label_before_background'])) {
		$css .= "#$unique_id .thegem-image-compare-label-before {";
		$css .= "background-color: " . esc_attr($atts['label_before_background']) . ";";
		$css .= "}";
	}
	
	// Label after
	if (!empty($atts['label_after_color'])) {
		$css .= "#$unique_id .thegem-image-compare-label-after {";
		$css .= "color: " . esc_attr($atts['label_after_color']) . ";";
		$css .= "}";
	}
	
	if (!empty($atts['label_after_background'])) {
		$css .= "#$unique_id .thegem-image-compare-label-after {";
		$css .= "background-color: " . esc_attr($atts['label_after_background']) . ";";
		$css .= "}";
	}
	
	// Label positions
	$before_position = thegem_get_label_position_css($atts['label_before_position']);
	$after_position = thegem_get_label_position_css($atts['label_after_position']);
	
	if (!empty($before_position)) {
		$css .= "#$unique_id .thegem-image-compare-label-before {";
		$css .= $before_position;
		$css .= "}";
	}
	
	if (!empty($after_position)) {
		$css .= "#$unique_id .thegem-image-compare-label-after {";
		$css .= $after_position;
		$css .= "}";
	}
	
	// Label padding
	$padding_css = '';
	if (!empty($atts['labels_padding_top'])) {
		$padding_css .= 'padding-top: ' . esc_attr($atts['labels_padding_top']) . 'px;';
	}
	if (!empty($atts['labels_padding_bottom'])) {
		$padding_css .= 'padding-bottom: ' . esc_attr($atts['labels_padding_bottom']) . 'px;';
	}
	if (!empty($atts['labels_padding_left'])) {
		$padding_css .= 'padding-left: ' . esc_attr($atts['labels_padding_left']) . 'px;';
	}
	if (!empty($atts['labels_padding_right'])) {
		$padding_css .= 'padding-right: ' . esc_attr($atts['labels_padding_right']) . 'px;';
	}
	
	if (!empty($padding_css)) {
		$css .= "#$unique_id .thegem-image-compare-label {";
		$css .= $padding_css;
		$css .= "}";
	}
	
	return $css;
}

// Helper function for label positions
function thegem_get_label_position_css($position) {
	$positions = array(
		'top' => 'top: 10px; bottom: auto;',
		'middle' => 'top: 50%; bottom: auto; transform: translateY(-50%);',
		'bottom' => 'top: auto; bottom: 10px;'
	);
	
	return isset($positions[$position]) ? $positions[$position] : '';
}

// WPBakery settings
function thegem_image_compare_shortcode_settings($shortcodes) {
	$shortcodes['gem_image_compare'] = array(
		'name' => esc_html__('Image Compare', 'thegem'),
		'base' => 'gem_image_compare',
		'description' => esc_html__('Image comparison slider', 'thegem'),
		'icon' => 'thegem-icon-wpb-ui-image-compare',
		'category' => esc_html__('TheGem', 'thegem'),
		'params' => array(
			// Before Image Section
			array(
				'type' => 'thegem_delimeter_heading',
				'heading' => esc_html__('Before Image', 'thegem'),
				'param_name' => 'before_head',
				'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding vc_column vc_col-sm-12',
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'attach_image',
				'heading' => esc_html__('Image Before', 'thegem'),
				'param_name' => 'image_before_id',
				'description' => esc_html__('Select the image for the "Before" state.', 'thegem'),
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Label Before', 'thegem'),
				'param_name' => 'label_before',
				'value' => esc_html__('Before', 'thegem'),
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Label Before Position', 'thegem'),
				'param_name' => 'label_before_position',
				'value' => array(
					esc_html__('Top', 'thegem') => 'top',
					esc_html__('Middle', 'thegem') => 'middle',
					esc_html__('Bottom', 'thegem') => 'bottom',
				),
				'std' => 'top',
				'group' => esc_html__('General', 'thegem'),
			),
			
			// After Image Section
			array(
				'type' => 'thegem_delimeter_heading',
				'heading' => esc_html__('After Image', 'thegem'),
				'param_name' => 'after_head',
				'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top border-top margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'attach_image',
				'heading' => esc_html__('Image After', 'thegem'),
				'param_name' => 'image_after_id',
				'description' => esc_html__('Select the image for the "After" state.', 'thegem'),
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Label After', 'thegem'),
				'param_name' => 'label_after',
				'value' => esc_html__('After', 'thegem'),
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Label After Position', 'thegem'),
				'param_name' => 'label_after_position',
				'value' => array(
					esc_html__('Top', 'thegem') => 'top',
					esc_html__('Middle', 'thegem') => 'middle',
					esc_html__('Bottom', 'thegem') => 'bottom',
				),
				'std' => 'bottom',
				'group' => esc_html__('General', 'thegem'),
			),
			
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Common Settings', 'thegem'),
				'param_name' => 'common_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
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

			// Slider Settings
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Handle Settings', 'thegem'),
				'param_name' => 'slider_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Orientation', 'thegem'),
				'param_name' => 'orientation',
				'value' => array(
					esc_html__('Horizontal', 'thegem') => 'horizontal',
					esc_html__('Vertical', 'thegem') => 'vertical',
				),
				'std' => 'horizontal',
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'dropdown',
				'heading' => esc_html__('Interaction Mode', 'thegem'),
				'param_name' => 'interaction_mode',
				'value' => array(
					esc_html__('Drag', 'thegem') => 'drag',
					esc_html__('Hover', 'thegem') => 'hover',
				),
				'std' => 'drag',
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Initial Position (%)', 'thegem'),
				'param_name' => 'initial_offset',
				'std' => '50',
				'description' => esc_html__('The starting position of the slider (0-100).', 'thegem'),
				'group' => esc_html__('General', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Images Border Radius', 'thegem'),
				'param_name' => 'images_border_radius',
				'description' => esc_html__('In pixels', 'thegem'),
				'group' => esc_html__('Styles', 'thegem'),
			),

			// Handle Styles
			array(
				'type' => 'thegem_delimeter_heading',
				'heading' => esc_html__('Handle Styles', 'thegem'),
				'param_name' => 'handle_head',
				'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12 capitalize',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Line Color', 'thegem'),
				'param_name' => 'handle_line_color',
				'value' => '#ffffff',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Line Width (px)', 'thegem'),
				'param_name' => 'handle_line_width',
				'value' => 2,
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Box Size (px)', 'thegem'),
				'param_name' => 'handle_box_size',
				'value' => 40,
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Box Background', 'thegem'),
				'param_name' => 'handle_box_background',
				'value' => 'rgba(255,255,255,0.2)',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Border Width (px)', 'thegem'),
				'param_name' => 'handle_box_border_width',
				'value' => 3,
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Border Color', 'thegem'),
				'param_name' => 'handle_box_border_color',
				'value' => '#ffffff',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Border Radius', 'thegem'),
				'param_name' => 'handle_box_border_radius',
				'description' => esc_html__('In pixels or percentage', 'thegem'),
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Arrows Color', 'thegem'),
				'param_name' => 'handle_arrows_color',
				'value' => '#ffffff',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Arrows Size (px)', 'thegem'),
				'param_name' => 'handle_arrows_size',
				'value' => 8,
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__('Activate shadow', 'thegem'),
				'param_name' => 'handle_shadow',
				'value' => array(esc_html__('Yes', 'thegem') => 'yes'),
				'group' => esc_html__('Styles', 'thegem'),
			),

			// Label Styles
			array(
				'type' => 'thegem_delimeter_heading',
				'heading' => esc_html__('Label Styles', 'thegem'),
				'param_name' => 'labels_head',
				'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12 capitalize',
				'group' => esc_html__('Styles', 'thegem'),
			),
			
			// Text Style Settings
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Text Style', 'thegem'),
				'param_name' => 'text_style_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level vc_column vc_col-sm-12',
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
			
			// Before Label Styles
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Before Label', 'thegem'),
				'param_name' => 'before_label_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Label Before Color', 'thegem'),
				'param_name' => 'label_before_color',
				'value' => '#ffffff',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Label Before Background', 'thegem'),
				'param_name' => 'label_before_background',
				'value' => 'rgba(0,0,0,0.5)',
				'group' => esc_html__('Styles', 'thegem'),
			),
			
			// After Label Styles
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('After Label', 'thegem'),
				'param_name' => 'after_label_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Label After Color', 'thegem'),
				'param_name' => 'label_after_color',
				'value' => '#ffffff',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'colorpicker',
				'heading' => esc_html__('Label After Background', 'thegem'),
				'param_name' => 'label_after_background',
				'value' => 'rgba(0,0,0,0.5)',
				'group' => esc_html__('Styles', 'thegem'),
			),
			
			// Common Label Settings
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Common Label Settings', 'thegem'),
				'param_name' => 'common_label_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level border-top margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'checkbox',
				'heading' => esc_html__('Show on Hover', 'thegem'),
				'param_name' => 'labels_show_on_hover',
				'value' => array(esc_html__('Yes', 'thegem') => 'yes'),
				'std' => '',
				'group' => esc_html__('Styles', 'thegem'),
			),
			array(
				'type' => 'thegem_delimeter_heading_two_level',
				'heading' => esc_html__('Labels Paddings', 'thegem'),
				'param_name' => 'labels_paddings_head',
				'edit_field_class' => 'thegem-param-delimeter-heading-two-level margin-top vc_column vc_col-sm-12',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Top', 'thegem'),
				'param_name' => 'labels_padding_top',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Bottom', 'thegem'),
				'param_name' => 'labels_padding_bottom',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Left', 'thegem'),
				'param_name' => 'labels_padding_left',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
			),
			array(
				'type' => 'textfield',
				'heading' => esc_html__('Right', 'thegem'),
				'param_name' => 'labels_padding_right',
				'edit_field_class' => 'vc_column vc_col-sm-3',
				'group' => esc_html__('Styles', 'thegem')
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
add_filter('thegem_shortcodes_array', 'thegem_image_compare_shortcode_settings');