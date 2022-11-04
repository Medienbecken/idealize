import { __, _e } from '@wordpress/i18n';
import { ServerSideRender } from '@wordpress/editor';
import { Disabled } from '@wordpress/components';


const plugin = 'acf-frontend-form-element';

const FormPreview = (props) => {
    if (props.form) {
        return(
            <Disabled 
                key='fea-disabled-render'
                >
                <ServerSideRender
                    block={props.block}
                    attributes={{ 
                        formID: props.form,
                        editMode: 1
                    }}
                />
            </Disabled>      
        )
    } else {
        return null;
    }

}


 
export default FormPreview;