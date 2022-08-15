<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h5 class="modal-title" id="myModalLabel"><?php echo lang($page_title); ?> FOR <span style="color:red"><?=$dsm_name?></span></h5>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form','enctype'=>'multipart/form-data','method'=>'POST');
        echo form_open("system_settings/import_dsm_mapping_csv", $attrib); ?>
        <input type="hidden" value="<?=$dsm_id?>" name="dsm_id" id="dsm_id">
        <div class="modal-body">
                    <div class="form-group">
                <?= lang("Distributor_Customer", "Distributor_names") ?> <a href="<?php echo $this->config->base_url(); ?>assets/csv/dsm_team_mapping.csv"
                                   class="btn btn-primary pull-right"><i class="fa fa-download"></i>Sample Template</a>
                <input id="site_logo" type="file" name="userfile" data-show-upload="true" data-show-preview="true" class="form-control file">
            </div>
            <div class="row">
                <table id="PRData" class="table table-bordered table-condensed table-hover table-striped" width="90%">
                        <thead>
                            <tr class="primary"><th>#</th><th>Team Name</th><th>Actions</th></tr></thead>
                <?php 
                $count=1;
               foreach($dsmteam_mapping as $dp){ ?>
                   
                        <tr><td ><?=$count?></td>
                        <td width="70%">
                           
                            <?php foreach($teamsaall as $dball){
                            $ctry[$dball->id]=$dball->name;
                        }?>
                        <?php echo form_dropdown('newdistributor', $ctry,$dp->team_id, 'class="form-control tip select" id="pname'.$dp->team_id.'" style="width:100%;" required="required" disabled="true"'); ?>
                            </td>
                        <td><a class="deletedp" href="#" id="<?=$dp->id?>"><i class="fa fa-trash-o"></i></a></td></tr>     
                   
               <?php
               $count++;
               
               }
                ?>
                        <tr><td>#</td><td><select style="min-width:80px" class="form-control" name="newteam" id="newteam">
                            
                            <?php 
                            
                            foreach ($teams as $db) {

                    echo "<option value='".$db->id."'>".$db->name."</option>";
                }?>
                                    </select></td><td><a class="addition btn btn-primary">Add Mapping</a></td></tr>
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
var confirmation=confirm("Do you want to edit Dsm/Team ?");
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
var confirmation=confirm("Do you want to delete DSM mapping?");
if(confirmation){
        $.ajax({
            
  url: "<?= site_url('system_settings/delete_dsm_mapping') ?>",
  data:{"value": "<?= $this->security->get_csrf_hash() ?>","dp_id":id}
}).done(function(response) {
    alert(response);
 window.location.reload();
});
//alert(id+newname+newcountry);
}

})

$(".addition").on("click",function(e){
//if($("#cnamenew").val()!=""){
    e.preventDefault();
var newteam=$("#newteam").val();
var confirmation=confirm("Do you want to add Dsm Team mapping?");
if(confirmation){
        $.ajax({
            
  url: "<?= site_url('system_settings/add_dsm_team_mapping') ?>",
  data:{"value": "<?= $this->security->get_csrf_hash() ?>","newteam":newteam,"dsm_id":$("#dsm_id").val()}
}).done(function(response) {
    alert(response);
 //window.location.reload();
});
    
}
//}else{
//    alert("Enter distributor Customer");
//}

})


</script>
<?= $modal_js ?>
