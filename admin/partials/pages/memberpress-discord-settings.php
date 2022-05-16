<?php
$ets_memberpress_discord_client_id    = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_id' ) ) );
$discord_client_secret                = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_client_secret' ) ) );
$discord_bot_token                    = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_bot_token' ) ) );
$ets_memberpress_discord_redirect_url = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_redirect_url' ) ) );
$ets_discord_roles                    = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_role_mapping' ) ) );
$ets_memberpress_discord_server_id    = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_server_id' ) ) );
$current_url = ets_memberpress_discord_get_current_screen_url();

$ets_memberpress_discord_connected_bot_name =  sanitize_text_field( trim( get_option( 'ets_memberpress_discord_connected_bot_name' ) ) );
if ( empty( $ets_memberpress_discord_redirect_url ) ) {
	$acc_url                              = sanitize_text_field( trim( get_site_url() . '/account' ) );
	$ets_memberpress_discord_redirect_url = ets_memberpress_discord_get_memberpress_formated_discord_redirect_url( $acc_url );
}
?>
<form method="post" action="<?php echo esc_attr( get_site_url() ) . '/wp-admin/admin-post.php'; ?>">
<input type="hidden" name="action" value="memberpress_discord_general_settings">
<input type="hidden" name="current_url" value="<?php echo esc_html( $current_url ); ?> " />
	<?php wp_nonce_field( 'save_discord_settings', 'ets_discord_save_settings' ); ?>
	<div class="ets-input-group">
		<label><?php echo __( 'Client ID', 'connect-memberpress-discord-add-on' ); ?> :</label>
		<input type="text" class="ets-input" name="ets_memberpress_discord_client_id" value="<?php if ( isset( $ets_memberpress_discord_client_id ) ) { echo $ets_memberpress_discord_client_id;} ?>" required placeholder="<?php echo __( 'Discord Client ID', 'connect-memberpress-discord-add-on' ); ?>">
	</div>
	<div class="ets-input-group">
		<label><?php echo __( 'Client Secret', 'connect-memberpress-discord-add-on' ); ?> :</label>
		<input type="password" class="ets-input" name="ets_memberpress_discord_client_secret" value="<?php if ( isset( $discord_client_secret ) ) { echo esc_attr( $discord_client_secret ); } ?>" required placeholder="<?php echo __( 'Discord Client Secret', 'connect-memberpress-discord-add-on' ); ?>">
	</div>
	<div class="ets-input-group">
		<label><?php echo __( 'Redirect URL', 'connect-memberpress-discord-add-on' ); ?> :</label>
		<input type="text" class="ets-input" name="ets_memberpress_discord_redirect_url" placeholder="<?php echo __( 'Discord Redirect Url', 'connect-memberpress-discord-add-on' ); ?>" value="<?php if ( isset( $ets_memberpress_discord_redirect_url ) ) { echo esc_attr( $ets_memberpress_discord_redirect_url ); } ?>" required>
		<p class="description"><?php echo __( 'Registered discord app url', 'connect-memberpress-discord-add-on' ); ?></p>    
	</div>
  <div class="ets-input-group">
  <label><?php echo __( 'Bot Auth Redirect URL', 'connect-memberpress-discord-add-on' ); ?> :</label>
		<input type="text" readonly="true" class="ets-input" name="ets_memberpress_discord_bot_auth_redirect" value="<?php echo get_admin_url('', 'admin.php').'?page=memberpress-discord&via=memberpress-discord-bot'; ?>" required placeholder="<?php echo __( 'Bot Auth Redirect URL', 'connect-memberpress-discord-add-on' ); ?>">
    <p class="description msg-green"><b><?php echo __("Copy this URL and paste inside your https://discord.com/developers/applications -> 0Auth2 -> Redirects", "connect-memberpress-discord-add-on"); ?></b></p>
	</div>
	<div class="ets-input-group">
  <?php
            if ( isset( $ets_memberpress_discord_connected_bot_name ) && !empty( $ets_memberpress_discord_connected_bot_name ) ){
                echo sprintf(__( '<p class="description msg-green">Make sure the Bot <b> %1$s </b> <span class="discord-bot"></span> have the high priority than the roles it has to manage. Open <a target="_blank" href="https://discord.com/channels/%2$s">Discord Server</a></p>', 'connect-memberpress-discord-add-on'), $ets_memberpress_discord_connected_bot_name, $ets_memberpress_discord_server_id );
            }
            ?>
		<label><?php echo __( 'Bot Token', 'connect-memberpress-discord-add-on' ); ?> :</label>
		<input type="password" class="ets-input" name="ets_memberpress_discord_bot_token" value="<?php if ( isset( $discord_bot_token ) ) { echo esc_attr( $discord_bot_token ); } ?>" required placeholder="<?php echo __( 'Discord Bot Token', 'connect-memberpress-discord-add-on' ); ?>">
	</div>
  
	<div class="ets-input-group">
		<label><?php echo __( 'Server ID', 'connect-memberpress-discord-add-on' ); ?> :</label>
		<input type="text" class="ets-input" name="ets_memberpress_discord_server_id" placeholder="<?php echo __( 'Discord Server Id', 'connect-memberpress-discord-add-on' ); ?>" value="<?php if ( isset( $ets_memberpress_discord_server_id ) ) { echo esc_attr( $ets_memberpress_discord_server_id ); } ?>" required>
	</div>
	<?php if ( empty( $ets_memberpress_discord_client_id ) || empty( $discord_client_secret ) || empty( $discord_bot_token ) || empty( $ets_memberpress_discord_redirect_url ) || empty( $ets_memberpress_discord_server_id ) ) { ?>
		<p class="ets-danger-text description">
		<?php echo __( 'Please save your form', 'connect-memberpress-discord-add-on' ); ?>
		</p>
	<?php } ?>
	<p>
		<button type="submit" name="submit" value="ets_submit" class="ets-submit ets-bg-green">
		<?php echo __( 'Save Settings', 'connect-memberpress-discord-add-on' ); ?>
		</button>
		<?php if ( get_option( 'ets_memberpress_discord_client_id' ) ) : ?>
            <a href="?action=mepr-discord-connectToBot" class="ets-btn btn-connect-to-bot" id="connect-discord-bot"><?php echo __( 'Connect your Bot', 'connect-memberpress-discord-add-on' ); ?><?php echo ETS_Memberpress_Discord::get_discord_logo_white();?></a>
		<?php endif; ?>
	</p>
</form>
