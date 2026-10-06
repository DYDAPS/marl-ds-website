(function ($) {
	function initProductGallery() {
		if (window.tgpLazyItems !== undefined) {
			var isShowed = window.tgpLazyItems.checkGroupShowed(this, function (node) {
				initProductGallery.call(node);
			});
			if (!isShowed) {
				return;
			}
		}

		var $portfolio = $(this);
		tabsMinHeight($portfolio);

		if ($('.portfolio-filter-tabs', $portfolio).length) {
			$('.portfolio-filter-tabs-content', $portfolio).css('min-height', $('.portfolio-filter-tabs-content', $portfolio).height());

			$('.portfolio-filter-tabs-list-tab', $portfolio).on('click', function (e) {
				if (!$(this).hasClass('active')) {
					$('.portfolio-filter-tabs-list-tab', $portfolio).removeClass('active');
					$(this).addClass('active');
					var num = $(this).data('num');
					$('.extended-products-grid-carousel-item', $portfolio).hide();
					$('.extended-products-grid-carousel-item[data-num="'+num+'"]', $portfolio).show();

					$('.portfolio-filter-tabs-content', $portfolio).css('min-height', '');
					$('.portfolio-filter-tabs-content', $portfolio).css('min-height', $('.portfolio-filter-tabs-content', $portfolio).height());

					var uid = $portfolio.data('portfolio-uid');
					var queryParams = new URLSearchParams(window.location.search);
					queryParams.set(uid + '-tab', num);
					history.replaceState(null, null, "?" + queryParams.toString());
					if ($portfolio.hasClass('loading-animation')) {
						$portfolio.addClass('animation-update');
					}
				}
			});
		}

		updateProductGallery($portfolio);
	}

	function updateProductGallery($portfolio) {

		$portfolio.find('.extended-products-grid-carousel-item').each(function () {
			var $tabItem = $(this);
			var $galleryElement = $('.portfolio-set', $tabItem);

			var isTouch = window.gemSettings.isTouch,
				autoplay = true,
				animationSpeed = $portfolio.attr("data-autoscroll-speed"),
				slideBy = 'page',
				animationEffect = $portfolio.attr("data-sliding-animation"),
				isArrows = $portfolio.attr("data-arrows") === '1',
				isDots = $portfolio.attr("data-dots") === '1',
				isLoop = $portfolio.attr("data-loop") === '1',
				$carouselItem = $('.extended-carousel-item', $galleryElement);

			if (animationSpeed == '0') {
				autoplay = false
			}

			if (animationEffect == 'one-by-one') {
				slideBy = 1
			}

			//Init preview carousel
			$carouselItem.owlCarousel({
				loop: isLoop,
				items: 1,
				rewind: false,
				mouseDrag: true,
				autoplay: autoplay,
				autoplayTimeout: animationSpeed,
				slideTransition: 'ease',
				slideBy: slideBy,
				dots: isDots,
				nav: isArrows,
				rtl: $('body').hasClass('rtl'),
				responsive: {
					0: {
						items: $portfolio.data('columns-mobile'),
						margin: $portfolio.hasClass("item-separator") ? 0 : $portfolio.data("margin-mobile"),
					},
					768: {
						items: $portfolio.data('columns-tablet'),
						margin: $portfolio.hasClass("item-separator") ? 0 : $portfolio.data("margin-tablet"),
					},
					992: {
						items: $portfolio.data('columns-desktop'),
						margin: $portfolio.hasClass("item-separator") ? 0 : $portfolio.data("margin-desktop"),
					},
				},
				onRefreshed: function () {
					if ($portfolio.hasClass('loading-animation') && $portfolio.hasClass('animation-update')) {
						$portfolio.removeClass('animation-update');
						if ($portfolio.itemsAnimations('instance').getAnimationName() != 'disabled') {
							$portfolio.itemsAnimations('instance').reinitItems($('.portfolio-item', $carouselItem));
							$portfolio.itemsAnimations('instance').show($('.portfolio-item', $carouselItem));
						}
					}
				},
				onInitialized: function () {
					$tabItem.addClass('inited');
					changedArrows();

					$galleryElement.closest('.portfolio-preloader-wrapper').prev('.preloader').remove();

					if ($('.portfolio-filter-tabs', $portfolio).length) {
						tabsMinHeight($portfolio);
					}

					if ($portfolio.find('.extended-products-grid-carousel-item').length == $portfolio.find('.extended-products-grid-carousel-item.inited').length) {
						$portfolio.initExtendedPortfolioGrids();
					}

					$('.gem-attribute-selector', $portfolio).gemWooAttributeSelector();
					$('.product-variations', $portfolio).each(function () {
						try {
							$(this).wc_variation_form();
						} catch(err) {
							console.log(err);
						}
						initVariations($(this));
					});

				},
				onChange: function () {
					if (window.tgpLazyItems !== undefined) {
						window.tgpLazyItems.scrollHandle();
					}
				}
			});

			// Changed arrows
			function changedArrows() {
				$('.slider-prev-icon', $tabItem).appendTo($('.owl-nav .owl-prev', $galleryElement));
				$('.slider-next-icon', $tabItem).appendTo($('.owl-nav .owl-next', $galleryElement));

				var dotsHeight = $('.owl-dots', $tabItem).outerHeight() + parseInt($('.owl-dots', $tabItem).css('marginTop'));
				$('.owl-nav .owl-prev', $galleryElement).css('top', 'calc(50% - '+dotsHeight+'px/2)');
				$('.owl-nav .owl-next', $galleryElement).css('top', 'calc(50% - '+dotsHeight+'px/2)');
			}

			// Resize and orientation changes
			window.addEventListener("resize", function () {
				isTouch = window.gemSettings.isTouch;

				$carouselItem.trigger('refresh.owl.carousel');

				if ($('.portfolio-filter-tabs', $portfolio).length) {
					tabsMinHeight($portfolio);
				}
			}, { passive: true });
		})
	}

	function tabsMinHeight($portfolio) {
		$('.portfolio-filter-tabs-content', $portfolio).css('min-height', '');
		$('.portfolio-filter-tabs-content', $portfolio).css('min-height', $('.portfolio-filter-tabs-content', $portfolio).height());
	}

	$.fn.initProductGalleries = function () {
		var $portfolio = $(this);
		if ($(this).hasClass('product-carousel-inited')) {
			return;
		}
		$(this).addClass('product-carousel-inited');
		setTimeout(function () {
			$portfolio.addClass('ready');
		}, 500);
		function initGallery() {
			$portfolio.each(initProductGallery);
		}
		waitForFunction(initGallery);
	};

	function waitForFunction(runFunction){
		if (typeof owlCarousel === undefined || typeof initExtendedPortfolioGrids === undefined) {
			setTimeout( waitForFunction(runFunction), 100);
			return;
		}
		runFunction();
	}

	$(document).ready(function () {
		$('body:not(.vc_editor) .extended-products-grid-carousel').initProductGalleries();
	});

		function initVariations($form) {
			let $price = $form.closest('.portfolio-item').find('.product-price'),
				variablePrice = $price.html(),
				$selectOptionsWrap = $form.closest('.portfolio-item').find('span.cart:not(.swatches-button)'),
				$cartWrap = $form.closest('.portfolio-item').find('span.cart.swatches-button'),
				$cart = $cartWrap.find('a'),
				$image = $form.closest('.portfolio-item').find('.image'),
				$productImg = $form.closest('.portfolio-item').find('img:not(.variation-image)'),
				$variationImg = $form.closest('.portfolio-item').find('.variation-image'),
				hideVariation = () => {
					$price.html(variablePrice);
					$cartWrap.hide();
					$selectOptionsWrap.show().insertBefore($cartWrap);
					$productImg.show();
					$variationImg.hide();
					$variationImg.attr('src', '');
					$variationImg.attr('srcset', '');
				};

			hideVariation();

			$form.on('hide_variation', function (e) {
				hideVariation();
			});

			$form.on('show_variation', function (e, variation) {
				if (variation.is_purchasable) {
					if (variation.is_in_stock) {
						if (variation.price_html != '') {
							$price.html(variation.price_html);
						}
						$selectOptionsWrap.hide();
						$cart.data('variation_id', variation.variation_id);
						let variationsArray = $form.serializeArray();
						let variations = {};
						$.each(variationsArray, function () {
							variations[this.name] = this.value || "";
						});
						$cart.data('variation', variations);
						$cartWrap.show().insertBefore($selectOptionsWrap);
					} else {
						$price.html('<div class="price">'+variation.availability_html+'</div>');
					}

					if (variation.image && variation.image.src) {
						$image.prepend('<div class="preloader-spin-new"></div>');
						$variationImg.attr('src', variation.image.src);
						$variationImg.attr('height', variation.image.src_h);
						$variationImg.attr('width', variation.image.src_w);
						$variationImg.attr('srcset', variation.image.srcset);
						$variationImg.attr('sizes', variation.image.sizes);
						$variationImg.attr('title', variation.image.title);
						$variationImg.attr('data-caption', variation.image.caption);
						$variationImg.attr('alt', variation.image.alt);
					} else {
						$productImg.show();
						$variationImg.hide();
						$variationImg.attr('src', '');
						$variationImg.attr('srcset', '');
					}
					$form.closest('.portfolio-item').find('.image .variations-notification').html('').hide();
				} else {
					hideVariation();
					if ($form.find('.single_variation > p').length) {
						$form.closest('.portfolio-item').find('.image .variations-notification').html('<span class="close"></span>'+$form.find('.single_variation > p').html()).css('display', 'flex');
					}
				}
			});

			$form.on('reset_data', function (e, variation) {
				$form.closest('.portfolio-item').find('.image .variations-notification').html('').hide();
				setTimeout(checkVariables.bind(this, $form), 100);
			});

			$cart.on('click', function (e) {

				if ($cart.data('product_id')) {
					e.preventDefault();

					let $thisButton = $(this);

					let data = {
						action: 'woocommerce_ajax_add_to_cart'
					};

					// Fetch changes that are directly added by calling $thisbutton.data( key, value )
					$.each( $thisButton.data(), function( key, value ) {
						data[ key ] = value;
					});

					$(document.body).trigger('adding_to_cart', [$thisButton, data]);

					$.ajax({
						type: 'post',
						url: wc_add_to_cart_params.ajax_url,
						data: data,
						success: function (response) {
							if ( ! response ) {
								return;
							}

							if ( response.error && response.product_url ) {
								window.location = response.product_url;
								return;
							}

							$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $thisButton]);
						},
					});

					return false;
				}
			});

			$variationImg.on('load', function () {
				$productImg.hide();
				$variationImg.show();
				$image.find('.preloader-spin-new').remove();
			});

			$('.variations-notification').on('click', '.close', function () {
				$(this).parent().hide();
			})
		}

		function checkVariables($form) {
			if ($form.find('.wc-no-matching-variations').length) {
				$form.closest('.portfolio-item').find('.image .variations-notification').html('<span class="close"></span>'+$form.find('.wc-no-matching-variations').html()).css('display', 'flex');
			}
		}

})(jQuery);
