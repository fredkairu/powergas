
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-plus"></i><?= lang('Add_LPG_Purchase'); ?></h2>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?php echo lang('enter_info'); ?></p>
                <?php
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo form_open_multipart("purchases/add_gas", $attrib)
                ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang("Supplier", "supplier"); ?>
                                <?php
                                $wh[''] = '';
                                foreach ($suppliers as $supplier) {
                                    $sup[$supplier->id] = $supplier->name;
                                }
                                echo form_dropdown('supplier', $sup, (isset($_POST['supplier']) ? $_POST['supplier'] : ''), 'id="supplier" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("supplier") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang("status", "postatus"); ?>
                                <?php
                                $post = array('received' => lang('received'), 'pending' => lang('pending'), 'ordered' => lang('ordered'));
                                echo form_dropdown('status', $post, (isset($_POST['status']) ? $_POST['status'] : ''), 'id="postatus" class="form-control input-tip select" data-placeholder="' . $this->lang->line("select") . ' ' . $this->lang->line("status") . '" required="required" style="width:100%;" ');
                                ?>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang("Volume", "Volumetric Weight") ?>
                                <input class="form-control" id="volume" type="number" name="volume">
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang("Cost", "Cost") ?>
                                <input class="form-control" id="cost" type="number" name="cost">
                            </div>
                        </div>
                        

                        <div class="col-md-12">
                            <div class="form-group">
                                <?= lang("Note", "Note") ?>
                                <textarea class="form-control" name="note">
                                    
                                </textarea>
                            </div>
                        </div>
                        
                        <div class="col-md-12">
                            <div class="from-group"><?php echo form_submit('add_volumetric_purchase', $this->lang->line("submit"), 'id="add_volumetric_purchase" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                <button type="button" class="btn btn-danger" id="reset"><?= lang('reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>

                <?php echo form_close(); ?>

            </div>

        </div>
    </div>
</div>

