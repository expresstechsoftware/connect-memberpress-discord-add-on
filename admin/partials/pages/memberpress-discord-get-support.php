<?php
  $currUserName = '';
  $currentUser  = wp_get_current_user();
if ( $currentUser ) {
	$currUserName = sanitize_text_field( trim( $currentUser->user_login ) );
}
?>
<div class="contact-form ">
	<form method="post" action="<?php echo esc_attr( get_site_url() ) . '/wp-admin/admin-post.php'; ?>">
		<input type="hidden" name="action" value="memberpress_discord_send_support_mail">
	  <div class="ets-container">
		<div class="top-logo-title">
		  <img src="<?php esc_html_e( ETS_MEMBERPRESS_DISCORD_PLUGIN_DIR_URL . 'admin/images/ets-logo.png' ); ?>" class="img-fluid company-logo" alt="">
		  <h1><?php esc_html_e( 'ExpressTech Softwares Solutions Pvt. Ltd.', 'connect-memberpress-discord-add-on' ); ?></h1>
		  <p><?php esc_html_e( 'ExpressTech Software Solution Pvt. Ltd. is the leading Enterprise WordPress development company.', 'connect-memberpress-discord-add-on' ); ?><br>
		  <?php esc_html_e( 'Contact us for any WordPress Related development projects.', 'connect-memberpress-discord-add-on' ); ?></p>
		</div>
		<ul style="text-align: left;">
			<li class="mp-icon mp-icon-right-big"><?php esc_html_e( 'If you encounter any issues or errors, please report them on our support forum for the Memberpress Discord Add-on plugin. Our community will be happy to help you troubleshoot and resolve the issue.', 'connect-memberpress-discord-add-on' ); ?></li>
			<li class="mp-icon mp-icon-right-big">
			<?php
			echo wp_kses(
				'<a href="https://wordpress.org/support/plugin/expresstechsoftwares-memberpress-discord-add-on/">Support » Plugin: Connect MemberPress To Discord</a>',
				array(
					'a' => array(
						'href' => array(),
					),
				)
			);
			?>
 </li>
		</ul>
		<!-- <div class="form-fields-box ">
		  <div class="ets-row ets-mt-5 ets-align-items-center">
			<div class="ets-col-7 ets-offset-md-1">
			  <div class="contact-fields pr-100">
				<div class="ets-form-group">
				  <label><?php esc_html_e( 'Full Name', 'connect-memberpress-discord-add-on' ); ?></label>
				  <input type="text" name="ets_user_name" value="<?php echo esc_html( $currUserName ); ?>" class="form-control contact-input" placeholder="<?php esc_html_e( 'Write Your Full Name', 'connect-memberpress-discord-add-on' ); ?>">
				  <?php wp_nonce_field( 'send_support_mail', 'ets_discord_send_support_mail' ); ?>
				</div>
				<div class="ets-form-group">
				  <label><?php esc_html_e( 'Contact Email', 'connect-memberpress-discord-add-on' ); ?></label>
				  <input type="text" name="ets_user_email" class="form-control contact-input" value="<?php echo esc_attr( get_option( 'admin_email' ) ); ?>" placeholder="<?php esc_html_e( 'Write Your Email', 'connect-memberpress-discord-add-on' ); ?>">
				</div>
				<div class="ets-form-group">
				  <label><?php esc_html_e( 'Subject', 'connect-memberpress-discord-add-on' ); ?></label>
				  <input type="text" name="ets_support_subject" class="form-control contact-input" placeholder="<?php esc_html_e( 'Write Your Subject', 'connect-memberpress-discord-add-on' ); ?>" required="">
				</div>
				<div class="ets-form-group">
				  <label><?php esc_html_e( 'Message', 'connect-memberpress-discord-add-on' ); ?></label>
				  <textarea name="ets_support_msg" class="form-control contact-textarea" required=""></textarea>
				</div>
				<div class="submit-btn d-flex align-items-center w-100 pt-3">
				  <input type="submit" name="save" id="save" class="btn btn-submit ets-bg-green" value="Submit">                  
				  <a href="skype:ravi.soni971?chat" class="btn btn-skype ml-auto"><?php esc_html_e( 'Skype', 'connect-memberpress-discord-add-on' ); ?></a>
				</div>
			  </div>
			</div>
			<div class="ets-col-3">
			  <div class="right-side-box">
				<div class="contact-details d-inline-block w-100 mb-4">
				  <div class="top-icon-title d-flex align-items-center w-100">
					<i class="fas fa-envelope title-icon fa-lg fa-inverse" aria-hidden="true"></i>
					<p><?php esc_html_e( 'Email', 'connect-memberpress-discord-add-on' ); ?></p>
				  </div>
				  <div class="contact-body mt-3">
					<p><a href="mailto:contact@expresstechsoftwares.com"><?php echo esc_html( 'contact@expresstechsoftwares.com' ); ?></a></p>
					<p><a href="mailto:vinod.tiwari@expresstechsoftwares.com"><?php echo esc_html( 'vinod.tiwari@expresstechsoftwares.com' ); ?></a></p>
				  </div>
				</div>
				<div class="contact-details d-inline-block w-100 mb-4">
				  <div class="top-icon-title d-flex align-items-center w-100">
					<i class="fab fa-skype title-icon fa-lg fa-inverse" aria-hidden="true"></i>
					<p><?php esc_html_e( 'Skype', 'connect-memberpress-discord-add-on' ); ?></p>
				  </div>
				  <div class="contact-body mt-3">
					<p><?php esc_html_e( 'ravi.soni971', 'connect-memberpress-discord-add-on' ); ?></p>
				  </div>
				</div>
				<div class="contact-details d-inline-block w-100">
				  <div class="top-icon-title d-flex align-items-center w-100">
					<i class="fab fa-whatsapp title-icon fa-lg fa-inverse" aria-hidden="true"></i>
					<p><?php esc_html_e( 'Whatsapp / Phone', 'connect-memberpress-discord-add-on' ); ?></p>
				  </div>
				  <div class="contact-body mt-3">
					<p><?php echo esc_html( '+91-9806724185' ); ?></p>
				  </div>
				</div>
			  </div>
			</div>
		  </div>
		</div> -->

	  </div>
  </form>
</div>
