  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script type="text/javascript">
    var count = 1, an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>, DC = '<?= $default_currency->code ?>', shipping = 0,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>, poitems = {},
        audio_success = new Audio('<?= $assets ?>sounds/sound2.mp3'),
        audio_error = new Audio('<?= $assets ?>sounds/sound3.mp3');
    $(document).ready(function () {
        <?php if($this->input->get('supplier')) { ?>
        if (!localStorage.getItem('poitems')) {
            localStorage.setItem('posupplier', <?=$this->input->get('supplier');?>);
        }
        <?php } ?>
        <?php if ($Owner || $Admin) { ?>
//        if (!localStorage.getItem('podate')) {
//            $("#podate").datetimepicker({
//                format: site.dateFormats.js_ldate,
//                fontAwesome: true,
//                language: 'sma',
//                weekStart: 1,
//                todayBtn: 1,
//                autoclose: 1,
//                todayHighlight: 1,
//                startView: 2,
//                forceParse: 0
//            }).datetimepicker('update', new Date());
//        }
        $(document).on('change', '#podate', function (e) {
            localStorage.setItem('podate', $(this).val());
        });
        if (podate = localStorage.getItem('podate')) {
            $('#podate').val(podate);
        }
        <?php } ?>
        $('#extras').on('ifChecked', function () {
            $('#extras-con').slideDown();
        });
        $('#extras').on('ifUnchecked', function () {
            $('#extras-con').slideUp();
        });
        //warehouse change
        $('#powarehouse').change(function(e){
       if($("#powarehouse :selected").text()=="SSO"){
  $(".text-info").html("(<?= lang("distributor_product_name") . ', '. lang("quantity")    . ', ' . lang("expiry");?>)");
  $("#stocksample").attr("href","<?php echo $this->config->base_url();?>assets/csv/ssostock.csv");
       } 
    });    
    });
</script>
<style>

