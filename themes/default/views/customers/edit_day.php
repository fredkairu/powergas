<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close day_modal" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> TO SERVE SHOP<span style="color:#ff0000"><?= $allocation_day[0]->shop_name ?></span> ON ROUTE <span style="color:#ff0000"><?= $allocation[0]->route_name ?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/edit_day/".$allocation_day[0]->id, $attrib); ?>
        <div class="modal-body">
            <div class="form-group">
                <?= lang("Day", "day") ?>
                <select class="form-control" name="day">
                    <option value="1" <?php if($allocation_day[0]->day==1){ echo "selected";}?> >Mon</option>
                    <option value="2" <?php if($allocation_day[0]->day==2){ echo "selected";}?> >Tue</option>
                    <option value="3" <?php if($allocation_day[0]->day==3){ echo "selected";}?> >Wed</option>
                    <option value="4" <?php if($allocation_day[0]->day==4){ echo "selected";}?> >Thur</option>
                    <option value="5" <?php if($allocation_day[0]->day==5){ echo "selected";}?> >Fri</option>
                    <option value="6" <?php if($allocation_day[0]->day==6){ echo "selected";}?> >Sat</option>
                    <option value="7" <?php if($allocation_day[0]->day==7){ echo "selected";}?> >Sun</option>
                </select>
            </div>
            <div class="form-group">
                <label for="expiry">Expiry</label><br>
                <input type="datetime-local" name="expiry" value="<?= $allocation_day[0]->expiry ?>">
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_day', lang('edit_day'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
