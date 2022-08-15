<script>
    $(document).ready(function () {
        var oTable = $('#GuestData').dataTable({
            "aaSorting": [[1, "asc"]],
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            'bProcessing': true, 'bServerSide': true,
            'sAjaxSource': '<?= site_url('vehicles/getRouteStartingPoints') ?>',
            'fnServerData': function (sSource, aoData, fnCallback) {
                aoData.push({
                    "name": "<?= $this->security->get_csrf_token_name() ?>",
                    "value": "<?= $this->security->get_csrf_hash() ?>"
                });
                $.ajax({'dataType': 'json', 'type': 'POST', 'url': sSource, 'data': aoData, 'success': fnCallback});
            },
            "aoColumns": [{
                "bSortable": false,
                "mRender": checkbox
            }, null,null,null,null,{"mRender": row_status2}, {"bSortable": false}]
        }).dtFilter([
            {column_number: 1, filter_default_label: "[<?=lang('Shop Name');?>]", filter_type: "text", data: []},
            {column_number: 2, filter_default_label: "[<?=lang('Day');?>]", filter_type: "text", data: []},
            {column_number: 3, filter_default_label: "[<?=lang('Customer');?>]", filter_type: "text", data: []},
            {column_number: 4, filter_default_label: "[<?=lang('Vehicle');?>]", filter_type: "text", data: []},
            {column_number: 5, filter_default_label: "[<?=lang('Starting Point');?>]", filter_type: "text", data: []},
        ], "footer");
    });
</script>
<div class="box">
    <div class="box-content">
        <div class="row">
            <div class="col-lg-12">

                <p class="introtext"><?= lang('list_results'); ?></p>

                <div class="table-responsive">
                    <table id="GuestData" cellpadding="0" cellspacing="0" border="0"
                           class="table table-bordered table-condensed table-hover table-striped">
                        <thead>
                        <tr class="primary">
                            <th style="min-width:30px; width: 30px; text-align: center;">
                                <input class="checkbox checkth" type="checkbox" name="check"/>
                            </th>
                            <th><?= lang("shop"); ?></th>
                            <th><?= lang("Days Served"); ?></th>
                            <th><?= lang("Customer"); ?></th>
                            <th><?= lang("Vehicle"); ?></th>
                            <th><?= lang("Starting Point"); ?></th>
                            <th style="width:85px;"><?= lang("actions"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td colspan="8" class="dataTables_empty"><?= lang('loading_data_from_server') ?></td>
                        </tr>
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
                        
                            <th style="width:85px;" class="text-center"><?= lang("actions"); ?></th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
        $(document).ready(function(){
        $('table tbody').sortable({
           
            update:function(event,ui)
            {
            $(this).children().each(function(index){
                if($(this).attr('data-position') != (index+1))
                {
                    $(this).attr('data-position',(index+1)).addClass('updated');
                }
            });

            saveNewPositions();
            }
        });

        function saveNewPositions()
        {
            var positions = [];

            $('.updated').each(function(){
                positions.push([$(this).attr('data-index'),$(this).attr('data-position')]);
                $(this).removeClass('updated');
            })

            $.ajax({
                url: "index.php",
                method: "POST",
                dataType: "text",
                data: {
                    update :1,
                    positions :positions
                },success:function(response)
                {
                    console.log(response);
                }
                
            })
        }
    });
  
</script>
<?php if ($Owner) { ?>
    <div style="display: none;">
        <input type="hidden" name="form_action" value="" id="form_action"/>
        <?= form_submit('performAction', 'performAction', 'id="action-form-submit"') ?>
    </div>
    <?= form_close() ?>
<?php } ?>
<?php if ($action && $action == 'add') {
    echo '<script>$(document).ready(function(){$("#add").trigger("click");});</script>';
}
?>
	

