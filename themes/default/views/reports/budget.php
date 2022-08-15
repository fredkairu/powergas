 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php

$v = "";
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */
if ($this->input->post('reference_no')) {
    $v .= "&reference_no=" . $this->input->post('reference_no');
}
if ($this->input->post('budget_forecast')) {
    $v .= "&budget_forecast=" . $this->input->post('budget_forecast');
}
if ($this->input->post('customer')) {
    $v .= "&customer=" . $this->input->post('customer');
}
if ($this->input->post('PSOdist')) {
    $v .= "&PSOdist=" . $this->input->post('PSOdist');
}

if ($this->input->post('biller')) {
    $v .= "&biller=" . $this->input->post('biller');
}
if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
}
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}
if ($this->input->post('type')) {
    $v .= "&type=" . $this->input->post('type');
}
if ($this->input->post('cluster')) {
    $v .= "&cluster=" . $_POST['cluster'];
	//print_r( $_POST['cluster']);
	//die;
}
if ($this->input->post('s_country')) {
	$ctriess=$this->input->post('s_country');
	foreach($ctriess as $ctry){
		if($ctry){
		$countries.=$ctry.",";
		}
	}
    $v .= "&country=".rtrim($countries,",");
}
if ($this->input->post('serial')) {
    $v .= "&serial=" . $this->input->post('serial');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}

?>
<style>

    select[multiple], select[size] {
    height: 0px;
    height: 0px;
    background-color: white;
    border: 1px white;
    overflow-y: hidden;
    display: none !important;
}
select2-search-choice {
    border: 0;
    padding: 6px 6px 6px 20px;
    border-radius: 0;
    box-shadow: none;
}
    
