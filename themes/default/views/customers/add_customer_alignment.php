<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add Customer Alignment'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open("customers/add_customer_alignment", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

                       <div class="form-group" id="msr_a">
                      <label for="sf_alignment_name"><?php echo $this->lang->line("Sf Alignment"); ?></label>
                        
                        <?php
                        $temsr['0'] ='-----';
                        foreach($msrs as $msr){
                            $temsr[$msr->id]=$msr->msr_alignment_name;
                        }?>
                        <?php echo form_dropdown('sf_alignment_name', $temsr,'', 'class="form-control tip select" id="sf_alignment_name" style="width:100%;" required="required"'); ?>
                    </div>
         
            <div class="form-group">
                        <label for="customer_name"><?php echo $this->lang->line("Customer Name"); ?></label>
                        <?php foreach($companies as $company){
                            $ctry[$company->id]=$company->name;
                        }?>
                        <?php echo form_dropdown('customer_name', $ctry,'', 'class="form-control tip select" id="customer_name" style="width:100%;" required="required"'); ?>
                    </div>
            
           
      <div class="form-group">
                        <label for="products"><?php echo $this->lang->line("Products"); ?></label>

                        <?php foreach($products as $product){
                            $ctry[$product->id]=$product->name;
                        }?>
                        <?php echo form_dropdown('products', $ctry,'', 'class="form-control tip select" id="products" style="width:100%;" required="required"'); ?>
                    </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_alignment', lang('add alignment'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<?= $modal_js ?>