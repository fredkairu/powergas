<style type="text/css" media="screen">
    #PRData td:nth-child(6), #PRData td:nth-child(7) {
        text-align: right;
    }
    <?php if($Owner || $Admin || $this->session->userdata('show_cost')) { ?>
    #PRData td:nth-child(8) {
        text-align: right;
    }
    <?php } ?>
</style>
<!-- <script>
    var oTable;
    $(document).ready(function () {
        oTable = $('#PRData').dataTable({
            "aaSorting": [[2, "asc"], [3, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('products/getProducts1'.($warehouse_id ? '/'.$warehouse_id : '')) ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            'fnRowCallback': function (nRow, aData, iDisplayIndex) {
                var oSettings = oTable.fnSettings();
                nRow.id = aData[0];
                nRow.className = "product_link";
                //if(aData[7] > aData[9]){ nRow.className = "product_link warning"; } else { nRow.className = "product_link"; }
                return nRow;
            },
            "aoColumns": [
                {"bSortable": false, "mRender": checkbox},null,null,{"bSortable": false}
            ]
        }).fnSetFilteringDelay().dtFilter([

            {column_number: 1, filter_default_label: "[<?=lang('Product_Name');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('Product_Price');?>]", filter_type: "text", data: []},

         
        ], "footer");

    });
</script> -->
<?php if ($Owner) {
    echo form_open('products/product_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-barcode"></i><?= lang('products') . ' (' . ($warehouse_id ? $warehouse->name : lang('all_warehouses')) . ')'; ?>
        </h2>


    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="PRData" class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th style="text-align: center;">
                                <!-- <input class="checkbox checkth" type="checkbox" name="check"/> -->
                            </th>
                            <th><?= lang("Product_Name") ?></th>
                            <th><?= lang("Product_Price") ?></th>


                            <th style="min-width:65px; text-align:center;"><?= lang("actions") ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- <tr>
                            <td colspan="5" class="dataTables_empty"><?= lang('loading_data_from_server'); ?></td>
                        </tr> -->
                        <?php
                    foreach($products as $product)
                    {
                        echo "<tr><td></td><td>".$product->name."</td><td>".$product->price."</td><td>                <a class=\"tip\" title='" . $this->lang->line("add_discount") . "' href='" . site_url('products/add_discount/'.$product->id.'') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a> 
                        <a class=\"tip\" title='" . $this->lang->line("view_discount") . "' href='" . site_url('products/view_discounts/'.$product->id.'') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-eye\"></i></a> </td></tr>";
                    }
                        ?>
                        </tbody>

                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th></th>
                            <th></th>
                            <th style="width:65px; text-align:center;"><?= lang("actions") ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>

<script type="text/javascript">
$(document).ready(function(e){
    $("#PRData").DataTable();
   $("#distributorpricing").click(function(e){
       e.preventDefault();
       // alert($("#country").val());
     $.ajax({
  url: "<?= site_url('products/distributor_template') ?>",
  data:{"value": "<?= $this->security->get_csrf_hash() ?>"}
}).done(function(url) {
 window.open(url);
});
   });
});



</script>
