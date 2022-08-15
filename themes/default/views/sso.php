<div>   <link href="<?= $assets ?>js/jquery-ui.css" rel="stylesheet"/>
  
  <script type="text/javascript" src="<?= $assets ?>/js/jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="<?=$assets?>/styles/datatables.min.css"/>
  <script>
    $(document).ready(function () {
         var prd20=[];var prd16=[];var prd1=[];var prd3=[];var prd4=[];var prd5=[];
         var prd6=[];var prd2=[];var prd10=[];var prd11=[];var prd8=[];
         var prd13=[];var prd9=[];var prd18=[];var prd15=[];var prd17=[];
          var prd20=[];var prd16=[];var prd28=[];var prd30=[];var prd10=[];var prd26=[];var prd24=[];
          var prd25=[];  var prd27=[];
          
        var oTable = $('.SLData').dataTable({
            "aaSorting": [[0, "asc"], [1, "desc"]],
            "bSort":true,
            "aLengthMenu": [[10, 25, 50, 100,500,1000,2000, -1], [10, 25, 50, 100,500,1000,2000,"<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            scrollY:        "200px",
        scrollX:        true,
        scrollCollapse:true,
        paging:         false,
         buttons: [
            'copyHtml5',
            'excelHtml5',
            'csvHtml5',
            'pdfHtml5'
        ],
        fixedColumns:   {
            leftColumns: 1,
    rightColumns:0
            
        }, "footerCallback": function ( row, data, start, end, display ) {
				var api = this.api();
				nb_cols = api.columns().nodes().length;
				var j = 1;
				while(j < nb_cols){
					var pageTotal = api
                .column( j, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                     return Math.round(a) + Math.round(b);
                }, 0 );
          // Update footer
         
          $( api.column( j ).footer() ).html(pageTotal);
					j++;
							} 
                      //specific cells
                     
                      prd20.push(parseInt($(".prd20").html()));
                      prd16.push(parseInt($(".prd16").html()));
                      prd4.push(parseInt($(".prd4").html()));
                      prd1.push(parseInt($(".prd1").html()));
                       prd6.push(parseInt($(".prd6").html()));
                      prd2.push(parseInt($(".prd2").html()));
                       prd11.push(parseInt($(".prd11").html()));
                      prd8.push(parseInt($(".prd8").html()));
                       prd13.push(parseInt($(".prd13").html()));
                      prd9.push(parseInt($(".prd9").html()));
                       prd18.push(parseInt($(".prd18").html()));
                      prd15.push(parseInt($(".prd15").html()));
                      //
                      prd20.push(parseInt($(".prd20").html()));
                      prd16.push(parseInt($(".prd16").html()));
                       prd24.push(parseInt($(".prd24").html()));
                        prd25.push(parseInt($(".prd25").html()));
                      prd28.push(parseInt($(".prd28").html()));
                       prd30.push(parseInt($(".prd30").html()));
                        prd10.push(parseInt($(".prd10").html()));
                        //prevent overlapping of totals
                        $(".prd5").html(parseInt(Math.round((prd4[0]/prd1[0])*100))+"%"); 
$(".prd7").html(parseInt(Math.round((prd6[0]/prd2[0])*100))+"%");
$(".prd12").html(parseInt(Math.round((prd11[0]/prd8[0])*100))+"%");
$(".prd14").html(parseInt(Math.round((prd13[0]/prd9[0])*100))+"%");
$(".prd19").html(parseInt(Math.round((prd18[0]/prd15[0])*100))+"%");
$(".prd21").html(parseInt(Math.round((prd20[0]/prd16[0])*100))+"%");
$(".prd29").html(parseInt(Math.round((prd28[0]/prd24[0])*100))+"%");
$(".prd31").html(parseInt(Math.round((prd30[0]/prd25[0])*100))+"%");
                        
                      
}
			}
	);

// % totals columns


	   $('.exporttable').click(function(e){
             e.preventDefault();
     $("#exceltable").table2excel({
					//exclude: ".noExl",
					name: "Exported File",
					filename: "exportedList"
				});
				
     });
     $('.exporttable2').click(function(e){
             e.preventDefault();
     $("#exceltable2").table2excel({
					//exclude: ".noExl",
					name: "Exported File",
					filename: "exportedList"
				});
				
     });
     
     //mark red 
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
      overflow-x: hidden !important;
  text-align:center;
}
.DTFC_LeftBodyLiner{
    
 overflow-x: hidden !important;   
}
::-webkit-scrollbar {
    width:0px;
    height:3px;
    background-color:#ccc; /* make scrollbar transparent */
    opacity:0.7;
}
  
  .amChartsLegend{
      height:300px !important;
      overflow-y:scroll !important;
  }

select[multiple], select[size] {
    height: 0px;
    height: 0px;
    background-color: white;
    border: 1px white;
    overflow-y: hidden;
    display: none !important;
}
select2-search-choice {
    border: 0;
    padding: 6px 6px 6px 20px;
    border-radius: 0;
    box-shadow: none;
}

#chartdivstackedfullyearlegend{
    overflow-y:scroll;
}
.dataTables_scrollBody{
   // overflow-x:hidden !important ;
}

      
      </style>
  

  


<?php 
    foreach ($chatData as $currentmonth_sale) {
        $currentmonths[] = date('M-Y', strtotime($currentmonth_sale->month));
        $msales[] = $currentmonth_sale->sales;
        $mtax1[] = $currentmonth_sale->tax1;
        $mtax2[] = $currentmonth_sale->tax2;
        $mpurchases[] = $currentmonth_sale->purchases;
        $mtax3[] = $currentmonth_sale->ptax;
    }
    
    function markRed($number){
        return round($number,2);

    }
    
    
  $attrib = array('data-toggle' => 'validator', 'novalidate','role' => 'form','method'=>'POST','id'=>'searchform');?>
  <div class="searchform" style="z-index:999;top:10px;background-color:white;">
              <?php  echo  form_open_multipart("welcome/sso", $attrib);?>
            <div class="row">
                <div class="col-md-2"><div class="form-group">
                                                <?= lang("GBU", "gbu"); ?>
                                                <?php
                                                $bus=$this->settings_model->getBU(TRUE);
                                                $gbu["all"]="ALL";
                                                foreach ($bus as $bu){ 
                                                    $gbu[$bu->name]=strtoupper($bu->name);
                                                
                                                }
                                                
                                                //$gbu=array("all"=>"All","GEM"=>"GEM","CHC"=>"CHC","AFRICASON"=>"AFRICASON");
                                                echo form_dropdown('gbu', $gbu,($_POST['gbu']), 'id="gbu" multiple="multiple" autocomplete="off" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("gbu") . '"  style="width:100%;" ');
                                                ?>
            </div></div>
            
             
                <div class="col-md-2">
    <div class="form-group">
                                                <?= lang("Cluster*", "cluster"); ?>
                                                <?php
                                                foreach ($clusters as $clust) {
                                                    $clusterdetails[$clust->name]=$clust->name;
                                                }
                                                
                                                echo form_dropdown('cluster[]',$clusterdetails,NULL, 'id="cluster" multiple="multiple" autocomplete="off" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("cluster") . '"  style="width:100%;" ');
                                                ?>
                                            </div>
        </div>
                <div class="col-md-2">
                     <?= lang("Country*", "country"); ?>
                    <div class="form-group" style="z-index:999">
                         <select id="f_country" name="f_country[]" id="f_country_label"  multiple="multiple"  class="form-control select input-xs">
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
            
          <div class="col-md-2" style="display:none">
                  <div class="form-group">
                                                <?= lang("Sanofi Customer", "customer"); ?>
                                                <?php
                                                $this->db->where(array("group_name"=>"customer"));
                                                $q = $this->db->get('companies');
                                                $customers["all"]="Select All";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->name){
                $customers[$row->id] = $row->name;
                }
                else{
                   $customers[$row->id] = $row->company;  
                }
            }
        }
                                                
                                                echo form_dropdown('customer[]',$customers,($post['customer']), 'id="customer" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' customer"  style="width:100%;" ');
                                                ?>
            </div>  
                    
                </div>
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
                                                
                                                echo form_dropdown('distributor[]',$distributors,($post['distributor']), 'id="distributor" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("distributor") . '"  style="width:100%;" ');
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
                           $currentmonths=array("01-".$twoyearsago=>"01-".$twoyearsago,"02-".$twoyearsago=>"02-".$twoyearsago,"03-".$twoyearsago=>"03-".$twoyearsago,"04-".$twoyearsago=>"04-".$twoyearsago,"05-".$twoyearsago=>"05-".$twoyearsago,"06-".$twoyearsago=>"06-".$twoyearsago,"07-".$twoyearsago=>"07-".$twoyearsago,"08-".$twoyearsago=>"08-".$twoyearsago,"09-".$twoyearsago=>"09-".$twoyearsago,"10-".$twoyearsago=>"10-".$twoyearsago,"11-".$twoyearsago=>"11-".$twoyearsago,"12-".$twoyearsago=>"12-".$twoyearsago,"01-".$oneyearago=>"01-".$oneyearago,"02-".$oneyearago=>"02-".$oneyearago,"03-".$oneyearago=>"03-".$oneyearago,"04-".$oneyearago=>"04-".$oneyearago,"05-".$oneyearago=>"05-".$oneyearago,"06-".$oneyearago=>"06-".$oneyearago,"07-".$oneyearago=>"07-".$oneyearago,"08-".$oneyearago=>"08-".$oneyearago,"09-".$oneyearago=>"09-".$oneyearago,"10-".$oneyearago=>"10-".$oneyearago,"11-".$oneyearago=>"11-".$oneyearago,"12-".$oneyearago=>"12-".$oneyearago,"01-".date("Y")=>"01-".date("Y"),"02-".date("Y")=>"02-".date("Y"),"03-".date("Y")=>"03-".date("Y"),"04-".date("Y")=>"04-".date("Y"),"05-".date("Y")=>"05-".date("Y"),"06-".date("Y")=>"06-".date("Y"),"07-".date("Y")=>"07-".date("Y"),"08-".date("Y")=>"08-".date("Y"),"09-".date("Y")=>"09-".date("Y"),"10-".date("Y")=>"10-".date("Y"),"11-".date("Y")=>"11-".date("Y"),"12-".date("Y")=>"12-".date("Y"));
                      echo form_dropdown('period[]',$currentmonths,($post['period']), 'id="period" multiple="multiple"  class="form-control input-tip select"  data-placeholder="' . lang("select") . ' period" style="width:100%;" ');?>   
                        
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
                                                
                                                echo form_dropdown('category[]',$data,($post['category']), 'id="category" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("category") . '"  style="width:100%;" ');
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
                                                
                                                echo form_dropdown('products[]',$products,($post['products']), 'id="prodcts" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("products") . '"  style="width:100%;" ');
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
                   <div class="col-md-2">
                      <label for="gsales" class="">Batches</label>
                <?php
                                                $batches=array("20"=>"First 20","40"=>"Next 20-40","60"=>"40-60","80"=>"60-80","100"=>"80-100");
                                                echo form_dropdown('batch', $batches,($_POST['batch']), 'id="batch"  autocomplete="on" class="form-control input-tip select"   style="width:100%;" ');
                                                ?>

                    </div>
                   
                   <div class="col-md-2">
                      
                
                <input id="gsales3" name="ssocountry" type="radio" class="" <?php  if($_POST["ssocountry"]=="ssocountry"){echo 'checked="checked"';}?> value="ssocountry" <?php echo $this->form_validation->set_radio('ssocountry', "ssocountry"); ?> />
                <label for="gsales" class="">SSO Country</label>
                <br>
                <input id="gsales4" name="ssocountry" type="radio" class="" <?php  if($_POST["ssocountry"]=="ssoproduct"){echo 'checked="checked"';}?> value="ssoproduct" <?php echo $this->form_validation->set_radio('ssocountry', "ssoproduct"); ?> />
                <label for="gsales" class="">SSO Product</label>
                <br>
                <!--<input id="gsales" name="ssocountry" type="radio" class=""  value="monthlytrend" <?php echo $this->form_validation->set_radio('ssocountry', "monthlytrend"); ?> />
                <label for="gsales" class="">SSO Monthly Trend</label>-->

                    </div>
                  
                    <!--<div class="col-md-2"><div class="form-group">
                        
                      <?php 
                      $pricetypes=array("unified"=>"Unified","resale"=>"Resale","supply"=>"Supply");
                      echo form_dropdown('price_type',$pricetypes,($post['price_type']), 'id="price_type"  class="form-control input-tip select"  data-placeholder="' . lang("select") . ' price_type" style="width:100%;" ');?>   
                        
                    </div></div>-->
    <div class="col-md-2"><div class="form-group" style="padding-top:30px"><input type="submit" name="filter" value="Filter" class="btn btn-primary">&nbsp;<input type="button" id="reset" value="Reset" class="btn btn-info"></div>
                </div>
<?php echo form_close(); ?>
    

</div>
  <?php $yearonly=  substr($selectedyear,-2); 
  if(!$currentmonth){$currentmonth="Month";}
?>
  


  <div class="row">
      <div style="overflow-y:hidden;overflow-x:scroll;max-height:400px !important">
      <div class="col-md-12" >
          <?php if($table=="ssocountry") {?>
          <br>
          <h4>SSO Country <a href="#" class="exporttable pull-right">Export As Xls</a></h4>
          <div id="exceltable">
         <table class="SLData table table-bordered table-hover table-striped order-column display">
                        <thead>
                            <tr><td colspan="8"><span style="font-weight:bold"><?=$currentmonth?></span></td><td colspan="7"><span style="font-weight:bold">YTD<?=$currentmonth?></span></td> <td colspan="7"><span style="font-weight:bold">YTG<?=$currentmonth?></span></td><td colspan="2" width="100px"><span style="font-weight:bold">FULL YEAR</span></td><td colspan="2"><span style="font-weight:bold">YTD AVG</span></td><td colspan="6"><span style="font-weight:bold">YTG-MONTHLY AVERAGES</span></td></tr>
                        <tr>
                            <th class="headcol"><?php echo $this->lang->line("Country"); ?></th>
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("B".($yearonly)); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/ A'.($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/ B'.$yearonly); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                            
                            
                            <!--ytd-->
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                          <th><?php echo $this->lang->line("B".$yearonly); ?></th>
                           <th><?php echo $this->lang->line("A".$yearonly); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/A'.($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/ B'.$yearonly); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                             <!--ytg only -->
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                           <th><?php echo $this->lang->line("B".$yearonly); ?></th>
                           <th><?php echo $this->lang->line("A".$yearonly); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/A'.($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/ B'.$yearonly); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                               <!--full year only -->
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                           <th><?php echo $this->lang->line("B".$yearonly); ?></th>
                         
                               <!--ytd avg only -->
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                                   <th><?php echo $this->lang->line("A".$yearonly); ?></th>
                                   <!--ytg avg only -->
                            <th><?php echo $this->lang->line("YTG".($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("YTG".($yearonly)); ?></th>
                            <th><?php echo $this->lang->line("YTG A".($yearonly-1)."/YTD A".($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                          <th><?php echo $this->lang->line("YTG A".($yearonly)."/ YTD A".($yearonly)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                       
                           <?php
                           $alldataa=array();
                           foreach ($countries as $ctry){ 
                               
                               ?>
<!--  
[{
        "period":"N-1",
        "ET": 2.5,
        "KE": 2.5,
        "TZ": 2.1,
        "UG": 0.3,
        "MG": 0.2
    }, {
        "period": "Forecast",
         "ET": 2.5,
        "KE": 2.4,
        "TZ": 2.6,
        "UG": 0.7,
        "MG": 0.6
    }, {
        "period":"YTG-Feb",
                    "ET": 2.5,
                   "KE": 2.5,
                   "TZ": 2.1,
                   "UG": 0.3,
                   "MG": 0.2
    },{
        "period":"YTD-AVG",
        "ET": 2.5,
        "KE": 2.5,
        "TZ": 2.1,
        "UG": 0.3,
        "MG": 0.2
    }
    
    ]
-->


                            <tr>
                                  <td class="headcol"><?=$ctry["name"]?>  </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["lastyearsalesmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudgetmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearsalesmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["thisyearsalesmonth"]-$ctry["lastyearsalesmonth"],2))?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"> <?=markRed(round(((($ctry["thisyearsalesmonth"]-$ctry["lastyearsalesmonth"])/$ctry["lastyearsalesmonth"])*100),2))?>%</td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"> <?=markRed(round($ctry["thisyearsalesmonth"]-$ctry["thisyearbudgetmonth"],2))?></td>
                                 <td  class="center"  title="<?=$ctry["name"]?>">
                                 <?=markRed(round((($ctry["thisyearsalesmonth"]-$ctry["thisyearbudgetmonth"])/$ctry["thisyearbudgetmonth"])*100,2))?>%
                                 </td>
                                 
                                  <!--ytd-->
                                  <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsaleslastyear"],2))?> </td>      
                                  <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["budgetytd"])?>  </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["ytdsalesthisyear"])?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]-$ctry["ytdsaleslastyear"],2))?> </td>
                                   <td class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(((($ctry["ytdsalesthisyear"]-$ctry["ytdsaleslastyear"])/$ctry["ytdsaleslastyear"])*100),2))?>% </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]-$ctry["budgetytd"],2))?> </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?= markRed(round((($ctry["ytdsalesthisyear"]-$ctry["budgetytd"])/$ctry["budgetytd"])*100,2))?> %</td>
                                    <!--ytg-->
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=abs(round($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"],2))?> </td>
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"]-$ctry["budgetytd"])?>  </td> 
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])?> </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgcomparison=round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]),2);
                                   echo markRed($ytgcomparison);
                                   ?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((($ytgcomparison/round($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]))*100),2))?>%</td>
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["thisyearbudget"]-$ctry["budgetytd"]),2))?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(((($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["thisyearbudget"]-$ctry["budgetytd"]))/($ctry["thisyearbudget"]-$ctry["budgetytd"]))*100,2))?> %</td>
                                    
                                    <!--full year-->
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["lastyearsales"])?> </td>
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"])?>  </td>
                                     
                                     <!--YTD monthly avg-->
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsaleslastyear"]/(12-$remainingmonths),2))?> </td>
                                      <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]/(12-$remainingmonths),2))?> </td>
                                      <!--monthly avg ytg-->
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytgsaleslastyear"]/$remainingmonths,2))?> </td>
                                      <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytgsalesthisyear"]/$remainingmonths,2))?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgdiff=($ctry["ytgsaleslastyear"]/$remainingmonths)-($ctry["ytdsaleslastyear"]/(12-$remainingmonths));
                                    echo markRed($ytgdiff)?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(($ytgdiff/($ctry["ytdsaleslastyear"]/(12-$remainingmonths))*100))?> %</td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgdiffthsyear=(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])/$remainingmonths)-($ctry["ytdsalesthisyear"]/(12-$remainingmonths));
                                    echo markRed($ytgdiffthsyear)?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((round($ytgdiffthsyear)/round($ctry["ytdsalesthisyear"]/(12-$remainingmonths))*100)))?> %</td>
                                    
                                    <!--full year-->
                                   
                                    
                           
                            </tr>
                           <?php  }
