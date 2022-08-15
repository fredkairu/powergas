<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$vehicle->plate_no?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("vehicles/add_route/".$vehicle->id, $attrib); ?>
        <div class="modal-body">

            <div class="form-group">
                <?= lang("Route", "route_id") ?>
                <?php
                $routenames=array();
                foreach ($routes as $route) {
                    $routenames[$route->id] = $route->name;
                }
                echo form_dropdown('route_id', $routenames, (isset($_POST['route']) ? $_POST['route'] : ($route ? $route->id : '')), 'class="form-control select" id="route_id" placeholder="' . lang("select") . " " . lang("route_name") . '" required="required" style="width:100%"')
                ?>
            </div>
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
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_route', lang('add_route'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>
