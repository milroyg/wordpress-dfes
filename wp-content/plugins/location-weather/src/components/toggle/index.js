import { __ } from '@wordpress/i18n';
import { Popover, ToggleControl } from '@wordpress/components';
import {
	createPortal,
	memo,
	useEffect,
	useRef,
	useState,
} from '@wordpress/element';
import { InfoIcon, VideoTooltipIcon, WeatherTitleIcon } from '../../icons';
import './toggle.scss';

const Toggle = ( {
	label,
	attributes,
	setAttributes = true,
	attributesKey,
	onChange = false,
	updated = false,
	enableInfoIcon = false,
	onlyPro = false,
	InfoText = '',
	videoTooltip = false,
	demoLink = false,
} ) => {
	const [ isVisible, setIsVisible ] = useState( false );
	const [ showVideo, setShowVideo ] = useState( false );

	// ---- FIX: debounce tooltip hide ----
	const hideTimeout = useRef( null );

	const handleEnter = () => {
		clearTimeout( hideTimeout.current );
		setShowVideo( true );
	};

	const handleLeave = () => {
		hideTimeout.current = setTimeout( () => {
			setShowVideo( false );
		}, 120 ); // small delay prevents blinking
	};

	const toggleVisible = () => {
		setIsVisible( ( state ) => ! state );
	};

	return (
		<div
			className={ `spl-weather-toggle-component spl-weather-component-mb sp-align-i-center${
				updated ? ' updated-toggle' : ''
			}${ enableInfoIcon ? ' spl-weather-toggle-info-icon' : '' }${
				onlyPro ? ' spl-only-pro-toggle' : ''
			}` }
		>
			{ onlyPro && ! updated && (
				<div className="spl-weather-toggle-left sp-d-flex sp-align-i-center">
					{ /* { updated && <WeatherTitleIcon /> } */ }
					<span className="spl-pro-title">{ label }</span>
					{ demoLink ? (
						<a
							className="spl-pro-badge"
							href={ demoLink }
							target="_blank
						"
						>
							(Pro)
						</a>
					) : (
						<span className="spl-pro-badge">(Pro)</span>
					) }

					{ videoTooltip && (
						<span
							className="spl-weather-video-tooltip"
							onMouseEnter={ handleEnter }
							onMouseLeave={ handleLeave }
						>
							<VideoTooltipIcon />
						</span>
					) }

					{ showVideo && (
						<Popover
							shift={ true }
							className="spl-video-tooltip-popup-portal"
						>
							<video src={ videoTooltip } autoPlay loop muted />
							<span className="spl-video-tooltip-text">
								{ label }
							</span>
						</Popover>
					) }
				</div>
			) }
			{ ( updated || enableInfoIcon ) && (
				<div className="spl-weather-toggle-left">
					{ /* { updated && <WeatherTitleIcon /> } */ }
					{ onlyPro ? (
						<div className="spl-weather-toggle-left">
							{ /* { updated && <WeatherTitleIcon /> } */ }
							<span className="spl-pro-title">{ label }</span>
							{ onlyPro && (
								<span className="spl-pro-badge">(Pro)</span>
							) }
						</div>
					) : (
						<span>{ label }</span>
					) }

					{ enableInfoIcon && (
						<span
							className="splw-info-icon"
							onMouseEnter={ toggleVisible }
							onMouseLeave={ toggleVisible }
						>
							{ <InfoIcon /> }
						</span>
					) }
				</div>
			) }
			{ /* { enableInfoIcon && (
				<div
					onMouseEnter={ () => setIsVisible( true ) }
					onMouseLeave={ () => setIsVisible( false ) }
					className="toggle-info"
				>
					<div
						className={ `splw-info-popup ${
							isVisible ? 'show' : ''
						}` }
					>
						
						<InfoText />
					</div>
				</div>
			) } */ }
			{ isVisible && (
				<Popover
					variant="unstyled"
					shift={ true }
					placement="top"
					className="splw-info-popup"
				>
					<div
						className="splw-info-popup-content"
						onMouseEnter={ () => setIsVisible( true ) }
						onMouseLeave={ () => setIsVisible( false ) }
					>
						<InfoText />
					</div>
				</Popover>
			) }
			<ToggleControl
				label={
					! ( updated || enableInfoIcon || onlyPro ) ? label : ''
				}
				checked={ attributes }
				onChange={ ( newField ) =>
					onChange
						? onChange( ! newField )
						: setAttributes( {
								[ attributesKey ]: ! attributes,
						  } )
				}
				__nextHasNoMarginBottom
			/>
		</div>
	);
};

export default memo( Toggle );
