<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$vehicle->plate_no?></span></h5>
        </div>

        <div class="modal-body">
            <div class="table-responsive">
                <table id="CompTable" cellpadding="0" cellspacing="0" border="0"
                       class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th style="width:30%;"><?= $this->lang->line("Route_Name"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Day"); ?></th>
                        <th style="width:10%;"><?= $this->lang->line("actions"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($vehicleroutes)) {
                        foreach ($vehicleroutes as $vehicleroute) { ?>
                            <tr class="row<?= $vehicleroute->id ?>">

                                <td><?= $vehicleroute->route_name; ?></td>
                                <td><?= $vehicleroute->day; ?></td>
                                <td>
                                    <div class="text-center">
                                            <a href="<?= site_url('vehicles/edit_route/' . $vehicleroute->id) ?>"
                                               data-toggle="modal" data-target="#myModal2"><i
                                                        class="fa fa-edit"></i></a>
                                            <a href="<?= site_url('vehicles/edit_allocation/' . $vehicleroute->route_id.'/' .$vehicleroute->actual_day) ?>"
                                               ><i class="fa fa-building"></i></a>
                                            <a href="#" class="po"
                                               title="<b><?= $this->lang->line("delete_route") ?></b>"
                                               data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' id='<?= $vehicleroute->id ?>' href='<?= site_url('vehicles/delete_route/' . $vehicleroute->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn po-close'><?= lang('no') ?></button>"
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
