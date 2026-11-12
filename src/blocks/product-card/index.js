import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck, BlockControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, ToolbarButton } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes } ) {
	const { type, kind, name, desc, url, linkLabel, thumbStyle, imageId, imageUrl, letter } = attributes;
	const blockProps = useBlockProps( { 'data-type': type } );
	const plain = ( name || '' ).replace( /<[^>]+>/g, '' );
	let thumb;
	if ( thumbStyle === 'letter' || ! imageUrl ) {
		thumb = <span className="letter" aria-hidden="true">{ letter || plain.charAt( 0 ) }</span>;
	} else {
		thumb = <img className={ thumbStyle === 'icon' ? 'icon' : 'shot' } src={ imageUrl } alt="" />;
	}
	return (
		<>
			<BlockControls group="other">
				<MediaUploadCheck>
					<MediaUpload
						onSelect={ ( media ) => setAttributes( { imageId: media.id, imageUrl: media.url } ) }
						allowedTypes={ [ 'image' ] }
						value={ imageId }
						render={ ( { open } ) => <ToolbarButton onClick={ open }>{ __( 'Image', 'denisb' ) }</ToolbarButton> }
					/>
				</MediaUploadCheck>
			</BlockControls>
			<InspectorControls>
				<PanelBody title={ __( 'Card', 'denisb' ) }>
					<SelectControl __nextHasNoMarginBottom __next40pxDefaultSize label={ __( 'Type', 'denisb' ) } value={ type } options={ [ { label: __( 'Plugin', 'denisb' ), value: 'plugin' }, { label: __( 'Theme', 'denisb' ), value: 'theme' } ] } onChange={ ( value ) => setAttributes( { type: value } ) } />
					<TextControl __nextHasNoMarginBottom __next40pxDefaultSize label={ __( 'Link URL', 'denisb' ) } value={ url } onChange={ ( value ) => setAttributes( { url: value } ) } />
					<SelectControl __nextHasNoMarginBottom __next40pxDefaultSize label={ __( 'Thumbnail', 'denisb' ) } value={ thumbStyle } options={ [ { label: __( 'Screenshot', 'denisb' ), value: 'shot' }, { label: __( 'Icon', 'denisb' ), value: 'icon' }, { label: __( 'Letter', 'denisb' ), value: 'letter' } ] } onChange={ ( value ) => setAttributes( { thumbStyle: value } ) } />
					<TextControl __nextHasNoMarginBottom __next40pxDefaultSize label={ __( 'Image URL', 'denisb' ) } help={ __( 'Pick an image from the toolbar, or paste an external URL such as a WordPress.org screenshot.', 'denisb' ) } value={ imageUrl } onChange={ ( value ) => setAttributes( { imageUrl: value, imageId: 0 } ) } />
					<TextControl __nextHasNoMarginBottom __next40pxDefaultSize label={ __( 'Letter', 'denisb' ) } value={ letter } onChange={ ( value ) => setAttributes( { letter: value } ) } />
				</PanelBody>
			</InspectorControls>
			<li { ...blockProps }>
				<div className="product">
					<div className="thumb">{ thumb }</div>
					<div className="pbody">
						<RichText tagName="span" className="mono" value={ kind } placeholder={ __( 'Plugin · Category', 'denisb' ) } onChange={ ( value ) => setAttributes( { kind: value } ) } />
						<RichText tagName="h3" value={ name } placeholder={ __( 'Name', 'denisb' ) } onChange={ ( value ) => setAttributes( { name: value } ) } />
						<RichText tagName="p" value={ desc } placeholder={ __( 'Description', 'denisb' ) } onChange={ ( value ) => setAttributes( { desc: value } ) } />
						<RichText tagName="span" className="go" value={ linkLabel } placeholder={ __( 'Details and downloads →', 'denisb' ) } onChange={ ( value ) => setAttributes( { linkLabel: value } ) } />
					</div>
				</div>
			</li>
		</>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => null } );
