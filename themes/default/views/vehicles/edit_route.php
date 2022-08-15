<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$vehicle->plate_no?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("vehicles/edit_route/".$vehicle_route->id, $attrib); ?>
        <div class="modal-body">

            <div class="form-group">
                <?= lang("routes", "route_id") ?>
                <select class="form-control" name="route_id" id="route_id" required >
                    <?php

                    foreach ($routes as $route) {
                        if($vehicle_route->route_id == $route->id){
                            echo '<option selected value="'.$route->id.'" >'.$route->name.'</option>';
                        }else{
                            echo '<option value="'.$route->id.'" >'.$route->name.'</option>';
                        }
                    }

                    ?>

                </select>
            </div>
            <div class="form-group">
                <?= lang("Day", "day") ?>
                <select class="form-control" name="day">
                    <option value="1" <?php if($vehicle_route->day==1){ echo "selected";}?> >Mon</option>
                    <option value="2" <?php if($vehicle_route->day==2){ echo "selected";}?> >Tue</option>
                    <option value="3" <?php if($vehicle_route->day==3){ echo "selected";}?> >Wed</option>
                    <option value="4" <?php if($vehicle_route->day==4){ echo "selected";}?>>Thur</option>
                    <option value="5" <?php if($vehicle_route->day==5){ echo "selected";}?> >Fri</option>
                    <option value="6" <?php if($vehicle_route->day==6){ echo "selected";}?>>Sat</option>
                    <option value="7" <?php if($vehicle_route->day==7){ echo "selected";}?>>Sun</option>
                </select>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_route', lang('edit_route'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
