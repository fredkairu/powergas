<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$customer_name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/add_shop/".$customer_id, $attrib); ?>
        <input type="hidden" value="<?=$customer_id?>" name="customer_id" id="customer_id">
        <div class="modal-body">
            <div class="form-group">
                <?= lang("Shop Name", "shop_name") ?>
                <input id="shop_name" type="text" name="shop_name"  class="form-control">
            </div>
            <div class="form-group">
                <?= lang("Latitude", "lat") ?>
                <input id="ltd" type="text" name="lat"  class="form-control">
            </div>
            <div class="form-group">
                <?= lang("Longitude", "lng") ?>
                <input id="lng" type="text" name="lng"  class="form-control">
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_shop', lang('add_shop'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
