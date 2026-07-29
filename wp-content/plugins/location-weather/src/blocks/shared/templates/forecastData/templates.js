import { __ } from '@wordpress/i18n';
import { Fragment, memo, useState } from '@wordpress/element';
import {
	jsonParse,
	inArray,
	weatherItemLabels,
	iconFolderName,
	getWeatherIconByType,
} from '../../../../controls';
import { useTogglePanelBody } from '../../../../context';
import { WeatherArrowIcon } from '../../../vertical/icons';

// forecast components parts.
export const ForecastSelect = memo( ( { value, items, onSelect } ) => {
	const [ active, setActive ] = useState( false );
	const activeTab = items.find( ( item ) => item.value === value );
	const activeItem = activeTab ? activeTab : items[ 0 ];

	return (
		<div className="spl-weather-forecast-select">
			<div
				onClick={ () => setActive( ( prev ) => ! prev ) }
				className="spl-weather-select-active-item sp-d-flex sp-align-i-center sp-justify-between sp-gap-4px"
			>
				<span className="spl-weather-forecast-selected-option">
					{ activeItem?.label }
				</span>
				<span
					className={ `spl-weather-forecast-select-svg ${
						active ? 'active' : 'inactive'
					}` }
				>
					<i className="splwp-icon-chevron"></i>
				</span>
			</div>
			{ active && (
				<ul className="spl-weather-forecast-select-list sp-li-style-none sp-d-flex sp-flex-col">
					{ items?.map( ( item, i ) => (
						<li
							key={ i }
							onClick={ () => {
								onSelect( item );
								setActive( false );
							} }
							className={ `spl-weather-forecast-select-item${
								value === item.value ? ' active' : ''
							}` }
						>
							{ item?.label }
						</li>
					) ) }
				</ul>
			) }
		</div>
	);
} );

export const ForecastHeader = memo(
	( {
		attributes,
		displayData,
		setDisplayData,
		flex = false,
		overviewTitle = false,
	} ) => {
		const { togglePanelBody } = useTogglePanelBody();
		const {
			dataType,
			hourlyTitle,
			forecastTitleArray,
			weatherForecastType,
		} = attributes;

		const forecastSelectOptions = forecastTitleArray
			?.map(
				( { name, value } ) =>
					value && {
						label: weatherItemLabels[ name ],
						value: name,
					}
			)
			?.filter( Boolean );

		return (
			<div
				className={ `spl-weather-forecast-header-area spl-weather-forecast-${
					flex ? 'tabs' : 'select'
				}-header` }
				onClick={ () => togglePanelBody( 'forecast-data', true ) }
			>
				{ ! flex && (
					<div className="spl-weather-forecast-header-type-select sp-d-flex sp-justify-between sp-align-i-center">
						<div className="spl-weather-forecast-title-wrapper">
							<span className="spl-weather-forecast-title">
								{ hourlyTitle }
							</span>
							{ overviewTitle && (
								<span className="spl-weather-forecast-overview-label">
									{ overviewTitle }
								</span>
							) }
						</div>
						<ForecastSelect
							value={ displayData }
							items={ forecastSelectOptions }
							onSelect={ ( item ) => {
								setDisplayData( item.value );
							} }
						/>
					</div>
				) }
				{ flex && (
					<ul className="spl-weather-forecast-tabs">
						{ forecastSelectOptions?.map( ( { value }, i ) => (
							<li
								key={ i }
								className={ `spl-weather-forecast-tab sp-cursor-pointer ${
									value === displayData ? 'active' : ''
								}` }
								onClick={ () => setDisplayData( value ) }
							>
								{ weatherItemLabels[ value ] }
							</li>
						) ) }
					</ul>
				) }
			</div>
		);
	}
);
export const Separator = memo( ( { attributes, maxTemp, minTemp } ) => {
	const { maximumTemp, minimumTemp } = attributes;
	const difference = maximumTemp - minimumTemp;
	let style = { left: '', right: '' };
	let positionInPercentage = 10;

	if ( minTemp !== minimumTemp ) {
		positionInPercentage = ( ( minTemp - minimumTemp ) * 100 ) / difference;
		style.left = `${ positionInPercentage }%`;
	}
	if ( maxTemp === maximumTemp ) {
		style.right = '0';
		style.left = 'auto';
	}

	return (
		<span className="spl-weather-forecast-separator splw-separator-gradient">
			<span style={ style } className="lw-separator"></span>
		</span>
	);
} );

