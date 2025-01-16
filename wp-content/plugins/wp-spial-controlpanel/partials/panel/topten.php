

<div class="row mt-3">
    <div class="col-sm-6 col-md-4">
        <?php
        global $wpdb;
        $meta_key = '_score_points';
        $top_scores = $wpdb->get_col($wpdb->prepare(
            "
    SELECT DISTINCT meta_value
     FROM PN_usermeta 
     WHERE meta_key = %s 
     ORDER BY CAST(meta_value AS UNSIGNED) DESC
      LIMIT 10",
            $meta_key
        ));
        if (!empty($top_scores)) {
            $meta_query = array(
                'relation' => 'OR',
            );
            foreach ($top_scores as $score) {
                $meta_query[] = array(
                    'key' => $meta_key,
                    'value' => $score,
                    'compare' => '='
                );
            }
            $args = array(
                'role' => 'subscriber',
                'orderby' => 'meta_value_num',
                'order' => 'DESC',
                'meta_query' => $meta_query
            );
            $user_query = new WP_User_Query($args);
            $subscribers = $user_query->get_results();
            if (!empty($subscribers)) {
        ?>
                <div class="bg-white p-2 rounded mt-2 mx-2">
                    <div class="fs-5 p-2 position-relative">
                        ده امتیاز برتر مدرسه
                        <img class="position-absolute top-ten-img" src="<?php echo get_template_directory_uri() . '/assets/images/TopTen.png' ?>" alt="topten">
                    </div>
                    <div class="table-responsive-sm shadow-sm">
                        <table class="table table-hover table-borderless table-success align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>رتبه</th>
                                    <th>نام و نام خانوادگی</th>
                                    <th>امتیاز</th>
                                </tr>
                            </thead>
                            <tbody class="table-group-divider">
                                <?php
                                $i = 1;
                                foreach ($subscribers as $user) {
                                    if ($i === 11) {
                                        break;
                                    }
                                    $score = get_user_meta($user->ID, '_score_points', true);
                                ?>
                                    <tr class="table-success">
                                        <td scope="row"><?php echo $i ?></td>
                                        <td><?php echo esc_html($user->display_name) ?></td>
                                        <td><?php echo esc_html($score) ?></td>
                                    </tr>
                                <?php
                                    $i++;
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php
            } else {
            ?>
                <div class="alert alert-info">
                    هیچ دانش آموزی برای لیست 10 نمره برتر مدرسه یافت نشد
                </div>
            <?php
            }
        } else {
            ?>
            <div class="alert alert-info">
                10 نمره برتر مدرسه یافت نشد
            </div>

        <?php
        } ?>
    </div>
    <?php

    // Get current user classroom id 
    $classrooms_id = get_user_meta($current_user_id, '_classroom_id', true);
    $i = 0;
    // Get classmates id 
    $All_classmates_id = [];
    if ($classrooms_id !== '') {
        $classrooms_id = array_values(json_decode($classrooms_id, true));
        foreach ($classrooms_id as $classroom_id) {
            $students_id_classroom = $wpdb->get_var($wpdb->prepare("SELECT students_id FROM classrooms WHERE id=%d", $classroom_id));
            $classmates_id = json_decode($students_id_classroom, true);
            $All_classmates_id[$classroom_id] = $classmates_id;
        }
    }
    foreach ($All_classmates_id as $classroom_id => $classmates_id) {
        // Classroom name
        $classroom_name = $wpdb->get_var($wpdb->prepare("SELECT classroom_name FROM classrooms WHERE id=%d", $classroom_id));
        $users_score = [];
        foreach ($classmates_id as $user_id) {
            $scores = get_user_meta($user_id, '_score_points', true);
            $users_score[$user_id] = $scores;
        }
        arsort($users_score);
    ?>
        <div class="col-sm-6 col-md-4">
            <div class="bg-white p-2 rounded mt-2 mx-2">
                <div class="fs-5 p-2 position-relative text-center">
                    ده امتیاز برتر کلاس
                    <br>
                    <span class="text-success"><?php echo esc_html($classroom_name) ?></span>
                    <img class="position-absolute top-ten-img" src="<?php echo get_template_directory_uri() . '/assets/images/TopTen.png' ?>" alt="topten">
                </div>
                <div class="table-responsive-sm shadow-sm">
                    <table class="table table-hover table-borderless table-success align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>رتبه</th>
                                <th>نام و نام خانوادگی</th>
                                <th>امتیاز</th>
                            </tr>
                        </thead>
                        <tbody class="table-group-divider">
                            <?php
                            $i = 1;
                            foreach ($users_score as $user_id => $score) {
                                if ($i === 11) {
                                    break;
                                }
                                $user = get_userdata($user_id);
                            ?>
                                <tr class="table-success">
                                    <td scope="row"><?php echo $i ?></td>
                                    <td><?php echo esc_html($user->display_name) ?></td>
                                    <td><?php echo esc_html($score) ?></td>
                                </tr>
                            <?php
                                $i++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
    }
