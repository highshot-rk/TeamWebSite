<?php $comments_count = get_comment_count(); ?>
<div class="dpr-row">
    <div class="dpr-col3 welcome">
        <div class="header">
            <h2><?php echo __('Analysis', 'depc'); ?></h2>
        </div>

        <div class="content">
            <?php echo __('Deeper Comments plugin is a revolutionary move in WordPress commenting systems and will bring a new experience for users never seen before. On this page you can see your comment\'s statistics like recent comments, most commented posts, most active authors and a lot of other types of analytics that we are about to bring in later updates. Feel free to overview your comments statistics.', 'depc'); ?>
            <br>
            <a href="<?php echo admin_url('admin.php?page=deeper_settings'); ?>"><?php echo __('Settings', 'depc'); ?></a>
            <a href="<?php echo admin_url('admin.php?page=deeper_inapp_cm'); ?>"><?php echo __('Inappropriate Comments', 'depc'); ?></a>
        </div>
    </div>
    <div class="dpr-col6 general-information">
        <div class="header">
            <h3><?php echo __('General Information', 'depc'); ?></h3>
        </div>

        <div class="content">
            <canvas id="general_information" width="400" height="400"></canvas>
            <script>
                jQuery(document).ready(function() {
                    var ctx = document.getElementById('general_information');
                    var general_information_bar = new Chart(ctx, {
                        type: 'horizontalBar',
                        data: {
                            "labels": ["<?php echo __('All Comments', 'depc'); ?>", "<?php echo __('Approved', 'depc'); ?>", "<?php echo __('Word_Blacklist', 'depc'); ?>", "<?php echo __('Spam', 'depc'); ?>", "<?php echo __('In Trash', 'depc'); ?>"],
                            "datasets": [{
                                "label": "<?php echo __('General information', 'depc'); ?>",
                                "data": [<?php echo $comments_count['all'] ?>, <?php echo $comments_count['approved'] ?>, <?php echo $comments_count['awaiting_moderation'] ?>, <?php echo $comments_count['spam'] ?>, <?php echo $comments_count['trash'] ?>],
                                "fill": false,
                                "backgroundColor": ["#008aff", "#38E5C2", "#7C51FB", "#fbd61e", "#fb321e"],
                                "borderColor": ["#008aff", "#38E5C2", "#7C51FB", "#fbd61e", "#fb321e"],
                                "borderWidth": 1
                            }],
                        },
                        options: {
                            maintainAspectRatio: false,
                        }
                    });
                })
            </script>
        </div>
    </div>
    <div class="dpr-col3 recent-comments">
        <div class="header">
            <h3><?php echo __('Recent Comments', 'depc'); ?></h3>
        </div>

        <div class="content">
            <ul>
                <?php
                    $recent_comments = get_comments(['number' => 5]);
                    foreach ($recent_comments as $r) {
                        echo '<li>';
                        echo '<a href="'. get_comment_link($r->comment_ID) .'" class="cm-link" title="'. __('Date/time:', 'depc') . $r->comment_date .'">'. $r->comment_author .'</a>';
                        echo '<a href="'. get_post_permalink($r->comment_post_ID) .'" style="text-align:right">'. get_the_title($r->comment_post_ID) .'</a>';
                        echo strip_tags(html_entity_decode(get_comment_excerpt($r->comment_ID))) .'</li>';
                    }
                ?>
            </ul>
        </div>
    </div>
    <div class="dpr-col3 most-commented">
        <div class="header">
            <h3><?php echo __('Most Commented Posts', 'depc'); ?></h3>
        </div>

        <div class="content">

            <ul class="most-commented">
            <?php $wp_query = new WP_Query('orderby=comment_count&posts_per_page=5');
                while ($wp_query->have_posts()) : $wp_query->the_post(); { ?>
                    <li><a href="<?php the_permalink() ?>"><?php the_title(); ?></a> (<?php comments_number('0 comment', '1 comment', '% comments'); ?>)</li>
            <?php } endwhile; ?>
            </ul>
        </div>
    </div>

    <div class="dpr-col3 active-users">
        <div class="header">
            <h3><?php echo __('Most Active Authors', 'depc'); ?></h3>
        </div>

        <div class="content">

            <ul>
                <?php
                    global $wpdb;
                    $comments = $wpdb->get_results('SELECT COUNT(comment_ID) as cm_count, user_id, comment_author_email, comment_author FROM '. $wpdb->comments. ' WHERE comment_type NOT IN ("pingback", "trackback") GROUP BY comment_author_email order by COUNT(comment_ID) desc limit 5');
                    foreach ($comments as $r) {
                        echo '<li>';
                        if($r->user_id) {
                            echo '<a href="'. admin_url('user-edit.php?user_id='.$r->user_id) .'" class="cm-link">'. $r->comment_author .'</a>';
                        } else {
                            echo '<a href="#" class="cm-link">'. $r->comment_author .'</a>';
                        }
                            echo '<div class="details">';
                            echo '<strong class="author-email">'. $r->comment_author_email .'</strong>';
                            echo '<strong class="cm-count">'. $r->cm_count .'</strong>';
                            echo '</div>';
                        echo '</li>';
                    }
                ?>
            </ul>
        </div>
    </div>

</div>