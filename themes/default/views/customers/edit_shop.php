<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$customer_name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/edit_shop/".$distributor_customer_shop[0]->id, $attrib); ?>
        <input type="hidden" value="<?=$customer_id?>" name="customer_id" id="customer_id">
        <div class="modal-body">
            <div class="form-group">
                <?= lang("Shop Name", "shop_name") ?>
                <input id="shop_name" type="text" name="shop_name"  value="<?=$distributor_customer_shop[0]->shop_name?>" class="form-control">
            </div>
            <div class="form-group">
                <?= lang("Latitude", "lat") ?>
                <input id="ltd" type="text" name="lat" value="<?=$distributor_customer_shop[0]->lat?>" class="form-control">
            </div>
            <div class="form-group">
                <?= lang("Longitude", "lng") ?>
                <input id="lng" type="text" name="lng" value="<?=$distributor_customer_shop[0]->lng?>" class="form-control">
            </div>
            
            <div class="form-group">
                <?= lang("Day", "day") ?>
                <select class="form-control" name="day">
                    <option value="1" <?php if($distributor_customer_shop[0]->day==1){ echo "selected";}?> >Mon</option>
                    <option value="2" <?php if($distributor_customer_shop[0]->day==2){ echo "selected";}?> >Tue</option>
                    <option value="3" <?php if($distributor_customer_shop[0]->day==3){ echo "selected";}?> >Wed</option>
                    <option value="4" <?php if($distributor_customer_shop[0]->day==4){ echo "selected";}?>>Thur</option>
                    <option value="5" <?php if($distributor_customer_shop[0]->day==5){ echo "selected";}?> >Fri</option>
                    <option value="6" <?php if($distributor_customer_shop[0]->day==6){ echo "selected";}?>>Sat</option>
                    <option value="7" <?php if($distributor_customer_shop[0]->day==7){ echo "selected";}?>>Sun</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_shop', lang('edit_shop'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
