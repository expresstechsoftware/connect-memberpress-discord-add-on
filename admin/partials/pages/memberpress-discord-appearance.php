<?php
$btn_color                          = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_btn_color' ) ) );
$ets_memberpress_discord_btn_disconnect_color                          = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_btn_disconnect_color' ) ) );
$btn_text                        	= sanitize_text_field( trim( get_option( 'ets_memberpress_discord_loggedout_btn_text' ) ) );
$loggedin_btn_text                  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_loggedin_btn_text' ) ) );
$ets_memberpress_discord_disconnect_btn_text                  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_disconnect_btn_text' ) ) );
$current_screen = ets_memberpress_discord_get_current_screen_url();
?>
<form method="post" action="<?php echo get_site_url().'/wp-admin/admin-post.php' ?>">
 <input type="hidden" name="action" value="memberpress_discord_save_appearance_settings">
 <input type="hidden" name="current_url" value="<?php echo $current_screen; ?>" />
<?php wp_nonce_field( 'save_discord_aprnc_settings', 'ets_discord_save_aprnc_settings' ); ?>
  <table class="form-table" role="presentation">
	<tbody>
    <tr>
		<th scope="row"><?php echo __( 'Connect/Login Button color', 'connect-memberpress-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_btn_color" type="text" id="ets_memberpress_btn_color" value="<?php if ( $btn_color ) {echo $btn_color; }?>" data-default-color="#77a02e">
		</fieldset></td> 
	</tr>
  <tr>
		<th scope="row"><?php echo __( 'Disconnect Button color', 'connect-memberpress-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_discord_btn_disconnect_color" type="text" id="ets_memberpress_discord_btn_disconnect_color" value="<?php if ( $ets_memberpress_discord_btn_disconnect_color ) {echo $ets_memberpress_discord_btn_disconnect_color; }?>" data-default-color="#ff0000">
		</fieldset></td> 
	</tr>
	<tr>
		<th scope="row"><?php echo __( 'Text on the Button for logged-in users', 'connect-memberpress-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_loggedin_btn_text" type="text" id="ets_memberpress_loggedin_btn_text" value="<?php if ( $loggedin_btn_text ) {echo $loggedin_btn_text; }?>">
		</fieldset></td> 
	</tr>
	<tr>
		<th scope="row"><?php echo __( 'Text on the Button for non-login users', 'connect-memberpress-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_loggedout_btn_text" type="text" id="ets_memberpress_loggedout_btn_text" value="<?php if ( $btn_text ) { echo $btn_text; } ?>">
		</fieldset></td> 
	</tr>	
  <tr>
		<th scope="row"><?php echo __( 'Text on the Disconnect Button', 'connect-memberpress-discord-add-on' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_discord_disconnect_btn_text" type="text" id="ets_memberpress_discord_disconnect_btn_text" value="<?php if ( $ets_memberpress_discord_disconnect_btn_text ) { echo $ets_memberpress_discord_disconnect_btn_text; } ?>">
		</fieldset></td> 
	</tr>	
	</tbody>
  </table>
  <div class="bottom-btn">
	<button type="submit" name="apr_submit" value="ets_submit" class="ets-submit ets-bg-green">
	  <?php echo __( 'Save Settings', 'connect-memberpress-discord-add-on' ); ?>
	</button>
  </div>
</form>
