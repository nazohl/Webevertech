<?php //echo "<pre/>"; print_r($courses); exit(); ?>
<div id="note">
    <?php if ($message) echo $message; ?>
    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '';?>
</div>
<div class="block">
    <div class="navbar block-inner block-header">
        <div class="row"><p class="text-muted">Course List </p></div>
    </div>
    <div class="block-content">
    <div class="row">
    <div class="col-sm-12">
        <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="example">
            <thead>
                <tr>
                    <th>IMG</th>
                    <th>Course Title</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($courses as $course) {
                ?>
                <tr class="<?= ($i & 1) ? 'even' : 'odd'; ?>">
                    <td width="70px;">
                        <?php if (file_exists("./course-images/$course->course_id.png")) { ?>
                            <img style="width: 60px; height: 40px;" src="<?=base_url("course-images/$course->course_id.png"); ?>">
                        <?php }else{ ?>
                            <img style="width: 60px; height: 40px;" src="<?=base_url("course-images/placeholder.png"); ?>">
                        <?php } ?>
                    </td>
                    <td>
                        <p class="lead"><?=$course->course_title?>
                        </p>
                        <span class="text-muted">Category: </span> <?=$course->category_name; ?>
                        &nbsp;
                        <span class="text-muted">Sub-category: </span> <?=$course->sub_cat_name; ?>
                        &nbsp;
                        <span class="text-muted">Price: </span>
                        <?= $currency_code . ' ' . $currency_symbol ?><?= $course->course_price; ?>
                        <span class="pull-right">
                            <span class="text-muted">Author: </span>
                            <?php echo $course->user_name; ?>
                        </span>
                    </td>
                    <td width="70px;">
                        <a href="<?= base_url('index.php/course/course_detail/' . $course->course_id); ?>" data-toggle="tooltip" title="View Sections" data-placement="top"><i class="glyphicon glyphicon-fullscreen"></i>&nbsp</a>

                        <a href="<?= base_url('index.php/course/edit_course_detail/' . $course->course_id); ?>" data-toggle="tooltip" title="View Detail" data-placement="top"><i class="glyphicon glyphicon-edit"></i>&nbsp</a>

                        <a onclick="return delete_confirmation()" href="<?= base_url('index.php/course/delete_course/' . $course->course_id); ?>" data-toggle="tooltip" title="Delete" data-placement="top"><i class="glyphicon glyphicon-trash"></i>&nbsp</a>
                    </td>
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
</div><!--/span-->

