<div>   <link href="<?= $assets ?>js/jquery-ui.css" rel="stylesheet"/>
   
  <script type="text/javascript" src="<?= $assets ?>/js/jquery-ui.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.20/fc-3.3.0/fh-3.1.6/r-2.2.3/rg-1.1.1/rr-1.2.6/sc-2.0.1/sp-1.0.1/sl-1.3.1/datatables.min.css"/>
  <script>
    $(document).ready(function () {
        var oTable = $('.SLData').dataTable({
            "aaSorting": [[0, "asc"], [1, "desc"]],
            "aLengthMenu": [[10, 25, 50, 100,500,1000,2000, -1], [10, 25, 50, 100,500,1000,2000,"<?= lang('all') ?>"]],
            "iDisplayLength": <?= $Settings->rows_per_page ?>,
            scrollY:        "500px",
        scrollX:        true,
        scrollCollapse: true,
        paging:         false,
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
                    return Number(a) + Number(b);
                }, 0 );
          // Update footer
          $( api.column( j ).footer() ).html(pageTotal);
					j++;
							} 
			}
		}
	);
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

  

select[multiple], select[size] {
    height: 0px;
    height: 0px;
    background-color: white;
    border: 1px white;
    overflow-y: hidden;
    display: none !important;
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
        if($number < 0 ){
            return "<span style='font-color:bold;color:red'>".$number."</span>";
        }
        else{
            return $number;
        }
    
    }
    
    
  $attrib = array('data-toggle' => 'validator', 'novalidate','role' => 'form','method'=>'POST','id'=>'searchform');?>
  <div class="searchform" style="z-index:999;top:10px;background-color:white;">
              <?php  echo  form_open_multipart("welcome/sso", $attrib);?>
            <div class="row">
                <div class="col-md-2"><div class="form-group">
                                                <?= lang("GBU", "gbu"); ?>
                                                <?php
                                                $gbu=array("all"=>"All","GEM"=>"GEM","CHC"=>"CHC");
                                                echo form_dropdown('gbu', $gbu,($_POST['gbu']), 'id="gbu" multiple="multiple" autocomplete="off" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("gbu") . '"  style="width:100%;" ');
                                                ?>
            </div></div>
            
             
                <div class="col-md-2">
    <div class="form-group">
                                                <?= lang("Cluster", "cluster"); ?>
                                                <?php
                                                foreach ($clusters as $clust) {
                                                    $clusterdetails[$clust->name]=$clust->name;
                                                }
                                                
                                                echo form_dropdown('cluster[]',$clusterdetails,($post['cluster']), 'id="cluster" multiple="multiple" autocomplete="off" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("cluster") . '"  style="width:100%;" ');
                                                ?>
                                            </div>
        </div>
                <div class="col-md-2">
                     <?= lang("Country", "country"); ?>
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
                                                $this->db->where(array("group_name"=>"customer",'also_distributor'=>"Y"));
                                                $q = $this->db->get('companies');
                                                $customers["all"]="Select All";
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                if($row->name){
                $distributors[$row->name] = $row->name;
                }
                else{
                   $distributors[$row->name] = $row->company;  
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
                          <?= lang("Period", "period"); ?>
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
                $products[$row->id] = $row->name."(".$row->code.")";
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
                      
                
                <input id="gsales" name="gsales" type="radio" class="" <?php  if($_POST["gsales"]==1){echo "checked='checked'";}?> value="1" <?php echo $this->form_validation->set_radio('gsales', 1); ?> />
                <label for="gsales" class="">Gross Sales</label>
                <br>
                <input id="gsales2" name="gsales" type="radio" class=""  <?php  if($_POST["gsales"]==0){echo "checked='checked'";}?> value="0" <?php echo $this->form_validation->set_radio('gsales', 0); ?> />
                <label for="gsales" class="">Net Sales</label>

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
  <?php $yearonly=  substr(date("Y"),-2); 
  if(!$currentmonth){$currentmonth="Month";}
?>
  


  <div class="row">
      <div style="overflow-y:hidden;overflow-x:scroll;max-height:400px !important">
      <div class="col-md-12" >
          <?php if($table=="ssocountry") {?>
          <h4>SSO Country</h4>
         <table class="SLData table table-bordered table-hover table-striped order-column">
                        <thead>
                            <tr><td colspan="7"><span style="font-weight:bold"><?=$currentmonth?></span></td><td></td><td colspan="7"><span style="font-weight:bold">YTD<?=$currentmonth?></span></td> <td colspan="7"><span style="font-weight:bold">YTG<?=$currentmonth?></span></td><td colspan="2" width="100px"><span style="font-weight:bold">FULL YEAR</span></td><td colspan="2"><span style="font-weight:bold">YTD AVG</span></td><td colspan="6"><span style="font-weight:bold">YTG-MONTHLY AVERAGES</span></td></tr>
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
                       
                           <?php foreach ($countries as $ctry){ ?>
                           <tr>
                               <td class="headcol"><?=$ctry["name"]?>  </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["lastyearsalesmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudgetmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearsalesmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["thisyearsalesmonth"]-$ctry["lastyearsalesmonth"],0))?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"> <?=markRed(round(((($ctry["thisyearsalesmonth"]-$ctry["lastyearsalesmonth"])/$ctry["thisyearsalesmonth"])*100),0))?>%</td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"> <?=markRed(round($ctry["thisyearsalesmonth"]-$ctry["thisyearbudgetmonth"],0))?></td>
                                 <td  class="center"  title="<?=$ctry["name"]?>">
                                 <?=markRed(round((($ctry["thisyearsalesmonth"]-$ctry["thisyearbudgetmonth"])/$ctry["thisyearbudgetmonth"])*100,0))?>%
                                 </td>
                                 
                                  <!--ytd-->
                                  <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsaleslastyear"]))?> </td>      
                                  <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["budgetytd"])?>  </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["ytdsalesthisyear"])?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]-$ctry["ytdsaleslastyear"],0))?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(((($ctry["ytdsalesthisyear"]-$ctry["ytdsaleslastyear"])/$ctry["ytdsalesthisyear"])*100),0))?>% </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]-$ctry["budgetytd"],0))?> </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?= markRed(round((($ctry["ytdsalesthisyear"]-$ctry["budgetytd"])/$ctry["budgetytd"])*100,0))?> %</td>
                                    <!--ytg-->
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=abs(round($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]))?> </td>
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"]-$ctry["budgetytd"])?>  </td> 
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])?> </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgcomparison=round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]),0);
                                   echo markRed($ytgcomparison);
                                   ?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((($ytgcomparison/round($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]))*100),0))?>%</td>
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["thisyearbudget"]-$ctry["budgetytd"])))?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(((($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["thisyearbudget"]-$ctry["budgetytd"]))/($ctry["thisyearbudget"]-$ctry["budgetytd"]))*100))?> %</td>
                                    
                                    <!--full year-->
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["lastyearsales"])?> </td>
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"])?>  </td>
                                     
                                     <!--YTD monthly avg-->
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsaleslastyear"]/(12-$remainingmonths)))?> </td>
                                      <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]/(12-$remainingmonths)))?> </td>
                                      <!--monthly avg ytg-->
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytgsaleslastyear"]/$remainingmonths))?> </td>
                                      <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytgsalesthisyear"]/$remainingmonths))?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgdiff=round($ctry["ytgsaleslastyear"]/$remainingmonths)-round($ctry["ytdsaleslastyear"]/(12-$remainingmonths));
                                    echo $ytgdiff?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((round($ytgdiff)/round($ctry["ytdsaleslastyear"]/(12-$remainingmonths))*100)))?> %</td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgdiffthsyear=round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])/$remainingmonths)-round($ctry["ytdsalesthisyear"]/(12-$remainingmonths));
                                    echo markRed($ytgdiffthsyear)?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((round($ytgdiffthsyear)/round($ctry["ytdsalesthisyear"]/(12-$remainingmonths))*100)))?> %</td>
                                    
                                    <!--full year-->
                                   
                                    
                           
                            </tr>
                           <?php  }
