<script>
    $(document).ready(function () {
        var oTable = $('#GuestData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('vehicles/getRouteStartingPoints') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            }, null,null,null,null,{"mRender": row_status2}, {"bSortable": false}]
        }).dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('Shop Name');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('Day');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('Customer');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('Vehicle');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('Starting Point');?>]", filter_type: "text", data: []},
        ], "footer");
    });

</script>

<?php 
$conn = mysqli_connect("localhost","root","Trymenot#123$","techsava_powergas");
$current_date = date("Y-m-d").' '.'23:59:00';
 $day = 1;
 $vehicle_id = 13;
            // $stmt = $conn->prepare($query);
            // $stmt->bindParam(1, $current_date);
            // $stmt->bindParam(2, $current_date);
            // $stmt->bindParam(3, $vehicle_id);
            // $stmt->bindParam(4, $day);
            // $stmt->bindParam(5, $day);
            // $stmt->bindParam(6, $vehicle_id);
            // $stmt->execute();
$sql = "SELECT sma_customers.id as id,sma_allocation_days.id as allid, sma_customers.name, sma_customers.phone, sma_customers.active, sma_customers.email, sma_customers.customer_group_id, sma_customers.customer_group_name, sma_allocation_days.duration as durations,sma_allocation_days.position as positions,sma_shops.image as logo, sma_shops.shop_name, sma_shops.id as shop_id, sma_shops.lat, sma_shops.lng, sma_currencies.french_name as county_name, sma_cities.city as town_name,sma_cities.id as town_id
FROM   sma_shops
				left join sma_customers on sma_customers.id = sma_shops.customer_id
                left join sma_cities on sma_cities.id = sma_customers.city
                left join sma_currencies on sma_currencies.id = sma_cities.county_id
                left join sma_shop_allocations on sma_shop_allocations.shop_id = sma_shops.id 
                left join sma_vehicle_route on sma_shop_allocations.route_id=sma_vehicle_route.route_id
                left join sma_vehicles on sma_vehicle_route.vehicle_id = sma_vehicles.id
                left join sma_routes on sma_vehicle_route.route_id = sma_routes.id 
                left join sma_allocation_days on sma_allocation_days.allocation_id = sma_shop_allocations.id 
WHERE NOT EXISTS
  (SELECT *
   FROM   sma_sales
   WHERE  sma_shops.id = sma_sales.shop_id and sma_sales.date = CURRENT_DATE and sma_sales.created < '$current_date') 
   
AND NOT EXISTS
  (SELECT *
   FROM   sma_tickets
   WHERE  sma_shops.id = sma_tickets.shop_id and sma_tickets.date = CURRENT_DATE and sma_tickets.created_at < '$current_date') and 
   sma_vehicles.id = $vehicle_id and sma_customers.active = 1 and sma_allocation_days.day = $day and (sma_allocation_days.duration > 0 or sma_allocation_days.start_point = 1) and sma_allocation_days.active = 1 and sma_vehicle_route.day = $day and sma_allocation_days.salesman_id = $vehicle_id and 
   sma_allocation_days.expiry IS NULL or sma_allocation_days.expiry <= CURRENT_TIMESTAMP GROUP BY sma_shops.id ORDER BY sma_allocation_days.position ASC";

$result = mysqli_query($conn,$sql);
            $i=0;
            while($row=mysqli_fetch_assoc($result))
            {
                
                echo "<tr data-index='".$row['allid']."' data-position='".$row['positions']."'>";
                    echo "<td>".$row['allid']."</td>";
                    echo "<td>".$row['name']."</td>";
                    echo "<td>".$row['lat']."</td>";
                    echo "<td>".$row['lng']."</td>";  
                    echo "<td>".$row['shop_name']."</td>";
                    echo "<td>".$row['positions']."</td>";
                    // echo "<td>".$row['country']."</td>";
                    // echo "<td>".$row['latitude']."</td>";
                    // echo "<td>".$row['longitude']."</td>";
                    // echo "<td>".$row['name']."</td>";
                echo "</tr>";

                
            }
?>
<div class="table-responsive">
                    <table id="GuestData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang("shop"); ?></th>
                            <th><?= lang("Days Served"); ?></th>
                            <th><?= lang("Customer"); ?></th>
                            <th><?= lang("Vehicle"); ?></th>
                            <th><?= lang("Starting Point"); ?></th>
                            <th style="width:85px;"><?= lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                        
                            <th style="width:85px;" class="text-center"><?= lang("actions"); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <?php echo json_encode($vehicle)
            
            
            ?>

            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_vehicle') . " (" . $vehicle->plate_no . ")"; ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'edit-vehicle-form');
        echo form_open_multipart("vehicles/edit/" . $vehicle->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">
                        <?= lang("plate_no", "plate_no"); ?>
                        <input type="text" name="plate_no" class="form-control" id="plate_no" value="<?php echo $vehicle->plate_no ?>" required="required"/>
                    </div>

                    <div class="form-group">
                        <label for="discount_enabled" >Discount</label>
                        <select name="discount_enabled" class="form-control">
                            <?php
                                if($vehicle->discount_enabled=='Enabled'){
                                    echo '<option value="Enabled" selected >Enabled</option>';
                                    echo '<option value="Disabled" >Disabled</option>';
                                }else{
                                    echo '<option value="Enabled" >Enabled</option>';
                                    echo '<option value="Disabled" selected >Disabled</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_vehicle', lang('edit_vehicle'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function (e) {
        $('#edit-vehicle-form').bootstrapValidator({
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            }, excluded: [':disabled']
        });
        $('select.select').select2({minimumResultsForSearch: 6});
        fields = $('.modal-content').find('.form-control');
        $.each(fields, function () {
            var id = $(this).attr('id');
            var iname = $(this).attr('name');
            var iid = '#' + id;
            if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
                $("label[for='" + id + "']").append(' *');
                $(document).on('change', iid, function () {
                    $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
                });
            }
        });
    });

</script>