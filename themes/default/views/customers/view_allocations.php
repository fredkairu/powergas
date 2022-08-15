<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR SHOP <span style="color:red"><?=$shop->shop_name?></span></h5>
        </div>

        <div class="modal-body">
            <div class="table-responsive">
                <table id="CompTable" cellpadding="0" cellspacing="0" border="0"
                       class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th style="width:30%;"><?= $this->lang->line("shop_name"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Route"); ?></th>
                        <th style="width:10%;"><?= $this->lang->line("actions"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($allocations)) {
                        foreach ($allocations as $allocation) { ?>
                            <tr class="row<?= $allocation->id ?>">

                                <td><?= $allocation->shop_name; ?></td>
                                <td><?= $allocation->route_name; ?></td>
                                <td>
                                    <div class="text-center">
                                            <a class="tip" title="Add Allocation Day" href="<?= site_url('customers/add_day/' . $allocation->id) ?>"
                                               data-toggle="modal" data-target="#myModal3"><i
                                                        class="fa fa-plus-circle"></i></a>
                                            <a class="tip" title="View Allocation Day(s)" href="<?= site_url('customers/view_days/' . $allocation->id) ?>"
                                               data-toggle="modal" data-target="#myModal4"><i
                                                        class="fa fa-eye"></i></a>
                                            <a class="tip" title="Edit Allocation" href="<?= site_url('customers/edit_allocation/' . $allocation->id) ?>"
                                               data-toggle="modal" data-target="#myModal5"><i
                                                        class="fa fa-edit"></i></a>
                                            <a href="#" class="po"
                                               title="<b><?= $this->lang->line("delete_allocation") ?></b>"
                                               data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' id='<?= $allocation->id ?>' href='<?= site_url('customers/delete_allocation/' . $allocation->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn po-close'><?= lang('no') ?></button>"
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
