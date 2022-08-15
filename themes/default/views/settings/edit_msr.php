<script type="text/javascript">
    $(document).ready(function () {
        $("#team_name").select2("destroy").empty().attr("placeholder", "<?= lang('Select_Country_to_Load') ?>").select2({
            placeholder: "<?= lang('Select_Country_to_Load') ?>", data: [
                {id: '', text: '<?= lang('Select_Country_to_Load') ?>'}
            ]
        });
        $('#country').change(function () {
            var v = $(this).val();
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= site_url('system_settings/getCountryTeams') ?>/" + v,
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
                            $("#team_name").select2("destroy").empty().attr("placeholder", "<?= lang('Select_Team') ?>").select2({
                                placeholder: "<?= lang('Select_Country_to_Load') ?>",
                                data: scdata
                            });
                        }
                    },
                    error: function () {
                       // bootbox.alert('<?= lang('ajax_error') ?>');
                       $("#team_name").select2("destroy").empty();
                        $('#modal-loading').hide();
                    }
                });
            } else {
                $("#team_name").select2("destroy").empty().attr("placeholder", "<?= lang('Select_Country_to_Load') ?>").select2({
                    placeholder: "<?= lang('Select_Country_to_Load') ?>",
                    data: [{id: '', text: '<?= lang('Select_Country_to_Load') ?>'}]
                });
            }
            $('#modal-loading').hide();
        });
    
    });
</script>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Edit_MSR'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("system_settings/edit_msr/id/".$id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
<!--             <div class="form-group">
                <?//php echo lang('category_name', 'name'); ?>
                <div class="controls">
                    <?//php echo form_input($name); ?>
                </div>
            </div>-->
           
            <div class="form-group">
                <?php echo lang('MSR*', 'name'); ?>
                <div class="controls">
                   <?php echo form_input('name', $msr->msr_alignment_name, 'class="form-control" id="msr_name"'); ?>
                </div>
                
                   <?php echo form_hidden('id', $msr->id, 'class="form-control" id="msr_id"'); ?>
               
            </div>
            <div class="form-group">
                        <?= lang("country", "country"); ?>
                        <?php foreach($countries as $country){
                            $ctry[$country->id]=$country->country;
                        }?>
                        <?php echo form_dropdown('country', $ctry,$msr->country_id, 'class="form-control tip select" id="country" style="width:100%;" required="required"'); ?>
                    </div>
            <div class="form-group">
                        <?= lang("Business_Unit", "business_unit"); ?>
                         <?php foreach($bu as $bu1){
                            $arrbu[$bu1->business_unit]=$bu1->business_unit;
                        }?>
                        <?php echo form_dropdown('business_unit', $arrbu,$msr->business_unit, 'class="form-control tip select" id="business_unit" style="width:100%;" required="required"'); ?>
    
                    </div>
      
                     <div class="form-group all">
                        <?= lang("Team_Name", "team_name") ?>
                        <div class="controls" id="team_namei"> <?php
                            echo form_input('team_name', ($msr ? $msr->team_id : ''), 'class="form-control" id="team_name"  placeholder="' . lang("team_name") . '"');
                            ?>
                        </div>
                    </div>
       
        </div>
        <div class="modal-footer">
            <?php echo form_submit('update_msr', lang('Update_MSR'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>
<script type="text/javascript">
 <?php if ($msr) { ?>
    $.ajax({
            type: "get", async: false,
            url: "<?= site_url('system_settings/getCountryTeams') ?>/" + <?= $msr->country_id ?>,
            dataType: "json",
            success: function (scdata) {
                if (scdata != null) {
                    $("#team_name").select2("destroy").empty().attr("placeholder", "<?= lang('Select_Team') ?>").select2({
                        placeholder: "<?= lang('Select_Country_to_Load') ?>",
                        data: scdata
                    });
                }
            }
        });
 <?php } ?>
</script>