?>                        
                        </tbody>
                        <tfoot id="434">
		<tr>
                    <td>Total</td>
                        
			<?php 
                        for($i=1;$i<=31;$i++){
                           echo "<td class='prd".$i."'></td>";
                        }
                        
                        ?>
		</tr>
	</tfoot> 
                    </table>
          
          </div>
          <?php } else if($table=="ssoproduct"){
              
            //die(print_r($countries));  
              ?>
          <br>
           <h4>SSO Product <a href="#" class="exporttable2 pull-right">Export As Xls</a></h4>
            <div id="exceltable2">
            <table   class="SLData table table-bordered table-hover table-striped">
                        <thead>
                            <tr><td colspan="8"><span style="font-weight:bold"><?=$currentmonth?></span></td><td colspan="7"><span style="font-weight:bold">YTD<?=$currentmonth?></span></td> <td colspan="7"><span style="font-weight:bold">YTG<?=$currentmonth?></span></td><td colspan="2" width="100px"><span style="font-weight:bold">FULL YEAR</span></td><td colspan="2"><span style="font-weight:bold">YTD AVG</span></td><td colspan="6"><span style="font-weight:bold">YTG-MONTHLY AVERAGES</span></td></tr>
                        <tr>
                          <th class="headcol"><?php echo $this->lang->line("Brand"); ?></th>
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("B".($yearonly)); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/ A'.($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/ B'.$yearonly); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                            
                            
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                          <th><?php echo $this->lang->line("B".$yearonly); ?></th>
                           <th><?php echo $this->lang->line("A".$yearonly); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/A'.($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/ B'.$yearonly); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                             <!--ytg only -->
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                           <th><?php echo $this->lang->line("B".$yearonly); ?></th>
                           <th><?php echo $this->lang->line("A".$yearonly); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/'.($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                            <th><?php echo $this->lang->line("A".$yearonly.'/ B'.$yearonly); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                               <!--full year only -->
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                           <th><?php echo $this->lang->line("B".$yearonly); ?></th>
                         
                               <!--ytd avg only -->
                            <th><?php echo $this->lang->line("A".($yearonly-1)); ?></th>
                                   <th><?php echo $this->lang->line("A".$yearonly); ?></th>
                                   <!--ytg avg only -->
                            <th><?php echo $this->lang->line("YTG".($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("YTG".($yearonly)); ?></th>
                            <th><?php echo $this->lang->line("YTG A".($yearonly-1)."/YTD A".($yearonly-1)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                          <th><?php echo $this->lang->line("YTG A".($yearonly)."/ YTD A".($yearonly)); ?></th>
                            <th><?php echo $this->lang->line("%"); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                       
                           <?php foreach ($countries as $ctry){
                               //$promotedcategory=$this->settings_model->getPromotedProductCategory($ctry["id"],$_POST["promotion"]);
                               //&& $ctry["lastyearsales"]  && $ctry["ytdsalesthisyear"]
                                if($ctry["name"]  ){ //show only selected brands and promotion category
                               ?>
                          <tr>
                                  <td class="headcol"><?=$ctry["name"]?>  </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["lastyearsalesmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudgetmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearsalesmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["thisyearsalesmonth"]-$ctry["lastyearsalesmonth"],2))?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"> <?=markRed(round(((($ctry["thisyearsalesmonth"]-$ctry["lastyearsalesmonth"])/$ctry["lastyearsalesmonth"])*100),2))?>%</td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"> <?=markRed(round($ctry["thisyearsalesmonth"]-$ctry["thisyearbudgetmonth"],2))?></td>
                                 <td  class="center"  title="<?=$ctry["name"]?>">
                                 <?=markRed(round((($ctry["thisyearsalesmonth"]-$ctry["thisyearbudgetmonth"])/$ctry["thisyearbudgetmonth"])*100,2))?>%
                                 </td>
                                 
                                  <!--ytd-->
                                  <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsaleslastyear"],2))?> </td>      
                                  <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["budgetytd"])?>  </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["ytdsalesthisyear"])?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]-$ctry["ytdsaleslastyear"],2))?> </td>
                                   <td class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(((($ctry["ytdsalesthisyear"]-$ctry["ytdsaleslastyear"])/$ctry["ytdsaleslastyear"])*100),2))?>% </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]-$ctry["budgetytd"],2))?> </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?= markRed(round((($ctry["ytdsalesthisyear"]-$ctry["budgetytd"])/$ctry["budgetytd"])*100,2))?> %</td>
                                    <!--ytg-->
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=abs(round($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"],2))?> </td>
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"]-$ctry["budgetytd"])?>  </td> 
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])?> </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgcomparison=round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]),2);
                                   echo markRed($ytgcomparison);
                                   ?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((($ytgcomparison/round($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]))*100),2))?>%</td>
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["thisyearbudget"]-$ctry["budgetytd"]),2))?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(((($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["thisyearbudget"]-$ctry["budgetytd"]))/($ctry["thisyearbudget"]-$ctry["budgetytd"]))*100,2))?> %</td>
                                    
                                    <!--full year-->
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["lastyearsales"])?> </td>
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"])?>  </td>
                                     
                                     <!--YTD monthly avg-->
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsaleslastyear"]/(12-$remainingmonths),2))?> </td>
                                      <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]/(12-$remainingmonths),2))?> </td>
                                      <!--monthly avg ytg-->
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytgsaleslastyear"]/$remainingmonths,2))?> </td>
                                      <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytgsalesthisyear"]/$remainingmonths,2))?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgdiff=($ctry["ytgsaleslastyear"]/$remainingmonths)-($ctry["ytdsaleslastyear"]/(12-$remainingmonths));
                                    echo markRed($ytgdiff)?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(($ytgdiff/($ctry["ytdsaleslastyear"]/(12-$remainingmonths))*100))?> %</td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgdiffthsyear=(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])/$remainingmonths)-($ctry["ytdsalesthisyear"]/(12-$remainingmonths));
                                    echo markRed($ytgdiffthsyear)?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((round($ytgdiffthsyear)/round($ctry["ytdsalesthisyear"]/(12-$remainingmonths))*100)))?> %</td>
                                    
                                    <!--full year-->
                                   
                                    
                           
                            </tr>
                           <?php  }
                           }
