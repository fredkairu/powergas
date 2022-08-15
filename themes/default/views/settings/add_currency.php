<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_country_and_currency'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open("system_settings/add_currency", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
            <div class="form-group">
                <label class="control-label" for="code"><?php echo $this->lang->line("country"); ?></label>

                <div
                    class="controls"> <?php echo form_input('country', '', 'class="form-control" id="country" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="french_name"><?php echo $this->lang->line("french_name"); ?></label>

                <div
                    class="controls"> <?php echo form_input('french_name', $currency->french_name, 'class="form-control" id="french_name"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="portuguese_name"><?php echo $this->lang->line("portuguese_name"); ?></label>

                <div
                    class="controls"> <?php echo form_input('portuguese_name', $currency->portuguese_name, 'class="form-control" id="portuguese_name"'); ?> </div>
            </div>
             <div class="form-group">
                <label class="control-label" for="cluster"><?php echo $this->lang->line("cluster"); ?></label>

                <div class="controls"> 
                      <?php foreach ($clusters as $clust) {
                            $cat[$clust->id] = $clust->name;
                        }
                       echo form_dropdown('cluster', $cat, (isset($_POST['cluster']) ? $_POST['cluster'] : $currency->cluster), 'class="form-control select" id="name" placeholder="' . lang("select") . " " . lang("cluster") . '" required="required" style="width:100%"');?>
                   
                </div></div>

            <div class="form-group">
                <label class="control-label" for="code"><?php echo $this->lang->line("currency_code"); ?></label>

                <div
                    class="controls"> <?php echo form_input('code', '', 'class="form-control" id="code" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="name"><?php echo $this->lang->line("currency_name"); ?></label>

                <div
                    class="controls"> <?php echo form_input('name', '', 'class="form-control" id="name" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <label class="control-label" for="rate"><?php echo $this->lang->line("exchange_rate"); ?></label>

                <div
                    class="controls"> <?php echo form_input('rate', '', 'class="form-control" id="rate" required="required"'); ?> </div>
            </div>
            <div class="form-group">
                <input type="checkbox" value="1" name="auto_update" id="auto_update">
                <label class="padding-left-10"
                       for="auto_update"><?php echo $this->lang->line("auto_update_rate"); ?></label>
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_currency', lang('add_currency'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<?= $modal_js ?>
