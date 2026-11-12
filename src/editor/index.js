/**
 * Editor tools: the "Rotating words" text format and the SEO panel.
 */
import { registerFormatType, applyFormat, removeFormat, getActiveFormat, useAnchor } from '@wordpress/rich-text';
import { RichTextToolbarButton } from '@wordpress/block-editor';
import { Popover, TextControl, TextareaControl, Button } from '@wordpress/components';
import { registerPlugin } from '@wordpress/plugins';
import { PluginDocumentSettingPanel, store as editorStore } from '@wordpress/editor';
import { useSelect } from '@wordpress/data';
import { useEntityProp } from '@wordpress/core-data';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

const FORMAT = 'denisb/rotate';

function RotateEdit( { isActive, value, onChange, contentRef } ) {
	const [ open, setOpen ] = useState( false );
	const active = getActiveFormat( value, FORMAT );
	const [ words, setWords ] = useState( '' );
	const anchor = useAnchor( { editableContentElement: contentRef.current, settings: { tagName: 'span', className: 'rot' } } );
	const start = () => {
		const first = value.text.slice( value.start, value.end ).trim();
		setWords( active?.attributes?.words || first );
		setOpen( true );
	};
	const save = () => {
		const list = words.split( ',' ).map( ( w ) => w.trim() ).filter( Boolean ).join( ', ' );
		onChange( applyFormat( value, { type: FORMAT, attributes: { words: list } } ) );
		setOpen( false );
	};
	return (
		<>
			<RichTextToolbarButton icon="update" title={ __( 'Rotating words', 'denisb' ) } onClick={ start } isActive={ isActive } />
			{ open && (
				<Popover anchor={ anchor } onClose={ () => setOpen( false ) } placement="bottom" className="denisb-rotate-popover">
					<div style={ { padding: 16, width: 280 } }>
						<TextControl
							__nextHasNoMarginBottom
							__next40pxDefaultSize
							label={ __( 'Words, separated by commas', 'denisb' ) }
							help={ __( 'The selected text shows first, then the words take turns.', 'denisb' ) }
							value={ words }
							onChange={ setWords }
						/>
						<div style={ { display: 'flex', gap: 8, marginTop: 12 } }>
							<Button variant="primary" onClick={ save }>{ __( 'Apply', 'denisb' ) }</Button>
							{ isActive && (
								<Button variant="tertiary" isDestructive onClick={ () => { onChange( removeFormat( value, FORMAT ) ); setOpen( false ); } }>
									{ __( 'Remove', 'denisb' ) }
								</Button>
							) }
						</div>
					</div>
				</Popover>
			) }
		</>
	);
}

registerFormatType( FORMAT, {
	title: __( 'Rotating words', 'denisb' ),
	tagName: 'span',
	className: 'rot',
	attributes: { words: 'data-words' },
	edit: RotateEdit,
} );

function SeoPanel() {
	const { postType, supported } = useSelect( ( select ) => {
		const type = select( editorStore ).getCurrentPostType();
		return { postType: type, supported: [ 'page', 'post', 'sfcore_project' ].includes( type ) };
	}, [] );
	const [ meta, setMeta ] = useEntityProp( 'postType', postType, 'meta' );
	if ( ! supported || ! meta ) {
		return null;
	}
	const set = ( key ) => ( value ) => setMeta( { ...meta, [ key ]: value } );
	return (
		<PluginDocumentSettingPanel name="denisb-seo" title={ __( 'SEO', 'denisb' ) }>
			<TextControl __nextHasNoMarginBottom __next40pxDefaultSize label={ __( 'Title', 'denisb' ) } help={ __( 'Leave empty to use the page title.', 'denisb' ) } value={ meta._denisb_seo_title || '' } onChange={ set( '_denisb_seo_title' ) } />
			<TextareaControl __nextHasNoMarginBottom label={ __( 'Description', 'denisb' ) } help={ __( 'Leave empty to use the excerpt.', 'denisb' ) } value={ meta._denisb_seo_desc || '' } onChange={ set( '_denisb_seo_desc' ) } />
			<TextareaControl __nextHasNoMarginBottom label={ __( 'JSON-LD', 'denisb' ) } help={ __( 'Optional structured data. Saved only when it is valid JSON.', 'denisb' ) } value={ meta._denisb_schema || '' } onChange={ set( '_denisb_schema' ) } rows={ 6 } />
		</PluginDocumentSettingPanel>
	);
}

registerPlugin( 'denisb-seo', { render: SeoPanel } );
