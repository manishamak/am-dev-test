import { registerBlockType } from '@wordpress/blocks';
import Edit from './edit';
import metadata from './block.json';

registerBlockType( metadata.name, {
	/**
	 * Used to construct a preview for the block to be shown in the block inserter.
	 */
	example: {
		attributes: {
			preview: true,
		},
	},
	/**
	 * @see ./edit.js
	 */
	edit: Edit,

	save() {
		return null;
	},
} );
