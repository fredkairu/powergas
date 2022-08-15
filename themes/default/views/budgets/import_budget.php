 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script type="text/javascript">
    var count = 1, an = 1, product_variant = 0, DT = <?= $Settings->default_tax_rate ?>,
        product_tax = 0, invoice_tax = 0, total_discount = 0, total = 0,
        tax_rates = <?php echo json_encode($tax_rates); ?>;
    //var audio_success = new Audio('<?=$assets?>sounds/sound2.mp3');
    //var audio_error = new Audio('<?=$assets?>sounds/sound3.mp3');
    $(document).ready(function () {
        
        <?php if ($Owner || $Admin) { ?>
        if (!localStorage.getItem('sldate')) {
            $("#sldate").datetimepicker({
                format: site.dateFormats.js_ldate,
                fontAwesome: true,
                language: 'sma',
                weekStart: 1,
                todayBtn: 1,
                autoclose: 1,
                todayHighlight: 1,
                startView: 2,
                forceParse: 0
            }).datetimepicker('update', new Date());
        }
        $(document).on('change', '#sldate', function (e) {
            localStorage.setItem('sldate', $(this).val());
        });
        if (sldate = localStorage.getItem('sldate')) {
            $('#sldate').val(sldate);
        }
        $(document).on('change', '#slbiller', function (e) {
            localStorage.setItem('slbiller', $(this).val());
        });
        if (slbiller = localStorage.getItem('slbiller')) {
            $('#slbiller').val(slbiller);
        }
        <?php } ?>
        if (!localStorage.getItem('slref')) {
            localStorage.setItem('slref', '<?=$slnumber?>');
        }

    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Import_Budget_or_Forecast'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo form_open_multipart("budgets/import_budgets", $attrib);

                ?>
                <div class="row">
                    <div class="col-lg-12" >
                        <?php if ($Owner || $Admin) { ?>
                            <div class="col-md-4" style="display:none">
                                <div class="form-group">
                                    <?= lang("date", "sldate"); ?>
                                    <?php echo form_input('date', (isset($_POST['date']) ? $_POST['date'] : ""), 'class="form-control input-tip datetime" id="sldate" required="required"'); ?>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="col-md-4" style="display:none">
                            <div class="form-group">
                                <?= lang("reference_no", "slref"); ?>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : $slnumber), 'class="form-control input-tip" id="slref"'); ?>
                            </div>
                        </div>
                        <?php if (!$Settings->restrict_user || $Owner || $Admin) { ?>
                            <div class="col-md-4" style="display:none">
                                <div class="form-group">
                                    <?= lang("biller", "slbiller"); ?>
                                    <?php
                                    $bl[""] = "";
                                    foreach ($billers as $biller) {
                                        $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                                    }
                                    echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : $Settings->default_biller), 'id="slbiller" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("biller") . '" required="required" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        <?php } else {
                            $biller_input = array(
                                'type' => 'hidden',
                                'name' => 'biller',
                                'id' => 'slbiller',
                                'value' => $this->session->userdata('biller_id'),
                            );

                            echo form_input($biller_input);
                        } ?>

