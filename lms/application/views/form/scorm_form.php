<?=($this->session->flashdata('message')) ? $this->session->flashdata('message') : '';?>
<!-- block -->
<div class="block">
    <div class="navbar block-inner block-header">
        <div class="row"><p class="text-muted">Create New Scorm Course </p></div>
    </div>
    <div class="block-content">
    <?=form_open_multipart(base_url('index.php/course/save_scorm'), 'role="form" class="form-horizontal"'); ?>
    <div class="row">
    <div class="col-sm-12">
        <div class="row">
            <div class="col-xs-offset-1 col-xs-10">
                <?=validation_errors('<div class="alert alert-danger">', '</div>'); ?>
            </div>
        </div>
        <div class="row">
            <?php
            $option = array();
            $option[''] = 'Select Category';
            foreach ($categories as $category) {
                if ($category->active) {
                    $option[$category->category_id] = $category->category_name;
                }
            }
            ?>
            <div class="form-group">
                <label for="parent-category" class="col-sm-offset-0 col-lg-2 col-xs-offset-1 col-xs-3 control-label mobile">Select Category:</label>
                <div class="col-lg-3 col-sm-4 col-xs-4">
                    <?=form_dropdown('parent-category', $option,'', 'id="parent-category" class="form-control"') ?>
                </div>
                <div class="col-lg-3 col-sm-4 col-xs-4">
                    <select name="category" id="category" class="form-control">
                        <option>Sub-category</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label for="scorm_file" class="col-sm-offset-0 col-lg-2 col-xs-offset-1 col-xs-3 control-label mobile">Scorm File: </label>
                <div class="col-lg-5 col-sm-8 col-xs-7 col-mb">
                    <?=form_upload('scorm_file', '', 'id="scorm_file" class="form-control"') ?>
                    <p class="help-block"><i class="glyphicon glyphicon-warning-sign"></i> Suggested types = zip</p>
                </div>
            </div>
            <div class="form-group">
              <label class="col-xs-offset-3 col-sm-8 col-xs-offset-2 col-xs-9">
                  <p class="text-muted"><i class="glyphicon glyphicon-info-sign"> </i> All fields are Required.</p>
              </label>
            </div>
            <br/><hr/>
            <div class="row">
                <div class="col-xs-offset-1 col-xs-11 col-sm-offset-2 col-md-8">
                    <button type="submit" class="btn btn-primary col-xs-5 col-sm-3">Next</button>
                    <button type="reset" class="btn btn-warning col-xs-offset-1">Reset</button>
                </div>
            </div>
            <?=form_close(); ?>
        </div>
    </div>
    </div>
    </div>
</div>
<script>
$('select#parent-category').change(function() {

    var category = $(this).val();
    var link = '<?=base_url()?>'+'index.php/admin_control/get_subcategories_ajax/'+category;
    $.ajax({
        data: category,
        url: link
    }).done(function(subcategories) {
      $('#category').html(subcategories);
    });
});
</script>