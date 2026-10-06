<?php
$thegem_item_data = thegem_get_sanitize_client_data(get_the_ID());
$thegem_client_item_tag = $thegem_item_data['link'] ? 'a' : 'span';
$thegem_item_data['link'] = $thegem_item_data['link'] ? $thegem_item_data['link'] : '#';
$thegem_classes = array('gem-client-item');
if (!empty($params['effects_enabled'])) {
	$thegem_classes[] = 'lazy-loading-item';
}
$caption = function_exists('thegem_clients_caption') ? thegem_clients_caption(get_the_ID(), $params) : '';

?>

<div id="post-<?php the_ID(); ?>" <?php post_class($thegem_classes); ?> <?php if(!$params['widget']) : ?>style="width: <?php echo esc_attr(100/$cols); ?>%;"<?php endif; ?> <?php if(!empty($params['effects_enabled'])): ?>data-ll-effect="drop-bottom"<?php endif; ?>>
	<<?php echo $thegem_client_item_tag; ?> href="<?php echo esc_url($thegem_item_data['link']); ?>" target="<?php echo esc_attr($thegem_item_data['link_target']); ?>" class="gscale">
		<?php
		if($params['widget']) {
			$thegem_small_image_url = thegem_generate_thumbnail_src(get_post_thumbnail_id(), 'thegem-widget-column-1x');
			$thegem_small_image_url_2x = thegem_generate_thumbnail_src(get_post_thumbnail_id(), 'thegem-widget-column-2x');
			echo '<img src="'.esc_url($thegem_small_image_url[0]).'" srcset="'.esc_attr($thegem_small_image_url_2x[0]).' 2x" width="'.esc_attr($thegem_small_image_url[1]).'" alt="'.get_the_title().'" class="img-responsive"/>';
		} else {
			thegem_post_thumbnail($params['thumbnail'] === 'default' ? 'thegem-person' : $params['thumbnail'], true, 'img-responsive');
		}
		?>
		<?php echo $caption; ?>
	</<?php echo $thegem_client_item_tag; ?>>
</div>