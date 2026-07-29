import { Button } from '@wordpress/components';
import { ResetIcon } from '../../icons';
import './editor.scss';

const ResetButton = ( { onClick } ) => {
	return (
		<Button
			className="spl-weather-header-control-reset"
			onClick={ onClick }
		>
			<ResetIcon />
		</Button>
	);
};

export default ResetButton;
