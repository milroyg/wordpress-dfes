/**
 * Description: Cette méthode est déclenchée lorsque les sections 'eac-addon-articles-liste' ou 'eac-addon-product-grid' sont chargées dans la page
 *
 * @param {selector} $scope. Le contenu de la section
 * @since 1.0.0
 * @since 2.3.2 Global link pour chaque tiem
 * @since 2.3.3 Force la hauteur des items
 * @since 2.3.4 Utilisation de la lib 'fit-rows' pour gérer la hauteur des lignes en mode grid
 */
import { EacSwiper, setGridItemsGlobalLink } from '../modules/eac-modules.js';

class widgetArticlesListe extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                targetInstance: '.eac-post-grid',
                targetWrapper: '.al-posts__wrapper',
                articleWrapper: '.al-post__wrapper',
                imagesInstance: '.al-post__image-loaded',
                targetCards: '.al-post__inner-wrapper',
                targetSkipGrid: '.eac-skip-grid',
                filterWrapper: '.al-filters__wrapper a',
                filterWrapperSelect: '.al-filters__select',
                buttonPaged: '.al-more-button-paged',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        let components = {
            $targetInstance: this.$element.find(selectors.targetInstance),
            $targetWrapper: this.$element.find(selectors.targetWrapper),
            $articleWrapper: this.$element.find(selectors.articleWrapper),
            $imagesInstance: this.$element.find(selectors.imagesInstance),
            $targetCards: this.$element.find(selectors.targetCards),
            $targetSkipGrid: this.$element.find(selectors.targetSkipGrid),
            $filterWrapper: this.$element.find(selectors.filterWrapper),
            $filterWrapperSelect: this.$element.find(selectors.filterWrapperSelect),
            $buttonPaged: this.$element.find(selectors.buttonPaged),
            settings: this.$element.find(selectors.targetWrapper).data('settings') || {},
            $targetId: null,
            $paginationId: null,
            $navigationId: null,
            widgetId: this.$element.data('id'),
            iso: null,
            isotopeOptions: {
                itemSelector: '.al-post__wrapper',
                percentPosition: true,
                masonry: {
                    columnWidth: '.al-posts__wrapper-sizer',
                },
                fitRows: {
                    equalheight: false,
                },
                layoutMode: 'fitRows',
                sortBy: 'original-order',
                visibleStyle: { transform: 'scale(1)', opacity: 1 },
            },
            infiniteScrollOptions: {
                path: function () { return location.pathname.replace(/\/?$/, '/') + "page/" + parseInt(this.pageIndex + 1); },
                debug: false,
                button: '',
                status: '',
                scrollThreshold: false,
                history: false,
                horizontalOrder: false,
            },
            has_swiper: false,
        };
        components.$targetId = jQuery(components.settings.data_id);
        components.isotopeOptions.layoutMode = components.settings.data_layout;
        components.isotopeOptions.fitRows.equalheight = components.settings.data_layout === 'fitRows' ? true : false;
        components.$paginationId = components.settings.data_pagination ? jQuery(components.settings.data_pagination_id) : null;
        components.$navigationId = components.settings.data_navigation ? jQuery(components.settings.data_navigation_id) : null;
        components.infiniteScrollOptions.button = components.settings.data_pagination_id + ' button';
        components.infiniteScrollOptions.status = components.settings.data_pagination_id + ' .al-page-load-status';
        components.has_swiper = components.settings.data_sw_swiper || false;

        return components;
    }

    onInit() {
        super.onInit();

        if (this.elements.has_swiper) {
            new EacSwiper(this.elements.settings, this.elements.$targetInstance);
        } else {
            /** Les images sont désactivées et pas de lazy load, il faut initialiser Isotope */
            if (this.elements.$imagesInstance.length === 0 || !this.elements.settings.data_lazy) {
                this.elements.$targetId.isotope(this.elements.isotopeOptions);
            }

            if (this.elements.settings.data_filtre) {
                window.setTimeout(() => { this.setFilterWithWindowLocation(); }, 500);
            }

            if (this.elements.settings.data_pagination && !elementorFrontend.isEditMode()) {
                this.elements.$targetId.infiniteScroll(this.elements.infiniteScrollOptions);
            }
        }

        /** @since 2.3.2 */
        if (!elementorFrontend.isEditMode()) {
            setGridItemsGlobalLink(this.elements.$targetCards);
        }
    }

    bindEvents() {
        if (!elementorFrontend.isEditMode()) {
            if (this.elements.settings.data_filtre) {
                this.elements.$filterWrapper.on('click', (evt) => { this.onFilterGridClick(evt); });
                this.elements.$filterWrapperSelect.on('change', (evt) => { this.onFilterGridChange(evt); });
            }

            if (this.elements.settings.data_pagination) {
                this.elements.$targetId.on('load.infiniteScroll', this.onLoadInfiniteScroll.bind(this));
            } else if (this.elements.settings.data_navigation) {
                this.setAccessibilityElements();
            }

            if (this.elements.$targetInstance.length > 0) {
                this.elements.$targetInstance.on('keydown', (evt) => { this.setKeyboardEvents(evt); });
            }
        }

        /** @since 2.3.4 Dans l'éditeur Elementor désactive le lazyload, les images sont toutes chargées */
        /**if (!this.elements.has_swiper) {
            this.elements.$imagesInstance.one('load', (evt) => {
                if (evt.currentTarget.complete) {
                    this.elements.$targetId.isotope('layout');
                } else {
                    console.log('Not loaded => ' + evt.currentTarget.getAttribute('alt') + ' :: src => ' + evt.currentTarget.getAttribute('src'));
                }
            });
        }*/

        /** @since 2.3.4 Dans l'éditeur Elementor désactive le lazyload, les images sont toutes chargées */
        if (!this.elements.has_swiper && this.elements.$imagesInstance.length > 0 && this.elements.settings.data_lazy) {
            jQuery.each(this.elements.$imagesInstance, (index, image) => {
                new IntersectionObserver(this.observeImageInViewport.bind(this), { threshold: 0.5 }).observe(image);
            });
        }

        /** Elementor nested tab déclenche event resize */
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

    /** Filtre dans l'URL pour le widget WC */
    setFilterWithWindowLocation() {
        const that = this;
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        const filter = urlParams.has('filter') ? decodeURIComponent(urlParams.get('filter')) : false;
        let domInterval = 0;
        if (filter) {
            let domProgress = window.setInterval(function () {
                if (domInterval < 5) {
                    const $data_filter = jQuery(".al-filters__wrapper a[data-filter='." + filter + "']", that.elements.$targetInstance);
                    const $data_select = jQuery('.al-filters__wrapper-select .al-filters__select', that.elements.$targetInstance);
                    if ($data_filter.length === 1 && $data_select.length === 1) {
                        window.clearInterval(domProgress);
                        domProgress = null;
                        $data_filter.trigger('click');
                        $data_filter.trigger('focus');
                        $data_select.val('.' + filter);
                        $data_select.trigger('change');
                    } else {
                        domInterval++;
                    }
                } else {
                    window.clearInterval(domProgress);
                    domProgress = null;
                }
            }, 100);
        }
    }

    onFilterGridClick(evt) {
        const $this = jQuery(evt.currentTarget);
        const $optionSet = $this.parents('.al-filters__wrapper');
        evt.preventDefault();
        // L'item du filtre est déjà sélectionné
        if ($this.parents('.al-filters__item').hasClass('al-active')) {
            return;
        }

        $optionSet.find('.al-active').removeClass('al-active');
        $this.parents('.al-filters__item').addClass('al-active');

        // Applique le filtre
        this.elements.$targetId.isotope({ filter: $this.attr('data-filter') });
    }

    onFilterGridChange(evt) {
        evt.preventDefault();
        // Applique le filtre
        this.elements.$targetId.isotope({ filter: evt.currentTarget.value });
    }

    /** Pagination */
    onLoadInfiniteScroll(evt, response, path) {
        const infScroll = this.elements.$targetId.data('infiniteScroll');
        const selecteur = 'button, a, [tabindex]:not([tabindex="-1"])';
        const selectedItems = '.' + this.elements.settings.data_article + '.al-post__wrapper';
        const $items = jQuery(response).find(selectedItems);     // Recherche les nouveaux items
        const $cards = $items.find('.al-post__inner-wrapper');
        const $firstLoadedItem = $items.find(selecteur).first(); // Recherche du premier élément focusable dans les nouveaux items

        this.elements.$targetId.append($items).isotope('appended', $items);
        $items.imagesLoaded()
            .progress((instance, image) => {
                if (!image.isLoaded) {
                    console.log('Not loaded => ' + image.img.getAttribute('alt') + ' :: src => ' + image.img.getAttribute('src'));
                }
            }).always(() => {
                this.elements.$targetId.isotope(this.elements.isotopeOptions);
                window.setTimeout(() => {
                    $firstLoadedItem.trigger('focus');
                    /** @since 2.3.2 */
                    setGridItemsGlobalLink($cards);
                }, 200);
            });

        // On teste l'égalité entre le nombre de page totale et celles chargées dans infiniteScroll
        // lorsque le pagging s'applique sur une 'static page' ou 'front page'
        if (parseInt(infScroll.pageIndex) >= parseInt(this.elements.settings.data_max_pages)) {
            this.elements.$targetId.infiniteScroll('destroy'); // Destroy de l'instance
            this.elements.$paginationId.remove(); // Supprime la div status
        } else {
            const nbPosts = parseInt(this.elements.$buttonPaged.text().split('/')[0]) + $items.length;
            const totalPosts = nbPosts + '/' + this.elements.settings.data_found_posts;
            this.elements.$buttonPaged.text(totalPosts); // modifie le nombre d'éléments chargés du bouton 'MORE'
        }
    }

    /** Navigation clavier */
    setKeyboardEvents(evt) {
        const selecteur = 'button, a, [tabindex]:not([tabindex="-1"])';
        const id = evt.code || evt.key || 0;
        let $targetArticleFirst = null;
        let $targetArticleLast = null;

        if (this.elements.settings.data_filtre) {
            const elementsFiltered = this.elements.$targetId.isotope('getFilteredItemElements');
            $targetArticleFirst = jQuery(elementsFiltered).find(selecteur).first();
            $targetArticleLast = jQuery(elementsFiltered).find(selecteur).last();
        } else {
            const $targetArticlesWithLink = this.elements.$targetInstance.find('article').find(selecteur);
            $targetArticleFirst = $targetArticlesWithLink.first();
            $targetArticleLast = $targetArticlesWithLink.last();
        }

        if (('Tab' === id && !evt.shiftKey) || ('Tab' === id && evt.shiftKey)) {
            return true;
        } else if ('Home' === id) {
            evt.preventDefault();
            $targetArticleFirst.trigger('focus');
        } else if ('End' === id) {
            evt.preventDefault();
            $targetArticleLast.trigger('focus');
        } else if ('Escape' === id) {
            this.elements.$targetSkipGrid.trigger('focus');
        }
    }

    /** Pagination wordpress ajout des attributs aria-label */
    setAccessibilityElements() {
        const $numbers = this.elements.$navigationId.find('a.page-numbers').not('.current').not('.dots');
        jQuery.each($numbers, (index, number) => {
            if (jQuery(number).hasClass('next')) {
                jQuery(number).attr('aria-label', 'Next page');
            } else if (jQuery(number).hasClass('prev')) {
                jQuery(number).attr('aria-label', 'Previous page');
            } else {
                jQuery(number).attr('aria-label', 'Page ' + number.innerText);
            }
        });
    }
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-articles-liste & eac-addon-product-grid' sont chargés dans la page
 *
 * @param elements (Ex: $scope)
 */
jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('eac-addon-articles-liste', widgetArticlesListe);
    elementorFrontend.elementsHandler.attachHandler('eac-addon-product-grid', widgetArticlesListe);
});
