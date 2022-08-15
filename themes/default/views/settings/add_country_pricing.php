<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_product_pricing'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("system_settings/add_country_pricing/" . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('add_info'); ?></p>

            <div class="form-group">
                <?php echo lang('Product_name', 'product_id'); ?>
                <div class="controls">
                  
				   <?php echo form_dropdown('product_id', $product_ids, '', 'class="form-control select" id="product_id" placeholder="' . lang("select") . " " . lang("Product") . '" required="required" style="width:100%"'); ?>

                </div>
            </div>

            <div class="form-group" style="display:none" >
                <?php echo lang('Unified_price', 'unified_price'); ?>
                <div class="controls" >
                    <?php echo form_input($unifiedprice); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo lang('Supply_price', 'supply_price'); ?>
                <div class="controls">
                    <?php echo form_input($supplyprice); ?>
                </div>
            </div>
            <div class="form-group">
                <?php echo lang('Resale_price', 'resell_price'); ?>
                <div class="controls">
                    <?php echo form_input($resellprice); ?>
                </div>
            </div>
            
            <input type="hidden" name="thiscountry" value="<?=$id?>">
             
            
           <div class="form-group">
                <?php echo lang('Tender Price', 'tender_price'); ?>
                <div class="controls">
                    <?php echo form_input($tenderprice); ?>
                </div>
            </div>
            <div class="form-group">
                     <?= lang("Customer", "ssocustomer"); ?>
                                                <?php
                                                $ssoCust[''] = " ";
                                               
                        foreach ($sanoficustomer as $assoCust) {
                            $country= $this->settings_model->getCurrencyByID($assoCust->country);
                            $ssoCust[$assoCust->id] = $assoCust->name.'  ('.$country->country.')';
                        }
                             echo form_dropdown('ssocustomer', $ssoCust,($_POST['ssocustomer']), 'id="ssocustomer" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("Customer") . '"  ');
                                                ?>
                                            </div>
                                            <div class="form-group">
                                                <?= lang("customer", "slcustomer"); ?>
                                                <?php
                                                $SIdist[''] = " ";
                                               
                        foreach ($distrib as $aSIdist) {
                            $country= $this->settings_model->getCurrencyByID($aSIdist->country);
                            $SIdist[$aSIdist->id] = $aSIdist->name.'  ('.$country->country.')';
                        }
                             echo form_dropdown('customer', $SIdist,($_POST['customer']), 'id="slcustomer" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("Distributor") . '"  ');
                                                ?>
                                            </div>
            <div class="form-group">
                <?php echo lang('Special Resale Price', 'sp_resale_price'); ?>
                <div class="controls">
                    <?php echo form_input($sp_resellprice); ?>
                </div>
            </div>
             <div class="form-group">
                <?php echo lang('Special Tender Price', 'sp_tender_price'); ?>
                <div class="controls">
                    <?php echo form_input($sp_tenderprice); ?>
                </div>
            </div>
            
            <div class="form-group">
                <?php echo lang('Promotion', 'promotion'); ?>
                <div class="controls">
                   <?php echo form_dropdown('promotion',$promotion,'class="form-control" style="width:100% !important" id="promotion" required="required"');?>
                </div>
            </div>
           <div class="form-group">
                <?php echo lang('Price_effective_from', 'from_date'); ?>
                <div class="controls">
                    <?php echo form_input($fromdate); ?>
                </div>
            </div> 
            
            <div class="form-group">
                <?php echo lang('Price_effective_to', 'to_date'); ?>
                <div class="controls">
                    <?php echo form_input($todate); ?>
                </div>
            </div> 
            <?php echo form_hidden('country', $country); ?>
            <?php echo form_hidden('id', $id); ?>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_country_pricing', lang('Add_country_pricing'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>

<script>
   
    $(document).ready(function () {

     $("#product_id").select2();
      
   });
</script>
