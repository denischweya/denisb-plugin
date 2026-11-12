import { registerBlockType } from '@wordpress/blocks';
import {
	useBlockProps,
	useInnerBlocksProps,
	RichText,
	InspectorControls,
	InnerBlocks,
	store as blockEditorStore,
} from '@wordpress/block-editor';
import { PanelBody, TextControl } from '@wordpress/components';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
import metadata from './block.json';
import './editor.css';

function Edit( { attributes, setAttributes, clientId, isSelected } ) {
	const { label, url, note, allLabel } = attributes;
	const open = useSelect(
		( select ) => isSelected || select( blockEditorStore ).hasSelectedInnerBlock( clientId, true ),
		[ clientId, isSelected ]
	);
	const blockProps = useBlockProps( { className: open ? 'mm is-editing' : 'mm' } );
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'mm-grid' },
		{
			allowedBlocks: [ 'denisb/mega-menu-item' ],
			template: [ [ 'denisb/mega-menu-item' ], [ 'denisb/mega-menu-item' ] ],
			orientation: 'horizontal',
			renderAppender: open ? InnerBlocks.ButtonBlockAppender : false,
		}
	);
	return (
		<>
			<InspectorControls>
				<PanelBody title={ __( 'Link', 'denisb' ) }>
					<TextControl
						__nextHasNoMarginBottom
						__next40pxDefaultSize
						label={ __( 'Menu link URL', 'denisb' ) }
						help={ __( 'Where the menu label and the "All" link go.', 'denisb' ) }
						value={ url }
						onChange={ ( value ) => setAttributes( { url: value } ) }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				<span className="mm-trigger">
					<RichText
						tagName="span"
						value={ label }
						allowedFormats={ [] }
						placeholder={ __( 'Menu label', 'denisb' ) }
						onChange={ ( value ) => setAttributes( { label: value } ) }
					/>
					<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.4" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true">
						<path d="m6 9 6 6 6-6"></path>
					</svg>
				</span>
				<div className="mm-panel">
					<div className="mm-inner">
						<div { ...innerBlocksProps } />
						<div className="mm-foot">
							<RichText
								tagName="span"
								value={ note }
								placeholder={ __( 'A short note', 'denisb' ) }
								onChange={ ( value ) => setAttributes( { note: value } ) }
							/>
							<RichText
								tagName="span"
								className="mm-all"
								value={ allLabel }
								allowedFormats={ [] }
								onChange={ ( value ) => setAttributes( { allLabel: value } ) }
							/>
						</div>
					</div>
				</div>
			</div>
		</>
	);
}

registerBlockType( metadata.name, {
	edit: Edit,
	save: () => <InnerBlocks.Content />,
} );
