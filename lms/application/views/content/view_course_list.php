<section id="internal-slider" class="carousel">
  <div class="container">
    <h1>
      <?=isset($category_name)?$category_name:'Courses'; ?>
    </h1>
  </div>
</section>
<section id="exams">
  <div class="coursedropdownbar">
    <div class="container">
      <div class="coursedropbox">
        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 nopadding">
          <ul class="nav  category-menu" style="float:left;">
            <li class="dropdown"> <a href="#" class="dropdown-toggle " data-toggle="dropdown"><i class=" fa fa-sitemap"></i> &nbsp;All Categories <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <?php
                            foreach ($categories as $value) {
                                $sub = $this->db->get_where('sub_categories', array('cat_id' => $value->category_id))->result();
                                if(!empty($sub)){ ?>
                <li class="dropdown-submenu"> <a href="#" tabindex="-1" class="dropdown-toggle" data-toggle="dropdown">
                  <?=$value->category_name; ?>
                  </a>
                  <ul class="dropdown-menu">
                    <h3>
                      <?=$value->category_name; ?>
                    </h3>
                    <?php foreach ($sub as $sub_cat) { ?>
                    <li> <a href="<?=base_url('index.php/course/view_course_by_category/'.$sub_cat->id); ?>">
                      <?=$sub_cat->sub_cat_name; ?>
                      </a> </li>
                    <?php } ?>
                  </ul>
                </li>
                <?php
                                }
                            } ?>
              </ul>
            </li>
          </ul>
        </div>
        <!--/.col-md-2-->
      </div>
      <div class="courseshortlist">
        <?php if ($commercial) { ?>
        <div class=" pull-right"> <a href="<?=base_url('index.php/course/index') ?>">All</a> <a href="<?=base_url('index.php/course/courses_type/paid') ?>">Paid</a> <a href="<?=base_url('index.php/course/courses_type/free') ?>">Free</a> </div>
        <?php } ?>
      </div>
    </div>
  </div>
  <div class="container">
    <div>
      <div class="row">
        <div class="col-xs-10 col-xs-offset-1">
          <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
          <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
          <?=(isset($message)) ? $message : ''; ?>
        </div>
        <div class="col-lg-12 col-md-9 col-sm-12 col-xs-12 nopadding">
          <h4>
            <?=isset($category_name)?$category_name:'All Courses'; ?>
          </h4>
          <?php if ($commercial) { ?>
          <?php } ?>
          <div class="exam-list">
            <?php
                        if (isset($courses) AND !empty($courses)) {  $i = 1;
                            foreach ($courses as $course) {
                                if ($course->active == 1) {
                                ?>
            <div class="col-lg-3 col-md-4 col-xs-12 col-sm-6 exam-item">
              <div class="thumbnail"> 
              	
                <span style="position: absolute; right: 15px; top: 40px; font-weight: lighter; font-size:18px;">
                <?php if ($course->course_price) {
                                                    echo '<span class="label label-warning pull-right">'.$currency_symbol.$course->course_price.'</span>';
                                                }else{
                                                    echo '<span class="label label-primary pull-right">Free</span>';
                                                } ?>
                </span> 
                <a href="<?php echo base_url( $this->session->userdata('log') ? 'index.php/course/course_summary/'.$course->course_id : '#login'); ?> " class="login_open">
                <?php if (file_exists("course-images/$course->course_id.png")) { ?>
                <img class="exam-thumbnail" src="<?=base_url("course-images/$course->course_id.png"); ?>" data-src="holder.js/300x300" alt="...">
                <?php }else{ ?>
                <img class="exam-thumbnail" src="<?=base_url('exam-images/placeholder.png'); ?>" data-src="holder.js/300x300" alt="...">
                <?php } ?>
				        </a> 
                <div class="categoryandls">
                <strong>Category</strong>: <?=$course->category_name.'/'.$course->sub_cat_name;?><br />
                <strong>Lessons</strong>:&nbsp;&nbsp; <?=$this->db->where('course_id', $course->course_id)->from('course_videos')->count_all_results(); ?>
                <div class="clear"></div>
                </div>
                <div class="caption"> 
                 <span class="exam-title">
                  	 <a href="<?php echo base_url( $this->session->userdata('log') ? 'index.php/course/course_summary/'.$course->course_id : '#login'); ?> " class="login_open">
					<?=$course->course_title;?>
                    </a>
                  </span>
                
                 
                  <a href="<?php echo base_url( $this->session->userdata('log') ? 'index.php/course/course_summary/'.$course->course_id : '#login'); ?>" class="homecoursebutton login_open">Start Now</a>
                  <div class="fb-share-button" data-href="<?php echo base_url( $this->session->userdata('log') ? 'index.php/course/course_summary/'.$course->course_id : '#login'); ?>" data-layout="button" > </div>
                  <div class="clear"></div>
              
              
                </div>
                
                
                
                 
                </div>
            </div>
            <?php $i++;
                                }
                            }
                        } else {
                            echo '<h4>No course found!</h4>';
                        }
                        ?>
          </div>
          <!-- /exam-list -->
        </div>
        <!--/.col-md-10-->
      </div>
      <!--/.row-->
    </div>
    <!--/.box-->
  </div>
  <!--/.container-->
</section>
<!--/#emaxs-->
