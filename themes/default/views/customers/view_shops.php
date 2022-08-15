<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$customer_name?></span></h5>
        </div>

        <div class="modal-body">
            <div class="table-responsive">
                <table id="CompTable" cellpadding="0" cellspacing="0" border="0"
                       class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th style="width:30%;"><?= $this->lang->line("shop_name"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Route"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("lat"); ?></th>
                        <th style="width:15%;"><?= $this->lang->line("lng"); ?></th>
                        <th style="width:10%;"><?= $this->lang->line("actions"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($distributor_customer_shops)) {
                        foreach ($distributor_customer_shops as $distributor_customer_shop) { ?>
                            <tr class="row<?= $distributor_customer_shop->id ?>">

                                <td><?= $distributor_customer_shop->shop_name; ?></td>
                                <td><?= $distributor_customer_shop->route_name; ?></td>
                                <td><?= $distributor_customer_shop->lat; ?></td>
                                <td><?= $distributor_customer_shop->lng; ?></td>
                                <td>
                                    <div class="text-center">
                                            <a class="tip" title="Add Allocation" href="<?= site_url('customers/add_allocation/' . $distributor_customer_shop->id) ?>"
                                               data-toggle="modal" data-target="#myModal2"><i
                                                        class="fa fa-plus-circle"></i></a>
                                            <a class="tip" title="View Allocations" href="<?= site_url('customers/view_allocations/' . $distributor_customer_shop->id) ?>"
                                               data-toggle="modal" data-target="#myModal2"><i
                                                        class="fa fa-eye"></i></a>
                                            <a class="tip" title="Edit Shop" href="<?= site_url('customers/edit_shop/' . $distributor_customer_shop->id) ?>"
                                               data-toggle="modal" data-target="#myModal2"><i
                                                        class="fa fa-edit"></i></a>
                                            <a href="#" class="po"
                                               title="<b><?= $this->lang->line("delete_shop") ?></b>"
                                               data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' id='<?= $distributor_customer_shop->id ?>' href='<?= site_url('customers/delete_shop/' . $distributor_customer_shop->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn po-close'><?= lang('no') ?></button>"
                                               rel="popover"><i class="fa fa-trash-o"></i></a>
                                    </div>
                                </td>
                            </tr>
                        <?php }
                    } else {
                        echo "<tr><td colspan='5'>" . lang('no_data_available') . "</td></tr>";
                    } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

<?= $modal_js ?>
