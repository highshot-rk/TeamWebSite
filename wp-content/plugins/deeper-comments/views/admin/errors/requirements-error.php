<div class="error">
	
	<p>Plugin Name error: Your environment doesn't meet <strong>all</strong> of the system requirements listed below.</p>

	<ul class="ul-disc">
		
		<li>
			<strong>PHP <?php echo Depc::DEPC_PHP_VERSION; ?>+ is required</strong>
			<em>(You're running version <?php echo PHP_VERSION; ?>)</em>
		</li>

		<li>
			<strong>WordPress <?php echo Depc::DEPC_REQUIRED_WP_VERSION; ?>+ is required</strong>
			<em>(You're running version <?php echo esc_html( $wp_version ); ?>)</em>
		</li>

	</ul>
	
</div>