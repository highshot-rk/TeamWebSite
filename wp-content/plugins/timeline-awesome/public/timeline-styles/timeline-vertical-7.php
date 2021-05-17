<?php
$timelines = carbon_get_post_meta( get_the_ID(), 'timeline_items' );
?>

<div class="timeline-vertical-7 timeline-ver-7-timeline js-timeline-ver-7-timeline timeline-post-<?php echo esc_attr(get_the_ID()); ?>" id="vertical-background">
	<div class="timeline-ver-7-timeline__container">
	 <?php
		  foreach ( $timelines as $timeline ) {
			?>
	  	<div class="timeline-ver-7-timeline__block js-timeline-ver-7-block">
			<div class="timeline-ver-7-timeline__img timeline-ver-7-timeline__img--picture js-timeline-ver-7-img">
				<i class="icon-vertical7 <?php echo esc_attr($timeline['social_site_icon']['class']); ?>"></i>
			</div>

			<div class="timeline-ver-7-timeline__content js-timeline-ver-7-content">
			  	<span class="timeline-ver-7-timeline__date timeline-info mobile">
			  		<?php echo esc_html($timeline['timeline_date'])?>
			  	</span>
			  	<h2 class="timeline-title mobile"><?php echo esc_html($timeline['timeline_item_title']); ?></h2>
				<div class="timeline-img">
			  		<?php echo wp_get_attachment_image(esc_html($timeline['timeline_item_img']), 'full'); ?>
			  	</div>
			  	<p class="timeline-text"><?php echo wp_specialchars_decode($timeline['timeline_item_content']); ?></p>
			  	<span class="timeline-ver-7-timeline__date timeline-info">
			  		<?php echo esc_html($timeline['timeline_date'])?>
			  	</span>
			  	<h2 class="timeline-title"><?php echo esc_html($timeline['timeline_item_title']); ?></h2>
			</div> 
			<!-- timeline-ver-7-timeline__content -->
	  	</div>
	  <?php } ?>
	  
	</div>
</div>
<script type="text/javascript">
  // Timeline Body Animations Below ===
	function VerticalTimeline(element) {
	    this.element = element;
	    this.blocks = this.element.getElementsByClassName("js-timeline-ver-7-block");
	    this.images = this.element.getElementsByClassName("js-timeline-ver-7-img");
	    this.contents = this.element.getElementsByClassName("js-timeline-ver-7-content");
	    this.offset = 0.8;
	    this.hideBlocks();
	};

	VerticalTimeline.prototype.hideBlocks = function() {
	    //hide timeline blocks which are outside the viewport
	    if (!"classList" in document.documentElement) {
	        return;
	    }
	    var self = this;
	    for (var i = 0; i < this.blocks.length; i++) {
	        (function(i) {
	            if (self.blocks[i].getBoundingClientRect().top > window.innerHeight * self.offset) {
	                self.images[i].classList.add("timeline-ver-7-is-hidden");
	                self.contents[i].classList.add("timeline-ver-7-is-hidden");
	            }
	        })(i);
	    }
	};

	VerticalTimeline.prototype.showBlocks = function() {
	    if (!"classList" in document.documentElement) {
	        return;
	    }
	    var self = this;
	    for (var i = 0; i < this.blocks.length; i++) {
	        (function(i) {
	            if (self.contents[i].classList.contains("timeline-ver-7-is-hidden") && self.blocks[i].getBoundingClientRect().top <= window.innerHeight * self.offset) {
	                // add bounce-in animation
	                self.images[i].classList.add("timeline-ver-7-timeline__img--bounce-in");
	                self.contents[i].classList.add("timeline-ver-7-timeline__content--bounce-in");
	                self.images[i].classList.remove("timeline-ver-7-is-hidden");
	                self.contents[i].classList.remove("timeline-ver-7-is-hidden");
	            }
	        })(i);
	    }
	};

	var verticalTimelines = document.getElementsByClassName("js-timeline-ver-7-timeline"),
	    verticalTimelinesArray = [],
	    scrolling = false;
	if (verticalTimelines.length > 0) {
	    for (var i = 0; i < verticalTimelines.length; i++) {
	        (function(i) {
	            verticalTimelinesArray.push(new VerticalTimeline(verticalTimelines[i]));
	        })(i);
	    }

	    //show timeline blocks on scrolling
	    window.addEventListener("scroll", function(event) {
	        if (!scrolling) {
	            scrolling = true;
	            (!window.requestAnimationFrame) ? setTimeout(checkTimelineScroll, 250): window.requestAnimationFrame(checkTimelineScroll);
	        }
	    });
	}

	function checkTimelineScroll() {
	    verticalTimelinesArray.forEach(function(timeline) {
	        timeline.showBlocks();
	    });
	    scrolling = false;
	};
</script>