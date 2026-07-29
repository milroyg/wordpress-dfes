/**
 * Description: Cette méthode est déclenchée lorsque le composant 'eac-addon-acf-repeater' est chargé dans la page
 *
 * @since 1.9.7
 */
import { setGridItemsGlobalLink } from '../modules/eac-modules.js';

class widgetRepeaterACF extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
        return {
            selectors: {
                targetQuestion: '.acf-repeater_faq-question',
                targetResponse: '.acf-repeater_faq-response',
                targetTogglerSvg: '.acf-repeater_faq-toggler svg',
                targetTogglerAwe: '.acf-repeater_faq-toggler i',
                targetCards: '.acf-repeater_container-wrapper',
                targetFile: '.acf-repeater_file a',
                targetFancybox: '.acf-repeater_file a[data-fancybox]',
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $targetQuestion: this.$element.find(selectors.targetQuestion),
            $targetResponse: this.$element.find(selectors.targetResponse),
            $targetTogglerSvg: this.$element.find(selectors.targetTogglerSvg),
            $targetTogglerAwe: this.$element.find(selectors.targetTogglerAwe),
            $targetCards: this.$element.find(selectors.targetCards),
            $targetFile: this.$element.find(selectors.targetFile),
            $targetFancybox: this.$element.find(selectors.targetFancybox),

        };
    }

    onInit() {
        super.onInit();

        if (!elementorFrontend.isEditMode() && this.elements.$targetCards.length > 0) {
            setGridItemsGlobalLink(this.elements.$targetCards);
        }
    }

    bindEvents() {
        const that = this;

        this.elements.$targetQuestion.on('click', (evt) => { this.onHeadClickKeyboardEvent(evt); });

        if (elementorFrontend.isEditMode()) {
            this.elements.$targetFile.on('click', function (evt) { evt.preventDefault(); }); // Le fancybox ne s'ouvre pas dans l'éditeur
        } else if (!elementorFrontend.isEditMode()) {
            this.elements.$targetQuestion.on('keydown', (evt) => { this.onHeadClickKeyboardEvent(evt); });

            if (this.elements.$targetFancybox.length) {
                this.elements.$targetFancybox.fancybox({
                    idleTime: false,
                    afterShow: function (instance, current) {
                        const $content = current.$content;
                        const $closeBtn = instance.$refs.container.find('.fancybox-button--close').first();
                        if ($closeBtn.length) {
                            $closeBtn.focus();
                            $closeBtn.css({
                                'outline': '#fff solid 3px',
                                'outline-offset': '-3px',
                            });
                        }

                        /** Accessibilité */
                        $content.attr('aria-modal', 'true');
                        $content.attr('role', 'dialog');
                        that.elements.$targetFile.attr('aria-expanded', 'true');
                    },
                    afterClose: function (instance, current) {
                        that.elements.$targetFile.attr('aria-expanded', 'false');
                    }
                });
            }
        }
    }

    onHeadClickKeyboardEvent(evt) {
        const $this = jQuery(evt.currentTarget);
        const selectors = this.getSettings('selectors');
        const $targetResponse = $this.next();

        if ('keydown' === evt.type) {
            const id = evt.code || evt.key || 0;
            if ('Enter' !== id && 'Space' !== id) {
                return;
            }
        }
        evt.preventDefault();

        if ($this.hasClass('open')) {
            $this.removeClass('open')
            $this.attr('aria-expanded', 'false');
            jQuery(selectors.targetTogglerSvg, $this).css('transform', 'rotate(0deg)');
            jQuery(selectors.targetTogglerAwe, $this).css('transform', 'rotate(0deg)');
            $targetResponse.slideUp(300);
            return;
        } else {
            this.elements.$targetQuestion.removeClass('open');
            this.elements.$targetQuestion.attr('aria-expanded', 'false');
            this.elements.$targetTogglerSvg.css('transform', 'rotate(0deg)');
            this.elements.$targetTogglerAwe.css('transform', 'rotate(0deg)');
            this.elements.$targetResponse.slideUp(300);
        }

        $this.addClass('open');
        $this.attr('aria-expanded', 'true');
        jQuery(selectors.targetTogglerSvg, $this).css('transform', 'rotate(180deg)');
        jQuery(selectors.targetTogglerAwe, $this).css('transform', 'rotate(180deg)');
        $targetResponse.slideDown(300);
    }
}

/**
 * Description: La class est créer lorsque le composant 'eac-addon-acf-repeater' est chargé dans la page
 *
 * @param elements (Ex: $scope)
 * @since 2.1.3
 */
jQuery(window).on('elementor/frontend/init', () => {
    elementorFrontend.elementsHandler.attachHandler('eac-addon-acf-repeater', widgetRepeaterACF);
});
