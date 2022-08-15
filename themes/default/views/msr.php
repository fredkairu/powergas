<div>   
    
    <link href="<?= $assets ?>js/jquery-ui.css" rel="stylesheet"/>
  <script type="text/javascript" src="<?= $assets ?>/js/jquery-ui.js"></script>
  
  
  <script>
   $(document).ready(function () {
      /*  var oTable = $('.SLData').dataTable({
           "aaSorting": [[0, "asc"], [1, "desc"]],
           "bSort":true,
            "aLengthMenu": [[500,1000,2000, -1], [500,1000,2000,"<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            scrollY:        "300px",
        scrollX:        true,
        scrollCollapse: true,
            paging:         true,
        fixedColumns:   {
            leftColumns:2,
    rightColumns:0
            
        }  , "footerCallback": function ( row, data, start, end, display ) {
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
			}
		}
	);
	*/
	$('.exporttable2').click(function(e){
             e.preventDefault();
     $("#exceltable2").table2excel({
					//exclude: ".noExl",
					name: "Exported File",
					filename: "msr"
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
/*
td, th {
  margin: 0;
  border: 1px solid grey;
  white-space: nowrap !important;
  border-top-width: 0px;
  text-align:center;
      overflow-x: hidden !important;
}
*/


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



td.description {
  vertical-align: top !important;
}

table {
  text-align: left;
  position: relative;
}

th {
  background: white;
  position: sticky;
  top: 0;
}
      </style>
  


<?php 
    $this->load->model('budget_model');
    
    function markRed($number){
       
            return $number;
    
    
    }
    
    
  $attrib = array('role' => 'form','method'=>'GET','class'=>'searchform');?>
  <div class="searchform" style="z-index:999;top:10px;background-color:white;display:none">
              <?php  echo  form_open_multipart("welcome/msranalysis", $attrib);?>
            <div class="row">
                <div class="col-md-2"><div class="form-group">
                                                <?= lang("GBU", "gbu"); ?>
                                                <?php
                                               $bus=$this->settings_model->getBU(TRUE);
                                                $gbu["all"]="ALL";
                                                foreach ($bus as $bu){ 
                                                    $gbu[$bu->name]=strtoupper($bu->name);
                                                
                                                }
                                                echo form_dropdown('gbu', $gbu,($_GET['gbu']), 'id="gbu" multiple="multiple" autocomplete="on" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("gbu") . '"  style="width:100%;" ');
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
            
            $selectedcountries=@$_GET["countrys"];
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
                          <?= lang("Period*", "period"); ?>
                      <?php   
                         $oneyearago=date('Y', strtotime('-1 year'));
                      $twoyearsago=date('Y', strtotime('-2 year'));
                           $currentmonths=array("01-".$twoyearsago=>"01-".$twoyearsago,"02-".$twoyearsago=>"02-".$twoyearsago,"03-".$twoyearsago=>"03-".$twoyearsago,"04-".$twoyearsago=>"04-".$twoyearsago,"05-".$twoyearsago=>"05-".$twoyearsago,"06-".$twoyearsago=>"06-".$twoyearsago,"07-".$twoyearsago=>"07-".$twoyearsago,"08-".$twoyearsago=>"08-".$twoyearsago,"09-".$twoyearsago=>"09-".$twoyearsago,"10-".$twoyearsago=>"10-".$twoyearsago,"11-".$twoyearsago=>"11-".$twoyearsago,"12-".$twoyearsago=>"12-".$twoyearsago,"01-".$oneyearago=>"01-".$oneyearago,"02-".$oneyearago=>"02-".$oneyearago,"03-".$oneyearago=>"03-".$oneyearago,"04-".$oneyearago=>"04-".$oneyearago,"05-".$oneyearago=>"05-".$oneyearago,"06-".$oneyearago=>"06-".$oneyearago,"07-".$oneyearago=>"07-".$oneyearago,"08-".$oneyearago=>"08-".$oneyearago,"09-".$oneyearago=>"09-".$oneyearago,"10-".$oneyearago=>"10-".$oneyearago,"11-".$oneyearago=>"11-".$oneyearago,"12-".$oneyearago=>"12-".$oneyearago,"01-".date("Y")=>"01-".date("Y"),"02-".date("Y")=>"02-".date("Y"),"03-".date("Y")=>"03-".date("Y"),"04-".date("Y")=>"04-".date("Y"),"05-".date("Y")=>"05-".date("Y"),"06-".date("Y")=>"06-".date("Y"),"07-".date("Y")=>"07-".date("Y"),"08-".date("Y")=>"08-".date("Y"),"09-".date("Y")=>"09-".date("Y"),"10-".date("Y")=>"10-".date("Y"),"11-".date("Y")=>"11-".date("Y"),"12-".date("Y")=>"12-".date("Y"));
                      echo form_dropdown('period[]',$currentmonths,($_GET['period']), 'id="period"  multiple="multiple"  class="form-control input-tip select"   data-placeholder="' . lang("select") . ' period" style="width:100%;" ');?>   
                        
                        </div>
                 </div>        
                

    <div class="col-md-2"><div class="form-group" style="padding-top:30px"><input type="submit" name="filter" value="Filter" class="btn btn-primary">&nbsp;<input type="button" id="resetform" value="Reset" class="btn btn-info"></div>
                </div>
<?php echo form_close(); ?>         
             
                
             
                     
</div>
           
                    
                 
                
                
 
            
                
                       
                       
                    <!--<div class="col-md-2"><div class="form-group"><label>From</label><input type="text" placeholder="dd/yyyy" autocomplete="off" class="form-control input-tip datepicker monthPicker"  id="datefrom" value="01/<?=date('Y')?>" name="datefrom"></div>
                </div>
                    <div class="col-md-2"><div class="form-group"><label>To</label><input type="text" placeholder="dd/yyyy" autocomplete="off" class="form-control input-tip datepicker monthPicker"  id="dateto" value="12/<?=date('Y')?>" name="dateto"></div>-->
                
                 
                 <!--   <div class="col-md-2">
                      
                
                <input id="gsales" name="gsales" type="radio" class="" <?php echo "checked='checked'"; ?> value="1" <?php echo $this->form_validation->set_radio('gsales', 1); ?> />
                <label for="gsales" class="">Gross Sales</label>
                <br>
                <input id="gsales2" name="gsales" type="radio" class=""  value="0" <?php echo $this->form_validation->set_radio('gsales', 0); ?> />
                <label for="gsales" class="">Net Sales</label>

                    </div>-->
                   
                   
                  
                  
   
    

   
            </div>
               
  <?php 
  if(!$yearonly){
  $yearonly=  substr(date("Y"),-2); 
  }
  if(!$currentmonth){$currentmonth="Month";}
  
//print_r($alldates);
?>
      
    
  
<div class="row">
      <div style="overflow-y: scroll;overflow-x:scroll;height:400px;max-height:400px !important">
      <div class="col-md-12" >
          <h4>Msr Analysis<a href="#" class="exporttable2 pull-right">Export As Xls</a></h4>
            <div id="exceltable2">
       
         <table  class="SLData table table-bordered table-hover table-striped">
                        <thead>
                           
                        <tr>
                            <th class="headcol"><?php echo $this->lang->line("Team"); ?></th>
                            <th class="headcol"><?php echo $this->lang->line("Sf_alignment"); ?></th>
                            <th class="headcol"><?php echo $this->lang->line("Brand"); ?></th>
                           <th class="headcol"><?php echo $this->lang->line("Customer"); ?></th>
                             <th class="headcol"><?php echo $this->lang->line("Product"); ?></th>
                              <th class="headcol"><?php echo $this->lang->line("Target (K.Euro)"); ?></th>
                               <th class="headcol"><?php echo $this->lang->line("Ach.Net(K.Euro)"); ?></th>
                             <th class="headcol"><?php echo $this->lang->line("% Ach."); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                       
                           <?php
                           $targettotal=0;
                           $salestotal=0;
                           foreach ($alldata as $ctry){
                               $values=0;
                               $counted=count($ctry["alignment_products_customers"])+1;
                                if($counted>2){
                                    //die(print_r($ctry["alignment_products_customers"]));
                               ?>
                           <tr>
                               <td rowspan="<?=$counted?>" class="description"><?=$ctry["team_name"]?>  </td>
                               <td rowspan="<?=$counted?>" class="description"><?=$ctry["sf_name"]?></td>
                               <?php                                  foreach ($ctry["alignment_products_customers"] as $sfproduct) {
                                   $category=$this->site->getProductCategoryByProductId($sfproduct->product_id); 
//                                  if($filters["gbu"]){
//                                   if($category["gbu"]==$filters["gbu"])  { 
//                                   
                                  
                                   ?>
                                     
                                 <tr> 
                                      <td><?php 
                                     
                                      echo $category["category_name"]?>  </td>
                               <td><?php echo $sfproduct->customer_name?></td>
                                    <td><?php echo $sfproduct->products?> </td>
                                    <td>
                                     <?php
                                    $budget= $this->budget_model->getMsrTotalBudgetForPeriodWithProduct($newdates,$sfproduct->sf_alignment_id,$sfproduct->customer_id,$category["product_gmid"],"ALL","budget",$filters);
                                   echo $budget;
                                   $targettotal+=$budget;
                                    ?>
                                        
                                    </td>
                                    <td>
                                        
                                   <?php
                                   $sale=$this->budget_model->getMsrTotalSalesForPeriodWithProduct($newdates,$sfproduct->sf_alignment_id,$sfproduct->customer_id,$category["product_gmid"],"ALL",$filters);
                                     echo $sale;
                                     $salestotal+=$sale;
                                     ?>      
                                    </td>
                                    
                                    <td>
                                       <?php echo round((($sale-$budget)/$budget)*100); ?>
                                    </td>
                           </tr>
                               
                              
                               
                           
                            </tr>
                           <?php 
//                                   }
//                           
//                                   }
                                }
                                
                                   }
                           }
?>                       
                       
                         
                        </tbody>
                        <tfoot>
                <tr>
			<th>Totals</th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th></th>
                        <th><?=round($targettotal,2)?></th>
                        <th><?=round($salestotal,2)?></th>
                        <th>
                             <?php echo round((($salestotal-$targettotal)/$targettotal)*100); ?>
                        </th>
                       
		</tr>
         </tfoot>
                    </table>
          </div>
      </div>
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



<!--<script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.20/fc-3.3.0/fh-3.1.6/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.js"></script>-->



</div>