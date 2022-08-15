<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_Exchange_Rate'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open("system_settings/add_conversion", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            
            
            
             <div class="form-group">
                <div class="controls"> 
                
                                                     <?= lang("Currency_code", "currency_codee"); ?>
                                <?php $pst = array('USD' => lang('USD'));
                                echo form_dropdown('currency_codee', $pst, '', 'class="form-control input-tip" required="required" id="currency_codee"'); ?>
                </div></div>

            
            <div class="form-group">
                <label class="control-label" for="exchange_rate"><?php echo $this->lang->line("exchange_rate"); ?></label>

                <div
                    class="controls"> <?php echo form_input('exchange_rate', '', 'class="form-control" id="exchange_rate" required="required"'); ?> </div>
            </div>
           <div class="form-group">
                                <?= lang("Month", "csmonth"); ?>
                                <input type="text" placeholder="mm-yyyy"  class="form-control input-tip datepicker monthPicker"  id="csmonth" name="csmonth" required="required" value="<?= date('m-Y');?>" autocomplete="off">

                            </div>
            
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_conversion', lang('Add_Exchange_Rate'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
