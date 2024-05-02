<?php
/**
 * Template Name: List WP Cron Events
 */

include("../wp-load.php");

$cron_jobs = _get_cron_array(); // 获取当前所有已计划的 Cron 任务
?>
<div class="wp-cron-list-container">
    <h1>WP Cron 事件列表</h1>
    <?php if ( !empty($cron_jobs) ): ?>
        <ul>
            <?php foreach ( $cron_jobs as $timestamp => $crons ): ?>
                <?php foreach ( $crons as $hook => $events ): ?>
                    <?php foreach ( $events as $event ): ?>
                        <?php //if ($hook == "irent_slicewp_split_payment_event"): ?>
                        <li>
                            <strong>钩子名:</strong> <?php echo esc_html( $hook ); ?><br>
                            <strong>下次执行时间:</strong> <?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $timestamp ), 'Y-m-d H:i:s' ); ?><br>
                            <strong>调度频率:</strong> <?php echo esc_html( $event['schedule'] ); ?><br>
                            <strong>参数:</strong> <?php echo implode( ', ', $event['args'] ); ?>
                        </li>
                        <?php //endif; ?>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>没有找到计划中的 WP Cron 事件。</p>
    <?php endif; ?>
</div>
<?php

// do_action('irent_slicewp_split_payment_event');
// do_action('handle_gtm_tracking_hook');
// get_footer();
