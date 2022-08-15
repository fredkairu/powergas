<div>   <link href="<?= $assets ?>js/jquery-ui.css" rel="stylesheet"/>
  <script type="text/javascript" src="<?= $assets ?>/js/jquery-ui.js"></script>
  
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.20/fc-3.3.0/fh-3.1.6/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
  <script>
    $(document).ready(function () {
        var oTable = $('.SLData').dataTable({
           //"aaSorting": [[0, "asc"], [1, "desc"]],
           "bSort":false,
            "aLengthMenu": [[10, 25, 50, 100,500,1000,2000, -1], [10, 25, 50, 100,500,1000,2000,"<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
        fixedColumns:   {
            leftColumns: 1,
    rightColumns:0
            
        }  
       /* , "footerCallback": function ( row, data, start, end, display ) {
				var api = this.api();
				nb_cols = api.columns().nodes().length;
				var j = 1;
				while(j < nb_cols){
					var pageTotal = api
                .column( j, { page: 'previous'} )
                .data()
                .reduce( function (a, b) {
                    return Math.round(a) + Math.round(b);
                }, 0 );
          // Update footer
          $( api.column( j ).footer() ).html(pageTotal);
					j++;
							} 
			}*/
		}
	);
	
	$('.exporttable2').click(function(e){
             e.preventDefault();
     $("#exceltable2").table2excel({
					//exclude: ".noExl",
					name: "Exported File",
					filename: "exportedList"
				});
				
     });
     
       $(".SLData td").each(function() {
    if (parseInt($(this).text()) < 0) {
      $(this).css('color', 'red');
    }
  });
});
</script>
  <style>
td, th {
  margin: 0;
  border: 1px solid grey;
  white-space: nowrap !important;
  border-top-width: 0px;
  text-align:center;
      overflow-x: hidden !important;
}

#chartdiv {
  width	: 100%;
  height	: 250px;
}

#chartdiv2{
  width	: 100%;
  height: 400px;
}

select[multiple], select[size] {
    height: 0px;
    height: 0px;
    background-color: white;
    border: 1px white;
    overflow-y: hidden;
    display: none !important;
}

.DTFC_LeftBodyLiner{
    
 overflow-x: hidden !important;   
}

select2-search-choice {
    border: 0;
    padding: 2px 2px 2px 18px;
    border-radius: 0;
    box-shadow: none;
    background: #ccc
}
.dataTables_scrollBody{
  //  overflow-x:hidden !important ;
}

      </style>
  


