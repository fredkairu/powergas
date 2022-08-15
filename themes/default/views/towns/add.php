<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('add_town'); ?></h2>
    </div>
    <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form', 'id' => 'add-town-form');
    echo form_open_multipart("towns/add"); ?>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>

                <div class="col-md-5">
                    <div class="form-group">
                        <?= lang("county", "county_id") ?>
                        <?php
                        $countynames[''] = "";
                        foreach ($counties as $county) {
                            $countynames[$county->id] = $county->french_name;
                        }
                        echo form_dropdown('county_id', $countynames, (isset($_POST['route']) ? $_POST['route'] : ($county ? $county->id : '')), 'class="form-control select" id="county_id" placeholder="' . lang("select") . " " . lang("county") . '" required="required" style="width:100%"')
                        ?>
                    </div>

                    <div class="form-group">
                        <?= lang("town", "city"); ?>
                        <input type="text" name="city" class="form-control" id="city" required="required"/>
                    </div>

                    <div class="form-group">
                        <?php echo form_submit('add_town', lang('add_town'), 'class="btn btn-primary"'); ?>
                    </div>
                </div>

            </div>

        </div>

    </div>
    <?php echo form_close(); ?>
</div>

<script type="text/javascript">

    $(document).ready(function (e) {
        $('#add-town-form').bootstrapValidator({
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


