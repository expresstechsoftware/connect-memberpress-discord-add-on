<div class="error-log">
<?php
	$filename = Memberpress_Discord_Admin::$log_file_name;
	$handle   = fopen( MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . $filename, 'a+' );
while ( ! feof( $handle ) ) {
	echo fgets( $handle ) . '<br />';
}
	fclose( $handle );
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
		<a href="<?php echo MEMBERPRESS_DISCORD_PLUGIN_DIR_PATH . 'discord_api_logs.txt'; ?>" class="ets-submit ets-bg-download" download>Download</a>
	</div>
</div>
