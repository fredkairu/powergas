<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_sales_person'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("customers/edit2/" . $customer->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="form-group">
                <?= lang("Vehicle", "vehicle_id") ?>
                <select class="form-control" name="vehicle_id" id="vehicle_id" required >
                    <?php

                    foreach ($vehicles as $vehicle) {
                        if($vehicle->id == $customer->vehicle_id){
                            echo '<option selected value="'.$vehicle->id.'" >'.$vehicle->plate_no.'</option>';
                        }else{
                            echo '<option value="'.$vehicle->id.'" >'.$vehicle->plate_no.'</option>';
                        }
                    }

                    ?>

                </select>
            </div>
            <!--
             <div class="form-group">
                <label class="control-label"
                       for="customer_group"><?php echo $this->lang->line("Company_Hierachy"); ?></label>

                <div class="controls"> <?php
                   $parent=array("0"=>"Parent","1"=>"Subsidiary");
                    echo form_dropdown('parent_subsidiary', $parent, $customer->is_subsidiary, 'class="form-control tip select" id="parent_subsidiary" style="width:100%;" required="required"');
                    ?>
                </div>
            </div>
            <div class="form-group">
                <label class="control-label"
                       for="customer_group"><?php echo $this->lang->line("default_customer_group"); ?></label>

                <div class="controls"> <?php
                    foreach ($customer_groups as $customer_group) {
                        $cgs[$customer_group->id] = $customer_group->name;
                    }
                    echo form_dropdown('customer_group', $cgs, $customer->customer_group_id, 'class="form-control tip select" id="customer_group" style="width:100%;" required="required"');
                    ?>
                </div>
            </div>-->
        

            <div class="row">
                <div class="col-md-6">

                    <?php
                    $names = explode(" ", $customer->name);
                    $first_name =  $names[0]; // piece1
                    $second_name = $names[1]; // piece2
                    ?>
                    <div class="form-group person">
                        <?= lang("first_name", "first_name"); ?>
                        <?php echo form_input('first_name', $first_name, 'class="form-control tip" id="first_name" data-bv-notempty="true"'); ?>
                    </div>
                    <div class="form-group person">
                        <?= lang("last_name", "last_name"); ?>
                        <?php echo form_input('last_name', $second_name, 'class="form-control tip" id="last_name" data-bv-notempty="true"'); ?>
                    </div>

                    <!--
                    <div class="form-group person">
                         <div class="form-group"  id="showparent">
                        <?= lang("Parent_Company", "Parent_company"); ?>
                     
                        <?php 
                         $ctryy["none"]="No Parent Company";
                        foreach($companies as $country){
                            $ctryy[$country->id]=$country->name;
                        }?>
                        <?php echo form_dropdown('parent_company',$ctryy,$customer->parent_company, 'class="form-control tip select" id="parent_country" style="width:100%;" required="required"'); ?>
                    </div>


                    <div class="form-group company">
                        <?= lang("Aliases Separated By Comma(,)", "Aliases"); ?>
                        <?php echo form_input('company', $customer->company, 'class="form-control tip" placeholder="Alias1,Alias2" id="company" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("vat_no", "vat_no"); ?>
                        <?php echo form_input('vat_no', $customer->vat_no, 'class="form-control" id="vat_no"'); ?>
                    </div>
                    <div class="form-group company">
                    <?= lang("contact_person", "contact_person"); ?>
                    <?php //echo form_input('contact_person', $customer->contact_person, 'class="form-control" id="contact_person" required="required"'); ?>
                </div> -->
                    <div class="form-group">
                        <?= lang("email_address", "email_address"); ?>
                        <input type="email" name="email" class="form-control"  id="email_address"
                               value="<?= $customer->email ?>"/>
                    </div>
                    <div class="form-group">
                        <?= lang("phone", "phone"); ?>
                        <input type="tel" name="phone" class="form-control"  id="phone"
                               value="<?= $customer->phone ?>"/>
                    </div>
                    <div class="form-group">
                        <?= lang("Can view stock", "stock"); ?>
                        <select class="form-control" name="stock" id="stock">
                            <option value="1">Yes</option>
                            <option value="0" selected>No</option>
                        </select>
                    </div>
                    <!--<div class="form-group">
                        <?= lang("state", "state"); ?>
                        <?php echo form_input('state', $customer->state, 'class="form-control" id="state"'); ?>
                    </div>-->

                </div>
                <div class="col-md-6">
                   <!-- <div class="form-group">
                        <?= lang("postal_code", "postal_code"); ?>
                        <?php echo form_input('postal_code', $customer->postal_code, 'class="form-control" id="postal_code"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("country", "country"); ?>
                        <?php foreach($countries as $country){
                            $ctry[$country->id]=$country->country;
                        }?>
                        <?php echo form_dropdown('country', $ctry,$customer->country, 'class="form-control tip select" id="country" style="width:100%;" required="required"'); ?>
                    </div>
                   <div class="form-group">
                        <?= lang("address", "address"); ?>
                        <?php echo form_input('address', $customer->address, 'class="form-control" id="address" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("city", "city"); ?>
                        <?php echo form_input('city', $customer->city, 'class="form-control" id="city" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("ccf1", "cf1"); ?>
                        <?php echo form_input('cf1', $customer->cf1, 'class="form-control" id="cf1"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("ccf2", "cf2"); ?>
                        <?php echo form_input('cf2', $customer->cf2, 'class="form-control" id="cf2"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("ccf3", "cf3"); ?>
                        <?php echo form_input('cf3', $customer->cf3, 'class="form-control" id="cf3"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("ccf4", "cf4"); ?>
                        <?php echo form_input('cf4', $customer->cf4, 'class="form-control" id="cf4"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("ccf5", "cf5"); ?>
                        <?php echo form_input('cf5', $customer->cf5, 'class="form-control" id="cf5"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("ccf6", "cf6"); ?>
                        <?php echo form_input('cf6', $customer->cf6, 'class="form-control" id="cf6"'); ?>
                    </div>-->
                </div>
            </div>
            <!--<div class="form-group">
                <?= lang('award_points', 'award_points'); ?>
                <?= form_input('award_points', set_value('award_points', $customer->award_points), 'class="form-control tip" id="award_points"  required="required"'); ?>
            </div>-->

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_sales_person', lang('edit_sales_person'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function (e) {
$("#parent_subsidiary").on("change",function(e){
            
           if($("#parent_subsidiary :selected").text().toLowerCase()==="subsidiary"){
               $("#showparent").attr("display","block");
           } 
           else{
              $("#showparent").attr("display","none");  
           }
        });
         });
         </script>
        
<?= $modal_js ?>
