<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i>Routes Summary</h2>

      
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext">Route Summary</p>
                <div class="table-responsive">
                    <table id="CusData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            
        
                            <th><?= lang("Customer"); ?></th>
                            <th><?= lang("Shops"); ?></th>
                            <th><?= lang("Days Served"); ?></th>
                            <th><?= lang("Vehicle"); ?></th>

                            <!-- <th style="width:40px;"><?= lang("actions"); ?></th> -->
                        </tr>
                        </thead>
                        <tbody>
                            <?php 
                            foreach($customers as $customer){
                            ?>
                            <tr>
                                <td><?php echo $customer->name; ?></td>
                                <td><?php
                                $shops = $this->companies_model->getShopsForCustomer($customer->id);
                                  
                                 foreach($shops as $shop){
                                    echo "<span>".$shop->shop."</span></br>";
                                 }

                             ?></td>
                                <td><?php 
                                $shops = $this->companies_model->getShopsForCustomer($customer->id);
                                 // echo json_encode($shops); 
                                 foreach($shops as $shop){
                                    echo "<span>".$shop->shop."</span></br>";
                                    $shopallocs = $this->companies_model->getShopAllocations($shop->id);
                                    foreach($shopallocs as $shopalloc){
                                        $days = $this->companies_model->getAllocationDays($shopalloc->id);
                                         // echo json_encode($days);
                                         foreach($days as $day){
                                          echo "<span>".$day->day.", </span></br>";
                                      }
                                    }
                                   
                                    
                                 }

                                 ?>
                                     
                                 </td>
                                <td>
                                    <?php 
                                $shops = $this->companies_model->getShopsForCustomer($customer->id);
                                 // echo json_encode($shops); 
                                 foreach($shops as $shop){
                                    echo "<span>".$shop->shop."</span></br>";
                                    $shopallocs = $this->companies_model->getShopAllocations($shop->id);
                                    foreach($shopallocs as $shopalloc){
                                    // echo json_encode($shopalloc);
                                     echo "<span>".$shopalloc->route_name.",  </span></br>";
                                     }
                                 }

                                 ?>

                                </td>
                            </tr>

                            
                          <?php } ?>
                        </tbody>
            
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>
<?php if ($action && $action == 'add') {
}
?>
	

