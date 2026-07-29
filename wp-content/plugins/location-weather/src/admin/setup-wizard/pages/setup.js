import { __ } from '@wordpress/i18n';
import { toastSuccessMsg } from '../../functions';
import Blocks from '../../pages/blocks';
import { SelectField } from '../../../components';
import useBlockOptions from '../../hooks/useBlockOptions';

const websiteTypes = [
	{
		label: __( 'Select a website type', 'location-weather' ),
		value: '',
	},
	{
		label: __( 'Hospitality & Tourism', 'location-weather' ),
		value: 'hospitality_and_tourism',
	},
	{
		label: __( 'Hotels & Resorts', 'location-weather' ),
		value: 'hotels_and_resorts',
	},
	{
		label: __( 'Outdoor Event Planners', 'location-weather' ),
		value: 'outdoor_event_planners',
	},
	{
		label: __( 'Agriculture & Farming', 'location-weather' ),
		value: 'agriculture_and_farming',
	},
	{
		label: __( 'News & Media', 'location-weather' ),
		value: 'news_and_media',
	},
	{
		label: __( 'Marine & Boating Services', 'location-weather' ),
		value: 'marine_and_boating_services',
	},
	{
		label: __( 'Transportation & Logistics', 'location-weather' ),
		value: 'transportation_and_logistics',
	},
	{
		label: __( 'Airlines & Airport Websites', 'location-weather' ),
		value: 'airlines_and_airport_websites',
	},
	{
		label: __( 'Sports & Clubs', 'location-weather' ),
		value: 'sports_and_clubs',
	},
	{
		label: __( 'Government & Municipal', 'location-weather' ),
		value: 'government_and_municipal',
	},
	{
		label: __( 'Environmental Organizations', 'location-weather' ),
		value: 'environmental_organizations',
	},
	{
		label: __( 'Educational Institutions', 'location-weather' ),
		value: 'educational_institutions',
	},
	{
		label: __( 'Retail & E-commerce', 'location-weather' ),
		value: 'retail_and_ecommerce',
	},
	{
		label: __( 'Personal Blogs', 'location-weather' ),
		value: 'personal_blogs',
	},
	{
		label: __( 'Coastal Businesses', 'location-weather' ),
		value: 'coastal_businesses',
	},
	{
		label: __( 'Other', 'location-weather' ),
		value: 'other',
	},
];

const BlocksSetup = ( { websiteType, setWebsiteType, errorMessage } ) => {
	const [ options, setOptions ] = useBlockOptions();

	const show_notification = ( block ) => {
		const message = block.show
			? __( 'Block disabled successfully', 'location-weather' )
			: __( 'Block enabled successfully', 'location-weather' );
		toastSuccessMsg( message );
	};

	const blockShowHideHandler = ( name ) => {
		const newData = options?.map( ( item ) => {
			if ( name === item.name ) {
				show_notification( item );
				return { ...item, show: ! item.show };
			}
			return item;
		} );
		setOptions( newData );
	};
	return (
		<div className="splw-setup-blocks-page">
			<div className="splw-setup-blocks-page-header">
				<div className="header-left">
					<h3 className="splw-setup-page-title">
						{ __(
							'Tell Us About Your Website',
							'location-weather'
						) }
					</h3>
					<p className="splw-setup-page-desc">
						{ __(
							'Choose the website or business type that best describes your site.',
							'location-weather'
						) }
					</p>
				</div>
				<div className="header-right">
					<span className="splw-setup-page-desc">
						{ __( 'Website Type', 'location-weather' ) }{ ' ' }
						<span className="required-star">*</span>
					</span>
					<SelectField
						attributes={ websiteType }
						onChange={ ( val ) => setWebsiteType( val ) }
						items={ websiteTypes }
					/>
					{ errorMessage && websiteType === '' && (
						<p
							className="splw-setup-page-desc"
							style={ {
								color: 'red',
								position: 'absolute',
								marginTop: '4px',
							} }
						>
							{ __(
								'Please select your website type to proceed.',
								'location-weather'
							) }
						</p>
					) }
				</div>
			</div>
			<Blocks
				blockSettings={ options }
				blockShowHideHandler={ blockShowHideHandler }
			/>
		</div>
	);
};

export default BlocksSetup;
