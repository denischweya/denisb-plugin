import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, useInnerBlocksProps, RichText, InnerBlocks } from '@wordpress/block-editor';
import { useSelect } from '@wordpress/data';
import { __, sprintf } from '@wordpress/i18n';
import metadata from './block.json';

function Edit( { attributes, setAttributes, clientId } ) {
	const { title, lead } = attributes;
	const cards = useSelect( ( select ) => select( 'core/block-editor' ).getBlocks( clientId ), [ clientId ] );
	const count = ( type ) => cards.filter( ( b ) => ! type || b.attributes.type === type ).length;
	const blockProps = useBlockProps();
	const innerBlocksProps = useInnerBlocksProps(
		{ className: 'products' },
		{ allowedBlocks: [ 'denisb/product-card' ], template: [ [ 'denisb/product-card' ] ], orientation: 'horizontal', renderAppender: InnerBlocks.ButtonBlockAppender }
	);
	return (
		<div { ...blockProps }>
			<div className="hero">
				<RichText tagName="h1" className="display" value={ title } allowedFormats={ [] } onChange={ ( value ) => setAttributes( { title: value } ) } />
				<RichText tagName="p" className="lead" value={ lead } placeholder={ __( 'Intro', 'denisb' ) } onChange={ ( value ) => setAttributes( { lead: value } ) } />
				<div className="filters">
					{ /* translators: %d: number of products. */ }
					<button type="button" aria-pressed="true">{ sprintf( __( 'All (%d)', 'denisb' ), count() ) }</button>
					{ /* translators: %d: number of themes. */ }
					<button type="button" aria-pressed="false">{ sprintf( __( 'Themes (%d)', 'denisb' ), count( 'theme' ) ) }</button>
					{ /* translators: %d: number of plugins. */ }
					<button type="button" aria-pressed="false">{ sprintf( __( 'Plugins (%d)', 'denisb' ), count( 'plugin' ) ) }</button>
				</div>
				{ /* translators: %d: number of products. */ }
				<p className="count">{ sprintf( __( 'Showing %d products', 'denisb' ), count() ) }</p>
			</div>
			<ul { ...innerBlocksProps } />
		</div>
	);
}

registerBlockType( metadata.name, { edit: Edit, save: () => <InnerBlocks.Content /> } );
