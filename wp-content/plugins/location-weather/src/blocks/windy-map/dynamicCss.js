import { filterResponsiveDynamicCss } from '../../controls';
import { mapDynamicCss } from '../shared/templates/maps';

const dynamicCss = ( attributes, page = 'editor' ) => {
	const { mapDesktopCss, mapTabletCss, mapMobileCss } =
		mapDynamicCss( attributes );

	const { uniqueId, splwHideOnDesktop, splwHideOnTablet, splwHideOnMobile } =
		attributes;

	let desktopCss = [ ...mapDesktopCss ];
	let tabletCss = [ ...mapTabletCss ];
	let mobileCss = [ ...mapMobileCss ];

	// visibility show hide css.
	if ( splwHideOnDesktop ) {
		desktopCss = [
			...desktopCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'none',
				},
			},
		];
	}

	if ( splwHideOnTablet ) {
		tabletCss = [
			...tabletCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'none',
				},
			},
		];
	} else {
		tabletCss = [
			...tabletCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'block',
				},
			},
		];
	}

	if ( splwHideOnMobile ) {
		mobileCss = [
			...mobileCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'none',
				},
			},
		];
	} else {
		mobileCss = [
			...mobileCss,
			{
				selector: `#${ uniqueId }`,
				styles: {
					display: 'block',
				},
			},
		];
	}

	const dynamicCss = filterResponsiveDynamicCss(
		{
			desktopCss,
			tabletCss,
			mobileCss,
		},
		page
	);

	return dynamicCss;
};
export default dynamicCss;
