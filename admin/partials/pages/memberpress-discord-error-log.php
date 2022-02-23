<div class="error-log">
<?php
	$uuid     = get_option( 'ets_memberpress_discord_uuid_file_name' );
	$filename = $uuid . Memberpress_Discord_Admin::$log_file_name;
	$handle   = fopen( WP_CONTENT_DIR . '/' . $filename, 'a+' );
  if( $handle ){
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
		<a href="<?php echo esc_attr( content_url('/') . $filename ); ?>" class="ets-submit ets-bg-download" download><?php echo __( 'Download', 'memberpress-discord-add-on' ); ?></a>
	</div>
  <div class="form-group">
		<a href="<?php echo get_site_url(); ?>/wp-admin/tools.php?page=action-scheduler&status=pending&s=memberpress" class="ets-submit ets-bg-greent"><?php echo __( 'Action Queue', 'memberpress-discord-add-on' ); ?></a>
	</div>
  
</div>
