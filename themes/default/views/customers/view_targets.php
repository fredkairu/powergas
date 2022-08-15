<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$distributor->name?></span></h5>
        </div>

        <div class="modal-body">
            <div class="table-responsive">
                <table id="CompTable" cellpadding="0" cellspacing="0" border="0"
                       class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th style="width:30%;"><?= $this->lang->line("Product_Name"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Target"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Month"); ?></th>
                        <th style="width:10%;"><?= $this->lang->line("actions"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($distributortargets)) {
                        foreach ($distributortargets as $distributortarget) { ?>
                            <tr class="row<?= $distributortarget->id ?>">

                                <td><?= $distributortarget->name; ?></td>
                                <td><?= $distributortarget->target; ?></td>
                                <td><?php if($distributortarget->month==1){echo "Jan";} if($distributortarget->month==2){echo "Feb";} if($distributortarget->month==3){echo "Mar";} if($distributortarget->month==4){echo "Apr";} if($distributortarget->month==5){echo "May";} if($distributortarget->month==6){echo "Jun";} if($distributortarget->month==7){echo "Jul";} if($distributortarget->month==8){echo "Aug";} if($distributortarget->month==9){echo "Sep";} if($distributortarget->month==10){echo "Oct";} if($distributortarget->month==11){echo "Nov";}
                                if($distributortarget->month==12){echo "Dec";}?></td>
                                <td>
                                    <div class="text-center">
                                            <a href="<?= site_url('customers/edit_target/' . $distributortarget->id) ?>"
                                               data-toggle="modal" data-target="#myModal2"><i
                                                        class="fa fa-edit"></i></a>
                                            <a href="#" class="po"
                                               title="<b><?= $this->lang->line("delete_target") ?></b>"
                                               data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' id='<?= $distributortarget->id ?>' href='<?= site_url('customers/delete_target/' . $distributortarget->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn po-close'><?= lang('no') ?></button>"
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
