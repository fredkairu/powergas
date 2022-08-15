<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_town') . " (" . $town->city . ")"; ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'edit-town-form');
        echo form_open_multipart("towns/edit/" . $town->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="row">

                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("county", "county_id") ?>
                        <select class="form-control" name="county_id" id="county_id" required >
                            <?php

                            foreach ($counties as $county) {
                                if($town->county_id == $county->id){
                                    echo '<option selected value="'.$county->id.'" >'.$county->french_name.'</option>';
                                }else{
                                    echo '<option value="'.$county->id.'" >'.$county->french_name.'</option>';
                                }
                            }

                            ?>

                        </select>
                    </div>

                    <div class="form-group">
                        <?= lang("town", "city"); ?>
                        <input type="text" name="city" class="form-control" id="city" value="<?php echo $town->city ?>" required="required"/>
                    </div>

                </div>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_town', lang('edit_town'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function (e) {
        $('#edit-town-form').bootstrapValidator({
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