
/**
 * Description: Cette class est déclenchée lorsque la section 'eac-addon-images-comparison' est chargée dans la page
 *
 * @since 1.8.7
 */

class widgetImagesComparison extends elementorModules.frontend.handlers.Base {

	getDefaultSettings() {
		return {
			selectors: {
				target: '.images-comparison',
			},
		};
	}

	getDefaultElements() {
		const selectors = this.getSettings('selectors');
		let components = {
			$target: this.$element.find(selectors.target),
			settings: this.$element.find(selectors.target).data('settings') || {},
			leftTitle: '',
			rightTitle: '',
		};
		components.leftTitle = components.settings.data_title_left;
		components.rightTitle = components.settings.data_title_right;
		return components;
	}

	onInit() {
		super.onInit();

		this.elements.$target.imagesLoaded().always(() => {
			jQuery(this.elements.settings.data_diff).simpleImageDiff({ titles: { before: this.elements.leftTitle, after: this.elements.rightTitle } });
		});
	}
}

jQuery(window).on('elementor/frontend/init', () => {
	elementorFrontend.elementsHandler.attachHandler('eac-addon-images-comparison', widgetImagesComparison);
});