?>                        
                        </tbody>
                       <tfoot>
		<tr>
			<td>Totals</td>
			<?php echo str_repeat("<td></td>",31); ?>
		</tr>
	</tfoot> 
                    </table>
          
          <?php } else if($table=="ssoproduct"){?>
          
           <h4>SSO Product</h4>
           
            <table   class="SLData table table-bordered table-hover table-striped">
                        <thead>
                            <tr><td colspan="7"><span style="font-weight:bold"><?=$currentmonth?></span></td><td></td><td colspan="7"><span style="font-weight:bold">YTD<?=$currentmonth?></span></td> <td colspan="7"><span style="font-weight:bold">YTG<?=$currentmonth?></span></td><td colspan="2" width="100px"><span style="font-weight:bold">FULL YEAR</span></td><td colspan="2"><span style="font-weight:bold">YTD AVG</span></td><td colspan="6"><span style="font-weight:bold">YTG-MONTHLY AVERAGES</span></td></tr>
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
                               $promotedcategory=$this->settings_model->getPromotedProductCategory($ctry["name"],$_POST["promotion"]);
                                if($ctry["name"]){ //show only selected brands and promotion category
                               ?>
                             <tr>
                         <td class="headcol"><?=$ctry["name"].$promotedcategory?>  </td>

                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["lastyearsalesmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudgetmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearsalesmonth"])?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["thisyearsalesmonth"]-$ctry["lastyearsalesmonth"],0))?> </td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"> <?=markRed(round(((($ctry["thisyearsalesmonth"]-$ctry["lastyearsalesmonth"])/$ctry["thisyearsalesmonth"])*100),0))?>%</td>
                                 <td  class="center"  title="<?=$ctry["name"]?>"> <?=markRed(round($ctry["thisyearsalesmonth"]-$ctry["thisyearbudgetmonth"],0))?></td>
                                 <td  class="center"  title="<?=$ctry["name"]?>">
                                 <?=markRed(round((($ctry["thisyearsalesmonth"]-$ctry["thisyearbudgetmonth"])/$ctry["thisyearbudgetmonth"])*100,0))?>%
                                 </td>
                                  <!--ytd-->
                                  <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsaleslastyear"]))?> </td>      
                                  <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["budgetytd"])?>  </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["ytdsalesthisyear"])?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]-$ctry["ytdsaleslastyear"],0))?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(((($ctry["ytdsalesthisyear"]-$ctry["ytdsaleslastyear"])/$ctry["ytdsalesthisyear"])*100),0))?>% </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]-$ctry["budgetytd"],0))?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((($ctry["ytdsalesthisyear"]-$ctry["budgetytd"])/$ctry["budgetytd"])*100,0))?> %</td>
                                    <!--ytg-->
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=abs(round($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]))?> </td>
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"]-$ctry["budgetytd"])?>  </td> 
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"]))?> </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgcomparison=round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]),0);
                                   echo markRed($ytgcomparison);
                                   ?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((($ytgcomparison/round($ctry["lastyearsales"]-$ctry["ytdsaleslastyear"]))*100),0))?>%</td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["thisyearbudget"]-$ctry["budgetytd"])))?> </td> 
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round(((($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])-($ctry["thisyearbudget"]-$ctry["budgetytd"]))/($ctry["thisyearbudget"]-$ctry["budgetytd"]))*100))?> %</td>
                                    
                                    <!--full year-->
                                    <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["lastyearsales"])?> </td>
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed($ctry["thisyearbudget"])?>  </td>
                                     
                                     <!--YTD monthly avg-->
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsaleslastyear"]/(12-$remainingmonths)))?> </td>
                                      <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytdsalesthisyear"]/(12-$remainingmonths)))?> </td>
                                      <!--monthly avg ytg-->
                                     <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytgsaleslastyear"]/$remainingmonths))?> </td>
                                      <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round($ctry["ytgsalesthisyear"]/$remainingmonths))?> </td> 
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgdiff=round($ctry["ytgsaleslastyear"]/$remainingmonths)-round($ctry["ytdsaleslastyear"]/(12-$remainingmonths));
                                    echo markRed($ytgdiff)?> </td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?=markRed(round((round($ytgdiff)/round($ctry["ytdsaleslastyear"]/(12-$remainingmonths))*100)))?> %</td>
                                   <td  class="center"  title="<?=$ctry["name"]?>"><?php $ytgdiffthsyear=round(($ctry["thisyearbudget"]-$ctry["ytdsalesthisyear"])/$remainingmonths)-round($ctry["ytdsalesthisyear"]/(12-$remainingmonths));
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
			<td>Totals</td>
			<?php echo str_repeat("<td></td>",31); ?>
		</tr>
	</tfoot>
                    </table>
           
           
           
           
           
           
          <?php }
          
          else if ($table=="monthlytrend"){
              
              echo "Monthly trend";
          }
          
          
          ?>
      </div>
  </div>
  </div>


  <div class="row">
    <div class="col-md-6" style="overflow-x:scroll;overflow-y:no-scroll;">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                                 Periodic Sales:K€ 
               <div id="siperiodic" style="width:content-box;height:400px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>
    
    <div class="col-md-6" style="overflow-x:scroll;overflow-y:no-scroll;">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                                 Periodic Sales Variance:K€ 
               <div id="siperiodicvariance" style="width:container;height:400px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>
    
    
