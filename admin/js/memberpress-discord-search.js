jQuery(document).ready(function($) {
    
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
});
