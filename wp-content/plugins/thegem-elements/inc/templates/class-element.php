<?php

abstract class TheGem_Template_Element {
	abstract public function get_name();

	abstract public function shortcode_output($atts, $content = '');

	abstract public function shortcode_settings();

	public function need_head_scripts() {
		return false;
	}

	public function add_head_scripts_shortcodes($shortcodes) {
		$shortcodes[$this->get_name()] = array($this, 'head_scripts');
		return $shortcodes;
	}

	public function init() {
		add_shortcode($this->get_name(), array($this, 'shortcode_output'));
		add_filter('thegem_shortcodes_array', array($this, 'shortcode_activate'));
		if(method_exists($this, 'head_scripts')) {
			add_filter('head_scripts_shortcodes', array($this, 'add_head_scripts_shortcodes'));
		}
		if(method_exists($this, 'front_editor_scripts')) {
			add_action('vc_load_iframe_jscss', array($this, 'front_editor_scripts'));
		}
	}

	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'header') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'header') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'header') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'header') {
				$activate = true;
			}
			if($activate) {
				$shortcodes[$this->get_name()] = $this->shortcode_settings();
			}
		}
		return $shortcodes;
	}

	public function is_template() {
		global $pagenow;
		if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates') {
			return true;
		}
		return false;
	}

	public function is_template_type($type) {
		global $pagenow;
		if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == $type) {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == $type) {
			return true;
		}
		if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == $type) {
			return true;
		}
		if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == $type) {
			return true;
		}
		return false;
	}

}

abstract class TheGem_Product_Template_Element extends TheGem_Template_Element {
	public $show_in_loop = false;
	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'single-product') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'single-product') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'single-product') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'single-product') {
				$activate = true;
			}
			if($this->show_in_loop) {
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
			}
			if($activate) {
				$shortcodes[$this->get_name()] = $this->shortcode_settings();
			}
		}
		return $shortcodes;
	}

	public function is_loop_item_template() {
		global $pagenow;
		if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'loop-item') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'loop-item') {
			return true;
		}
		if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'loop-item') {
			return true;
		}
		if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'loop-items') {
			return true;
		}
		return false;
	}
}

abstract class TheGem_Cart_Template_Element extends TheGem_Template_Element {

	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'cart') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'cart') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'cart') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'cart') {
				$activate = true;
			}
			if($activate) {
				$shortcodes[$this->get_name()] = $this->shortcode_settings();
			}
		}
		return $shortcodes;
	}

}

abstract class TheGem_Checkout_Template_Element extends TheGem_Template_Element {

	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'checkout') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'checkout') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'checkout') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'checkout') {
				$activate = true;
			}
			if($activate) {
				$shortcodes[$this->get_name()] = $this->shortcode_settings();
			}
		}
		return $shortcodes;
	}

}

abstract class TheGem_Checkout_Thanks_Template_Element extends TheGem_Template_Element {

	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'checkout-thanks') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'checkout-thanks') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'checkout-thanks') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'checkout-thanks') {
				$activate = true;
			}
			if($activate) {
				$shortcodes[$this->get_name()] = $this->shortcode_settings();
			}
		}
		return $shortcodes;
	}
}

abstract class TheGem_Product_Archive_Template_Element extends TheGem_Template_Element {

	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'product-archive') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'product-archive') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'product-archive') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'product-archive') {
				$activate = true;
			}
			if($activate) {
				$shortcodes[$this->get_name()] = $this->shortcode_settings();
			}
		}
		return $shortcodes;
	}

}

abstract class TheGem_Blog_Archive_Template_Element extends TheGem_Template_Element {

	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'blog-archive') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'blog-archive') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'blog-archive') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'blog-archive') {
				$activate = true;
			}
			if($activate) {
				$shortcodes[$this->get_name()] = $this->shortcode_settings();
			}
		}
		return $shortcodes;
	}

}