<div class="col-md-1">
                               <div class="form-group">
                                    <?= lang("Use_Actual_Values?", "actual_values"); ?><br>
                <input type="checkbox" class="checkbox" name="actual_values" id="actual_values">
            </div>
                                        </div>
                        <div class="col-md-2" id="budgetforecastdiv">
                                            <div class="form-group">
                                                <?= lang("Budget_or_Forecast", "budget_forecast"); ?>
                                                <?php
                                                $options = array(""=>"Select Option","budget"=>"Budget","forecast"=>"Forecast 1","forecast2"=>"Forecast 2");
                                                
                                                echo form_dropdown('budget_forecast', $options, (isset($_POST['budget_forecast']) ? $_POST['budget_forecast'] : $Settings->default_warehouse), 'id="slwarehouse" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("option") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-2" id="netgrossdiv">
                                            <div class="form-group">
                                                <?= lang("Net_or_Gross", "net_gross"); ?>
                                                <?php
                                                $options = array(""=>"Select Option","G"=>"Gross","N"=>"Net");
                                                
                                                echo form_dropdown('net_gross', $options, (isset($_POST['net_gross']) ? $_POST['net_gross'] : $Settings->default_warehouse), 'id="slwarehouse" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("option") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
                                        </div>
                                    <?php if (!$Settings->restrict_user || $Owner || $Admin) { ?>
                                        <div class="col-md-3" style="display:none">
                                            <div class="form-group">
                                                <?= lang("warehouse", "slwarehouse"); ?>
                                                <?php
                                                $wh[''] = '';
                                                foreach ($warehouses as $warehouse) {
                                                    $wh[$warehouse->id] = $warehouse->name;
                                                }
                                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : $Settings->default_warehouse), 'id="slwarehouse" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("warehouse") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
                                        </div>
                                    <?php } else {
                                        $warehouse_input = array(
                                            'type' => 'hidden',
                                            'name' => 'warehouse',
                                            'id' => 'slwarehouse',
                                            'value' => $this->session->userdata('warehouse_id'),
                                        );

                                        echo form_input($warehouse_input);
                                    } ?>
                                    <div class="col-md-4" style="display:none">
                                        <div class="form-group">
                                            <?= lang("customer", "slcustomer"); ?>
                                            <div class="input-group">
                                                <?php
                                                echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'id="slcustomer" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '" required="required" class="form-control input-tip" style="width:100%;"');
                                                ?>
                                                <div class="input-group-addon no-print" style="padding: 2px 5px;"><a
                                                        href="<?= site_url('customers/add'); ?>" id="add-customer"
                                                        class="external" data-toggle="modal" data-target="#myModal"><i
                                                            class="fa fa-2x fa-plus-circle" id="addIcon"></i></a></div>
                                            </div>
                                        </div>
                                    </div>
                         <div class="col-md-4" style="display:none">
                                            <div class="form-group">
                                                <?= lang("Country", "country"); ?>
                                                <?php
                                               // $cluster=array("SSA32"=>"SSA32","EAH"=>"EAH","CCS"=>"CCS","EPDIS"=>"EPDIS");
                                                
                                                echo form_dropdown('country', $countries,($_POST['country']), 'id="country" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("cluster") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
											</div>
											 <div class="col-md-2">
                             <div class="form-group"id="typediv">
                                <?= lang("Budget_Type", "type"); ?>
                                <?php $pst = array(""=>"SCENARIO",'PSO' => lang('PSO'), 'SSO' => lang('SSO'));
                                echo form_dropdown('type', $pst, '', 'class="form-control input-tip" required="required" id="type"'); ?>

                            </div>
                                        </div>
                                   
                                 <div class="col-md-2" id="yeardiv">
                             <div class="form-group">
                                <?= lang("Year", "smonth"); ?>
                                <input type="text" placeholder="yyyy"  class="form-control input-tip datepicker monthPicker"  id="smonth" value="<?php echo date('Y'); ?>"name="smonth" required="required" autocomplete="off">

                            </div>
                                        </div>
										<div class="col-md-4" style="display:none" id="distributorid">
                             
                                 <!-- <div class="form-group">
                                                <?= lang("Distributor", "distributor"); ?>
                                                <?php
                                                $this->db->where(array("group_name"=>"customer"));
                                                $q = $this->db->get('companies');
                                                $customers["all"]="Select All";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->name){
                $customers[$row->id] = $row->name;
                }
                else{
                   $customers[$row->id] = $row->company;  
                }
            }
        }
                                                
                                                echo form_dropdown('customer[]',$customers,($_POST['customer']), 'id="customer" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("customer") . '"  style="width:100%;" ');
                                                ?>
            </div>   -->                                                 
                                                                                    
                                                                                    <div class="form-group">
                                <?= lang("Distributor", "distributor"); ?>
                                <?php $pst = array(""=>"SELECT DISTRIBUTOR",'EPDIS' => lang('EPDIS'), 'MARCAFA' => lang('MARCAFA')); 
                                echo form_dropdown('distributor', $pst, '', 'class="form-control input-tip" required="required" id="distributor"'); ?>

                            </div>
                                        </div>
							
                       

						<div class="col-md-12" id="epdispso" >
                            <div class="clearfix"></div>
                            <div class="well well-sm">
                                <a href="<?php echo $this->config->base_url(); ?>assets/csv/psobudgets.csv"
                                   class="btn btn-primary pull-right"><i class="fa fa-download"></i> Download Sample
                                    File (PSO BUDGET)</a>
                                <span class="text-warning"><?php echo $this->lang->line("csv1"); ?></span><br>
                                <?php echo $this->lang->line("csv2"); ?>: <br> <span
                                    class="text-info"><?=strtolower("(MONTH,COUNTRY,DISTRIBUTOR,PRODUCT,UNITS,VALUE)")?></span> <br>
                                <?= lang('All_Fields_are_required'); ?>
                            </div>
                        </div>
			
						<div class="col-md-12" id="ssobudget" style="display:block">
                            <div class="clearfix"></div>
                            <div class="well well-sm">
                                <a href="<?php echo $this->config->base_url(); ?>assets/csv/ssobudgets.csv"
                                   class="btn btn-primary pull-right"><i class="fa fa-download"></i> Download Sample
                                    File (SSO BUDGET)</a>
                                <span class="text-warning"><?php echo $this->lang->line("csv1"); ?></span><br>
                                <?php echo $this->lang->line("csv2"); ?> <br><span
                                    class="text-info"><?=strtolower("(MONTH,country,CUSTOMER,PRODUCT,UNITS,VALUE)")?> 
</span><br>
                                <?= lang('All_fields_are_required'); ?>
                            </div>
                        </div>
                        	<div class="col-md-12" id="actualbudget" style="display:none">
                            <div class="clearfix"></div>
                            <div class="well well-sm">
                                <a href="<?php echo $this->config->base_url(); ?>assets/csv/actualbudget.csv"
                                   class="btn btn-primary pull-right"><i class="fa fa-download"></i> Download Sample
                                    File (ACTUAL BUDGET)</a>
                                <span class="text-warning"><?php echo $this->lang->line("csv1"); ?></span><br>
                                <?php echo $this->lang->line("csv2"); ?> <br><span
                                    class="text-info"><?=strtolower("(Year format is (YYYY-MM-DD),values are in Euros")?> 
</span><br>
                                <?= lang('All_fields_are_required'); ?>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <?= lang("csv_file", "csv_file") ?>
                                <input id="csv_file" type="file" name="userfile" required="required"
                                       data-show-upload="false" data-show-preview="false" class="form-control file">
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="col-md-6" style="display:none">
                            <div class="form-group">
                                <?= lang("document", "document") ?>
                                <input id="document" type="file" name="document" data-show-upload="false"
                                       data-show-preview="false" class="form-control file">
                            </div>
                        </div>
                        <div class="clearfix"></div>

                        <?php if ($Settings->tax2) { ?>
                            <!--<div class="col-md-4">
                                <div class="form-group">
                                    <?= lang("order_tax", "sltax2"); ?>
                                    <?php
                                    $tr[""] = "";
                                    foreach ($tax_rates as $tax) {
                                        $tr[$tax->id] = $tax->name;
                                    }
                                    echo form_dropdown('order_tax', $tr, (isset($_POST['order_tax']) ? $_POST['order_tax'] : $Settings->default_tax_rate2), 'id="sltax2" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("order_tax") . '" class="form-control input-tip select" style="width:100%;"');
                                    ?>
                                </div>
                            </div>
                        <?php } ?>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("order_discount", "sldiscount"); ?>
                                <?php echo form_input('order_discount', '', 'class="form-control input-tip" id="sldiscount"'); ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <?= lang("shipping", "slshipping"); ?>
                                <?php echo form_input('shipping', '', 'class="form-control input-tip" id="slshipping"'); ?>

                            </div>
                        </div>

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("sale_status", "slsale_status"); ?>
                                <?php $sst = array('completed' => lang('completed'), 'pending' => lang('pending'));
                                echo form_dropdown('sale_status', $sst, '', 'class="form-control input-tip" required="required" id="slsale_status"'); ?>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("payment_term", "slpayment_term"); ?>
                                <?php echo form_input('payment_term', '', 'class="form-control tip" data-trigger="focus" data-placement="top" title="' . lang('payment_term_tip') . '" id="slpayment_term"'); ?>

                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("payment_status", "slpayment_status"); ?>
                                <?php $pst = array('pending' => lang('pending'), 'due' => lang('due'), 'paid' => lang('paid'));
                                echo form_dropdown('payment_status', $pst, '', 'class="form-control input-tip" required="required" id="slpayment_status"'); ?>

                            </div>
                        </div>-->
                        <div class="clearfix"></div>


                        <input type="hidden" name="total_items" value="" id="total_items" required="required"/>

                       
                        <div class="col-md-12">
                            <div
                                class="fprom-group"><?php echo form_submit('add_sale', $this->lang->line("submit"), 'id="add_sale" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></div>
                        </div>
                    </div>
                </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var $customer = $('#slcustomer');
    $customer.change(function (e) {
        localStorage.setItem('slcustomer', $(this).val());
        //$('#slcustomer_id').val($(this).val());
    });
    if (slcustomer = localStorage.getItem('slcustomer')) {
        $customer.val(slcustomer).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url+"customers/getCustomer/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "customers/suggestions",
                dataType: 'json',
                quietMillis: 15,
                data: function (term, page) {
                    return {
                        term: term,
                        limit: 10
                    };
                },
                results: function (data, page) {
                    if (data.results != null) {
                        return {results: data.results};
                    } else {
                        return {results: [{id: '', text: 'No Match Found'}]};
                    }
                }
            }
        });
        if (count > 1) {
            $customer.select2("readonly", true);
            $customer.val(slcustomer);
            $('#slwarehouse').select2("readonly", true);
            //$('#slcustomer_id').val(slcustomer);
        }
    } else {
        nsCustomer();
    }

