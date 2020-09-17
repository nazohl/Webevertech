<section id="internal-slider" class="carousel">
    <div class="container">
    	<h1>FAQs</h1>
    </div>
</section>
<section id="faq">
    <div class="container">
        <div class="">
            <div class="">
                <div class="col-xs-10 col-xs-offset-1">
                    <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>        
                    <?=(isset($message)) ? $message : ''; ?>
                </div>
                <div class="col-md-12 col-sm-12">
                   
                    <div class="big-gap"></div>
                    <div class="panel-group" id="accordion">
                    <?php  $faq_grps = $this->db->get('faq_grp')->result();
                    if (isset($faqs) AND !empty($faqs)) { 
                        foreach ($faq_grps as $faq_grp) { $i = 1;
                            echo "<h3>".$faq_grp->faq_grp_name."</h3>";
                            foreach ($faqs as $faq) { 
                                if($faq_grp->faq_grp_id == $faq->faq_grp_id){ ?>
                                 <div style="margin:20px;"></div>
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            <h4 class="panel-title"><?=$i;?>.
                                                <a data-toggle="collapse" data-parent="#accordion" href="#faq<?=$faq->faq_id; ?>">
                                                    <?=$faq->faq_ques; ?>
                                                </a>
                                            </h4>
                                        </div>
                                        <div id="faq<?=$faq->faq_id; ?>" class="panel-collapse collapse">
                                            <div class="panel-body">
                                                <?=$faq->faq_ans; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="big-gap"></div>
                    <?php           $i++;
                                }
                            }
                        }

                    } else {
                        echo '<div class="panel panel-default"><div class="panel-body">No result found!</div></div>';
                    }
                    ?>
                    </div>
                </div>
            </div>
        </div> 
    </div>
</section><!--/#pricing-->