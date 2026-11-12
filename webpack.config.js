/**
 * Builds the blocks (found through their block.json files) plus the editor tools.
 */
const path = require( 'path' );
const config = require( '@wordpress/scripts/config/webpack.config' );

const extend = ( base ) => ( {
	...base,
	entry: async () => ( {
		...( typeof base.entry === 'function' ? await base.entry() : base.entry ),
		'editor/index': path.resolve( __dirname, 'src/editor/index.js' ),
	} ),
} );

module.exports = Array.isArray( config ) ? [ extend( config[ 0 ] ), ...config.slice( 1 ) ] : extend( config );