<?php 
    
    
    function markRed($number){
      
            return $number;
     
    
    }
    
    
  $attrib = array('role' => 'form','method'=>'POST','class'=>'searchform');?>
  <div class="searchform" style="z-index:999;top:10px;background-color:white;">
              <?php  echo  form_open_multipart("welcome/sit", $attrib);?>
            <div class="row">
                <div class="col-md-2"><div class="form-group">
                                                <?= lang("GBU", "gbu"); ?>
                                                <?php
                                                $bus=$this->settings_model->getBU(TRUE);
                                                $gbu["all"]="ALL";
                                                foreach ($bus as $bu){ 
                                                    $gbu[$bu->name]=strtoupper($bu->name);
                                                
                                                }
                                                echo form_dropdown('gbu', $gbu,($_POST['gbu']), 'id="gbu" multiple="multiple" autocomplete="on" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("gbu") . '"  style="width:100%;" ');
                                                ?>
            </div></div>
            
             
                <div class="col-md-2">
        <div class="form-group">
                                                <?= lang("Cluster*", "cluster"); ?>
                                                <?php
                                                foreach ($clusters as $clust) {
                                                    $clusterdetails[$clust->name]=$clust->name;
                                                }
                                                
                                                echo form_dropdown('cluster[]',$clusterdetails,NULL, 'id="cluster" multiple="multiple" autocomplete="on" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("cluster") . '"  style="width:100%;" ');
                                                ?>
                                            </div>
        </div>
                <div class="col-md-2">
                     <?= lang("Country*", "country"); ?>
                    <div class="form-group">
                         <select id="f_country" name="f_country[]" id="f_country_label"  multiple="multiple" class="form-control select input-xs">
            <?php 
            
            $selectedcountries=@$_POST["f_country"];
            if(@$selectedcountries){
                foreach(@$selectedcountries as $country){
                    $cname=$this->settings_model->getCurrencyByID($country);
                ?>
                             <option selected="selected" value="<?=$country?>"><?=$cname->country?></option>
                             
                             
            <?php 
                }
                }?>
        </select>
                                
            </div></div>
            
            
                <div class="col-md-2">
                  <div class="form-group">
                                                <?= lang("Distributor", "distributor"); ?>
                                                <?php
                                                $this->db->select("companies.name,companies.id,currencies.country");
                                                $this->db->where(array("group_name"=>"customer"));
                                                $this->db->join("currencies","companies.country=currencies.id","left");
                                                $q = $this->db->get('companies');
         if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->name){
                $distributors[$row->id] =$row->name."(".$row->country.")";  
                }
                else{
                   $distributors[$row->id] = $row->name."(".$row->country.")";  
                }
            }
        }
                                                
                                                echo form_dropdown('distributor[]',$distributors,($_POST['distributor']), 'id="distributor" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("distributor") . '"  style="width:100%;" ');
                                                ?>
            </div>  
                    
                </div>
                
            
                <div class="col-md-2"><div class="form-group">
                                                <?= lang("Promotion", "promotion"); ?>
                                                <?php
                                                $promo=array("all"=>"All","1"=>"Promoted","0"=>"Non-Promoted");
                                               echo form_dropdown('promotion', $promo,($_POST['promotion']), 'id="promo" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("Promotion_type") . '"  style="width:100%;" ');
                                                ?>
            </div></div>
            
             
                
             
                     
</div>
               
            </div>
                <div class="row">
                    <div class="col-md-2">
                     <div class="form-group">
                          <?= lang("Period*", "period"); ?>
                      <?php   
                         $oneyearago=date('Y', strtotime('-1 year'));
                      $twoyearsago=date('Y', strtotime('-2 year'));
                      $thisyear=date('Y');
                           $currentyears=array($twoyearsago=>$twoyearsago,$oneyearago=>$oneyearago,$thisyear=>$thisyear);
                      echo form_dropdown('period',$currentyears,($_POST['period']), 'id="period"   class="form-control input-tip select"  data-placeholder="' . lang("select") . ' period" style="width:100%;" ');?>   
                        
                        </div>
                 </div>
                   <!--  <div class="col-md-2">
                  <div class="form-group">
                      <?= lang("Sales Type", "Sales Type"); ?>
                        <?php echo form_dropdown('sales_type',array("all"=>"All","SI"=>"SI","PSO"=>"Primary","SSO"=>"Secondary"),($_POST['sales_type']), 'id="sales_type"  class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("sales_type") . '"  style="width:100%;" ');?>
                      
                  </div></div>-->
                   <div class="col-md-2">
                  <div class="form-group">
                                                <?= lang("Brand", "category"); ?>
                                                <?php
                                                $q = $this->db->get('categories');
                                                $data['all']="Select All";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[$row->id] = $row->name;
            }
        }
                                                
                                                echo form_dropdown('category[]',$data,($_POST['category']), 'id="category" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("category") . '"  style="width:100%;" ');
                                                ?>
            </div>  
                    
                </div>
                
                
 
            
                <div class="col-md-2" style="z-index:1000">
                  <div class="form-group">
                                                <?= lang("Product GMID", "product"); ?>
                                                <?php
                                                
                                                $q = $this->db->get('products');
                                                 $products["all"]="Select All";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $products[$row->code] = $row->name."(".$row->code.")";
            }
        }
                                                
                                                echo form_dropdown('products[]',$products,($_POST['products']), 'id="prodcts" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("products") . '"  style="width:100%;" ');
                                                ?>
            </div>  
                    
                </div>
                       
                       
                    <!--<div class="col-md-2"><div class="form-group"><label>From</label><input type="text" placeholder="dd/yyyy" autocomplete="off" class="form-control input-tip datepicker monthPicker"  id="datefrom" value="01/<?=date('Y')?>" name="datefrom"></div>
                </div>
                    <div class="col-md-2"><div class="form-group"><label>To</label><input type="text" placeholder="dd/yyyy" autocomplete="off" class="form-control input-tip datepicker monthPicker"  id="dateto" value="12/<?=date('Y')?>" name="dateto"></div>-->
                
                 
                    <div class="col-md-2">
                      <label for="gsales" class="">Gross/Net</label>
                <?php
                                                $grossnet=array("1"=>"Gross","0"=>"Net");
                                                echo form_dropdown('gsales', $grossnet,($_POST['gsales']), 'id="gsales"  autocomplete="on" class="form-control input-tip select"   style="width:100%;" ');
                                                ?>

                    </div>
                   
                   
                  
                    <!--<div class="col-md-2"><div class="form-group">
                        
                      <?php 
                      $pricetypes=array("unified"=>"Unified","resale"=>"Resale","supply"=>"Supply");
                      echo form_dropdown('price_type',$pricetypes,($_POST['price_type']), 'id="price_type"  class="form-control input-tip select"  data-placeholder="' . lang("select") . ' price_type" style="width:100%;" ');?>   
                        
                    </div></div>-->
    <div class="col-md-2"><div class="form-group" style="padding-top:30px"><input type="submit" name="filter" value="Filter" class="btn btn-primary">&nbsp;<input type="button" id="resetform" value="Reset" class="btn btn-info"></div>
                </div>
<?php echo form_close(); ?>
    

</div>
  <?php 
  if(!$yearonly){
  $yearonly=  substr(date("Y"),-2); 
  }
  if(!$currentmonth){$currentmonth="Month";}
  
//print_r($alldates);
?>
      
      <div class="row">
      <div style="overflow-y: scroll;overflow-x:scroll;max-height:400px !important">
      <div class="col-md-12" >
          <h4>SIT Analysis <a href="#" class="exporttable2 pull-right">Export As Xls</a></h4>
            <div id="exceltable2">
       
         <table  class="SLData table table-bordered table-hover table-striped">
                        <thead>
                           
                        <tr>
                            <th class="headcol"><?php echo $this->lang->line("Report"); ?></th>
                            <?php foreach ($alldates as $month) { 
                                $month_name = date("F", mktime(0, 0, 0, substr($month,0,2), 10)); 
                                ?>
                                
                           
                            <th><?php echo $this->lang->line(substr($month_name,0,3)."-".substr($month,-2)); ?></th>
                           <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                       
                           <?php foreach ($countries as $ctry){
                               $values=0;
                               ?>
                           <tr>
                             <td><?=$ctry["name"]?>  </td>
                                  <?php foreach ($ctry["salestotals"] as $monthsalestotals=>$value) { 
                                      
                                      ?>
                                
                           
                               <td><?php if(!$value){echo "0";} else{echo round($value,2);}?></td>
                           <?php } ?>
                                   
                                    
                           
                            </tr>
                           <?php  }
?>                       
                       
                   <!-- <tr>
			<td style="font-weight:bold">Totals</td>
			<?php
                        foreach ($alldates as $month) {
                         
                            echo "<td style='font-weight:bold'>".sumSales($month,$countries)."</td>";
                        
                        }
                        ?>
		</tr> -->       
                        </tbody>
               <!--       <tfoot>     
		<tr>
			<td>Totals</td>
			
		</tr>
                </tfoot>-->
                    </table>
          </div>
      </div>
      </div>
  </div>
  
<!--<div class="row">
    <h4>&nbsp;&nbsp;&nbsp;PSO-SSO-&-SIT_Analyis</h4>
    <div class="col-md-1"></div>
    <div class="col-md-11" id="chartdiv">
        
        
        
        
    </div>
</div>-->
<div class="row">
    <h4>&nbsp;&nbsp;&nbsp;PSO-SSO-&-SIT_Analyis Monthly Trend</h4>
    <div class="col-md-1"></div>
    <div class="col-md-11" id="chartdiv2">
        
        
        
        
    </div>
</div>
  
    <?php
    
   
    function sumSales($month,$countries){
       // die(print_r($countries));
        $sum=0;
        foreach ($countries as $sales) {
          $sum+=$sales["salestotals"][$month];
      
                  }
         return $sum;
    }
    
    ?>
      
      
      
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
        $("#ui-datepicker-div").position({
            my: "center top",
            at: "center bottom",
            of: $(this)
        });
    });
    
    //get countries
    //$('#f_country, #f_country_label').hide();