// Order level shipping and discount localStorage 
if (sldiscount = localStorage.getItem('sldiscount')) {
    $('#sldiscount').val(sldiscount);
}
$('#sltax2').change(function (e) {
    localStorage.setItem('sltax2', $(this).val());
});
if (sltax2 = localStorage.getItem('sltax2')) {
    $('#sltax2').select2("val", sltax2);
}
$('#slsale_status').change(function (e) {
    localStorage.setItem('slsale_status', $(this).val());
});
if (slsale_status = localStorage.getItem('slsale_status')) {
    $('#slsale_status').select2("val", slsale_status);
}


var old_payment_term;
$('#slpayment_term').focus(function () {
    old_payment_term = $(this).val();
}).change(function (e) {
    var new_payment_term = $(this).val() ? parseFloat($(this).val()) : 0;
    if (!is_numeric($(this).val())) {
        $(this).val(old_payment_term);
        bootbox.alert('Unexpected value provided!');
        return;
    } else {
        localStorage.setItem('slpayment_term', new_payment_term);
        $('#slpayment_term').val(new_payment_term);
    }
});
if (slpayment_term = localStorage.getItem('slpayment_term')) {
    $('#slpayment_term').val(slpayment_term);
}

var old_shipping;
$('#slshipping').focus(function () {
    old_shipping = $(this).val();
}).change(function () {
    if (!is_numeric($(this).val())) {
        $(this).val(old_shipping);
        bootbox.alert('Unexpected value provided!');
        return;
    } else {
        shipping = $(this).val() ? parseFloat($(this).val()) : '0';
    }
    localStorage.setItem('slshipping', shipping);
    var gtotal = ((total + product_tax + invoice_tax) - total_discount) + shipping;
    $('#gtotal').text(formatMoney(gtotal));
});
if (slshipping = localStorage.getItem('slshipping')) {
    shipping = parseFloat(slshipping);
    $('#slshipping').val(shipping);
} else {
    shipping = 0;
}

