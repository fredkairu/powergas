<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('import_by_csv1'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("customers/import_employee_csv", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="well well-small">
                <a href="<?php echo base_url(); ?>assets/csv/employee.csv" class="btn btn-primary pull-right"><i
                        class="fa fa-download"></i> Download Sample File</a>
                <span class="text-warning"><?= lang("csv1"); ?></span><br/><?= lang("csv2"); ?> <span class="text-info">(<?= lang("name") . ', ' . lang("designation") . ', ' . lang("country") . ', ' . lang("phone") . ', ' . lang("city") ?>
                    )</span> <?= lang("csv3"); ?><br>
                <span class="text-success"><?= lang('first_4_required'); ?></span>
            </div>
            <div class="form-group">
                <?= lang("upload_file", "csv_file") ?>
                <input id="csv_file" type="file" name="csv_file" data-bv-notempty="true" data-show-upload="false"
                       data-show-preview="false" class="form-control file">
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('import1', lang('import1'), 'class="btn btn-primary"'); ?>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>