$('#cluster').change(function(){
    var state_id = $('#cluster').val();
   // alert(state_id);
    if(state_id==null){
       $('#f_country').empty();  
    }
   states=state_id.toString().split(',');
   laststate=states.pop();
   if(laststate !=""){
       state_id=laststate;
   }
    if (state_id != ""){
        var post_url = "index.php/system_settings/get_cities/" + state_id;
        $.ajax({
            type: "POST",
            url: post_url,
             data:{'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
    
            success: function(cities) //we're calling the response json array 'cities'
            {
              //  $('#f_country').empty();
                $('#f_country, #f_country_label').show();
                $.each(cities,function(id,city)
                {
                    var opt = $('<option />'); // here we're creating a new select option for each group
                    opt.val(id);
                    opt.text(city);
                    $('#f_country').append(opt);
                });
            } //end success
         }); //end AJAX
    } else {
        $('#f_country').empty();
      //  $('#f_country, #f_country_label').hide();
    }//end if
}); //end change
    
    $('#category').change(function(){
    var state_id = $(this).val();
   // alert(state_id);
    if(state_id==null){
       $('#prodcts').empty();  
    }
   states=state_id.toString().split(',');
   laststate=states.pop();
   if(laststate !=""){
       state_id=laststate;
   }
    if (state_id != ""){
        var post_url = "index.php/system_settings/get_products/" + state_id;
        $.ajax({
            type: "POST",
            url: post_url,
             data:{'<?php echo $this->security->get_csrf_token_name(); ?>' : '<?php echo $this->security->get_csrf_hash(); ?>'},
    
            success: function(cities) //we're calling the response json array 'cities'
            {
              //  $('#f_country').empty();
              $('#prodcts').empty();
                $('#prodcts').show();
                $.each(cities,function(id,city)
                {
                    var opt = $('<option />'); // here we're creating a new select option for each group
                    opt.val(id);
                    opt.text(city);
                    $('#prodcts').append(opt);
                });
            } //end success
         }); //end AJAX
    } else {
        $('#prodcts').empty();
    
    }//end if
}); //end change
    
    $("#resetform").click(function(e){
  
    $("#cluster").val("");
    $('#f_country').val("");
   
    $('#gbu').val("");$('#promotion').val("");$('#category').val(0);$('#prodcts').val(0);$("#datefrom").val("");$("#dateto").val("");$("#s2id_prodcts").empty();$("#s2id_category").empty();$("#s2id_customer").empty(); $("#s2id_cluster").empty();$("#s2id_f_country").empty();$("#f_country").val(""); $('#prodcts').empty();
    
    window.location.reload();

    });
    
 /*****change sales type**********/
 $("#sales_type").change(function(e){
if($("#sales_type :selected").val()=="SSO" || $("#sales_type :selected").val()=="PSO" ){
    $("#s2id_customer").css("display","none");
    $("#s2id_distributor").css("display","block");
} 
else if($("#sales_type :selected").val()=="SSO" ){
  $("#s2id_customer").css("display","block");
  $("#s2id_distributor").css("display","none");
}
else{
     $("#s2id_customer").css("display","block");
  $("#s2id_distributor").css("display","block");
}
});    
});
</script>


