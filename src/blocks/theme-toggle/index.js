import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import { ICONS } from './icons';
import metadata from './block.json';

function Edit() {
	const blockProps = useBlockProps( {
		className: 'tt',
		role: 'group',
		'aria-label': __( 'Colour theme', 'denisb' ),
	} );
	return (
		<div { ...blockProps }>
			{ Object.keys( ICONS ).map( ( key ) => (
				<button key={ key } type="button" data-t={ key } aria-pressed={ key === 'system' } tabIndex={ -1 }>
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
						{ ICONS[ key ] }
					</svg>
				</button>
			) ) }
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
