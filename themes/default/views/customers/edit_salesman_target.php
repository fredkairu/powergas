<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$sales_person->name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/edit_salesman_target/".$target->id, $attrib); ?>
        <div class="modal-body">

            <div class="form-group">
                <?= lang("Product", "product_id") ?>
                <select class="form-control" name="product_id" id="product_id" required >
                    <?php

                    foreach ($products as $product) {
                        if($target->product_id == $product->id){
                            echo '<option selected value="'.$product->id.'" >'.$product->name.'</option>';
                        }else{
                            echo '<option value="'.$product->id.'" >'.$product->name.'</option>';
                        }
                    }

                    ?>
                </select>
            </div>
            <div class="form-group">
                <?= lang("Target", "target") ?>
                <input id="target" type="number" name="target" value="<?php echo $target->target ?>" class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_target', lang('edit_target'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
