/**
 * Dismisses plugin notices.
 */
( function( $ ) {
	'use strict';
	$( document ).ready( function() {
		$( '.notice.is-dismissible.wp-easy-admin .notice-dismiss' ).on( 'click', function() {
			$.ajax( {
				url: wp_easy_admin.ajax_url,
				data: {
					action: 'dismiss_notice'
				}
			} );
		} );

		$( '#wpea-popup .wpea-modal-close' ).on( 'click', function() {
			$( '#wpea-popup' ).hide();
		} );

		$( 'body.wp-admin, body.logged-in' ).on( 'keydown' , function( e ) {
			var code = e.keyCode || e.which;

			if ( wp_easy_admin.wpea_key === String.fromCharCode(code).toLowerCase() && e.altKey ) {
				$( '#wpea-popup' ).fadeIn();
				$( '.wpea-search-form #wpea-popup-input' ).focus();
			}

			if ( 27 === code ) {
				$( '#wpea-popup' ).fadeOut();
			}
		} );

		$( '.wpea-search-form #wpea-popup-input' ).on( 'keyup' , function( e ) {
			var code = e.keyCode || e.which;

			if ( 40 === code || 9 === code ) {
				$( '#wpea-popup .wpea-search-result a:first-child' ).addClass( 'current' ).focus();
			} else if ( 38 === code ) {
				$( '#wpea-popup .wpea-search-result a:last-child' ).addClass( 'current' ).focus();
			} else {
				$( '.wpea-search-form' ).animate( { top: '10px' }, 350 );
				$( '#wpea-popup .wpea-search-result' ).html('').hide();

				var search_key = $( this ).val().toLowerCase();
				var wpea_menu = wp_easy_admin.wpea_menu.sort();
				var search_result = '';

				$.each( wpea_menu, function( index, value ) {
					var search_term = value[0];

					if ( typeof value[2] !== 'undefined' ) {
						search_term += ' : ' + value[2];
					}

					if ( search_term.toLowerCase().indexOf( search_key ) >= 0 && '' !== value[0] ) {
						search_result = '<a href="' + wp_easy_admin.admin_url + value[1] + '">' + value[0];

						if ( typeof value[2] !== "undefined" ) {
							search_result += ' : ' + value[2];
						}

						search_result += '</a>';
						$( '#wpea-popup .wpea-search-result' ).append( search_result );
						$( '#wpea-popup .wpea-search-result' ).height( $( '#wpea-popup' ).height() - 150 );
						$( '#wpea-popup .wpea-search-result' ).show();
					}
				} );

				if ( '' === search_result ) {
					search_key = search_key.split(/ +/);
					var search_result_a = [];

					$.each( wpea_menu, function( index, value ) {
						var search_term = value[0];

						if ( typeof value[2] !== 'undefined' ) {
							search_term += ' : ' + value[2];
						}

						var count = 0;

						$.each( search_key, function( sk_index, sk_value ) {
							if ( search_term.toLowerCase().indexOf( sk_value ) >= 0 && '' !== value[0] ) {
								count++;
							}
						} );

						if ( count ) {
							search_result = '<a href="' + value[1] + '">' + value[0];

							if ( typeof value[2] !== "undefined" ) {
								search_result += '  :  ' + value[2];
							}

							search_result += '</a>';
							search_result_a.push( [ count, search_result ] );
						}
					} );

					if ( search_result_a.length ) {
						search_result_a = search_result_a.sort().reverse();
						var search_result_s = [];

						$.each( search_result_a, function( index, value ) {
							search_result_s.push( value[1] );
						} );

						$( '#wpea-popup .wpea-search-result' ).append( search_result_s.join('') );
						$( '#wpea-popup .wpea-search-result' ).height( $( '#wpea-popup' ).height() - 150 );
						$( '#wpea-popup .wpea-search-result' ).show();
					}
				}
			}
		} );

		$( '#wpea-popup .wpea-search-result' ).on( 'keyup' , function( e ) {
			var code = e.keyCode || e.which;
			var current_a = $( this ).find( '.current' );

			if ( 9 === code && ! $( current_a ).length ) {
				$( '#wpea-popup .wpea-search-result a:first-child' ).addClass( 'current' );
			} else if ( ( 40 === code || 9 === code ) && $( current_a ).next().length ) {
				$( current_a ).removeClass( 'current' ).next().addClass( 'current' ).focus();
			} else if ( 38 === code && $( current_a ).prev().length ) {
				$( current_a ).removeClass( 'current' ).prev().addClass( 'current' ).focus();
			} else if ( 39 === code || 37 === code || 13 === code ) {
				$( current_a )[0].click();
			} else if ( 27 !== code ) {

				if ( 9 !== code && 40 !== code && 38 !== code ) {
					$( '.wpea-search-form #wpea-popup-input' ).val( $( '.wpea-search-form #wpea-popup-input' ).val() + String.fromCharCode( code ).toLowerCase() );
				}

				$( '.wpea-search-form #wpea-popup-input' ).focus();
				$( '.wpea-search-form #wpea-popup-input' ).trigger( 'keyup' );
			}
		} );

		$( window ).resize( function() {
			$( '#wpea-popup .wpea-search-result' ).height( $( '#wpea-popup' ).height() - 150 );
		} );
	} );
} )( jQuery );
