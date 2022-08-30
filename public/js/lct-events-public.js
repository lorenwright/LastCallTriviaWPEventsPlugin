(function( $ ) {
	'use strict';

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	$(document).ready(function () {
		// If we are on the events/map page, let's search by events local to where we are at physically
		var url = window.location.href
		if (url.indexOf('/play') > -1) {
			// We only want to append the coordinates search if the user is not already filtering
			if (url.includes('?')) {
				// Silence
			} else {
				// If we can get coordinates, we are going to redirect to our query string
				if (navigator.geolocation) {
					navigator.geolocation.getCurrentPosition(function (position) {
						onMapLoading('body', '.tribe-events-view-loader', '.tribe-common-a11y-hidden', function(mapIsLoading) {
							window.mapIsLoading = mapIsLoading
							console.log('mapIsLoading: ' + window.mapIsLoading)

							// If no results and we should reload map
							if (!$('.tribe-events-pro-map').length && window.reloadMap) {
								searchByLocation('')
								window.reloadMap = false
							}
						});

						searchByLocation(position.coords.latitude + ' ' + position.coords.longitude)
						
						window.reloadMap = true
					});
				}
			}
		}

		function searchByLocation (locationString) {
			$('.tribe-events-c-events-bar__search-form').each(function () {
				$(this).find('#tribe-events-events-bar-location').val(locationString)
				$(this).submit()
			})
		}
	});

	function onMapLoading(containerSelector, elementSelector, elementLoadingClass, callback) {
		var onMutationsObserved = function(mutations) {
			// If elementSelector has class then we are not loading
			callback(!$(elementSelector + elementLoadingClass).length > 0)
		};
	
		var target = $(containerSelector)[0];
		var config = { childList: true, subtree: true };
		var MutationObserver = window.MutationObserver || window.WebKitMutationObserver;
		var observer = new MutationObserver(onMutationsObserved);    
		observer.observe(target, config);
	
	}


})( jQuery );
