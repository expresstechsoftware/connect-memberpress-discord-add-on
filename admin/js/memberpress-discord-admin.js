(function ($) {
	'use strict';
	$(document).ready(function () {
		if (etsMemberpressParams.is_admin) {
			if (window.location.href.indexOf("mepr_") == -1 && jQuery("#skeletabsTab1").data('identity') == 'mepr_settings') {
				jQuery("#skeletabsTab1").trigger("click");
			}
			/*Load all roles from discord server*/
			$.ajax({
				type: "POST",
				dataType: "JSON",
				url: etsMemberpressParams.admin_ajax,
				data: { 'action': 'memberpress_load_discord_roles', 'ets_memberpress_discord_nonce': etsMemberpressParams.ets_memberpress_discord_nonce, },
				beforeSend: function () {
					$(".discord-roles .spinner").addClass("is-active");
					$(".initialtab.spinner").addClass("is-active");
				},
				success: function (response) {
					if (response != null && response.hasOwnProperty('code') && response.code == 50001 && response.message == 'Missing Access') {
						$(".btn-connect-to-bot").show();
					} else if ( response.code === 10004 && response.message == 'Unknown Guild' ) {
						$(".btn-connect-to-bot").show().after('<p><b>The server ID is wrong or you did not connect the Bot.</b></p>');
					}else if( response.code === 0 && response.message == '401: Unauthorized' ) {
						$("#connect-discord-bot").show().html("Error: Unauthorized - The Bot Token is wrong").addClass('error-bk');
					} else if (response == null || response.message == '401: Unauthorized' || response.hasOwnProperty('code') || response == 0) {
						$("#connect-discord-bot").show().html("Error: Please check all details are correct").addClass('error-bk');
					} else {
						if ($('.ets-tabs button[data-identity="level-mapping"]').length) {
							$('.ets-tabs button[data-identity="level-mapping"]').show();
						}
						$("#connect-discord-bot").show().html("Bot Connected " + etsMemberpressParams.discord_icon  ).addClass('not-active');

						var activeTab = localStorage.getItem('activeTab');
						if ($('.ets-tabs button[data-identity="level-mapping"]').length == 0 && activeTab == 'level-mapping') {
							$('.ets-tabs button[data-identity="mepr_settings"]').trigger('click');
						}
						$.each(response, function (key, val) {
							var isbot = false;
							if (val.hasOwnProperty('tags')) {
								if (val.tags.hasOwnProperty('bot_id')) {
									isbot = true;
								}
							}

							if (key != 'previous_mapping' && isbot == false && val.name != '@everyone') {
								$('.discord-roles').append('<div class="makeMeDraggable" style="background-color:#' + val.color.toString(16) + '" data-role_id="' + val.id + '" >' + val.name + '</div>');
								$('#defaultRole').append('<option value="' + val.id + '" >' + val.name + '</option>');
								makeDrag($('.makeMeDraggable'));
							}
						});
						var defaultRole = $('#selected_default_role').val();
						if (defaultRole) {
							$('#defaultRole option[value=' + defaultRole + ']').prop('selected', true);
						}

						if (response.previous_mapping) {
							var mapjson = response.previous_mapping;
						} else {
							var mapjson = localStorage.getItem('MemberPressMappingjson');
						}

						$("#maaping_json_val").html(mapjson);
						$.each(JSON.parse(mapjson), function (key, val) {
							var arrayofkey = key.split('id_');
							
							// 1. Clone ONLY original elements (exclude those with data-level_id)
							var preclone = $('[data-role_id="' + val + '"]:not([data-level_id])').clone();
							
							if (preclone.length > 1) {
								preclone.slice(1).hide();
							}
							
							// 2. Check if the target level container needs the element
							var $targetLevel = $('[data-level_id="' + arrayofkey[1] + '"]');
							if ($targetLevel.find('[data-role_id="' + val + '"]').length === 0) {
								// 3. Append the clone and mark it with data-level_id
								preclone
									.attr('data-level_id', arrayofkey[1])
									.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '0px', 'order': '1' });
								
								$targetLevel
									.append(preclone)
									.attr('data-drop-role_id', val)
									.find('span')
									.css({ 'order': '2' });
							}
							
							// 4. Conditionally destroy droppable
							if ($targetLevel.find('.makeMeDraggable').length >= 1) {
								$targetLevel.droppable("destroy");
							}
							
							// 5. Initialize dragging AFTER appending
							makeDrag(preclone);
						});
					}

				},
				error: function (response) {
					$("#connect-discord-bot").show().html("Error: Please check all details are correct").addClass('error-bk');
					console.error(response);
				},
				complete: function () {
					$(".discord-roles .spinner").removeClass("is-active").css({ "float": "right" });
					$("#skeletabsTab1 .spinner").removeClass("is-active").css({ "float": "right", "display": "none" });
				}
			});


			/*Clear log log call-back*/
			$('#clrbtn').click(function (e) {
				e.preventDefault();
				$.ajax({
					url: etsMemberpressParams.admin_ajax,
					type: "POST",
					data: { 'action': 'memberpress_discord_clear_logs', 'ets_memberpress_discord_nonce': etsMemberpressParams.ets_memberpress_discord_nonce, },
					beforeSend: function () {
						$(".clr-log.spinner").addClass("is-active").show();
					},
					success: function (data) {
						if (data.error) {
							// handle the error
							alert(data.error.msg);
						} else {
							$('.error-log').html("Clear logs Sucesssfully !");
						}
					},
					error: function (response) {
						console.error(response);
					},
					complete: function () {
						$(".clr-log.spinner").removeClass("is-active").hide();
					}
				});
			});

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

			/*Flush settings from local storage*/
			$("#MemberPressRevertMapping").click(function () {
				localStorage.removeItem('MemberPressMapArray');
				localStorage.removeItem('MemberPressMappingjson');
			});

			/*Create droppable element*/
			function init() {
				$('.makeMeDroppable').droppable({
					drop: handleDropEvent,
					hoverClass: 'hoverActive',
				});
				$('.discord-roles-col').droppable({
					drop: handlePreviousDropEvent,
					hoverClass: 'hoverActive',
				});
			}

			$(init);

			/*Create draggable element*/
			function makeDrag(el) {
				// Pass me an object, and I will make it draggable
				el.draggable({
					revert: "invalid",
					helper: 'clone',
					start: function(e, ui) {
					ui.helper.css({"width":"45%"});
					}
				});
			}

			/*Handel droppable event for saved mapping*/
			function handlePreviousDropEvent(event, ui) {
				var draggable = ui.draggable;
				if(draggable.data('level_id')){
					$(ui.draggable).remove().hide();
				}
				
				$(this).append(draggable);
				$('*[data-drop-role_id="' + draggable.data('role_id') + '"]').droppable({
					drop: handleDropEvent,
					hoverClass: 'hoverActive',
				});
				$('*[data-drop-role_id="' + draggable.data('role_id') + '"]').attr('data-drop-role_id', '');

				var oldItems = JSON.parse(localStorage.getItem('MemberPressMapArray')) || [];
				$.each(oldItems, function (key, val) {
					if (val) {
						var arrayofval = val.split(',');
						if (arrayofval[0] == 'level_id_' + draggable.data('level_id') && arrayofval[1] == draggable.data('role_id')) {
							delete oldItems[key];
						}
					}
				});
				var jsonStart = "{";
				$.each(oldItems, function (key, val) {
					if (val) {
						var arrayofval = val.split(',');
						if (arrayofval[0] != 'level_id_' + draggable.data('level_id') || arrayofval[1] != draggable.data('role_id')) {
							jsonStart = jsonStart + '"' + arrayofval[0] + '":' + '"' + arrayofval[1] + '",';
						}
					}
				});
				localStorage.setItem('MemberPressMapArray', JSON.stringify(oldItems));
				var lastChar = jsonStart.slice(-1);
				if (lastChar == ',') {
					jsonStart = jsonStart.slice(0, -1);
				}

				var MemberPressMappingjson = jsonStart + '}';
				$("#maaping_json_val").html(MemberPressMappingjson);
				localStorage.setItem('MemberPressMappingjson', MemberPressMappingjson);
				draggable.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '10px' });
			}

			/*Handel droppable area for current mapping*/
			function handleDropEvent(event, ui) {
				var draggable = ui.draggable;
				var newItem = [];
				
				var newClone = $(ui.helper).clone();
				if($(this).find(".makeMeDraggable").length >= 1){
					return false;
				}
				$('*[data-drop-role_id="' + newClone.data('role_id') + '"]').droppable({
					drop: handleDropEvent,
					hoverClass: 'hoverActive',
				});
				$('*[data-drop-role_id="' + newClone.data('role_id') + '"]').attr('data-drop-role_id', '');
				if ($(this).data('drop-role_id') != newClone.data('role_id')) {
					var oldItems = JSON.parse(localStorage.getItem('MemberPressMapArray')) || [];
					$(this).attr('data-drop-role_id', newClone.data('role_id'));
					newClone.attr('data-level_id', $(this).data('level_id'));

					$.each(oldItems, function (key, val) {
						if (val) {
							var arrayofval = val.split(',');
							if (arrayofval[0] == 'level_id_' + $(this).data('level_id') ) {
								delete oldItems[key];
							}
						}
					});

					var newkey = 'level_id_' + $(this).data('level_id');
					oldItems.push(newkey + ',' + newClone.data('role_id'));
					var jsonStart = "{";
					$.each(oldItems, function (key, val) {
						if (val) {
							var arrayofval = val.split(',');
							if (arrayofval[0] == 'level_id_' + $(this).data('level_id') || arrayofval[1] != newClone.data('role_id') && arrayofval[0] != 'level_id_' + $(this).data('level_id') || arrayofval[1] == newClone.data('role_id')) {
								jsonStart = jsonStart + '"' + arrayofval[0] + '":' + '"' + arrayofval[1] + '",';
							}
						}
					});

					localStorage.setItem('MemberPressMapArray', JSON.stringify(oldItems));
					var lastChar = jsonStart.slice(-1);
					if (lastChar == ',') {
						jsonStart = jsonStart.slice(0, -1);
					}

					var MemberPressMappingjson = jsonStart + '}';
					localStorage.setItem('MemberPressMappingjson', MemberPressMappingjson);
					$("#maaping_json_val").html(MemberPressMappingjson);
				}

				// $(this).append(ui.draggable);
				// $(this).find('span').css({ 'order': '2' });
				$(this).append(newClone);
				$(this).find('span').css({ 'order': '2' });
				if (jQuery(this).find('.makeMeDraggable').length >= 1) {
					$(this).droppable("destroy");
			    }
				makeDrag($('.makeMeDraggable'));

				newClone.css({ 'width': '100%','margin-bottom': '0px', 'left': '0', 'position':'unset', 'order': '1' });
			}
		}
		$('#ets_memberpress_btn_color').wpColorPicker();
		$('#ets_memberpress_discord_btn_disconnect_color').wpColorPicker();

		$(' .ets-memberpress-discord-review-notice > button.notice-dismiss' ).on('click', function() {
			$.ajax({
				type: "POST",
				dataType: "JSON",
				url: etsMemberpressParams.admin_ajax,
				data: { 
					'action': 'ets_memberpress_discord_notice_dismiss', 
					'ets_memberpress_discord_nonce' : etsMemberpressParams.ets_memberpress_discord_nonce 
				},
				beforeSend: function () {
					console.log('sending...');
				},
				success: function (response) {
					console.log(response);
				},
				error: function (response) {
					console.error(response);
				},
				complete: function () {
					// 
				}
			});
		});

	});
	/*Tab options*/
	$.skeletabs.setDefaults({
		keyboard: false,
	});
})(jQuery);
