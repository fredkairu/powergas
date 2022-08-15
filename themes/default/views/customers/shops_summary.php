
<?php if ($Owner) {
    echo form_open('customers/sanoficustomer_actions', 'id="action-form"');
    
} ?>
<div class="box">

    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('shops_summary'); ?></h2>

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
                    <table id="CusData" cellpadding="0" cellspacing="0" border ="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            
                            <th><?= lang("Customer"); ?></th>
                            <th><?= lang("Shop"); ?></th>
                            <th><?= lang("Days"); ?></th>
                            
                        </tr>
                        </thead>
                        <tbody>
                        <!-- <tr>
                            <td colspan="11" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr> -->
                        <?php
                        foreach($shops as $customer)
                        {
                            $this->load->model('customers');
                            $days=$this->customers_model->getShops($customer->all_id);

                            foreach($days as $day){
                               $nm=$day->name;
                            };
                            $data= "<tr><td></td>
                            <td>".$customer->name."</td>
                            <td>".$customer->shop."</td>
                            <td>". $nm. "</td>
                            <td>
                           </td>
                            </tr>";
                            // <a href='#' class='tip po' title='<b>" . $this->lang->line("delete_customer1") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-danger po-delete' href='" . site_url('customers/delete1/'.$customer->id.'') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-trash-o\"></i></a>
                        //     <a href='#' class='tip po' title='<b>" . $this->lang->line("activate_customer") . "</b>' data-content=\"<p>" . lang('r_u_sure') . "</p><a class='btn btn-success po-active' href='" . site_url('customers/activate_customer/'.$customer->id.'') . "'>" . lang('i_m_sure') . "</a> <button class='btn po-close'>" . lang('no') . "</button>\"  rel='popover'><i class=\"fa fa-check\"></i></a>
                           
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
	

