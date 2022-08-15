<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/handle_ticket/".$id, $attrib); ?>
        <div class="modal-body">
            <div class="form-group">
                <?= lang("Feedback", "feedback") ?>
                <textarea id="feedback" type="number" name="feedback"  class="form-control"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_handle_ticket', lang('Submit'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
