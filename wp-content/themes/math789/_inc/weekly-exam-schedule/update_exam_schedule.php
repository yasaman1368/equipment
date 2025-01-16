
<?php

function update_exam_schedule()
{
    wp_mail('alipor.hossain@gmail.com', 'cron job', 'cron job about weekly exam schedule active');
    global $wpdb;
    $today = new DateTime();
    $start_of_current_week = clone $today;
    $start_of_current_week->modify('last saturday');
    $start_of_next_week = clone $start_of_current_week;
    $start_of_next_week->modify('+1 week');
    $start_two_next_week = clone $start_of_current_week;
    $start_two_next_week->modify('+2 week');

    // Get schedules for current week, next week, and two weeks ahead
    $current_week_schedules = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM wp_exam_schedule WHERE exam_date >= %s AND exam_date < %s",
            $start_of_current_week->format('Y-m-d'),
            $start_of_next_week->format('Y-m-d')
        )
    );
    $next_week_schedules = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM wp_exam_schedule WHERE exam_date >= %s AND exam_date < %s",
            $start_of_next_week->format('Y-m-d'),
            $start_two_next_week->format('Y-m-d')
        )
    );
    $next_two_week_schedules = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM wp_exam_schedule WHERE exam_date >= %s",
            $start_two_next_week->format('Y-m-d')
        )
    );

    $flag_two = false;
    $flag_three = false;

    // Update current week schedules with next week's data
    foreach ($current_week_schedules as $schedule) {
        $next_week_date = new DateTime($schedule->exam_date);
        $next_week_date->modify('+1 week'); // Move to the same day next week

        // Find corresponding next week schedule
        $next_week_schedule = array_filter($next_week_schedules, function ($nws) use ($schedule) {
            return  $nws->day == $schedule->day;
        });

        if (!empty($next_week_schedule)) {
            $next_week_schedule = reset($next_week_schedule);
            $wpdb->update(
                'wp_exam_schedule',
                [
                    // 'subject' => $next_week_schedule->subject,
                    'content' => $next_week_schedule->content,
                    'exam_date' => $next_week_date->format('Y-m-d')
                ],
                ['id' => $schedule->id],
                ['%s', '%s', '%s'], // Data types
                ['%d'] // ID type
            );
        }
        $flag_two = true;
    }
    if ($flag_two) {
        // Update next week schedules with two weeks ahead data
        foreach ($next_week_schedules as $schedule) {
            $two_next_week_date = new DateTime($schedule->exam_date);
            $two_next_week_date->modify('+1 week');

            // Find corresponding two weeks ahead schedule
            $two_next_week_schedule = array_filter($next_two_week_schedules, function ($tnws) use ($schedule) {
                return  $tnws->day == $schedule->day;
            });

            if (!empty($two_next_week_schedule)) {
                $two_next_week_schedule = reset($two_next_week_schedule);
                $wpdb->update(
                    'wp_exam_schedule',
                    [
                        // 'subject' => $two_next_week_schedule->subject,
                        'content' => $two_next_week_schedule->content,
                        'exam_date' => $two_next_week_date->format('Y-m-d')
                    ],
                    ['id' => $schedule->id],
                    ['%s', '%s', '%s'], // Data types
                    ['%d'] // ID type
                );
            }
            $flag_three = true;
        }
    }

    if ($flag_three) {
        // Update two weeks ahead schedules

        foreach ($next_two_week_schedules as $schedule) {
            $three_next_week_date = new DateTime($schedule->exam_date);
            $three_next_week_date->modify('+1 week');

            // You can decide what to do for subject and content here
            $wpdb->update(
                'wp_exam_schedule',
                [
                    //    'subject' => '', // Set new subject if needed
                    'content' => '', // Set new content if needed
                    'exam_date' => $three_next_week_date->format('Y-m-d')
                ],
                ['id' => $schedule->id],
                ['%s', '%s', '%s'], // Data types
                ['%d'] // ID type
            );
        }
    }
}

// Schedule the function to run every Saturday
function schedule_update_exam_schedule()
{
    if (!wp_next_scheduled('update_exam_schedule_event')) {
        $next_saturday = strtotime('Saturday');
        wp_schedule_event($next_saturday, 'weekly', 'update_exam_schedule_event');
    }
}


add_action('wp', 'schedule_update_exam_schedule');
add_action('update_exam_schedule_event', 'update_exam_schedule');
