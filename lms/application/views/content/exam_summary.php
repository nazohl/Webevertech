<section id="internal-slider" class="carousel">
    <div class="container">
    	<h1><?=$mock->title_name?></h1>
    </div>
</section>

<div class="container">
	<div class="coursedetailsec">
    	
        <!-- Course Image -->
        <div class="col-md-3 coursedetailsecimg">
       		<?php if (file_exists("exam-images/$mock->title_id.png")) { ?>
            <img class="course-summary-thumbnail" width="250" src="<?=base_url("exam-images/$mock->title_id.png"); ?>">
            <?php }else{ ?>
            <img class="course-summary-thumbnail" width="250" src="<?=base_url('exam-images/placeholder.png'); ?>">
            <?php } ?>
        </div>

        <!-- Course Title and Category -->
        <div class="col-md-7 coursedetailsecleft">
            	<h1><?=$mock->category_name.' / '.$mock->sub_cat_name; ?></h1>
                <h3>Instructor: 
				<?php if (file_exists("user-avatar/$mock->user_id.png")) { ?>
                <img align="absmiddle" vspace="5" width="20px" height="20px" src="<?= base_url("user-avatar/$course->created_by.png"); ?>">
                <?php }else{ ?>
                <img align="absmiddle" vspace="5" width="20px" height="20px" src="<?= base_url('user-avatar/avatar-placeholder.png'); ?>">
                <?php } ?>
                <span class="courseinstname"><?=$mock->user_name?></span>
                </h3>
        	<div class="clear"></div>
        	<div class="coursedetailcategory">
                <span>Category:</span>
                <span><?=$mock->category_name.' / '.$mock->sub_cat_name; ?></span>
            </div>
        </div>
        <div class="col-md-2 coursedetailprice">
        		<div class="pb-t">
                        <div class="pb-p">
                            <span class="pb-pr ">
                                <?php if ($mock->exam_price) {
                                    echo $currency_symbol.$mock->exam_price;
                                }else{
                                    echo "Free";
                                } ?>
                            </span>
                        </div>
                </div>
        </div>
        <div class="clear"></div>
    </div>    
</div>


<section id="exam_summary">
    <div class="container">
        <div class="coursedetailssection">
            <div class="row">
                <div class="col-xs-10 col-xs-offset-1">
                    <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
                    <?=(isset($message)) ? $message : ''; ?>
                </div>
                 
                 
                <div class="col-md-9">
                    <ol class="breadcrumb hidden-print">
                        <?php if ($this->session->userdata('log')) { ?>
                            <li><a href="<?= base_url('index.php/dashboard/' . $this->session->userdata('user_id')); ?>"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                        <?php } ?>
                        <li><a href="<?= base_url('index.php/exam_control/view_all_mocks') ?>"><i class="fa fa-puzzle-piece"></i> Exams</a></li>
                        <li class="active">Exam Summery</li>
                    </ol>
                    

                    <h4>Passing Score:</h4>
                    <p><?= $mock->pass_mark ?>%</p>

                    <h4>Duration:</h4>
                    <p><?=$mock->time_duration;?><span class="text-muted"> hours</span></p>

                    <h4>Total Questions:</h4>
                    <p>
                        <?=($mock->random_ques_no != 0) ? $mock->random_ques_no : $this->db->where('exam_id', $mock->title_id)->get('questions')->num_rows();?>
                    </p>

                    <h4>syllabus:</h4>
                    <p><?=$mock->syllabus; ?></p>

                    <div class="big-gap"></div>

                </div>
                <div class="col-md-3">
                    
 

                    <a href="<?=base_url('index.php/exam_control/proceed/'.$mock->title_id);?>" class="btn btn-info btn-block"> Take the Exam </a>

                    <div class="big-gap"></div>

                    <div class="fb-share-button"
                        data-href="<?=base_url('index.php/exam_control/view_exam_summary/'.$mock->title_id)?>"
                        data-layout="button_count" data-size="large" >
                    </div>

                    <div>
                        <h4 class="related_courses">Related courses: </h4>
                        <hr />
                    </div>
                        <?php
                            $related_exams = $this->db->where('category_id', $mock->category_id)->where('active', 1)->where('public', 1)->get('exam_title')->result();
                            foreach ($related_exams as $value) {
                                if ($value->title_id != $mock->title_id) { ?>
                                    <div class="thumbnail relatedcourses">
                                    <a href="<?=base_url('index.php/exam_control/view_exam_summary/'.$value->title_id); ?>">

                                        <?php if (file_exists("exam-images/$value->title_id.png")) { ?>
                                            <img class="course-summary-thumbnail" width="250" src="<?=base_url("exam-images/$value->title_id.png"); ?>">
                                        <?php }else{ ?>
                                            <img class="course-summary-thumbnail" width="250" src="<?=base_url('exam-images/placeholder.png'); ?>">
                                        <?php } ?>

                                        <div class="caption">
                                            <h4><?=$value->title_name;?></h4>
                                        </div>
                                    </a>
                                    </div>
                        <?php   }
                            } ?>
                </div>

            </div>
            <div class="big-gap"><br/></div>
            <p class="result-note"><strong>Note: </strong>The value of this exam certificate is only valid under the terms and conditions of <?= $brand_name ?>.</p>
        </div>
    </div>
</section><!--/#pricing-->
