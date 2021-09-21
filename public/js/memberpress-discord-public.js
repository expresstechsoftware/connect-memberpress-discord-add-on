(function( $ ) {
	'use strict';
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
