( function() {
	'use strict';

	var STYLE_ID   = 'mutemenu-hide';
	var BUTTON_ID  = 'wp-admin-bar-mutemenu-toggle';

	/**
	 * Add or remove the inline style that hides notifications.
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
			icon.style.opacity = '0.5';
			setTimeout( function() {
				icon.className = 'ab-icon dashicons ' + ( muted ? 'dashicons-hidden' : 'dashicons-bell' );
				icon.style.opacity = '1';
			}, 100 );
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
		var data = new FormData();
		data.append( 'action', 'mutemenu_toggle' );
		data.append( 'nonce', MuteMenu.nonce );

		fetch( MuteMenu.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
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
					var muted = result.data.muted;
					setMuteStyle( muted );
					updateButton( muted );
				}
			} )
			.catch( function( error ) {
				console.error( 'Mute Menu Notifications:', error );
				var link = document.querySelector( '#' + BUTTON_ID + ' a' );
				if ( link ) {
					link.style.color = '#dc3232';
					setTimeout( function() {
						link.style.color = '';
					}, 1500 );
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
			link.addEventListener( 'click', function( event ) {
				event.preventDefault();
				toggleMute();
			} );
		}
	} );
} )();
