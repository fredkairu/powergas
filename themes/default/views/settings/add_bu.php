
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_bu'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("system_settings/add_bu", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
<!--             <div class="form-group">
                <?//php echo lang('category_name', 'name'); ?>
                <div class="controls">
                    <?//php echo form_input($name); ?>
                </div>
            </div>-->
             <div class="form-group all">
                        <?= lang("BU_name", "name") ?>
                        <?php
                        //$cat[''] = "";
                       // foreach ($categories as $categor) {
                        //    $cat[$categor->category_id] = $categor->name;
                       // }
                      // echo form_dropdown('name', $cat, (isset($_POST['category']) ? $_POST['category'] : ($category ? $category->name : '')), 'class="form-control select" id="name" placeholder="' . lang("select") . " " . lang("category") . '" required="required" style="width:100%"');
                      //  ?>
                       <input id="name" type="text" name="name" class="form-control input" required="required" style="width:100%"> 
                    </div>
            
<div class="form-group">
                <label class="control-label" for="cluster"><?php echo $this->lang->line("Status"); ?></label>

                <div class="controls"> 
                      <?php 
                      $gbu=array("Y"=>"Active","N"=>"Inactive");
                       echo form_dropdown('active', $gbu, (isset($_POST['active']) ? $_POST['active'] : ""), 'class="form-control select" id="name" placeholder="' . lang("select") . " " . lang("cluster") . '" required="required" style="width:100%"');?>
                   
                </div></div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_business_unit', lang('add_business_unit'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>