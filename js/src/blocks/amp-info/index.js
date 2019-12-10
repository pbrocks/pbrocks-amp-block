/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import {
	registerBlockType,
} from '@wordpress/blocks';

import {
	ServerSideRender
} from '@wordpress/components';

import icon from './icon'; 

registerBlockType( 'pbrocks-amp-block/amp-info', {
	title: __( 'AMP: Info', 'pbrocks-amp-block' ),
	icon: {
		background: '#29c8aa',
		foreground: '#ffffff',
		src: icon,
	},

	category: 'layout',

	edit: function( props ) {
		return (
			<ServerSideRender
				block="pbrocks-amp-block/amp-info"
				attributes={ props.attributes }
			/>
			);
	},
	save: function() {
		return null;
	},
} );