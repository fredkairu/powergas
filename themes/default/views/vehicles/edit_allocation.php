
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">
            <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("vehicles/update_allocation/".$route->id."/".$day, $attrib); ?>
                <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$route->name?></span> ON <span style="color:red"><?=$actual_day?></span></h5>
                <ul id="srtb">
                    <?php if (!empty($allocations)) {
                        foreach ($allocations as $allocation) { ?>
                            <li class="ui-state-default" id="<?= $allocation->id ?>" style="margin:10px;list-style: none;"><p style="margin:10px;" ><?= strtoupper($allocation->shop_name." (".$allocation->customer_name.")"); ?><input id="allocation_ids" name="allocation_ids[]" type="hidden" value="<?= $allocation->id ?>" /><input id="shop_ids" name="shop_ids[]" type="hidden" value="<?= $allocation->shop_id ?>" /></p></li>
                        <?php }
                    } else {
                        echo "<li>No data available</li>";
                    } ?>
                </ul>

                <input style="margin-left:10px" class="btn btn-primary" type="submit" value="Submit"/>
            <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
