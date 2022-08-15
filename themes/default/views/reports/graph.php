<style>
#chartdiv {
  width: 600px;
  height: 300px;
}

#chartdiv1 {
  width: 500px;
  height: 300px;
}
#chartdivsalesbytype {
  width: 500px;
  height: 300px;
}
#roombooking {
  width: 500px;
  height: 300px;
}
</style>
<div class="box">
    <div class="box-header">
        <h2 class="blue"><i class="fa-fw fa fa-users"></i><?= lang('sales_reports'); ?>:From <?=$fromdate." to ".$todate?></h2>

        <div class="box-icon">
            <ul class="btn-tasks">
                
                <form action="sales/graph">
                   <li><input type="submit" name="graph" value="Search" class="btn btn-primary"></li>
                      <li><input type="text" placeholder="To" name="todate" class="form-control input-tip date"></li>
                   <li><input type="text" placeholder="From" name="fromdate" class="form-control input-tip date"></li>
                </form>
            </ul>
        </div>
    </div>
    <div class="box-content">
        <div class="row">
            <div class="col-md-6">

                <center class="introtext bold"><?= lang('Sales/Purchases/Expenses'); ?>&nbsp;Sales:<?=$sales?>&nbsp;Purchases:<?=$purchases?></center>
<div id="chartdiv"></div>
              
            </div>
             <div class="col-md-6">

                 <center class="introtext bold"><?= lang('Sales/Purchases/Expenses'); ?>&nbsp;Sales:<?=$sales?>&nbsp;Purchases:<?=$purchases?></center>
<div id="chartdiv1"></div>
              
            </div>
            
        </div>
          <div class="row">
            <div class="col-md-6">

                <center class="introtext bold">
                <?php 
                $totalpayments=0;
                $typess=json_decode($payment_type);
               // die(print_r($typess));
                foreach ($typess as $value) {
                       $totalpayments+=$value->total_amount; 
                    }?>
                <?= lang('sales_per_payment_method(KES '.$totalpayments.')');?>(Due Amount:<?=$sales-$totalpayments?>)</center>
<div id="chartdivsalesbytype"></div>
              
            </div>
             <div class="col-md-6">

                <!-- <center class="introtext bold"><?= lang('room_bookings_per_day(KES)'); ?></center>
<div id="roombooking"></div>-->
              
            </div>
            
        </div>
    </div>
</div>
<script type="text/javascript" src="<?= $assets ?>js/html2canvas.min.js"></script>
<script type="text/javascript" src="<?= $assets ?>amcharts/amcharts.js"></script>
<script type="text/javascript" src="<?= $assets ?>amcharts/pie.js"></script>
<script type="text/javascript" src="<?= $assets ?>amcharts/serial.js"></script>
<script type="text/javascript" src="<?= $assets ?>amcharts/themes/light.js"></script>

<script type="text/javascript">
    $(document).ready(function () {
 var chart = AmCharts.makeChart( "chartdiv", {
  "type": "pie",
  "theme": "light",
  "dataProvider": [ {
    "category": "Sales",
    "value":<?=$sales?>
  }, {
    "category": "Purchases",
    "value": <?=$purchases?>
  }, {
    "category": "Expenses",
    "value": <?=$expenses?>
  } ],
  "valueField": "value",
  "titleField": "category",
   "balloon":{
   "fixedPosition":true
  },
  "export": {
    "enabled": true
  }
} );
//bar chart
 var chart = AmCharts.makeChart( "chartdiv1", {
  "type": "serial",
  "theme": "light",
  "dataProvider": [ {
    "category": "Sales",
    "values": <?=$sales?>
  }, {
    "category": "Purchases",
    "values": <?=$purchases?>
  }, {
    "category": "Expenses",
    "values": <?=$expenses?>
  } ],
  "valueAxes": [ {
    "gridColor": "#FFFFFF",
    "gridAlpha": 0.2,
    "dashLength": 0
  } ],
  "gridAboveGraphs": true,
  "startDuration": 1,
  "graphs": [ {
    "balloonText": "[[category]]: <b>[[value]]</b>",
    "fillAlphas": 0.8,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "values"
  } ],
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "category",
  "categoryAxis": {
    "gridPosition": "start",
    "gridAlpha": 0,
    "tickPosition": "start",
    "tickLength": 20
  },
  "export": {
    "enabled": true
  }

} );

//pie chart sales by type

 var chart = AmCharts.makeChart( "chartdivsalesbytype", {
  "type": "pie",
  "theme": "light",
  "dataProvider": <?=$payment_type?>,
  "valueField": "total_amount",
  "titleField": "paid_by",
   "balloon":{
   "fixedPosition":true
  },
  "export": {
    "enabled": true
  }
} );

//bvar chart
 var chart = AmCharts.makeChart( "roombooking", {
  "type": "serial",
  "theme": "light",
  "dataProvider":<?=$room_bookings?>,
  "valueAxes": [ {
    "gridColor": "#FFFFFF",
    "gridAlpha": 0.2,
    "dashLength": 0
  } ],
  "gridAboveGraphs": true,
  "startDuration": 1,
  "graphs": [ {
    "balloonText": "[[invoice_date]]: <b>[[paid_amount]]</b>",
    "fillAlphas": 0.8,
    "lineAlpha": 0.2,
    "type": "column",
    "valueField": "paid_amount"
  } ],
  "chartCursor": {
    "categoryBalloonEnabled": false,
    "cursorAlpha": 0,
    "zoomable": false
  },
  "categoryField": "invoice_date",
  "categoryAxis": {
    "gridPosition": "start",
    "gridAlpha": 0,
    "tickPosition": "start",
    "tickLength": 20
  },
  "export": {
    "enabled": true
  }

} );


} );
</script>
