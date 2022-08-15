 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<?php

$v = "";
/* if($this->input->post('name')){
  $v .= "&name=".$this->input->post('name');
  } */
if ($this->input->post('reference_no')) {
    $v .= "&reference_no=" . $this->input->post('reference_no');
}
if ($this->input->post('supplier')) {
    $v .= "&supplier=" . $this->input->post('supplier');
}
if ($this->input->post('warehouse')) {
    $v .= "&warehouse=" . $this->input->post('warehouse');
}
if ($this->input->post('user')) {
    $v .= "&user=" . $this->input->post('user');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
if ($this->input->post('PSOdist')) {
    $v .= "&PSOdist=" . $this->input->post('PSOdist');
}
if ($this->input->post('cluster')) {
    $v .= "&cluster=" . $_POST['cluster'];
	//print_r( $_POST['cluster']);
	//die;
}
if ($this->input->post('category')) {
	$category=$this->input->post('category');
	foreach($category as $categ){
		if($categ){
		$categorys.=$categ.",";
		}
	}
    $v .= "&category=".rtrim($categorys,",");
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
?>
<script type="text/javascript">
    $(document).ready(function () {
        var oTable = $('#PoRData').dataTable({
            "aaSorting": [[1, "desc"]],
                 "aLengthMenu": [[10, 25, 50, 100,500,1000,2000, -1], [10, 25, 50, 100,500,1000,2000, "<?= lang('all') ?>"]],
            //"iDisplayLength": <?= $Settings->rows_per_page ?>,
             "iDisplayLength": 50,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('reports/getPurchasesReport/?v=1' . $v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                //$("td:first", nRow).html(oSettings._iDisplayStart+iDisplayIndex +1);
                nRow.id = aData[0];
                nRow.className = "purchase_link";
                //if(aData[7] > aData[9]){ nRow.className = "product_link warning"; } else { nRow.className = "product_link"; }
                return nRow;
            },
            "aoColumns": [
               {
                "bSortable": false,
                "mRender": checkbox
            },null, null,null, {"mRender": currencyFormat},null], //{"mRender": fld}
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var gtotal = 0, paid = 0, qtotal = 0; ntotal=0; vtotal=0;
                for (var i = 0; i < aaData.length; i++) {
                    //qtotal += parseFloat(aaData[aiDisplay[i]][7]);
                    //vtotal += parseFloat(aaData[aiDisplay[i]][9]);
                    gtotal += parseFloat(aaData[aiDisplay[i]][4]);
                  
                }
                var nCells = nRow.getElementsByTagName('th');
                 nCells[4].innerHTML = currencyFormat(parseFloat(qtotal));
               // nCells[9].innerHTML = currencyFormat(parseFloat(vtotal));
                //nCells[8].innerHTML = currencyFormat(parseFloat(gtotal));
           
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('date');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('supplier');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('Product_Name');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('Product_Quantity');?>]", filter_type: "text", data: []},

           
        ], "footer");
    });
</script>
<script type="text/javascript">
    $(document).ready(function () {
        
          		$(document).on('change', '#warehouse', function (e) {
//alert ($("#warehouse").val());
if($("#warehouse").val()=="2"){ //PSO
document.getElementById("detailsrow").style.display = "none";
 document.getElementById("PSOdetailsrow").style.display = "block";
}
else if ($("#warehouse").val()=="10"){ //SSO

document.getElementById("detailsrow").style.display = "block";
 document.getElementById("PSOdetailsrow").style.display = "none";
}


	});
        
        
        
        $('#form').hide();
        <?php if ($this->input->post('customer')) { ?>
        $('#supplier').val(<?= $this->input->post('supplier') ?>).select2({
            minimumInputLength: 1,
            data: [],
            initSelection: function (element, callback) {
                $.ajax({
                    type: "get", async: false,
                    url: site.base_url + "suppliers/suggestions/" + $(element).val(),
                    dataType: "json",
                    success: function (data) {
                        callback(data.results[0]);
                    }
                });
            },
            ajax: {
                url: site.base_url + "suppliers/suggestions",
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

        $('#supplier').val(<?= $this->input->post('supplier') ?>);
        <?php } ?>
        $('.toggle_down').click(function () {
            $("#form").slideDown();
            return false;
        });
        $('.toggle_up').click(function () {
            $("#form").slideUp();
            return false;
        });
    });
</script>

<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-star"></i><?= lang('Stock'); ?> <?php
            if ($this->input->post('start_date')) {
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?></h2>
        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li class="divider"></li>
                        <li><a href="#" class="bpo" title="<b><?= $this->lang->line("delete_stock") ?></b>" data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left"><i class="fa fa-trash-o"></i> <?= lang('delete_stock') ?></a></li>
                    </ul>
                </li>
                 </ul>
                </div>
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

                    <?php echo form_open("reports/purchases"); ?>
                    <div class="row">
                        <div class="col-sm-4">
                            <div class="form-group">
                                <label class="control-label" for="warehouse"><?= lang("warehouse"); ?></label>
                                <?php
                                $wh[""] = "SSO";
                                // foreach ($warehouses as $warehouse) {
                                //     $wh[$warehouse->id] = $warehouse->name;
                                // }
                                echo form_dropdown('warehouse', $wh, (isset($_POST['warehouse']) ? $_POST['warehouse'] : ""), 'class="form-control" id="warehouse" data-placeholder="' . $this->lang->line("select") . " " . $this->lang->line("warehouse") . '"');
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Start_date*", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control datepicker monthPicker" id="start_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("End_date*", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control datepicker monthPicker" id="end_date" autocomplete="off"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="PSOdetailsrow" >
                        <div class="col-sm-4">
                           <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Distributor"); ?></label>
                                     <?php
                                     //$cp['']='Select Distributor';
                                      foreach ($companies as $comp) {
                                         $country= $this->settings_model->getCurrencyByID($comp->country);
                                    $cp[$comp->id] = $comp->name."  (".$country->country.")";
                                }
                                     echo form_dropdown('customer', $cp, $_POST['customer'], 'id="customer" class="form-control input-tip select " multiple="multiple" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '"  style="width:100%;" ');?>

                                
                                </div>
                        </div>
                          



                        </div>
                    <div class="row" id="detailsrow" style="display:none">
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
                        <div class="col-sm-4" >
                            <div class="form-group">
                                <label class="control-label" for="biller"><?= lang("Distributor"); ?></label>
                                     <?php
                                     $cp['']='Select Distributor';
                                      foreach ($companies as $comp) {
                                         $country= $this->settings_model->getCurrencyByID($comp->country);
                                    $cp[$comp->id] = $comp->name."(".$country->country.")";
                                }
                                     echo form_dropdown('supplier', $cp, $_POST['supplier'], 'id="supplier_id" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("customer") . '"  style="width:100%;" ');?>

                                
                                </div>
                        </div>
                        
                        
                      

                        </div>
                         
                    <div class="form-group">
                        <div
                            class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                            <div class="controls"> <?php echo form_button('reset', $this->lang->line("reset"), 'class="btn btn-primary pull-right" id="reset"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>
                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="PoRData"
                           class="table table-bordered table-hover table-striped table-condensed reports-table">
                        <thead>
                        <tr> <th style="min-width:10px; width:10px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang("date"); ?></th>

                            <th><?= lang("customer"); ?></th>

                            <th><?= lang("Vehicle"); ?></th>
                            <th><?= lang("product_qty"); ?></th>
                            
                           


                            <th><?php echo $this->lang->line("Actions"); ?></th>
                        
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
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
});
   
   
        $('#pdf').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('reports/getPurchasesReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('reports/getPurchasesReport/0/xls/?v=1'.$v)?>";
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
        
        $("#reset").click(function(e){
          location.href = site.base_url + "reports/purchases";
        })
        
    });
</script>