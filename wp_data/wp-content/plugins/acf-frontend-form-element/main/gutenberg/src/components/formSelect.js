import { InspectorControls, useBlockProps } from '@wordpress/block-editor';
import { PanelBody, PanelRow } from '@wordpress/components';
import { __, _e } from '@wordpress/i18n';
import { useSelect } from '@wordpress/data';
import FormSelect from './formSelectControl';
import FormPreview from './formPreview';


const plugin = 'acf-frontend-form-element';

const Edit = (props) => {
  //  const { pageCount, setPageCount } = useState(1);
    const { attributes, setAttributes } = props;
    const blockProps = useBlockProps();

    const forms = useSelect( ( select ) => {		
        return select( 'core' ).getEntityRecords( 
            'postType',
            'admin_form', 
            {
                per_page: -1,
                status: 'any',
            } 
        )
    } );
    
    const isLoading = useSelect((select) => {
        return select('core/data').isResolving('core', 'getEntityRecords', [
            'postType',
            'admin_form', 
            {
                per_page: -1,
                status: 'any',
            } 
        ]);
    });

	return (
		<div { ...blockProps }>
            <InspectorControls 
            key='fea-inspector-controls'
            >
            <PanelBody
                    title={__("Form Settings", plugin )}
                    initialOpen={true}
                >
                    <PanelRow>
                    <FormSelect
                        isLoading={isLoading}
                        forms={forms}
                        value={attributes.formID}
                        onChange={(newval) => setAttributes({ formID: parseInt(newval) })}
                    /> 
                    </PanelRow>
            </PanelBody>
            </InspectorControls>
            <FormSelect
                isLoading={isLoading}
                forms={forms}
                value={attributes.formID}
                onChange={(newval) => setAttributes({ formID: parseInt(newval) })}
            />
            <FormPreview
                form={attributes.formID}
                block={props.name}
                />
        </div>

    )    
}

export default Edit;