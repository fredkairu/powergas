
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_cluster'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("system_settings/edit_cluster/id/".$id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
<!--             <div class="form-group">
                <?//php echo lang('category_name', 'name'); ?>
                <div class="controls">
                    <?//php echo form_input($name); ?>
                </div>
            </div>-->
           
            <div class="form-group">
                <?php echo lang('Cluster_name*', 'name'); ?>
                <div class="controls">
                   <?php echo form_input('name', $cluster->name, 'class="form-control" id="cluster_name"'); ?>
                </div>
                
                   <?php echo form_hidden('id', $cluster->id, 'class="form-control" id="cluster_id"'); ?>
               
            </div>
           

        </div>
        <div class="modal-footer">
            <?php echo form_submit('update_cluster', lang('Update_cluster'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>