?>                        
                        </tbody>
                             <tfoot>
		<tr>
			<th>Totals</th>
                        
			<?php 
                       for($i=1;$i<=31;$i++){
                           echo "<td class='prd".$i."'></td>";
                        }
                        
                        ?>
		</tr>
	</tfoot>
                    </table>
           
           </div>
           
           
           
           
          <?php }
          
        
          
          
          ?>
      </div>
  </div>
  </div>


  <div class="row">
    <div class="col-md-6" style="overflow-x:scroll;overflow-y:no-scroll;">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                                 Periodic Sales:K€ 
               <div id="chartdivstacked" style="width:content-box;height:300px;overflow-y:scroll;background-color:#fff"> <!--siperiodic-->
                
                       
               </div></div>
    </div>
        
        
    
    
</div>
    
      <div class="col-md-6" style="overflow-x:scroll;max-width:1500px">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                              YTD Sales:K€ 
               <div id="chartdivstackedytd" style="width:content-box;height:300px;overflow-y:scroll;background-color:#fff"> <!--ytdsales-->
                
                       
               </div></div>
    </div>
    <!--<div class="col-md-6" style="overflow-x:scroll;overflow-y:no-scroll;">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                                 Periodic Sales Variance:K€ 
               <div id="siperiodicvariance" style="width:container;height:400px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>-->
    
    
