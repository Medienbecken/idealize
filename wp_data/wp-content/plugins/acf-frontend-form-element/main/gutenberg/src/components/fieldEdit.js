import { RichText, InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { TextControl, PanelBody, PanelRow } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';
const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
  //  const { pageCount, setPageCount } = useState(1);
    const { attributes, setAttributes } = props;
    const blockProps = useBlockProps();
   
	return (
		<div { ...blockProps }>
            <InspectorControls 
            key='fea-inspector-controls'
            >
            <PanelBody
                    title={__("Basic", plugin )}
                    initialOpen={true}
                >
                    <PanelRow>
                    <TextControl
                        label={__( 'Label', plugin )}
                        value={attributes.label}
                        onChange={(newval) => setAttributes({ label: newval })}
                    /> 
                    <TextControl
                        label={__( 'Default', plugin )}
                        value={attributes.default_value}
                        onChange={(newval) => setAttributes({ default_value: newval })}
                    /> 
                    </PanelRow>
            </PanelBody>
            </InspectorControls>
            <RichText
				tagName="label"
                onChange={(newval) => setAttributes({ label: newval })}
                withoutInteractiveFormatting
                value={attributes.label}
            />
            <TextControl
                label={__( 'Default', plugin )}
                hideLabelFromVision='true'
                value={attributes.default_value}
                onChange={(newval) => setAttributes({ default_value: newval })}
            /> 
        </div>

    )    

}

export default Edit;