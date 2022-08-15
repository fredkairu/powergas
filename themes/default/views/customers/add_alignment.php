<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add Alignment'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open("customers/add_alignment", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <label for="name"><?php echo $this->lang->line("Name of Alignment"); ?></label>

                <div
                    class="controls"> <?php echo form_input('alignment_name', '', 'class="form-control" id="name" required="required"'); ?> </div>
            </div>
            
            <div class="form-group">
                <label for="name"><?php echo $this->lang->line("Alignment Rep"); ?></label>

                <div
                    class="controls"> <?php echo form_input('alignment_rep', '', 'class="form-control" id="alignment_rep" required="required"'); ?> </div>
            </div>
            
            
            <div class="form-group">
                <label for="percent"><?php echo $this->lang->line("Alignment Region"); ?></label>

                <div
                    class="controls"> <?php echo form_input('region', '', 'class="form-control" id="region" required="required"'); ?> </div>
            </div>
            
              <div class="form-group">
                <label for="percent"><?php echo $this->lang->line("Alignment Country"); ?></label>

                <div
                    class="controls"> <?php echo form_input('country', '', 'class="form-control" id="country" required="required"'); ?> </div>
            </div>
            
                     <div class="form-group">
                <label for="percent"><?php echo $this->lang->line("Period"); ?></label>

                <div
                    class="controls"> <?php echo form_input('period', '', 'class="form-control" id="period" required="required"'); ?> </div>
            </div>
            
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_alignment', lang('add alignment'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>