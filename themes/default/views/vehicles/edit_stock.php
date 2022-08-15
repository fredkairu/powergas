<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$vehicle->plate_no?></span></h5>
        </div>

        <div class="modal-body">
            <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'edit-vehicle-stock-form');
    echo form_open_multipart("vehicles/update_vehicle_stock"); ?>
                    <input name="id" type="hidden" value="<?= $vehicle->id; ?>">
                    <div class="table-responsive">
                        <table id="StockData" cellpadding="0" cellspacing="0" border="0"
                               class="table table-bordered table-condensed table-hover table-striped">
                            <thead>
                            <tr class="primary">

                                <th><?= lang("Product_Id"); ?></th>
                                <th><?= lang("product_name"); ?></th>
                                <th><?= lang("quantity"); ?></th>
                                <th style="width:85px;"><?= lang("actions"); ?></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($vehicle_stocks)>0){
                                foreach ($vehicle_stocks as $stock){
                                    ?>

                                <tr class="row126">
                                    <td><input name="product_ids[]" value="<?= $stock->product_id; ?>" readonly></td>
                                    <td><input name="names[]" value="<?= $stock->product_name; ?>" readonly></td>
                                    <td><input name="quantitys[]" value="<?= $stock->product_quantity; ?>"></td>
                                    <td><button class="remove" style="padding-left:10px;padding-right:10px;"><i class="fa fa-trash-o"></i></button></td>
                                </tr>

                                <?php
                                }
                            }
                            ?>

                            </tbody>
                            <tfoot class="dtFilter">
                            <tr class="active">
                                <th></th>
                                <th></th>
                                <th></th>
                                <th style="width:85px;" class="text-center"><?= lang("actions"); ?></th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="form-group">
                        <?php echo form_submit('edit_vehicle_stock', lang('Edit_Vehicle_Stock'), 'class="btn btn-primary"'); ?>
                    </div>
                    <?php echo form_close(); ?>
        </div>

    </div>

</div>

<?= $modal_js ?>

<script type="text/javascript">

    $(document).ready(function (e) {
        $('#add-vehicle-stock-form').bootstrapValidator({
            feedbackIcons: {
                valid: 'fa fa-check',
                invalid: 'fa fa-times',
                validating: 'fa fa-refresh'
            }, excluded: [':disabled']
        });
        $('select.select').select2({minimumResultsForSearch: 6});
        fields = $('.modal-content').find('.form-control');
        $.each(fields, function () {
            var id = $(this).attr('id');
            var iname = $(this).attr('name');
            var iid = '#' + id;
            if (!!$(this).attr('data-bv-notempty') || !!$(this).attr('required')) {
                $("label[for='" + id + "']").append(' *');
                $(document).on('change', iid, function () {
                    $('form[data-toggle="validator"]').bootstrapValidator('revalidateField', iname);
                });
            }
        });
        $(document).on("click",".remove", function(e){
            e.preventDefault();
            $(this).parent('td').parent('tr').remove();
            x--;
        });
    });

</script>