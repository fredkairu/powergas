<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$product->name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("products/edit_discount/".$product->id, $attrib); ?>
        <div class="modal-body">

            <div class="form-group">
                <?= lang("Range From", "range_from") ?>
                <input id="range_from" type="number" name="range_from" value="<?php echo $product_discount->range_from ?>" class="form-control">
            </div>
            <div class="form-group">
                <?= lang("Range To", "range_to") ?>
                <input id="range_to" type="number" name="range_to" value="<?php echo $product_discount->range_to ?>" class="form-control">
            </div>
            <div class="form-group">
                <?= lang("Discount", "discount") ?>
                <input id="discount" type="number" name="discount" value="<?php echo $product_discount->discount ?>" class="form-control">
            </div>
            <div class="form-group">
                <?= lang("Loyalty Points", "loyalty_points") ?>
                <input id="loyalty_points" type="number" name="loyalty" value="<?php echo $product_discount->loyalty ?>" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_discount', lang('edit_discount'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
