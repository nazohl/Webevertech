<?php    date_default_timezone_set($local_time_zone);
    // Create the data table for Revenue.
    $data_revenue = "";
    for ($i=0; $i < 6; $i++)
    {
        $month_name = date('M', strtotime(-$i." month"));
        $month = date('m', strtotime(-$i." month"));

        $earned = $this->db->where("MONTH(pay_date)", $month)
                        ->select_sum('pay_amount')
                        ->get('payment_history')
                        ->row()->pay_amount;

         $earned = ($earned)?$earned:'0';

         $data_revenue .= "['".$month_name."',". $earned."],";
    }
    $data_revenue = substr($data_revenue, 0, -1);


    // Create the data table for Subscriptions.
    $plans = $this->db->select('price_table_id,price_table_title')->get('price_table')->result();

    $data_subscriptions = "";
    foreach ($plans as $plan)
    {
        $subscribers = $this->db->where('active', 1)->where('subscription_id', $plan->price_table_id)->where('subscription_end >', date('Y-m-d'))->get('users')->num_rows();

         $data_subscriptions .= "['". $plan->price_table_title ."',". $subscribers."],";
    }
    $data_subscriptions = substr($data_subscriptions, 0, -1);

    // Create the data table for Category Based Courses and Exams.
    $categories = $this->db->select('category_id, category_name')->get('categories')->result();

    $data_categoryBasedCourses = "";
    foreach ($categories as $category)
    {
        $courses = 0;
        $exams = 0;

        $sub_categories = $this->db->select('id')->where('cat_id', $category->category_id)->get('sub_categories')->result_array();
        $sub_category_ids = array_column($sub_categories, 'id');
        $sub_category_ids = $sub_category_ids?:0;

        $courses += $this->db->where_in('category_id', $sub_category_ids)->get('courses')->num_rows();
        $exams += $this->db->where_in('category_id', $sub_category_ids)->get('exam_title')->num_rows();

        if($courses || $exams)
        {
            $data_categoryBasedCourses .= "['".$category->category_name."',". $courses.",". $exams."],";
        }
    }
    $data_categoryBasedCourses = substr($data_categoryBasedCourses, 0, -1);
?>

<script type="text/javascript">
    // Load Charts and the corechart package.
    google.charts.load('current', {'packages':['corechart']});

    // Draw the pie chart for Active Subscribers Chart when Charts is loaded.
    google.charts.setOnLoadCallback(drawSubscribersChart);

    // Draw the pie chart for the Revenue Chart when Charts is loaded.
    google.charts.setOnLoadCallback(drawRevenueChart);

    // Draw the pie chart for the Category Based Courses and Exams Chart when Charts is loaded.
    google.charts.setOnLoadCallback(drawCategoryBasedCourses);

    // Callback that draws the pie chart for Active Subscribers.
    function drawSubscribersChart()
    {
        var subscriptionData = new google.visualization.DataTable();
        subscriptionData.addColumn('string', 'Subscription');
        subscriptionData.addColumn('number', 'Subscribers');
        subscriptionData.addRows([
             <?=$data_subscriptions?>
        ]);

        var options = {
            // pieHole: 0.35,
            pieSliceText: 'label',
            pieStartAngle: 20,
            legend: 'none',
        };

        var chart = new google.visualization.PieChart(document.getElementById('subscribersChart'));
        chart.draw(subscriptionData, options);
    }

    // Callback that draws the pie chart for Revenue.
    function drawRevenueChart()
    {
        var revenueData = new google.visualization.DataTable();
        revenueData.addColumn('string', 'Month');
        revenueData.addColumn('number', 'Revenue');
        revenueData.addRows([
            <?=$data_revenue;?>
        ]);

        var barchart_options = {
            animation:{
                startup: true,
                duration: 2000,
                easing: 'out',
              },
           legend: 'none',
       };

        var barchart = new google.visualization.BarChart(document.getElementById('revenueChart'));
        barchart.draw(revenueData, barchart_options);
    }

    // Callback that draws the pie chart for Category Based Courses and Exams.
    function drawCategoryBasedCourses()
    {
        var categoryBasedCourseData = new google.visualization.DataTable();
        categoryBasedCourseData.addColumn('string', 'Category');
        categoryBasedCourseData.addColumn('number', 'Courses');
        categoryBasedCourseData.addColumn('number', 'Exams');
        categoryBasedCourseData.addRows([
            <?=$data_categoryBasedCourses;?>
        ]);

        var categoryBasedCourse_options = {
            // height: 400,
            bars: 'vertical',
            legend: { position: "bottom" },
            animation:{
                startup: true,
                duration: 2000,
                easing: 'out',
              },
        };

        var categoryBasedCoursesChart = new google.visualization.ColumnChart(document.getElementById('categoryBasedCourses'));
        categoryBasedCoursesChart.draw(categoryBasedCourseData, categoryBasedCourse_options);
    }