</div>
  </div>
  
<!--<div class="row">
    
    
    

   <!-- <div class="col-md-6" style="overflow-x:scroll;max-width:1500px">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                              YTD Sales Variance:K€ 
               <div id="psoytdvariance" style="width:content-box;height:400px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>
    
    
    
</div>-->
  
  
  <div class="row">
      
      
    <div class="col-md-6" style="overflow-x:scroll;max-width:1500px">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                              YTG Sales:K€ 
               <div id="chartdivstackedytg" style="width:content-box;height:300px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>
  <div class="col-md-6" style="overflow-x:scroll;">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                          YTD Sales (Full Year):K€ 
               <div id="chartdivstackedfullyear" style="width:content-box;height:300px;background-color:#fff">
                
                       
               </div>
                   
                                              
                                              
                                             </div>
              
               <div  style="text-align:center">
                                            YTD Average:K€
               <div id="ytgmonthlyavg" style="width:content-box;height:300px;background-color:#fff">
                
                       
               </div>
    </div>     
                                              
                                              
                                             </div>
              
    </div>
    
    
</div>
    
    
    
</div>
   <div class="row">
<?php if($_POST["ssocountry"]=="ssocountry"){ ?>

    <div class="col-md-12" style="overflow-x:scroll;overflow-y:no-scroll;max-width:2000px">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                                 Monthly Sales Trend:K€ 
               <div id="sipercountry" style="width:content-box;height:700px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>
    
    
    
    

      
<?php }?>
       
       
      
  
  <?php 
  function getDataValueFromCountry($countries,$ctrynameindex,$dataname){
      return round($countries[$ctrynameindex][$dataname],2);
  }
  
  
  $stackdata=array(); $stackdataytg=array();
  $periods=array("lastyearsalesmonth"=>"N-1","thisyearsalesmonth"=>"Actual","thisyearbudgetmonth"=>"Budget");
