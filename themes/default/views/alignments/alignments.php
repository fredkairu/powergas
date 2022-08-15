<script>
    $(document).ready(function () {
        var oTable = $('#SLData').dataTable({
            "aaSorting": [[0, "asc"], [1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100,500, -1], [10, 25, 50, 100,500, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('customers/getAlignments')?>',
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
                nRow.className = "invoice_link";
                //if(aData[7] > aData[9]){ nRow.className = "product_link warning"; } else { nRow.className = "product_link"; }
                return nRow;
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            },null,null,null,null,null, null,null,null,null,null,null,null, {"bSortable": false}], //{"mRender": row_status} ,{"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": currencyFormat}, {"mRender": row_status}
            "fnFooterCallback": function (nRow, aaData, iStart, iEnd, aiDisplay) {
//                var gtotal = 0, paid = 0, balance = 0;
//                for (var i = 0; i < aaData.length; i++) {
//                    gtotal += parseFloat(aaData[aiDisplay[i]][11]);
//                    paid += parseFloat(aaData[aiDisplay[i]][12]);
//                    balance += parseFloat(aaData[aiDisplay[i]][13]);
//                }
                var nCells = nRow.getElementsByTagName('th');
//                nCells[11].innerHTML = currencyFormat(parseFloat(gtotal));
//                nCells[12].innerHTML = currencyFormat(parseFloat(paid));
//                nCells[13].innerHTML = currencyFormat(parseFloat(balance));
            }
        }).fnSetFilteringDelay().dtFilter([
           {column_number: 1, filter_default_label: "[<?=lang('scenario');?>]", filter_type: "text", data: []},
                {column_number: 2, filter_default_label: "[<?=lang('budget_forecast');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('date');?>]", filter_type: "text", data: []},
            
            {column_number: 4, filter_default_label: "[<?=lang('country');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('customer');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('market');?>]", filter_type: "text", data: []},
             {column_number: 7, filter_default_label: "[<?=lang('brand');?>]", filter_type: "text", data: []},
            {column_number: 8, filter_default_label: "[<?=lang('Product');?>]", filter_type: "text", data: []},
             {column_number: 9, filter_default_label: "[<?=lang('BU');?>]", filter_type: "text", data: []},
              {column_number:10, filter_default_label: "[<?=lang('Budget_Qty');?>]", filter_type: "text", data: []},
             {column_number:11, filter_default_label: "[<?=lang('Budget Amount');?>]", filter_type: "text", data: []},
              {column_number:12, filter_default_label: "[<?=lang('Avg Price');?>]", filter_type: "text", data: []},
            
        ], "footer");

        
    

        $(document).on('click', '.sledit', function (e) {
            if (localStorage.getItem('slitems')) {
                e.preventDefault();
                var href = $(this).attr('href');
                bootbox.confirm("<?=lang('you_will_loss_sale_data')?>", function (result) {
                    if (result) {
                        window.location.href = href;
                    }
                });
            }
        });

    });

</script>

<?php if ($Owner) {
    echo form_open('budgets/delete_budget', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-heart"></i><?= lang('Alignments'); ?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip"
                                                                                  data-placement="left"
                                                                                  title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= site_url('budgets/import_budgets') ?>"><i class="fa fa-plus-circle"></i> <?= lang('import_budget') ?></a></li>
                        <li><a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
                        <li><a href="#" id="pdf" data-action="export_pdf"><i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?></a></li>
                        <li class="divider"></li>
                        <li><a href="#" class="bpo" title="<b><?= $this->lang->line("delete_budgets") ?></b>" data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left"><i class="fa fa-trash-o"></i> <?= lang('delete_budget') ?></a></li>
                    </ul>
                </li>
                <?php if (!empty($warehouses)) { ?>
                    <li class="dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-building-o tip" data-placement="left" title="<?= lang("warehouses") ?>"></i></a>
                        <ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">
                            <li><a href="<?= site_url('sales') ?>"><i class="fa fa-building-o"></i> <?= lang('all_warehouses') ?></a></li>
                            <li class="divider"></li>
                            <?php
                            foreach ($warehouses as $warehouse) {
                                echo '<li><a href="' . site_url('sales/' . $warehouse->id) . '"><i class="fa fa-building"></i>' . $warehouse->name . '</a></li>';
                            }
                            ?>
                        </ul>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="SLData" class="table table-bordered table-hover table-striped">
                        <thead>
                        <tr>
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                             <th><?php echo $this->lang->line("Scenario"); ?></th>
                             <th><?php echo $this->lang->line("Budget_or_Forecast"); ?></th>
                            <th><?php echo $this->lang->line("Date"); ?></th>
                            
                            <th><?php echo $this->lang->line("Country"); ?></th>
                            <th><?php echo $this->lang->line("Customer"); ?></th>
                            <th><?php echo $this->lang->line("Market"); ?></th>
                           <th><?php echo $this->lang->line("Brand"); ?></th>
                            
                            <th><?php echo $this->lang->line("Product"); ?></th>
                            <th><?php echo $this->lang->line("BU"); ?></th>
                            <th><?php echo $this->lang->line("Budget Qty"); ?></th>
                            <th><?php echo $this->lang->line("Budget Value"); ?></th>
                             <th><?php echo $this->lang->line("Avg Price"); ?></th>
                             
                            <th style="width:80px; text-align:center;"><?php echo $this->lang->line("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="13"
                                class="dataTables_empty"><?php echo $this->lang->line("loading_data"); ?></td>
                        </tr>
                        </tbody>
                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                             <th></th>
                            <th></th>  <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th> <th></th> <th></th> <th></th>
                            
                            <th></th><th></th>
                          <!--  <th><?php echo $this->lang->line("grand_total"); ?></th>
                            <th><?php echo $this->lang->line("paid"); ?></th>
                            <th><?php echo $this->lang->line("balance"); ?></th>
                            <th></th>-->
                            <th style="width:80px; text-align:center;"><?php echo $this->lang->line("actions"); ?></th>
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
