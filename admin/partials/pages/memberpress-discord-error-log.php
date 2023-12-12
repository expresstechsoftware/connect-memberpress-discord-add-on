<!-- Search fileds	 -->
<form id="ets-log-search-form" method="GET">
		<label for="api-response-code"><?php esc_html_e( 'API Response Code:', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="api-response-code" id="api-response-code">

		<label for="error-message"><?php esc_html_e( 'Error Message:', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="error-message" id="error-message">

		<label for="wp-user-id"><?php esc_html_e( 'WordPress User ID:', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="wp-user-id" id="wp-user-id">

		<label for="discord-user-id"><?php esc_html_e( 'Discord User ID:', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="discord-user-id" id="discord-user-id">

		<label for="datetime"><?php esc_html_e( 'Datetime (YYYY-MM-DD HH:MM:SS):', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="datetime" id="datetime">

		<input type="submit" class="ets-submit ets-bg-blue" value="<?php esc_attr_e( 'Search', 'connect-memberpress-discord-add-on' ); ?>">
	</form>
<div class="error-log">
<?php
	$uuid     = get_option( 'ets_memberpress_discord_uuid_file_name' );
	$filename = $uuid . ETS_Memberpress_Discord_Admin::$log_file_name;
	$handle   = fopen( WP_CONTENT_DIR . '/' . $filename, 'a+' );
if ( $handle ) {
	while ( ! feof( $handle ) ) {
		echo fgets( $handle ) . '<br />';
	}
	fclose( $handle );
}
?>
</div>
<div class="clrbtndiv">
	<div class="form-group">
		<input type="button" class="clrbtn ets-submit ets-bg-red" id="clrbtn" name="clrbtn" value="Clear Logs !">
		<span class="clr-log spinner" ></span>
	</div>
	<div class="form-group">
		<input type="button" class="ets-submit ets-bg-green" value="Refresh" onClick="window.location.reload()">
	</div>
	<div class="form-group">
		<a href="<?php echo esc_attr( content_url( '/' ) . $filename ); ?>" class="ets-submit ets-bg-download" download><?php echo __( 'Download', 'connect-memberpress-discord-add-on' ); ?></a>
	</div>
  <div class="form-group">
		<a href="<?php echo get_site_url(); ?>/wp-admin/tools.php?page=action-scheduler&status=pending&s=memberpress" class="ets-submit ets-bg-greent"><?php echo __( 'Action Queue', 'connect-memberpress-discord-add-on' ); ?></a>
	</div>
  
</div>
