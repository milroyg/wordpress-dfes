/**
 * Description: Cette méthode est déclenchée lorsque le composant 'eac-addon-mega-menu' est chargé dans la page
 * Ref: * Elementor 3.1.0 https://developers.elementor.com/a-new-method-for-attaching-a-js-handler-to-an-element/
 * 
 * @param $element. Le contenu du widget
 * @since 1.0.0
 * @since 2.3.2 Global link pour chaque tiem
 * @since 2.3.3 Force la hauteur des items
 * @since 2.3.4 Utilisation de la lib 'fit-rows' pour gérer la hauteur des lignes en mode grid
 */
import { EacSwiper, setGridItemsGlobalLink } from '../modules/eac-modules.js';

class widgetImageGalerie extends elementorModules.frontend.handlers.Base {
	getDefaultSettings() {
		return {
			selectors: {
				targetInstance: '.eac-image-galerie',
				target: '.image-galerie',
				targetSkipGrid: '.eac-skip-grid',
				targetCards: '.image-galerie__inner-wrapper',
				imagesInstance: '.image-galerie__image-instance',
				itemsInstance: '.image-galerie__item',
				targetJustify: '.fj-gallery',
				filterWrapperLink: '.ig-filters__wrapper a',
				filterWrapperSelect: '.ig-filters__select',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		let components = {
			$targetInstance: this.$element.find(selectors.targetInstance),
			$target: this.$element.find(selectors.target),
			$targetSkipGrid: this.$element.find(selectors.targetSkipGrid),
			$targetCards: this.$element.find(selectors.targetCards),
			$imagesInstance: this.$element.find(selectors.imagesInstance),
			$itemsInstance: this.$element.find(selectors.itemsInstance),
			$targetJustify: this.$element.find(selectors.targetJustify),
			$filterWrapperLink: this.$element.find(selectors.filterWrapperLink),
			$filterWrapperSelect: this.$element.find(selectors.filterWrapperSelect),
			settings: this.$element.find(selectors.target).data('settings'),
			$targetId: null,
			widgetId: this.$element.data('id'),
			iso: null,
			isotopeOptions: {
				itemSelector: '.image-galerie__item',
				percentPosition: true,
				masonry: {
					columnWidth: '.image-galerie__item-sizer',
				},
				fitRows: {
					equalheight: false,
				},
				layoutMode: 'fitRows',
				sortBy: 'original-order',
				visibleStyle: { transform: 'scale(1)', opacity: 1 }, // Transition
			},
			justifyOptions: {
				itemSelector: '.fj-gallery-item',
				rowHeight: 250,
				rowHeightTolerance: 0,
				edgeCaseMinRowHeight: 1,
				gutter: 10,
				calculateItemsHeight: true
			},
			has_swiper: false,
			activeBreakpoints: elementorFrontend.config.responsive.activeBreakpoints,
			windowWidthMob: 0,
			windowWidthMobExtra: 0,
			windowWidthTab: 0,
			windowWidthTabExtra: 0,
			windowWidthLaptop: 0,
			windowWidthWidescreen: 0,
		};
		components.$targetId = jQuery(components.settings.data_id);
		components.isotopeOptions.layoutMode = components.settings.data_layout;
		components.isotopeOptions.fitRows.equalheight = components.settings.data_layout === 'fitRows' ? true : false;
		components.has_swiper = components.settings.data_sw_swiper || false;
		components.justifyOptions.gutter = components.settings.data_gutter;
		components.justifyOptions.rowHeight = components.settings.data_rowheight;

		return components;
	}

	onInit() {
		super.onInit();

		if (this.elements.has_swiper) {
			new EacSwiper(this.elements.settings, this.elements.$targetInstance);
		} else if ('justify' !== this.elements.settings.data_layout) {
			if (this.elements.settings.data_metro) {
				this.elements.$itemsInstance.eq(0).addClass('layout-type-metro');
			} else {
				this.elements.$itemsInstance.eq(0).removeClass('layout-type-metro');
			}
		}

		/** @since 2.3.2 */
		if (!elementorFrontend.isEditMode()) {
			setGridItemsGlobalLink(this.elements.$targetCards);
		}
	}

	bindEvents() {
		if (!elementorFrontend.isEditMode()) {
			if (!this.elements.has_swiper && this.elements.settings.data_filtre) {
				this.elements.$filterWrapperLink.on('click', (evt) => { this.onFilterGridClick(evt); });
				this.elements.$filterWrapperSelect.on('change', (evt) => { this.onFilterGridChange(evt); });
			}

			if (this.elements.$itemsInstance.length > 0) {
				this.elements.$targetInstance.on('keydown', (evt) => { this.setKeyboardEvents(evt); });
			}
		}

		/** @since 2.3.4 Dans l'éditeur Elementor désactive le lazyload, les images sont toutes chargées */
		if (!this.elements.has_swiper) {
			if ('justify' !== this.elements.settings.data_layout) {
				jQuery.each(this.elements.$imagesInstance, (index, image) => {
					new IntersectionObserver(this.observeImageInViewport.bind(this), { threshold: 0.5 }).observe(image);
				});
			} else {
				new IntersectionObserver(this.observeJustifyInViewport.bind(this)).observe(this.elements.$targetId[0]);
			}
		}

		/** Elementor nested tab trigger event resize */
		jQuery('.e-n-tab-title').on('click', () => {
			setTimeout(() => {
				window.dispatchEvent(new Event('resize'));
			}, 50);
		});
	}

	/** @since 2.3.4 */
	observeImageInViewport(entries, observer) {
		if (entries[0].isIntersecting) {
			const img = entries[0].target;
			if (this.elements.settings.data_animate) {
                jQuery(img).closest('article').addClass('animate-first-load');
            }
			observer.unobserve(img);
			this.elements.$targetId.isotope(this.elements.isotopeOptions);
			window.setTimeout(() => { jQuery(img).closest('article').removeClass('animate-first-load'); }, 1000);
		}
	}

	/** @since 2.3.4 */
	observeJustifyInViewport(entries, observer) {
		if (entries[0].isIntersecting) {
			const target = entries[0].target;
			this.elements.$targetId.imagesLoaded().always(() => {
				this.elements.$targetJustify.fjGallery(this.elements.justifyOptions);
			});
			observer.unobserve(target);
		}
	}

	onFilterGridClick(evt) {
		const $this = jQuery(evt.currentTarget);
		const $optionSet = $this.parents('.ig-filters__wrapper');
		evt.preventDefault();

		// L'item du filtre est déjà sélectionné
		if ($this.parents('.ig-filters__item').hasClass('ig-active')) {
			return;
		}

		$optionSet.find('.ig-active').removeClass('ig-active');
		$this.parents('.ig-filters__item').addClass('ig-active');
		this.elements.$targetId.isotope({ filter: $this.attr('data-filter') });
	}

	onFilterGridChange(evt) {
		evt.preventDefault();
		// Applique le filtre
		this.elements.$targetId.isotope({ filter: evt.currentTarget.value });
	}

	onElementChange(propertyName) {
		if ('ig_image_height' === propertyName && 'justify' === this.elements.settings.data_layout) {
			let heightRow = this.elements.justifyOptions.rowHeight;

			if ('ig_image_height' === propertyName) {
				heightRow = this.getElementSettings('ig_image_height')['size'];
			} else if ('ig_image_height_tablet_extra' === propertyName) {
				heightRow = this.getElementSettings('ig_image_height_tablet_extra')['size'];
			} else if ('ig_image_height_tablet' === propertyName) {
				heightRow = this.getElementSettings('ig_image_height_tablet')['size'];
			} else if ('ig_image_height_mobile_extra' === propertyName) {
				heightRow = this.getElementSettings('ig_image_height_mobile_extra')['size'];
			} else if ('ig_image_height_mobile' === propertyName) {
				heightRow = this.getElementSettings('ig_image_height_mobile')['size'];
			} else if ('ig_image_height_laptop' === propertyName) {
				heightRow = this.getElementSettings('ig_image_height_laptop')['size'];
			}
			this.elements.justifyOptions.rowHeight = heightRow;
			this.elements.$targetId.imagesLoaded().always(() => {
				this.elements.$targetJustify.fjGallery(this.elements.justifyOptions);
			});
		}
	}

	setKeyboardEvents(evt) {
		const selecteur = 'button, a, [tabindex]:not([tabindex="-1"])';
		const id = evt.code || evt.key || 0;
		let $targetArticleFirst = null;
		let $targetArticleLast = null;
		let $targetArticleContentFirst = null;
		let $targetArticleContentLast = null;

		if (this.elements.settings.data_filtre) {
			const elementsFiltered = this.elements.$targetId.isotope('getFilteredItemElements');
			const $targetArticlesWithLinkFiltered = jQuery(elementsFiltered).find(selecteur);
			$targetArticleFirst = $targetArticlesWithLinkFiltered.first();
			$targetArticleLast = $targetArticlesWithLinkFiltered.last();
		} else {
			const $targetArticlesWithLink = this.elements.$itemsInstance.find(selecteur);
			$targetArticleFirst = $targetArticlesWithLink.first();
			$targetArticleLast = $targetArticlesWithLink.last();
		}
		$targetArticleContentFirst = $targetArticleFirst.closest('.image-galerie__content');
		$targetArticleContentLast = $targetArticleLast.closest('.image-galerie__content');

		if (('Tab' === id && !evt.shiftKey) || ('Tab' === id && evt.shiftKey)) {
			return true;
		} else if ('Home' === id) {
			evt.preventDefault();
			if (this.elements.settings.data_overlay === 'overlay-out') {
				$targetArticleFirst.trigger('focus');
			} else {
				$targetArticleContentFirst.trigger('focus');
				window.setTimeout(() => { $targetArticleFirst.focus(); }, 500);
			}
		} else if ('End' === id) {
			evt.preventDefault();
			if (this.elements.settings.data_overlay === 'overlay-out') {
				$targetArticleLast.trigger('focus');
			} else {
				$targetArticleContentLast.trigger('focus');
				window.setTimeout(() => { $targetArticleLast.focus(); }, 500);
			}
		} else if ('Escape' === id) {
			this.elements.$targetSkipGrid.trigger('focus');
		}
	}
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-image-galerie' est chargé dans la page
 *
 * @param elements (Ex: $scope)
 */
jQuery(window).on('elementor/frontend/init', () => {
	elementorFrontend.elementsHandler.attachHandler('eac-addon-image-galerie', widgetImageGalerie);
});