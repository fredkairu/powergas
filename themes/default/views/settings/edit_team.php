
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_Van'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("system_settings/edit_team/id/".$id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
           
            <div class="form-group">
                <?php echo lang('Van_name*', 'name'); ?>
                <div class="controls">
                   <?php echo form_input('name', $team->name, 'class="form-control" id="team_name"'); ?>
                </div>
                
                   <?php echo form_hidden('id', $team->id, 'class="form-control" id="team_id"'); ?>
               
            </div>
           <div class="form-group">
                        <?= lang("country", "country"); ?>
                        <?php foreach($countries as $country){
                            $ctry[$country->id]=$country->country;
                        }?>
                        <?php echo form_dropdown('country', $ctry,$team->country_id, 'class="form-control tip select" id="country" style="width:100%;" required="required"'); ?>
                    </div>
                    
                     <div class="form-group">
                        <?= lang("Business_Unit", "business_unit"); ?>
                         <?php foreach($bu as $bu1){
                            $arrbu[$bu1->business_unit]=$bu1->business_unit;
                        }?>
                        <?php echo form_dropdown('business_unit', $arrbu,$team->business_unit, 'class="form-control tip select" id="business_unit" style="width:100%;" required="required"'); ?>
    
                    </div>
       
        </div>
        <div class="modal-footer">
            <?php echo form_submit('update_team', lang('Update_Van'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>