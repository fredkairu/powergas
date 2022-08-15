<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Add_Sales_Person'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-customer-form');
        echo form_open_multipart("customers/add2", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("Vehicle", "vehicle_id") ?>
                        <?php
                        $vehicleplate[''] = "";
                        foreach ($vehicles as $vehicle) {
                            $vehicleplate[$vehicle->id] = $vehicle->plate_no;
                        }
                        echo form_dropdown('vehicle_id', $vehicleplate, (isset($_POST['vehicle']) ? $_POST['vehicle'] : ($vehicle ? $vehicle->id : '')), 'class="form-control select" id="vehicle_id" placeholder="' . lang("select") . " " . lang("vehicle") . '" required="required" style="width:100%"')
                        ?>
                    </div>
                    <div class="form-group person">
                        <?= lang("first_name", "first_name"); ?>
                        <?php echo form_input('first_name', '', 'class="form-control tip" id="first_name" data-bv-notempty="true"'); ?>
                    </div>
                    <div class="form-group person">
                        <?= lang("last_name", "last_name"); ?>
                        <?php echo form_input('last_name', '', 'class="form-control tip" id="last_name" data-bv-notempty="true"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("email_address", "email_address"); ?>
                        <input type="email" name="email" class="form-control" r id="email_address"/>
                    </div>
                    <div class="form-group">
                        <?= lang("phone", "phone"); ?>
                        <input type="tel" name="phone" class="form-control" id="phone"/>
                    </div>
                    <div class="form-group">
                        <?= lang("Can view stock", "stock"); ?>
                        <select class="form-control" name="stock" id="stock">
                            <option value="1">Yes</option>
                            <option value="0" selected>No</option>
                        </select>
                    </div>
                </div>

            </div>


        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_sales_person', lang('Add_Sales_Person'), 'class="btn btn-primary"'); ?>
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
    });
</script>
