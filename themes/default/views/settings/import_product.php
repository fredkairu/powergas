 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo $page_title; ?></h4>
            
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data');
        echo form_open("system_settings/prices_csv", $attrib); ?>
        <div class="modal-body">
            <p class="red"><?=$notice?></p>
            <p>Use sample template for accuracy<a href="#" id="sampletemplate" class="btn btn-primary pull-right"><i class="fa fa-download"></i>Download Sample Template</a><br></p>
             <div class="form-group">
                <?= lang("Country_product_pricing", "Country_product_pricing") ?>
                    <input type="file" name="userfile" class="form-control file" data-show-upload="false"
                                       data-show-preview="false" id="csv_file" required="required"/>
                    <input type="hidden" name="country" id="country" value="<?=$country_name?>">
                    <input type="hidden" name="currency_id" id="currency_id" value="<?=$currency_id?>">
              
            </div>
            <div class="form-group"><label>Price From</label><input type="text" placeholder="Price Effective From" name="fromdate" value="<?=date("m/Y")?>" class="form-control input-tip datepicker monthPicker" required="required" id="fromdate" required="required" data-original-title="" title="" data-bv-field="date"><label>Price To</label><input type="text" name="todate" value="<?=date("m/Y")?>" class="form-control input-tip datepicker monthPicker" id="todate" required="required" placeholder="mm/YY" data-original-title="" required="required" title="" data-bv-field="date"></div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('add_country', lang('Upload_Country_Pricing'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
$(document).ready(function(e){
    document.getElementById("ajaxCall").style.display = "none"; 
    
   $("#sampletemplate").click(function(e){
       e.preventDefault();
       // alert($("#country").val());
     $.ajax({
  url: "<?= site_url('system_settings/country_pricing_csv') ?>",
  data:{'country':$("#country").val(),'currency_id':$("#currency_id").val(),"name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"}
}).done(function(url) {
 window.open(url);
});
   });
     $(".datepicker").datepicker({
        dateFormat: 'yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,

        onClose: function(dateText, inst) {

            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('yy', new Date(year, 0, 1)));
        }
    });
     $(".monthPicker").focus(function () {
        $(".ui-datepicker-calendar").hide();
        $(".ui-datepicker-month").hide();
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        });
    });
   
});



</script>




<?= $modal_js ?>
