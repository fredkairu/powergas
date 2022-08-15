<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_Employee'); ?></h4>
        </div>

        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
         echo form_open_multipart("customers/edit_employee/" . $employee->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
 <div class="form-group">
               
            </div>
            
            
            <!--<div class="form-group">
                <label class="control-label"
                       for="customer_group"><?php echo $this->lang->line("default_customer_group"); ?></label>

                <div class="controls"> <?php
                    foreach ($customer_groups as $customer_group) {
                        $cgs[$customer_group->id] = $customer_group->name;
                    }
                    echo form_dropdown('customer_group', $cgs, $this->Settings->customer_group, 'class="form-control tip select" id="customer_group" style="width:100%;" required="required"');
                    ?>
                </div>
            </div>-->

            <div class="row">
                <div class="col-md-6">
                   
                    <div class="form-group person">
                        <?= lang("name", "name"); ?>
                        <?php echo form_input('name', $employee->name, 'class="form-control tip" id="name" data-bv-notempty="true"'); ?>
                        <?php echo form_hidden('id', $employee->id, 'class="form-control" id="id"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("Employee_Group", "emp_grp") ?>
                        <?php
                        $optss = array('MSR' => lang('MSR'), 'DSM' => lang('DSM'));
                        echo form_dropdown('emp_grp', $optss, (isset($_POST['emp_grp']) ? $_POST['emp_grp'] : ($employee ? $employee->group_name : '')), 'class="form-control" id="emp_grp" required="required"');
                        ?>
                    </div>
                      <!-- <div class="form-group company">
                        <?= lang("company", "company"); ?>
                        <?php echo form_input('company', '', 'class="form-control tip" id="company" '); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("vat_no", "vat_no"); ?>
                        <?php echo form_input('vat_no', '', 'class="form-control" id="vat_no"'); ?>
                    </div>
                   <div class="form-group company">
                    <?= lang("contact_person", "contact_person"); ?>
                    <?php echo form_input('contact_person', '', 'class="form-control" id="contact_person" data-bv-notempty="true"'); ?>
                </div>-->
                    <div class="form-group">
                        <?= lang("Email", "email"); ?>
                        <?php echo form_input('email', $employee->email, 'class="form-control" id="email" type="email"'); ?>
                        
                        
                    </div>
                    <div class="form-group">
                        <?= lang("phone", "phone"); ?>

                        <?php echo form_input('phone', $employee->phone, 'class="form-control" id="phone" type="tel"'); ?>
                    </div>
                    
                    <!--<div class="form-group">
                        <?= lang("state", "state"); ?>
                        <?php echo form_input('state', '', 'class="form-control" id="state"'); ?>
                    </div>-->

                </div>
                <div class="col-md-6">
                  <!--  <div class="form-group">
                        <?= lang("postal_code", "postal_code"); ?>
                        <?php echo form_input('postal_code', '', 'class="form-control" id="postal_code"'); ?>
                    </div>-->
                     <div class="form-group">
                        <?= lang("country", "country"); ?>
                        <?php foreach($countries as $country){
                            $ctry[$country->id]=$country->country;
                        }?>
                        <?php echo form_dropdown('country', $ctry,$employee->country, 'class="form-control tip select" id="country" style="width:100%;" required="required"'); ?>
                    </div>
                    <?php if ($employee->group_name == "MSR") { ?>
                    <div class="form-group" id="msr_a">
                        <?= lang("MSR_Alignment", "msr_alignment"); ?>
                        
                        <?php
                        $temsr[$employee->alignment_id]=$employee->alignment_name;
                        $temsr['0'] ='-----';
                        foreach($msrs as $msr){
                            $temsr[$msr->id]=$msr->msr_alignment_name;
                        }?>
                        <?php echo form_dropdown('msr_alignment', $temsr,$employee->alignment_id, 'class="form-control tip select" id="msr_alignment" style="width:100%;" required="required"'); ?>
                    </div> <?php } elseif ($employee->group_name == "DSM")  { ?>
                    <div class="form-group"  id="dsm_a">
                        <?= lang("DSM_alignment", "dsm_alignment"); ?>
                        <?php
                        $temdsm[$employee->alignment_id]=$employee->alignment_name;
                        $temdsm['0'] = '--------';
                        foreach($dsms as $dsm){
                            $temdsm[$dsm->id]=$dsm->dsm_alignment_name;
                        }?>
                        <?php echo form_dropdown('dsm_alignment', $temdsm,$employee->alignment_id, 'class="form-control tip select" id="dsm_alignment" style="width:100%;" required="required"'); ?>
                    </div>
                 <?php } ?>
                    <div class="form-group">
                        <?= lang("city", "city"); ?>
                         <?php echo form_input('city', $employee->city, 'class="form-control" id="city"'); ?>
                    </div>
                    <!--
                    <div class="form-group">
                        <?= lang("ccf1", "cf1"); ?>
                        <?php echo form_input('cf1', '', 'class="form-control" id="cf1"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("ccf2", "cf2"); ?>
                        <?php echo form_input('cf2', '', 'class="form-control" id="cf2"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("ccf3", "cf3"); ?>
                        <?php echo form_input('cf3', '', 'class="form-control" id="cf3"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("ccf4", "cf4"); ?>
                        <?php echo form_input('cf4', '', 'class="form-control" id="cf4"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("ccf5", "cf5"); ?>
                        <?php echo form_input('cf5', '', 'class="form-control" id="cf5"'); ?>

                    </div>
                    <div class="form-group">
                        <?= lang("ccf6", "cf6"); ?>
                        <?php echo form_input('cf6', '', 'class="form-control" id="cf6"'); ?>
                    </div>-->
                </div>
            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_employee', lang('Update_Employee'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function (e) {
        $('#add-customer-form').bootstrapValidator({
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
        
        $("#name").keyup(function(e){
            $("#company").val($(this).val());
        });
        
        $("#parent_subsidiary").on("change",function(e){
            
           if($("#parent_subsidiary :selected").text().toLowerCase()=="subsidiary"){
               $("#showparent").attr("display","block");
           } 
           else{
              $("#showparent").attr("display","none");  
           }
        });
           $("#emp_grp").on("change",function(e){
            
           if($("#emp_grp").val() =="MSR"){
               document.getElementById("msr_a").style.display = "block";
               document.getElementById("dsm_a").style.display = "none";
           } 
           else{
             document.getElementById("msr_a").style.display = "none";
               document.getElementById("dsm_a").style.display = "block";
           }
        });
    });
</script>
