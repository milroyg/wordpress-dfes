import { dispatch } from '@wordpress/data';
import { Button } from '@wordpress/components';
import { useDeviceType } from '../../controls';
import { DesktopIcon, MobileIcon, TabletIcon } from './responsiveIcons';

const Responsive = () => {
	const Device = ( e ) => {
		const canvas = document.getElementsByClassName(
			'edit-site-visual-editor__editor-canvas'
		);
		if ( canvas.length > 0 ) {
			dispatch( 'core/edit-site' ).__experimentalSetPreviewDeviceType(
				e.target.closest( 'button' ).value
			);
		} else {
			dispatch( 'core/edit-post' ).__experimentalSetPreviewDeviceType(
				e.target.closest( 'button' ).value
			);
		}
	};

	const deviceType = useDeviceType();

	const DeviceIcon = () => {
		if ( 'Desktop' === deviceType ) {
			return <DesktopIcon />;
		}
		if ( 'Tablet' === deviceType ) {
			return <TabletIcon />;
		}
		if ( 'Mobile' === deviceType ) {
			return <MobileIcon />;
		}
	};

	return (
		<div className="spl-weather-responsive">
			<div className="spl-weather-units">
				<span>
					<DeviceIcon />
				</span>
				<div className="spl-weather-units-btn">
					<Button
						className={ deviceType === 'Desktop' ? 'active' : '' }
						value={ 'Desktop' }
						onClick={ Device }
					>
						<DesktopIcon />
					</Button>
					<Button
						className={ deviceType === 'Tablet' ? 'active' : '' }
						value={ 'Tablet' }
						onClick={ Device }
					>
						<TabletIcon />
					</Button>
					<Button
						className={ deviceType === 'Mobile' ? 'active' : '' }
						value={ 'Mobile' }
						onClick={ Device }
					>
						<MobileIcon />
					</Button>
				</div>
			</div>
		</div>
	);
};

export default Responsive;
