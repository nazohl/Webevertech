<?php
$str = '[';
foreach ($currencies as $value) {
    $str .= "{value:" . $value->currency_id . ",text:'" . $value->currency_name . " (" . $value->currency_symbol . ")'},";
}
$str = substr($str, 0, -1);
$str .= "]";
?>
<div id="note">
    <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
    <?=(isset($message)) ? $message : ''; ?>
</div>
<div class="block">
    <div class="navbar block-inner block-header">
        <div class="row"><p class="text-muted">System Info </p></div>
    </div>
    <div class="block-content">
        <div class="row">
            <div class="col-sm-12">
                <!--BEGIN TABS-->
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab_1" data-toggle="tab">Basic Info</a></li>
                    <li><a href="#tab_7" data-toggle="tab">Content</a></li>
                    <li><a href="#tab_2" data-toggle="tab">Social Profiles</a></li>
                    <li><a href="#tab_3" data-toggle="tab">Payment Gateways</a></li>
                    <?php
                    $paypal_mode = $this->db->where('id', 1)->get('paypal_settings')->row()->enabled;
                    if(1 == $paypal_mode){ ?>
                        <li><a href="#tab_5" data-toggle="tab">PayPal Settings</a></li>
                    <?php
                    }
                    $payu_mode = $this->db->where('id', 1)->get('payu_settings')->row()->enabled;
                    if(1 == $payu_mode){ ?>
                        <li><a href="#tab_6" data-toggle="tab">PayUMoney Settings</a></li>
                    <?php
                    } ?>
                </ul>
                <div class="tab-content info-display">
                    <div class="tab-pane" id="tab_7">
                        <?=form_open_multipart(base_url('index.php/admin/system_control/update_content'), 'role="form" class="form"'); ?>
                        <?php $slider_count = 0;
                        $sys_content = $this->db->get('content')->result();
                        foreach ($sys_content as $value)
                        {
                            if ($value->content_type == 'about_us')
                            { ?>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h4>About Us:</h4>
                                        <input type="text" name="about_title" value="<?=$value->content_heading?>" placeholder="Title" class="form-control" required="required">
                                        <br/>
                                        <textarea name="about_content" placeholder="About Us" class='form-control textarea-wysihtml5'><?=$value->content_data?></textarea>
                                    </div>
                                </div>
                            <?php
                            }
                            if ($value->content_type == 'price_table_msg')
                            { ?>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <h4>Price Table:</h4>
                                        <input type="text" name="price_tbl_title" value="<?=$value->content_heading?>" placeholder="Title" class="form-control" required="required">
                                        <br/>
                                        <textarea name="price_tbl_content" placeholder="Price Table Message" rows="2" class='form-control textarea-wysihtml5'><?=$value->content_data?></textarea>
                                    </div>
                                </div>
                            <?php
                            }
                            if ($value->content_type == 'slider_text')
                            {  ?>
                            <div class="row">
                                <div class="col-md-12">
                                    <?php
                                    if($slider_count == 0)
                                    { ?>
                                        <h4 class="col-md-12">Slider Image:
                                        <small><input type="file" name="slider" style="display: inline;"></small></h4>
                                        <?php
                                        if(file_exists ('slider.png'))
                                        { ?>
                                            <img width="100%" src="<?=base_url('slider.png');?>" alt="slider">
                                        <?php
                                        }?>
                                        <h4 class="col-md-12">Slider text:</h4>
                                    <?php
                                    } ?>
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            <input type="text" name="slider_text_title[]" value="<?=$value->content_heading?>" placeholder="Title" class="form-control" required="required">
                                        </div>
                                    </div>
                                    <div class="col-md-7">
                                        <div class="form-group">
                                            <input type="text" name="slider_text[]" value="<?=$value->content_data?>" placeholder="Slider Text" class="form-control" required="required">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php $slider_count++;
                            }
                        } ?>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-lg btn-primary col-xs-5 col-sm-4 pull-right"><i class="glyphicon glyphicon-ok"> </i> Save</button>
                        </div>

                        <?=form_close() ?>
                    </div>

                    <div class="tab-pane" id="tab_6">
                        <dl class="dl-horizontal">
                            <dt>PayUMoney Status: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="payu_sandbox" data-type="select" data-source="[{value:0,text:'Live'},{value:1,text:'Sandbox'}]" data-value="<?= (@$payu_set->sandbox) ? '1' : '0'; ?>" data-url="<?php echo base_url('index.php/admin/system_control/update_payu_info'); ?>" data-pk="<?=($payu_set->id) ? $payu_set->id : 1; ?>" class="data-modify-payu no-style"><?= (@$payu_set->sandbox) ? 'Sandbox' : 'Live'; ?></a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Merchant Key: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="payu_merchant_key" data-type="textarea" data-rows="2"  data-url="<?php echo base_url('index.php/admin/system_control/update_payu_info'); ?>" data-pk="<?=($payu_set->id) ? $payu_set->id : 1; ?>" class="data-modify-payu no-style"><?= @$payu_set->merchant_key ?></a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Salt: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="payu_salt" data-type="text" data-url="<?php echo base_url('index.php/admin/system_control/update_payu_info'); ?>" data-pk="<?=($payu_set->id) ? $payu_set->id : 1; ?>" class="data-modify-payu no-style"><?= @$payu_set->salt ?></a>
                                    </p>
                                </blockquote>
                            </dd>
                        </dl>
                        <span class="help-block"><i class="fa fa-warning"></i> You need your own test or live account to test PayUMoney in action.</span>
                        <hr/>
                        <?php if ($this->session->userdata['user_role_id'] == 1) { ?>
                            <div class="col-xs-10 col-xs-offset-1">
                                <a class="btn btn-info btn-block modify" name="modify-payu" href = "#"><i class="glyphicon glyphicon-edit"></i> Modify</a>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="tab-pane" id="tab_5">
                        <dl class="dl-horizontal">
                            <dt>System Commission: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="commission" data-type="text" data-rows="2"  data-url="<?=base_url('index.php/admin/system_control/update_paypal_info'); ?>" data-pk="<?=($payment_set->id) ? $payment_set->id : 1; ?>" class="data-modify-pp no-style"><?= @$payment_set->commission_percent ?></a> %
                                        <small><i class="fa fa-warning"></i> Set 100 to disable the commission based system. </small>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>PayPal Mode: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="pp_sandbox" data-type="select" data-source="[{value:0,text:'Live'},{value:1,text:'Sandbox'}]" data-value="<?= (@$payment_set->sandbox) ? '1' : '0'; ?>" data-url="<?=base_url('index.php/admin/system_control/update_paypal_info'); ?>" data-pk="<?=($payment_set->id) ? $payment_set->id : 1; ?>" class="data-modify-pp no-style"><?= (@$payment_set->sandbox) ? 'Sandbox' : 'Live'; ?></a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Currency: <br/><small><em>( USD, EUR, etc. )</em></small></dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="pp_currency" data-type="select" data-url="<?=base_url('index.php/admin/system_control/update_paypal_info'); ?>" data-pk="<?=($payment_set->id) ? $payment_set->id : 1; ?>" data-source="<?= $str; ?>" data-value="<?= @$payment_set->currency_id; ?>" class="data-modify-pp no-style"><?= @$payment_set->currency_name . ' (' . @$payment_set->currency_symbol . ')' ?></a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>PayPal Email: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="pp_email" data-type="text" data-rows="2"  data-url="<?=base_url('index.php/admin/system_control/update_paypal_info'); ?>" data-pk="<?=($payment_set->id) ? $payment_set->id : 1; ?>" class="data-modify-pp no-style"><?= @$payment_set->paypal_email ?></a>
                                        <small><i class="fa fa-warning"></i> Required if the payment will be shared with teachers. This email will be used to get the comissions.</small>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Application ID: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="pp_app_id" data-type="text" data-rows="2"  data-url="<?=base_url('index.php/admin/system_control/update_paypal_info'); ?>" data-pk="<?=($payment_set->id) ? $payment_set->id : 1; ?>" class="data-modify-pp no-style"><?= @$payment_set->application_id ?></a>
                                        <small><i class="fa fa-warning"></i> Required if the payment will be shared with teachers. <a href="https://developer.paypal.com/docs/api/quickstart/credentials/?mark=client%20id" target="_blank">Check PayPal document for help.</a></small>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>PayPal API Username: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="pp_user" data-type="textarea" data-rows="2"  data-url="<?=base_url('index.php/admin/system_control/update_paypal_info'); ?>" data-pk="<?=($payment_set->id) ? $payment_set->id : 1; ?>" class="data-modify-pp no-style"><?= @$payment_set->api_username ?></a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>PayPal API Password: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="pp_pass" data-type="text" data-url="<?=base_url('index.php/admin/system_control/update_paypal_info'); ?>" data-pk="<?=($payment_set->id) ? $payment_set->id : 1; ?>" class="data-modify-pp no-style"><?= @$payment_set->api_pass ?></a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Paypal API Signature: </dt>
                            <dd>
                                <blockquote>
                                    <p>
                                        <a href="#" data-name="pp_sign" data-type="textarea" data-rows="3"  data-url="<?=base_url('index.php/admin/system_control/update_paypal_info'); ?>" data-pk="<?=($payment_set->id) ? $payment_set->id : 1; ?>" class="data-modify-pp no-style"><?= @$payment_set->api_signature ?></a>
                                    </p>
                                </blockquote>
                            </dd>
                        </dl>
                        <small><i class="fa fa-warning"></i> <a href="https://www.paypal-knowledge.com/infocenter/index?page=content&widgetview=true&id=FAQ1455" target="_blank">Check PayPal document for help.</a></small>

                        <hr/>
                        <?php if ($this->session->userdata['user_role_id'] == 1) { ?>
                            <div class="col-xs-10 col-xs-offset-1">
                                <a class="btn btn-info btn-block modify" name="modify-pp" href = "#"><i class="glyphicon glyphicon-edit"></i> Modify</a>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="tab-pane" id="tab_3">
                        <table class="table table-striped" width="100%">
                            <tr>
                                <th>Logo</th>
                                <th>Name</th>
                                <th>Region</th>
                                <th>Action</th>
                            </tr>
                            <tr style="height:100px;">
                                <td>
                                    <?php if(file_exists('assets/images/paypal.png')){ ?>
                                        <img src="<?=base_url()?>assets/images/paypal.png" height="20">
                                    <?php }else{ ?>
                                        <strong>PayPal</strong>
                                    <?php } ?>
                                </td>
                                <td>PayPal</td>
                                <td>International</td>
                                <td>
                                    <?php
                                    if(1 != $paypal_mode){ ?>
                                        <a href="<?=base_url('index.php/admin/system_control/set_payment_gatewaye/PayPal/install');?>" class="btn btn-info"> <i class="glyphicon glyphicon-save"></i> Install</a>
                                    <?php
                                    }else{ ?>
                                        <a href="<?=base_url('index.php/admin/system_control/set_payment_gatewaye/PayPal/uninstall');?>" class="btn btn-warning"> <i class="glyphicon glyphicon-trash"></i> Unnstall</a>
                                    <?php
                                    }?>
                                </td>
                            </tr>

                            <tr style="height:100px;">
                                <td>
                                    <?php if(file_exists('assets/images/PayUMoney.png')){ ?>
                                        <img src="<?=base_url()?>assets/images/PayUMoney.png" height="20">
                                    <?php }else{ ?>
                                        <strong>PayUMoney</strong>
                                    <?php } ?>
                                    <span class="help-block"><i class="fa fa-warning"></i> You need your own test or live account to test PayUMoney in action.</span>

                                </td>
                                <td>PayUMoney</td>
                                <td>India</td>
                                <td>
                                    <?php
                                    if(1 != $payu_mode){ ?>
                                        <a href="<?=base_url('index.php/admin/system_control/set_payment_gatewaye/PayUMoney/install');?>" class="btn btn-info"> <i class="glyphicon glyphicon-save"></i> Install</a>
                                    <?php
                                    }else{ ?>
                                        <a href="<?=base_url('index.php/admin/system_control/set_payment_gatewaye/PayUMoney/uninstall');?>" class="btn btn-warning"> <i class="glyphicon glyphicon-trash"></i> Unnstall</a>
                                    <?php
                                    }?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <div class="tab-pane" id="tab_2">
                        <dl class="dl-horizontal">
                            <dt>YouTube: </dt>
                            <dd>
                                <blockquote>
                                    <p class="lead">
                                        <a href="#" data-name="youtube" data-type="textarea" data-rows="2" data-url="<?=base_url('index.php/admin/system_control/update_system_info'); ?>" data-pk="<?= $sys_set->brand_id ?>" class="data-modify-social no-style"><?= $sys_set->you_tube_url ?></a>
                                        <a href="<?= $sys_set->you_tube_url ?>" target="_blank" class="btn btn-default btn-xs vitis-url"> Visit the link </a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Facebook: </dt>
                            <dd>
                                <blockquote>
                                    <p class="lead">
                                        <a href="#" data-name="facebook" data-type="textarea" data-rows="2" data-url="<?=base_url('index.php/admin/system_control/update_system_info'); ?>" data-pk="<?= $sys_set->brand_id ?>" class="data-modify-social no-style"><?= $sys_set->facbook_url ?></a>
                                        <a href="<?= $sys_set->facbook_url ?>" target="_blank" class="btn btn-default btn-xs vitis-url"> Visit the link </a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Google+: </dt>
                            <dd>
                                <blockquote>
                                    <p class="lead">
                                        <a href="#" data-name="googleplus" data-type="textarea" data-rows="2" data-url="<?=base_url('index.php/admin/system_control/update_system_info'); ?>" data-pk="<?= $sys_set->brand_id ?>" class="data-modify-social no-style"><?= $sys_set->googleplus_url ?></a>
                                        <a href="<?= $sys_set->googleplus_url ?>" target="_blank" class="btn btn-default btn-xs vitis-url"> Visit the link </a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Twitter: </dt>
                            <dd>
                                <blockquote>
                                    <p class="lead">
                                        <a href="#" data-name="twitter" data-type="textarea" data-rows="2" data-url="<?=base_url('index.php/admin/system_control/update_system_info'); ?>" data-pk="<?= $sys_set->brand_id ?>" class="data-modify-social no-style"><?= $sys_set->twitter_url ?></a>
                                        <a href="<?= $sys_set->twitter_url ?>" target="_blank" class="btn btn-default btn-xs vitis-url"> Visit the link </a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Linkedin: </dt>
                            <dd>
                                <blockquote>
                                    <p class="lead">
                                        <a href="#" data-name="linkedin" data-type="textarea" data-rows="2" data-url="<?=base_url('index.php/admin/system_control/update_system_info'); ?>" data-pk="<?= $sys_set->brand_id ?>" class="data-modify-social no-style"><?= $sys_set->linkedin_url ?></a>
                                        <a href="<?= $sys_set->linkedin_url ?>" target="_blank" class="btn btn-default btn-xs vitis-url"> Visit the link </a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Pinterest: </dt>
                            <dd>
                                <blockquote>
                                    <p class="lead">
                                        <a href="#" data-name="pinterest" data-type="textarea" data-rows="2" data-url="<?=base_url('index.php/admin/system_control/update_system_info'); ?>" data-pk="<?= $sys_set->brand_id ?>" class="data-modify-social no-style"><?= $sys_set->pinterest_url ?></a>
                                        <a href="<?= $sys_set->pinterest_url ?>" target="_blank" class="btn btn-default btn-xs vitis-url"> Visit the link </a>
                                    </p>
                                </blockquote>
                            </dd>
                            <dt>Flickr: </dt>
                            <dd>
                                <blockquote>
                                    <p class="lead">
                                        <a href="#" data-name="flickr" data-type="textarea" data-rows="2" data-url="<?=base_url('index.php/admin/system_control/update_system_info'); ?>" data-pk="<?= $sys_set->brand_id ?>" class="data-modify-social no-style"><?= $sys_set->flickr_url ?></a>
                                        <a href="<?= $sys_set->flickr_url ?>" target="_blank" class="btn btn-default btn-xs vitis-url"> Visit the link </a>
                                    </p>
                                </blockquote>
                            </dd>
                        </dl>
                        <hr/>
                        <?php if ($this->session->userdata['user_role_id'] == 1) { ?>
                            <div class="col-xs-10 col-xs-offset-1">
                                <a class="btn btn-info btn-block modify" id="rev-link" name="modify-social" href = "#"><i class="glyphicon glyphicon-edit"></i> Modify</a>
                            </div>
                        <?php } ?>
                    </div>

                    <div class="tab-pane active" id="tab_1">
                        <?=form_open_multipart(base_url('index.php/admin/system_control/view_settings'), 'role="form" class="form"'); ?>

                        <?php if(file_exists ('logo.png')): ?>
                            <img src="<?=base_url('logo.png');?>" alt="LOGO">
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand_name">Brand Name:</label>
                                        <input type="text" name="brand_name" value="<?=$sys_set->brand_name?>" class="form-control" placeholder="Brand Name" required="required">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="brand_tagline" class=" mobile">Brand Tagline:</label>
                                        <input type="text" name="brand_tagline" value="<?=$sys_set->brand_tagline?>" class="form-control" placeholder="Brand Tagline" required="required">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="local_time_zone">Timezone:</label>
                                        <select name="local_time_zone" class="form-control" required="required">
                                            <option value=''>Select Timezone</option>
                                            <?php  foreach ($time_zone as $value) { ?>
                                                <option value="<?=$value->timezone_name?>" <?=($sys_set->local_time_zone == $value->timezone_name)?'selected':''?>><?=$value->timezone_name?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bussiness_type" class=" mobile">Bussiness Type:</label>
                                        <select name="bussiness_type" class="form-control" required="required">
                                            <option value="1" <?=($sys_set->commercial == 1)?'selected':''?> >Commercial</option>
                                            <option value="0" <?=($sys_set->commercial == 0)?'selected':''?> >Non-commercial</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="support_email">Support Email:</label>
                                        <input type="text" name="support_email" value="<?=$sys_set->support_email?>" class="form-control" placeholder="Support Email" required="required">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="support_phone" class=" mobile">Support Phone:</label>
                                        <input type="text" name="support_phone" value="<?=$sys_set->support_phone?>" class="form-control" placeholder="Support Phone">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="address">Full Address:</label>
                                <input type="text" name="address" value="<?=$sys_set->address?>" class="form-control" placeholder="Full Address" required="required">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="student_can_register">Student Can Register:</label>
                                        <select name="student_can_register" class="form-control" required="required">
                                            <option value="1" <?=($sys_set->student_can_register == 1)?'selected':''?>>Yes</option>
                                            <option value="0" <?=($sys_set->student_can_register == 0)?'selected':''?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="teacher_can_register" class=" mobile">Teacher Can Register:</label>
                                        <select name="teacher_can_register" class="form-control" required="required">
                                            <option value="1" <?=($sys_set->teacher_can_register == 1)?'selected':''?>>Yes</option>
                                            <option value="0" <?=($sys_set->teacher_can_register == 0)?'selected':''?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="teacher_can_create_categories" class=" mobile">Teacher Can Create Categories:</label>
                                        <select name="teacher_can_create_categories" class="form-control" required="required">
                                            <option value="1" <?=($sys_set->teacher_can_create_categories == 1)?'selected':''?>>Yes</option>
                                            <option value="0" <?=($sys_set->teacher_can_create_categories == 0)?'selected':''?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="teacher_can_create_blogs" class=" mobile">Teacher Can Create Blogs:</label>
                                        <select name="teacher_can_create_blogs" class="form-control" required="required">
                                            <option value="1" <?=($sys_set->teacher_can_create_blogs == 1)?'selected':''?>>Yes</option>
                                            <option value="0" <?=($sys_set->teacher_can_create_blogs == 0)?'selected':''?>>No</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="show_latest_courses_on_homepage">Show Latest Courses on Homepage:</label>
                                        <select name="show_latest_courses_on_homepage" class="form-control" required="required">
                                            <option value="1" <?=($sys_set->show_latest_courses_on_homepage == 1)?'selected':''?>>Yes</option>
                                            <option value="0" <?=($sys_set->show_latest_courses_on_homepage == 0)?'selected':''?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="number_of_latest_course" class=" mobile">Number of Latest Course:</label>
                                        <select name="number_of_latest_course" class="form-control" required="required">
                                            <option value="4" <?=($sys_set->number_of_latest_course == 4)?'selected':''?>>4</option>
                                            <option value="8" <?=($sys_set->number_of_latest_course == 8)?'selected':''?>>8</option>
                                            <option value="12" <?=($sys_set->number_of_latest_course == 12)?'selected':''?>>12</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="show_latest_exams_on_homepage">Show Latest Exams on Homepage:</label>
                                        <select name="show_latest_exams_on_homepage" class="form-control" required="required">
                                            <option value="1" <?=($sys_set->show_latest_exams_on_homepage == 1)?'selected':''?>>Yes</option>
                                            <option value="0" <?=($sys_set->show_latest_exams_on_homepage == 0)?'selected':''?>>No</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="number_of_latest_exam" class=" mobile">Number of Latest Exam:</label>
                                        <select name="number_of_latest_exam" class="form-control" required="required">
                                            <option value="4" <?=($sys_set->number_of_latest_exam == 4)?'selected':''?>>4</option>
                                            <option value="8" <?=($sys_set->number_of_latest_exam == 8)?'selected':''?>>8</option>
                                            <option value="12" <?=($sys_set->number_of_latest_exam == 12)?'selected':''?>>12</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <label for="logo" class=" mobile">Logo:</label>
                            <input type="file" name="logo" id="logo">
                            <p class="help-block"><i class="glyphicon glyphicon-warning-sign"></i> Allowed only: jpg | jpeg | png. Standard: 200px X 78px</p>
                        </div>

                        <div class="col-md-12">
                            <button type="submit" class="btn btn-lg btn-primary col-xs-5 col-sm-4 pull-right"><i class="glyphicon glyphicon-ok"> </i> Save</button>
                        </div>

                        <?=form_close() ?>
                    </div>
                </div>
                <!--END TABS-->
            </div>
        </div>
    </div>
</div><!--/span-->

<?php include 'application/views/plugin_scripts/bootstrap-wysihtml5.php';?>
