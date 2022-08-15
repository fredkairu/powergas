<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_product_pricing'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("system_settings/edit_country_pricing/" . $id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('update_info'); ?></p>

            <div class="form-group">
                <?php echo lang('Product_name', 'name'); ?>
                <div class="controls">
                    <?php echo form_input($name); ?>
                </div>
            </div>

            <div class="form-group" style="display:none">
                <?php echo lang('Unified_price', 'unified_price'); ?>
                <div class="controls">
                    <?php echo form_input($unifiedprice); ?>
                </div>
            </div>
             <div class="form-group" >
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
            
            <div class="form-group">
                <?php echo lang('Tender Price', 'tender_price'); ?>
                <div class="controls">
                    <?php echo form_input($tenderprice); ?>
                </div>
            </div>
            <div class="form-group">
                     <?= lang("Customer", "ssocustomer"); ?>
                                                <?php
                                                $ssoCust['0'] = "";
                                               
                        foreach ($sanoficustomer as $assoCust) {
                            $country= $this->settings_model->getCurrencyByID($assoCust->country);
                            $ssoCust[$assoCust->id] = $assoCust->name.'  ('.$country->country.')';
                        }
                      // print_r($pricedetails);
                             echo form_dropdown('ssocustomer', $ssoCust,(isset($_POST['ssocustomer']) ? $_POST['ssocustomer'] : ($pricedetails ? $pricedetails->customer_id : '')), 'id="ssocustomer" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("Customer") . '"  ');
                                                ?>
                                            </div>
                                            <div class="form-group">
                                                <?= lang("customer", "slcustomer"); ?>
                                                <?php
                                                $SIdist['0'] = " ";
                                               
                        foreach ($distrib as $aSIdist) {
                            $country= $this->settings_model->getCurrencyByID($aSIdist->country);
                            $SIdist[$aSIdist->id] = $aSIdist->name.'  ('.$country->country.')';
                        }
                         echo form_dropdown('customer', $SIdist,(isset($_POST['customer']) ? $_POST['customer'] : ($pricedetails ? $pricedetails->distributor_id : '')), 'id="slcustomer" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("Distributor") . '"  ');
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
                    <?php echo form_input($promotion); ?>
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
            <?php echo form_hidden('id', $id); ?>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_country_pricing', lang('Edit_country_pricing'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>