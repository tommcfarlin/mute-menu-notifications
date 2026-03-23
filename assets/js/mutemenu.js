( function() {
	'use strict';

	var STYLE_ID   = 'mutemenu-hide';
	var BUTTON_ID  = 'wp-admin-bar-mutemenu-toggle';
	var toggling   = false;

	/**
	 * Announce a message to screen readers via a live region.
	 *
	 * @param {string} message The message to announce.
	 */
	function announce( message ) {
		var region = document.getElementById( 'mutemenu-live' );
		if ( ! region ) {
			region = document.createElement( 'div' );
			region.id = 'mutemenu-live';
			region.setAttribute( 'role', 'status' );
			region.setAttribute( 'aria-live', 'polite' );
			region.className = 'screen-reader-text';
			document.body.appendChild( region );
		}
		region.textContent = message;
	}

	/**
	 * Add or remove the inline style that hides notifications.
	 *
	 * Note: these selectors are duplicated in src/NotificationMuter.php inject_inline_css().
	 *
	 * @param {boolean} muted Whether notifications should be hidden.
	 */
	function setMuteStyle( muted ) {
		var existing = document.getElementById( STYLE_ID );

		if ( muted && ! existing ) {
			var style = document.createElement( 'style' );
			style.id = STYLE_ID;
			style.textContent =
				'.update-plugins { display: none !important; }' +
				'#wp-admin-bar-updates { display: none !important; }' +
				'.plugin-update-tr { display: none !important; }';
			document.head.appendChild( style );
		} else if ( ! muted && existing ) {
			existing.parentNode.removeChild( existing );
		}
	}

	/**
	 * Update the admin bar button label and icon.
	 *
	 * @param {boolean} muted Whether notifications are currently muted.
	 */
	function updateButton( muted ) {
		var node = document.getElementById( BUTTON_ID );
		if ( ! node ) {
			return;
		}

		var icon  = node.querySelector( '.ab-icon' );
		var label = node.querySelector( '.ab-label' );
		var link  = node.querySelector( 'a' );

		if ( icon ) {
			icon.style.opacity = '0';
			var settled = false;
			var onEnd = function() {
				if ( settled ) {
					return;
				}
				settled = true;
				icon.removeEventListener( 'transitionend', onEnd );
				icon.className = 'ab-icon dashicons ' + ( muted ? 'dashicons-hidden' : 'dashicons-bell' );
				icon.style.opacity = '1';
			};
			icon.addEventListener( 'transitionend', onEnd );
			setTimeout( onEnd, 200 );
		}

		if ( label ) {
			label.textContent = muted ? MuteMenu.labelUnmute : MuteMenu.labelMute;
		}

		if ( link ) {
			link.setAttribute( 'aria-pressed', muted ? 'true' : 'false' );
			link.setAttribute( 'aria-label', muted ? MuteMenu.labelUnmute : MuteMenu.labelMute );
		}
	}

	/**
	 * Send a POST request to toggle the mute state.
	 */
	function toggleMute() {
		if ( toggling ) {
			return;
		}
		toggling = true;

		var link = document.querySelector( '#' + BUTTON_ID + ' a' );
		if ( link ) {
			link.classList.remove( 'mutemenu-error' );
			link.setAttribute( 'aria-disabled', 'true' );
			link.style.opacity = '0.5';
		}

		var data = new FormData();
		data.append( 'action', 'mutemenu_toggle' );
		data.append( 'nonce', MuteMenu.nonce );

		fetch( MuteMenu.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'X-Requested-With': 'XMLHttpRequest' },
			body: data
		} )
			.then( function( response ) {
				if ( ! response.ok ) {
					throw new Error( 'Request failed: ' + response.status );
				}
				return response.json();
			} )
			.then( function( result ) {
				if ( result.success ) {
					var muted = !! ( result.data && result.data.muted );
					setMuteStyle( muted );
					updateButton( muted );
					announce( muted ? MuteMenu.confirmMuted : MuteMenu.confirmUnmuted );
				}
			} )
			.catch( function() {
				console.error( 'Mute Menu Notifications: toggle request failed.' );
				if ( link ) {
					link.classList.add( 'mutemenu-error' );
				}
				announce( MuteMenu.errorMessage );
			} )
			.finally( function() {
				toggling = false;
				if ( link ) {
					link.removeAttribute( 'aria-disabled' );
					link.style.opacity = '';
				}
			} );
	}

	document.addEventListener( 'DOMContentLoaded', function() {
		var button = document.getElementById( BUTTON_ID );
		if ( ! button ) {
			return;
		}

		var link = button.querySelector( 'a' );
		if ( link ) {
			link.setAttribute( 'role', 'button' );
			link.setAttribute( 'tabindex', '0' );
			link.setAttribute( 'aria-pressed', MuteMenu.isMuted ? 'true' : 'false' );
			link.setAttribute( 'aria-label', MuteMenu.isMuted ? MuteMenu.labelUnmute : MuteMenu.labelMute );

			link.addEventListener( 'click', function( event ) {
				event.preventDefault();
				toggleMute();
			} );

			link.addEventListener( 'keydown', function( event ) {
				if ( ' ' === event.key || 'Spacebar' === event.key ) {
					event.preventDefault();
					toggleMute();
				}
			} );
		}
	} );
} )();