$ytdperiods=array("ytdsaleslastyear"=>"N-1","ytdsalesthisyear"=>"Actual","budgetytd"=>"Budget");
$ytgperiods=array("lastyearsales"=>"N-1","ytdsalesthisyear"=>"Actual","thisyearbudget"=>"Budget");
 $fyperiods=array("lastyearsales"=>"N-1","thisyearbudget"=>"Actual");
$ytdavgperiods=array("ytdsaleslastyear"=>"N-1","ytdsalesthisyear"=>"Actual");

//ytd
  foreach($periods as $key=>$period){
      $graph["period"]=$period;
      foreach($countrybrandnames as $country){
          //array index corresponds to data of that country in countries array
        
         $indexofarray= array_search($country,$countrybrandnames);
        $graph[$country]=getDataValueFromCountry($countries,$indexofarray,$key);  
      }
      array_push($stackdata,$graph);
      
  }
  $stackdataytd=array();
  foreach($ytdperiods as $key=>$period){
      $graph["period"]=$period;
      foreach($countrybrandnames as $country){
          //array index corresponds to data of that country in countries array
         $indexofarray= array_search($country,$countrybrandnames);
        $graph[$country]=getDataValueFromCountry($countries,$indexofarray,$key);  
      }
      array_push($stackdataytd,$graph);
      
  }
//ytg
  foreach($ytgperiods as $key=>$period){
       $graph["period"]=$period;
      foreach($countrybrandnames as $country){
          //array index corresponds to data of that country in countries array
         $indexofarray= array_search($country,$countrybrandnames);
        if($period=="N-1"){
            $graph[$country]=(getDataValueFromCountry($countries,$indexofarray,"lastyearsales")-getDataValueFromCountry($countries,$indexofarray,"ytdsaleslastyear"));
        }
       else if($period=="Budget"){
            $graph[$country]=(getDataValueFromCountry($countries,$indexofarray,"thisyearbudget")-getDataValueFromCountry($countries,$indexofarray,"budgetytd"));
        }
        else if($period=="Actual"){
            $graph[$country]=(getDataValueFromCountry($countries,$indexofarray,"thisyearbudget")-getDataValueFromCountry($countries,$indexofarray,"ytdsalesthisyear"));
        }
        else{
           // $graph["period"]=null;
        $graph[$country]=getDataValueFromCountry($countries,$indexofarray,$key);  
        }
      }
      
      array_push($stackdataytg,$graph);
      
  }
  
  //full year
  $stackdatafullyear=array();
  foreach($fyperiods as $key=>$period){
       $graph["period"]=$period;
       foreach($countrybrandnames as $country){
            $indexofarray= array_search($country,$countrybrandnames);
        $graph[$country]=getDataValueFromCountry($countries,$indexofarray,$key);  
           
       }
       array_push($stackdatafullyear,$graph);
  }
  //ytd  avg
    $stackdataytdavg=array();
  foreach($ytdavgperiods as $key=>$period){
       $graph["period"]=$period;
      foreach($countrybrandnames as $country){
          //array index corresponds to data of that country in countries array
         $indexofarray= array_search($country,$countrybrandnames);
        
            $graph[$country]=round(getDataValueFromCountry($countries,$indexofarray,$key)/(12-$remainingmonths),2);
        
      }
       array_push($stackdataytdavg,$graph);
  }
  
  
  $allgraphfields=array();
  foreach($countrybrandnames as $ct){
     if($ct){
        $graphfields["balloonText"]= "<b>[[title]]</b><br><span style='font-size:10px'>[[category]]: <b>[[value]]</b></span>";
        $graphfields["fillAlphas"]= 0.8;
        $graphfields["labelText"]= "[[value]]";
        $graphfields["lineAlpha"]= 0.2;
        $graphfields["title"]= $ct;
        $graphfields["type"]="column";
   $graphfields["color"]="#000000";
       $graphfields["valueField"]= $ct;
       array_push($allgraphfields,$graphfields);
     }
  }
