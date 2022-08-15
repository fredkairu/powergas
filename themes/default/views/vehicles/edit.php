<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_vehicle') . " (" . $vehicle->plate_no . ")"; ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'edit-vehicle-form');
        echo form_open_multipart("vehicles/edit/" . $vehicle->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="row">

                <div class="col-md-6">

                    <div class="form-group">
                        <?= lang("plate_no", "plate_no"); ?>
                        <input type="text" name="plate_no" class="form-control" id="plate_no" value="<?php echo $vehicle->plate_no ?>" required="required"/>
                    </div>

                    <div class="form-group">
                        <label for="discount_enabled" >Discount</label>
                        <select name="discount_enabled" class="form-control">
                            <?php
                                if($vehicle->discount_enabled=='Enabled'){
                                    echo '<option value="Enabled" selected >Enabled</option>';
                                    echo '<option value="Disabled" >Disabled</option>';
                                }else{
                                    echo '<option value="Enabled" >Enabled</option>';
                                    echo '<option value="Disabled" selected >Disabled</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_vehicle', lang('edit_vehicle'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function (e) {
        $('#edit-vehicle-form').bootstrapValidator({
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
    });

</script>