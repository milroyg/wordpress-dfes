import { blockRegisterInfo, inArray, navigateToPricing } from '../../controls';
import { __ } from '@wordpress/i18n';
import { RightSymbolIcon } from '../../icons';
import './editor.scss';

const Layout = ( {
	layout,
	handleActive,
	activeLayout,
	displayActive,
	preset,
} ) => {
	const { icon, value, label, onlyPro } = layout;
	const Icon = icon;

	return (
		<div
			onClick={ () => {
				if ( onlyPro ) {
					navigateToPricing();
				} else {
					handleActive( value );
				}
			} }
			className={ `spl-weather-layout-card sp-cursor-pointer sp-d-flex sp-flex-col sp-align-i-center
				${ value === activeLayout ? 'active' : 'inactive' } 
				${ onlyPro ? 'spl-only-pro-card' : '' }` }
		>
			{ value === activeLayout && displayActive && (
				<span className="active-symbol">
					<RightSymbolIcon />
				</span>
			) }
			<div className="splw-preset-icon-wrapper">
				<Icon activeStyle={ value === activeLayout } />
				{ onlyPro && (
					<span className="spl-pro-badge">
						<a
							href="https://locationweather.io/pricing/"
							target="_blank"
							rel="noopener noreferrer"
							className="spl-pro-card-link"
						>
							{ ' ' }
							PRO{ ' ' }
						</a>
					</span>
				) }
			</div>
			{ ! preset && (
				<span className="spl-weather-component-title">{ label }</span>
			) }
		</div>
	);
};

const Layouts = ( {
	items,
	grid = 2,
	label = '',
	attributes,
	setAttributes,
	attributesKey,
	displayActive = false,
	preset = false,
	blockName = '',
} ) => {
	/**
	 * Handle click on a layout card
	 */
	const handleActive = ( value ) => {
		const selectedLayout = items.find( ( item ) => item.value === value );

		// Extract main and additional layout values
		const updatedLayout = selectedLayout?.value;
		const active_additional_data_layout =
			selectedLayout?.additional_layout || false;

		// Update both attributes
		setAttributes( {
			[ attributesKey ]: updatedLayout,
			...( active_additional_data_layout && {
				active_additional_data_layout,
			} ),
		} );
	};
	const modifiedBlockName = `sp-location-weather-pro/${
		blockName === 'vertical' ? 'vertical-card' : blockName
	}`;

	const demoLink = blockRegisterInfo[ modifiedBlockName ]?.demoLink;

	return (
		<div
			className={ `spl-weather-layout-picker spl-weather-component-mb${
				preset &&
				inArray(
					[ 'vertical', 'horizontal', 'aqi-minimal' ],
					blockName
				)
					? ' spl-layout-preset-set'
					: ''
			} ${ blockName }` }
		>
			<div className="spl-weather-component-title-wrapper sp-mb-8px">
				<label className="spl-weather-component-title">{ label }</label>
			</div>
			<div
				className={ `spl-weather-layouts-list sp-d-grid grid-${ grid }` }
			>
				{ items?.map( ( layout, i ) => (
					<Layout
						key={ i }
						layout={ layout }
						displayActive={ displayActive }
						handleActive={ handleActive }
						activeLayout={ attributes }
						preset={ preset }
					/>
				) ) }
			</div>
			{ preset &&
				inArray(
					[ 'vertical', 'horizontal', 'aqi-minimal' ],
					blockName
				) && (
					<div className="spl-see-more-overlay">
						<a
							href={ demoLink }
							target="_blank"
							rel="noopener noreferrer"
							className="spl-pro-link"
						>
							{ __( 'See More', 'location-weather' ) }
						</a>
					</div>
				) }
		</div>
	);
};

export default Layouts;
