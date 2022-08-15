<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_convrate'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open("system_settings/edit_convrate/" . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            
            
            
             <div class="form-group">
                <div class="controls"> 
                
                                                     <?= lang("Currency_code", "edi_currency_codee"); ?>
                                <?php $pst = array('USD' => lang('USD'));
                                echo form_dropdown('edi_currency_codee', $pst, '', 'class="form-control input-tip" required="required" id="edi_currency_codee"'); ?>
                </div></div>

            
            <div class="form-group">
                <label class="control-label" for="exchange_rate"><?php echo $this->lang->line("exchange_rate"); ?></label>

                <div
                    class="controls"> 
                   
                    <?php echo form_input('edi_exchange_rate', $exchange_rate->conversion_rate, 'class="form-control" id="edi_exchange_rate" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="edi_csmonth"><?php echo $this->lang->line("Month"); ?></label>

                <div
                    class="controls"> 
                   
                    <?php echo form_input('edi_csmonth', date('m-Y',strtotime($exchange_rate->month)), 'class="form-control" id="edi_csmonth" required="required" autocomplete="off"'); ?> </div>
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_currency', lang('edit_currency'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>