<script>
    /*
var chart = AmCharts.makeChart("chartdiv", {
  "type": "serial",
  "theme": "light",
  "dataProvider":<?=$sitanalysis?>,
  "graphs": [
    {
      "type": "line",
      "title": "SSO",
      "valueField": "SSO",
     "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "showBalloon": true
    },
    {
      "type": "line",
      "fillAlphas": 0.9,
        "lineAlpha": 0.2,
      "title": "PSO",
      "valueField": "PSO",
      "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
  
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "showBalloon": true
    },
    {
      "title": "Stock",
      "type": "line",
      "valueField": "Stock",
      "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "showBalloon": true
    },
    {
      "type": "line",
      "fillAlphas": 0.9,
        "lineAlpha": 0.2,
      "title": "Month_Cvr",
      "valueField": "Month_Cvr",
      "bullet": "round",
     "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
   
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "showBalloon": true
    }
   
   
    
            ],
  "categoryField": "Date",
  "categoryAxis": {
    "gridPosition": "start",
    "labelRotation":65,
    "fontSize":9
  }, "export": {
    "enabled": true
  },
  "legend": {
      "useGraphSettings": true
  }});
  */
  //bar chart
  var chartt = AmCharts.makeChart("chartdiv2", {
  "type": "serial",
  "theme": "light",
  "dataProvider":<?=$sitanalysis?>,
  "valueAxes": [{
        "id":"v1",
        "axisColor": "#FF6600",
        "axisThickness": 2,
        "axisAlpha": 1,
        "position": "left"
    }, {
        "id":"v2",
        "axisColor": "#FCD202",
        "axisThickness": 2,
        "axisAlpha": 1,
        "position": "right"
    }],
  "graphs": [
    {
        "valueAxis": "v1",
      "type": "column",
      "fillAlphas": 0.9,
        "lineAlpha": 0.2,
      "title": "SSO",
      "valueField": "SSO",
      "balloonText": "[[category]]: <b>[[value]]</b>",
    "showBalloon": true
    },
    {
        "valueAxis": "v1",
      "type": "column",
      "fillAlphas": 0.9,
        "lineAlpha": 0.2,
      "title": "PSO",
      "valueField": "PSO",
      "balloonText": "[[category]]: <b>[[value]]</b>",
    "showBalloon": true
    },
    {"valueAxis": "v1",
      "type": "column",
      "fillAlphas": 0.9,
        "lineAlpha": 0.2,
      "title": "Stock",
      "valueField": "Stock",
      "balloonText": "[[category]]: <b>[[value]]</b>",
    "showBalloon": true
    },
        {
      "type": "line",
     "valueAxis": "v2",
      "title": "Month_Cvr",
      "valueField": "Month_Cvr",
      "labelText": "[[value]]",
    "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
      "balloonText": "[[category]]: <b>[[value]]</b>",
    "showBalloon": true
    }
   
   
    
            ],
  "categoryField": "Date",
  "categoryAxis": {
    "gridPosition": "start",
    "labelRotation":65,
    "fontSize":9
  }, "export": {
    "enabled": true
  },
  "legend": {
      "useGraphSettings": true
  }});
  
  
  
  
  
  
  
  
  
   function saveJpg(){
        chart.export.capture({}, function() {
    this.toJPG({}, function(data) {
      this.download(data, this.defaults.formats.JPG.mimeType, "amCharts.JPG");
    });
        });
    }
    
    function exportJSON() {
  chart.export.toJSON({}, function(data) {
    this.download(data, this.defaults.formats.JSON.mimeType, "amCharts.json");
  });
}

/**
 * Exports and triggers download of chart data as CSV file
 */
function exportCSV() {
  chart.export.toCSV({}, function(data) {
    this.download(data, this.defaults.formats.CSV.mimeType, "amCharts.csv");
  });
}

/**
 * Exports and triggers download of chart data as Excel file
 */
function exportXLSX() {
  chart.export.toXLSX({}, function(data) {
    this.download(data, this.defaults.formats.XLSX.mimeType, "amCharts.xlsx");
  });
}

function printChart(){
   chart.export.capture( {}, function() {
       this.toPRINT();
   } );
}

//chart.addListener("dataUpdated", zoomChart);
//
//function zoomChart(){
//    chart.zoomToDates(new Date(<?=date("Y")-1?>, 0, 2), new Date(<?=date("Y")?>, 0, 13));
//}
</script>
<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/fc-3.3.0/fh-3.1.6/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.js"></script>



</div>