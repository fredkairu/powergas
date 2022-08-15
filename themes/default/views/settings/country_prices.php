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
        
        $(".duplicate").click(function(e){
            alert("Ensure you filll in the From and To Dates");
      $(".showdates").css("display","block");
      
    });
     
      
   });
</script>
<?php if ($Owner) {
    echo form_open('system_settings/pricing_actions', 'id="action-form"');
} ?>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i
                class="fa-fw fa fa-barcode"></i><?=$page_title?>
        </h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                <li class="dropdown">
                    <a data-toggle="dropdown" class="dropdown-toggle" href="#"><i class="icon fa fa-tasks tip" data-placement="left" title="<?= lang("actions") ?>"></i></a>
                    <ul class="dropdown-menu pull-right" class="tasks-menus" role="menu" aria-labelledby="dLabel">
                        <li><?=anchor('system_settings/add_country_pricing?id='.$country_id, '<i class="fa fa-plus-circle"></i>Add Country Pricing', 'data-toggle="modal" data-target="#myModal"')?></li>
                        <li><a href="#" id="excel" data-action="export_excel"><i class="fa fa-file-excel-o"></i> <?= lang('export_to_excel') ?></a></li>
                        <li><a href="#" id="pdf" data-action="export_pdf"><i class="fa fa-file-pdf-o"></i> <?= lang('export_to_pdf') ?></a></li>
                       
                        <li><a href="#" class="bpo duplicate"  title="<b><?= $this->lang->line("duplicate_pricing") ?></b>" data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-info' id='excel' data-action='duplicate'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left"><i class="fa fa-copy"></i> <?= lang('duplicate_pricing') ?></a></li>
                        <li class="divider"></li>
                       <li><a href="#" class="bpo" title="<b><?= $this->lang->line("delete_pricing") ?></b>" data-content="<p><?= lang('r_u_sure') ?></p><button type='button' class='btn btn-danger' id='delete' data-action='delete'><?= lang('i_m_sure') ?></a> <button class='btn bpo-close'><?= lang('no') ?></button>" data-html="true" data-placement="left"><i class="fa fa-trash-o"></i> <?= lang('delete_pricing') ?></a></li>
                    </ul>
                </li>
                
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>
                <p class="showdates" style="display:none">From Date:<input type='text' placeholder='mm/YYYY' name='newtodate'>&nbsp;To Date:<input type='text' placeholder='mm/YYYY' name='newfromdate'></p>

                <div class="table-responsive">
                    <table id="GPData" class="table table-bordered table-condensed table-hover table-striped datatable">
                        <thead>
                        <tr class="primary">
                            
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkft" type="checkbox" name="check"/>
                            </th>
                            <th>#</th>
                            <th><?= lang("country") ?></th>
                            <th><?= lang("Distributor") ?></th>
                             <th><?= lang("Customer") ?></th>
                            <th><?= lang("product_code") ?></th>
                            <th><?= lang("product_name") ?></th>
                           <th><?= lang("Supply_price") ?></th>
                            <th><?= lang("Resale_price") ?></th>
                            <th><?= lang("Tender_price") ?></th>
							
							<th><?= lang("Special_Resale_price") ?></th>
							<th><?= lang("Special_Tender_price") ?></th>
                                                        <th><?= lang("Promotion") ?></th>
                                                        <th><?= lang("Effective_From") ?></th>
                                                        <th><?= lang("Effective_To") ?></th>
                            
                            <th style="min-width:65px; text-align:center;"><?= lang("actions") ?></th>
                        </tr>
                        </thead>
                        <tbody>
                    
                            <?php 
                            $count=1;
 foreach ($products as $value) { ?>
                        <tr><td><input class="checkbox multi-select input-xs" type="checkbox" name="val[]" value="<?=$value->id?>"></td><td><?=$count?></td><td><?=$value->country?></td><td><?=$value->dname?></td><td><?=$value->cname?></td><td><?=$value->code?></td><td><?=$value->product_name?></td><td><?=$value->supply_price?></td><td><?=$value->resell_price?></td><td><?=$value->tender_price?></td><td><?=$value->special_resell_price?></td><td><?=$value->special_tender_price?></td><td><?=$value->promotion?></td><td><?=$value->from_date?></td><td><?=$value->to_date?></td><td><?=anchor('system_settings/edit_country_pricing?id='.$value->id, '<i class="fa fa-pencil"></i> ', 'data-toggle="modal" data-target="#myModal"')?>&nbsp;</td></tr>
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
                            <th></th>
                            <th></th>
                           
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th>
                            <th></th><th></th><th></th><th></th><th></th>
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