</script>


<div id="note">
    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
</div>

<div class="row">
    <div class="col-lg-3 col-md-6">
        <a href="<?= base_url('index.php/message_control'); ?>">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <i class="fa fa-envelope-o fa-5x"></i>
                        </div>
                        <div class="col-xs-6 text-center">
                            <p class="dashboard-heading"><?=$this->db->where('message_read', 0)->get('messages')->num_rows(); ?></p>
                        </div>
                        <div class="col-xs-12">
                            <p class="dashboard-text text-center">Unread Messages</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-6">
        <a href="<?= base_url('index.php/user_control'); ?>">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <i class="fa fa-group fa-5x"></i>
                        </div>
                        <div class="col-xs-6 text-center">
                            <p class="dashboard-heading"><?=$this->db->where('user_role_id', 5)->get('users')->num_rows(); ?></p>
                        </div>
                        <div class="col-xs-12">
                            <p class="dashboard-text text-center">Total Students</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-6">
        <a href="<?= base_url('index.php/mocks'); ?>">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <i class="fa fa-puzzle-piece fa-5x"></i>
                        </div>
                        <div class="col-xs-6 text-center">
                            <p class="dashboard-heading"><?=$this->db->get('exam_title')->num_rows(); ?></p>
                        </div>
                        <div class="col-xs-12">
                            <p class="dashboard-text text-center">Total Exams</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
    <div class="col-lg-3 col-md-6">
        <a href="<?= base_url('index.php/exam_control/view_results'); ?>">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-xs-6">
                            <i class="fa fa-book fa-5x"></i>
                        </div>
                        <div class="col-xs-6 text-center">
                            <p class="dashboard-heading"><?=$this->db->get('courses')->num_rows(); ?></p>
                        </div>
                        <div class="col-xs-12">
                            <p class="dashboard-text text-center">Total Courses</p>
                        </div>
                    </div>
                </div>
            </div>
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                Last Six Month's Revenue in <?=$currency_code; ?>
            </div>
            <div class="panel-body">
                <div id="revenueChart"></div>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                Active Subscribers
            </div>
            <div class="panel-body">
                <div id="subscribersChart"></div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                Latest Courses
            </div>
            <div class="panel-body">
                <table class="table table-striped">
                    <?php
                    $latestCourses = $this->db->where('active', 1)->order_by('course_id', 'DESC')->limit(5)->get('courses')->result();
                    foreach ($latestCourses as $course)
                    { ?>
                        <tr>
                            <td>
                                <a href="<?=base_url('index.php/course/course_summary/'.$course->course_id)?>">
                                    <?=$course->course_title?>
                                </a>
                            </td>
                            <td><?=$course->course_price ? $currency_symbol . $course->course_price : 'Free'; ?></td>
                        </tr>
                    <?php
                    } ?>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="panel panel-default">
            <div class="panel-heading">
                Latest Exams
            </div>
            <div class="panel-body">
                <table class="table table-striped">
                    <?php
                    $latestExams = $this->db->where('active', 1)->order_by('title_id', 'DESC')->limit(5)->get('exam_title')->result();
                    foreach ($latestExams as $exam)
                    { ?>
                        <tr>
                            <td>
                                <a href="<?=base_url('index.php/exam_control/view_exam_summary/'.$exam->title_id)?>">
                                    <?=$exam->title_name?>
                                </a>
                            </td>
                            <td><?=$exam->exam_price ? $currency_symbol . $exam->exam_price : 'Free'; ?></td>
                        </tr>
                    <?php
                    } ?>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                Courses and Exams Based on Categories
            </div>
            <div class="panel-body">
                <div id="categoryBasedCourses"></div>
            </div>
        </div>
    </div>
</div>
