<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$distributor->name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/edit_target/".$target->id, $attrib); ?>
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
            <div class="form-group">
                <?= lang("Month", "month") ?>
                <select class="form-control" name="month" required>
                    <option value="1" <?php if($target->month=="1"){ echo "selected"; } ?> >Jan</option>
                    <option value="2" <?php if($target->month=="2"){ echo "selected";} ?> >Feb</option>
                    <option value="3" <?php if($target->month=="3"){ echo "selected";} ?> >Mar</option>
                    <option value="4" <?php if($target->month=="4"){ echo "selected";} ?> >Apr</option>
                    <option value="5" <?php if($target->month=="5"){ echo "selected";} ?> >May</option>
                    <option value="6" <?php if($target->month=="6"){ echo "selected";} ?> >Jun</option>
                    <option value="7" <?php if($target->month=="7"){ echo "selected";} ?> >Jul</option>
                    <option value="8" <?php if($target->month=="8"){ echo "selected";} ?> >Aug</option>
                    <option value="9" <?php if($target->month=="9"){ echo "selected";} ?> >Sep</option>
                    <option value="10" <?php if($target->month=="10"){ echo "selected";} ?> >Oct</option>
                    <option value="11" <?php if($target->month=="1"){ echo "selected";} ?> >Nov</option>
                    <option value="12" <?php if($target->month=="12"){ echo "selected";} ?> >Dec</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_target', lang('edit_target'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
