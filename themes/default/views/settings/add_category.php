<script type="text/javascript">
    $(document).ready(function () {     
        
     $('#name').change(function () {
            var v = $(this).val();
            //alert(v);
            $('#modal-loading').show();
            if (v) {
                $.ajax({
                    type: "get",
                    async: false,
                    url: "<?= site_url('System_settings/liststimacategorybyid') ?>/" + v,
                    //url: "<//?= site_url('products/suggestionsfromstima') ?>",
                    dataType: "json",
                    success: function (scdata) {
                        if (scdata != null) {
//                            $("#name").select2("destroy").empty().attr("placeholder", "<//?= lang('product_name') ?>").select2({
//                                placeholder: "<//?= lang('select_product_to_load') ?>",
//                                data: scdata
//                            });
                      //  alert(scdata.description);
                         $("#code").val(scdata.code);
                         //$("#category").val(scdata.category);
//                         $('#category').append( new Option(scdata.category,scdata.categoryid,true,false) );
//                         $("#price").val(scdata.price);
//                         $("#details").append(scdata.description);
//                         $("#alert_quantity").val(scdata.alertqty);
//                         $("#wh_qty_1").val(scdata.stockqty);
                         
                        }
                    },
                    error: function () {
                        bootbox.alert('<?= lang('ajax_error') ?>');
                        $('#modal-loading').hide();
                    }
                });           
            } else {
//                $("#name").select2("destroy").empty().attr("placeholder", "<?= lang('select_name_to_load') ?>").select2({
//                    placeholder: "<?= lang('select_name_to_load') ?>",
//                    data: [{id: '', text: '<?= lang('select_name_to_load') ?>'}]
//                });
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
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('add_category'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("system_settings/add_category", $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>
<!--             <div class="form-group">
                <?//php echo lang('category_name', 'name'); ?>
                <div class="controls">
                    <?//php echo form_input($name); ?>
                </div>
            </div>-->
              <div class="form-group">
                <?php echo lang('category_code', 'code'); ?>
                <div class="controls">
                 
                    <?= form_input('code', (isset($_POST['code']) ? $_POST['code'] : ($lastid->id)), 'class="form-control" id="code"  required="required" readonly') ?>

                </div>
            </div>
             <div class="form-group all">
                        <?= lang("category_name", "name") ?>
                        <?php
                        //$cat[''] = "";
                       // foreach ($categories as $categor) {
                        //    $cat[$categor->category_id] = $categor->name;
                       // }
                      // echo form_dropdown('name', $cat, (isset($_POST['category']) ? $_POST['category'] : ($category ? $category->name : '')), 'class="form-control select" id="name" placeholder="' . lang("select") . " " . lang("category") . '" required="required" style="width:100%"');
                      //  ?>
                       <input id="name" type="text" name="name" class="form-control input" required="required" style="width:100%"> 
                    </div>
          <div class="form-group all">
                        <?= lang("BU", "bu") ?>
                        <?php
                       $cat12[''] = "";
                       //print_r($bu);
                       foreach ($bu as $categor) {
                            $cat12[$categor->business_unit] = $categor->business_unit;
                        }
                       echo form_dropdown('bu', $cat12, (isset($_POST['bu']) ? $_POST['bu'] : '' ), 'class="form-control select" id="name" placeholder="' . lang("select") . " " . lang("BU") . '" required="required" style="width:100%"');
                        ?>
                     
                    </div>
            <div class="form-group" style="display:none">
                <?= lang("category_image", "image") ?>
                <input id="image" type="file" name="userfile" data-show-upload="false" data-show-preview="false"
                       class="form-control file">
            </div>

        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_category', lang('add_category'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript" src="<?= $assets ?>js/custom.js"></script>
<?= $modal_js ?>