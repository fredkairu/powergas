<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            <h4 class="modal-title" id="myModalLabel"><?php echo lang('Sales_Team_alignments'); ?></h4>
        </div>
        <?php $attrib = array('data-toggle' => 'validator', 'role' => 'form');
        echo form_open_multipart("customers/edit1/" . $team->id, $attrib); ?>
        <div class="modal-body">
            <p><?= lang('enter_info'); ?></p>

    

            <div class="row">
                <div class="col-md-6">
                         <div class="form-group">
                        <?= lang("country", "country"); ?>
                        <?php foreach($countries as $country){
                            $ctry[$country->country]=$country->country;
                        }?>
                        <?php echo form_dropdown('country', $ctry,$team->country, 'class="form-control tip select" id="country" style="width:100%;" required="required"'); ?>
                    </div>
                    <div class="form-group person">
                         <div class="form-group"  id="showparent">
                        <?= lang("DSM", "dsm"); ?>
                        <?php foreach($dsms as $dsm){
                            $arrdsm[$dsm->id]=$dsm->dsm_alignment_name;
                        }?>
                    <?php echo form_dropdown('dsm', $arrdsm,$team->dm_alignment_id, 'class="form-control tip select" id="dsm" style="width:100%;" required="required"'); ?>
        
                        
                    </div>
                        <?= lang("Msr", "msr"); ?>
                        <?php foreach($msrs as $msr){
                            $arrmsr[$msr->id]=$msr->msr_alignment_name;
                        }?>
                        <?php echo form_dropdown('msr', $arrmsr,$team->sf_alignment_id, 'class="form-control tip select" id="msr" style="width:100%;" required="required"'); ?>
                    </div>
                    <div class="form-group company">
                        <?= lang("Team_Name", "team_name"); ?>
                        <?php foreach($teams as $team1){
                            $arrteam[$team1->id]=$team1->name;
                        }?>
                         <?php echo form_dropdown('team_name', $arrteam,$team->team_id, 'class="form-control tip select" id="team_name" style="width:100%;" required="required"'); ?>
                    </div>
                   <div class="form-group " style="display:none">
                        <?= lang("Name", "name"); ?>
                        <?php echo form_input('name', $team->team_name, 'class="form-control tip" placeholder="Alias1,Alias2" id="name" '); ?>
                    </div>
                   
                    
                    

                </div>
                <div class="col-md-6">
                   
                   <div class="form-group">
                        <?= lang("Business_Unit", "business_unit"); ?>
                         <?php foreach($bu as $bu1){
                            $arrbu[$bu1->business_unit]=$bu1->business_unit;
                        }?>
                        <?php echo form_dropdown('business_unit', $arrbu,$team->business_unit, 'class="form-control tip select" id="business_unit" style="width:100%;" required="required"'); ?>
    
                    </div>
                   <div class="form-group">
                        <?= lang("Dsm_Name", "Dsm_Name"); ?>
                         <?php foreach($dsmemployee as $dsmemployee1){
                            $arrdsmemp[$dsmemployee1->name]=$dsmemployee1->name;
                        }?>
                        <?php echo form_dropdown('dsm_name', $arrdsmemp,$team->dm_name, 'class="form-control tip select" id="dsm_name" style="width:100%;" required="required"'); ?>
                    </div>
                    <div class="form-group">
                        <?= lang("Msr_Name", "msr_name"); ?>
                        <?php echo form_input('msr_name', $team->sf_name, 'class="form-control" id="msr_name" '); ?>
                    </div>
                    
                </div>
            </div>
            <!--<div class="form-group">
                <?= lang('award_points', 'award_points'); ?>
                <?= form_input('award_points', set_value('award_points', $customer->award_points), 'class="form-control tip" id="award_points"  required="required"'); ?>
            </div>-->

        </div>
        <div class="modal-footer">
            <?php echo form_submit('edit_customer', lang('Edit_Sales_Team'), 'class="btn btn-primary"'); ?>
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
