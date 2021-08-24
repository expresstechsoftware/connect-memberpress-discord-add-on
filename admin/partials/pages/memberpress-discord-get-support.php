<?php
  $currUserName = '';
  $currentUser  = wp_get_current_user();
if ( $currentUser ) {
	$currUserName = sanitize_text_field( trim( $currentUser->user_login ) );
}
?>
<div class="contact-form ">
	<form method="post" action="<?php echo esc_attr( get_site_url() ) . '/wp-admin/admin-post.php' ?>">
		<input type="hidden" name="action" value="memberpress_discord_send_support_mail">
	  <div class="ets-container">
		<div class="top-logo-title">
		  <img src="<?php echo MEMBERPRESS_DISCORD_PLUGIN_DIR_URL  . 'admin/images/ets-logo.png'; ?>" class="img-fluid company-logo" alt="">
		  <h1><?php echo __( 'ExpressTech Softwares Solutions Pvt. Ltd.', 'ets_memberpress_discord' ); ?></h1>
		  <p><?php echo __( 'ExpressTech Software Solution Pvt. Ltd. is the leading Enterprise WordPress development company.', 'ets_memberpress_discord' ); ?><br>
		  <?php echo __( 'Contact us for any WordPress Related development projects.', 'ets_memberpress_discord' ); ?></p>
		</div>
		<div class="form-fields-box ">
		  <div class="ets-row ets-mt-5 ets-align-items-center">
			<div class="ets-col-7 ets-offset-md-1">
			  <div class="contact-fields pr-100">
				<div class="ets-form-group">
				  <label><?php echo __( 'Full Name', 'ets_memberpress_discord' ); ?></label>
				  <input type="text" name="ets_user_name" value="<?php echo esc_html( $currUserName ); ?>" class="form-control contact-input" placeholder="<?php echo __( 'Write Your Full Name', 'ets_memberpress_discord' ); ?>">
				  <?php wp_nonce_field( 'send_support_mail', 'ets_discord_send_support_mail' ); ?>
				</div>
				<div class="ets-form-group">
				  <label><?php echo __( 'Contact Email', 'ets_memberpress_discord' ); ?></label>
				  <input type="text" name="ets_user_email" class="form-control contact-input" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" placeholder="<?php echo __( 'Write Your Email', 'ets_memberpress_discord' ); ?>">
				</div>
				<div class="ets-form-group">
				  <label><?php echo __( 'Subject', 'ets_memberpress_discord' ); ?></label>
				  <input type="text" name="ets_support_subject" class="form-control contact-input" placeholder="<?php echo __( 'Write Your Subject', 'ets_memberpress_discord' ); ?>" required="">
				</div>
				<div class="ets-form-group">
				  <label><?php echo __( 'Message', 'ets_memberpress_discord' ); ?></label>
				  <textarea name="ets_support_msg" class="form-control contact-textarea" required=""></textarea>
				</div>
				<div class="submit-btn d-flex align-items-center w-100 pt-3">
				  <input type="submit" name="save" id="save" class="btn btn-submit ets-bg-green" value="Submit">                  
				  <a href="skype:ravi.soni971?chat" class="btn btn-skype ml-auto"><?php echo __( 'Skype', 'ets_memberpress_discord' ); ?></a>
				</div>
			  </div>
			</div>
			<div class="ets-col-3">
			  <div class="right-side-box">
				<div class="contact-details d-inline-block w-100 mb-4">
				  <div class="top-icon-title d-flex align-items-center w-100">
					<i class="fas fa-envelope title-icon fa-lg fa-inverse" aria-hidden="true"></i>
					<p><?php echo __( 'Email', 'ets_memberpress_discord' ); ?></p>
				  </div>
				  <div class="contact-body mt-3">
					<p><a href="mailto:contact@expresstechsoftwares.com"><?php echo esc_html( 'contact@expresstechsoftwares.com' ); ?></a></p>
					<p><a href="mailto:business@expresstechsoftwares.com"><?php echo esc_html( 'business@expresstechsoftwares.com' ); ?></a></p>
				  </div>
				</div>
				<div class="contact-details d-inline-block w-100 mb-4">
				  <div class="top-icon-title d-flex align-items-center w-100">
					<i class="fab fa-skype title-icon fa-lg fa-inverse" aria-hidden="true"></i>
					<p><?php echo __( 'Skype', 'ets_memberpress_discord' ); ?></p>
				  </div>
				  <div class="contact-body mt-3">
					<p><?php echo __( 'ravi.soni971', 'ets_memberpress_discord' ); ?></p>
				  </div>
				</div>
				<div class="contact-details d-inline-block w-100">
				  <div class="top-icon-title d-flex align-items-center w-100">
					<i class="fab fa-whatsapp title-icon fa-lg fa-inverse" aria-hidden="true"></i>
					<p><?php echo __( 'Whatsapp / Phone', 'ets_memberpress_discord' ); ?></p>
				  </div>
				  <div class="contact-body mt-3">
					<p><?php echo esc_html( '+91-9806724185' ); ?></p>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div>
	  </div>
  </form>
</div>
