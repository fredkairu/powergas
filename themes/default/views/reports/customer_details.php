 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php

$v = "";
 if($this->input->post('cluster')){
  $v .= "&cluster=".$this->input->post('cluster');
  } 


?>

<script>
    $(document).ready(function () {
        var oTable = $('#SlRData').dataTable({
            "aaSorting": [[0, "desc"]],
                 "aLengthMenu": [[10, 25, 50, 100,500,1000,2000, -1], [10, 25, 50, 100,500,1000,2000, "<?= lang('all') ?>"]],
            "iDisplayLength":100,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('reports/getCustomersReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null,null, null, null,null,null],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var gtotal = 0, paid = 0, balance = 0,resale =0; qty =0; tender =0;
                for (var i = 0; i < aaData.length; i++) {
					
                   // balance += parseFloat(aaData[aiDisplay[i]][7]);
                }
                var nCells = nRow.getElementsByTagName('th');
				 
               // nCells[7].innerHTML = currencyFormat(parseFloat(balance));
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Customer  Name');?>]", filter_type: "text", data: []},
            {column_number: 1, filter_default_label: "[<?=lang('MSR');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('MSR Name');?>]", filter_type: "text", data: []},
			{column_number: 3, filter_default_label: "[<?=lang('Products');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('DSM');?>]", filter_type: "text", data: []},
             {column_number: 5, filter_default_label: "[<?=lang('DSM Name');?>]", filter_type: "text", data: []},
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
document.getElementById("psodistributor").style.display = "block";
document.getElementById("alldistributor").style.display = "none";
}
else if($("#type").val()=="SSO"){
document.getElementById("psodistributor").style.display = "none";
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
		
            return false;
        });
		
    });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-heart"></i><?= lang('Customers report'); ?> <?php
         
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
                    <?php echo form_open("reports/customer_details",$attrib); ?>
                    <div class="row">
						
						<div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Select Customer"); ?></label>
                                <?php
                                $cl[""] = "";
                                foreach ($clusters as $cluster) {
                                    $cl[$cluster->id] = $cluster->name;
                                }
                                echo form_dropdown('cluster', $cl, (isset($_POST['cluster']) ? $_POST['cluster'] : ""), 'class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("") . '"');
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
                 
                         <div class="col-sm-4" style="display:none" id="alldistributor">
                            <div class="form-group">
                                <label class="control-label" for="customer"><?= lang("customer"); ?></label>
                                <?php echo form_input('customer', (isset($_POST['customer']) ? $_POST['customer'] : ""), 'class="form-control" id="customer" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("customer") . '"'); ?>
                            </div>
                        </div>
                    
                        <div class="col-md-4" style="display:none" id="psodistributor">
                                            <div class="form-group">
                                                <?= lang("Customer", "Customer"); ?>
                                                <?php
                                                $SIdist[''] = "";
                                               
                        foreach ($sanoficustomer as $aSIdist) {
                            $SIdist[$aSIdist->name] = $aSIdist->name;
                        }
                             echo form_dropdown('PSOdist', $SIdist,($_POST['PSOdist']), 'id="PSOdist" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("Customer") . '"  ');
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
                        <tr>
                            <th><?= lang("Customer Name"); ?></th>
                             <th><?= lang("MSR"); ?></th>
                            <th><?= lang("MSR Name"); ?></th>
                            <th><?= lang("Products"); ?></th>
							<th><?= lang("DSM"); ?></th>
                            <th><?= lang("DSM Name"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="5" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                           
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
            window.location.href = "<?=site_url('reports/getSalesReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('reports/getSalesReport/0/xls/?v=1'.$v)?>";
            return false;
        });
     $(document).on('click', '#delete', function (e) {

window.location.href = "<?=site_url('sales/sale_actions/')?>";
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