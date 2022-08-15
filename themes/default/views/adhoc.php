   <link href="<?= $assets ?>js/jquery-ui.css" rel="stylesheet"/>
  <script type="text/javascript" src="<?= $assets ?>/js/jquery-ui.js"></script>
 
  


<?php 
   
    
  $attrib = array('data-toggle' => 'validator', 'novalidate','role' => 'form','method'=>'POST','id'=>'searchform');?>
  <div class="searchform" style="z-index:999;top:10px;bottom:10px;background-color:white;">
              <?php  echo  form_open_multipart("welcome/adhoc", $attrib);?>
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
                                                
                                                echo form_dropdown('cluster[]',$clusterdetails,($_POST['cluster']), 'id="cluster" multiple="multiple" autocomplete="off" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("cluster") . '"  style="width:100%;" ');
                                                ?>
                                            </div>
        </div>
                <div class="col-md-2">
                     <?= lang("Country", "country"); ?>
                    <div class="form-group">
                         <select id="f_country" name="f_country[]" id="f_country_label" multiple="multiple" class="form-control select input-xs">
            <option value=""></option>
        </select>
                                
            </div></div>
           
             <!--   <!--<div class="col-md-2"><div class="form-group">
                                                <?= lang("Promotion", "promotion"); ?>
                                                <?php
                                                $promo=array("all"=>"All","1"=>"Promoted","0"=>"Non-Promoted");
                                               echo form_dropdown('promotion', $promo,($_POST['promo']), 'id="promo" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("Promotion_type") . '"  style="width:100%;" ');
                                                ?>
            </div></div>-->
               <!-- <div class="col-md-2">
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
                  <div class="col-md-2">
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
                                                
                                                echo form_dropdown('products[]',$products,($_POST['products']), 'id="prodcts" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("products") . '"  style="width:100%;" ');
                                                ?>
            </div>  
                    
                </div>-->
          <div class="col-md-3">
                     <div class="form-group">
                          <?= lang("Period", "period"); ?>
                      <?php   
                      $oneyearago=date('Y', strtotime('-1 year'));
                      $twoyearsago=date('Y', strtotime('-2 year'));
                           $months=array("01-".$twoyearsago=>"01-".$twoyearsago,"02-".$twoyearsago=>"02-".$twoyearsago,"03-".$twoyearsago=>"03-".$twoyearsago,"04-".$twoyearsago=>"04-".$twoyearsago,"05-".$twoyearsago=>"05-".$twoyearsago,"06-".$twoyearsago=>"06-".$twoyearsago,"07-".$twoyearsago=>"07-".$twoyearsago,"08-".$twoyearsago=>"08-".$twoyearsago,"09-".$twoyearsago=>"09-".$twoyearsago,"10-".$twoyearsago=>"10-".$twoyearsago,"11-".$twoyearsago=>"11-".$twoyearsago,"12-".$twoyearsago=>"12-".$twoyearsago,"01-".$oneyearago=>"01-".$oneyearago,"02-".$oneyearago=>"02-".$oneyearago,"03-".$oneyearago=>"03-".$oneyearago,"04-".$oneyearago=>"04-".$oneyearago,"05-".$oneyearago=>"05-".$oneyearago,"06-".$oneyearago=>"06-".$oneyearago,"07-".$oneyearago=>"07-".$oneyearago,"08-".$oneyearago=>"08-".$oneyearago,"09-".$oneyearago=>"09-".$oneyearago,"10-".$oneyearago=>"10-".$oneyearago,"11-".$oneyearago=>"11-".$oneyearago,"12-".$oneyearago=>"12-".$oneyearago,"01-".date("Y")=>"01-".date("Y"),"02-".date("Y")=>"02-".date("Y"),"03-".date("Y")=>"03-".date("Y"),"04-".date("Y")=>"04-".date("Y"),"05-".date("Y")=>"05-".date("Y"),"06-".date("Y")=>"06-".date("Y"),"07-".date("Y")=>"07-".date("Y"),"08-".date("Y")=>"08-".date("Y"),"09-".date("Y")=>"09-".date("Y"),"10-".date("Y")=>"10-".date("Y"),"11-".date("Y")=>"11-".date("Y"),"12-".date("Y")=>"12-".date("Y"));
                      echo form_dropdown('period[]',$months,($_POST['period']), 'id="period" multiple="multiple"  class="form-control input-tip select"  data-placeholder="' . lang("select") . ' period" style="width:100%;" ');?>   
                        
                        </div>
                 </div>
            
                    <div class="col-md-3">
                      
                <br>
                <input id="gsales" name="gsales" type="radio" class="" <?php echo "checked='checked'"; ?> value="1" <?php echo $this->form_validation->set_radio('gsales', 1); ?> />
                <label for="gsales" class="">Gross Sales</label>
                <br>
                  <input id="gsales" name="gsales" type="radio" class=""  value="0" <?php echo $this->form_validation->set_radio('gsales', 0); ?> />
                <label for="gsales" class="">Net Sales</label>

                    </div>
               
            </div>
                <div class="row">
                    
                   <!--  <div class="col-md-2">
                  <div class="form-group">
                      <?= lang("Sales Type", "Sales Type"); ?>
                        <?php echo form_dropdown('sales_type',array("all"=>"All","SI"=>"SI","PSO"=>"Primary","SSO"=>"Secondary"),($_POST['sales_type']), 'id="sales_type"  class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("sales_type") . '"  style="width:100%;" ');?>
                      
                  </div></div>
                        <div class="col-md-3">
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
                                                
                                                echo form_dropdown('distributor[]',$distributors,($_POST['distributor']), 'id="distributor" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("distributor") . '"  style="width:100%;" ');
                                                ?>
            </div>  
                    
                </div>
                        <div class="col-md-3">
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
                                                
                                                echo form_dropdown('customer[]',$customers,($_POST['customer']), 'id="customer" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' customer"  style="width:100%;" ');
                                                ?>
            </div>  
                    
                </div>-->
                    <!--<div class="col-md-2"><div class="form-group"><label>From</label><input type="text" placeholder="dd/yyyy" autocomplete="off" class="form-control input-tip datepicker monthPicker"  id="datefrom" value="01/<?=date('Y')?>" name="datefrom"></div>
                </div>
                    <div class="col-md-2"><div class="form-group"><label>To</label><input type="text" placeholder="dd/yyyy" autocomplete="off" class="form-control input-tip datepicker monthPicker"  id="dateto" value="12/<?=date('Y')?>" name="dateto"></div>-->
                 
                   
                  
                    <!--<div class="col-md-2"><div class="form-group">
                        
                      <?php 
                      $pricetypes=array("unified"=>"Unified","resale"=>"Resale","supply"=>"Supply");
                      echo form_dropdown('price_type',$pricetypes,($_POST['price_type']), 'id="price_type"  class="form-control input-tip select"  data-placeholder="' . lang("select") . ' price_type" style="width:100%;" ');?>   
                        
                    </div></div>-->
                    <div class="col-md-2"><div class="form-group" style="padding-top:30px"><input type="submit" name="filter" value="Filter" class="btn btn-primary">&nbsp;<input type="button" id="reset" value="Reset" class="btn btn-info"></div>
</div>
                </div>
<?php echo form_close(); ?>
    

</div>
  
  

<div class="row">
    <div class="col-md-6">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                                 Top Ten Distributors (PSO) <span style="color:red;font-size:9px">*All filters apply</span>
               <div id="toptendistributorspso" style="width:100%;
  height: 300px;">
                
                       
               </div></div>
    </div>
    
    
</div>
    
      <div class="col-md-6">
          <div class="panel panel-white">
                                             <div class="panel-heading" style="text-align:center">
                                                 Top Ten Distributors (SSO) <span style="color:red;font-size:9px">*All filters apply</span>
               <div id="toptendistributorssso" style="width:100%;
  height: 300px;">
                
                       
               </div></div>
    </div>
    
    
</div>
    
    
    
</div>
  

  <div class="row" style="margin-top:3px">
                                      
                                         
                        
                         <div class="col-lg-6 col-md-10" >
                                              
                    <div class="panel panel-white">
                                             <div class="panel-heading">
                           
                            <div class="row">
                                                                <div class="col-xs-9 text-center">
                                    <div class="mediumfont"><i class="fa fa-bar-chart"></i> Top 10 Best Selling Products (PSO):K€ <span style="color:red;font-size:9px">*All filters apply</span></div>
                                    
                                    
                                    
                                </div>
                            </div>
                                             </div>
                        <div id="chartdivprojects" style="width:100%;
  height: 300px;">
                                 
                                        
                                    </div>
                    </div>
                      
                           
                        
                    </div>
    
     <div class="col-lg-6 col-md-10" >
                                              
                    <div class="panel panel-white">
                                             <div class="panel-heading">
                           
                            <div class="row">
                                                                <div class="col-xs-9 text-center">
                                    <div class="mediumfont"><i class="fa fa-bar-chart"></i> Top 10 Best Selling Products (SSO):K€ <span style="color:red;font-size:9px">*All filters apply</span></div>
                                    
                                    
                                    
                                </div>
                            </div>
                                             </div>
                        <div id="chartdivprojectssso" style="width:100%;
  height: 300px;">
                                 
                                        
                                    </div>
                    </div>
                      
                           
                        
                    </div>
    
    
    
                             
                        </div> 

      
   


 
 <script id="_webengage_script_tag" type="text/javascript">
 $(document).ready(function(e){
     var chart2 = AmCharts.makeChart("chartdivprojects", {
  "type": "serial",
  "startDuration": 2,
  "dataProvider":$.parseJSON('<?=$bestselling?>'),
  "valueAxes": [{
    "position": "left",
    "title": "Sales Value('000)"
  }],
  "graphs": [{
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "showBalloon": true,
    "colorField": "color",
    "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "SoldQty",
     "labelText": "[[value]]",
     
  }
  ],
  "depth3D": 20,
  "angle": 30,
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "product",
  "categoryAxis": {
    "gridPosition": "start",
    "labelRotation":65,
    "fontSize":9
  }, "export": {
    "enabled": true
  }
});
  


//sso
 var chartssoproducts = AmCharts.makeChart("chartdivprojectssso", {
  "type": "serial",
  "startDuration": 2,
  "dataProvider":<?=$bestsellingsso?>,
  "valueAxes": [{
    "position": "left",
    "title": "Sales Value('000)"
  }],
  "graphs": [{
    "balloonText": " [[category]]: <b>[[value]]</b>",
    "colorField": "color",
    "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "SoldQty",
     "labelText": "[[value]]",
     "showBalloon": true
  }
 ],
  "depth3D": 20,
  "angle": 30,
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "product",
  "categoryAxis": {
    "gridPosition": "start",
    "labelRotation":65,
    "fontSize":9
  }, "export": {
    "enabled": true
  }
});
     
     
     
<?php
$allcustomerdata=array();
foreach($bestcustomers as $cust){
    $data["customer"]=$cust->name;
    $data["salesvalue"]=$cust->id;
    array_push($allcustomerdata,$data);
}
?>
var chart6 = AmCharts.makeChart("toptendistributorspso", {
  "type": "pie",
  "theme": "light",

  "dataProvider":<?=$bestcustomerspso?>,//$.parseJSON($("#customersales").val()),
  "radius": 100,
  "valueField": "salesvalue",
  "titleField": "customer",
   "balloon":{
   "fixedPosition":true
  },
  "export": {
    "enabled": true
  }
} );

  


//sso
//console.log(<?=$bestcustomerssso?>);
var chartsso = AmCharts.makeChart("toptendistributorssso", {
  "type": "pie",
  "theme": "light",

  "dataProvider":<?=$bestcustomerssso?>,//$.parseJSON($("#customersales").val()),
  "radius": 100,
  "valueField": "salesvalue",
  "titleField": "customer",
   "balloon":{
   "fixedPosition":true
  },
  "export": {
    "enabled": true
  }
} );

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
if($("#sales_type :selected").val()=="SI" || $("#sales_type :selected").val()=="PSO" ){
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