</div>
  
  
<div class="row">
    <div class="col-md-6" style="overflow-x:scroll;max-width:1500px">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                              YTD Sales:K€ 
               <div id="ytdsales" style="width:content-box;height:400px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>
    <div class="col-md-6" style="overflow-x:scroll;max-width:1500px">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                              YTD Sales Variance:K€ 
               <div id="psoytdvariance" style="width:content-box;height:400px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>
    
    
    
</div>
  
  
  <div class="row">
      
      
    <div class="col-md-6" style="overflow-x:scroll;max-width:1500px">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                              YTD Sales Average:K€ 
               <div id="ytdaverage" style="width:content-box;height:400px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>
  <div class="col-md-6" style="overflow-x:scroll;">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                          YTD Sales Average Variance:K€ 
               <div id="ytdsalesavgvariance" style="width:content-box;height:300px;background-color:#fff">
                
                       
               </div>
                   
                                              
                                              
                                             </div>
              <div class="panel-heading" style="text-align:center">
                                            
               <div id="ytgmonthlyavg" style="width:content-box;height:300px;background-color:#fff">
                
                       
               </div>
                   
                                              
                                              
                                             </div>
              
    </div>
    
    
</div>
    
    
    
</div>

<div class="row">
    <div class="col-md-12" style="overflow-x:scroll;overflow-y:no-scroll;max-width:1500px">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                                 Monthly Sales Trend:K€ 
               <div id="sipercountry" style="width:2000px;height:400px;background-color:#fff">
                
                       
               </div></div>
    </div>
    
    
