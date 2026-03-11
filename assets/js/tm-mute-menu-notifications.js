( function() {
	'use strict';

	var STYLE_ID   = 'tm-mmn-hide';
	var BUTTON_ID  = 'wp-admin-bar-tm-mmn-toggle';

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

		if ( icon ) {
			icon.className = 'ab-icon dashicons ' + ( muted ? 'dashicons-hidden' : 'dashicons-bell' );
		}

		if ( label ) {
			label.textContent = muted ? TmMuteMenuNotifications.labelUnmute : TmMuteMenuNotifications.labelMute;
		}
	}

	/**
	 * Send a POST request to toggle the mute state.
	 */
	function toggleMute() {
		var data = new FormData();
		data.append( 'action', 'tm_mmn_toggle' );
		data.append( 'nonce', TmMuteMenuNotifications.nonce );

		fetch( TmMuteMenuNotifications.ajaxUrl, {
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
