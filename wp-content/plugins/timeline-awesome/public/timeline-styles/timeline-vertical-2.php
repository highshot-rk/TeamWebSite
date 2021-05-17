<?php
$timelines = carbon_get_post_meta( get_the_ID(), 'timeline_items' );
?>

<div class="timeline-content vertical-split vertical-2 timeline-post-<?php echo esc_attr(get_the_ID()); ?>" id="vertical-basic">
    <ul class="timeline timeline-split">
    <?php
    foreach ( $timelines as $timeline ) { ?>
        <li class="timeline-item">
            <div class="timeline-info">
                <span><?php echo esc_html($timeline['timeline_date']); ?></span>
            </div>
            <div class="timeline-marker"></div>
            <div class="timeline-content">
                <h3 class="timeline-title"><?php echo esc_html($timeline['timeline_item_title']); ?></h3>
                <div class="timeline-img">
                    <?php echo wp_get_attachment_image( esc_html($timeline['timeline_item_img']), 'full' ); ?>
                </div>
                <p class="timeline-text"><?php echo wp_specialchars_decode($timeline['timeline_item_content']); ?></p>
            </div>
        </li>
    <?php } ?>
    </ul>
</div>