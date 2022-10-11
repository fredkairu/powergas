<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close day_modal" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> TO SERVE SHOP <span style="color:#ff0000"><?= $allocation[0]->shop_name ?></span> ON ROUTE <span style="color:#ff0000"><?= $allocation[0]->route_name ?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/add_day/".$allocation[0]->id, $attrib);

        $customdays = array(

              array(
                'day' => 'Mon',
                "value" => '1',
            ),
              array(
                'day' => 'Tue',
                "value" => '2',
            ),
              array(
                'day' => 'Wed',
                "value" => '3',
            ),

              array(
                'day' => 'Thur',
                "value" => '4',
            ),
              array(
                'day' => 'Fri',
                "value" => '5',
            ),
              array(
                'day' => 'Sat',
                "value" => '6',
            ),

              array(
                'day' => 'Sun',
                "value" => '7',
            ),
          );

           

         ?>
        <div class="modal-body">
            <div class="form-group">
                <?= lang("Day", "day") ?>
                <br/>
                <?php foreach($customdays as $day) { 
// Test
                     if ($this->companies_model->myArrayContainsDay($day['day'],$allocation_days)) {   
                        $day_id = $this->companies_model->myArrayGetDay($day['day'],$allocation_days);
                        if ($day_id) {
                            echo $day_id;
                        
                        
                        ?>

                <input type="checkbox" checked="checked" class="form-control" name='day[]' value="<?php echo $day_id ?>"> <?php echo $day['day'] ?> <br/>


                <?php } } else{ ?>

                <input type="checkbox"  class="form-control" name='day[]' value="<?php echo $day['value'] ?>"> <?php echo $day['day'] ?> <br/>
        <?php } }?>
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
