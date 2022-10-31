<!-- <script>
    $(document).ready(function () {
        var oTable = $('#CusData').dataTable({
            "aaSorting": [[1, "asc"]],
            //"serverSide": true,
            "aLengthMenu": [[10, 25, 50, 100,200, -1], [10, 25, 50, 100,200, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 
            "pageLength": 10,
            'bServerSide': true,
            'sAjaxSource': '<?= site_url('customers/getCustomers1') ?>',
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
            }, null, null, null, null, null, null, null,  {"mRender": row_status2}, {"bSortable": false}]
        }).dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('created_at');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('name');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('email_address');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('phone');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('County');?>]", filter_type: "text", data: []},
            {column_number: 6, filter_default_label: "[<?=lang('city');?>]", filter_type: "text", data: []},
            {column_number: 7, filter_default_label: "[<?=lang('Sales_Person');?>]", filter_type: "text", data: []},
            {column_number: 8, filter_default_label: "[<?=lang('status');?>]", filter_type: "text", data: []},
            {column_number: 9, filter_default_label: "[<?=lang('actions');?>]", filter_type: "text", data: []}
        ], "footer");
    });
</script> -->
<?php if ($Owner) {
    echo form_open('customers/sanoficustomer_actions', 'id="action-form"');
    
} ?>
<div class="box">

    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('customers1'); ?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip"
                                                                                  data-placement="left"
                                                                                  title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><a href="<?= site_url('customers/add1'); ?>" data-toggle="modal" data-target="#myModal"
                               id="add"><i class="fa fa-plus-circle"></i> <?= lang("add_customer1"); ?></a></li>
                               
                        <li><a href="<?= site_url('customers/import_csv1'); ?>" data-toggle="modal"
                               data-target="#myModal"><i class="fa fa-plus-circle"></i> <?= lang("import_by_csv1"); ?>
                            </a></li>
                            
                        <?php if ($Owner) { ?>
                        <li><a href="<?= site_url('customers/import_customeralign_all'); ?>" data-toggle="modal"
                               data-target="#myModal"><i class="fa fa-plus-circle"></i> <?= lang("Customer_Distributor_Mapping"); ?>
                            </a></li>
                            <li><a href="#" id="excel" data-action="export_excel"><i
                                        class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
                            <li><a href="#" id="pdf" data-action="export_pdf"><i
                                        class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?></a></li>
                                        
                            <li class="divider"></li>
                            
                            <li><a href="#" class="bpo" title="<b><?= $this->lang->line("delete_customers1") ?></b>"
                                   data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>"
                                   data-html="true" data-placement="left"><i
                                        class="fa fa-trash-o"></i> <?= lang('delete_customers1') ?></a></li>
                        <?php } ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="CusData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            
                            <th><?= lang("date"); ?></th>
                            <th><?= lang("name"); ?></th>
                            <th><?= lang("email"); ?></th>
                            <th><?= lang("phone"); ?></th>
                            <th><?= lang("County"); ?></th>
                            <th><?= lang("city"); ?></th>
                            <th><?= lang("Sales_Person"); ?></th>
                            <th><?= lang("status"); ?></th>

                            <th style="width:40px;"><?= lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <!-- <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr> -->
                        <?php
                        foreach($customers as $customer)
                        {
                            
                            $data= "<tr><td></td>
                            <td>".$customer->created_at."</td>
                            <td>".$customer->name."</td>
                            <td>".$customer->email."</td>
                            <td>".$customer->phone."</td>
                            <td>".$customer->french_name."</td>
                            <td>".$customer->city."</td>
                            <td>".$customer->sales_person_name."</td>
                            <td>".$customer->active."</td>
                            <td>
                            <center>

                            <a class=\"tip\" title='" . $this->lang->line("add_shop") . "' href='" . site_url('customers/add_shop/'.$customer->id.'') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-building\"></i></a>
                            <a class=\"tip\" title='" . $this->lang->line("view_shops") . "' href='" . site_url('customers/view_shops/'.$customer->id.'') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list\"></i></a>
                            <a class=\"tip\" title='" . $this->lang->line("Add_Credit_Limit") . "' href='" . site_url('customers/add_limit/'.$customer->id.'') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-plus-circle\"></i></a>
                            <a class=\"tip\" title='" . $this->lang->line("View_Credit_Limit") . "' href='" . site_url('customers/view_limit/'.$customer->id.'') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-credit-card\"></i></a>
                            <a class=\"tip\" title='" . $this->lang->line("add_payment_method") . "' href='" . site_url('customers/add_payment_method/'.$customer->id.'') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-money\"></i></a>
                            <a class=\"tip\" title='" . $this->lang->line("view_payment_methods") . "' href='" . site_url('customers/view_payment_methods/'.$customer->id.'') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-list-alt\"></i></a>
                            <a class=\"tip\" title='" . $this->lang->line("edit_customer1") . "' href='" . site_url('customers/edit1/'.$customer->id.'') . "' data-toggle='modal' data-target='#myModal'><i class=\"fa fa-edit\"></i></a>
                           ";
                           $data.="<a onClick=\"javascript: return confirm('Are you sure?');\" href='" . site_url('customers/activate_customer/'.$customer->id.'') . "'><i class=\"fa fa-check\"></i></a>";
                            $data.="<a onClick=\"javascript: return confirm('Are you sure?');\" href='" . site_url('customers/delete1/'.$customer->id.'') . "'><i class=\"fa fa-trash-o\"></i></a></center>
                            </td>
                            </tr>";
                            
                  
                         echo $data;
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
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>

                          
                            <th style="width:40px;" class="text-center"><?= lang("actions"); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>$(document).ready(function(e){
    $("#CusData").DataTable();

   function confirm_del()
   {
    return confirm('are you sure?');
   }
});

</script>

<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>
<?php if ($action && $action == 'add') {
    echo '<script>$(document).ready(function(){$("#add").trigger("click");});</script>';
}
?>
	

