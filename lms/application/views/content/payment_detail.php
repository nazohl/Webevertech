<section id="internal-slider" class="carousel">
    <div class="container">
    	<h1>Payment</h1>
    </div>
</section>
<section id="blog">
    <div class="container">
        <div>
            <div class="row">
                <div class="col-xs-10 col-xs-offset-1">
                    <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
                    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
                </div>
                <div class="col-md-10 col-sm-12 col-md-offset-1 col-sm-offset-0">
                <div class="big-gap"></div>
                    <div><!-- /.row Start-->
                        <div>
                            <h1 class="text-center">Payment Details</h1>
                            <div class="big-gap"></div>
                            <div class="blog-body">
                            <table class="table">
                                <tr>
                                    <th>Item</th>
                                    <th>Price</th>
                                </tr>
                                <tr>
                                <?php if(isset($course)){ ?>
                                    <td><?=$course->course_title?></td>
                                    <td><?=$currency_symbol.$course->course_price?></td>
                                <?php }elseif(isset($exam)){ ?>
                                    <td><?=$exam->title_name?></td>
                                    <td><?=$currency_symbol.$exam->exam_price?></td>
                                <?php }elseif(isset($subscribtion)){ ?>
                                    <td><?='Subscribtion: ' . $subscribtion->price_table_title?></td>
                                    <td><?=$currency_symbol.$subscribtion->price_table_cost?></td>
                                <?php } ?>
                                </tr>
                            </table>
                            <br/><br/>
                            <h3 class="text-center">Pay With:</h3>
                            <p class="text-center" style="margin-top:15px;">
                            <?php $payment_method = false;
                            $pay_url = base_url();
                            if(isset($course)){
                                $pay_url .= 'index.php/course/payment/' . $course->course_id . '/';
                            }elseif (isset($exam)) {
                                $pay_url .= 'index.php/exam_control/payment/' . $exam->title_id . '/';
                            }elseif (isset($subscribtion)) {
                                $pay_url .= 'index.php/membership/payment/' . $subscribtion->price_table_id . '/';
                            }

                            if($this->db->where('id', 1)->get('paypal_settings')->row()->enabled == 1)
                            { ?>
                                <a href="<?=$pay_url . 'PayPal'?>" class="btn btn-default">
                                    <?php if(file_exists('assets/images/paypal.png')){ ?>
                                        <img src="<?=base_url()?>assets/images/paypal.png" height="20">
                                    <?php }else{ ?>
                                        <strong>PayPal</strong>
                                    <?php } ?>
                                </a>
                            <?php  $payment_method = TRUE;
                            }

                            if($this->db->where('id', 1)->get('payu_settings')->row()->enabled == 1)
                            { ?>
                                <a href="<?=$pay_url . 'PayUMoney'?>" class="btn btn-default">
                                    <?php if(file_exists('assets/images/PayUMoney.png')){ ?>
                                        <img src="<?=base_url()?>assets/images/PayUMoney.png" height="20">
                                    <?php }else{ ?>
                                        <strong>PayUMoney</strong>
                                    <?php } ?>
                                </a>
                                <?php $payment_method = TRUE;
                            } ?>

                            <?php if (!$payment_method) {
                                echo "<h3 style='color: red;'>No payment method available to process this payment!</h3>";
                            } ?>
                            </p>
                            </div>
                        </div>
                    </div><!-- /.row End-->
                </div><!--/.col-md-10-->
            </div><!--/.row-->
        </div><!--/.box-->
        <div class="big-gap"></div>
    </div><!--/.container-->
</section><!--/#services-->