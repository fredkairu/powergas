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
if ($this->input->post('category')) {
    $v .= "&category=" . $this->input->post('category');
}

if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
if ($this->input->post('customer')) {
	$distrib=$this->input->post('customer');
	foreach($distrib as $distr){
		if($distr){
		$distribtrs.=$distr.",";
		}
	}
    $v .= "&PSOdist=".rtrim($distribtrs,",");
}
if ($this->input->post('PSOdist')) {
    $v .= "&customer=" . $this->input->post('PSOdist');
}
if ($this->input->post('customer1')) {
	$distrib1=$this->input->post('customer1');
	foreach($distrib1 as $distr1){
		if($distr1){
		$distribtrs1.=$distr1.",";
		}
	}
    $v .= "&customer1=".rtrim($distribtrs1,",");
}


if ($this->input->post('bus_unit')) {
   $v .= "&bus_unit=".$this->input->post('bus_unit');
}
if ($this->input->post('team')) {
    $teamsalignment=$this->input->post('team');
	foreach($teamsalignment as $unit1){
		if($unit1){
		$units1.=$unit1.",";
		}
	}
   $v .= "&team=".rtrim($units1,",");
}
if ($this->input->post('products')) {
    $products=$this->input->post('products');
	foreach($products as $product){
		if($product){
		$productss.=$product.",";
		}
	}
   $v .= "&products=".rtrim($productss,",");
}
if ($this->input->post('sales')) {
    $saleCoulumn=$this->input->post('sales');
	foreach($saleCoulumn as $sales){
		if($sales){
		$saless.=$sales.",";
		}
	}
   $v .= "&salecolumn=".rtrim($saless,",");
}
if ($this->input->post('stock')) {
    $stockColumn=$this->input->post('stock');
	foreach($stockColumn as $stock){
		if($stock){
		$stocks.=$stock.",";
		}
	}
   $v .= "&stockcolumn=".rtrim($stocks,",");
}
if ($this->input->post('budget')) {
    $budgetColumn=$this->input->post('budget');
	foreach($budgetColumn as $budget){
		if($budget){
		$budgets.=$budget.",";
		}
	}
   $v .= "&budgetcolumn=".rtrim($budgets,",");
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

if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}

?>

