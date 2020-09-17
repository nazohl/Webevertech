<?php
    if ($this->session->userdata('time_zone')) date_default_timezone_set($this->session->userdata('time_zone'));
    else if( ! ini_get('date.timezone') ) date_default_timezone_set('GMT');
?>
<section id="internal-slider" class="carousel">
    <div class="container">
    	<h1>Blog</h1>
    </div>
</section>
<section id="blog">
    <div class="container">
            <div class="row">
                <div class="col-xs-10 col-xs-offset-1">
                    <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
                    <?=(isset($message)) ? $message : ''; ?>
                </div>
                <div>
                    <!--<div class="block-search">
                        <?=form_open(base_url('index.php/blog/find'), 'method="GET" role="form" class="form-horizontal"'); ?>
                            <input name="keyword" type="search" class="form-control" placeholder="Search..." />
                        <?=form_close(); ?>
                    </div>-->
                    <div class="big-gap"></div>
                    <div class="blog"><!-- /.row Start-->
                        <?php if(empty($blogs)) echo "<h3>No result found!</h3>"; ?>
                        <?php foreach ($blogs as $value) { ?>
                            <div class="col-lg-3 col-md-4 col-xs-12 col-sm-6 postblockdisplaylist">
                            	<div class="postblockdisplay">
                                <h1><a href="<?=base_url('index.php/blog/post/'.$value->blog_id); ?>"><?=$value->blog_title; ?></a></h1>
                                <div class="blog-caption"><em>Author: <?=$value->user_name.', Published: '. date("F j, Y", strtotime($value->blog_post_date)); ?></em></div>
                                <div class="blog-body"><?=substr($value->blog_body, 0, 250); ?></div>
                                <div class="read-more"><a href="<?=base_url('index.php/blog/post/'.$value->blog_id); ?>" class="btn btn-default btn-sm col-sm-5"> Read More </a></div>
                                </div>
                                <div class="clear"></div>
                                
                            </div>
                        <?php } ?>
                    </div><!-- /.row End-->
					<div class="clear"></div>
                    <div class="text-center">
                         <?=$this->pagination->create_links(); ?>
                    </div>
                </div><!--/.col-md-4-->
                <div class="big-gap"></div>
            </div><!--/.row-->
         
    </div><!--/.container-->
</section><!--/#services-->