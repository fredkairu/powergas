   <link href="<?= $assets ?>js/jquery-ui.css" rel="stylesheet"/>
  <script type="text/javascript" src="<?= $assets ?>/js/jquery-ui.js"></script>
 
  


<?php
function row_status($x)
{
    if ($x == null) {
        return '';
    } elseif ($x == 'pending') {
        return '<div class="text-center"><span class="label label-warning">' . lang($x) . '</span></div>';
    } elseif ($x == 'completed' || $x == 'paid' || $x == 'sent' || $x == 'received') {
        return '<div class="text-center"><span class="label label-success">' . lang($x) . '</span></div>';
    } elseif ($x == 'partial' || $x == 'transferring') {
        return '<div class="text-center"><span class="label label-info">' . lang($x) . '</span></div>';
    } elseif ($x == 'due') {
        return '<div class="text-center"><span class="label label-danger">' . lang($x) . '</span></div>';
    } else {
        return '<div class="text-center"><span class="label label-default">' . lang($x) . '</span></div>';
    }
}

?>
<?php 
    foreach ($chatData as $month_sale) {
        $months[] = date('M-Y', strtotime($month_sale->month));
        $msales[] = $month_sale->sales;
        $mtax1[] = $month_sale->tax1;
        $mtax2[] = $month_sale->tax2;
        $mpurchases[] = $month_sale->purchases;
        $mtax3[] = $month_sale->ptax;
    }
    
  $attrib = array('data-toggle' => 'validator', 'novalidate','role' => 'form','method'=>'POST','id'=>'searchform');?>
  <div class="searchform" style="z-index:999;top:10px;background-color:white;">
              <?php  echo  form_open_multipart("welcome/search", $attrib);?>
            <div class="row">
                <div class="col-md-2"><div class="form-group">
                                                <?= lang("GBU", "gbu"); ?>
                                                <?php
                                                $gbu=array("all"=>"All","CHC"=>"CHC","GEM"=>"GEM","AFRICASON"=>"AFRICASON");
                                                echo form_dropdown('gbu', $gbu,($post['gbu']), 'id="gbu" multiple="multiple" autocomplete="off" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("gbu") . '"  style="width:100%;" ');
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
                    <div class="form-group">
                         <select id="f_country" name="f_country[]" id="f_country_label" multiple="multiple" class="form-control select input-xs">
            <option value=""></option>
        </select>
                                
            </div></div>
            
                <!--<div class="col-md-2"><div class="form-group">
                                                <?= lang("Promotion", "promotion"); ?>
                                                <?php
                                                $promo=array("all"=>"All","1"=>"Promoted","0"=>"Non-Promoted");
                                               echo form_dropdown('promotion', $promo,($post['promo']), 'id="promo" multiple="multiple" class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("Promotion_type") . '"  style="width:100%;" ');
                                                ?>
            </div></div>-->
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
                        <div class="col-md-2">
                  <div class="form-group">
                                                <?= lang("Customer", "customer"); ?>
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
                    <div class="col-md-2"><div class="form-group"><label>From</label><input type="text" placeholder="dd/yyyy" autocomplete="off" class="form-control input-tip datepicker monthPicker"  id="datefrom" value="01/<?=date('Y')?>" name="datefrom"></div>
                </div>
                    <div class="col-md-2"><div class="form-group"><label>To</label><input type="text" placeholder="dd/yyyy" autocomplete="off" class="form-control input-tip datepicker monthPicker"  id="dateto" value="12/<?=date('Y')?>" name="dateto"></div>
                </div>
                
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
               <div class="col-md-2"><div class="form-group">
                        
                      <?php 
                      $pricetypes=array("unified"=>"Unified","resale"=>"Resale","supply"=>"Supply");
                      echo form_dropdown('price_type',$pricetypes,($post['price_type']), 'id="price_type"  class="form-control input-tip select"  data-placeholder="' . lang("select") . ' price_type" style="width:100%;" ');?>   
                        
                    </div></div>
                
            
                    <div class="col-md-2">
                        <input id="gsales" name="gsales" type="radio" class="" <?php echo "checked='checked'"; ?> value="1" <?php echo $this->form_validation->set_radio('gsales', 1); ?> />
                <label for="gsales" class="">Gross Sales</label>
                        <br>
                        <input id="gsales" name="gsales" type="radio" class=""  value="0" <?php echo $this->form_validation->set_radio('gsales', 0); ?> />
                <label for="gsales" class="">Net Sales</label>
                
                

                    </div>
                    
                    
                    <div class="col-md-2"><div class="form-group" style="padding-top:10px"><input type="submit" name="filter" value="Filter" class="btn btn-primary">&nbsp;<input type="button" id="reset" value="Reset" class="btn btn-info"></div> 
                
            </div>
                <div class="row">
                    
                    
                    
                   <!--  <div class="col-md-2">
                  <div class="form-group">
                      <?= lang("Sales Type", "Sales Type"); ?>
                        <?php echo form_dropdown('sales_type',array("all"=>"All","SI"=>"SI","PSO"=>"Primary","SSO"=>"Secondary"),($post['sales_type']), 'id="sales_type"  class="form-control input-tip select" data-placeholder="' . lang("select") . ' ' . lang("sales_type") . '"  style="width:100%;" ');?>
                      
                  </div></div>-->
                    
          
                           
                
</div>
              
                </div>
<?php echo form_close(); ?>
    

</div>
  
  
  
  <div class="row">
     <span style="font-size:10px;color:green">
                        <?php if(isset($filters)){
                          echo "<center>Search Parameters:&nbsp;".$filters."</center>";  
                        }?>
                    </span>
         <!-- <div class="col-lg-12 col-md-10" >
                                              
                    <div class="panel panel-white">
                         
                                             <div class="panel-heading">
                           
                            <div class="row">
                                                                <div class="col-xs-9 text-center">
                                    <div class="mediumfont"><i class="fa fa-bar-chart"></i>PSO,SSO,SI SALES COMPARISON:K€ <span style="color:red;font-size:9px">*All filters apply</span> </div>
                                    
                                    
                                    
                                </div>
                            </div>
                                             </div>
                                             

                        <div id="chartdivrevenue" style="width:100%;
  height: 300px;">
                                 
                                        
                                    </div>
                    </div>
                      
                           
                        
                    </div>-->
      
      
      
  </div>
       
  

   <div class="row">
       <div style="margin-left:5px" class="col-lg-11 col-md-10" >
                                             
                    <div class="panel panel-white">
                                             <div class="panel-heading">
                           
                            <div class="row">
                                                                <div class="col-xs-9 text-center">
                                    <div class="mediumfont"><i class="fa fa-arrow-up"></i>Inventory & SISO Analysis (PSO) <span style="color:red;font-size:9px">*Distributor and date filter apply</span>
</div>
                                    
                                    
                                    
                                </div>
                            </div>
                                             </div>
                        <div id="stockcoverpso" style="width:99%;
  height: 500px;">
                                 
                                        
                                    </div>
                    </div>
                      
                           
                        
                    </div>
   </div>

  
  
 
<div class="row">
       <div style="margin-left:5px" class="col-lg-11 col-md-10" >
                                             
                    <div class="panel panel-white">
                                             <div class="panel-heading">
                           
                            <div class="row">
                                                                <div class="col-xs-9 text-center">
                                    <div class="mediumfont"><i class="fa fa-arrow-up"></i>Inventory Analysis (SSO) 
</div>
                                    
                                    
                                    
                                </div>
                            </div>
                                             </div>
                        <div id="stockcover" style="width:99%;
  height:500px;">
                                 
                                        
                                    </div>
                    </div>
                      
                           
                        
                    </div>
   </div>

 <div class="row" style="display:none">
       <div style="margin-left:5px" class="col-lg-11 col-md-10" >
                                             
                    <div class="panel panel-white">
                                             <div class="panel-heading">
                           
                            <div class="row">
                                                                <div class="col-xs-9 text-center">
                                    <div class="mediumfont"><i class="fa fa-arrow-up"></i>Inventory Analysis (SI) 
</div>
                                    
                                    
                                    
                                </div>
                            </div>
                                             </div>
                        <div id="stockcoversi" style="width:99%;
  height:500px;">
                                 
                                        
                                    </div>
                    </div>
                      
                           
                        
                    </div>
   </div>


  
  
  




      
    <input type="hidden" id="customersales" value="<?=json_encode($allsaless)?>">


 
 <script id="_webengage_script_tag" type="text/javascript">
 $(document).ready(function(e){

    
//psossostockunit
 var chart4 = AmCharts.makeChart("stockcoverpso", {
  "type": "serial",
  "startDuration":0,
  "columnSpacing":2,
  "dataProvider":<?=$stockcoverpso?>,
    "dataDateFormat": "MM-YYYY",
  "valueAxes": [{
    "position": "left",
    "title": "Sales Units",
    "id":"v1"
  },{
    "position": "right",
    "title": "Stock Cover",
    "id":"v2"
  }
  ],
  "graphs": [
      
  {
        "labelText": "[[value]]",
         "balloonText": "Stock Cover [[category]]: <b>[[value]]</b>",
        "bullet": "round",
    "lineThickness": 3,
     "valueAxis": "v2",
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
        "title": "Stock Cover",
               "valueField":"value"
    }
  ,{
    "balloonText": "Inventory [[category]]: <b>[[value]]</b>",
    //"colorField": "colorsso",
    "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "inventory",
    "title": "Inventory",
    "labelText": "[[value]]",
      "showBalloon": true
  },
  ,{
    "balloonText": "SI Sales [[category]]: <b>[[value]]</b>",
    "colorField": "colorsso",
    "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "si",
    "labelText": "[[value]]",
    "title": "SI Sales",
      "showBalloon": true
  },
        ,{
    "balloonText": "PSO Sales [[category]]: <b>[[value]]</b>",
        "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "pso",
    "title": "PSO Sales",
    "labelText": "[[value]]",
      "showBalloon":true
  }
  ],
  "depth3D": 20,
  "angle": 30,
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "date",
  "categoryAxis": {
    "gridPosition": "start",
    "labelRotation": 45
  },"export": {
    "enabled": true
  },
  "legend": {
    "useGraphSettings": true
  }
});
  
   


   



    /***************88stock cover sso***************/
    var chart5 = AmCharts.makeChart("stockcover", {
  "type": "serial",
  "startDuration": 0,
  "columnSpacing":2,
  "dataProvider":<?=$stockcover?>,
    "dataDateFormat": "MM-YYYY",
  "valueAxes": [{
    "position": "left",
    "title": "Sales Units",
    "id":"v1"
  },
  {
    "position": "right",
    "title": "Stock Cover",
    "id":"v2"
  }
    ],
  "graphs": [
      
  {
        "labelText": "[[value]]",
         "balloonText": "Stock Cover [[category]]: <b>[[value]]</b>",
        "bullet": "round",
    "lineThickness": 3,
    "valueAxis": "v2",
    "bulletSize": 7,
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
        "title": "Stock Cover",
               "valueField":"value"
    }
  ,{
    "balloonText": "Inventory [[category]]: <b>[[value]]</b>",
    //"colorField": "colorsso",
    "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "inventory",
    "title": "Inventory",
    "labelText": "[[value]]",
      "showBalloon": true
  },
  ,{
    "balloonText": "PSO Sales [[category]]: <b>[[value]]</b>",
    "colorField": "colorsso",
    "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "pso",
    "labelText": "[[value]]",
    "title": "PSO Sales",
      "showBalloon": true
  },
        ,{
    "balloonText": "SSO Sales [[category]]: <b>[[value]]</b>",
        "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "sso",
    "title": "SSO Sales",
    "labelText": "[[value]]",
      "showBalloon":true
  }

  ],
  "depth3D": 20,
  "angle": 30,
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "date",
  "categoryAxis": {
    "gridPosition": "start",
    "labelRotation": 45
  },"export": {
    "enabled": true
  },
  "legend": {
    "useGraphSettings": true
  }
});
    

    /***************stock cover SI************/
    var chart6 = AmCharts.makeChart("stockcoversi", {
  "type": "serial",
  "startDuration": 0,
  "columnSpacing":2,
  "dataProvider":<?=$stockcoversi?>,
    "dataDateFormat": "MM-YYYY",
  "valueAxes": [{
    "position": "left",
    "title": "Sales Units",
    "id":"v1"
  },
  {
    "position": "right",
    "title": "Stock Cover",
    "id":"v2"
  }],
  "graphs": [
      
  {
        "labelText": "[[value]]",
         "balloonText": "Stock Cover [[category]]: <b>[[value]]</b>",
        "bullet": "round",
    "lineThickness": 3,
    "bulletSize": 7,
    "valueAxis": "v2",
    "bulletBorderAlpha": 1,
    "bulletColor": "#FFFFFF",
    "useLineColorForBulletBorder": true,
    "bulletBorderThickness": 3,
    "fillAlphas": 0,
        "title": "Stock Cover",
               "valueField":"value"
    }
  ,{
    "balloonText": "Inventory [[category]]: <b>[[value]]</b>",
    //"colorField": "colorsso",
    "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "inventory",
    "title": "Inventory",
    "labelText": "[[value]]",
      "showBalloon": true
  },
  ,{
    "balloonText": "PSO Sales [[category]]: <b>[[value]]</b>",
    "colorField": "colorsso",
    "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "pso",
    "labelText": "[[value]]",
    "title": "PSO Sales",
      "showBalloon": true
  },
        ,{
    "balloonText": "SSO Sales [[category]]: <b>[[value]]</b>",
        "fillAlphas": 1,
    "lineAlpha": 0.1,
    "type": "column",
    "valueField": "sso",
    "title": "SSO Sales",
    "labelText": "[[value]]",
      "showBalloon":true
  }

  ],
  "depth3D": 20,
  "angle": 30,
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "date",
  "categoryAxis": {
    "gridPosition": "start",
    "labelRotation": 45
  },"export": {
    "enabled": true
  },
  "legend": {
    "useGraphSettings": true
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
    
    
      } );
    
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
   // $('#f_country, #f_country_label').hide();
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