<?php
	$timelines = carbon_get_post_meta( get_the_ID(), 'timeline_items' );
?>
<div class="timeline-container timeline-theme-9 timeline-post-<?php echo esc_attr(get_the_ID()); ?>" id="timeline-horizontal-9">
	<div class="input-flex-container">
		<?php foreach ( $timelines as $timeline ) { ?>
		<div class="input">
			<span class="timeline-info" data-year="<?php echo esc_attr($timeline['timeline_date']); ?>" data-info="<?php echo esc_attr($timeline['timeline_item_title']); ?>"></span>
		</div>
		<?php } ?>
	</div>
	<div class="description-flex-container">
	<?php foreach ( $timelines as $timeline ) { ?>
		<div class="content">
			<?php echo wp_get_attachment_image( intval($timeline['timeline_item_img']), 'full' ); ?>
			<div class="timeline-desc">
			<h5 class="timeline-info"><?php echo esc_html($timeline['timeline_date']); ?></h5>
			<h2 class="timeline-title"><?php echo esc_html($timeline['timeline_item_title']); ?></h1>
			<p class="timeline-text"><?php echo wp_specialchars_decode($timeline['timeline_item_content']); ?></p>
			</div>
		</div>
	<?php } ?>
	</div>
</div>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$(function(){
			var inputs = $('.input');
			var paras = $('#timeline-horizontal-9 .description-flex-container').find('div.content');
			$(inputs).click(function(){
				var t = $(this),
						ind = t.index(),
						matchedPara = $(paras).eq(ind);
				
				$(t).add(matchedPara).addClass('active');
				$(inputs).not(t).add($(paras).not(matchedPara)).removeClass('active');
			});
		});
		$("#timeline-horizontal-9 div.input").first().addClass('active');
		$("#timeline-horizontal-9 div.content").first().addClass('active');
	});
</script>