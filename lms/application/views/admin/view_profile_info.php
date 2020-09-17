<div id="note">
    <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
    <?=(isset($message)) ? $message : ''; ?>
</div>

<div class="block">
    <div class="navbar block-inner block-header">
        <div class="row"><p class="text-muted">Profile Info </p></div>
    </div>
    <div class="block-content">
        <div class="row">
            <div class="col-xs-12">
                <div class="col-sm-4 col-md-3">
                    <ul class="proile tabbable">
                        <li class="active">
                            <a data-toggle="tab" href="#tab-1"><i class="fa fa-cog"></i> Personal info </a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tab-2"><i class="fa fa-lock"></i> Change Password</a>
                        </li>
                        <li>
                            <a data-toggle="tab" href="#tab-3"><i class="fa fa-lock"></i> Subscription</a>
                        </li>
                    </ul>
                </div>
                <div class="col-sm-8 col-md-9 proile-info">
                    <div class="row">&nbsp;</div>
                    <div class="tab-content">
                        <div id="tab-3" class="tab-pane">
                            <?php
                            if ($profile_info->subscription_id)
                            {
                                $package = $this->db->get_where('price_table', array('price_table_id' => $profile_info->subscription_id))->row()->price_table_title; ?>
                                <p class="lead">You are subscribed in "<?=$package; ?>" package.</p>
                                    <p>Subscription will expire on <?=date('F d, Y',$profile_info->subscription_end)?></p>
                            <?php
                            }else
                            { ?>
                                <p class="lead">Currently you have not subscribed in package. </p>
                                <a href="<?=base_url('index.php/guest/pricing') ?>" class="btn btn-default"> Subscribe Now</a>
                            <?php
                            } ?>
                        </div>

                        <div id="tab-2" class="tab-pane">
                            <?= form_open(base_url('index.php/admin_control/change_password'), 'role="form" class="form-horizontal"'); ?>
                            <div class="form-group">
                                <label for="password" class="col-sm-3 control-label invisible-on-sm">Current Password: </label>
                                <div class="col-sm-9">
                                    <?= form_password('old-pass', '', 'placeholder="Old Password" class="form-control" required="required"') ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label invisible-on-sm">New Password: </label>
                                <div class="col-sm-9">
                                    <?= form_password('new-pass', '', 'placeholder="New Password" class="form-control" required="required"') ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword3" class="col-sm-3 control-label invisible-on-sm">Re-type New Password: </label>
                                <div class="col-sm-9">
                                    <?= form_password('re-new-pass', '', 'placeholder="Re-type New Password" class="form-control" required="required"') ?>
                                </div>
                            </div><br/>
                            <hr/>
                            <div class="form-group">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">Change Password</button>
                                </div>
                            </div>
                            <?= form_close() ?>
                        </div>

                        <div id="tab-1" class="tab-pane active">
                            <div class="row">
                                <div class="col-md-10 col-xs-9 con-md-offset-2 con-md-offset-3">
                                </div>
                            </div>
                            <br/>

                            <?=form_open_multipart(base_url('index.php/admin_control/update_profile'), 'role="form" class="form"'); ?>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <?php if(file_exists ('./user-avatar/'.$profile_info->user_id.'.png')): ?>
                                        <img src="<?=base_url('./user-avatar/'.$profile_info->user_id.'.png');?>" alt="Avatar" style="margin-left: 150px; max-height: 150px; max-width: 150px;" >
                                    <?php else: ?>
                                        <img src="<?=base_url('./user-avatar/avatar-placeholder.jpg');?>" alt="Avatar" style="margin-left: 150px; max-height: 150px; max-width: 150px;" >
                                    <?php endif; ?>

                                    <input type="file" name="avatar" id="avatar" style="display: inline; margin-left: 20px;">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Full Name: </label>
                                    <input type="text" name="user_name" value="<?=$profile_info->user_name?>" placeholder="Full Name *" class="form-control" required="required">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Phone: </label>
                                    <input type="text" name="user_phone" value="<?=$profile_info->user_phone?>" placeholder="Phone" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Email: </label>
                                    <?=form_input('user_email', $profile_info->user_email, 'id="user_email" type="email" pattern="^[a-zA-Z0-9.!#$%&'."'".'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$" title="you@domain.com" placeholder="Email address *" class="form-control" required="required"') ?>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">PayPal ID: </label>
                                    <?=form_input('paypal_id', $profile_info->paypal_id, 'id="paypal_id" type="email" pattern="^[a-zA-Z0-9.!#$%&'."'".'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$" title="you@domain.com" placeholder="PayPal email address" class="form-control"') ?>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">Role: </label>
                                    <input type="text" name="user_name" value="<?=$profile_info->user_role_name?>" class="form-control" disabled="disabled">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <button type="submit" class="btn btn-lg btn-primary col-xs-5 col-sm-4 pull-right"><i class="glyphicon glyphicon-ok"> </i> Save</button>
                            </div>
                            <?=form_close() ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div><!--/span-->