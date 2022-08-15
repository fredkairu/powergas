   <link href="<?= $assets ?>js/jquery-ui.css" rel="stylesheet"/>
  <script type="text/javascript" src="<?= $assets ?>/js/jquery-ui.js"></script>

<div class="col-md-2 col-xs-4 padding1010">
                    <a class="bred white quick-button" href="">
                        <?php echo $volume ?>
                        <!--<i class="fa fa-bar-chart-o"></i>-->

                        <p>Available Volumetric Gas</p>
                    </a>
                </div>
<?php
    foreach ($chatData as $month_sale) {
        $months[] = date('M-Y', strtotime($month_sale->month));
        $msales[] = $month_sale->sales;
        $mtax1[] = $month_sale->tax1;
        $mtax2[] = $month_sale->tax2;
        $mpurchases[] = $month_sale->purchases;
        $mtax3[] = $month_sale->ptax;
    }

  $attrib = array('data-toggle' => 'validator', 'novalidate','role' => 'form','method'=>'POST','id'=>'searchform');
    ?>

  

 
  
  
  
  





 