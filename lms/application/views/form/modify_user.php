<div id="note">
    <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
    <?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '' ?>
</div>
<div class="block">
  <div class="navbar block-inner block-header">
      <div class="row">
          <p class="text-muted">Modify user</p>
      </div>
  </div>
  <div class="block-content">
      <div class="row">
        <div class="col-sm-12">
          <?=form_open_multipart(base_url('index.php/user_control/modify_user/'.$data_id), 'role="form" class="form-horizontal"'); ?>
            <div class="form-group">
              <label for="user_name" class="col-sm-2 control-label col-xs-2">User Name: *</label>
              <div class="col-xs-6">
                  <?php echo form_input('user_name', $user->user_name, 'placeholder="User Name" class="form-control" required="required"') ?>
              </div>
            </div>
            <div class="form-group">
              <label for="user_email" class="col-sm-2 control-label col-xs-2">Email: *</label>
              <div class="col-xs-6">
                  <?php echo form_input('user_email', $user->user_email, 'pattern="^[a-zA-Z0-9.!#$%&'."'".'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$" title="you@domain.com" placeholder="Email address" class="form-control" required="required"') ?>
              </div>
            </div>

            <div class="form-group">
              <label for="user_phone" class="col-sm-2 control-label col-xs-2">Phone:</label>
              <div class="col-xs-6">
                  <?php echo form_input('user_phone', $user->user_phone, 'pattern="^(\(?\+?[0-9]*\)?)?[0-9_\- \(\)]*$" title="Enter Valid Phone Number" min="8" max="15" placeholder="Phone Number" class="form-control"') ?>
              </div>
            </div>
            <div class="form-group">
              <label for="user_role" class="col-sm-2 control-label col-xs-2">Role: *</label>
              <div class="col-xs-6">
                <select name="user_role" class="form-control" id="user_role">
                    <?php foreach ($user_role as $value) {
                        if ($value->user_role_id > $this->session->userdata('user_role_id')) { ?>
                        <option value="<?=$value->user_role_id;?>" <?=($user->user_role_id == $value->user_role_id)?'selected':'';?> ><?=$value->user_role_name;?></option>
                    <?php } } ?>
                </select>
              </div>
            </div>
            <div class="form-group <?=$user->user_role_id == 4 ? '' : 'hide'?>" id="paypal_info">
              <label for="paypal_id" class="col-sm-2 control-label col-xs-2">PayPal: </label>
              <div class="col-xs-6">
                  <?=form_input('paypal_id', $user->paypal_id, 'pattern="^[a-zA-Z0-9.!#$%&'."'".'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$" title="you@domain.com" placeholder="PayPal ID Email" class="form-control"') ?>
              </div>
                <p class="help-block info"><i class="glyphicon glyphicon-warning-sign"></i> Required if the payment will be shared with teachers. This email will be used to get the comissions.</p>
            </div>
            <div class="form-group">
              <label for="user_role" class="col-sm-2 control-label col-xs-2">Avatar: </label>
              <div class="col-xs-6">
                  <div class="col-md-10 col-xs-9 con-md-offset-2 con-md-offset-3">
                      <?php if(file_exists ('./user-avatar/'.$user->user_id.'.png')): ?>
                          <img src="<?=base_url('./user-avatar/'.$user->user_id.'.png');?>" alt="Avatar" style="margin-left: 150px; max-height: 150px; max-width: 150px;" >
                      <?php endif; ?>
                  </div>
                  <p class="form-control-static">
                      <?=form_upload('avatar', '', 'id="avatar" class="form-control"') ?>
                  </p>
              </div>
            </div>

            <div class="form-group">
              <label class="col-xs-offset-3 col-sm-8 col-xs-offset-2 col-xs-9">
                  <p class="text-muted">* Required fields.</p>
              </label>
            </div>
            <div class="col-xs-offset-1 col-sm-offset-2 col-xs-4">
                <button type="submit" class="btn btn-primary col-xs-6">Save</button>&nbsp;
                <button type="reset" class="btn btn-default">Reset</button>
            </div>
          <?php echo form_close() ?>
        </div>
      </div>
  </div>
</div>

<script type="text/javascript">
  $("#user_role").change(function(){
    // console.log(this.value);
    if (4 == this.value)
      $("#paypal_info").removeClass('hide');
    else
      $("#paypal_info").addClass('hide');

  });
</script>