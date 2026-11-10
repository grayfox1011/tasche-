/*
 * The photo of a dish (the one that appears with the mouse on its row, on computers) next to the words of
 * the dish instead of over them. The Figma puts each photo at a point of the row, right for its own short
 * names; with a longer name or description the photo covered their end, and on narrower windows the first
 * price too. For each row this script measures where the words end and where the prices start and gives
 * the row the values the CSS reads (assets/css/tasche-piene-figma.css, "0.8.1"):
 *
 *   --tp-menu-fit-left   the centre of the photo, moved right until it clears the words (never past the prices)
 *   --tp-menu-fit-scale  the size of the photo, smaller when the room between words and prices is less than it
 *   --tp-menu-fit-meta   the width of the description, on two lines when one line would leave the photo too small
 *   --tp-menu-fit-top    the height of the photo's centre, when it has to hang under the words
 *
 * The photo never covers the prices nor the words of its dish: where even the smallest photo has no room after
 * the name (long names on narrow windows) it stands against the prices and hangs under the name.
 * A row whose words and prices are already clear of the photo gets nothing: it stays as the Figma draws it.
 * Without this script the photos stay where the CSS puts them.
 */
(function () {
	'use strict';

	var DESKTOP = '(min-width: 981px)';
	var ROWS = '.tp-menu-section .tp-menu-row';
	var LEFT = '--tp-menu-fit-left';
	var SCALE = '--tp-menu-fit-scale';
	var META = '--tp-menu-fit-meta';
	var TOP = '--tp-menu-fit-top';
	var WORDS_GAP = 16; // px between the words and the photo
	var PRICE_GAP = 12; // px between the photo and the first price
	var SMALLEST = 0.62; // the photo is never smaller than this part of its size
	var COMFORTABLE = 0.85; // under this the description goes on two lines to leave the photo more room
	var UNDER_GAP = 8; // px between the words and a photo that hangs under them

	if ( ! window.matchMedia || ! document.createRange || ! document.createTreeWalker || ! window.requestAnimationFrame ) {
		return;
	}

	var desktop = window.matchMedia( DESKTOP );

	function toArray( list ) {
		return Array.prototype.slice.call( list || [] );
	}

	// The box of the words themselves (their module can be wider): left, right, bottom and number of lines.
	function words( element ) {
		if ( ! element ) {
			return null;
		}

		var range = document.createRange();
		var walker = document.createTreeWalker( element, window.NodeFilter.SHOW_TEXT, null, false );
		var left = Infinity;
		var right = -Infinity;
		var bottom = -Infinity;
		var tops = [];
		var node = walker.nextNode();
		var add = function ( rect ) {
			if ( rect.width < 0.5 || rect.height < 0.5 ) {
				return;
			}

			left = Math.min( left, rect.left );
			right = Math.max( right, rect.right );
			bottom = Math.max( bottom, rect.bottom );

			if ( ! tops.some( function ( top ) { return Math.abs( top - rect.top ) < rect.height / 2; } ) ) {
				tops.push( rect.top );
			}
		};

		while ( node ) {
			if ( node.nodeValue && node.nodeValue.replace( /\s+/g, '' ) ) {
				range.selectNodeContents( node );
				toArray( range.getClientRects() ).forEach( add );
			}

			node = walker.nextNode();
		}

		return tops.length ? { left: left, right: right, bottom: bottom, lines: tops.length } : null;
	}

	function clear( row ) {
		row.style.removeProperty( LEFT );
		row.style.removeProperty( SCALE );
		row.style.removeProperty( META );
		row.style.removeProperty( TOP );
	}

	// The description on two lines as short as they can be; back as it was if two lines are not enough.
	function twoLines( row, element ) {
		row.style.setProperty( META, '9999px' );

		var whole = words( element );
		var width = whole && 1 === whole.lines ? Math.ceil( ( whole.right - whole.left ) * 0.52 ) + 8 : 0;
		var tries = 0;
		var meta = null;

		while ( width && tries < 8 ) {
			row.style.setProperty( META, width + 'px' );
			meta = words( element );

			if ( meta && meta.lines <= 2 ) {
				return meta;
			}

			width = Math.ceil( width * 1.08 );
			tries++;
		}

		row.style.removeProperty( META );

		return words( element );
	}

	function fit( row ) {
		clear( row );

		var image = row.querySelector( '.tp-menu-row-image' );

		if ( ! desktop.matches || ! image ) {
			return;
		}

		var style = window.getComputedStyle( image );
		var box = row.getBoundingClientRect();
		var width = parseFloat( style.width );
		var height = parseFloat( style.height );
		var centre = parseFloat( style.left );

		if ( 'none' === style.display || ! box.width || ! ( width > 0 ) || ! ( height > 0 ) || isNaN( centre ) ) {
			return;
		}

		// The photo turns as it comes: the room it takes is that of the turned photo.
		var turn = parseFloat( style.getPropertyValue( '--tp-menu-row-image-rotate' ) );
		var angle = Math.abs( isNaN( turn ) ? 4.49 : turn ) * Math.PI / 180;
		var room = width * Math.cos( angle ) + height * Math.sin( angle );
		var name = words( row.querySelector( '.tp-menu-name' ) );
		var metaElement = row.querySelector( '.tp-menu-meta' );
		var meta = words( metaElement );
		var prices = toArray( row.querySelectorAll( '.tp-menu-price' ) ).map( words ).filter( Boolean );
		var limit = ( prices.length ? Math.min.apply( null, prices.map( function ( price ) { return price.left; } ) ) : box.right ) - PRICE_GAP;
		var nameEnd = name ? name.right : box.left;
		var sizeFor = function ( end ) {
			return ( limit - WORDS_GAP - end ) / room;
		};
		// Next to the name when even the smallest photo has room there; else against the prices, hanging under the words.
		var beside = sizeFor( nameEnd ) >= SMALLEST;

		centre += box.left;

		if ( meta && meta.lines <= 2 && ( ! name || 1 === name.lines ) ) {
			var tight = beside ?
				meta.right > nameEnd && sizeFor( meta.right ) < COMFORTABLE :
				meta.right > limit - room * SMALLEST - WORDS_GAP;

			if ( tight ) {
				meta = twoLines( row, metaElement );
			}
		}

		var end = beside && meta ? Math.max( nameEnd, meta.right ) : nameEnd;
		var size = Math.max( SMALLEST, Math.min( 1, sizeFor( end ) ) );
		var half = room * size / 2;
		// Clear of the words and before the prices: when the two cannot both be, the prices win.
		var place = Math.min( Math.max( centre, end + WORDS_GAP + half ), limit - half );

		// Against the prices the photo would stand over the end of the words: it hangs under them instead, under the
		// name and, if that too reaches it, under the description.
		if ( place - half < end + WORDS_GAP - 0.5 ) {
			var under = name && name.right + WORDS_GAP > place - half ? name.bottom : box.top;

			if ( meta && meta.right + WORDS_GAP > place - half ) {
				under = Math.max( under, meta.bottom );
			}

			if ( under > box.top ) {
				var tall = ( width * Math.sin( angle ) + height * Math.cos( angle ) ) * size;

				row.style.setProperty( TOP, ( under + UNDER_GAP + tall / 2 - box.top ).toFixed( 1 ) + 'px' );
			}
		}

		if ( size < 0.999 ) {
			row.style.setProperty( SCALE, size.toFixed( 4 ) );
		}

		if ( Math.abs( place - centre ) >= 0.5 ) {
			row.style.setProperty( LEFT, ( place - box.left ).toFixed( 1 ) + 'px' );
		}
	}

	function fitAll() {
		toArray( document.querySelectorAll( ROWS ) ).forEach( fit );
	}

	var frame = 0;

	function later() {
		if ( frame ) {
			return;
		}

		frame = window.requestAnimationFrame( function () {
			frame = 0;
			fitAll();
		} );
	}

	function start() {
		fitAll();

		// The row the mouse or the keyboard enters once more, right before its photo comes: fonts, words and
		// prices are then the ones on the screen.
		toArray( document.querySelectorAll( ROWS ) ).forEach( function ( row ) {
			var again = function () {
				fit( row );
			};

			row.addEventListener( 'mouseenter', again );
			row.addEventListener( 'focusin', again );
		} );

		window.addEventListener( 'resize', later );
		window.addEventListener( 'load', later );

		if ( document.fonts && document.fonts.ready && document.fonts.ready.then ) {
			document.fonts.ready.then( later );
		}
	}

	if ( 'loading' === document.readyState ) {
		document.addEventListener( 'DOMContentLoaded', start );
	} else {
		start();
	}
})();
