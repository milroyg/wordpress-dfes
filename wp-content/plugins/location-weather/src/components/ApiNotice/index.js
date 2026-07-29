import { __ } from '@wordpress/i18n';
import { memo } from '@wordpress/element';

const ApiNotice = memo( () => {
	return (
		<div className="spl-weather-api-notice">
			<p>
				This weather uses the default API with a 20-call/view limit.
				{' '}
				<a
					href={`${splWeatherBlockLocalize?.adminUrl}edit.php?post_type=location_weather&page=splw_admin_dashboard#lw_settings`}
					target="_blank"
					rel="noopener noreferrer"
				>
					{ __( 'Add your own API key', 'location-weather' ) }
				</a>
				{' '}
				for more calls and uninterrupted weather updates.
			</p>
		</div>
	);
} );

export default ApiNotice;