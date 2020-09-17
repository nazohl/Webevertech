<section id="internal-slider" class="carousel">
    <div class="container">
    	<h1>Noticeboard</h1>
    </div>
</section>
<section id="noticeboard">
    <div class="container">
                <div class="col-xs-12 col-xs-offset-1">
                    <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
                    <?=(isset($message)) ? $message : ''; ?>
                </div>
                <div class="col-md-12 col-sm-12">
                <div class="big-gap"></div>
                    <div class=""><!-- /.row Start-->
                        <?php if(empty($notices)) echo "<h3>No result found!</h3>"; ?>
                        <?php foreach ($notices as $value) { ?>
                            <div class="blog-post">
                                <h1 class="text-center"><a href="<?=base_url('index.php/noticeboard/notice/'.$value->notice_id); ?>"><?=$value->notice_title; ?></a></h1>
                                <div class="blog-caption"><em>Created by: <?=$value->notice_created_by.', Published: '. date("F j, Y", strtotime($value->notice_start)); ?></em></div>
                                <div class="blog-body"><?=substr($value->notice_descr, 0, 250); ?></div><br/>
                                <div class="read-more"><a href="<?=base_url('index.php/noticeboard/notice/'.$value->notice_id); ?>" class="btn btn-default btn-sm col-sm-4 col-sm-offset-4"> Read More </a></div>
                            </div>
                        <?php } ?>
                    </div><!-- /.row End-->
                    <div class="big-gap"></div>
                </div><!--/.col-md-4-->

        <div class="big-gap"></div>
        
    </div><!--/.container-->
</section><!--/#noticeboard-->