//  print_r($countrybrandnames);
  
  //    echo json_encode($allgraphfields);
  
  
  
  ?>
  
  





    <style type="text/css" media="screen">
        .tooltip-inner {
            max-width: 500px;
        }
    </style>
    <script src="<?= $assets; ?>js/hc/highcharts.js"></script>
    <script type="text/javascript">
        $(function () {
            Highcharts.getOptions().colors = Highcharts.map(Highcharts.getOptions().colors, function (color) {
                return {
                    radialGradient: {cx: 0.5, cy: 0.3, r: 0.7},
                    stops: [[0, color], [1, Highcharts.Color(color).brighten(-0.3).get('rgb')]]
                };
            });
            $('#ov-chart').highcharts({
                chart: {},
                credits: {enabled: false},
                title: {text: ''},
                xAxis: {categories: <?= json_encode($currentmonths); ?>},
                yAxis: {min: 0, title: ""},
                tooltip: {
                    shared: true,
                    followPointer: true,
                    formatter: function () {
                        if (this.key) {
                            return '<div class="tooltip-inner hc-tip" style="margin-bottom:0;">' + this.key + '<br><strong>' + currencyFormat(this.y) + '</strong> (' + formatNumber(this.percentage) + '%)';
                        } else {
                            var s = '<div class="well well-sm hc-tip" style="margin-bottom:0;"><h2 style="margin-top:0;">' + this.x + '</h2><table class="table table-striped"  style="margin-bottom:0;">';
                            $.each(this.points, function () {
                                s += '<tr><td style="color:{series.color};padding:0">' + this.series.name + ': </td><td style="color:{series.color};padding:0;text-align:right;"> <b>' +
                                currencyFormat(this.y) + '</b></td></tr>';
                            });
                            s += '</table></div>';
                            return s;
                        }
                    },
                    useHTML: true, borderWidth: 0, shadow: false, valueDecimals: site.settings.decimals,
                    style: {fontSize: '14px', padding: '0', color: '#000000'}
                },
                series: [ 
                    {
                        type: 'column',
                        name: '<?= lang("sales"); ?>',
                        data: [<?php
                    echo implode(', ', $msales);
                    ?>]
                    }, {
                        type: 'spline',
                        name: '<?= lang("purchases"); ?>',
                        data: [<?php
                    echo implode(', ', $mpurchases);
                    ?>],
                        marker: {
                            lineWidth: 2,
                            states: {
                                hover: {
                                    lineWidth: 4
                                }
                            },
                            lineColor: Highcharts.getOptions().colors[3],
                            fillColor: 'white'
                        }
                    }, {
                        type: 'pie',
                        name: '<?= lang("stock_value"); ?>',
                        data: [
                            ['', 0],
                            ['', 0],
                            ['<?= lang("stock_value_by_price"); ?>', <?php echo $stock->stock_by_price; ?>],
                            ['<?= lang("stock_value_by_cost"); ?>', <?php echo $stock->stock_by_cost; ?>],
                        ],
                        center: [80, 42],
                        size: 80,
                        showInLegend: false,
                        dataLabels: {
                            enabled: false
                        }
                    }]
            });
        });
    </script>

    <script type="text/javascript">
        $(function () {
            $('#lmbschart').highcharts({
                chart: {type: 'column'},
                title: {text: ''},
                credits: {enabled: false},
                xAxis: {type: 'category', labels: {rotation: -60, style: {fontSize: '13px'}}},
                yAxis: {min: 0, title: {text: ''}},
                legend: {enabled: false},
                series: [{
                    name: '<?=lang('sold');?>',
                    data: [<?php
                    foreach ($lmbs as $r) {
                        if($r->SoldQty > 0) {
                            echo "['".$r->name."', ".$r->SoldQty."],";
                        }
                    }
                    ?>],
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        color: '#000',
                        align: 'right',
                        y: -25,
                        style: {fontSize: '12px'}
                    }
                }]
            });
            $('#bschart').highcharts({
                chart: {type: 'column'},
                title: {text: ''},
                credits: {enabled: false},
                xAxis: {type: 'category', labels: {rotation: -60, style: {fontSize: '13px'}}},
                yAxis: {min: 0, title: {text: ''}},
                legend: {enabled: false},
                series: [{
                    name: '<?=lang('sold');?>',
                    data: [<?php
                foreach ($bs as $r) {
                    if($r->SoldQty > 0) {
                        echo "['".$r->name."', ".$r->SoldQty."],";
                    }
                }
                ?>],
                    dataLabels: {
                        enabled: true,
                        rotation: -90,
                        color: '#000',
                        align: 'right',
                        y: -25,
                        style: {fontSize: '12px'}
                    }
                }]
            });

        });
    </script>

      
    <input type="hidden" id="customersales" value="<?=json_encode($allsaless)?>">


 
 <script id="_webengage_script_tag" type="text/javascript">
 $(document).ready(function(e){

  // console.log(<?=$siperiodic?>);

    /***********periodic sales SI*********/
    
     var chart = AmCharts.makeChart("siperiodic", { 
    "theme": "none",
    "type": "serial",
      "columnSpacing":2,
    "dataProvider":[<?=$siperiodic?>],
    "valueAxes": [{
      //  "stackType": "1d",
       // "unit": "%",
        "position": "left",
        "title": "SSO Sales"
    }],
    "startDuration":0,
    
    "graphs": [ 
{
      "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "N-1",
        "type": "column",
        "valueField": "ActualLast",
        "color":"#4287f5"
    },
    {
        "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title":"Budget",
        "type": "column",
        "valueField": "Budget",
         "color":"#f59042"
    },{
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Forecast 1",
        "type": "column",
        "valueField": "Forecast",
        "color":"#429ef5"
    },
    {
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Forecast 2",
        "type": "column",
        "valueField": "Forecast2",
        "color":"#4e42f5"
    },
    {
        "labelText": "[[value]]",
        "fillAlphas":0.9,
        "lineAlpha":0.2,
        "title": "Actual",
        "type": "column",
        "valueField":"Actual",
        "color":"#a742f5"
    }],
    "plotAreaFillAlphas": 0.1,
  //  "depth3D": 60,
    //"angle": 60,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation":0,
    },
    "legend": {
    "useGraphSettings": true
  },
    "export": {
    	"enabled": true
     }
});
jQuery('.chart-input').off().on('input change',function() {
  var property	= jQuery(this).data('property');
  var target		= chart;
  chart.startDuration = 0;

  if ( property == 'topRadius') {
    target = chart.graphs[0];
      	if ( this.value == 0 ) {
          this.value = undefined;
      	}
  }

  target[property] = this.value;
  chart.validateNow();
});
/**********************variance**************/
console.log(<?=$psoperiodicvariance?>);
   var chartv = AmCharts.makeChart("siperiodicvariance", {
    "theme": "none",
    "type": "serial",
      "columnSpacing":2,
    "dataProvider":[<?=$psoperiodicvariance?>],
    "valueAxes": [{
      //  "stackType": "1d",
       // "unit": "%",
       
       // "position": "left",
        "title": "SSO Sales Variance"
    }],
    "startDuration": 1,
    "graphs": [{
        "labelText": "[[value]]",
        "fillAlphas":0.9,
        "lineAlpha":0.2,
        "title": "Vs N-1",
        "type": "column",
        "valueField":"vsN1",
        "color":"#4287f5"
    }, 
    {
        "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title":"Vs Budget",
        "type": "column",
        "valueField": "vsBudget",
        "color":"#4287f5"
    },{
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Vs Forecast",
        "type": "column",
        "valueField": "vsForecast",
        "color":"#4287f5"
    },
    {
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Vs Forecast2",
        "type": "column",
        "valueField": "vsForecast2",
        "color":"#4287f5"
    }
    ],
        "rotate":true,
    "plotAreaFillAlphas": 0.1,
  //  "depth3D": 60,
    //"angle": 60,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation":0,
    },
    "legend": {
    "useGraphSettings": true
  },
    "export": {
    	"enabled": true
     }
});
jQuery('.chart-input').off().on('input change',function() {
  var property	= jQuery(this).data('property');
  var target		= chartv;
  chartv.startDuration = 0;

  if ( property == 'topRadius') {
    target = chartv.graphs[0];
      	if ( this.value == 0 ) {
          this.value = undefined;
      	}
  }

  target[property] = this.value;
  chartv.validateNow();
});

