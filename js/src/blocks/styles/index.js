import { __ } from '@wordpress/i18n';
import { registerBlockType } from '@wordpress/blocks';
 
registerBlockType( 'pbrocks-amp-block/pbrx-02-stylesheets', {
    title: 'PBrx: Stylesheets',
 
    icon: {
        background: '#29c8aa',
        foreground: '#ffffff',
        src: 'universal-access-alt'
    },
 
    category: 'layout',
 
    example: {},
 
    edit( { className } ) {
        return <p className={ className }>Hello World, step 2 (from the editor, in green).</p>;
    },
 
    save() {
        return <p>Hello World, step 2 (from the frontend, in red).</p>;
    }
} );