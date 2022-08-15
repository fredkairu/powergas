<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR CUSTOMER <span style="color:red"><?=$customer->name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/edit_limit/".$limit->id, $attrib); ?>
        <input type="hidden" value="<?=$customer->id?>" name="customer_id" id="customer_id">
        <div class="modal-body">
            <div class="form-group">
                <?= lang("Cash Limit", "limit") ?>
                <input id="cash_limit" type="number" name="cash_limit"  value="<?= $limit->cash_limit ?>" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_limit', lang('Edit_Limit'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
