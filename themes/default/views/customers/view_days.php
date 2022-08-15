<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close day_modal" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> TO SERVE SHOP <span style="color:#ff0000"><?= $allocation[0]->shop_name ?></span> ON ROUTE <span style="color:#ff0000"><?= $allocation[0]->route_name ?></span></h5>
        </div>

        <div class="modal-body">
            <div class="table-responsive">
                <table id="CompTable" cellpadding="0" cellspacing="0" border="0"
                       class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th style="width:30%;"><?= $this->lang->line("Day"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("expiry"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("status"); ?></th>
                        <th style="width:10%;"><?= $this->lang->line("actions"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($allocation_days)) {
                        foreach ($allocation_days as $allocation_day) { ?>
                            <tr class="row<?= $allocation_day->id ?>">

                                <td><?= $allocation_day->day; ?></td>
                                <td><?= $allocation_day->expiry; ?></td>
                                <td><?php
                                    if(date('Y-m-d H:i:s')>$allocation_day->expiry){
                                        ?><span class="label label-danger">Expired</span><?php
                                    }else{
                                        ?><span class="label abel-success">Active</span><?php
                                    }
                                    ?>
                                </td>
                                <td>
                                    <div class="text-center">
                                            <a href="<?= site_url('customers/edit_day/' . $allocation_day->id) ?>"
                                               data-toggle="modal" data-target="#myModal6"><i
                                                        class="fa fa-edit"></i></a>
                                            <a href="#" class="po"
                                               title="<b><?= $this->lang->line("delete_day") ?></b>"
                                               data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' id='<?= $allocation_day->id ?>' href='<?= site_url('customers/delete_day/' . $allocation_day->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn po-close'><?= lang('no') ?></button>"
                                               rel="popover"><i class="fa fa-trash-o"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php }
                    } else {
                        echo "<tr><td colspan='6'>" . lang('no_data_available') . "</td></tr>";
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<?= $modal_js ?>
