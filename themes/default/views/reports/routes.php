<?php
$v = "";

if ($this->input->post('vehicle')) {
    $v .= "&vehicle=" . $this->input->post('vehicle');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
?>
<!-- <script>
    $(document).ready(function () {
        var oTable = $('#PrRData').dataTable({
            "aaSorting": [[3, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('reports/getVehiclesReport/?v=1'.$v) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [null, {"mRender": currencyFormat, "bSearchable": false}, {"mRender": currencyFormat, "bSearchable": false}],
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
                var sQty = 0, sAmt = 0;
                for (var i = 0; i < aaData.length; i++) {
                    sQty += parseFloat(aaData[aiDisplay[i]][1]);
                    sAmt += parseFloat(aaData[aiDisplay[i]][2]);
                }
                var nCells = nRow.getElementsByTagName('th');
                
                nCells[1].innerHTML = decimalFormat(parseFloat(sQty));
                
                nCells[2].innerHTML = currencyFormat(parseFloat(sAmt));
                
            }
        }).fnSetFilteringDelay().dtFilter([
            {column_number: 0, filter_default_label: "[<?=lang('Grand_Total');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script> -->
<script type="text/javascript">
    $(document).ready(function () {
        $('#form').hide();
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
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('Vehicles_Report'); ?><?php
            if ($this->input->post('vehicle') && $this->input->post('start_date')) {
                                
                    foreach ($vehicles as $vehicle) {
                        if($vehicle->id==$this->input->post('vehicle')){
                            echo " For ".$vehicle->plate_no." ";
                        }
                    }
                echo "From " . $this->input->post('start_date') . " to " . $this->input->post('end_date');
            }
            ?></h2>

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

    
                <div class="clearfix"></div>


 <form id="routesfilter">
  <div class="form-row">
    <div class="form-group col-md-4">
      <label for="inputCity">Vehicle</label>
      <select name="vehicle_ids" required id="vehicle" class="form-control">
        <option selected>Choose...</option>
        <?php 
  foreach ($vehicles as $vehicle) {
      echo "<option value='$vehicle->id'>".$vehicle->plate_no."</option>";
    }
        ?>
        
      </select>
    </div>
    <div class="form-group col-md-4">
      <label for="inputState">Day</label>
      <select name="day_ids" id="day" class="form-control">
        <option selected>Choose...</option>
        <option value="1">Monday</option>
        <option value="2">Tuesday</option>
        <option value="3">Wednesday</option>
        <option value="4">Thursday</option>
        <option value="5">Friday</option>
        <option value="6">Saturday</option>
        <option value="7">Sunday</option>
      </select>
    </div>
    <div class="form-group col-md-4">
      <label for="inputZip">Done</label>
       <button type="submit" value="submit" id="submitFilter" class="btn btn-primary form-control">Generate</button> 
    </div>
  </div>
 
 
</form>
                 <div  id ="tableHolder" class="table-responsive">
                    <!-- <table id="PrRData"
                           class="table table-striped table-bordered table-condensed table-hover dfTable reports-table"
                           style="margin-bottom:5px;">
                        <thead>
                        <tr class="active">
                            <th><?= lang("Product_Name"); ?></th>
                            <th><?= lang("Product_Quantity"); ?></th>
                            <th><?= lang("Product_Amount"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="3" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th><?= lang("Product_Name"); ?></th>
                            <th><?= lang("Product_Quantity"); ?></th>
                            <th><?= lang("Product_Amount"); ?></th>
                        </tr>
                        </tfoot>
                    </table> -->
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
            // window.location.href = "<?=site_url('reports/getVehiclesReport/pdf/?v=1'.$v)?>";
            return false;
        });
        // $('#printTest').click(function (event) {
        //     event.preventDefault();
        //     $("#tableHolder").printThis();
        //     return false;
        // });
        $('#xls').click(function (event) {
            event.preventDefault();
            // window.location.href = "<?=site_url('reports/getVehiclesReport/0/xls/?v=1'.$v)?>";
            return false;
        });
        $('#image').click(function (event) {
            // event.preventDefault();
            // html2canvas($('.box'), {
            //     onrendered: function (canvas) {
            //         var img = canvas.toDataURL()
            //         window.open(img);
            //     }
            // });
            return false;
        });

    });

$("#submitFilter").click(function (e) {

    e.preventDefault();
var formData = {
       day_ids: $("#day").val(),
     vehicle_ids: $("#vehicle").val(),
   };
   // alert(formData); 
   $.ajax({
     type: "POST",
     url: "reports/getRoutes",
     data: formData,
     dataType: "json",
     encode: true,
   }).done(function (data) {
   
        var table='<table id="PrRData"'+
                           'class="table table-striped table-bordered table-condensed table-hover dfTable reports-table"'+
                           'style="margin-bottom:5px;">'+
                        '<thead>'+
                       '<tr class="active">'+
                            '<th>ID</th>'+
                            '<th>Name</th>'+
                            '<th>Latitude</th>'+
                            '<th>Longitude</th>'+
                            '<th>Shop</th>'+
                            '<th>Position</th>'+
                        '</tr>'+
                        '</thead>'+
                        '<tbody>';

                               $.each(data, function (idx, obj) {                                   
                                    table += '<tr><td>'+obj.allid+'</td><td>'+obj.name+'</td><td>'+obj.lat+'</td><td>'+obj.lng+'</td><td>'+obj.shop_name+'</td><td>'+obj.positions+'</td></tr>';
                              });
                        table += '</table>';
                        $("#tableHolder").html(table);   
   });

  });
</script>