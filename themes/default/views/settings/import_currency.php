<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_county_and_currency'); ?></h4>
            
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("system_settings/import_csv", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?><a href="<?php echo base_url(); ?>assets/csv/countrycurrency.csv" class="btn btn-primary pull-right">Sample Template</a></p>
             <div class="form-group">
                <?= lang("Country_list", "country_list") ?>
                    <input type="file" name="userfile" class="form-control file" data-show-upload="false"
                                       data-show-preview="false" id="csv_file" required="required"/>
              
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_country', lang('Upload_Country&Currency'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
