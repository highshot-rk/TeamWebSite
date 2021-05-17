<?php
	$timelines = carbon_get_post_meta( get_the_ID(), 'timeline_items' );
?>

<div id="timeline-horizontal-6" class="timeline-content timeline-post-<?php echo esc_attr(get_the_ID()); ?>">
	<?php
	foreach ( $timelines as $timeline ) { ?>
	<div class="tl-item" style="background-image: url(<?php echo esc_url(wp_get_attachment_url( intval($timeline['timeline_item_img']))) ?>);">
		<div class="tl-year">
			<p class="f2 heading--sanSerif timeline-info"><?php echo esc_html($timeline['timeline_date']); ?></p>
		</div>

		<div class="tl-content">
			<h1 class="timeline-title"><?php echo esc_html($timeline['timeline_item_title']); ?></h1>
			<p class="timeline-text"><?php echo wp_specialchars_decode($timeline['timeline_item_content']); ?></p>
		</div>
	</div>
	<?php } ?>

</div>