export const DateTimeHTML = memo( ( { forecast, isLayoutThree = false } ) => {
	const { date, forecastType } = forecast;

	return (
		<div className={ `spl-weather-forecast-date-time` }>
			<span className="spl-weather-forecast-time">{ date?.time }</span>
		</div>
	);
} );

export const ForecastImage = ( { iconType, icon, description = false } ) => {
	const baseUrl = `${ splWeatherBlockLocalize.pluginUrl }/assets/images/icons/`;
	const forecastIconFolder = iconFolderName( iconType );
	return (
		<div className="spl-weather-forecast-icon">
			<img
				src={ `${ baseUrl }/${ forecastIconFolder }/${ icon }.svg` }
				alt="weather icon"
				className="weather-icon"
			/>
			{ description && (
				<span className="spl-weather-forecast-description">
					{ description }
				</span>
			) }
		</div>
	);
};
export const TemperatureHTML = memo(
	( { attributes, forecastType, value, separator = 'slash' } ) => {
		const isShowTempMinMaxIcon =
			attributes?.blockName === 'vertical' &&
			'popup' === attributes?.forecastDisplayStyle &&
			'layout-two' === attributes?.forecastPopupLayout
				? true
				: false;
		return (
			<span
				className={ `spl-weather-forecast-value temperature${
					separator === 'gradient'
						? ' sp-d-flex sp-align-i-center sp-justify-end'
						: ''
				}` }
			>
				{ forecastType === '1hourly' && (
					<span className="spl-weather-forecast-max-temp">
						{ value?.now }°
					</span>
				) }
				{ forecastType !== '1hourly' && (
					<>
						<span className="spl-weather-forecast-min-temp">
							{ isShowTempMinMaxIcon && (
								<WeatherArrowIcon
									isDownArrow={ true }
									fill={ '#4AB866' }
									size={ 16 }
								/>
							) }{ ' ' }
							{ value?.min }°
						</span>
						{ ! isShowTempMinMaxIcon && (
							<>
								{ separator === 'gradient' && (
									<Separator
										attributes={ attributes }
										maxTemp={ value?.max }
										minTemp={ value?.min }
									/>
								) }
								<span className="spl-weather-forecast-separator divider">
									{ separator === 'slash' && ' / ' }
									{ separator === 'vertical-bar' && ' | ' }
								</span>
							</>
						) }
						<span className="spl-weather-forecast-max-temp">
							{ isShowTempMinMaxIcon && (
								<WeatherArrowIcon
									isDownArrow={ false }
									fill={ '#F42F2F' }
									size={ 16 }
								/>
							) }
							{ value?.max }°
						</span>
					</>
				) }
			</span>
		);
	}
);
// forecast layout one template.
export const ForecastLayoutOne = memo(
	( {
		forecast,
		attributes,
		separator,
		isLayoutThree,
		showDescriptionWithIcon,
	} ) => {
		const { displayIcon, forecastDataIconType } = attributes;
		const { activeForecast, forecastType, value, weather } = forecast;
		const WrapperTag = isLayoutThree ? 'div' : Fragment;

		return (
			<div className="spl-weather-forecast-container sp-d-flex sp-justify-between sp-align-i-center">
				<WrapperTag
					{ ...( isLayoutThree && {
						className: 'sp-d-flex sp-row-reverse sp-gap-8px',
					} ) }
				>
					<DateTimeHTML
						forecast={ forecast }
						isLayoutThree={ isLayoutThree }
					/>
					{ displayIcon && (
						<ForecastImage
							iconType={ forecastDataIconType }
							icon={ weather?.icon }
							description={
								showDescriptionWithIcon
									? weather?.description
									: false
							}
						/>
					) }
				</WrapperTag>
				<div className="spl-weather-forecast-value-wrapper sp-d-flex sp-justify-end">
					{ activeForecast === 'temperature' && (
						<TemperatureHTML
							value={ value }
							attributes={ attributes }
							forecastType={ forecastType }
							separator={ separator }
							isLayoutThree={ isLayoutThree }
						/>
					) }
					{ activeForecast !== 'temperature' && (
						<span className="spl-weather-forecast-value">
							{ value }
						</span>
					) }
				</div>
			</div>
		);
	}
);
// forecast swiper layout .
export const NavigationHtml = memo( ( { arrow, template = 'forecast' } ) => (
	<>
		<button
			className={ `spl-weather-${ template }-swiper-nav-next spl-weather-swiper-nav ${ template } spl-weather-swiper-nav-next lw-arrow sp-cursor-pointer` }
		>
			<i className={ `splwp-icon-${ arrow } right` }></i>
		</button>
		<button
			className={ `spl-weather-${ template }-swiper-nav-prev spl-weather-swiper-nav ${ template } spl-weather-swiper-nav-prev lw-arrow sp-cursor-pointer` }
		>
			<i className={ `splwp-icon-${ arrow } left` }></i>
		</button>
	</>
) );
export const ForecastLayoutTwo = memo(
	( { forecast, attributes, separator } ) => {
		const { displayIcon, forecastDataIconType } = attributes;
		const { activeForecast, forecastType, value, weather } = forecast;

		return (
			<div className="spl-weather-forecast-container sp-d-flex sp-flex-col sp-align-i-center">
				<DateTimeHTML forecast={ forecast } />
				{ displayIcon && (
					<ForecastImage
						iconType={ forecastDataIconType }
						icon={ weather?.icon }
					/>
				) }
				<div className="spl-weather-forecast-value-wrapper">
					{ activeForecast === 'temperature' && (
						<TemperatureHTML
							value={ value }
							attributes={ attributes }
							forecastType={ forecastType }
							separator={ separator }
						/>
					) }
					{ activeForecast !== 'temperature' && (
						<span className="spl-weather-forecast-value">
							{ value }
						</span>
					) }
				</div>
			</div>
		);
	}
);
// forecast table layout.
export const ForecastTableHeader = memo(
	( {
		isShowTop,
		forecastData,
		iconType = 'icon_set_one',
		forecastType = 'daily',
	} ) => {
		const additionalDataIcon = getWeatherIconByType( iconType );
		const dateLabel = {
			hourly: __( 'Hour', 'location-weather' ),
			daily: __( 'Day', 'location-weather' ),
		};
		return (
			<thead>
				{ isShowTop && (
					<tr className="spl-weather-table-top-header">
						<th>{ __( 'Time', 'location-weather' ) }</th>
						<th colSpan="2">
							{ __( 'Weather Condition', 'location-weather' ) }
						</th>
						<th colSpan="3">
							{ __( 'Comport', 'location-weather' ) }
						</th>
						<th colSpan="3">
							{ __( 'Precipitation', 'location-weather' ) }
						</th>
					</tr>
				) }
				<tr className="spl-weather-table-header">
					{ forecastData?.map( ( { name }, i ) => (
						<th key={ i } scope="col">
							{ additionalDataIcon[ name ] && (
								<span className="spl-weather-details-icon">
									<i
										className={
											additionalDataIcon[ name ] || ''
										}
									></i>
								</span>
							) }
							{ name === 'date' && dateLabel[ forecastType ] }
							{ name === 'weather' &&
								__( 'Condition', 'location-weather' ) }
							{ name === 'precipitation' && (
								<>
									{ isShowTop
										? __( 'Amount', 'location-weather' )
										: __(
												'Precipitation',
												'location-weather'
										  ) }
								</>
							) }
							{ ! inArray(
								[ 'date', 'weather', 'precipitation' ],
								name
							) && weatherItemLabels[ name ] }
						</th>
					) ) }
				</tr>
			</thead>
		);
	}
);
export const ForecastTableRow = memo(
	( { attributes, forecast, separator } ) => {
		const { forecastTitle, forecastDataIconType } = attributes;
		const { weather, forecastType } = forecast;

		return (
			<tr className="spl-weather-forecast-table-row">
				{ forecastTitle?.map( ( { name }, i ) => (
					<td
						key={ i }
						className={ `spl-weather-table-forecast-${ name }` }
					>
						{ name === 'date' && (
							<DateTimeHTML forecast={ forecast } />
						) }
						{ name === 'weather' && (
							<div className="spl-weather-forecast-icon">
								<ForecastImage
									iconType={ forecastDataIconType }
									icon={ weather?.icon }
								/>
								<span className="spl-weather-forecast-description">
									{ weather?.description }
								</span>
							</div>
						) }
						{ name === 'temperature' && (
							<TemperatureHTML
								value={ forecast[ 'temperature' ] }
								attributes={ attributes }
								separator={ separator }
								forecastType={ forecastType }
							/>
						) }
						{ ! inArray(
							[ 'weather', 'date', 'temperature' ],
							name
						) && (
							<span className="spl-weather-forecast-value sp-d-flex sp-align-i-center">
								{ forecast[ name ] }
							</span>
						) }
					</td>
				) ) }
			</tr>
		);
	}
);