</div>
    
    
    
    
</div>
      
      <div class="row">
        <div id="chartdivstacked" style="width:100%;height:400px;background-color:#fff">  
      </div>
 
  
  
  
  





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

   console.log(<?=$siperiodic?>);

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
        "title": "Forecast 1",
        "type": "column",
        "valueField": "Forecast"
    },
    {
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Forecast 2",
        "type": "column",
        "valueField": "Forecast2"
    },
    {
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

/********************SI YTG monthly AVG****************/
 var chartt = AmCharts.makeChart("ytgmonthlyavg",{
    "theme": "none",
    "type": "serial",
      "columnSpacing":2,
    "dataProvider":[<?=$ytgmonthlyaverage?>],
    "valueAxes": [{
      //  "stackType": "1d",
       // "unit": "%",
        "position": "left",
        "title": "YTG Monthly Average"
    }],
    "startDuration": 1,
    "graphs": [ 
  {
      "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "N-1",
        "type": "column",
        "valueField": "N-1"
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
        "title": "Forecast 1",
        "type": "column",
        "valueField": "Forecast1"
    }, {
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Forecast 2",
        "type": "column",
        "valueField": "Forecast2"
    },
    
        {
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
        "labelRotation":0
    },
    "legend": {
    "useGraphSettings": true
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
    },{
       "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "lineAlpha": 0.2,
        "title": "Forecast",
        "type": "column",
        "valueField": "Forecast"
    },{
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
   var chart = AmCharts.makeChart("sipercountry", {
    "theme": "none",
    "type": "serial",
      "columnSpacing":2,
    "dataProvider":<?=$consolidatedsi?>,
    "valueAxes": [{
      //  "stackType": "1d",
       // "unit": "%",
        "position": "left",
        "title": "SSO Comparison"
    }],
    "startDuration":0,
    "graphs": [
 {
      "labelText": "[[value]]",
    "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
        "title": "N-1",
        
        "valueField": "ActualLast"
    }
       , 
    {        "lineAlpha": 0.2,

        "labelText": "[[value]]",
        "fillAlphas": 0.9,
        "title":"Budget",
        "type": "column",
        "valueField": "Budget"
    },{
       "labelText": "[[value]]",
       "bullet": "round",
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
       "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
        "title": "Forecast 2",
     
        "valueField": "Forecast2"
    }, {
        "labelText": "[[value]]",
        "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
        "title": "Actual",
               "valueField":"Actual"
    }   ],
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
    
/***********stcked*******************
 * 
 * @type @exp;AmCharts@call;makeChart|@exp;AmCharts@call;makeChart|@exp;AmCharts@call;makeChart|@exp;AmCharts@call;makeChartbar
 */   
  var chart = AmCharts.makeChart("chartdivstacked", {
    "type": "serial",
  "theme": "none",
    "legend": {
        "horizontalGap": 10,
        "maxColumns": 1,
        "position": "right",
    "useGraphSettings": true,
    "markerSize": 10
    },
    "dataProvider": [{
        "period":"Feb",
        "ET": 2.5,
        "KE": 2.5,
        "TZ": 2.1,
        "UG": 0.3,
        "MG": 0.2
    }, {
        "period": "YTD-Feb",
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
    
    ],
    "valueAxes": [{
        "stackType": "regular",
        "axisAlpha": 0.3,
        "gridAlpha": 0
    }],
    "graphs": [{
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0.3,
        "title": "KE",
        "type": "column",
    "color": "#000000",
        "valueField": "KE"
    }, {
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0.3,
        "title": "UG",
        "type": "column",
    "color": "#000000",
        "valueField": "UG"
    }, {
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0.3,
        "title": "ET",
        "type": "column",
    "color": "#000000",
        "valueField": "ET"
    }, {
        "balloonText": "<b>[[title]]</b><br><span style='font-size:14px'>[[category]]: <b>[[value]]</b></span>",
        "fillAlphas": 0.8,
        "labelText": "[[value]]",
        "lineAlpha": 0.3,
        "title": "MG",
        "type": "column",
    "color": "#000000",
        "valueField": "MG"
    }],
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
      

