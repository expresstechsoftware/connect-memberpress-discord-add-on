jQuery(document).ready(function($) {
	if (etsMemberpressParams.is_admin) {    
	  $("#ets-cspf-table-search-submit").on('click', function(e) {
		  e.preventDefault();
		  ets_mepr_process_table_search();
		});
	  
	  function ets_mepr_process_table_search() {
		  var loc = window.location.href;
	  
		  loc = loc.replace(/[&\?]search-discord=[^&]*/gi, '');
		  loc = loc.replace(/[&\?]search-field-discord=[^&]*/gi, '');
		  loc = loc.replace(/[&\?]paged=[^&]*/gi, ''); // Show first page when search button is clicked
	  
		  var search_discord = encodeURIComponent($('#ets-cspf-table-search').val());
		  var search_field_discord = $('#ets-cspf-table-search-field').val();
	  
		  loc = loc + '&search-discord=' + search_discord + '&search-field-discord=' + search_field_discord;
	  
		  // Clean up
		  if(!/\?/.test(loc) && /&/.test(loc)) {
			loc = loc.replace(/&/,'?'); // not global, just the first
		  }
	  
		  window.location = loc;
		}
  
			  /*Call-back to manage member connection with discord from memberpress members-list*/
			  $('.ets-memberpress-run-api').on('click', function (e) {
				  e.preventDefault();
				  var userId = $(this).data('uid');
				  $.ajax({
					  type: "POST",
					  dataType: "JSON",
					  url: etsMemberpressParams.admin_ajax,
					  data: { 'action': 'memberpress_discord_member_table_run_api', 'user_id': userId, 'ets_memberpress_discord_nonce': etsMemberpressParams.ets_memberpress_discord_nonce, },
					  beforeSend: function () {
						  $("." + userId + ".spinner").addClass("is-active").show();
					  },
					  success: function (response) {
						  if (response.status == 1) {
							  $("." + userId + ".ets-save-success").show();;
						  }
					  },
					  error: function (response) {
						  console.error(response);
					  },
					  complete: function () {
						  $("." + userId + ".spinner").removeClass("is-active").hide();
					  }
				  });
			  }); 
		
  }
  });
  