<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$customer_name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/add_payment_method/".$customer_id, $attrib); ?>
        <input type="hidden" value="<?=$customer_id?>" name="customer_id" id="customer_id">
        <div class="modal-body">
            <div class="form-group">
                <?= lang("add_payment_method", "type") ?>
                <?php
                $method = array();
                foreach ($payment_methods as $payment_method) {
                    $method[$payment_method->id] = $payment_method->name;
                }
                echo form_dropdown('payment_method_id', $method, (isset($_POST['payment_method_id']) ? $_POST['payment_method_id'] : ($payment_method ? $payment_method->id : '')), 'class="form-control select" id="add_payment_method" placeholder="' . lang("select") . " " . lang("add_payment_method") . '" required="required" style="width:100%"')
                ?>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_payment_method', lang('add_payment_method'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
