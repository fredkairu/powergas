<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$distributor->name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/add_target/".$distributor->id, $attrib); ?>
        <div class="modal-body">

            <div class="form-group">
                <?= lang("Product", "product_id") ?>
                <?php
                $productsname=array();
                foreach ($products as $product) {
                    $productsname[$product->id] = $product->name;
                }
                echo form_dropdown('product_id', $productsname, (isset($_POST['product']) ? $_POST['product'] : ($product ? $product->id : '')), 'class="form-control select" id="product_id" placeholder="' . lang("select") . " " . lang("Product") . '" required="required" style="width:100%"')
                ?>
            </div>
            <div class="form-group">
                <?= lang("Target", "target") ?>
                <input id="target" type="number" name="target"  class="form-control">
            </div>
            <div class="form-group">
                <?= lang("Month", "month") ?>
                <select class="form-control" name="month">
                    <option value="1">Jan</option>
                    <option value="2">Feb</option>
                    <option value="3">Mar</option>
                    <option value="4">Apr</option>
                    <option value="5">May</option>
                    <option value="6">Jun</option>
                    <option value="7">Jul</option>
                    <option value="8">Aug</option>
                    <option value="9">Sep</option>
                    <option value="10">Oct</option>
                    <option value="11">Nov</option>
                    <option value="12">Dec</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_target', lang('Add_Target'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
