import { __ } from '@wordpress/i18n';
import { Popover } from '@wordpress/components';
import { useEffect, useState } from '@wordpress/element';
import { PopOverToggleIcon } from '../../icons';
import { getRandomId } from '../../controls';
import './editor.scss';

// compound component.
const SpPopover = ( { children, label = 'popover' } ) => {
	const [ open, setOpen ] = useState( false );
	const uniqueId = `${ getRandomId( 'spl-weather' ) }popover`;

	useEffect( () => {
		const clickOutSite = ( e ) => {
			const componentView = e.target.closest(
				'.spl-weather-popover__content-visible'
			);
			const colorPickerPopover = e.target.closest(
				'.components-popover'
			);
			const buttonTarget = e.target.closest(
				`.spl-weather-popover-toggle-button.${ uniqueId }`
			);
			const select = e.target.closest( '.css-1nmdiq5-menu' );
			if (
				open &&
				! componentView &&
				! buttonTarget &&
				! select &&
				! colorPickerPopover
			) {
				setOpen( false );
			}
		};
		window.addEventListener( 'click', clickOutSite );

		return () => window.removeEventListener( 'click', clickOutSite );
	} );

	return (
		<div className="spl-weather-popover-component spl-weather-component-mb">
			{ label && (
				<div className="spl-weather-popover-toggle-wrapper sp-d-flex sp-justify-between">
					<span className="spl-weather-component-title">
						{ label }
					</span>
					<button
						onClick={ () => setOpen( ( prev ) => ! prev ) }
						className={ `spl-weather-popover-toggle-button ${ uniqueId } sp-m-0 sp-p-0 sp-cursor-pointer${
							open ? ' active' : ''
						}` }
					>
						<PopOverToggleIcon />
					</button>
				</div>
			) }
			{ open && (
				<Popover shift={ true }>
					<div
						className="spl-weather-popover__content-visible sp-location-weather-tabs-panel"
						onClick={ ( e ) => e.stopPropagation() }
					>
						<div className="spl-weather-popover__content-visible-content">
							{ children }
						</div>
					</div>
				</Popover>
			) }
		</div>
	);
};

SpPopover.Header = ( { children } ) => (
	<div className="spl-weather-popover__content-visible-label">
		{ children }
	</div>
);

SpPopover.Content = ( { children } ) => (
	<div className="spl-weather-popover__content">{ children }</div>
);

export default SpPopover;
