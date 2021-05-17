<?php
$timelines = carbon_get_post_meta( get_the_ID(), 'timeline_items' );
?>

<div id="timeline-vertical-9" class="timeline-content timeline-post-<?php echo esc_attr(get_the_ID()); ?>">
	<ul class="timeline-v9">
	 <?php foreach ( $timelines as $timeline ) { ?>
		<li class="event" data-date="<?php echo esc_attr($timeline['timeline_date']); ?>">
			<div class="member-infos">
				<span class="timeline-info mobile"><?php echo esc_html($timeline['timeline_date']); ?></span>
				<h1 class="member-title timeline-title"><?php echo esc_html($timeline['timeline_item_title']);?></h1>
				<div class="member-content">
					<div class="timeline-img">
	                    <?php echo wp_get_attachment_image(esc_html($timeline['timeline_item_img']), 'full'); ?>
	                </div>
                	<p class="timeline-text"><?php echo wp_specialchars_decode($timeline['timeline_item_content']); ?></p>
                </div>
			</div>
			<ul class="icon-v9">
				<li class="icon-style-v9">
					<a class="fa fa-chevron-down"></a>
				</li>
			</ul> 
		</li>
	<?php } ?>
	</ul>
</div>


<script type="text/javascript">
jQuery(document).ready(function($){
	$('.member-title').click(function(e) {
		if($(this).parent().hasClass('active')) {       
	        $(this).parent().removeClass('active');
	    }
	    else{
	        $(this).parent().addClass('active');
	    }
		$(this).next().slideToggle();
		$(this).next().next().next().slideToggle();
	});
});
</script>