$('#slref').change(function (e) {
    localStorage.setItem('slref', $(this).val());
});
if (slref = localStorage.getItem('slref')) {
    $('#slref').val(slref);
}

$('#slwarehouse').change(function (e) {
    localStorage.setItem('slwarehouse', $(this).val());
});
if (slwarehouse = localStorage.getItem('slwarehouse')) {
    $('#slwarehouse').select2("val", slwarehouse);
}

        // prevent default action usln enter
$('body').bind('keypress', function (e) {
    if (e.keyCode == 13) {
        e.preventDefault();
        return false;
    }
});

// Order tax calcuation 
if (site.settings.tax2 != 0) {
    $('#sltax2').change(function () {
        localStorage.setItem('sltax2', $(this).val());
        loadItems();
        return;
    });
}

// Order discount calcuation 
var old_sldiscount;
$('#sldiscount').focus(function () {
    old_sldiscount = $(this).val();
}).change(function () {
    var new_discount = $(this).val() ? $(this).val() : '0';
    if (is_valid_discount(new_discount)) {
        localStorage.removeItem('sldiscount');
        localStorage.setItem('sldiscount', new_discount);
        loadItems();
        return;
    } else {
        $(this).val(old_sldiscount);
        bootbox.alert('Unexpected value provided!');
        return;
    }

});

		$(document).on('change', '#type', function (e) {
//alert ("Test");
 if ($("#type").val()=="PSO"){

document.getElementById("epdispso").style.display = "block";

document.getElementById("ssosales").style.display = "none";document.getElementById("sibudget").style.display = "none";

document.getElementById("distributorid").style.display = "block";
}
else if($("#type").val()=="SSO"){

document.getElementById("epdispso").style.display = "none";

document.getElementById("ssosales").style.display = "block";	
document.getElementById("distributorid").style.display = "none";
document.getElementById("sibudget").style.display = "none";
}
else if($("#type").val()=="SI"){

document.getElementById("epdispso").style.display = "none";

document.getElementById("ssosales").style.display = "none";	
document.getElementById("distributorid").style.display = "none";
document.getElementById("sibudget").style.display = "block";
}

	});
