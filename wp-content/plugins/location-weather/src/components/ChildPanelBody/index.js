import { PanelBody } from '@wordpress/components';
import './editor.scss';

const ChildPanelBody = ( { title, children, initialOpen = false } ) => {
	return (
		<PanelBody
			className="splw-child-panel-body"
			title={ title }
			initialOpen={ initialOpen }
		>
			{ children }
		</PanelBody>
	);
};

export default ChildPanelBody;