</style>
<script>
    $(document).ready(function () {
        var oTable = $('#SlRData').dataTable({
            "aaSorting": [[1, "desc"]],
                 "aLengthMenu": [[10, 25, 50, 100,500,1000,2000, -1], [10, 25, 50, 100,500,1000,2000, "<?= lang('all') ?>"]],
            "iDisplayLength":100,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('reports/getBudgetReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            },null,null, null,null, null , null, null, null,null,null, {"mRender": currencyFormat},null, {"mRender": currencyFormat},null],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var gtotal = 0, paid = 0, balance = 0,resale =0; qty =0; tender =0;
                for (var i = 0; i < aaData.length; i++) {
					qty += parseFloat(aaData[aiDisplay[i]][10]);
                    gtotal += parseFloat(aaData[aiDisplay[i]][11]);
					
                   //paid += parseFloat(aaData[aiDisplay[i]][14]);
                   // balance += parseFloat(aaData[aiDisplay[i]][7]);
                }
                var nCells = nRow.getElementsByTagName('th');
				 nCells[10].innerHTML = currencyFormat(parseFloat(qty));
                nCells[11].innerHTML = currencyFormat(parseFloat(gtotal));
				
               //nCells[12].innerHTML = currencyFormat(parseFloat(tender));
		//	   nCells[13].innerHTML = currencyFormat(parseFloat(paid));
               // nCells[7].innerHTML = currencyFormat(parseFloat(balance));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?>]", filter_type: "text", data: []},
                {column_number: 2, filter_default_label: "[<?=lang('Scenario');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('Budget_Type');?>]", filter_type: "text", data: []},
//            {column_number: 3, filter_default_label: "[<?=lang('Cluster');?>]", filter_type: "text", data: []},
			{column_number: 4, filter_default_label: "[<?=lang('Country');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('Region');?>]", filter_type: "text", data: []},
       
            {column_number: 7, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
			{column_number: 8, filter_default_label: "[<?=lang('busines_unit');?>]", filter_type: "text", data: []},
			{column_number: 9, filter_default_label: "[<?=lang('product');?>]", filter_type: "text", data: []},
         //   {column_number: 8, filter_default_label: "[<?=lang('payment_status');?>]", filter_type: "text", data: []},
            // {column_number: 9, filter_default_label: "[<?=lang('paid_by');?>]", filter_type: "text", data: []},
            //  {column_number:10, filter_default_label: "[<?=lang('transaction_no');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
        
        		$(document).on('change', '#type', function (e) {
//alert ("Test");
if($("#type").val()=="SI"){
document.getElementById("psodistributor").style.display = "block";
document.getElementById("alldistributor").style.display = "none";
}
else if ($("#type").val()=="PSO"){
document.getElementById("psodistributor").style.display = "none";
document.getElementById("alldistributor").style.display = "block";
}
else if($("#type").val()=="SSO"){
document.getElementById("psodistributor").style.display = "block";
document.getElementById("alldistributor").style.display = "block";

}
else if($("#type").val()==""){
document.getElementById("psodistributor").style.display = "none";
document.getElementById("alldistributor").style.display = "none";

}
  
	});
	    		
	
        
        <?php if ($this->input->post('customer')) { ?>
        $('#customer').val(<?= $this->input->post('customer') ?>).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "customers/suggestions/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0]);
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

        $('#customer').val(<?= $this->input->post('customer') ?>);
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
		$('#salesrpt_reset').click(function () {
			
			 $("#cluster").val("");
			$("#s_country").val("");
			$("#customer").val("");
			$("#type").val('');
			$("#start_date").val('');
            $("#end_date").val('');
            location.href = site.base_url + "reports/budget";
            return false;
        });
		
    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('Budget'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?></h2>
              <div class="box-icon">
                        <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip"
                                                                                  data-placement="left"
                                                                                  title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li class="divider"></li>
                        <li><a href="#" class="bpo" title="<b><?= $this->lang->line("delete_budget") ?></b>" data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger delete' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left"><i class="fa fa-trash-o"></i> <?= lang('delete_budget') ?></a></li>
                    </ul>
                </li></div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown"><a href="#" class="toggle_up tip" title="<?= lang('hide_form') ?>"><i
                            class="icon fa fa-toggle-up"></i></a></li>
                <li class="dropdown"><a href="#" class="toggle_down tip" title="<?= lang('show_form') ?>"><i
                            class="icon fa fa-toggle-down"></i></a></li>
            </ul>
        </div>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown"><a href="#" id="pdf" class="tip" title="<?= lang('download_pdf') ?>"><i
                            class="icon fa fa-file-pdf-o"></i></a></li>
                <li class="dropdown"><a href="#" id="xls" class="tip" title="<?= lang('download_xls') ?>"><i
                            class="icon fa fa-file-excel-o"></i></a></li>
                <li class="dropdown"><a href="#" id="image" class="tip" title="<?= lang('save_image') ?>"><i
                            class="icon fa fa-file-picture-o"></i></a></li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('customize_report'); ?></p>

                <div id="form">
<?php $attrib = array('data-toggle' => 'validator', 'novalidate','role' => 'form','method'=>'POST','id'=>'searchform');?>
                    <?php echo form_open("reports/budget",$attrib); ?>
                    <div class="row">
                        <div class="col-sm-4" style="display:none">
                            <div class="form-group">
                                <label class="control-label" for="reference_no"><?= lang("reference_no"); ?></label>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ""), 'class="form-control tip" id="reference_no"'); ?>

                            </div>
                        </div>

                        <div class="col-sm-4" style="display:none">
                            <div class="form-group">
                                <label class="control-label" for="user"><?= lang("created_by"); ?></label>
                                <?php
                                $us[""] = "";
                                foreach ($users as $user) {
                                    $us[$user->id] = $user->first_name . " " . $user->last_name;
                                }
                                echo form_dropdown('user', $us, (isset($_POST['user']) ? $_POST['user'] : ""), 'class="form-control" id="user" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("user") . '"');
                                ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                             <div class="form-group">
                                <?= lang("Budget or Forecast", "budget_forecast"); ?>
                                <?php $pstt = array(''=>lang('Select_Option'),'budget' => lang('Budget'), 'forecast' => lang('Forecast 1'), 'forecast2' => lang('Forecast 2'));
                                echo form_dropdown('budget_forecast', $pstt, (isset($_POST['budget_forecast']) ? $_POST['budget_forecast'] : ""), 'class="form-control input-tip"  id="budget_forecast"'); ?>

                            </div>
                                        </div>
						
						<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Cluster"); ?></label>
                                <?php
                                //$cl[""] = "";
                                foreach ($clusters as $cluster) {
                                    $cl[$cluster->name] = $cluster->name;
                                }
                                echo form_dropdown('cluster[]', $cl, (isset($_POST['cluster']) ? $_POST['cluster'] : ""), 'class="form-control" multiple="multiple" id="cluster" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("cluster") . '"');
                                ?>
                            </div>
                        </div>
						<div class="col-sm-4" >
                                                <?= lang("Country", "country"); ?>
                                                <div class="form-group">
                   
                         <select id="s_country"  name="s_country[]"  multiple="multiple" class="form-control" data-placeholder="Select Country">
         <?php
       
         if(isset($_POST['s_country'])){
     foreach ($_POST['s_country'] as $ctry) {
        $cr= $this->sales_model->getCountryByID($ctry);
         ?>
                             <option selected="selected" value="<?=$ctry?>"><?=$cr->country?></option>
         
    <?php } 
         }?>
        </select>
                                              
												
            </div></div>
            
			                      
                        <div class="col-sm-4" style="display:none">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("biller"); ?></label>
                                <?php
                                $bl[""] = "";
                                foreach ($billers as $biller) {
                                    $bl[$biller->id] = $biller->company != '-' ? $biller->company : $biller->name;
                                }
                                echo form_dropdown('biller', $bl, (isset($_POST['biller']) ? $_POST['biller'] : ""), 'class="form-control" id="biller" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("biller") . '"');
                                ?>
                            </div>
                        </div>
						
                         <div class="col-md-4">
                             <div class="form-group">
                                <?= lang("Scenario", "type"); ?>
                                <?php $pst = array(''=>lang('Select_Scenario'), 'PSO' => lang('PSO'), 'SSO' => lang('SSO'));
                                echo form_dropdown('type', $pst, (isset($_POST['type']) ? $_POST['type'] : ""), 'class="form-control input-tip" required="required" id="type"'); ?>

                            </div>
                                        </div>
                                         
                        <?php if($this->Settings->product_serial) { ?>
                            <div class="col-sm-4" style="display:none">
                                <div class="form-group">
                                    <?= lang('serial_no', 'serial'); ?>
                                    <?= form_input('serial', '', 'class="form-control tip" id="serial"'); ?>
                                </div>
                            </div>
                        <?php } ?>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control datepicker monthPicker" id="start_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control datepicker monthPicker" id="end_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                         <div class="col-sm-4" style="display:none" id="alldistributor">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang("customer"); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4" style="display:none" id="psodistributor">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang("Customer"); ?></label>
                                
                                  <?php
                                //$cust[""] = "";
                                  $sanoficustomer{0}="";
                                foreach ($sanoficustomer as $biller) {
                                    $cust[$biller->id] =  $biller->name;
                                }
                                echo form_dropdown('PSOdist', $cust, (isset($_POST['PSOdist']) ? $_POST['PSOdist'] : ""), 'class="form-control" id="PSOdist" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Customer") . '"');
                                ?>
                           
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?>
									<input type="reset" id="salesrpt_reset" class="btn btn-primary"> </input>
							</div>
							 
                    </div>
                    <?php echo form_close(); ?>

                </div>
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="SlRData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <tr><th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang("date"); ?></th>
                            <th style="width:20px"><?= lang("Scenario"); ?></th>
                            <th><?= lang("Budget"); ?></th>
                            <th><?= lang("Country"); ?></th>
							<th><?= lang("Distributor"); ?></th>
							<th><?= lang("Customer"); ?></th>
                            <th><?= lang("BU"); ?></th>
                            <th><?= lang("SKU"); ?></th>
                            <th><?= lang("Brand"); ?></th>
							<th><?= lang("Net_Qty"); ?></th>
							 <th><?= lang("Net_Value"); ?></th>
                            <th><?= lang("Gross_Qty"); ?></th>
							 <th><?= lang("Gross_Value"); ?></th>
							<th>Acions</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="17" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                             <th></th>
                            <th></th>
							<th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
							<th><?= lang("GBU"); ?></th>
                            <th><?= lang("product"); ?></th>
                           <!-- <th></th>-->
							<th><?= lang("Qty_Total"); ?></th>
							<th></th>
                            <th></th>
                          	<th></th>		
                            
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('reports/getBudgetReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('reports/getBudgetReport/0/xls/?v=1'.$v)?>";
            return false;
        });
     $(document).on('click', '#delete', function (e) {

window.location.href = "<?=site_url('budget/sale_actions/')?>";
            return false;
  
	});
        $('#image').click(function (event) {
            event.preventDefault();
            html2canvas($('.box'), {
                onrendered: function (canvas) {
                    var img = canvas.toDataURL()
                    window.open(img);
                }
            });
            return false;
        });
    });
</script>
<script type="text/javascript">
$(document).ready(function()
{   
    $(".datepicker").datepicker({
        dateFormat: 'MM yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,

        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('mm/yy', new Date(year, month, 1)));
        }
    });
   
          //  $('.multiselect').multiselect();      

    $(".monthPicker").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        });
    });
	$('#cluster').change(function(){
    var state_id = $('#cluster').val();
	//alert(state_id);
   states=state_id.toString().split(',');
   laststate=states.pop();
   if(laststate !=""){
       state_id=laststate;
   }
  // $('#s_country').empty();
    if (state_id != ""){
        var post_url = "index.php/system_settings/get_cities/" + state_id;
        $.ajax({
            type: "POST",
            url: post_url,
             data:{'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
    
            success: function(cities) //we're calling the response json array 'cities'
            {
              //  $('#f_country').empty();
                $('#s_country, #s_country_label').show();
				//document.getElementById("salesrptdiv").style.display = "none";
                $.each(cities,function(id,city)
                {
                   var opt = $('<option />'); // here we're creating a new select option for each group
                    opt.val(id);
                    opt.text(city);
                    $('#s_country').append(opt);
				//	 $("#s_country").append("<option value="+id+">"+city+"</option>");
                });
            } //end success
         }); //end AJAX
    } else {
        $('#s_country').empty();
      //  $('#f_country, #f_country_label').hide();
    }//end if
}); //end change
    
});
</script>