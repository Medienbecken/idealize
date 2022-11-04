import { registerBlockType } from '@wordpress/blocks';
import { __, _e } from '@wordpress/i18n';
import edit from '../../components/fieldEdit';
import name from './block.json';

registerBlockType(name, {
    edit: edit,
    save: () => { return null }
});
