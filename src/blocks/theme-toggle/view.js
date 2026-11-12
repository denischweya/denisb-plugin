/* Light, dark and system colour themes, remembered per browser. */
( function () {
	const root = document.documentElement;
	const buttons = document.querySelectorAll( '.wp-block-denisb-theme-toggle [data-t]' );
	function apply( t ) {
		if ( t === 'system' ) {
			root.removeAttribute( 'data-theme' );
		} else {
			root.setAttribute( 'data-theme', t );
		}
		try {
			window.localStorage.setItem( 'dbTheme', t );
		} catch ( e ) {}
		buttons.forEach( ( b ) => b.setAttribute( 'aria-pressed', b.dataset.t === t ? 'true' : 'false' ) );
	}
	let saved = 'system';
	try {
		saved = window.localStorage.getItem( 'dbTheme' ) || 'system';
	} catch ( e ) {}
	apply( saved );
	buttons.forEach( ( b ) => b.addEventListener( 'click', () => apply( b.dataset.t ) ) );
} )();
