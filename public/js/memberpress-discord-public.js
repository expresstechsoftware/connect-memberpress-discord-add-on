!(function (e) {
  "use strict";
  e(document).ready(function () {
      e(".btn-disconnect").on("click", function (s) {
          s.preventDefault();
          var r = e(this).data("user-id");
          e.ajax({
              type: "POST",
              dataType: "JSON",
              url: etsMemberpresspublicParams.admin_ajax,
              data: { action: "memberpress_disconnect_from_discord", user_id: r, ets_memberpress_discord_public_nonce: etsMemberpresspublicParams.ets_memberpress_discord_public_nonce },
              beforeSend: function () {
                  e(".ets-spinner").addClass("ets-is-active");
              },
              success: function (e) {
                  1 == e.status && (window.location = window.location.href.split("?")[0]);
              },
              error: function (e) {
                  console.error(e);
              },
          });
      });
  });
})(jQuery);
