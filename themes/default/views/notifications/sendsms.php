
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="smsModalLabel"><?php echo lang('Send Message'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open("notifications/addSms", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            
            <div class="row">
                <div class="col-sm-6">
                    
                <div class="form-group">
                <?= lang("message", 'note'); ?>
                <?php echo form_textarea($note, (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="smsnote" required="required"'); ?>
            </div>
            </div>

                <div class="col-sm-6">
                    <div class="form-group">  
                        <?php echo lang("destination_number", 'destination_number'); ?>
                        <div class="controls">
                            <?php echo form_input('destination_number', '', 'class="form-control" id="destination_number" placeholder ="phone numbers separated by a comma," required="required"'); ?>
                        </div>
                    </div>
                </div>      
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_notification', lang('add_notification'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<script type="text/javascript" charset="UTF-8">
    $.fn.datetimepicker.dates['sma'] = <?=$dp_lang?>;
</script>