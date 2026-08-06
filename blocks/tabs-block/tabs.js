/**
 * Tabs block behaviour.
 *
 * Replaces the previous inline onclick handlers with one delegated listener,
 * and implements the ARIA tabs keyboard pattern: arrows move between tabs,
 * Home/End jump to the ends, and a roving tabindex keeps exactly one tab in
 * the page tab order.
 */
( function () {
	'use strict';

	var BLOCK = '.acf-tabs-block';

	function tabsIn( block ) {
		return Array.prototype.slice.call(
			block.querySelectorAll( '.acf-tab-button' )
		).filter( function ( tab ) {
			return tab.closest( BLOCK ) === block;
		} );
	}

	function panelsIn( block ) {
		return Array.prototype.slice.call(
			block.querySelectorAll( '.acf-tab-panel' )
		).filter( function ( panel ) {
			return panel.closest( BLOCK ) === block;
		} );
	}

	function activate( block, index, focusTab ) {
		var tabs = tabsIn( block );
		var panels = panelsIn( block );

		if ( ! tabs.length ) {
			return;
		}

		index = Math.max( 0, Math.min( index, tabs.length - 1 ) );

		tabs.forEach( function ( tab, i ) {
			var selected = i === index;
			tab.classList.toggle( 'active', selected );
			tab.setAttribute( 'aria-selected', selected ? 'true' : 'false' );
			// Roving tabindex: only the selected tab is reachable via Tab.
			tab.setAttribute( 'tabindex', selected ? '0' : '-1' );
		} );

		panels.forEach( function ( panel, i ) {
			var selected = i === index;
			panel.classList.toggle( 'active', selected );
			if ( selected ) {
				panel.removeAttribute( 'hidden' );
			} else {
				panel.setAttribute( 'hidden', '' );
			}
		} );

		if ( focusTab && tabs[ index ] ) {
			tabs[ index ].focus();
		}
	}

	document.addEventListener( 'click', function ( event ) {
		var tab = event.target.closest ? event.target.closest( '.acf-tab-button' ) : null;
		if ( ! tab ) {
			return;
		}

		var block = tab.closest( BLOCK );
		if ( ! block ) {
			return;
		}

		event.preventDefault();
		activate( block, tabsIn( block ).indexOf( tab ), false );
	} );

	document.addEventListener( 'keydown', function ( event ) {
		var tab = event.target.closest ? event.target.closest( '.acf-tab-button' ) : null;
		if ( ! tab ) {
			return;
		}

		var block = tab.closest( BLOCK );
		if ( ! block ) {
			return;
		}

		var tabs = tabsIn( block );
		var current = tabs.indexOf( tab );
		var next = null;

		switch ( event.key ) {
			case 'ArrowRight':
			case 'ArrowDown':
				next = ( current + 1 ) % tabs.length;
				break;
			case 'ArrowLeft':
			case 'ArrowUp':
				next = ( current - 1 + tabs.length ) % tabs.length;
				break;
			case 'Home':
				next = 0;
				break;
			case 'End':
				next = tabs.length - 1;
				break;
			default:
				return;
		}

		event.preventDefault();
		activate( block, next, true );
	} );
}() );
