<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close day_modal" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> TO SERVE SHOP <span style="color:#ff0000"><?= $allocation[0]->shop_name ?></span> ON ROUTE <span style="color:#ff0000"><?= $allocation[0]->route_name ?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/add_day/".$allocation[0]->id, $attrib); ?>
        <div class="modal-body">
            <div class="form-group">
                <?= lang("Day", "day") ?>
                <select class="form-control" name="day">
                    <option value="1">Mon</option>
                    <option value="2">Tue</option>
                    <option value="3">Wed</option>
                    <option value="4">Thur</option>
                    <option value="5">Fri</option>
                    <option value="6">Sat</option>
                    <option value="7">Sun</option>
                </select>
            </div>
            <div class="form-group">
                <label for="expiry">Expiry</label><br>
                <input type="datetime-local" name="expiry">
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_day', lang('add_day'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
