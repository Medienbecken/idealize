import { __, _e } from '@wordpress/i18n';
import { SelectControl } from '@wordpress/components';

const plugin = 'acf-frontend-form-element';

const FormSelectControl = (props) => {
	const { isLoading, forms } = props;

    // Show the loading state if we're still waiting.
    if (isLoading) {
        return <p>{__( 'Loading forms...', plugin )}</p>;
    }
    let choices = [];
    if (forms) {
        choices.push({ value: 0, label: __( 'Select a form', plugin ), disabled: true });
        forms.forEach(post => {
            choices.push({ value: post.id, label: post.title.rendered });
        });
    } 
    return(
		<SelectControl
            options={choices}
            label={__( 'Form', plugin )}
            labelPosition='side'
            value={props.value}
            onChange={props.onChange}
            onClick={props.onClick}
        />
	);
}


 
export default FormSelectControl;