//	$(document).on('change', '#distributor', function (e) {
//		//alert('Test');
//		if($("#distributor").val()=="MARCAFA"){
//			document.getElementById("mercarfapso").style.display = "block";
//document.getElementById("epdispso").style.display = "none";
//		}else if ($("#distributor").val()=="EPDIS"){
//			document.getElementById("mercarfapso").style.display = "none";
//document.getElementById("epdispso").style.display = "block";
//		}
//		
//		});
//    });

function nsCustomer() {
    $('#slcustomer').select2({
        minimumInputLength: 1,
        ajax: {
            url: site.base_url + "customers/suggestions",
            dataType: 'json',
            quietMillis: 15,
            data: function (term, page) {
                return {
                    term: term,
                    limit: 10
                };
            },
            results: function (data, page) {
                if (data.results != null) {
                    return {results: data.results};
                } else {
                    return {results: [{id: '', text: 'No Match Found'}]};
                }
            }
        }
    });
}
</script>
<script type="text/javascript">
$(document).ready(function()
{   
   $(".datepicker").datepicker({
        dateFormat: 'yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,

        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('yy', new Date(year, month, 1)));
        }
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
    
      $('#actual_values').on('ifChecked', function (event) {
     
            $('#budgetforecastdiv').css('display','none');
            $('#typediv').css('display','none');
            $('#ssobudget').css('display','none');
             $('#netgrossdiv').css('display','none');
            $('#epdispso').css('display','none');
            $('#yeardiv').css('display','none');
            $('#actualbudget').css('display','block');
        });
        $('#actual_values').on('ifUnchecked', function (event) {
             $('#budgetforecastdiv').css('display','block');
            $('#typediv').css('display','block');
            $('#ssobudget').css('display','block');
            $('#netgrossdiv').css('display','block');
            $('#epdispso').css('display','block');
            $('#actualbudget').css('display','none');
             $('#yeardiv').css('display','block')
        });
    
    
});
</script>