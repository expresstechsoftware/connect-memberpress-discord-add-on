<button id="toggle-search-form"><?php esc_html_e( 'Toggle Search Form', 'connect-memberpress-discord-add-on' ); ?></button>

<div class="search-form-wrapper" style="display: none;">

	<form id="ets-log-search-form" method="GET" action="
	<?php
	echo esc_url(
		add_query_arg(
			array(
				'page' => 'memberpress-discord',
			),
			admin_url( 'admin.php' )
		)
	);
	?>
	#mepr_logs">
		<label for="api-response-code"><?php esc_html_e( 'API Response Code:', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="api-response-code" id="api-response-code" value="<?php echo esc_attr( isset( $_GET['api-response-code'] ) ? $_GET['api-response-code'] : '' ); ?>">

		<label for="error-message"><?php esc_html_e( 'Error Message:', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="error-message" id="error-message" value="<?php echo esc_attr( isset( $_GET['error-message'] ) ? $_GET['error-message'] : '' ); ?>">

		<label for="wp-user-id"><?php esc_html_e( 'WordPress User ID:', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="wp-user-id" id="wp-user-id" value="<?php echo esc_attr( isset( $_GET['wp-user-id'] ) ? $_GET['wp-user-id'] : '' ); ?>">

		<label for="discord-user-id"><?php esc_html_e( 'Discord User ID:', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="discord-user-id" id="discord-user-id" value="<?php echo esc_attr( isset( $_GET['discord-user-id'] ) ? $_GET['discord-user-id'] : '' ); ?>">

		<label for="datetime"><?php esc_html_e( 'Datetime (YYYY-MM-DD HH:MM:SS):', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="datetime" id="datetime" value="<?php echo esc_attr( isset( $_GET['datetime'] ) ? $_GET['datetime'] : '' ); ?>">

		<!-- Add new search field for API endpoint -->
		<label for="api-endpoint"><?php esc_html_e( 'API Endpoint:', 'connect-memberpress-discord-add-on' ); ?></label>
		<input type="text" name="api-endpoint" id="api-endpoint" value="<?php echo esc_attr( isset( $_GET['api-endpoint'] ) ? $_GET['api-endpoint'] : '' ); ?>">

		<input type="submit" class="ets-submit ets-bg-blue" value="<?php esc_attr_e( 'Search', 'connect-memberpress-discord-add-on' ); ?>">
	</form>

</div>

<?php

$logs = ets_memberpress_discord_display_log_data();
// var_dump( $logs );
if ( $logs ) {
	$page        = isset( $_GET['page'] ) ? absint( $_GET['page'] ) : 1;
	$per_page    = 10;
	$total_logs  = count( $logs );
	$total_pages = ceil( $total_logs / $per_page );
	$offset      = ( $page - 1 ) * $per_page;
	$sort_by     = isset( $_GET['sort_by'] ) ? sanitize_key( $_GET['sort_by'] ) : 'datetime';
	$sort_order  = isset( $_GET['sort_order'] ) ? strtoupper( sanitize_text_field( $_GET['sort_order'] ) ) : 'DESC';

	usort(
		$logs,
		function ( $a, $b ) use ( $sort_by, $sort_order ) {
				 return strcasecmp( $sort_order === 'DESC' ? $b->$sort_by : $a->$sort_by, $sort_order === 'DESC' ? $a->$sort_by : $b->$sort_by );
		}
	);
	$paginated_logs = array_slice( $logs, $offset, $per_page );

	?>

<div class="error-log">
	<table class="log-table">
	<thead>
		<tr>
			<th><a href="?sort_by=id&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">ID ?</a></th>
			<th><a href="?sort_by=api_endpoint&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">API Endpoint</a></th>
			<th><a href="?sort_by=api_endpoint_version&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">API Endpoint Version</a></th>
			<th><a href="?sort_by=request_params&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Request Params</a></th>
			<th><a href="?sort_by=api_response_header&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">API Response Header</a></th>
			<th><a href="?sort_by=api_response_body&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">API Response Body</a></th>
			<th><a href="?sort_by=api_response_http_code&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">API Response HTTP Code</a></th>
			<th><a href="?sort_by=error_detail_code&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Error Detail Code</a></th>
			<th><a href="?sort_by=error_message&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Error Message</a></th>
			<th><a href="?sort_by=wp_user_id&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">WordPress User ID</a></th>
			<th><a href="?sort_by=discord_user_id&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Discord User ID</a></th>
			<th><a href="?sort_by=datetime&sort_order=<?php echo $sort_order === 'ASC' ? 'DESC' : 'ASC'; ?>">Timestamp</a></th>
		</tr>
	</thead>
	<tbody>
		<?php
		foreach ( $logs as $log ) :
			?>
			<tr>
				<td><?php echo esc_html( $log->id ); ?></td>
				<td><?php echo esc_html( $log->api_endpoint ); ?></td>
				<td><?php echo esc_html( $log->api_endpoint_version ); ?></td>
				<td><?php echo esc_html( $log->request_params ); ?></td>
				<td><?php echo esc_html( $log->api_response_header ); ?></td>
				<td><?php echo esc_html( $log->api_response_body ); ?></td>
				<td><?php echo esc_html( $log->api_response_http_code ); ?></td>
				<td><?php echo esc_html( $log->error_detail_code ); ?></td>
				<td><?php echo esc_html( $log->error_message ); ?></td>
				<td><?php echo esc_html( $log->wp_user_id ); ?></td>
				<td><?php echo esc_html( $log->discord_user_id ); ?></td>
				<td><?php echo esc_html( $log->datetime ); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

	<div class="log-pagination">
		<?php
		echo paginate_links(
			array(
				'base'         => add_query_arg( 'paged', '%#%' ),
				'format'       => '',
				'prev_text'    => __( '&laquo; Previous' ),
				'next_text'    => __( 'Next &raquo;' ),
				'total'        => $total_pages,
				'current'      => $current_page,
				'add_fragment' => '#mepr_logs',
			)
		);
		?>
	</div>
</div>
	<?php
} else {
	echo 'No logs found.....';
}
?>

<div class="ets-log-popup" style="display: none;"></div>


<div class="clrbtndiv">
	<div class="form-group">
		<input type="button" class="clrbtn ets-submit ets-bg-red" id="clrbtn" name="clrbtn" value="Clear Logs !">
		<span class="clr-log spinner"></span>
	</div>
	<div class="form-group">
		<input type="button" class="ets-submit ets-bg-green" value="Refresh" onClick="window.location.reload()">
	</div>
	<div class="form-group">
		<a href="<?php echo get_site_url(); ?>/wp-admin/tools.php?page=action-scheduler&status=pending&s=memberpress" class="ets-submit ets-bg-greent"><?php echo __( 'Action Queue', 'connect-memberpress-discord-add-on' ); ?></a>
	</div>
</div>
