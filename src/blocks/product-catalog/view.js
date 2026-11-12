/* Theme and plugin filter on the products catalogue. */
document.querySelectorAll( '.wp-block-denisb-product-catalog' ).forEach( ( root ) => {
	const buttons = root.querySelectorAll( '.filters button' );
	const items = root.querySelectorAll( '.products li' );
	const count = root.querySelector( '.count' );
	const label = { all: 'products', theme: 'themes', plugin: 'plugins' };
	function filter( type, push ) {
		let n = 0;
		items.forEach( ( li ) => {
			const show = type === 'all' || li.dataset.type === type;
			li.hidden = ! show;
			if ( show ) {
				n++;
			}
		} );
		buttons.forEach( ( b ) => b.setAttribute( 'aria-pressed', b.dataset.f === type ? 'true' : 'false' ) );
		count.textContent = 'Showing ' + n + ' ' + label[ type ];
		if ( push ) {
			window.history.replaceState( null, '', type === 'all' ? window.location.pathname : '#' + type + 's' );
		}
	}
	buttons.forEach( ( b ) => b.addEventListener( 'click', () => filter( b.dataset.f, true ) ) );
	if ( window.location.hash === '#themes' ) {
		filter( 'theme' );
	} else if ( window.location.hash === '#plugins' ) {
		filter( 'plugin' );
	}
} );