/*****************ytdaverage*************/
var chartavg = AmCharts.makeChart("ytdaverage", {
    "theme": "none",
    "type": "serial",
      "columnSpacing":2,
    "dataProvider":[<?=$ytdaverage?>],
    "valueAxes": [{
      //  "stackType": "1d",
       // "unit": "%",
        "position": "left",
        "title": "YTD Sales Average"
    }],
    "startDuration": 0,
    "graphs": [ 
 {
      "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "N-1",
        "type": "column",
        "valueField": "ActualLast"
    },
    {
        "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title":"Budget",
        "type": "column",
        "valueField": "Budget"
    },{
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Forecast",
        "type": "column",
        "valueField": "Forecast"
    }, {
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Forecast 2",
        "type": "column",
        "valueField": "Forecast2"
    },{
        "labelText": "[[value]]",
        "fillAlphas":0.9,
        "lineAlpha":0.2,
        "title": "Actual",
        "type": "column",
        "valueField":"Actual"
    }],
    "plotAreaFillAlphas": 0.1,
  //  "depth3D": 60,
    //"angle": 60,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation":0,
    },
    "legend": {
    "useGraphSettings": true
  },
    "export": {
    	"enabled": true
     }
});
jQuery('.chart-input').off().on('input change',function() {
  var property	= jQuery(this).data('property');
  var target		= chartavg;
  chartavg.startDuration = 0;

  if ( property == 'topRadius') {
    target = chartavg.graphs[0];
      	if ( this.value == 0 ) {
          this.value = undefined;
      	}
  }

  target[property] = this.value;
  chartavg.validateNow();
});



/******8ytd avg variance ytdsalesavgvariance*************/
var chartv = AmCharts.makeChart("ytdsalesavgvariance", {
    "theme": "none",
    "type": "serial",
      "columnSpacing":2,
    "dataProvider":[<?=$ytdaveragevariance?>],
    "valueAxes": [{
      //  "stackType": "1d",
       // "unit": "%",
       
       // "position": "left",
       // "title": "PSO Sales Avg Variance YTD"
    }],
    "startDuration": 1,
    "graphs": [{
        "labelText": "[[value]]",
        "fillAlphas":0.9,
        "lineAlpha":0.2,
        "title": "Vs N-1",
        "type": "column",
        "valueField":"vsN1",
        "color":"#4287f5"
    }, 
    {
        "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title":"Vs Budget",
        "type": "column",
        "valueField": "vsBudget",
        "color":"#4287f5"
    },{
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Vs Forecast",
        "type": "column",
        "valueField": "vsForecast"
    },
    {
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Vs Forecast 2",
        "type": "column",
        "valueField": "vsForecast2"
    }
    ],
        "rotate":true,
    "plotAreaFillAlphas": 0.1,
  //  "depth3D": 60,
    //"angle": 60,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation":0,
    },
    "legend": {
    "useGraphSettings": true
  },
    "export": {
    	"enabled": true
     }
});

var chart = AmCharts.makeChart("ytgmonthlyavg", {
    "type": "serial",
  "theme": "none",
    "legend": {
        "horizontalGap": 10,
        "fontSize":8,
        "verticalGap":0,
        "maxColumns": 1,
        "position": "right",
    "useGraphSettings": true,
    "markerSize": 10
    },
    "dataProvider":<?=json_encode($stackdataytdavg)?>,
    "valueAxes": [{
        "stackType": "regular",
        "axisAlpha": 0.3,
        "gridAlpha": 0
    }],
    "graphs":<?=json_encode($allgraphfields)?>,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "gridAlpha": 0,
        "position": "left"
    },
    "export": {
    	"enabled": true
     }

});


  /******************ytd sales&variance*****************/
 var chart = AmCharts.makeChart("ytdsales", {
    "theme": "none",
    "type": "serial",
      "columnSpacing":2,
    "dataProvider":[<?=$ytdsales?>],
    "valueAxes": [{
      //  "stackType": "1d",
       // "unit": "%",
        "position": "left",
        "title": "SSO Sales(YTD)"
    }],
    "startDuration": 1,
    "graphs": [ {
      "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "N-1",
        "type": "column",
        "valueField": "ActualLast"
    }, 
    {
        "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title":"Budget",
        "type": "column",
        "valueField": "Budget"
       
    },
//    },{
//       "labelText": "[[value]]",
//        "fillAlphas": 0.9,
//        "lineAlpha": 0.2,
//        "title": "Forecast",
//        "type": "column",
//        "valueField": "Forecast"
//    },
    {
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Forecast 2",
        "type": "column",
        "valueField": "Forecast2"
    },    {
      "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Actual",
        "type": "column",
        "valueField": "Actual"
    }],
    "plotAreaFillAlphas": 0.1,
  //  "depth3D": 60,
    //"angle": 60,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation":0,
    },
    "legend": {
    "useGraphSettings": true
  },
    "export": {
    	"enabled": true
     }
});

/**********variance**************/
var chartv = AmCharts.makeChart("psoytdvariance", {
    "theme": "none",
    "type": "serial",
      "columnSpacing":2,
    "dataProvider":[<?=$ytdvariance?>],
    "valueAxes": [{
      //  "stackType": "1d",
       // "unit": "%",
       
       // "position": "left",
        "title": "SSO Sales Variance YTD"
    }],
    "startDuration": 1,
    "graphs": [{
        "labelText": "[[value]]",
        "fillAlphas":0.9,
        "lineAlpha":0.2,
        "title": "Vs N-1",
        "type": "column",
        "valueField":"vsN1"
    }, 
    {
        "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title":"Vs Budget",
        "type": "column",
        "valueField": "vsBudget"
    },{
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Vs Forecast",
        "type": "column",
        "valueField": "vsForecast"
    },
    {
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Vs Forecast2",
        "type": "column",
        "valueField": "vsForecast2"
    }],
        "rotate":true,
    "plotAreaFillAlphas": 0.1,
  //  "depth3D": 60,
    //"angle": 60,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation":0,
    },
    "legend": {
    "useGraphSettings": true
  },
    "export": {
    	"enabled": true
     }
});

    