abstract class TheGem_Single_Post_Template_Element extends TheGem_Template_Element {
	public $show_in_posts = true;
	public $show_in_loop = false;
	public $show_in_title = false;
	public $show_in_product = false;
	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'single-post') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'single-post') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'single-post') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'single-post') {
				$activate = true;
			}
			if($this->show_in_posts) {
				if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] !== 'thegem_templates') {
					$activate = true;
				}
				if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) !== 'thegem_templates') {
					$activate = true;
				}
				if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) !== 'thegem_templates') {
					$activate = true;
				}
				if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) !== 'thegem_templates') {
					$activate = true;
				}
				if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) !== 'thegem_templates') {
					$activate = true;
				}
			}
			if($this->show_in_loop) {
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
			}
			if($this->show_in_title) {
				if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
					$activate = true;
				}
				if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'title') {
					$activate = true;
				}
				if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'title') {
					$activate = true;
				}
				if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'title') {
					$activate = true;
				}
				if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'title') {
					$activate = true;
				}
			}
			if($this->show_in_product) {
				if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
					$activate = true;
				}
				if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'single-product') {
					$activate = true;
				}
				if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'single-product') {
					$activate = true;
				}
				if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'single-product') {
					$activate = true;
				}
				if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'single-product') {
					$activate = true;
				}
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
	public function is_template() {
		global $pagenow;
		if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && (thegem_get_template_type($_REQUEST['post']) == 'single-post' || thegem_get_template_type($_REQUEST['post']) == 'loop-item')) {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && (thegem_get_template_type($_REQUEST['post_id']) == 'single-post' || thegem_get_template_type($_REQUEST['post_id']) == 'loop-item')) {
			return true;
		}
		if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && (thegem_get_template_type($_REQUEST['post_id']) == 'single-post' || thegem_get_template_type($_REQUEST['post_id']) == 'loop-item')) {
			return true;
		}
		if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && (thegem_get_template_type($_REQUEST['vc_post_id']) == 'single-post' || thegem_get_template_type($_REQUEST['vc_post_id']) == 'loop-item')) {
			return true;
		}
		return false;
	}
	public function is_title_template() {
		global $pagenow;
		if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'title') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'title') {
			return true;
		}
		if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'title') {
			return true;
		}
		if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'title') {
			return true;
		}
		return false;
	}
	public function is_loop_item_template() {
		global $pagenow;
		if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'loop-item') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'loop-item') {
			return true;
		}
		if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'loop-item') {
			return true;
		}
		if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'loop-items') {
			return true;
		}
		return false;
	}
	public function is_product_template() {
		global $pagenow;
		if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'single-product') {
			return true;
		}
		if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'single-product') {
			return true;
		}
		if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'single-product') {
			return true;
		}
		if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'single-product') {
			return true;
		}
		return false;
	}
}

abstract class TheGem_Portfolio_Item_Template_Element extends TheGem_Template_Element {
	public function shortcode_activate($shortcodes) {
		global $pagenow;
		if((is_admin() && in_array($pagenow, array('post-new.php', 'post.php', 'admin-ajax.php'))) || (!is_admin() && in_array($pagenow, array('index.php')))) {
			$activate = 0;
			if($pagenow === 'post-new.php' && !empty($_REQUEST['post_type']) && $_REQUEST['post_type'] === 'thegem_templates') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post']) && get_post_type($_REQUEST['post']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post']) == 'portfolio') {
				$activate = true;
			}
			if($pagenow === 'post.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'portfolio') {
				$activate = true;
			}
			if($pagenow === 'admin-ajax.php' && !empty($_REQUEST['post_id']) && get_post_type($_REQUEST['post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['post_id']) == 'portfolio') {
				$activate = true;
			}
			if($pagenow === 'index.php' && !empty($_REQUEST['vc_post_id']) && get_post_type($_REQUEST['vc_post_id']) === 'thegem_templates' && thegem_get_template_type($_REQUEST['vc_post_id']) == 'portfolio') {
				$activate = true;
			}
			if($activate) {
				$shortcodes[$this->get_name()] = $this->shortcode_settings();
			}
		}
		return $shortcodes;
	}
	
}
