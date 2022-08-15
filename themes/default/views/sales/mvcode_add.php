<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_MovementCode'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-customer-form');
        echo form_open_multipart("sales/addMovementCode",$attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

             <div class="form-group">
                <label class="control-label"
                       for="customer_group"><?php echo $this->lang->line("Company_Hierachy"); ?></label>

 
            </div>
          

            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <?= lang("Movement_Code", "Movement_Code"); ?>
                        <input type="text" name="m_code" class="form-control"  id="m_code"
                               value="<?= $movement->m_code ?>"/>
                    </div>
                    <div class="form-group">
                        <?= lang("movement_name", "movement_name"); ?>
                        <input type="text" name="movement_name" class="form-control"  id="movement_name"
                               value="<?= $movement->movement_name ?>"/>
                    </div>
                    

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("pl", "pl"); ?>
                        <input type="text" name="pl" class="form-control"  id="pl"
                               value="<?= $movement->pl ?>"/>
                    </div>
                   <div class="form-group">
                         <?= lang("scenario", "scenario"); ?>
                        <input type="text" name="scenario" class="form-control"  id="scenario"
                               value="<?= $movement->scenario ?>"/>
                    </div>
                    
                    
                </div>
            </div>
           
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_mvcode', lang('Add_MovementCode'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
        
<?= $modal_js ?>
