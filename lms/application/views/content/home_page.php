    <section id="main-slider" class="carousel">
        <div class="col-xs-10 col-xs-offset-1 " style="margin-top: -90px;">
            <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
            <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
            <?=(isset($message)) ? $message : ''; ?>
        </div>
        <div class="carousel-inner">
            <?php $i = 0;
            $sliders = $this->db->where('content_type', 'slider_text')->get('content')->result();
            foreach ($sliders as $slider) { $i++; ?>
            <div class="item <?=($i==1)?'active':'';?>">
                <div class="container">
                    <div class="carousel-content">
                        <h1><?=$slider->content_heading;?></h1>
                        <p class="lead"><?=$slider->content_data;?></p>
                    </div>
                </div>
            </div><!--/.item-->
            <?php } ?>
        </div><!--/.carousel-inner-->
        <?php if (!$this->session->userdata('log')): ?>
            <div class="container">
                <?php if ($student_can_register): ?>
                    <a href="#register" class="btn btn-primary btn-home-slider btn-lg register_open">Register</a>
                <?php endif; ?>
                <a href="#login" class="btn btn-primary btn-home-slider btn-lg login_open">Login</a>
	            <?php if ($teacher_can_register): ?>           
                <a href="#register_teacher" class="btn btn-success btn-home-slider btn-lg register_teacher_open">Become an Instructor</a>
            </div>
            <?php endif; ?>
        <?php endif; ?>
        <a class="prev" href="#main-slider" data-slide="prev"><i class="fa fa-angle-left"></i></a>
        <a class="next" href="#main-slider" data-slide="next"><i class="fa fa-angle-right"></i></a>
    </section><!--/#main-slider-->

    <section id="about-us">
    	<div class="homeaboutcontent">
         	<?php $temp = $this->db->get_where('content', array('content_type' => 'about_us'))->row(); ?>
            <h1><?=$temp->content_heading; ?></h1>
            <?=$temp->content_data; ?>
			<div class="homeaboutcontentarro">
            	<img src="assets/images/up-arrow.png">
            </div>
            <div class="homeaboutcontentbg">
              <div class="homeaboutcontentboxs container">
                <div class="col-md-4"> <img src="<?=base_url();?>assets/images/onlinevideo-img.png">
                  <h4>Oniline Video</h4>
                  <p>Explore over 45,000 courses taught <br>
                    by expert instructors</p>
                </div>
                <div class="col-md-4 boxmid"> <img src="<?=base_url();?>assets/images/easysearch-img.png">
                  <h4>Easy to Search</h4>
                  <p>Explore over 45,000 courses taught <br>
                    by expert instructors</p>
                </div>
                <div class="col-md-4"> <img src="<?=base_url();?>assets/images/digitallearning-img.png">
                  <h4>Digital Learning</h4>
                  <p>Explore over 45,000 courses taught <br>
                    by expert instructors</p>
                </div>
              </div>
              <div class="clear"></div>
            </div>
        </div>
    </section><!--/#services-->


    <?php if($show_latest_courses_on_homepage){ ?>
    <section id="latest-exams">
        <div class="container">
             
                <div class="row">
                     
                        <div class="homecoursedisplay">
                            <?php $courses = $this->db->where('active', 1)->order_by('course_id', 'DESC')->limit($number_of_latest_course)->get('courses')->result(); ?>
                            <!-- <i class="fa fa-apple fa fa-md fa fa-color1"></i> -->
                            <h1>Latest Courses</h1>
                            <div class="exam-list">
                                <?php
                                if (isset($courses) AND !empty($courses))
                                {  $i = 1;
                                    foreach ($courses as $course)
                                    { ?>
                                        <div class="col-md-3 col-xs-12 col-sm-6 exam-item">
                                            <div class="thumbnail">
                                                <!--
                                                <span style="position: absolute; top: 20px; left: 20px; font-weight: lighter;">
                                                        <?=$this->db->where('course_id', $course->course_id)->from('course_videos')->count_all_results(); ?> lessons
                                                </span>
                                                -->
                                                
                                                <span style="position: absolute; right: 15px; top: 40px; font-weight: lighter; font-size:18px;">
                                                    <?php if ($course->course_price) {
                                                        echo '<span class="label label-warning pull-right">'.$currency_symbol.$course->course_price.'</span>';
                                                    }else{
                                                        echo '<span class="label label-primary pull-right">Free</span>';
                                                    } ?>
                                                </span>

                                                <a href="<?php echo base_url('index.php/course/course_summary/'.$course->course_id); ?>">
                                                    <?php if (file_exists("course-images/$course->course_id.png")) { ?>
                                                        <img class="exam-thumbnail" src="<?=base_url("course-images/$course->course_id.png"); ?>" data-src="holder.js/300x300" alt="...">
                                                    <?php }else{ ?>
                                                        <img class="exam-thumbnail" src="<?=base_url('exam-images/placeholder.png'); ?>" data-src="holder.js/300x300" alt="...">
                                                    <?php } ?>

                                                    <span class="exam-title" style="text-align: left;"><?=$course->course_title;?></span>
                                                </a>&nbsp;
                                                <a href="<?php echo base_url('index.php/course/course_summary/'.$course->course_id); ?>" class="homecoursebutton">Start Now</a>
                                                <div class="clear"></div>
                                            </div>
                                            
                                        </div>
                                    <?php $i++;
                                    }
                                } else {
                                    echo '<p>No course available.</p>';
                                }
                                ?>
                                <div class="clear"></div>
                            </div> <!-- /exam-list -->

                        </div>
                    
                </div><!--/.row-->
             
        </div><!--/.container-->
    </section><!--/#services-->
    <?php } ?>

    <?php if($show_latest_exams_on_homepage){ ?>
    <section id="latest-exams">
        <div class="container">
            <div class="box first">
                <div class="row">
                    <div class="col-md-10 col-sm-12 col-md-offset-1 col-sm-offset-0">
                        <div class="center">
                            <?php $exams = $this->db->where('public', 1)->where('active', 1)->order_by('title_id', 'DESC')->limit($number_of_latest_exam)->get('exam_title')->result(); ?>
                            <!-- <i class="fa fa-apple fa fa-md fa fa-color1"></i> -->
                            <h1>Latest Exams</h1>

                            <div class="exam-list">
                                <?php
                                if (isset($exams) AND !empty($exams))
                                {  $i = 1;
                                    foreach ($exams as $exam)
                                    { ?>
                                        <div class="col-md-3 col-xs-12 col-sm-6 exam-item">
                                            <div class="thumbnail">
                                                <span style="position: absolute; top: 20px; left: 20px; font-weight: lighter;">
                                                </span>
                                                <span style="position: absolute; right: 20px; top: 20px; font-weight: lighter; font-size: 1.3em;">
                                                    <?php if ($exam->exam_price) {
                                                        echo '<span class="label label-warning pull-right">'.$currency_symbol.$exam->exam_price.'</span>';
                                                    }else{
                                                        echo '<span class="label label-primary pull-right">Free</span>';
                                                    } ?>
                                                </span>

                                                <a href="<?php echo base_url('index.php/exam_control/view_exam_summary/'.$exam->title_id); ?>">
                                                    <?php if (file_exists("exam-images/$exam->title_id.png")) { ?>
                                                        <img class="exam-thumbnail" src="<?=base_url("exam-images/$exam->title_id.png"); ?>">
                                                    <?php }else{ ?>
                                                        <img class="exam-thumbnail" src="<?=base_url('exam-images/placeholder.png'); ?>">
                                                    <?php } ?>

                                                    <span class="exam-title" style="text-align: left; margin: 10px;"><?=$exam->title_name;?></span>
                                                </a>
                                            </div>
                                        </div>
                                    <?php $i++;
                                    }
                                } else {
                                    echo '<p>No exam available.</p>';
                                }
                                ?>
                            </div> <!-- /exam-list -->

                        </div>
                    </div><!--/.col-md-4-->
                </div><!--/.row-->
            </div><!--/.box-->
        </div><!--/.container-->
    </section><!--/#services-->
    <?php } ?>