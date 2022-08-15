 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php

$v = "";
/* if($this->input->post('name')){
  $v .= "&product=".$this->input->post('product');
  } */
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}

if ($this->input->post('rtype')) {
    $v .= "&rtype=" . $this->input->post('rtype');
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

if ($this->input->post('bus_unit')) {
   $v .= "&bus_unit=".$this->input->post('bus_unit');
}
if ($this->input->post('net_gross')) {
   $v .= "&net_gross=".$this->input->post('net_gross');
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
            "aaSorting": [[0, "desc"]],
                 "aLengthMenu": [[10, 25, 50, 100,500,1000,2000, -1], [10, 25, 50, 100,500,1000,2000, "<?= lang('all') ?>"]],
            "iDisplayLength":100,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('reports/getMasharikiRpt/?v=1' . $v) ?>',
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
            },null,null, null, null,null, null , null, null,null,null],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var gtotal = 0, paid = 0, balance = 0,resale =0; qty =0; tender =0;
                for (var i = 0; i < aaData.length; i++) {
					//qty += parseFloat(aaData[aiDisplay[i]][6]);
                    //gtotal += parseFloat(aaData[aiDisplay[i]][7]);
					// resale += parseFloat(aaData[aiDisplay[i]][8]);
					 //tender += parseFloat(aaData[aiDisplay[i]][9]);
                  // paid += parseFloat(aaData[aiDisplay[i]][14]);
                   // balance += parseFloat(aaData[aiDisplay[i]][7]);
                }
                var nCells = nRow.getElementsByTagName('th');
				 //[6].innerHTML = currencyFormat(parseFloat(qty));
                //nCells[7].innerHTML = currencyFormat(parseFloat(gtotal));
				// nCells[8].innerHTML = currencyFormat(parseFloat(resale));
               //nCells[9].innerHTML = currencyFormat(parseFloat(tender));
			   //nCells[14].innerHTML = currencyFormat(parseFloat(paid));
               // nCells[7].innerHTML = currencyFormat(parseFloat(balance));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('Sales_Type');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('Cluster');?>]", filter_type: "text", data: []},
			{column_number: 4, filter_default_label: "[<?=lang('Country');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('Region');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('PSO_Distibutor');?>]", filter_type: "text", data: []},
            {column_number: 7, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
			{column_number: 8, filter_default_label: "[<?=lang('busines_unit');?>]", filter_type: "text", data: []},
		//	{column_number: 9, filter_default_label: "[<?=lang('product');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
            $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('reports/getMasharikiRpt/0/xls/?v=1'.$v)?>";
            return false;
        });
         $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
	$(document).on('change', '#type', function (e) {
 if ($("#type").val()=="PSO"){
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
       
	
		
    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('Mashariki_report'); ?> <?php
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
                        <li><a href="#" class="bpo" title="<b><?= $this->lang->line("delete_sales") ?></b>" data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger delete' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left"><i class="fa fa-trash-o"></i> <?= lang('delete_sales') ?></a></li>
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
                    <?php echo form_open("reports/mashariki_rpt",$attrib); ?>
                    <div class="row">
                        <div class="col-sm-3" style="display:none">
                            <div class="form-group">
                                <label class="control-label" for="reference_no"><?= lang("reference_no"); ?></label>
                                <?php echo form_input('reference_no', (isset($_POST['reference_no']) ? $_POST['reference_no'] : ""), 'class="form-control tip" id="reference_no"'); ?>

                            </div>
                        </div>

                        <div class="col-sm-3" style="display:none">
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
                        <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Cluster"); ?></label>
                                <?php
                                $clusters=$this->cluster_model->getClusters();
                                $cl[""] = "";
                                foreach ($clusters as $cluster) {
                                    $cl[$cluster->name] = $cluster->name;
                                }
                                echo form_dropdown('cluster[]', $cl, (isset($_POST['cluster']) ? $_POST['cluster'] : ""), 'class="form-control" multiple="multiple" id="cluster" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("cluster") . '"');
                                ?>
                            </div>
                        </div>
				    <div class="col-sm-3" >
                                                <?= lang("Country", "country"); ?>
                                                <div class="form-group">

                                <?php
                                //$contr[""] = "All";
//                                foreach ($currencies as $cluster1) {
//                                    $contr[$cluster1->id] = $cluster1->portuguese_name;
//                                }
//                                echo form_dropdown('s_country[]', $contr, (isset($_POST['s_country']) ? $_POST['s_country'] : ""), 'class="form-control input-tip select overflow-auto" multiple="multiple" id="s_country" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Country") . '"');
//                                 
                                ?> 
                                                    
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
												
            </div>
                                    
                                    </div>
            <div class="col-sm-3">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("BU"); ?></label>
                                <?php
                                $clu[""] = "Select BU";
                                foreach ($bu as $businness) {
                                    $clu[$businness->business_unit] = $businness->business_unit;
                                }
                                echo form_dropdown('bus_unit', $clu, (isset($_POST['bus_unit']) ? $_POST['bus_unit'] : ""), 'class="form-control" id="bus_unit" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("business_unit") . '"');
                                ?>
                            </div>
                        </div>		
						
                            <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("Start Month/Year", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : date('m/Y')), 'class="form-control datepicker monthPicker" id="start_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                        
                             <div class="col-sm-3">
                            <div class="form-group">
                                <?= lang("End Month/Year", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : date('m/Y')), 'class="form-control datepicker monthPicker" id="end_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                             <div class="form-group">
                                <?= lang("Report_Type*", "rtype"); ?>
                                <?php $pst = array('ssosales' => lang('SSO Sale'),'psosales' => lang('PSO Sale'), 'stock' => lang('Stock'), 'psobudget' => lang('PSO Budget'), 'ssobudget' => lang('SSO Budget'));
                                echo form_dropdown('rtype', $pst, (isset($_POST['rtype']) ? $_POST['rtype'] : ""), 'class="form-control input-tip"  required="required" id="rtype"'); ?>

                            </div></div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Net_Gross"); ?></label>
                                <?php
                                $nt[""] = "Select";
                                $nt=array("G"=>"Gross","NT"=>"Net","TN"=>"Tender");
                                echo form_dropdown('net_gross', $nt, (isset($_POST['net_gross']) ? $_POST['net_gross'] : ""), 'class="form-control" id="bus_unit" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("net_gross") . '"');
                                ?>
                            </div>
                        </div>
           
                       
                        <div class="col-sm-3" style="display:none">
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
                        
                 	
                                         
                        <?php if($this->Settings->product_serial) { ?>
                            <div class="col-sm-3" style="display:none">
                                <div class="form-group">
                                    <?= lang('serial_no', 'serial'); ?>
                                    <?= form_input('serial', '', 'class="form-control tip" id="serial"'); ?>
                                </div>
                            </div>
                        <?php } ?>
                      

                    </div>
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?>
									
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
                            <th><?php echo $this->lang->line("Month/Year"); ?></th>
                            <th><?php echo $this->lang->line("Type"); ?></th>
                            <th><?php echo $this->lang->line("Country"); ?></th>
                              <th><?php echo $this->lang->line("BU"); ?></th>
                              <th><?php echo $this->lang->line("Brand"); ?></th>
                              <th><?php echo $this->lang->line("Product"); ?></th>
                            <th><?php echo $this->lang->line("Distributor"); ?></th>
                            <th><?php echo $this->lang->line("Customer"); ?></th>
                            <th><?php echo $this->lang->line("Qty"); ?></th>
                            <th><?php echo $this->lang->line("Value"); ?></th>
                            
                            
							
						
						
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="12" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
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
            window.location.href = "<?=site_url('reports/getMasharikiReport/pdf/?v=1'.$v)?>";
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
                   // var opt = $('<option />'); // here we're creating a new select option for each group
                   // opt.val(id);
                   // opt.text(city);
                   // $('#s_country').append(opt);
					 $("#s_country").append("<option value="+id+">"+city+"</option>");
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