/****************8SI per country************/
console.log(<?=$consolidatedsi?>);
   var chart = AmCharts.makeChart("sipercountry", {
    "theme": "none",
    "type": "serial",
      //"columnSpacing":1,
    "dataProvider":<?=$consolidatedsi?>,
    "valueAxes": [{
             "gridThickness": 0,
      //  "stackType": "1d",
       // "unit": "%",
        "position": "left",
        "title": "SSO Comparison"
    }],
    "startDuration":0,
    
    "graphs": [
// {
//     // "labelText": "[[value]]",
//    "bullet": "",
//    "lineThickness":1,
//    "bulletSize": 7,
//    "bulletBorderAlpha": 1,
//    "bulletColor": "#FFFFFF",
//    "useLineColorForBulletBorder": true,
//    "bulletBorderThickness": 3,
//    "fillAlphas": 0,
//        "title": "Actual Trend",
//        
//        "valueField": "ActualTrend"
//    },
//    {
//      //"labelText": "[[value]]",
//    "bullet": "",
//    "lineThickness":1,
//    "bulletSize": 7,
//    "bulletBorderAlpha": 1,
//    "bulletColor": "#FFFFFF",
//    "useLineColorForBulletBorder": true,
//    "bulletBorderThickness": 3,
//    "fillAlphas": 0,
//        "title": "Budget Trend",
//        
//        "valueField": "BudgetTrend"
//    },
        {        "lineAlpha": 0.2,

      //  "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "title":"N-1",
        "type": "column",
        "valueField": "ActualLast",
        "fixedColumnWidth": 15
    }
       , 
    {        "lineAlpha": 0.2,

       // "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "title":"Budget",
        "type": "column",
        "valueField": "Budget",
        "fixedColumnWidth": 15
    },
     /*{        "lineAlpha": 0.2,

        "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "title":"Forecast",
        "type": "column",
        "valueField": "Forecast",
        "fixedColumnWidth": 10
    },
   {
       "labelText": "[[value]]",
       "bullet": "",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
        "title": "Forecast",
     
        "valueField": "Forecast"
    },{
       "labelText": "[[value]]",
       "bullet": "",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
        "title": "Forecast 2",
     
        "valueField": "Forecast2"
    },
    {
        "labelText": "[[value]]",
        "bullet": "",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
        "title": "Actual",
               "valueField":"Actual"
    }   */
    {        "lineAlpha": 0.2,

       // "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "title":"Actual",
        "type": "column",
        "valueField": "Actual",
        "fixedColumnWidth": 15
    },
    
    ],
    "plotAreaFillAlphas": 0.1,
  //  "depth3D": 60,
    //"angle": 60,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "labelRotation":60,
        "gridThickness": 0
    },
    "legend": {
    "useGraphSettings": true
  },
    "export": {
    	"enabled": true
     }
});
    
/***********stcked*******************
 * 
 * @type @exp;AmCharts@call;makeChart|@exp;AmCharts@call;makeChart|@exp;AmCharts@call;makeChart|@exp;AmCharts@call;makeChartbar
 */   
  var chart = AmCharts.makeChart("chartdivstacked", {
    "type": "serial",
  "theme": "none",
    "legend": {
        "horizontalGap": 10,
        "verticalGap":0,
        "fontSize":8,
        "maxColumns": 2,
        "position": "right",
    "useGraphSettings": true,
    "markerSize": 10
    },
    "dataProvider":<?=json_encode($stackdata)?>,
    "valueAxes": [{
        "stackType": "regular",
        "axisAlpha": 0.3,
        "gridAlpha": 0
    }],
    "graphs":<?=json_encode($allgraphfields)?>,
    "colors":["#FF6600", "#FCD202", "#B0DE09", "#0D8ECF", "#2A0CD0", "#CD0D74", "#CC0000", "#00CC00", "#0000CC", "#DDDDDD", "#999999", "#333333", "#990000"],
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "gridAlpha": 0,
        "position": "left"
    },
    "export": {
    	"enabled": true
     }

});

    //ytd
    var chart = AmCharts.makeChart("chartdivstackedytd", {
    "type": "serial",
  "theme": "none",
    "legend": {
        "horizontalGap": 10,
        "verticalGap":0,
        "fontSize":8,
        "maxColumns": 2,
        "position": "right",
    "useGraphSettings": true,
    "markerSize": 10
    },
    "dataProvider":<?=json_encode($stackdataytd)?>,
    "valueAxes": [{
        "stackType": "regular",
        "axisAlpha": 0.3,
        "gridAlpha": 0
    }],
    "graphs":<?=json_encode($allgraphfields)?>,
    "colors":["#FF6600", "#FCD202", "#B0DE09", "#0D8ECF", "#2A0CD0", "#CD0D74", "#CC0000", "#00CC00", "#0000CC", "#DDDDDD", "#999999", "#333333", "#990000"],
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "gridAlpha": 0,
        "position": "left"
    },
    "export": {
    	"enabled": true
     }

});


//ytg
  var chart = AmCharts.makeChart("chartdivstackedytg", {
    "type": "serial",
  "theme": "none",
    "legend": {
        "horizontalGap": 10,
        "verticalGap":0,
        "fontSize":8,
        "maxColumns":2,
        "position": "right",
    "useGraphSettings": true,
    "markerSize": 10
    },
    "dataProvider":<?=json_encode($stackdataytg)?>,
    "valueAxes": [{
        "stackType": "regular",
        "axisAlpha": 0.3,
        "gridAlpha": 0
    }],
    "graphs":<?=json_encode($allgraphfields)?>,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "gridAlpha": 0,
        "position": "left",
        "labelRotation":60,
    },
    "export": {
    	"enabled": true
     }

});

//full year
  var chart = AmCharts.makeChart("chartdivstackedfullyear", {
    "type": "serial",
  "theme": "none",
    "legend": {
        "horizontalGap":10,
        "verticalGap":0,
        "divId":"chartdivstackedfullyearlegend",
        "fontSize":8,
        "maxColumns": 2,
        "position": "right",
    "useGraphSettings": true,
    "markerSize": 9
    },
    "dataProvider":<?=json_encode($stackdatafullyear)?>,
    "valueAxes": [{
        "stackType": "regular",
        "axisAlpha": 0.3,
        "gridAlpha": 0
    }],
    "graphs":<?=json_encode($allgraphfields)?>,
    "categoryField": "period",
    "categoryAxis": {
        "gridPosition": "start",
        "axisAlpha": 0,
        "gridAlpha": 0,
        "position": "left",
        "labelRotation":60,
    },
    "export": {
    	"enabled": true
     }

});


    /*export*/
    
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
    
    
    });
    
    </script>

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
    
    $("#reset").click(function(e){
    alert("Clearing fields..");
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
</script></div>
      
<script type="text/javascript" src="<?= $assets ?>/js/datatables.min.js"></script>
