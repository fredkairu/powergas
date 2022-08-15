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
                        <th style="width:30%;"><?= $this->lang->line("Route_Details"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Duration"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($vehiclesroutes)) {
                        foreach ($vehiclesroutes as $vehiclesroute) { ?>
                            <tr class="row<?= $vehiclesrouteid->id;  ?>">

                                <td><?= $vehiclesroute->description; ?></td>
                                <td><?= $vehiclesroute->duration; ?></td>
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
