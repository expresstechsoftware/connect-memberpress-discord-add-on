(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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
			if (etsMemberpressParams.is_admin) {
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
						} else if (response == null || response.message == '401: Unauthorized' || response.hasOwnProperty('code') || response == 0) {
							$("#connect-discord-bot").show().html("Error: Please check all details are correct").addClass('error-bk');
						} else {
							if ($('.ets-tabs button[data-identity="level-mapping"]').length) {
								$('.ets-tabs button[data-identity="level-mapping"]').show();
							}
							$("#connect-discord-bot").show().html("Bot Connected <i class='fab fa-discord'></i>").addClass('not-active');

							var activeTab = localStorage.getItem('activeTab');
							if ($('.ets-tabs button[data-identity="level-mapping"]').length == 0 && activeTab == 'level-mapping') {
								$('.ets-tabs button[data-identity="settings"]').trigger('click');
							}
							$.each(response, function (key, val) {
								var isbot = false;
								if (val.hasOwnProperty('tags')) {
									if (val.tags.hasOwnProperty('bot_id')) {
										isbot = true;
									}
								}

								if (key != 'previous_mapping' && isbot == false && val.name != '@everyone') {
									$('.discord-roles').append('<div class="makeMeDraggable" style="background-color:#'+val.color.toString(16)+'" data-role_id="' + val.id + '" >' + val.name + '</div>');
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
								$('*[data-level_id="' + arrayofkey[1] + '"]').append($('*[data-role_id="' + val + '"]')).attr('data-drop-role_id', val).find('span').css({ 'order': '2' });
								if (jQuery('*[data-level_id="' + arrayofkey[1] + '"]').find('.makeMeDraggable').length >= 1) {
									$('*[data-level_id="' + arrayofkey[1] + '"]').droppable("destroy");
								}
								$('*[data-role_id="' + val + '"]').css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '0px', 'order': '1' }).attr('data-level_id', arrayofkey[1]);
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

				/*Flush settings from local storage*/
				$("#MemberPressRevertMapping").click( function () {
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
						revert: "invalid"
					});
				}

				/*Handel droppable event for saved mapping*/
				function handlePreviousDropEvent(event, ui) {
					var draggable = ui.draggable;
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
							if (arrayofval[0] == 'level_id_' + draggable.data('level_id') || arrayofval[1] == draggable.data('role_id')) {
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
					$('*[data-drop-role_id="' + draggable.data('role_id') + '"]').droppable({
						drop: handleDropEvent,
						hoverClass: 'hoverActive',
					});
					$('*[data-drop-role_id="' + draggable.data('role_id') + '"]').attr('data-drop-role_id', '');
					if ($(this).data('drop-role_id') != draggable.data('role_id')) {
						var oldItems = JSON.parse(localStorage.getItem('MemberPressMapArray')) || [];
						$(this).attr('data-drop-role_id', draggable.data('role_id'));
						draggable.attr('data-level_id', $(this).data('level_id'));

						$.each(oldItems, function (key, val) {
							if (val) {
								var arrayofval = val.split(',');
								if (arrayofval[0] == 'level_id_' + $(this).data('level_id') || arrayofval[1] == draggable.data('role_id')) {
									delete oldItems[key];
								}
							}
						});

						var newkey = 'level_id_' + $(this).data('level_id');
						oldItems.push(newkey + ',' + draggable.data('role_id'));
						var jsonStart = "{";
						$.each(oldItems, function (key, val) {
							if (val) {
								var arrayofval = val.split(',');
								if (arrayofval[0] == 'level_id_' + $(this).data('level_id') || arrayofval[1] != draggable.data('role_id') && arrayofval[0] != 'level_id_' + $(this).data('level_id') || arrayofval[1] == draggable.data('role_id')) {
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

					$(this).append(ui.draggable);
					$(this).find('span').css({ 'order': '2' });
					if (jQuery(this).find('.makeMeDraggable').length >= 1) {
						$(this).droppable("destroy");
					}

					draggable.css({ 'width': '100%', 'left': '0', 'top': '0', 'margin-bottom': '0px', 'order': '1' });
				}
			}
		});
		/*Tab options*/
		$.skeletabs.setDefaults({
			keyboard: false,
		});
})( jQuery );
