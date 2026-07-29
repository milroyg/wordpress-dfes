import './style.scss';
import Select from './Select';
import {
	GridTwoColIcon,
	GridThreeColIcon,
	HeartIcon,
	CloseIcon,
	RotateIcon,
	LocationWeatherLogo,
} from '../icons';
import { FILTER_OPTIONS } from '../constants';
import { Tooltip } from '@wordpress/components';
import { currentBlockTitle } from '../../controls';
const { __ } = wp.i18n;

export const FilterPart = ( props ) => {
	const {
		changeStates,
		column,
		showWishList,
		_fetchFile,
		fetching,
		searchQuery,
		fields,
		fieldValue,
		fieldOptions,
		wishListArr,
	} = props;

	return (
		<div className="splw-patterns-template-filter">
			<div className="splw-patterns-template-filter-search">
				<span>
					<i className="splw-icon-search-icon"></i>
				</span>
				<input
					type="search"
					id="splw-patterns-template-filter-search-field"
					placeholder="Search..."
					className="splw-patterns-template-filter-search-input"
					value={ searchQuery }
					onChange={ ( e ) => {
						changeStates &&
							changeStates( 'search', e.target.value );
					} }
				/>
			</div>
			<div className="splw-patterns-template-filter-sort">
				{ fields?.trend && fieldValue?.trend && (
					<Select
						value={ fieldValue?.trend }
						onChange={ ( v ) => {
							changeStates( 'trend', v );
						} }
						options={ FILTER_OPTIONS.TREND.map( ( option ) => ( {
							...option,
							label: option.label,
						} ) ) }
					/>
				) }
			</div>

			<div
				className="splw-patterns-template-filter-grid-two-col"
				onClick={ () => changeStates( 'column', '2' ) }
			>
				<button className={ ` ${ column == '2' ? 'active' : '' }` }>
					<GridTwoColIcon />
				</button>
			</div>
			<div
				className={ `splw-patterns-template-filter-grid-three-col ${
					column == '3' ? 'splw-patterns-col-active' : ''
				}` }
				onClick={ () => changeStates( 'column', '3' ) }
			>
				<button className={ ` ${ column == '3' ? 'active' : '' }` }>
					<GridThreeColIcon />
				</button>
			</div>
			<Tooltip
				text={ __( 'Refresh Patterns', 'location-weather' ) }
				delay={ 300 }
				placement="top"
			>
				<div className="splw-patterns-template-filter-reset">
					<button>
						<span
							className={ 'splw-patterns-popup-sync' }
							onClick={ () => _fetchFile() }
						>
							<i className={ fetching ? ' sp-rotate' : '' }>
								<RotateIcon />
							</i>
						</span>
					</button>
				</div>
			</Tooltip>
			<Tooltip
				text={ __( 'Wishlist', 'location-weather' ) }
				delay={ 300 }
				placement="top"
			>
				<div className="splw-patterns-template-filter-love">
					<button
						onClick={ () => {
							changeStates(
								'wishlist',
								showWishList ? false : true
							);
						} }
						className={ ` ${ showWishList ? 'active' : '' }` }
					>
						<HeartIcon />
						{ wishListArr?.length > 0 && (
							<span className="splw-patterns-template-filter-love-count">
								{ wishListArr?.length }
							</span>
						) }
					</button>
				</div>
			</Tooltip>
		</div>
	);
};

export const HeaderWithFilter = ( props ) => {
	const {
		changeStates,
		column,
		showWishList,
		_fetchFile,
		fetching,
		onClose,
		currentBlockName,
		wishListArr,
	} = props;

	const filterBlockName = currentBlockTitle( currentBlockName );

	return (
		<div className="splw-patterns-popup-header">
			<div className="splw-patterns-popup-filter-title">
				<div className="splw-patterns-popup-filter-image-head">
					<LocationWeatherLogo />
					<span>{ filterBlockName }</span>
				</div>
				<div className="splw-patterns-popup-filter-nav-right">
					<div
						className={ `splw-patterns-template-filter-grid-two-col ` }
						onClick={ () => changeStates( 'column', '2' ) }
					>
						<button
							className={ ` ${ column == '2' ? 'active' : '' }` }
						>
							<GridTwoColIcon />
						</button>
					</div>
					<div
						className={ `splw-patterns-template-filter-grid-three-col ${
							column == '3' ? 'splw-patterns-col-active' : ''
						}` }
						onClick={ () => changeStates( 'column', '3' ) }
					>
						{ ' ' }
						<button
							className={ ` ${ column == '3' ? 'active' : '' }` }
						>
							<GridThreeColIcon />
						</button>
					</div>
					<Tooltip
						text={ __( 'Refresh Patterns', 'location-weather' ) }
						delay={ 300 }
						placement="top"
					>
						<div className="splw-patterns-template-filter-reset">
							<button>
								<span
									className={ 'splw-patterns-popup-sync' }
									onClick={ () => _fetchFile() }
								>
									<i
										className={
											fetching ? ' sp-rotate' : ''
										}
									>
										<RotateIcon />
									</i>
								</span>
							</button>
						</div>
					</Tooltip>
					<Tooltip
						text={ __( 'Wishlist', 'location-weather' ) }
						delay={ 300 }
						placement="top"
					>
						<div className="splw-patterns-template-filter-love">
							<button
								onClick={ () => {
									changeStates(
										'wishlist',
										showWishList ? false : true
									);
								} }
								className={ ` ${
									showWishList ? 'active' : ''
								}` }
							>
								<HeartIcon />
								{ wishListArr?.length > 0 && (
									<span className="splw-patterns-template-filter-love-count">
										{ wishListArr?.length }
									</span>
								) }
							</button>
						</div>
					</Tooltip>
					<div className="splw-patterns-popup-filter-sync-close">
						<button
							className="splw-patterns-btn-close"
							onClick={ onClose }
							id="splw-patterns-btn-close"
							aria-label={ __( 'Close', 'location-weather' ) }
						>
							<CloseIcon />
						</button>
					</div>
				</div>
			</div>
		</div>
	);
};
