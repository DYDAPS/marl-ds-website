<?php

class TheGem_Template_Element_Loop_Product_Rating extends TheGem_Single_Post_Template_Element {

	public function __construct() {
	}

	public function get_name() {
		return 'thegem_te_loop_product_rating';
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
		// General params
		$params = shortcode_atts(array_merge(array(
			'alignment' => 'left',
			'show_counts' => '1',
			'stars_size' => '',
			'stars_rated_color' => '',
			'stars_base_color' => '',
			'counts_text_style' => '',
			'counts_text_weight' => 'normal',
		), $atts), $atts, 'thegem_te_product_rating');
		
		// Init Design Options Params
		$uniqid = uniqid('thegem-custom-').rand(1,9999);
		
		// Init Rating
		ob_start();
		$product = thegem_templates_init_product();
		if (empty($product) || !wc_review_ratings_enabled() || $product->get_rating_count() < 1) {
			ob_end_clean();
			return thegem_templates_close_product($this->get_name(), $this->shortcode_settings(), '');
		}
		$rating_count = $product->get_rating_count();
		$review_count = $product->get_review_count();
		$average = $product->get_average_rating();

		?>

		<div <?php if (!empty($params['element_id'])): ?>id="<?=esc_attr($params['element_id']); ?>"<?php endif;?>
			 class="thegem-te-loop-product-rating <?= esc_attr($uniqid); ?>"
			<?= thegem_data_editor_attribute($uniqid . '-editor') ?>>
			
			<div class="product-rating">
				<?= wc_get_rating_html( $average, $rating_count ); ?>

				<?php if ($params['show_counts']) :
					$counts_text_class = implode(' ', array('counts-text', $params['counts_text_style'], $params['counts_text_weight']));
				?>
					<div class="rating-counts small-body"><span class="<?= esc_attr($counts_text_class); ?>"><?php printf( __( '(%s)', 'thegem' ), $review_count ); ?></span></div>
				<?php endif ?>
			</div>
		</div>

		<?php
		//Custom Styles
		$customize = '.thegem-te-loop-product-rating.'.$uniqid;
		$custom_css = '';
		
		// General Styled
		if (!empty($params['alignment'])) {
			$custom_css .= $customize.' .product-rating {justify-content: ' . $params['alignment'] . '; text-align: ' . $params['alignment'] . ';}';
		}
		
		// Stars Styled
		if (!empty(intval($params['stars_size']))) {
			$custom_css .= $customize.' .product-rating .star-rating {font-size: ' . intval($params['stars_size']) . 'px;}';
		}
		if (!empty($params['stars_base_color'])) {
			$custom_css .= $customize.' .product-rating .star-rating:before {color: ' . $params['stars_base_color'] . ';}';
		}
		if (!empty($params['stars_rated_color'])) {
			$custom_css .= $customize.' .product-rating .star-rating > span:before {color: ' . $params['stars_rated_color'] . ';}';
		}

		if (!empty($params['show_counts'])) {
			if (!empty($params['counts_spacing'])) {
				$custom_css .= $customize.' .product-rating {gap: ' . intval($params['counts_spacing']) . 'px;}';
			}
			if (!empty($params['counts_color'])) {
				$custom_css .= $customize.' .product-rating .counts-text {color: ' . $params['counts_color'] . ';}';
			}
			if (!empty($params['counts_letter_spacing'])) {
				$custom_css .= $customize.' .product-rating .counts-text {letter-spacing: ' . floatval($params['counts_letter_spacing']) . 'px;}';
			}
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

	public function shortcode_settings() {

		return array(
			'name' => __('Product Rating', 'thegem'),
			'base' => 'thegem_te_loop_product_rating',
			'icon' => 'thegem-icon-wpb-ui-element-product-rating',
			'category' => __('Loop Item Builder', 'thegem'),
			'description' => __('Product Rating (Loop Item Builder)', 'thegem'),
			'params' => array_merge(

				/* General - Layout */
				array(
					array(
						'type' => 'thegem_delimeter_heading',
						'heading' => __('General', 'thegem'),
						'param_name' => 'layout_delim_head',
						'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding vc_column vc_col-sm-12 capitalize',
						'group' => __('General', 'thegem')
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Alignment', 'thegem'),
						'param_name' => 'alignment',
						'value' => array_merge(array(
								__('Left', 'thegem') => 'left',
								__('Center', 'thegem') => 'center',
								__('Right', 'thegem') => 'right',
							)
						),
						'std' => 'left',
						'edit_field_class' => 'vc_column vc_col-sm-12',
						'group' => 'General',
					),
					array(
						'type' => 'checkbox',
						'heading' => __('Show Counts', 'thegem'),
						'param_name' => 'show_counts',
						'value' => array(__('Yes', 'thegem') => '1'),
						'std' => '1',
						'edit_field_class' => 'vc_column vc_col-sm-12',
						'group' => 'General',
					),
				),
				
				/* General - Stars */
				array(
					array(
						'type' => 'thegem_delimeter_heading',
						'heading' => __('Rating Stars', 'thegem'),
						'param_name' => 'layout_delim_head',
						'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12 capitalize',
						'group' => __('General', 'thegem')
					),
					array(
						'type' => 'textfield',
						'heading' => __('Stars Size (px)', 'thegem'),
						'param_name' => 'stars_size',
						'group' => 'General'
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Rated Color', 'thegem'),
						'param_name' => 'stars_rated_color',
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => 'General'
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Base Color', 'thegem'),
						'param_name' => 'stars_base_color',
						'edit_field_class' => 'vc_column vc_col-sm-6',
						'group' => 'General'
					),
				),

				/* General - Counts */
				array(
					array(
						'type' => 'thegem_delimeter_heading',
						'heading' => __('Rating Counts', 'thegem'),
						'param_name' => 'counts_delim_head',
						'edit_field_class' => 'thegem-param-delimeter-heading no-top-padding margin-top vc_column vc_col-sm-12 capitalize',
						'dependency' => array(
							'element' => 'show_counts',
							'not_empty' => true
						),
						'group' => __('General', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Spacing (px)', 'thegem'),
						'param_name' => 'counts_spacing',
						'dependency' => array(
							'element' => 'show_counts',
							'not_empty' => true
						),
						'group' => 'General',
					),
					array(
						'type' => 'colorpicker',
						'heading' => __('Counts Color', 'thegem'),
						'param_name' => 'counts_color',
						'dependency' => array(
							'element' => 'show_counts',
							'not_empty' => true
						),
						'group' => 'General',
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Text style', 'thegem'),
						'param_name' => 'counts_text_style',
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
							'element' => 'show_counts',
							'not_empty' => true
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('General', 'thegem'),
					),
					array(
						'type' => 'dropdown',
						'heading' => __('Text weight', 'thegem'),
						'param_name' => 'counts_text_weight',
						'value' => array(__('Normal', 'thegem') => 'normal', __('Thin', 'thegem') => 'light'),
						'dependency' => array(
							'element' => 'show_counts',
							'not_empty' => true
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('General', 'thegem'),
					),
					array(
						'type' => 'textfield',
						'heading' => __('Letter Spacing', 'thegem'),
						'param_name' => 'counts_letter_spacing',
						'dependency' => array(
							'element' => 'show_counts',
							'not_empty' => true
						),
						'edit_field_class' => 'vc_column vc_col-sm-4',
						'group' => __('General', 'thegem'),
					),

				)
			),
		);
	}
}

$templates_elements['thegem_te_loop_product_rating'] = new TheGem_Template_Element_Loop_Product_Rating();
$templates_elements['thegem_te_loop_product_rating']->init();
