<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="GuestData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang("ID"); ?></th>
                            <th><?= lang("Name"); ?></th>
                            <th><?= lang("Latitude"); ?></th>
                            <th><?= lang("Longitude"); ?></th>
                            <th><?= lang("Shop"); ?></th>
                            <th style="width:85px;"><?= lang("position"); ?></th>
                            <th style="width:85px;">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                         <?php
                         foreach($myroutes as $row)
                         {
                             echo "<tr data-index='".$row->allid."' data-position='".$row->positions."'>";
                             echo "<td></td>";
                             echo "<td>".$row->allid."</td>";
                             echo "<td>".$row->name."</td>";
                             echo "<td>".$row->lat."</td>";
                             echo "<td>".$row->lng."</td>";
                             echo "<td>".$row->shop_name."</td>";
                             echo "<td>".$row->positions."</td>";
                             echo "<td><a href='".site_url('vehicles/disabletemporary/'. $row->allid .'/'. $dayNo .'/'. $vehicle_id .'')."'>Disable</a></td>";
                             echo "</tr>";
                         }
     
                        ?>
                      
                        </tbody>
    
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
        $(document).ready(function(){
        $('table tbody').sortable({
           
            update:function(event,ui)
            {
            $(this).children().each(function(index){
                if($(this).attr('data-position') != (index+1))
                {
                    $(this).attr('data-position',(index+1)).addClass('updated');
                }
            });

            saveNewPositions();
            }
        });

        function saveNewPositions()
        {
            var positions = [];

            $('.updated').each(function(){
                positions.push([$(this).attr('data-index'),$(this).attr('data-position')]);
                $(this).removeClass('updated');
            })

            $.ajax({
                url: "vehicles/updatePosition/",
                method: "POST",
                dataType: "text",
                data: {
                    update :1,
                    positions :positions
                },success:function(response)
                {
                    console.log(response);
                }
                
            })
        }
    });
  
</script>
<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>
<?php if ($action && $action == 'add') {
    echo '<script>$(document).ready(function(){$("#add").trigger("click");});</script>';
}
?>
	

