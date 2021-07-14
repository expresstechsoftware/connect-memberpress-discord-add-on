<?php
$user_id             = sanitize_text_field( trim( get_current_user_id() ) );
$mpr_memberships        = get_posts( array('post_type' => 'memberpressproduct', 'post_status' => 'publish') );
$default_role        = sanitize_text_field( trim( get_option( 'ets_memberpress_discord_default_role_id' ) ) );
$allow_none_member = sanitize_text_field( trim( get_option( 'ets_memberpress_allow_none_member' ) ) );
?>
<div class="notice notice-warning ets-notice">
  <p><i class='fas fa-info'></i> <?php echo __( 'Drag and Drop the Discord Roles over to the MemberPress Levels', 'ets_memberpress_discord' ); ?></p>
</div>
<div class="notice notice-warning ets-notice">
  <p><i class='fas fa-info'></i> <?php echo __( 'Note: Inactive memberships will not display', 'ets_memberpress_discord' ); ?></p>
</div>
<div class="row-container">
  <div class="ets-column discord-roles-col">
	<h2><?php echo __( 'Discord Roles', 'ets_memberpress_discord' ); ?></h2>
	<hr>
	<div class="discord-roles">
	  <span class="spinner"></span>
	</div>
  </div>
  <div class="ets-column">
	<h2><?php echo __( 'MemberPress Memberships', 'ets_memberpress_discord' ); ?></h2>
	<hr>
	<div class="memberpress-levels">
	<?php
	foreach ( array_reverse($mpr_memberships) as $key => $value ) {
			?>
		  <div class="makeMeDroppable" data-level_id="<?php echo $value->ID; ?>" ><span><?php echo $value->post_title; ?></span></div>
			<?php
	}
	?>
	</div>
  </div>
</div>
<form method="post" action="<?php echo get_site_url().'/wp-admin/admin-post.php' ?>">
	<input type="hidden" name="action" value="memberpress_discord_role_mapping">
  <table class="form-table" role="presentation">
	<tbody>
	  <tr>
		<th scope="row"><label for="defaultRole"><?php echo __( 'Default Role', 'ets_memberpress_discord' ); ?></label></th>
		<td>
		  <?php wp_nonce_field( 'discord_role_mappings_nonce', 'ets_memberpress_discord_role_mappings_nonce' ); ?>
		  <input type="hidden" id="selected_default_role" value="<?php echo $default_role; ?>">
		  <select id="defaultRole" name="defaultRole">
			<option value="none"><?php echo __( '-None-', 'ets_memberpress_discord' ); ?></option>
		  </select>
		  <p class="description"><?php echo __( 'This Role will be assigned to all level members', 'ets_memberpress_discord' ); ?></p>
		</td>
	  </tr>
	  <tr>
		<th scope="row"><label><?php echo __( 'Allow none members', 'ets_memberpress_discord' ); ?></label></th>
		<td>
		  <fieldset>
		  <label><input type="radio" name="allow_none_member" value="yes"  
		  <?php
			if ( $allow_none_member == 'yes' ) {
				echo 'checked="checked"'; }
			?>
			 > <span><?php echo __( 'Yes', 'ets_memberpress_discord' ); ?></span></label><br>
		  <label><input type="radio" name="allow_none_member" value="no" 
		  <?php
			if ( empty( $allow_none_member ) || $allow_none_member == 'no' ) {
				echo 'checked="checked"'; }
			?>
			 > <span><?php echo __( 'No', 'ets_memberpress_discord' ); ?></span></label>
		  <p class="description"><?php echo __( 'This setting will apply on Cancel and Expiry of Membership' ); ?></p>
		  </fieldset>
		</td>
	  </tr>
	</tbody>
  </table>
	<br>
  <div class="mapping-json">
	<textarea id="maaping_json_val" name="ets_memberpress_discord_role_mapping">
	<?php
	if ( isset( $ets_discord_roles ) ) {
		echo stripslashes( $ets_discord_roles );}
	?>
	</textarea>
  </div>
  <div class="bottom-btn">
	<button type="submit" name="submit" value="ets_submit" class="ets-submit ets-bg-green">
	  <?php echo __( 'Save Settings', 'ets_memberpress_discord' ); ?>
	</button>
	<button id="MemberPressRevertMapping" name="flush" class="ets-submit ets-bg-red">
	  <?php echo __( 'Flush Mappings', 'ets_memberpress_discord' ); ?>
	</button>
  </div>
</form>
