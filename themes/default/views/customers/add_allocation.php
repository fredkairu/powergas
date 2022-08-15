<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR SHOP <span style="color:red"><?=$shop->shop_name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/add_allocation/".$shop->id, $attrib); ?>
        <input type="hidden" value="<?=$shop->id?>" name="shop_id" id="shop_id">
        <div class="modal-body">
            <div class="form-group">
                <?= lang("Route", "route_id") ?>

                <?php
                $routename=array();
                foreach ($routes as $route) {
                    $routename[$route->id] = $route->name;
                }
                echo form_dropdown('route_id', $routename, (isset($_POST['route']) ? $_POST['route'] : ($route ? $route->id : '')), 'class="form-control select" id="route_id" placeholder="' . lang("select") . " " . lang("Routes") . '" required="required" style="width:100%"')
                ?>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_allocation', lang('add_allocation'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
