<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('edit_customer1'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("customers/edit1/" . $customer->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group name">
                        <?= lang("name", "name"); ?>
                        <?php echo form_input('name', $customer->name, 'class="form-control tip" id="name" required="required"'); ?>
                    </div>
                   
                    <div class="form-group">
                        <?= lang("email_address", "email_address"); ?>
                        <input type="email" name="email" class="form-control"  id="email_address"
                               value="<?= $customer->email ?>"/>
                    </div>
                    <div class="form-group">
                        <?= lang("phone", "phone"); ?>
                        <input type="tel" name="phone" class="form-control"  id="phone"
                               value="<?= $customer->phone ?>"/>
                    </div>
                    
                    <div class="form-group">
                        <?= lang("Phone (Whatsup)", "phone2"); ?>
                        <input type="tel" name="phone2" class="form-control" id="phone2" value="<?= $customer->phone2 ?>"/>
                    </div>

                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <?= lang("city", "city"); ?>
                        <select class="form-control" name="city" id="city" required >
                            <?php
        
                            foreach ($towns as $town) {
                                if($customer->city == $town->id){
                                    echo '<option selected value="'.$town->id.'" >'.$town->city.' ('.$town->french_name.')'.'</option>';
                                }else{
                                    echo '<option value="'.$town->id.'" >'.$town->city.' ('.$town->french_name.')'.'</option>';
                                }
                            }
        
                            ?>
        
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Status</label>
                        <select class="form-control" name="active">
                            <option value="1" <?= $customer->active==1 ? "selected" : "" ?>>Active</option>
                            <option value="0" <?= $customer->active==0 ? "selected" : "" ?>>Inactive</option>
                        </select>
                    </div>
                    
                </div>
            </div>
            

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_customer', lang('edit_customer1'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
    $(document).ready(function (e) {
$("#parent_subsidiary").on("change",function(e){
            
           if($("#parent_subsidiary :selected").text().toLowerCase()==="subsidiary"){
               $("#showparent").attr("display","block");
           } 
           else{
              $("#showparent").attr("display","none");  
           }
        });
         });
         </script>
        
<?= $modal_js ?>
