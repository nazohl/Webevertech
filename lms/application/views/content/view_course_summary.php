<style>
    .parentplayerppt {
        display:table;
        position:relative;
        margin:0 auto;
        left:-1px;
        width: 600px;
        height: 335px;
    }
    iframe + iframe {
        position:absolute;
        left:0;
        top:0;
        right:0;
        bottom:-10;
        margin:auto;
    }
    .playerppt{
        position: absolute;
        width: 100px;
        bottom: 1px;
        background-color: #444444;
        height: 22px;
        right: 0px;
        z-index: 1;
    }
</style>
<?php
date_default_timezone_set($local_time_zone);
$user_info = $this->db->get_where('users', array('user_id' => $this->session->userdata('user_id')))->row();
$purchased = $this->db->where('user_id', $this->session->userdata('user_id'))->where('pur_ref_id', $course->course_id)->get('puchase_history')->row();
$subscription_end = $user_info->subscription_end != "" ? $user_info->subscription_end : "1971-1-1";
?>
<section id="internal-slider" class="carousel">
    <div class="container">
    	<h1><?= $course->course_title ?></h1>
    </div>
</section>

<div class="container">
	<div class="coursedetailsec">
    	
        <!-- Course Image -->
        <div class="col-md-3 coursedetailsecimg">
        	<?php if (file_exists("course-images/$course->course_id.png")) { ?>
			<img class="course-summary-thumbnail" width="250" src="<?= base_url("course-images/$course->course_id.png"); ?>" alt="...">
            <?php } else { ?>
            <img class="course-summary-thumbnail" width="250" src="<?= base_url('course-images/placeholder.png'); ?>" alt="...">
            <?php } ?>
        </div>

        <!-- Course Title and Category -->
        <div class="col-md-7 coursedetailsecleft">
            	<h1><?= $course->course_title ?></h1>
                <h2><?= $course->course_intro; ?></h2>
                <h3>Instructor: 
                <?php if (file_exists("user-avatar/$course->created_by.png")) { ?>
                <img align="absmiddle" vspace="5" width="20px" height="20px" src="<?= base_url("user-avatar/$course->created_by.png"); ?>">
                <?php } else { ?>
                <img align="absmiddle" vspace="5" width="20px" height="20px" src="<?= base_url('user-avatar/avatar-placeholder.png'); ?>">
                <?php } ?>
                <span class="courseinstname"><?= $course->user_name ?></span>
                </h3>
        	<div class="clear"></div>
        	<div class="coursedetailcategory">
                <span>Category:</span>
                <span><?= $course->category_name . ' / ' . $course->sub_cat_name; ?></span>
                
                	
                
            </div>
        </div>
        <div class="col-md-2 coursedetailprice">
        		<div class="pb-t">
                        <div class="pb-p">
                            <span class="pb-pr ">
                                <?php
                                if ($course->course_price) {
                                    echo $currency_symbol . $course->course_price;
                                } else {
                                    echo "Free";
                                }
                                ?>
                            </span>
                        </div>
                        <div class="pb-ta">
                            <?php
                            if ($course->course_price) {
                                if (!$purchased && $subscription_end < date('Y-m-d')) {
                                    ?>
                                    <a href="<?= base_url('index.php/course/enroll/' . $course->course_id) ?>" class="btn btn-success"> Enroll Now </a>
                                    <?php
                                }
                            }
                            ?>
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
                    <?= validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                    <?= ($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
                    <?= (isset($message)) ? $message : ''; ?>
                </div>
                
                <div class="col-md-9">
                    <!--<p>
                        <img src="<?= base_url('course-images/CPkGq6G.png') ?>"/>
                        <img src="<?= base_url('course-images/CPkGq6G.png') ?>"/>
                        <img src="<?= base_url('course-images/CPkGq6G.png') ?>"/>
                        <img src="<?= base_url('course-images/CPkGq6G.png') ?>"/>
                        <img src="<?= base_url('course-images/CPkGq6G.png') ?>"/>
                    <?= $course->course_count_reviews; ?> Reviews
                    </p><hr/>
                    -->
                    <p><?= $course->course_description; ?></p>
                    <div>
                        <h4>Course requirement</h4>
                        <div><?= $course->course_requirement; ?></div>
                        <h4>What am I going to get from this course?</h4>
                        <div><?= $course->what_i_get; ?></div>
                        <h4>What is the target audience?</h4>
                        <div><?= $course->target_audience; ?></div>
                    </div>
                    <br/>
                    <div id="lessons">
                        <h4>Lessons</h4>
                        <div class="course-video-container">
                            <ul>
                                <?php
                                $videos_status = $this->db->order_by('created', 'desc')
                                            ->from('user_course_status')
                                            ->where(array('course_id' => $course->course_id, 'user_id' => $this->session->userdata('user_id')))
                                            ->get()
                                            ->result();
      
                                $i = 1;
                                $cnt = 1;
                                $sections = $this->db->get_where('course_sections', array('course_id' => $course->course_id))->result();
                                foreach ($sections as $value) {
                                    $j = 1;
                                    
                                    $videos = $this->db->where('section_id', $value->section_id)->order_by('orderList', 'asc')
                                            ->get('course_videos')
                                            ->result();
                                    if(count($videos) == 0) 
                                        continue;
                                    ?>
                                    <li class="chap-title"><b> <?= $value->section_name ?> : </b> <h5><?= ' ' . $value->section_title; ?> </h5></li>
                                    <?php
                                    if ($videos) {
                                        foreach ($videos as $video) {
                                            if(empty($videos_status[0]->section_id)) {
                                                $last_i =  1;
                                                $last_j = 1;
                                            } else {
                                                $last_i = $i;
                                                $last_j = count($videos_status) + 1;
                                            }

                                            //if((($videos_status->video_id ==  $video->video_id) && ($videos_status->course_id ==  $video->course_id) && ($videos_status->section_id ==  $video->section_id)) || (($videos_status->video_id !=  $video->video_id) || ($videos_status->course_id !=  $video->course_id) || ($videos_status->section_id !=  $video->section_id) && $flag == 1)) {
                                            /*if(($videos_status->video_id ==  $video->video_id) && ($videos_status->course_id ==  $video->course_id) && ($videos_status->section_id ==  $video->section_id))  {
                                                $sequence = $j;
                                            } */
                                            $date = @date_create($video->created_at);
                                          
                                            ?>
                                            
                                            <li class="lec">
                                                <div class="lec-left">
                                                    <span class="course-no"><?= $i . '.' . $j; ?> </span>
                                                </div>
                                                <div class="lec-right">
                                                    <div class="lec-url">
                                                        <div class="lec-main fxac">
                                                            <div class="lec-title">
                                                                <?php
                                                                if (
                                                                        ($video->preview_type == 'free') ||
                                                                        (!$course->course_price) ||
                                                                        $purchased ||
                                                                        (
                                                                        ($this->session->userdata('log')) &&
                                                                        ($user_info->subscription_id) &&
                                                                        ($subscription_end > date('Y-m-d'))
                                                                        )
                                                                ) {
                                                                    if ($video->content_type == 'external_link') {
                                                                        ?>

                                                                        <i class="glyphicon glyphicon-link"></i> 
                                                                        <?php if ($i  <= $last_i && $cnt <= $last_j) { ?>
                                                                            <a href="<?= $video->youtube_link; ?>" target="_blank"><?= $video->video_title; ?></a>
                                                                        <?php } else {?>
                                                                            <?= $video->video_title; ?>
                                                                        <?php }?>

                                                                        <?php if (($video->preview_type == 'free') || ($video->preview_type == 'Free') || !$course->course_price) { ?>
                                                                        <span class="label label-default pull-right">Free</span>
                                                                        <?php } ?>

                                                                        <span class="help-block small"><?= 'Type: '; ?> <?php $splits = explode('.', $video->video_link); ?> <?= end($splits)? : str_replace('_', ' ', $video->content_type); ?> <?= ' | Added: ' . date_format($date, 'M d, Y'); ?> </span>
                                                                        
                                                                    <?php } else if ($video->content_type == 'youtube') { ?>

                                                                        <i class="glyphicon glyphicon-expand"></i> 
                                                                        <?php if ($i  <= $last_i && $cnt <= $last_j) { ?>
                                                                            <?php
                                                                            $key = "JumriTaliya";
                                                                            $token = $video->video_id."#".$key.'#'.session_id();
                                                                            #$token = $key;
                                                                            $this->encryption->initialize(array(
                                                                                    'driver' => 'mcrypt',
                                                                                    'cipher' => 'aes-256',
                                                                                    'mode' => 'ctr',
                                                                                    'key' => $token
                                                                            ));
                                                                            $encrypt_link  = $this->encryption->encrypt($video->youtube_link);
                                                                            $link = base_url('index.php/course/view_link/' . base64_encode($encrypt_link).'/'.$video->video_id); ?>
                                                                            <a class="videoplaylink btnfileModal" data-toggle="modal" data-target="#fileModal" data-video-url="<?= $link; ?>"><?= $video->video_title; ?></a>
                                                                        <?php } else {?>
                                                                            <?= $video->video_title; ?>
                                                                        <?php }?>
                                                                        
                                                                        <?php if (($video->preview_type == 'free') || ($video->preview_type == 'Free') || !$course->course_price) { ?>
                                                                            <span class="label label-default pull-right">Free</span>
                                                                        <?php } ?>

                                                                        <span class="help-block small"><?= 'Type: '; ?> <?php $splits = explode('.', $video->video_link); ?> <?= end($splits)? : str_replace('_', ' ', $video->content_type); ?> <?= ' | Added: ' . date_format($date, 'M d, Y'); ?> </span>

                                                                        <?php
                                                                    } else if ($video->content_type == 'file') {
                                                                        ?>
                                                                        <i class="glyphicon glyphicon-download"></i>
                                                                        <?php if ($i  <= $last_i && $cnt <= $last_j) { ?>
                                                                        <a class="videoplaylink btnfileModal" data-toggle="modal" data-target="#fileModal" data-video-url="<?= base_url('course_videos/' . $video->course_id . '/' . $video->video_link); ?>"  download><?= $video->video_title; ?></a>
                                                                        <?php } else {?>
                                                                        <?= $video->video_title; ?>
                                                                        <?php }?>
                                                                        <?php if (($video->preview_type == 'free') || ($video->preview_type == 'Free') || !$course->course_price) { ?>
                                                                            <span class="label label-default pull-right">Free</span>
                                                                        <?php } ?>
                                                                        <span class="help-block small">Size: <?= number_format($video->file_size / 1000000, 2) . 'MB | Type: '; ?> <?php $splits = explode('.', $video->video_link); ?> <?= end($splits)? : str_replace('_', ' ', $video->content_type); ?> <?= ' | Added: ' . date_format($date, 'M d, Y'); ?> </span>


                                                                    <?php } else if ($video->content_type == 'exam') { ?>
                                                                        
                                                                        <?php $exam_id = $this->db->get_where('section_exams', array('section_id' => $value->section_id))->row()->exam_id; ?>
                                                                        <i class="glyphicon glyphicon-expand"></i>
                                                                        <?php if ($i  <= $last_i && $cnt <= $last_j) { ?>
                                                                        <a href="<?= base_url('index.php/exam_control/view_exam_summary/' . $exam_id); ?>" target="new" class="videoplaylink" data-toggle="modal"><?= $video->video_title; ?></a>
                                                                        <?php } else {?>
                                                                        <?= $video->video_title; ?>
                                                                        <?php }?>    
                                                                        
                                                                        <?php if (($video->preview_type == 'free') || ($video->preview_type == 'Free') || !$course->course_price) { ?>
                                                                        <span class="label label-default pull-right">Free</span>
                                                                        <?php } ?>
                                                                        <span class="help-block small"><?= 'Type: '; ?> <?php $splits = explode('.', $video->video_link); ?> <?= end($splits)? : str_replace('_', ' ', $video->content_type); ?> <?= ' | Added: ' . date_format($date, 'M d, Y'); ?> </span>
                                                                        
                                                                    <?php } else if ($video->content_type == 'live') { ?>
                                                                        <i class="glyphicon glyphicon-expand"></i>
                                                                        <?php if ($i  <= $last_i && $cnt <= $last_j) { ?>
                                                                           <a href="" class="videoplaylink" data-toggle="modal" data-target="#videoModal" data-video-url="<?= base_url('course_videos/' . $video->course_id . '/' . $video->video_link); ?>"><?= $video->video_title ?></a>
                                                                        <?php } else {?>
                                                                            <?= $video->video_title; ?>
                                                                        <?php }?> 
                                                                        <?php if (($video->preview_type == 'free') || ($video->preview_type == 'Free') || !$course->course_price) { ?>
                                                                            <span class="label label-default pull-right">Free</span>
                                                                        <?php } ?>
                                                                        <span class="help-block small"><?= 'Time: '; ?><?= date("M d, Y h:i A", strtotime($video->video_link)) ?><?= ' | Added: ' . date_format($date, 'M d, Y'); ?> </span>
                                                                    <?php } else { ?>
                                                                        <i class="glyphicon glyphicon-expand"></i>
                                                                        <?php if ($i  <= $last_i && $cnt <= $last_j) { ?>
                                                                            <a href="" class="videoplaylink" data-toggle="modal" data-target="#videoModal" data-video-url="<?= base_url('course_videos/' . $video->course_id . '/' . $video->video_link); ?>"><?= $video->video_title; ?></a>
                                                                         <?php } else {?>
                                                                            <?= $video->video_title; ?>
                                                                        <?php }?>        
                                                                        <?php if (($video->preview_type == 'free') || ($video->preview_type == 'Free') || !$course->course_price) { ?>
                                                                            <span class="label label-default pull-right">Free</span>
                                                                        <?php } ?>
                                                                        <span class="help-block small"><?= 'Type: '; ?> <?php $splits = explode('.', $video->video_link); ?> <?= end($splits)? : str_replace('_', ' ', $video->content_type); ?> <?= ' | Added: ' . date_format($date, 'M d, Y'); ?> </span>

                                                                     <?php } ?>
                                                                     <?php if ($i  == $last_i && $cnt == $last_j) { ?>
                                                                     <div class="completebtnarea">
                                                                     <a href="javascript:void(0)" class="complete" data-video="<?php echo $video->video_id?>" data-course="<?php echo $video->course_id?>" data-section="<?php echo $video->section_id?>" ><span class="label label-default pull-right">Complete</span></a>
                                                                     <div class="clear"></div>
                                                                     </div>
                                                                     
                                                                     <?php } ?>

                                                                <?php } else {
                                                                    ?>
                                                                    <i class="glyphicon glyphicon-expand"></i>
                                                                    <?= $video->title_name; ?>

                                                                    <span class="help-block small"><?= 'Type: '; ?> <?php $splits = explode('.', $video->video_link); ?> <?= end($splits)? : str_replace('_', ' ', $video->content_type); ?> <?= ' | Added: ' . date_format($date, 'M d, Y'); ?> </span>
                                                                <?php } ?>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </li>
                                            <?php
                                            $j++;
                                            $cnt++;
                                        }
                                        $i++;
                                        
                                    } else {
                                        ?>
                                        <li class="lec">
                                            <div class="lec-left">
                                                <span class="course-no"></span>
                                            </div>
                                            <div class="lec-right">
                                                <div class="lec-url">
                                                    <div class="lec-main fxac">
                                                        <div class="lec-title">
                                                            No video added yet!
                                                        </div>
                                                        <div class="lec-includes">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                    }
                                }
                                $exams = $this->db->where('course_id', $course->course_id)->get('exam_title')->result();
                                if ($exams) {
                                    $k = 1;
                                    ?>
                                    <li class="chap-title"><b> Associated Exams </b></li>
                                    <?php foreach ($exams as $exam) { ?>
                                        <li class="lec">
                                            <div class="lec-left">
                                                <span class="course-no"><?= $k; ?> </span>
                                            </div>
                                            <div class="lec-right">
                                                <div class="lec-url">
                                                    <div class="lec-main fxac">
                                                        <div class="lec-title">
                                                            <?php if ($exam->public || $purchased || (($this->session->userdata('log')) && ($user_info->subscription_id) && ($subscription_end > date('Y-m-d')))) { ?>
                                                                <a href="<?= base_url('index.php/exam_control/view_exam_summary/' . $exam->title_id) ?>">
                                                                    <?= $exam->title_name; ?>
                                                                </a>
                                                                <?php
                                                            } else {
                                                                echo $exam->title_name;
                                                            }
                                                            ?>
                                                            <?php if ($exam->exam_price == 0) { ?>
                                                                <span class="label label-default pull-right">Free</span>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </li>
                                        <?php
                                        $k++;
                                    }
                                }
                                ?>
                            </ul>

                        </div>
                    </div>

                </div>
                <div class="col-md-3">
                    <div class="fb-share-button"
                         data-href="<?= base_url('index.php/course/course_summary/' . $course->course_id) ?>"
                         data-layout="button_count" data-size="large" >
                    </div>
                    
                    <div>
                        <h4 class="related_courses">Related courses: </h4>
                        <hr />
                    </div>
                    <?php
                    $related_courses = $this->db->get_where('courses', array('category_id' => $course->id))->result();
                    foreach ($related_courses as $value) {
                        if ($value->course_id != $course->course_id) {
                            ?>
                            <div class="thumbnail relatedcourses">
                                <a href="<?= base_url('index.php/course/course_summary/' . $value->course_id); ?>">

                                    <?php if (file_exists("course-images/$value->course_id.png")) { ?>
                                        <img class="course-summary-thumbnail" width="250" src="<?= base_url("course-images/$value->course_id.png"); ?>" alt="...">
                                    <?php } else { ?>
                                        <img class="course-summary-thumbnail" width="250" src="<?= base_url('course-images/placeholder.png'); ?>" alt="...">
                                    <?php } ?>

                                    <div class="caption">
                                        <h4><?= $value->course_title; ?></h4>
                                    </div>
                                </a>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>
</section><!--/#pricing-->
<script src="<?= base_url('assets/js/video.js') ?>"></script>

<div class="modal fade bd-example-modal-lg" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 5px;">
                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4  class="modal-title" id="myModalLabel">
                    <span id="modalVideotitle"></span>
                </h4>
            </div>
            <div class="modal-body " style="padding: 0px;">
                <div class="embed-responsive embed-responsive-16by9">
                    <video id="videoPlayer" class="videoPlayer embed-responsive-item" height="" autoplay src="" type="video/x-flv"  oncontextmenu="return false;" controls/></video>
                </div>
            </div>
            <br/>
            <?php if (false) { ?>
                <div class="" style=" text-align: center;">
                    <div class="btn-group" role="group">
                        <button type="button" id="prevSec" class="btn btn-default"> <i class="glyphicon glyphicon-fast-backward"  data-toggle="tooltip" data-placement="top" title="Previous Section "></i> </button>
                        <button type="button" id="prevVideo" class="btn btn-default"> <i class="glyphicon glyphicon-step-backward"  data-toggle="tooltip" data-placement="top" title="Previous Video "></i> </button>
                        <button type="button" id="nextVideo" class="btn btn-default"> <i class="glyphicon glyphicon-step-forward"  data-toggle="tooltip" data-placement="top" title="Next Video "></i> </button>
                        <button type="button" id="nextSec" class="btn btn-default"> <i class="glyphicon glyphicon-fast-forward"  data-toggle="tooltip" data-placement="top" title="Next Section "></i> </button>
                    </div>
                </div>
            <?php } ?>
            <br/>
        </div>
    </div>
</div>

<div class="modal fade bd-example-modal-lg" id="fileModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" >
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="padding-bottom: 5px;">
                <button type="button" id="modalClose" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4  class="modal-title" id="myModalLabel">
                    <span id="modalFiletitle"></span>
                </h4>
            </div>
            <div class="modal-body " style="padding: 0px;">
                <div class="embed-responsive embed-responsive-16by9" id="fileImport">
                    <div class="parentplayerppt">

                    </div>
                </div>
            </div>
            <br/>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function () {

        $(".complete").on('click', function (evt) {
            evt.preventDefault();
            var video_id = $(this).attr("data-video");
            var course_id = $(this).attr("data-course");
            var section_id = $(this).attr("data-section");
            $.ajax({
                method: "POST",
                url: "<?php echo base_url("index.php/course/update_user_course_status")?>",
                data: {
                        video_id: video_id, 
                        course_id: course_id, 
                        section_id: section_id
                }
            }).done(function (msg) {
              if(msg == true) {
                window.location.reload();
              }  
            });
        });
        
        $('.btnfileModal').on('click', function (evt) {
            var modelTitle = $(this).text();
            $('#modalFiletitle').html(modelTitle);
            var url = $(this).attr('data-video-url');
            var type = url.substr(url.length - 4);
            var html = "";
            
            if (type == "pptx") {
                html = '<div class="playerppt">&nbsp;</div><iframe id="myiframe" src="https://view.officeapps.live.com/op/embed.aspx?src=' + url + '" width="600" height="400"></iframe>';
                $('#fileImport > .parentplayerppt').html(html);
            } else if (type == "docx") {
                html = '<div class="playerppt" style="background-color:#fff">&nbsp;</div><iframe id="myiframe" src="https://view.officeapps.live.com/op/embed.aspx?src=' + url + '" width="600" height="400"></iframe>';
                //$('#fileModal').find('iframe').attr('src', 'https://view.officeapps.live.com/op/embed.aspx?src='+url);
                $('#fileImport > .parentplayerppt').html(html);
            } else if (type == ".pdf") {
                //PDF Viewr with Embeded Type;
                html = '<object data="' + url + '#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="700"></object>';
                $('#fileImport > .parentplayerppt').html(html);
            } else {
                html = '<iframe id="myiframe" src="' + url + '"></iframe>';
                $('#fileImport').html(html);
            }        
        });
        $(this).bind("contextmenu", function (e) {
            e.preventDefault();
        });
    });
</script>