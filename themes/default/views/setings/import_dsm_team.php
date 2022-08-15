<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$customer_name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("customers/import_customer_mapping", $attrib); ?>
        <input type="hidden" value="<?=$cutomer_id?>" name="cutomer_id" id="cutomer_id">
        <div class="modal-body">
                    <div class="form-group">
                <?= lang("Distributor_Customer", "Distributor_names") ?> <a href="<?php echo $this->config->base_url(); ?>assets/csv/Cust_dist_mapping_template.csv"
                                   class="btn btn-primary pull-right"><i class="fa fa-download"></i>Sample Template</a>
                <input id="site_logo" type="file" name="userfile" data-show-upload="true" data-show-preview="true" class="form-control file">
            </div>
            <div class="row">
                <table id="PRData" class="table table-bordered table-condensed table-hover table-striped" width="90%">
                        <thead>
                            <tr class="primary"><th>#</th><th>Given Country</th><th>Distributor Name</th><th>Distributor Customer/Name</th><th>Actions</th></tr></thead>
                <?php 
                $count=1;
               foreach($distributor_customer as $dp){ ?>
                   
                        <tr><td ><?=$count?></td><td><?=$dp->country?></td><td><?=$dp->name?></td><td width="70%"><input value="<?=$dp->distributor_naming?>" class="form-control" id="pname<?=$dp->id?>"></td><td><a class="editdp" href="#" id="<?=$dp->id?>"><i class="fa fa-pencil"></i></a>&nbsp;<a class="deletedp" href="#" id="<?=$dp->id?>"><i class="fa fa-trash-o"></i></a></td></tr>     
                   
               <?php
               $count++;
               
               }
                ?>
                        <tr><td>#</td><td>
                                <select class="form-control" style="min-width:80px" id="newcountry">
                            <?php foreach ($countries as $ct) {
                            
                    echo "<option value='".$ct->id."'>".$ct->country."</option>";
                }?>
                                </select></td><td><select style="min-width:80px" class="form-control" name="newdistributor" id="newdistributor">
                            <?php foreach ($distributors as $db) {
                            $country=$this->settings_model->getCurrencyByID($db->country);
                    echo "<option value='".$db->id."'>".$db->name."</option>";
                }?>
                                    </select></td><td width="70%"><input type="text" class="form-control" id="cnamenew"></td><td><a class="addition btn btn-primary">Add Mapping</a></td></tr>
                </table> 
                
            </div>
        </div>
        <div class="modal-footer">
            <?php echo form_submit('import_products', lang('Upload New Mapping'), 'class="btn btn-primary"'); ?>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
<script type="text/javascript">
$(document).ready(function(e){
   $("#distributorpricingg").click(function(e){
       e.preventDefault();
       // alert($("#country").val());
     $.ajax({
  url: "<?= site_url('products/distributor_template') ?>",
  data:{"value": "<?= $this->security->get_csrf_hash() ?>","cutomer_id":$("#cutomer_id").val()}
}).done(function(url) {
 window.open(url);
});
   });
});

$(".editdp").on("click",function(e){
e.preventDefault();
var id=$(this).attr("id");
var newname=$("#pname"+id).val();
var newcountry=$("#pcountry"+id+" :selected").val();
var confirmation=confirm("Do you want to edit product name?");
if(confirmation){
        $.ajax({
            
  url: "<?= site_url('products/edit_mapping') ?>",
  data:{"value": "<?= $this->security->get_csrf_hash() ?>","dp_id":id,"newname":newname,"newcountry":newcountry}
}).done(function(response) {
    alert(response);
 window.location.reload();
});
//alert(id+newname+newcountry);
}

});


$(".deletedp").on("click",function(e){
e.preventDefault();
var id=$(this).attr("id");
var newname=$("#pname"+id).val();
var newcountry=$("#pcountry"+id+" :selected").val();
var confirmation=confirm("Do you want to delete product mapping?");
if(confirmation){
        $.ajax({
            
  url: "<?= site_url('products/delete_mapping') ?>",
  data:{"value": "<?= $this->security->get_csrf_hash() ?>","dp_id":id,"newname":newname,"newcountry":newcountry}
}).done(function(response) {
    alert(response);
 window.location.reload();
});
//alert(id+newname+newcountry);
}

})

$(".addition").on("click",function(e){
if($("#cnamenew").val()!=""){
    e.preventDefault();
var newname=$("#cnamenew").val();
var newcountry=$("#newcountry").val();
var newdistributor=$("#newdistributor").val();
var confirmation=confirm("Do you want to add customer mapping?");
if(confirmation){
        $.ajax({
            
  url: "<?= site_url('customers/add_mapping') ?>",
  data:{"value": "<?= $this->security->get_csrf_hash() ?>","newdistributor":newdistributor,"newname":newname,"newcountry":newcountry,"cutomer_id":$("#cutomer_id").val()}
}).done(function(response) {
    alert(response);
 //window.location.reload();
});
    
}
}else{
    alert("Enter distributor Customer");
}

})


</script>
<?= $modal_js ?>
