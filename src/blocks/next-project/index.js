import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText } from '@wordpress/block-editor';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

const mono = { fontFamily: "'Geist Mono',monospace", fontSize: 14, letterSpacing: '.06em', textTransform: 'uppercase', color: 'var(--muted)' };

function Edit( { attributes, setAttributes } ) {
	const blockProps = useBlockProps( { style: { padding: 'clamp(72px,9vw,128px) 0 0' } } );
	return (
		<section { ...blockProps }>
			<div style={ { display: 'flex', flexDirection: 'column', gap: 14, padding: 'clamp(28px,5vw,56px) 0', borderTop: '1px solid var(--line)', borderBottom: '1px solid var(--line)' } }>
				<RichText tagName="span" style={ mono } value={ attributes.label } allowedFormats={ [] } onChange={ ( value ) => setAttributes( { label: value } ) } />
				<span style={ { fontFamily: "'Bebas Neue',sans-serif", fontSize: 'clamp(56px,9vw,136px)', lineHeight: 0.88, opacity: 0.5 } }>
					{ __( 'Next case study title', 'denisb' ) }
				</span>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
