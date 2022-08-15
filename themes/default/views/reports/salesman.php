<?php
$v = "";

if ($this->input->post('salesperson')) {
    $v .= "&salesperson=" . $this->input->post('salesperson');
}
if ($this->input->post('start_date')) {
    $v .= "&start_date=" . $this->input->post('start_date');
}
if ($this->input->post('end_date')) {
    $v .= "&end_date=" . $this->input->post('end_date');
}
if ($this->input->post('payment_method')) {
    $v .= "&payment_method=" . $this->input->post('payment_method');
}
?>
<script>
    $(document).ready(function () {
        var oTable = $('#PrRData').dataTable({
            "aaSorting": [[3, "desc"], [2, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('reports/getSalespersonReport/?v=1'.$v) ?>',
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
</script>
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
        <h2 class="blue"><i class="fa-fw fa fa-folder-open"></i><?= lang('SalesPerson_Report'); ?><?php
            if ($this->input->post('salesperson') && $this->input->post('start_date')) {
                                
                    foreach ($salespeople as $salesperson) {
                        if($salesperson->id==$this->input->post('salesperson')){
                            echo " For ".$salesperson->name." ";
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

                <div id="form">

                    <?php echo form_open("reports/salespeople"); ?>
                    <div class="row">

                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Salesperson", "salesperson") ?>
                                <?php
                                $cat[''] = "";
                                foreach ($salespeople as $salesperson) {
                                    $cat[$salesperson->id] = $salesperson->name;
                                }
                                echo form_dropdown('salesperson', $cat, (isset($_POST['salesperson']) ? $_POST['salesperson'] : ''), 'class="form-control select" id="salesperson" placeholder="' . lang("select") . " " . lang("Salesperson") . '" style="width:100%"')
                                ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("Payment Method", "payment_method") ?>
                                <select class="form-control" name="payment_method">
                                    <option value="All" <?= $this->input->post('payment_method') == "All" ? "selected" : "" ?>>All</option>
                                    <option value="Cash Payment" <?= $this->input->post('payment_method') == "Cash Payment" ? "selected" : "" ?>>Cash</option>
                                    <option value="Mpesa Payment" <?= $this->input->post('payment_method') == "Mpesa Payment" ? "selected" : "" ?>>Mpesa</option>
                                    <option value="Invoice Payment" <?= $this->input->post('payment_method') == "Invoice Payment" ? "selected" : "" ?>>Invoice</option>
                                    <option value="Cheque Payment" <?= $this->input->post('payment_method') == "Cheque Payment" ? "selected" : "" ?>>Cheque</option>
                                </select>
                                
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("start_date", "start_date"); ?>
                                <?php echo form_input('start_date', (isset($_POST['start_date']) ? $_POST['start_date'] : ""), 'class="form-control" id="start_date"'); ?>
                            </div>
                        </div>
                        <div class="col-sm-4">
                            <div class="form-group">
                                <?= lang("end_date", "end_date"); ?>
                                <?php echo form_input('end_date', (isset($_POST['end_date']) ? $_POST['end_date'] : ""), 'class="form-control" id="end_date"'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div
                                class="controls"> <?php echo form_submit('submit_report', $this->lang->line("submit"), 'class="btn btn-primary"'); ?> </div>
                    </div>
                    <?php echo form_close(); ?>

                </div>

                <div class="clearfix"></div>

                <div class="table-responsive">
                    <table id="PrRData"
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
            window.location.href = "<?=site_url('reports/getSalespersonReport/pdf/?v=1'.$v)?>";
            return false;
        });
        $('#xls').click(function (event) {
            event.preventDefault();
            window.location.href = "<?=site_url('reports/getSalespersonReport/0/xls/?v=1'.$v)?>";
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