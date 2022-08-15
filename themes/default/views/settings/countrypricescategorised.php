<script>
    var oTable;
    $(document).ready(function () {
      $('#GPData').dataTable({
//            "aaSorting": [[0, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100,500,1000,2000, -1], [10, 25, 50, 100,500,1000,2000, "<?=lang('all')?>"]],
//            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            "oTableTools": {
                "sSwfPath": "assets/media/swf/copy_csv_xls_pdf.swf",
                "aButtons": ["csv", {"sExtends": "pdf", "sPdfOrientation": "landscape", "sPdfMessage": ""}, "print"]
            },
//            "aoColumns":[{"bSortable": false},null, null, null,null,null,null,{"bSortable": false}]

        });
        
        
       
        
     
      
   });
</script>


<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-barcode"></i>
           <?=$this->title?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><?=anchor('system_settings/add_country_pricing?id='.$country_id, '<i class="fa fa-plus-circle"></i>Add Country Pricing', 'data-toggle="modal" data-target="#myModal"')?></li>
                        <li><a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
                        <li><a href="#" id="pdf" data-action="export_pdf"><i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?></a></li>
                        <li class="divider"></li>
                       <li><a href="#" class="bpo" title="<b><?= $this->lang->line("delete_pricing") ?></b>" data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left"><i class="fa fa-trash-o"></i> <?= lang('delete_pricing') ?></a></li>
                    </ul>
                </li>
                
            </ul>
        </div>
    </div>
    <div class="box-content">
         <div class="row">
                <hr>
                Search prices for period:<br>
                <?php 
                $attrib = array('data-toggle' => 'validator', 'role' => 'form');
                echo form_open("system_settings/country_pricing", $attrib);
        ?>
                <div class="col-md-3"> <div class="form-group">
                        <label for="podate">From *</label>                                    <input type="text" name="fromdate" placeholder="mm/YYYY" value="01/<?=date("Y")?>" class="form-control input-tip datepicker monthPicker hasDatepicker" required="required" id="podate" data-original-title="" title=""></div>
                                </div>
                <div class="col-md-3"><div class="form-group">
                    <label for="podate">To *</label>                                    <input type="text" name="todate" placeholder="mm/YYYY" value="12/<?=date("Y")?>" class="form-control input-tip datepicker monthPicker hasDatepicker" required="required" id="podate" data-original-title="" title=""> </div>
                                </div>
                      <div class="col-md-3">
                                            <div class="form-group">
                                                <?= lang("Country", "country"); ?>
                                                <?php
                                                        foreach ($currencies as $value) {
                                                            $countries[$value->id]=$value->country;
                                                        }
                                               // $cluster=array("SSA32"=>"SSA32","EAH"=>"EAH","CCS"=>"CCS","EPDIS"=>"EPDIS");
                                                
                                                echo form_dropdown('country', $countries,($_POST['country']), 'id="country" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("cluster") . '" required="required" style="width:100%;" ');
                                                ?>
                                            </div>
                      </div>  <div class="col-md-3">
                           <div
                                class="form-group"><br><?php echo form_submit('search_prices', $this->lang->line("submit"), 'id="add_sale" class="btn btn-primary" style="padding: 6px 15px; margin:15px 0;"'); ?>
                                </div>
                          
                      </div>
            
            
            
           <?php echo form_close(); ?>
            </div>
        <?php if ($Owner) {
    echo form_open('system_settings/pricing_actions', 'id="action-form"');
} ?>
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive" style="display:none">
                    <table id="GPData" class="table table-bordered table-condensed table-hover table-striped datatable">
                        <thead>
                        <tr class="primary">
                            
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                           
                           
                                                        <th><?= lang("Period") ?></th>
                                                       
                            
                            <th style="min-width:65px; text-align:center;"><?= lang("actions") ?></th>
                        </tr>
                        </thead>
                        <tbody>
                 <?php 
    $count=1;
    $currentyear=date('Y');
     $lastyear=date('Y')-1;
      $last2year=date('Y')-2;
    $monthss=array("01/".$currentyear.",01/".$lastyear.",01/".$last2year=>"January","02/".$currentyear.",02/".$lastyear.",02/".$last2year=>"February","03/".$currentyear.",03/".$lastyear.",03/".$last2year=>"March","04/".$currentyear.",04/".$lastyear.",04/".$last2year=>"April","05/".$currentyear.",05/".$lastyear.",05/".$last2year=>"May","06/".$currentyear.",06/".$lastyear.",06/".$last2year=>"June","07/".$currentyear.",07/".$lastyear.",07/".$last2year=>"July","08/".$currentyear.",08/".$lastyear.",08/".$last2year=>"August","09/".$currentyear.",09/".$lastyear.",09/".$last2year=>"September","10/".$currentyear.",10/".$lastyear.",10/".$last2year=>"October","11/".$currentyear.",11/".$lastyear.",11/".$last2year=>"November","12/".$currentyear.",12/".$lastyear.",12/".$last2year=>"December");
   // die(print_r($monthss));\
 foreach ($monthss as $keyy=>$value) {
    
     ?>
                            <tr><td><input class="checkbox multi-select input-xs" type="checkbox" name="val[]" value="<?=$keyy?>"></td><td><a href="<?=site_url('system_settings/country_pricing?start='.$keyy)?>"><?=$value?></a></td><td><a href="<?=site_url('system_settings/country_pricing?start='.$keyy)?>"><i class="fa fa-search"></i></a></td></tr>
<?php 

$count++;
 }
                            
                            ?>
                       
                        </tbody>

                        <tfoot class="dtFilter">
                        <tr class="active">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            
                            <th></th>
                           
                           
                           
                            <th style="width:65px; text-align:center;"><?= lang("actions") ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>

<script type="text/javascript">
$(document).ready(function()
{   
   $(".datepicker").datepicker({
        dateFormat: 'MM yy',
        changeMonth: true,
        changeYear: true,
        showButtonPanel: true,

        onClose: function(dateText, inst) {
            var month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
            var year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
            $(this).val($.datepicker.formatDate('mm/yy', new Date(year, month, 1)));
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