.ui-datepicker .ui-datepicker-next{
    display: none;
}
.ui-datepicker .ui-datepicker-prev span
{
    display: none;
}
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_purchase_by_csv'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('role' => 'form', 'class' => 'edit-po-form','autocomplete'=>"off");//'data-toggle' => 'validator'
                echo form_open_multipart("purchases/purchase_by_csv", $attrib)
                ?>

                <div class="row">
                    <div class="col-lg-12">

                        <?php if ($Owner || $Admin) { ?>
                        <div class="col-md-1">
                               <div class="form-group">
                                    <?= lang("Use_Actual_Values?", "actual_values"); ?><br>
                <input type="checkbox" class="checkbox" name="actual_values" id="actual_values">
            </div>
                                        </div>
                        <div class="col-md-3" id="datediv">
                                <div class="form-group">
                                    <?= lang("date", "podate"); ?>
                                    <?php echo form_input('date',date('Y'), 'class="form-control input-tip datepicker monthPicker" required="required" id="podate1"'); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-md-4" style="display:none">
                            <div class="form-group">
                                <?= lang("reference_no", "poref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $ponumber), 'class="form-control input-tip" id="poref"'); ?>
                            </div>
                        </div>
                        <div class="col-md-4" id="typediv">
                            <div class="form-group">
                                <?= lang("warehouse", "powarehouse"); ?>
                                <?php
                                //$wh[''] = 'Select Stock Type';
                              
                                foreach ($warehouses as $warehouse) {
                                    if(strtolower($warehouse->name)=="sso"){
                                    $wh[$warehouse->id] = $warehouse->name;
                                    }
                                }
                                echo form_dropdown('warehouse', $wh, $_POST['warehouse'], 'id="powarehouse" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                         <!--<div class="col-md-4">
                           <div class="form-group">
                                <?= lang("status", "postatus"); ?>
                                <?php
                                $post = array('received' => lang('received'), 'pending' => lang('pending'), 'ordered' => lang('ordered'));
                                echo form_dropdown('status', $post, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("status") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>-->
                         <div class="col-md-4" style="display:none">
                            <div class="form-group">
                                <?= lang("customer", "posupplier"); ?>
                                
                                <div class="input-group">
                                
                                     <?php
                                     $cp['']='Select Distributor';
                                      foreach ($companies as $comp) {
                                         $country= $this->settings_model->getCurrencyByID($comp->country);
                                    $cp[$comp->id] = $comp->name."(".$country->country.")";
                                }
                                     echo form_dropdown('supplier', $cp, $_POST['supplier'], 'id="supplier_id" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '"  style="width:100%;" ');?>

                                    <div class="input-group-addon no-print" style="padding: 2px 5px;"><a
                                            href="<?= site_url('suppliers/add'); ?>" id="add-customer" class="external"
                                            data-toggle="modal" data-target="#myModal"><i
                                                class="fa fa-2x fa-plus-circle" id="addIcon"></i></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="stocksso">
                        <div class="clearfix"></div>
                        <div class="well well-sm">
                          
                
                            <a id="stocksample" href="<?php echo $this->config->base_url(); ?>assets/csv/stock_sso.csv" class="btn btn-primary pull-right"><i class="fa fa-download"></i> SSO Stock Sample</a>
                            <span class="text-warning"><?php echo $this->lang->line("csv1"); ?></span><br>
                            <?php echo $this->lang->line("csv2"); ?> <span class="text-info">(<?= lang("product_code") . ', '. lang("quantity")    . ', '.lang("expiry"); ?>
                                )</span> <?php echo $this->lang->line("csv3"); ?><br>
                            <?= lang('first_3_are_required_other_optional'); ?>
                        </div>
                    </div>
                    
                    <div class="col-md-12" id="stockssoactual" style="display:none">
                        <div class="clearfix"></div>
                        <div class="well well-sm">
                          
                
                            <a id="stocksample" href="<?php echo $this->config->base_url(); ?>assets/csv/actualstock.csv" class="btn btn-primary pull-right"><i class="fa fa-download"></i> Actual Stock Sample</a>
                            <span class="text-warning"><?php echo $this->lang->line("csv1"); ?></span><br>
                            <?php echo $this->lang->line("csv2"); ?> <span class="text-info">(<?= lang("stock_type,date(YYYY-MM-DD),supplier_id,supplier,total,shipping,grand_total,country_id,country,promotion,product_id,sku,brand_id,brand_name,gbu"); ?>
                                )</span> <?php echo $this->lang->line("csv3"); ?><br>
                            <?= lang('all_fields_required'); ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?= lang("csv_file", "csv_file") ?>
                            <input id="csv_file" type="file" name="userfile" required="required"
                                   data-show-upload="false" data-show-preview="false" class="form-control file"> </div>
                    </div> <div class="col-md-6" style="display:none">
                        <div class="form-group">
                            <?= lang("Epdis", "Epdis") ?>
                           <input type="radio"  name="distributor_name" id="distributor_name1" value="epdis" class="form-control file">
                            <?= lang("Mercafar", "Mercafar") ?>
                            <input type="radio"  name="distributor_name" id="distributor_name2"   value="mercafar" class="form-control file"> </div>
                              <?= lang("Other", "Other") ?>
                            <input type="radio"  name="distributor_name"  value="mercafar"  id="distributor_name3" class="form-control file"> </div>
                    </div>
                    <div class="clearfix"></div>
                   <!-- <div class="col-md-6">
                        <div class="form-group">
                            <?= lang("document", "document") ?>
                            <input id="document" type="file" name="document" data-show-upload="false"
                                   data-show-preview="false" class="form-control file">
                        </div>
                    </div>-->

                    <div class="clearfix"></div>
                    <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                    <div class="col-md-12">
                        <!--<div class="form-group">
                            <input type="checkbox" class="checkbox" id="extras" value=""/><label for="extras"
                                                                                                 class="padding05"><?= lang('more_options') ?></label>
                        </div>-->
                        <div class="row" id="extras-con" style="display: none;">
                            <?php if ($Settings->tax1) { ?>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <?= lang('order_tax', 'potax2') ?>
                                        <?php
                                        $tr[""] = "";
                                        foreach ($tax_rates as $tax) {
                                            $tr[$tax->id] = $tax->name;
                                        }
                                        echo form_dropdown('order_tax', $tr, "", 'id="potax2" class="form-control input-tip select" style="width:100%;"');
                                        ?>
                                    </div>
                                </div>
                            <?php } ?>

                            <div class="col-md-4" style="display:none;">
                                <div class="form-group">
                                    <?= lang("discount_label", "podiscount"); ?>
                                    <?php echo form_input('discount', '', 'class="form-control input-tip" id="podiscount"'); ?>
                                </div>
                            </div>

                            <div class="col-md-4" style="display:none;">
                                <div class="form-group" style="margin-bottom:5px;">
                                    <?= lang("shipping", "poshipping"); ?>
                                    <?php echo form_input('shipping', '', 'class="form-control input-tip" id="poshipping"'); ?>

                                </div>
                            </div>
                        </div>
                        <!--<div class="clearfix"></div>-->
                        <!--<div class="form-group">-->
                        <!--    <?= lang("note", "ponote"); ?>-->
                        <!--    <?php echo form_textarea('note', (isset($_POST['note']) ? $_POST['note'] : ""), 'class="form-control" id="ponote" style="margin-top: 10px; height: 100px;"'); ?>-->
                        <!--</div>-->

                    </div>
                    <div class="col-md-12">
                        <div
                            class="from-group"><?php echo form_submit('add_pruchase', $this->lang->line("submit"), 'id="add_pruchase" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?></div>
                    </div>
                </div>
            </div>

            <?php echo form_close(); ?>

        </div>

    </div>
</div>
</div>

<script type="text/javascript">
$(document).ready(function()
{   
  $(".datepicker").datepicker({
        dateFormat: 'yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,

        onClose: function(dateText, inst) {

            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('yy', new Date(year, 0, 1)));
        }
    });


 $('#actual_values').on('ifChecked', function (event) {
     
            $('#datediv').css('display','none');
            $('#typediv').css('display','none');
             $('#stockssoactual').css('display','block');
             $('#stocksso').css('display','none');
           
        });
        $('#actual_values').on('ifUnchecked', function (event) {
            $('#datediv').css('display','block');
            $('#typediv').css('display','block');
            $('#stockssoactual').css('display','none');
              $('#stocksso').css('display','block');
        });
    $(".monthPicker").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $(".ui-datepicker-month").hide();
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        });
    });
    
    
    $("#powarehouse").change(function(e){
      var stocktype=($("#powarehouse :selected").text()).toLowerCase();  
	  if(stocktype=="sso"){
		  $("#s2id_supplier_id").css("display","none");
	  } else{
		    $("#s2id_supplier_id").css("display","block");
	  }
      
      alert("Confirm  file headings are for selected stock type");
     
    });
    
//    $("#add_pruchase").click(function(e){
//        e.preventDefault();
//    if ($("input:radio[name='distributor_name']").is(':checked')==false) {
//     alert("you must select at least one distributor group");
//     return false;
//}  else{
//    $(".edit-po-form").submit();
//}
//    });
});
</script>