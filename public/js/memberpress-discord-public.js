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
	 $( document ).ready(function() {
			/*Call-back on disconnect from discord*/
			$('#disconnect-discord').on('click', function (e) {
				e.preventDefault();
				var userId = $(this).data('user-id');
				$.ajax({
					type: "POST",
					dataType: "JSON",
					url: etsMemberpresspublicParams.admin_ajax,
					data: { 'action': 'memberpress_disconnect_from_discord', 'user_id': userId, 'ets_memberpress_discord_public_nonce': etsMemberpresspublicParams.ets_memberpress_discord_public_nonce, },
					beforeSend: function () {
						$(".ets-spinner").addClass("ets-is-active");
					},
					success: function (response) {
						if (response.status == 1) {
							window.location = window.location.href.split("?")[0];
						}
					},
					error: function (response) {
						console.error(response);
					}
				});
			});
		});

})( jQuery );
