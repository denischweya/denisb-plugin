import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	RichText,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
	BlockControls,
} from '@wordpress/block-editor';
import { PanelBody, TextControl, Button, ToolbarButton } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { iconId, iconUrl, letter, kind, name, desc, url, badge } = attributes;
	const blockProps = useBlockProps( { className: 'mm-item' } );
	const plain = ( name || '' ).replace( /<[^>]+>/g, '' );
	const pick = ( media ) => setAttributes( { iconId: media.id, iconUrl: media.url } );
	return (
		<>
			<BlockControls group="other">
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ pick }
						allowedTypes={ [ 'image' ] }
						value={ iconId }
						render={ ( { open } ) => (
							<ToolbarButton onClick={ open }>
								{ iconUrl ? __( 'Replace icon', 'denisb' ) : __( 'Add icon', 'denisb' ) }
							</ToolbarButton>
						) }
					/>
				</MediaUploadCheck>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Item', 'denisb' ) }>
					<TextControl __nextHasNoMarginBottom __next40pxDefaultSize label={ __( 'Link URL', 'denisb' ) } value={ url } onChange={ ( value ) => setAttributes( { url: value } ) } />
					<TextControl __nextHasNoMarginBottom __next40pxDefaultSize label={ __( 'Badge', 'denisb' ) } help={ __( 'For example "New". Leave empty for none.', 'denisb' ) } value={ badge } onChange={ ( value ) => setAttributes( { badge: value } ) } />
					<TextControl __nextHasNoMarginBottom __next40pxDefaultSize label={ __( 'Letter', 'denisb' ) } help={ __( 'Shown when there is no icon.', 'denisb' ) } value={ letter } onChange={ ( value ) => setAttributes( { letter: value } ) } />
					{ iconUrl && (
						<Button variant="secondary" isDestructive onClick={ () => setAttributes( { iconId: 0, iconUrl: '' } ) }>
							{ __( 'Remove icon', 'denisb' ) }
						</Button>
					) }
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ iconUrl ? (
					<img className="mm-ico" src={ iconUrl } alt="" width="44" height="44" />
				) : (
					<span className="mm-ico mm-letter" aria-hidden="true">{ letter || plain.charAt( 0 ) }</span>
				) }
				<span className="mm-txt">
					<RichText tagName="span" className="mm-kind" value={ kind } placeholder={ __( 'Label', 'denisb' ) } onChange={ ( value ) => setAttributes( { kind: value } ) } />
					<span className="mm-name">
						<RichText tagName="span" value={ name } placeholder={ __( 'Name', 'denisb' ) } onChange={ ( value ) => setAttributes( { name: value } ) } />
						{ badge && <span className="mm-new">{ badge }</span> }
					</span>
					<RichText tagName="span" className="mm-desc" value={ desc } placeholder={ __( 'Description', 'denisb' ) } onChange={ ( value ) => setAttributes( { desc: value } ) } />
				</span>
			</div>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
