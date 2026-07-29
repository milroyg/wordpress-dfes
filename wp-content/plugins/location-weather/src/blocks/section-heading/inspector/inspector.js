import { __ } from '@wordpress/i18n';
import { PanelBody } from '@wordpress/components';
import { AdvanceSettingIcon, TemplatePresetIcon } from '../../../icons';
import { HeadingGeneralTab, SubHeadingGeneralTab } from './generalTabs';
import {
	HeadingAdvancedOptions,
	HeadingStyleTab,
	SubHeadingStyleTab,
} from './styleTabs';
import { TabControls } from '../../../components';
import { useTogglePanelBody } from '../../../context';

const SectionHeadingBlockInspector = ( { attributes, setAttributes } ) => {
	const { activeTab, openedPanelBody, toggleActiveTab, togglePanelBody } =
		useTogglePanelBody();
	return (
		<>
			<PanelBody
				title={ __( 'Heading', 'location-weather' ) }
				icon={ <TemplatePresetIcon /> }
				opened={ openedPanelBody === 'templates' }
				onToggle={ () => togglePanelBody( 'templates' ) }
			>
				<TabControls
					GeneralTab={ HeadingGeneralTab }
					StyleTab={ HeadingStyleTab }
					attributes={ attributes }
					setAttributes={ setAttributes }
					AdvancedTab={ false }
					tabName={ activeTab }
					setTabName={ toggleActiveTab }
				/>
			</PanelBody>
			<PanelBody
				title="Sub Heading"
				icon={ <AdvanceSettingIcon /> }
				opened={ openedPanelBody === 'sub-heading' }
				onToggle={ () => togglePanelBody( 'sub-heading' ) }
			>
				<TabControls
					GeneralTab={ SubHeadingGeneralTab }
					StyleTab={ SubHeadingStyleTab }
					attributes={ attributes }
					setAttributes={ setAttributes }
					AdvancedTab={ false }
					tabName={ activeTab }
					setTabName={ toggleActiveTab }
				/>
			</PanelBody>
			<PanelBody
				title="Advanced"
				icon={ <AdvanceSettingIcon /> }
				opened={ openedPanelBody === 'advanced' }
				onToggle={ () => togglePanelBody( 'advanced' ) }
			>
				<HeadingAdvancedOptions
					attributes={ attributes }
					setAttributes={ setAttributes }
				/>
			</PanelBody>
		</>
	);
};

export default SectionHeadingBlockInspector;
