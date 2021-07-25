<?php
$upon_failed_payment                          = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_payment_failed' ) ) );
$log_api_res                                  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_log_api_response' ) ) );
$retry_failed_api                             = sanitize_text_field( trim( get_option( 'ets_memberpress_retry_failed_api' ) ) );
$set_job_cnrc                                 = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_job_queue_concurrency' ) ) );
$set_job_q_batch_size                         = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_job_queue_batch_size' ) ) );
$retry_api_count                              = sanitize_text_field( trim( get_option( 'ets_memberpress_retry_api_count' ) ) );
$ets_memberpress_discord_send_expiration_warning_dm = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_expiration_warning_dm' ) ) );
$ets_memberpress_discord_expiration_warning_message = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_expiration_warning_message' ) ) );
$ets_memberpress_discord_expired_message            = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_expired_message' ) ) );
$ets_memberpress_discord_send_membership_expired_dm = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_membership_expired_dm' ) ) );
$ets_memberpress_discord_expiration_expired_message = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_expiration_expired_message' ) ) );
$ets_memberpress_discord_send_welcome_dm            = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_welcome_dm' ) ) );
$ets_memberpress_discord_welcome_message            = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_welcome_message' ) ) );
$ets_memberpress_discord_send_membership_cancel_dm  = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_send_membership_cancel_dm' ) ) );
$ets_memberpress_discord_cancel_message             = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_cancel_message' ) ) );
?>
<form method="post" action="<?php echo esc_attr( get_site_url() ) . '/wp-admin/admin-post.php' ?>">
<input type="hidden" name="action" value="memberpress_discord_advance_settings">
<?php wp_nonce_field( 'save_discord_adv_settings', 'ets_discord_save_adv_settings' ); ?>
  <table class="form-table" role="presentation">
	<tbody>
  <tr>
		<th scope="row"><?php echo __( 'Send welcome message', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_discord_send_welcome_dm" type="checkbox" id="ets_memberpress_discord_send_welcome_dm" 
		<?php
		if ( $ets_memberpress_discord_send_welcome_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Membership welcome message', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<textarea class="ets_memberpress_discord_dm_textarea" name="ets_memberpress_discord_welcome_message" id="ets_memberpress_discord_welcome_message" row="25" cols="50"><?php if ( $ets_memberpress_discord_welcome_message ) { echo esc_textarea( wp_unslash( stripslashes_deep ( $ets_memberpress_discord_welcome_message ) ) ); } ?></textarea> 
	<br/>
	<small>Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME], [MEMBERSHIP_ENDDATE], [MEMBERSHIP_STARTDATE]</small>
		</fieldset></td>
	  </tr>

	<tr>
		<th scope="row"><?php echo __( 'Send membership expiration warning message', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_discord_send_expiration_warning_dm" type="checkbox" id="ets_memberpress_discord_send_expiration_warning_dm" 
		<?php
		if ( $ets_memberpress_discord_send_expiration_warning_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Membership expiration warning message', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<textarea  class="ets_memberpress_discord_dm_textarea" name="ets_memberpress_discord_expiration_warning_message" id="ets_memberpress_discord_expiration_warning_message" row="25" cols="50"><?php if ( $ets_memberpress_discord_expiration_warning_message ) { echo esc_textarea( wp_unslash( stripslashes_deep ( $ets_memberpress_discord_expiration_warning_message ) ) ); } ?></textarea> 
	<br/>
	<small>Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME], [MEMBERSHIP_ENDDATE], [MEMBERSHIP_STARTDATE]</small>
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Send membership expired message', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_discord_send_membership_expired_dm" type="checkbox" id="ets_memberpress_discord_send_membership_expired_dm" 
		<?php
		if ( $ets_memberpress_discord_send_membership_expired_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Membership expired message', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<textarea  class="ets_memberpress_discord_dm_textarea" name="ets_memberpress_discord_expiration_expired_message" id="ets_memberpress_discord_expiration_expired_message" row="25" cols="50"><?php if ( $ets_memberpress_discord_expiration_expired_message ) { echo esc_textarea( wp_unslash( stripslashes_deep ( $ets_memberpress_discord_expiration_expired_message ) ) ); } ?></textarea> 
	<br/>
	<small>Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME]</small>
		</fieldset>
  </td>
		</tr>
	<tr>
		<th scope="row"><?php echo __( 'Send membership cancel message', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_discord_send_membership_cancel_dm" type="checkbox" id="ets_memberpress_discord_send_membership_cancel_dm" 
		<?php
		if ( $ets_memberpress_discord_send_membership_cancel_dm == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
		<tr>
		<th scope="row"><?php echo __( 'Membership cancel message', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<textarea  class="ets_memberpress_discord_dm_textarea" name="ets_memberpress_discord_cancel_message" id="ets_memberpress_discord_cancel_message" row="25" cols="50"><?php if ( $ets_memberpress_discord_cancel_message ) { echo esc_textarea( wp_unslash( stripslashes_deep ( $ets_memberpress_discord_cancel_message ) ) ); } ?></textarea> 
	<br/>
	<small>Merge fields: [MEMBER_USERNAME], [MEMBER_EMAIL], [MEMBERSHIP_LEVEL], [SITE_URL], [BLOG_NAME]</small>
		</fieldset>
  </td>
		</tr>
  <tr>
		<th scope="row"><?php echo __( 'Re-assign roles upon payment failure', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="upon_failed_payment" type="checkbox" id="upon_failed_payment" 
		<?php
		if ( $upon_failed_payment == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	  </tr>
		<th scope="row"><?php echo __( 'Retry Failed API calls', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="retry_failed_api" type="checkbox" id="retry_failed_api" 
		<?php
		if ( $retry_failed_api == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'How many times a failed API call should get re-try', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="ets_memberpress_retry_api_count" type="number" min="1" id="ets_memberpress_retry_api_count" value="<?php if ( isset( $retry_api_count ) ) { echo esc_attr( $retry_api_count ); } else { echo 1; } ?>">
		</fieldset></td>
	  </tr> 
	  <tr>
		<th scope="row"><?php echo __( 'Set job queue concurrency', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="set_job_cnrc" type="number" min="1" id="set_job_cnrc" value="<?php if ( isset( $set_job_cnrc ) ) { echo esc_attr( $set_job_cnrc ); } else { echo 1; } ?>">
		</fieldset></td>
	  </tr>
	  <tr>
		<th scope="row"><?php echo __( 'Set job queue batch size', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="set_job_q_batch_size" type="number" min="1" id="set_job_q_batch_size" value="<?php if ( isset( $set_job_q_batch_size ) ) { echo esc_attr( $set_job_q_batch_size ); } else { echo 10; } ?>">
		</fieldset></td>
	  </tr>
	<tr>
		<th scope="row"><?php echo __( 'Log API calls response (For debugging purpose)', 'ets_memberpress_discord' ); ?></th>
		<td> <fieldset>
		<input name="log_api_res" type="checkbox" id="log_api_res" 
		<?php
		if ( $log_api_res == true ) {
			echo 'checked="checked"'; }
		?>
		 value="1">
		</fieldset></td>
	  </tr>
	
	</tbody>
  </table>
  <div class="bottom-btn">
	<button type="submit" name="adv_submit" value="ets_submit" class="ets-submit ets-bg-green">
	  <?php echo __( 'Save Settings', 'ets_memberpress_discord' ); ?>
	</button>
  </div>
</form>
