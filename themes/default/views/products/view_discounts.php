<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$product->name?></span></h5>
        </div>

        <div class="modal-body">
            <div class="table-responsive">
                <table id="CompTable" cellpadding="0" cellspacing="0" border="0"
                       class="table table-bordered table-hover table-striped">
                    <thead>
                    <tr>
                        <th style="width:30%;"><?= $this->lang->line("Product_Name"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Range_From"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Range_To"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Discount"); ?></th>
                        <th style="width:30%;"><?= $this->lang->line("Loyalty_Points"); ?></th>
                        <th style="width:10%;"><?= $this->lang->line("actions"); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($product_discounts)) {
                        foreach ($product_discounts as $product_discount) { ?>
                            <tr class="row<?= $product_discount->id ?>">

                                <td><?= $product_discount->name; ?></td>
                                <td><?= $product_discount->range_from; ?></td>
                                <td><?= $product_discount->range_to; ?></td>
                                <td><?= $product_discount->discount; ?></td>
                                <td><?= $product_discount->loyalty; ?></td>
                                <td>
                                    <div class="text-center">
                                            <a href="<?= site_url('products/edit_discount/' . $product_discount->id) ?>"
                                               data-toggle="modal" data-target="#myModal2"><i
                                                        class="fa fa-edit"></i></a>
                                            <a href="#" class="po"
                                               title="<b><?= $this->lang->line("delete_target") ?></b>"
                                               data-content="<p><?= lang('r_u_sure') ?></p><a class='btn btn-danger' id='<?= $product_discount->id ?>' href='<?= site_url('customers/delete_target/' . $product_discount->id) ?>'><?= lang('i_m_sure') ?></a> <button class='btn po-close'><?= lang('no') ?></button>"
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