<script>
    $(document).ready(function () {
        var oTable = $('#SlRData').dataTable({
            "aaSorting": [[0, "desc"]],
                 "aLengthMenu": [[10, 25, 50, 100,500,1000,2000, -1], [10, 25, 50, 100,500,1000,2000, "<?= lang('all') ?>"]],
            "iDisplayLength":100,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('reports/getMasharikiReport/?v=1' . $v) ?>',
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
            },null,null, null, null,null, {"mRender": currencyFormat} , null, {"mRender": currencyFormat},null,{"mRender": currencyFormat},null,null],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var gtotal = 0, paid = 0, balance = 0,resale =0; qty =0; tender =0;
                for (var i = 0; i < aaData.length; i++) {
					qty += parseFloat(aaData[aiDisplay[i]][6]);
                    gtotal += parseFloat(aaData[aiDisplay[i]][7]);
					 resale += parseFloat(aaData[aiDisplay[i]][8]);
					 tender += parseFloat(aaData[aiDisplay[i]][9]);
                  // paid += parseFloat(aaData[aiDisplay[i]][14]);
                   // balance += parseFloat(aaData[aiDisplay[i]][7]);
                }
                var nCells = nRow.getElementsByTagName('th');
				 nCells[6].innerHTML = currencyFormat(parseFloat(qty));
                nCells[7].innerHTML = currencyFormat(parseFloat(gtotal));
				 nCells[8].innerHTML = currencyFormat(parseFloat(resale));
               nCells[9].innerHTML = currencyFormat(parseFloat(tender));
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
			{column_number: 9, filter_default_label: "[<?=lang('product');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
            $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('reports/getMasharikiReport/0/xls/?v=1'.$v)?>";
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
                    <?php echo form_open("reports/mashariki_report",$attrib); ?>
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
						
						
                            <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Start Month/Year", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : date('m/Y')), 'class="form-control datepicker monthPicker" id="start_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                        
                             <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("End Month/Year", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : date('m/Y')), 'class="form-control datepicker monthPicker" id="end_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4" >
                                                <?= lang("Country", "country"); ?>
                                                <div class="form-group">

                                <?php
                                //$contr[""] = "All";
                                foreach ($currencies as $cluster1) {
                                    $contr[$cluster1->id] = $cluster1->portuguese_name;
                                }
                                echo form_dropdown('s_country[]', $contr, (isset($_POST['s_country']) ? $_POST['s_country'] : ""), 'class="form-control input-tip select overflow-auto" multiple="multiple" id="s_country" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Country") . '"');
                                 
                                ?>                             
												
            </div></div>
            <div class="col-sm-4">
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
                              <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("category", "category") ?>
                                <?php
                                $cat[''] = "Select Brand";
                                foreach ($categories as $category) {
                                    $cat[$category->id] = $category->name;
                                }
                                echo form_dropdown('category', $cat, (isset($_POST['category']) ? $_POST['category'] : ''), 'class="form-control select" id="category" placeholder="' . lang("select") . " " . lang("category") . '" style="width:100%"')
                                ?>
                            </div>
                        </div>
                                                 <div class="col-md-4" style="z-index:1000">
                  <div class="form-group">
                                                <?= lang("Product GMID", "product"); ?>
                                                <?php
                                                
                                                $q = $this->db->get('products');
                                                 $products["all"]="Select All";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $products[$row->id] = $row->name."(".$row->code.")";
            }
        }
                                                
                                                echo form_dropdown('products[]',$products,($post['products']), 'id="prodcts" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("products") . '"  style="width:100%;" ');
                                                ?>
            </div>  
                    
                </div>
                             <div class="col-sm-4" >
                                 <div class="form-group">
                          
                                <label class="control-label" for="biller"><?= lang("Distributor"); ?></label>
                                     <?php
                                     //$cp['']='ALL';
                                      foreach ($companies as $comp) {
                                         $country= $this->settings_model->getCurrencyByID($comp->country);
                                    $cp[$comp->id] = $comp->name."  (".$country->country.")";
                                }
                                     echo form_dropdown('customer[]', $cp, $_POST['customer'], 'id="customer" class="form-control input-tip select " multiple="multiple" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("Distributor") . '"  style="width:100%;" ');?>

                                
                                </div>
                        </div>
           
           
           
                   <div class="col-sm-4" >
                                 <div class="form-group">
                          
                                <label class="control-label" for="biller"><?= lang("Customer"); ?></label>
                                     <?php
                                     //$cpc['']='ALL';
                                      foreach ($customers as $comp1) {
                                         $country= $this->settings_model->getCurrencyByID($comp1->country);
                                    $cpc[$comp1->id] = $comp1->name."  (".$country->country.")";
                                }
                                     echo form_dropdown('customer1[]', $cpc, $_POST['customer1'], 'id="customer1" class="form-control input-tip select " multiple="multiple" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("Customer") . '"  style="width:100%;" ');?>

                                
                                </div>
                        </div>
           
           
			                      
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
                        
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Sales"); ?></label>
                                <?php
                                $cludsle[""] = "ALL";
                                
                                    $cludsle['1'] ='PSO Qty';
                                    $cludsle['2'] ='PSO Value';
                                    $cludsle['3'] ='SSO Qty';
                                    $cludsle['4'] ='SSO Value';
                                echo form_dropdown('sales[]', $cludsle, (isset($_POST['sales']) ? $_POST['sales'] : ""), 'class="form-control" multiple="multiple" id="sales" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("team_name") . '"');
                                ?>
                            </div>
                        </div>
                           <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Stock"); ?></label>
                                <?php
                                $cludstck[""] = "ALL";
                                
                                    $cludstck['1'] ='Stock Qty';
                                    $cludstck['2'] ='Stock Value';
                                   
                                echo form_dropdown('stock[]', $cludstck, (isset($_POST['stock']) ? $_POST['stock'] : ""), 'class="form-control" multiple="multiple" id="stock" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("stock") . '"');
                                ?>
                            </div>
                        </div>
                         <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Budget"); ?></label>
                                <?php
                                $cludbdgt[""] = "All";
                                
                                    $cludbdgt['1'] ='PSO Budget QTY';
                                    $cludbdgt['2'] ='PSO Budget Value';
                                    $cludbdgt['3'] ='SSO Budget QTY';
                                    $cludbdgt['4'] ='SSO Budget Value';
                                    
                                echo form_dropdown('budget[]', $cludbdgt, (isset($_POST['budget']) ? $_POST['budget'] : ""), 'class="form-control" multiple="multiple" id="budget" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("Budget") . '"');
                                ?>
                            </div>
                        </div>

                 	<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Team"); ?></label>
                                <?php
                                //$cluteam[""] = "All";
                                foreach ($tm as $teams) {
                                    $cluteam[$teams->id] = $teams->name;
                                }
                                echo form_dropdown('team[]', $cluteam, (isset($_POST['team']) ? $_POST['team'] : ""), 'class="form-control" multiple="multiple" id="team" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("team_name") . '"');
                                ?>
                            </div>
                        </div>
                        
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("DSM"); ?></label>
                                <?php
                                //$cludsm[""] = "Select dsm";
                                
                                    $cludsm['1'] ='DSM Alignment';
                                    $cludsm['2'] ='DSM Name';
                                echo form_dropdown('dsm[]', $cludsm, (isset($_POST['dsm']) ? $_POST['dsm'] : ""), 'class="form-control" multiple="multiple" id="dsm" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("DSM") . '"');
                                ?>
                            </div>
                        </div>
                        
                        
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("MSR"); ?></label>
                                <?php
                               // $clumsr[""] = "Select msr";
                                 $clumsr['1'] ='MSR Alignment';
                                    $clumsr['2'] ='MSR Name';
                                    
                                echo form_dropdown('msr[]', $clumsr, (isset($_POST['msr']) ? $_POST['msr'] : ""), 'class="form-control" multiple="multiple" id="msr" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("MSR") . '"');
                                ?>
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
                            <th><?php echo $this->lang->line("Distributor"); ?></th>
                              <th><?php echo $this->lang->line("Brand"); ?></th>
                              <th><?php echo $this->lang->line("Product"); ?></th>
                            <th><?php echo $this->lang->line("BU"); ?></th>
                            
                          
                            <th><?php echo $this->lang->line("Unit Price"); ?></th>
                            <th><?php echo $this->lang->line("SSO(qty)"); ?></th>
                            <th><?php echo $this->lang->line("SSO Value(Euros)"); ?></th>
                            <th><?php echo $this->lang->line("Stock(qty)"); ?></th>
                            
							<th><?= lang("Stock Value(Euros)"); ?></th>
							
							 <th><?php echo $this->lang->line("Budget"); ?></th>
                            
							<th><?= lang("Customer"); ?></th>
						
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