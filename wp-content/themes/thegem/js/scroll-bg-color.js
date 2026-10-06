(function() {
	'use strict';

	class ScrollBackgroundManager {
		constructor(containerSelector) {
			this.container = document.querySelector(containerSelector);
			if (!this.container) return;

			this.activeTrigger = null;
			this.originalBg = this.container.style.backgroundColor;
			this.originalColor = this.container.style.color;
			this.wipeElement = null;
			this.hasActivated = false;
			this.animationTimer = null;

			this.init();
		}

		init() {
			this.container.classList.add('thegem-scroll-bg');
			this.findTriggers();
			this.setupScrollListener();
		}

		findTriggers() {
			this.triggers = Array.from(this.container.querySelectorAll('[data-thegem-scroll-bg="1"]'));
		}

		setupScrollListener() {
			this.checkActiveTrigger();

			window.addEventListener('scroll', () => {
				requestAnimationFrame(() => {
					this.checkActiveTrigger();
				});
			}, { passive: true });

			window.addEventListener('resize', () => {
				this.checkActiveTrigger();
			});

			if(!this.activeTrigger) {
				let hero = this.container.querySelector('.thegem-scroll-bg-hero');
				if(hero) {
					hero.classList.remove('thegem-scroll-bg-hero');
					this.activate(hero);
				}
			}
		}

		checkActiveTrigger() {
			let currentActiveTrigger = null;
			const viewportHeight = window.innerHeight;
			const midPoint = viewportHeight * 0.5;

			for (const trigger of this.triggers) {
				const rect = trigger.getBoundingClientRect();
				const topCrossesMid = rect.top <= midPoint && rect.bottom > midPoint;
				const bottomCrossesMid = rect.bottom >= midPoint && rect.top < midPoint;

				if (topCrossesMid || bottomCrossesMid) {
					currentActiveTrigger = trigger;
					break;
				}
			}

			if (currentActiveTrigger && this.activeTrigger !== currentActiveTrigger) {
				this.activate(currentActiveTrigger);
			} else if (!currentActiveTrigger && this.activeTrigger) {
				this.deactivate();
			}
		}

		addAnimateClass(duration) {
			this.container.classList.add('thegem-scroll-bg-animate');
			if (this.animationTimer) {
				clearTimeout(this.animationTimer);
			}
			this.animationTimer = setTimeout(() => {
				this.container.classList.remove('thegem-scroll-bg-animate');
			}, duration);
		}

		activate(trigger) {
			const bgColor = trigger.getAttribute('data-thegem-scroll-bg-color');
			const textColor = trigger.getAttribute('data-thegem-scroll-bg-text');
			const effect = trigger.getAttribute('data-thegem-scroll-bg-effect') || 'fade';
			let duration = parseInt(trigger.getAttribute('data-thegem-scroll-bg-duration')) || 500;
			const func = trigger.getAttribute('data-thegem-scroll-bg-function') || 'ease';
			const wipeDir = trigger.getAttribute('data-thegem-scroll-bg-wipe-dir') || 'left';

			this.container.style.setProperty('--thegem-scroll-bg-duration', duration + 'ms');
			this.container.style.setProperty('--thegem-scroll-bg-function', func);

			this.addAnimateClass(duration);

			if (textColor) {
				this.container.classList.add('thegem-scroll-bg-colored');
				//this.container.style.transition = `color ${duration}ms ${func}`;
				this.container.style.color = textColor;
				this.container.style.setProperty('--thegem-scroll-bg-text-color', textColor);
			} else {
				this.container.classList.remove('thegem-scroll-bg-colored');
				this.container.style.color = '';
				this.container.style.removeProperty('--thegem-scroll-bg-text-color');
			}

			if (bgColor) {
				if (effect === 'fade') {
					this.removeWipeElement();
					this.container.style.transition = `background-color ${duration}ms ${func}`;
					this.container.style.backgroundColor = bgColor;

					if (!this.hasActivated) {
						this.hasActivated = true;
						setTimeout(() => {
							this.container.classList.add('thegem-scroll-bg-activated');
						}, duration);
					}
				} else if (effect === 'wipe') {
					//this.container.style.transition = `color ${duration}ms ${func}`;
					this.container.style.backgroundColor = 'transparent';
					this.createWipeElement(bgColor, duration, func, wipeDir);
				}
			} else if (!this.hasActivated) {
				this.hasActivated = true;
				this.container.classList.add('thegem-scroll-bg-activated');
			}

			this.activeTrigger = trigger;
		}

		createWipeElement(bgColor, duration, func, wipeDir) {
			this.removeWipeElement();

			const wipeDiv = document.createElement('div');
			wipeDiv.className = 'thegem-scroll-wipe-bg';
			const transformOrigin = wipeDir === 'right' ? 'right center' : 'left center';
			const endTransform = 'scaleX(1)';

			wipeDiv.style.cssText = `
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				background-color: ${bgColor};
				z-index: -1;
				transform: scaleX(0);
				transform-origin: ${transformOrigin};
				pointer-events: none;
				will-change: transform;
			`;

			this.container.style.position = 'relative';
			this.container.insertBefore(wipeDiv, this.container.firstChild);

			this.wipeElement = wipeDiv;

			requestAnimationFrame(() => {
				requestAnimationFrame(() => {
					if (this.wipeElement === wipeDiv && wipeDiv.parentNode) {
						wipeDiv.style.transition = `transform ${duration}ms ${func}`;
						wipeDiv.style.transform = endTransform;

						if (!this.hasActivated) {
							this.hasActivated = true;
							setTimeout(() => {
								this.container.classList.add('thegem-scroll-bg-activated');
							}, duration);
						}
					}
				});
			});
		}

		removeWipeElement() {
			if (this.wipeElement && this.wipeElement.parentNode) {
				const wipe = this.wipeElement;
				const duration = this.activeTrigger
					? (parseInt(this.activeTrigger.getAttribute('data-thegem-scroll-bg-duration')) || 500)
					: 500;
				const func = this.activeTrigger
					? (this.activeTrigger.getAttribute('data-thegem-scroll-bg-function') || 'ease')
					: 'ease';

				wipe.style.transition = `transform ${duration}ms ${func}`;
				wipe.style.transform = 'scaleX(0)';

				const onTransitionEnd = () => {
					if (wipe && wipe.parentNode) wipe.remove();
					wipe.removeEventListener('transitionend', onTransitionEnd);
				};
				wipe.addEventListener('transitionend', onTransitionEnd);

				setTimeout(() => {
					if (wipe && wipe.parentNode) wipe.remove();
				}, duration + 100);
			}
			this.wipeElement = null;
		}

		deactivate() {
			if (this.activeTrigger) {
				const effect = this.activeTrigger.getAttribute('data-thegem-scroll-bg-effect') || 'fade';
				let duration = parseInt(this.activeTrigger.getAttribute('data-thegem-scroll-bg-duration')) || 500;
				const func = this.activeTrigger.getAttribute('data-thegem-scroll-bg-function') || 'ease';

				this.addAnimateClass(duration);

				this.container.classList.remove('thegem-scroll-bg-colored');
				this.container.style.removeProperty('--thegem-scroll-bg-text-color');

				if (effect === 'fade') {
					this.container.style.transition = `background-color ${duration}ms ${func}`;
					this.container.style.backgroundColor = this.originalBg || '';
				} else if (effect === 'wipe') {
					this.removeWipeElement();
					//this.container.style.transition = `color ${duration}ms ${func}`;
				}

				this.container.style.color = this.originalColor || '';
				this.activeTrigger = null;
			}
		}

		refresh() {
			this.findTriggers();
			this.checkActiveTrigger();
		}

		destroy() {
			if (this.animationTimer) {
				clearTimeout(this.animationTimer);
			}
			this.deactivate();
			this.removeWipeElement();
			this.triggers.forEach(trigger => {
				trigger.style.transition = '';
				trigger.style.backgroundColor = '';
			});
			this.container.classList.remove('thegem-scroll-bg');
			this.container.classList.remove('thegem-scroll-bg-colored');
			this.container.classList.remove('thegem-scroll-bg-activated');
			this.container.classList.remove('thegem-scroll-bg-animate');
			this.container.style.removeProperty('--thegem-scroll-bg-text-color');
			this.container.style.removeProperty('--thegem-scroll-bg-duration');
			this.container.style.removeProperty('--thegem-scroll-bg-function');
			this.container.style.backgroundColor = this.originalBg || '';
			this.container.style.color = this.originalColor || '';
			this.container.style.transition = '';
			this.container.style.position = '';
		}
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', () => {
			window.scrollBgManager = new ScrollBackgroundManager('.block-content');
		});
	} else {
		window.scrollBgManager = new ScrollBackgroundManager('.block-content');
	}
})();
