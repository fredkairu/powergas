<!DOCTYPE html>
<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    
    <base href="<?= site_url() ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?> - <?= $Settings->site_name ?></title>
    <link rel="shortcut icon" href="<?= $assets ?>images/icon.png"/>
    <link href="<?= $assets ?>styles/theme.css" rel="stylesheet"/>
    <link href="<?= $assets ?>styles/style.css" rel="stylesheet"/>
    <link  type="text/css" href="//cdn.amcharts.com/lib/3/plugins/export/export.css" rel="stylesheet">
    
    <script type="text/javascript" src="<?= $assets ?>js/jquery-2.0.3.min.js"></script>
    <script src="<?= $assets ?>js/jquery-ui.min.js" integrity="sha256-eTyxS0rkjpLEo16uXTS0uVCS4815lc40K2iVpWDvdSY=" crossorigin="anonymous"></script>
   
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
            src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDsO2OoiQC8-Gfhynkz3zzSjNs8Nik5ALQ&callback=initMap"
            defer
    ></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery-migrate-1.2.1.min.js"></script>
    
    <script type="text/javascript" src="<?= $assets ?>/js/amcharts/amcharts.js"></script>
    <script src="//cdn.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <script type="text/javascript" src="<?= $assets ?>/js/amcharts/serial.js"></script>
    <script type="text/javascript" src="<?= $assets ?>/js/amcharts/pie.js"></script>
    <script type="text/javascript" src="<?= $assets ?>/js/amcharts/themes/light.js"></script>
    <script type="text/javascript" src="<?= $assets ?>js/jquery.table2excel.js"></script>


<!--     
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.10.13/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.10.13/datatables.min.js"></script>
     -->



    <!--[if lt IE 9]>
    <script src="<?= $assets ?>js/jquery.js"></script>
    <![endif]-->
    <noscript><style type="text/css">#loading { display: none; }</style></noscript>
    <?php 
    
    $Admin=1;
    if ($Settings->rtl) { ?>
        <link href="<?= $assets ?>styles/helpers/bootstrap-rtl.min.css" rel="stylesheet"/>
        <link href="<?= $assets ?>styles/style-rtl.css" rel="stylesheet"/>
        <script type="text/javascript">
            $(document).ready(function () { $('.pull-right, .pull-left').addClass('flip'); });
        </script>
    <?php } ?>
    <script type="text/javascript">
        var gmarkers1 = [];
        $(window).load(function () {
            $("#loading").fadeOut("slow");
        });
        function initMap() {
            var locations = [];
            $.ajax({
                type:"GET",
                url:"https://techsavanna.technology/powergas/Api/getAllCustomersShops",
                dataType: 'json',
                cache:false,
                success: function(response) {

                    $.each(response, function(index, data) {
                        var location = [data.shop_name,data.lat,data.lng,data.id,data.salesperson_id,data.color];
                        locations.push(location);
                        

                    });

                    if(locations.length > 0 && document.getElementById("map")!=null){
                        var infoWindow =  new google.maps.InfoWindow({
                    		content: ''
                    	});
	
                        const map = new google.maps.Map(document.getElementById("map"), {
                            zoom: 8,
                            center: { lat: 0.5967698, lng: 34.7425024 },
                        });

                        for (i = 0; i < locations.length; i++) {
                            const image = "https://maps.gstatic.com/mapfiles/markers2/marker_sprite.png";
                            const svgMarker = {
                                path:
                                  "M10.453 14.016l6.563-6.609-1.406-1.406-5.156 5.203-2.063-2.109-1.406 1.406zM12 2.016q2.906 0 4.945 2.039t2.039 4.945q0 1.453-0.727 3.328t-1.758 3.516-2.039 3.070-1.711 2.273l-0.75 0.797q-0.281-0.328-0.75-0.867t-1.688-2.156-2.133-3.141-1.664-3.445-0.75-3.375q0-2.906 2.039-4.945t4.945-2.039z",
                                fillColor: locations[i][5],
                                fillOpacity: 0.6,
                                strokeWeight: 0,
                                rotation: 0,
                                scale: 2,
                                anchor: new google.maps.Point(15, 30),
                              };
                            const marker = new google.maps.Marker({
                                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                                label: {text: locations[i][0],color: 'black',},
                                salesperson_id:locations[i][4],
                                map,
                                icon: svgMarker,
                            });
                            marker.setLabel(null);
                            gmarkers1.push(marker);
                            bindInfoWindow(marker, map, infoWindow, "<p>" + locations[i][0] + "</p>");  
                            
                        }
                    }else{
                        //console.log('No locations present');
                    }

                }
            });
        }
        
        /**
         * Function to filter markers by category
         */
        
        function filterMarkers (category) {
            console.log(category);
            for (i = 0; i < gmarkers1.length; i++) {
                marker = gmarkers1[i];
                // If is same category or category not picked
                if (marker.salesperson_id == category || category.length === 0) {
                    marker.setVisible(true);
                }
                // Categories don't match 
                else {
                    marker.setVisible(false);
                }
            }
        }
        
        function bindInfoWindow(marker, map, infoWindow, html) { 
        	google.maps.event.addListener(marker, 'mouseover', function() { 
        		infoWindow.setContent(html); 
        		infoWindow.open(map, marker); 
        	});
        	google.maps.event.addListener(marker, 'mouseout', function() { 
        		infoWindow.close(map, marker); 
        	}); 
        } 
    </script>
</head>

<body>
<noscript>
    <div class="global-site-notice noscript">
        <div class="notice-inner">
            <p><strong>JavaScript seems to be disabled in your browser.</strong><br>You must have JavaScript enabled in
                your browser to utilize the functionality of this website.</p>
        </div>
    </div>
</noscript>
<div id="loading"></div>
<div id="app_wrapper">
    <header id="header" class="navbar">
        <div class="container">
            <a class="navbar-brand" href="<?= site_url() ?>"><span class="logo"><?= $Settings->site_name ?></span></a>

            <div class="btn-group visible-xs pull-right btn-visible-sm">
                <button class="navbar-toggle btn" type="button" data-toggle="collapse" data-target="#sidebar_menu"><span
                        class="fa fa-bars"></span></button>
                <a href="<?= site_url('users/profile/' . $this->session->userdata('user_id')); ?>" class="btn"><span
                        class="fa fa-user"></span></a>
                <a href="<?= site_url('logout'); ?>" class="btn"><span class="fa fa-sign-out"></span></a>
            </div>
            <div class="header-nav">
                <ul class="nav navbar-nav pull-right">
                    <li class="dropdown">
                        <a class="btn account dropdown-toggle" data-toggle="dropdown" href="#">
                            <img alt=""
                                 src="<?= $this->session->userdata('avatar'); ?>"
                                 class="mini_avatar img-rounded">

                            <div class="user">
                                <span><?= lang('welcome') ?> <?= $this->session->userdata('username'); ?></span>
                            </div>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a href="<?= site_url('users/profile/' . $this->session->userdata('user_id')); ?>"><i
                                        class="fa fa-user"></i> <?= lang('profile'); ?></a></li>
                            <li>
                                <a href="<?= site_url('users/profile/' . $this->session->userdata('user_id') . '/#cpassword'); ?>"><i
                                        class="fa fa-key"></i> <?= lang('change_password'); ?></a></li>
                            <li class="divider"></li>
                            <li><a href="<?= site_url('logout'); ?>"><i
                                        class="fa fa-sign-out"></i> <?= lang('logout'); ?></a></li>
                        </ul>
                    </li>
                </ul>
                <ul class="nav navbar-nav pull-right">
                    <!--<li class="dropdown hidden-xs"><a class="btn tip" title="<?= lang('dashboard') ?>"-->
                    <!--                                  data-placement="left" href="<?= site_url('welcome') ?>"><i-->
                    <!--            class="fa fa-dashboard"></i></a></li>-->
                    <?php if ($Owner) { ?>
<!--                        <li class="dropdown hidden-sm"><a class="btn tip" title="--><?//= lang('settings') ?><!--"-->
<!--                                                          data-placement="left"-->
<!--                                                          href="--><?//= site_url('system_settings') ?><!--"><i-->
<!--                                    class="fa fa-cogs"></i></a></li>-->
                    <?php } ?>
                    <!--<li class="dropdown hidden-xs"><a class="btn tip" title="<?= lang('calculator') ?>"-->
                    <!--                                  data-placement="left" href="#" data-toggle="dropdown"><i-->
                    <!--            class="fa fa-calculator"></i></a>-->
                    <!--    <ul class="dropdown-menu pull-right calc">-->
                    <!--        <li class="dropdown-content">-->
                    <!--            <span id="inlineCalc"></span>-->
                    <!--        </li>-->
                    <!--    </ul>-->
                    <!--</li>-->
                    <?php if ($info) { ?>
                        <!--<li class="dropdown hidden-sm"><a class="btn tip" title="<?= lang('notifications') ?>"-->
                        <!--                                  data-placement="left" href="#" data-toggle="dropdown"><i-->
                        <!--            class="fa fa-info-circle"></i><span-->
                        <!--            class="number blightOrange black"><?= sizeof($info) ?></span></a>-->
                        <!--    <ul class="dropdown-menu pull-right content-scroll">-->
                                <!--<li class="dropdown-header"><i-->
                                <!--        class="fa fa-info-circle"></i> <?= lang('notifications'); ?></li>-->
                        <!--        <li class="dropdown-content">-->
                        <!--            <div class="scroll-div">-->
                        <!--                <div class="top-menu-scroll">-->
                        <!--                    <ol class="oe">-->
                        <!--                    
                        <!--                        } ?>-->
                        <!--                    </ol>-->
                        <!--                </div>-->
                        <!--            </div>-->
                        <!--        </li>-->
                        <!--    </ul>-->
                        <!--</li>-->
                    <?php } ?>
                    <?php if ($events) { ?>
                        <li class="dropdown hidden-xs"><a class="btn tip" title="<?= lang('calendar') ?>"
                                                          data-placement="left" href="#" data-toggle="dropdown"><i
                                    class="fa fa-calendar"></i><span
                                    class="number blightOrange black"><?= sizeof($events) ?></span></a>
                            <ul class="dropdown-menu pull-right content-scroll">
                                <li class="dropdown-header"><i
                                        class="fa fa-calendar"></i> <?= lang('upcoming_events'); ?></li>
                                <li class="dropdown-content">
                                    <div class="top-menu-scroll">
                                        <ol class="oe">
                                            <?php foreach ($events as $event) {
                                                echo '<li><strong>' . date($dateFormats['php_sdate'], strtotime($event->date)) . ':</strong><br>' . $this->sma->decode_html($event->data) . '</li>';
                                            } ?>
                                        </ol>
                                    </div>
                                </li>
                                <li class="dropdown-footer"><a href="<?= site_url('calendar') ?>"
                                                               class="btn-block link"><i
                                            class="fa fa-calendar"></i> <?= lang('calendar') ?></a></li>
                            </ul>
                        </li>
                    <?php } else { ?>
                        <!--<li class="dropdown hidden-xs"><a class="btn tip" title="<?= lang('calendar') ?>" data-placement="left" href="<?= site_url('calendar') ?>"><i-->
                        <!--            class="fa fa-calendar"></i></a></li>-->
                    <?php } ?>
                    <!--<li class="dropdown hidden-sm">-->
                    <!--    <a class="btn tip" title="<?= lang('styles') ?>" data-placement="left" data-toggle="dropdown"-->
                    <!--       href="#">-->
                    <!--        <i class="fa fa-css3"></i>-->
                    <!--    </a>-->
                    <!--    <ul class="dropdown-menu pull-right">-->
                    <!--        <li class="bwhite noPadding">-->
                    <!--            <a href="#" id="fixed" class=""><i class="fa fa-angle-double-left"></i> <span-->
                    <!--                    id="fixedText">Fixed</span></a> <a href="#" id="cssLight" class="grey"><i-->
                    <!--                    class="fa fa-stop"></i> Grey</a> <a href="#" id="cssBlue" class="blue"><i-->
                    <!--                    class="fa fa-stop"></i> Blue</a> <a href="#" id="cssBlack" class="black"><i-->
                    <!--                    class="fa fa-stop"></i> Black</a>-->
                    <!--        </li>-->
                    <!--    </ul>-->
                    <!--</li>-->
                   <!-- <li class="dropdown hidden-xs">
                        <a class="btn tip" title="<?= lang('language') ?>" data-placement="left" data-toggle="dropdown"
                           href="#">
                            <img src="<?= base_url('assets/images/' . $Settings->language . '.png'); ?>" alt="">
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <?php $scanned_lang_dir = array_map(function ($path) {
                                return basename($path);
                            }, glob(APPPATH . 'language/*', GLOB_ONLYDIR));
                            foreach ($scanned_lang_dir as $entry) { ?>
                                <li><a href="<?= site_url('welcome/language/' . $entry); ?>"><img
                                            src="<?= base_url(); ?>assets/images/<?= $entry; ?>.png"
                                            class="language-img"> &nbsp;&nbsp;<?= ucwords($entry); ?></a></li>
                            <?php } ?>
                        </ul>

                    </li>-->
                    <?php if ($Owner && $Settings->update) { ?>
                        <li class="dropdown hidden-sm"><a class="btn blightOrange tip"
                                                          title="<?= lang('update_available') ?>"
                                                          data-placement="bottom" data-container="body"
                                                          href="<?= site_url('system_settings/updates') ?>"><i
                                class="fa fa-download"></i></a></li><?php } ?>
                    <?php if (($Owner || $Admin) && ($qty_alert_num > 0 || $exp_alert_num > 0)) { ?>
                        <li class="dropdown hidden-sm">
                            <a class="btn blightOrange tip" title="<?= lang('alerts') ?>" data-placement="left" data-toggle="dropdown"
                               href="#">
                                <i class="fa fa-exclamation-triangle"></i>
                            </a>
                            <ul class="dropdown-menu pull-right">
                               <!-- <li>
                                    <a href="<?= site_url('reports/quantity_alerts') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $qty_alert_num; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('quantity_alerts') ?></span>
                                    </a>
                                </li> -->
                                <li>
                                    <a href="<?= site_url('reports/expiry_alerts') ?>" class="">
                                        <span class="label label-danger pull-right" style="margin-top:3px;"><?= $exp_alert_num; ?></span>
                                        <span style="padding-right: 35px;"><?= lang('expiry_alerts') ?></span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    <?php } ?>
                    <?php if (POS) { ?>
                       <!-- <li class="dropdown hidden-xs"><a class="btn bdarkGreen tip" title="<?= lang('pos') ?>"
                                                          data-placement="left" href="<?= site_url('pos') ?>"><i
                                    class="fa fa-th-large"></i> <span class="padding05"><?= lang('pos') ?></span></a>
                        </li>
                        <?php if ($Owner) { ?>
                            <li class="dropdown">
                                <a class="btn bdarkGreen tip" id="today_profit" title="<span><?= lang('today_profit') ?></span>" data-placement="bottom" data-html="true" href="<?= site_url('pos/today_profit') ?>" data-toggle="modal" data-target="#myModal">
                                    <i class="fa fa-hourglass-2"></i>
                                </a>
                            </li>-->
                        <?php } ?>
                    <?php } ?>
                    <?php if ($Owner || $Admin) { ?>
                        <?php if (POS) { ?>
                           <!-- <li class="dropdown hidden-xs"><a class="btn bblue tip"
                                                              title="<?= lang('list_open_registers') ?>"
                                                              data-placement="bottom"
                                                              href="<?= site_url('pos/registers') ?>"><i
                                        class="fa fa-list"></i></a></li>
                        <?php } ?>
                        <li class="dropdown hidden-xs"><a class="btn bred tip" title="<?= lang('clear_ls') ?>"
                                                          data-placement="bottom" id="clearLS" href="#"><i
                                    class="fa fa-eraser"></i></a></li>-->
                    <?php } ?>
                </ul>
            </div>
        </div>
    </header>

    <div class="container bblack" id="container">
        <div class="row" id="main-con">
            <div id="sidebar-left" class="col-lg-2 col-md-2">
                <div class="sidebar-nav nav-collapse collapse navbar-collapse" id="sidebar_menu">
                    <ul class="nav main-menu">
                       <li class="mm_welcome"><a href="<?= site_url() ?>"><i class="fa fa-dashboard"></i><span
                                    class="text"> <?= lang('dashboard'); ?></span></a></li>
                                    
                                   

                        <?php
                        if ($Owner) {
                            ?>

                            <li class="mm_products">
                                <a class="dropmenu" href="#"><i class="fa fa-barcode"></i><span
                                        class="text"> <?= lang('products'); ?> </span> <span
                                        class="chevron closed"></span></a>
                                <ul>
                                    <li id="products_index"><a class="submenu" href="<?= site_url('products'); ?>"><i
                                                class="fa fa-barcode"></i><span
                                                class="text"> <?= lang('list_products'); ?></span></a></li>
                                    <li id="products_add"><a class="submenu" href="<?= site_url('products/add'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('add_product'); ?></span></a></li>
                                                
                                    <li id="products_add"><a class="submenu" href="<?= site_url('products/add_kitchen'); ?>"><i
                                                    class="fa fa-plus-circle"></i><span
                                                    class="text"> <?= lang('Add_Outright_Product'); ?></span></a></li>
                                    <li id="products_sheet" style="display:none"><a class="submenu"
                                                               href="<?= site_url('products/print_barcodes'); ?>"><i
                                                class="fa fa-tags"></i><span
                                                class="text"> <?= lang('print_barcodes'); ?></span></a></li>
                                    <li id="products_print_labels" style="display:none"><a class="submenu"
                                                                      href="<?= site_url('products/print_labels'); ?>"><i
                                                class="fa fa-tags"></i><span
                                                class="text"> <?= lang('print_labels'); ?></span></a></li>
                                    <li id="products_import_csv"><a class="submenu"
                                                                    href="<?= site_url('products/import_csv'); ?>"><i
                                                class="fa fa-file-text"></i><span
                                                class="text"> <?= lang('import_products'); ?></span></a></li>
                                    <li style="display:none" id="products_update_price"><a class="submenu"
                                                                      href="<?= site_url('products/update_price'); ?>"><i
                                                class="fa fa-money"></i><span
                                                class="text"> <?= lang('update_price'); ?></span></a></li>
                                    <li id="products_quantity_adjustments" style="display:none"><a class="submenu"
                                                                         href="<?= site_url('products/quantity_adjustments'); ?>"><i
                                                class="fa fa-filter"></i><span
                                                class="text"> <?= lang('quantity_adjustments'); ?></span></a></li>
                                                  <li id="system_settings_categories"><a
                                                href="<?= site_url('system_settings/categories') ?>"><i
                                                    class="fa fa-folder-open"></i><span
                                                    class="text"> <?= lang('categories'); ?></span></a></li>
                                        <li id="system_settings_variants" style="display:none"><a
                                                href="<?= site_url('system_settings/variants') ?>"><i
                                                    class="fa fa-tags"></i><span
                                                    class="text"> <?= lang('variants'); ?></span></a></li>
                                </ul>
                            </li>

                            <!--<li class="mm_routes">
                                <a class="dropmenu" href="#"><i class="fa fa-link"></i><span
                                            class="text"> <?/*= lang('Routes'); */?> </span> <span
                                            class="chevron closed"></span></a>
                                <ul>
                                    <li id="routes_index"><a class="submenu" href="<?/*= site_url('routes'); */?>"><i
                                                    class="fa fa-list"></i><span
                                                    class="text"> <?/*= lang('List_Routes'); */?></span></a></li>
                                    <li id="routes_add"><a class="submenu" href="<?/*= site_url('routes/add'); */?>"><i
                                                    class="fa fa-plus-circle"></i><span
                                                    class="text"> <?/*= lang('Add_Route'); */?></span></a></li>
                                </ul>
                            </li>

                            <li class="mm_vehicles">
                                <a class="dropmenu" href="#"><i class="fa fa-bus"></i><span
                                            class="text"> <?/*= lang('Vehicles'); */?> </span> <span
                                            class="chevron closed"></span></a>
                                <ul>
                                    <li id="vehicles_index"><a class="submenu" href="<?/*= site_url('vehicles'); */?>"><i
                                                    class="fa fa-list"></i><span
                                                    class="text"> <?/*= lang('List_Vehicles'); */?></span></a></li>
                                    <li id="vehicles_add"><a class="submenu" href="<?/*= site_url('vehicles/add'); */?>"><i
                                                    class="fa fa-plus-circle"></i><span
                                                    class="text"> <?/*= lang('Add_Vehicle'); */?></span></a></li>
                                </ul>
                            </li>

                            <li class="mm_counties">
                                <a class="dropmenu" href="#"><i class="fa fa-building"></i><span
                                            class="text"> <?/*= lang('Counties'); */?> </span> <span
                                            class="chevron closed"></span></a>
                                <ul>
                                    <li id="counties_index"><a class="submenu" href="<?/*= site_url('counties'); */?>"><i
                                                    class="fa fa-list"></i><span
                                                    class="text"> <?/*= lang('List_Counties'); */?></span></a></li>
                                    <li id="counties_add"><a class="submenu" href="<?/*= site_url('counties/add'); */?>"><i
                                                    class="fa fa-plus-circle"></i><span
                                                    class="text"> <?/*= lang('Add_County'); */?></span></a></li>
                                </ul>
                            </li>-->

                            <li class="mm_towns">
                                <a class="dropmenu" href="#"><i class="fa fa-building-o"></i><span
                                            class="text"> <?= lang('Towns'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                <ul>
                                    <li id="towns_index"><a class="submenu" href="<?= site_url('towns'); ?>"><i
                                                    class="fa fa-list"></i><span
                                                    class="text"> <?= lang('List_Towns'); ?></span></a></li>
                                    <li id="towns_add"><a class="submenu" href="<?= site_url('towns/add'); ?>"><i
                                                    class="fa fa-plus-circle"></i><span
                                                    class="text"> <?= lang('Add_Town'); ?></span></a></li>
                                </ul>
                            </li>

                            <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'settings' ? '' : 'mm_pos' ?>">
                                <a class="dropmenu" href="#"><i class="fa fa-heart"></i><span
                                        class="text"> <?= lang('sales'); ?> </span> <span class="chevron closed"></span></a>
                                <ul>

                                    <li id="sales_index"><a class="submenu" href="<?= site_url('sales'); ?>"><i
                                                class="fa fa-heart"></i><span
                                                class="text"> <?= lang('list_sales'); ?></span></a></li>
                                                <li id="reports_sales"><a href="<?= site_url('sales') ?>"><i
                                              class="fa fa-heart"></i><span
                                            class="text"> <?= lang('Sales_Filter'); ?></span></a></li>
                                    <?php if (POS) { ?>
                                        <li id="pos_sales"><a class="submenu" href="<?= site_url('pos/sales'); ?>"><i
                                                    class="fa fa-heart"></i><span
                                                    class="text"> <?= lang('pos_sales'); ?></span></a></li>
                                                      <?php
                                        if ($Owner || $Admin) {?>
                                        <li id="pos_sales"><a class="submenu" href="<?= site_url('pos/deletedsales'); ?>"><i
                                                               class="fa fa-trash-o"></i><span
                                                               class="text"> <?= lang('Deleted_Sales'); ?></span></a></li>
                                        <?php } ?>
                                    <?php } ?>
                                    <li id="sales_add"><a class="submenu" href="<?= site_url('sales/add'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('add_sale'); ?></span></a></li>
                                    <li id="sales_sale_by_csv"><a class="submenu"
                                                                          href="<?= site_url('sales/sale_by_csv'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('add_sale_by_csv'); ?></span></a></li>
                                                  <li id="sales_sale_by_csv"><a class="submenu"
                                                                          href="<?= site_url('sales/import_actuals'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('import_actual'); ?></span></a></li>

                                    <li id="sales_gift_cards"><a class="submenu"
                                                                 href="<?= site_url('sales/gift_cards'); ?>"><i
                                                class="fa fa-gift"></i><span
                                                class="text"> <?= lang('gift_cards'); ?></span></a></li>



                                </ul>
                            </li>

                           <!-- <li class="mm_quotes">
                                <a class="dropmenu" href="#"><i class="fa fa-heart-o"></i><span
                                        class="text"> <?= lang('quotes'); ?> </span> <span
                                        class="chevron closed"></span></a>
                                <ul>

                                    <li id="quotes_index"><a class="submenu" href="<?= site_url('quotes'); ?>"><i
                                                class="fa fa-heart-o"></i><span
                                                class="text"> <?= lang('list_quotes'); ?></span></a></li>
                                    <li id="quotes_add"><a class="submenu" href="<?= site_url('quotes/add'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('add_quote'); ?></span></a></li>
                                </ul>
                            </li>-->

                            <li class="mm_purchases">
                                <a class="dropmenu" href="#"><i class="fa fa-star"></i><span
                                        class="text"> <?= lang('Stock'); ?> </span> <span
                                        class="chevron closed"></span></a>
                                <ul>

                                     <!--<li id="purchases_index"><a class="submenu" href="<?= site_url('purchases'); ?>"><i
                                                class="fa fa-star"></i><span
                                                class="text"> <?= lang('List Stock'); ?></span></a></li>-->
                                    <li id="reports_purchases"><a href="<?= site_url('purchases') ?>"><i
                                                class="fa fa-star"></i><span
                                                class="text"> <?= lang('List Stock'); ?></span></a></li>
                                    <li id="purchases_add"><a class="submenu"
                                                              href="<?= site_url('purchases/add'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('add_purchase'); ?></span></a></li>
                                    <li id="purchases_purchase_by_csv"><a class="submenu"
                                                                          href="<?= site_url('purchases/purchase_by_csv'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('add_purchase_by_csv'); ?></span></a></li>
                                   <!-- <li id="purchases_expenses"><a class="submenu"
                                                                   href="<?= site_url('purchases/expenses'); ?>"><i
                                                class="fa fa-dollar"></i><span
                                                class="text"> <?= lang('expenses'); ?></span></a></li>
                                    <li id="purchases_add_expense"><a class="submenu"
                                                                      href="<?= site_url('purchases/add_expense'); ?>"
                                                                      data-toggle="modal" data-target="#myModal"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('add_expense'); ?></span></a></li>-->
                                </ul>
                            </li>

                           <li class="mm_budget">
                                <a class="dropmenu" href="#"><i class="fa fa-star-o"></i><span
                                        class="text"> <?= lang('Budgets'); ?> </span> <span
                                        class="chevron closed"></span></a>
                                <ul>

                                   <!--<li id="budget_index"><a class="submenu" href="<?= site_url('budgets'); ?>"><i
                                                class="fa fa-star-o"></i><span
                               class="text"> <?= lang('list_budgets'); ?></span></a></li>-->
                                    <li id="reports_purchases"><a href="<?= site_url('reports/budget') ?>"><i
                                                class="fa fa-star"></i><span
                                                class="text"> <?= lang('list_budgets'); ?></span></a></li> 
                                    
                                  <!--   <li class="mm_budget">
                                <a class="dropmenu" href="#"><i class="fa fa-star-o"></i><span
                                        class="text"> <?= lang('Country Level'); ?> </span> <span
                                        class="chevron closed"></span></a>
                                <ul>
                                    <li id="budget_index"><a class="submenu" href="<?= site_url('budgets/country_sso_budget'); ?>"><i
                                                class="fa fa-star-o"></i><span
                                                class="text"> <?= lang('SSO'); ?></span></a></li>
                                                
                                                <li id="budget_index"><a class="submenu" href="<?= site_url('budgets/country_pso_budget'); ?>"><i
                                                class="fa fa-star-o"></i><span
                                                class="text"> <?= lang('PSO'); ?></span></a></li>
                                    
                                    </ul>
                                    </li>   -->
                                                
                                    <!--<li id="budget_index"><a class="submenu" href="<?= site_url('budgets'); ?>"><i-->
                                    <!--            class="fa fa-star-o"></i><span-->
                                    <!--            class="text"> <?= lang('list_transfers1'); ?></span></a>-->
                                                
                                    <!--            </li>-->
                                    
                                    <!--<li id="budget_index"><a class="submenu" href="<?= site_url('budgets'); ?>"><i-->
                                    <!--            class="fa fa-star-o"></i><span-->
                                    <!--            class="text"> <?= lang('list_transfers2'); ?></span></a></li>-->
                                    
                                    <!--<li id="budget_index"><a class="submenu" href="<?= site_url('budgets/customer_sso_budget'); ?>"><i
                                                class="fa fa-star-o"></i><span
                                                class="text"> <?= lang('Agent Level(SSO)'); ?></span></a></li>-->
                                                
                                    <!--<li id="budget_index"><a class="submenu" href="<?= site_url('budgets'); ?>"><i-->
                                    <!--            class="fa fa-star-o"></i><span-->
                                    <!--            class="text"> <?= lang('SalesForce Level(SSO)'); ?></span></a></li>-->
                                
                                
                                    <li id="budget_purchase_by_csv"><a class="submenu"
                                                                         href="<?= site_url('budgets/import_budgets'); ?>"><i
                                            class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('Import_Budget_or_Forecast'); ?></span></a></li>
                                </ul>
                            </li>

                        <li class="mm_counties">
                            <a class="dropmenu" href="#"><i class="fa fa-building"></i><span
                                        class="text"> <?= lang('Counties'); ?> </span> <span
                                        class="chevron closed"></span></a>
                            <ul>
                                <li id="counties_index"><a class="submenu" href="<?= site_url('counties'); ?>"><i
                                                class="fa fa-list"></i><span
                                                class="text"> <?= lang('List_Counties'); ?></span></a></li>
                                <li id="counties_add"><a class="submenu" href="<?= site_url('counties/add'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('Add_County'); ?></span></a></li>
                            </ul>
                        </li>

                        <li class="mm_towns">
                            <a class="dropmenu" href="#"><i class="fa fa-building-o"></i><span
                                        class="text"> <?= lang('Towns'); ?> </span> <span
                                        class="chevron closed"></span></a>
                            <ul>
                                <li id="towns_index"><a class="submenu" href="<?= site_url('towns'); ?>"><i
                                                class="fa fa-list"></i><span
                                                class="text"> <?= lang('List_Towns'); ?></span></a></li>
                                <li id="towns_add"><a class="submenu" href="<?= site_url('towns/add'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('Add_Town'); ?></span></a></li>
                            </ul>
                        </li>
                        
                        <li class="mm_suppliers">
                            <a class="dropmenu" href="#"><i class="fa fa-users"></i><span
                                        class="text"> <?= lang('Purchases'); ?> </span> <span
                                        class="chevron closed"></span></a>
                            <ul>

                                <li id="suppliers_index" <?php if($this->Distributor) echo 'style="display:none"'; ?>><a class="submenu" href="<?= site_url('suppliers'); ?>"><i
                                                class="fa fa-users"></i><span
                                                class="text"> <?= lang('List_Suppliers'); ?></span></a></li>
                                <li id="suppliers_index" <?php if($this->Distributor) echo 'style="display:none"'; ?>><a class="submenu" href="<?= site_url('purchases/add_gas'); ?>"><i
                                                class="fa fa-users"></i><span
                                                class="text"> <?= lang('Add_LPG_Purchase'); ?></span></a></li>                
                            </ul>
                        </li>

                        <li class="mm_distributors">
                            <a class="dropmenu" href="#"><i class="fa fa-users"></i><span
                                        class="text"> <?= lang('Distributors'); ?> </span> <span
                                        class="chevron closed"></span></a>
                            <ul>

                                <li id="customers_index" <?php if($this->Distributor) echo 'style="display:none"'; ?>><a class="submenu" href="<?= site_url('customers'); ?>"><i
                                                class="fa fa-users"></i><span
                                                class="text"> <?= lang('list_customers'); ?></span></a></li>
                               <li id="customers_index"><a class="submenu" href="<?= site_url('customers/select_county'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('select_customers_by_counties'); ?></span></a></li>
                               <li id="customers_index"><a class="submenu" href="<?= site_url('customers/getshops'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('shops_days_served_summary'); ?></span></a></li>                
                                                
                            </ul>
                        </li>


                            <li class="mm_notifications"><a class="submenu" href="<?= site_url('notifications'); ?>"><i
                                        class="fa fa-info-circle"></i><span
                                        class="text"> <?= lang('notifications'); ?></span></a></li>
                            <?php if ($Owner) { ?>
                                <li class="mm_system_settings <?= strtolower($this->router->fetch_method()) != 'settings' ? '' : 'mm_pos' ?>">
                                    <a class="dropmenu" href="#"><i class="fa fa-cog"></i><span
                                            class="text"> <?= lang('settings'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="system_settings_index"><a href="<?= site_url('system_settings') ?>"><i
                                                    class="fa fa-cog"></i><span
                                                    class="text"> <?= lang('system_settings'); ?></span></a></li>
                                        <?php if (POS) { ?>
                                            <!--<li id="pos_settings"><a href="<?= site_url('pos/settings') ?>"><i
                                                        class="fa fa-th-large"></i><span
                                                        class="text"> <?= lang('pos_settings'); ?></span></a></li>-->
                                        <?php } ?>
                                        <li id="system_settings_change_logo"><a
                                                href="<?= site_url('system_settings/change_logo') ?>"
                                                data-toggle="modal" data-target="#myModal"><i
                                                    class="fa fa-upload"></i><span
                                                    class="text"> <?= lang('change_logo'); ?></span></a></li>
<!--                                        <li id="system_settings_currencies"><a-->
<!--                                                href="--><?//= site_url('system_settings/currencies') ?><!--"><i-->
<!--                                                    class="fa fa-money"></i><span-->
<!--                                                    class="text"> --><?//= lang('Country_Configuration'); ?><!--</span></a></li>-->
<!--                                                    <li id="system_settings_currencies"><a-->
<!--                                                href="--><?//= site_url('system_settings/conversion') ?><!--"><i-->
<!--                                                    class="fa fa-money"></i><span-->
<!--                                                    class="text"> --><?//= lang('Exchange_Rates'); ?><!--</span></a></li>-->
<!--                                        <li id="system_settings_currencies"><a-->
<!--                                                href="--><?//= site_url('system_settings/bu') ?><!--"><i-->
<!--                                                    class="fa fa-cog"></i><span-->
<!--                                                    class="text"> --><?//= lang('BU_Configuration'); ?><!--</span></a></li>            -->
<!--                                        <li id="system_settings_currencies"><a-->
<!--                                                href="--><?//= site_url('system_settings/teams') ?><!--"><i-->
<!--                                                    class="fa fa-star"></i><span-->
<!--                                                    class="text"> --><?//= lang('Vehicles'); ?><!--</span></a></li>-->
<!--                                                    <li id="system_settings_currencies"><a-->
<!--                                                href="--><?//= site_url('system_settings/dsm') ?><!--"><i-->
<!--                                                    class="fa fa-star"></i><span-->
<!--                                                    class="text"> --><?//= lang('DSM'); ?><!--</span></a></li>-->
<!--                                                    <li id="system_settings_currencies"><a-->
<!--                                                href="--><?//= site_url('system_settings/msr') ?><!--"><i-->
<!--                                                    class="fa fa-star"></i><span-->
<!--                                                    class="text"> --><?//= lang('S.Person'); ?><!--</span></a></li>-->
<!--                                                      <li id="system_settings_currencies"><a-->
<!--                                                href="--><?//= site_url('system_settings/clusters') ?><!--"><i-->
<!--                                                    class="fa fa-star"></i><span-->
<!--                                                    class="text"> --><?//= lang('Clusters'); ?><!--</span></a></li>-->
                                      <!--  <li id="system_settings_customer_groups"><a
                                                href="<?= site_url('system_settings/customer_groups') ?>"><i
                                                    class="fa fa-chain"></i><span
                                                    class="text"> <?= lang('customer_groups'); ?></span></a></li>-->
                                                    
                                    <!--<li id="system_settings_customer_groups"><a-->
                                    <!--            href="<?= site_url('system_settings/alignment_groups') ?>"><i-->
                                    <!--                class="fa fa-chain"></i><span-->
                                    <!--                class="text"> <?= lang('alignments_groups'); ?></span></a></li>-->
                                      
                                        <li id="system_settings_tax_rates"><a
                                                href="<?= site_url('system_settings/tax_rates') ?>"><i
                                                    class="fa fa-plus-circle"></i><span
                                                    class="text"> <?= lang('tax_rates'); ?></span></a></li>
                                        <!--<li id="system_settings_warehouses"><a
                                                href="<?= site_url('system_settings/warehouses') ?>"><i
                                                    class="fa fa-building-o"></i><span
                                                    class="text"> <?= lang('warehouses'); ?></span></a></li>
                                        <li id="system_settings_template"><a
                                                href="<?= site_url('system_settings/email_templates') ?>"><i
                                                    class="fa fa-envelope"></i><span
                                                    class="text"> <?= lang('email_templates'); ?></span></a></li>-->
                                                    <?php if ($Owner) { ?>
                                        <li id="auth_users"><a class="submenu" href="<?= site_url('users'); ?>"><i
                                                    class="fa fa-users"></i><span
                                                    class="text"> <?= lang('list_users'); ?></span></a></li>
                                        <li id="auth_create_user" style="display:none"><a class="submenu"
                                                                     href="<?= site_url('users/create_user'); ?>"><i
                                                    class="fa fa-user-plus"></i><span
                                                    class="text"> <?= lang('new_user'); ?></span></a></li>
                                      <!--  <li id="billers_index" style="display:none"><a class="submenu" href="<?= site_url('billers'); ?>"><i
                                                    class="fa fa-users"></i><span
                                                    class="text"> <?= lang('list_billers'); ?></span></a></li>-->
                                        <!--<li id="billers_index" style="display:none"><a class="submenu" href="<?= site_url('billers/add'); ?>"
                                                                  data-toggle="modal" data-target="#myModal"><i
                                                    class="fa fa-plus-circle"></i><span
                                   
                                        class="text"> <?= lang('add_biller'); ?></span></a></li>-->
                                                    
                                                     <li id="system_settings_user_groups"><a
                                                href="<?= site_url('system_settings/audit') ?>"><i
                                                    class="fa fa-key"></i><span
                                                        class="text"> <?= lang('User_login_trail'); ?></span></a></li>
                                    <?php } ?>
                                        <li id="system_settings_user_groups"><a
                                                href="<?= site_url('system_settings/user_groups') ?>"><i
                                                    class="fa fa-key"></i><span
                                                    class="text"> <?= lang('group_permissions'); ?></span></a></li>
                                        <li id="system_settings_backups"><a
                                                href="<?= site_url('system_settings/backups') ?>"><i
                                                    class="fa fa-database"></i><span
                                                    class="text"> <?= lang('backups'); ?></span></a></li>
                                      <!--  <li id="system_settings_updates"><a
                                                href="<?= site_url('system_settings/updates') ?>"><i
                                                    class="fa fa-upload"></i><span
                                                    class="text"> <?= lang('updates'); ?></span></a></li>-->
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="mm_reports">
                                 <!--reports/mashariki_report-->
<!--                                <a " href="reports/mashariki_rpt"><i class="fa fa-bar-chart-o"></i><span-->
<!--                                        class="text"> --><?//= lang('Mashariki reports'); ?><!-- </span> <span-->
<!--                                        class="chevron closed"></span></a>-->
                                <ul>
                                    <!--<li id="reports_index"><a href="<?= site_url('reports') ?>"><i-->
                                    <!--            class="fa fa-bars"></i><span-->
                                    <!--            class="text"> <?= lang('overview_chart'); ?></span></a></li>-->
                                    <!--<li id="reports_warehouse_stock"><a-->
                                    <!--        href="<?= site_url('reports/warehouse_stock') ?>"><i-->
                                    <!--            class="fa fa-building"></i><span-->
                                    <!--            class="text"> <?= lang('warehouse_stock'); ?></span></a></li>-->
                                    <?php if (POS) { ?>
                                      <!--  <li id="reports_register"><a href="<?= site_url('reports/register') ?>"><i
                                                    class="fa fa-th-large"></i><span
                                                    class="text"> <?= lang('register_report'); ?></span></a></li>-->
                                    <?php } ?>
                                    <!--<li id="reports_quantity_alerts"><a-->
                                    <!--        href="<?= site_url('reports/quantity_alerts') ?>"><i-->
                                    <!--            class="fa fa-bar-chart-o"></i><span-->
                                    <!--            class="text"> <?= lang('product_quantity_alerts'); ?></span></a></li>-->
                                    <?php if ($this->Settings->product_expiry) { ?>
                                        <li id="reports_expiry_alerts"><a
                                            href="<?= site_url('reports/expiry_alerts') ?>"><i
                                                class="fa fa-bar-chart-o"></i><span
                                                class="text"> <?= lang('product_expiry_alerts'); ?></span></a>
                                        </li><?php } ?>
                                    <!--<li id="reports_products"><a href="<?= site_url('reports/products') ?>"><i-->
                                    <!--            class="fa fa-barcode"></i><span-->
                                    <!--            class="text"> <?= lang('products_report'); ?></span></a></li>-->
                                                
                                    <!--<li id="reports_categories"><a href="<?= site_url('reports/categories') ?>"><i-->
                                    <!--            class="fa fa-folder-open"></i><span-->
                                    <!--            class="text"> <?= lang('categories_report'); ?></span></a></li>-->
                                   <!-- <li id="reports_daily_sales"><a href="<?= site_url('reports/daily_sales') ?>"><i
                                                class="fa fa-calendar-o"></i><span
                                                class="text"> <?= lang('daily_sales'); ?></span></a></li>
                                                  <li id="reports_daily_sales"><a href="<?= site_url('pos/closes_register/'.$this->session->userdata('user_id')) ?>"><i
                                                class="fa fa-calendar-o"></i><span
                                                class="text"> <?= lang('zed_report'); ?></span></a></li>
                                    <li id="reports_monthly_sales"><a href="<?= site_url('reports/monthly_sales') ?>"><i
                                                class="fa fa-calendar-o"></i><span
                                                class="text"> <?= lang('monthly_sales'); ?></span></a></li>-->
                                    <!--<li id="reports_sales"><a href="<?= site_url('reports/sales') ?>"><i-->
                                    <!--            class="fa fa-heart"></i><span-->
                                    <!--            class="text"> <?= lang('sales_report'); ?></span></a></li>-->
                                   <!-- <li id="reports_payments"><a href="<?= site_url('reports/payments') ?>"><i
                                                class="fa fa-money"></i><span
                                                class="text"> <?= lang('payments_report'); ?></span></a></li>
                                                       <li id="reports_payments"><a href="<?= site_url('sales/graph') ?>"><i
                                                class="fa fa-bar-chart-o"></i><span
                                                class="text"> <?= lang('Graphical_Summary'); ?></span></a></li>
                                    <li id="reports_profit_loss"><a href="<?= site_url('reports/profit_loss') ?>"><i
                                                class="fa fa-money"></i><span
                                                class="text"> <?= lang('profit_and_loss'); ?></span></a></li>-->
                                    <!--<li id="reports_purchases"><a href="<?= site_url('reports/purchases') ?>"><i-->
                                    <!--            class="fa fa-star"></i><span-->
                                    <!--            class="text"> <?= lang('Stock_report'); ?></span></a></li>-->
                                    <!--             <li id="reports_budget_report"><a href="<?= site_url('reports/budget') ?>"><i-->
                                                
                                    <!--            class="fa fa-star"></i><span-->
                                    <!--            class="text"> <?= lang('Budget_report'); ?></span></a></li>-->
                                    <!--<li id="reports_customer_report"><a href="<?= site_url('reports/customers') ?>"><i-->
                                                <!--class="fa fa-users"></i><span-->
                                                <!--class="text"> <?= lang('customers_report'); ?></span></a></li>-->
                                                
                                    <!--<li id="reports_customer_report"><a href="<?= site_url('reports/customer_details') ?>"><i-->
                                    <!--            class="fa fa-users"></i><span-->
                                    <!--            class="text"> <?= lang('Agents report'); ?></span></a></li>-->
                                                 <!--<li id="reports_customer_report"><a href="<?= site_url('reports/mashariki_report') ?>"><i-->
                                    <!--            class="fa fa-users"></i><span-->
                                    <!--            class="text"> <?= lang('Mashariki report'); ?></span></a></li>-->
                                                
                                    <!--<li id="reports_supplier_report"><a href="<?= site_url('reports/suppliers') ?>"><i-->
                                    <!--            class="fa fa-users"></i><span-->
                                    <!--            class="text"> <?= lang('suppliers_report'); ?></span></a></li>-->
                                <!--    <li id="reports_staff_report"><a href="<?= site_url('reports/users') ?>"><i-->
                                <!--                class="fa fa-users"></i><span-->
                                <!--                class="text"> <?= lang('staff_report'); ?></span></a></li>-->
                                <!--</ul>-->
                            </li>

                        <?php
                        } else { // not owner and not admin
                            ?>
                            <?php if ($GP['products-index']) { ?>
                                <li class="mm_products">
                                    <a class="dropmenu" href="#"><i class="fa fa-barcode"></i><span
                                            class="text"> <?= lang('products'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="products_index"><a class="submenu"
                                                                   href="<?= site_url('products'); ?>"><i
                                                    class="fa fa-barcode"></i><span
                                                    class="text"> <?= lang('list_products'); ?></span></a></li>
                                        <?php if ($GP['products-add']) { ?>
                                            <li id="products_add"><a class="submenu"
                                                                     href="<?= site_url('products/add'); ?>"><i
                                                        class="fa fa-plus-circle"></i><span
                                                        class="text"> <?= lang('add_product'); ?></span></a></li>
                                        <?php } ?>
                                        <li id="products_sheet"><a class="submenu"
                                                                   href="<?= site_url('products/print_barcodes'); ?>"><i
                                                    class="fa fa-tags"></i><span
                                                    class="text"> <?= lang('print_barcodes'); ?></span></a></li>
                                        <li id="products_print_labels"><a class="submenu"
                                                                          href="<?= site_url('products/print_labels'); ?>"><i
                                                    class="fa fa-tags"></i><span
                                                    class="text"> <?= lang('print_labels'); ?></span></a></li>
                                        <?php if ($GP['products-edit']) { ?>
                                            <li id="products_quantity_adjustments"><a class="submenu"
                                                                                 href="<?= site_url('products/quantity_adjustments'); ?>"><i
                                                        class="fa fa-filter"></i><span
                                                        class="text"> <?= lang('quantity_adjustments'); ?></span></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($GP['products-discount']) { ?>
                                <li class="mm_routes">
                                    <a class="dropmenu" href="#"><i class="fa fa-link"></i><span
                                                class="text"> <?= lang('Product_Discount'); ?> </span> <span
                                                class="chevron closed"></span></a>
                                    <ul>
                                        <li id="products_index"><a class="submenu"
                                                                   href="<?= site_url('products/index2'); ?>"><i
                                                        class="fa fa-barcode"></i><span
                                                        class="text"> <?= lang('list_products'); ?></span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($GP['routes-index']) { ?>
                                <li class="mm_routes">
                                    <a class="dropmenu" href="#"><i class="fa fa-link"></i><span
                                                class="text"> <?= lang('Routes'); ?> </span> <span
                                                class="chevron closed"></span></a>
                                    <ul>
                                        <li id="routes_index"><a class="submenu"
                                                                   href="<?= site_url('routes'); ?>"><i
                                                        class="fa fa-barcode"></i><span
                                                        class="text"> <?= lang('List_Routes'); ?></span></a></li>
                                        <?php if ($GP['routes-add']) { ?>
                                            <!--<li id="routes_plan"><a class="submenu"
                                                                     href="<?= site_url('vehicles/updateAllRoutes'); ?>"><i
                                                            class="fa fa-plus-circle"></i><span
                                                            class="text"> <?= lang('Update_All_Routes_Plans'); ?></span></a></li>-->
                                        <?php } ?>
                                        <?php if ($GP['routes-add']) { ?>
                                            <li id="routes_view"><a class="submenu"
                                                                     href="<?= site_url('vehicles/routeplan'); ?>"><i
                                                            class="fa fa-plus-circle"></i><span
                                                            class="text"> <?= lang('View_Shops_Allocations'); ?></span></a></li>
                                        <?php } ?>
                                        <?php if ($GP['routes-add']) { ?>
                                            <li id="routes_view"><a class="submenu"
                                                                     href="<?= site_url('vehicles/routeplanByDay'); ?>"><i
                                                            class="fa fa-plus-circle"></i><span
                                                            class="text"> <?= lang('Daily_Shops_Allocations'); ?></span></a></li>
                                        <?php } ?>
                                        <?php if ($GP['routes-add']) { ?>
                                            <li id="routes_view"><a class="submenu"
                                                                     href="<?= site_url('vehicles/disabledRouteplanByDay'); ?>"><i
                                                            class="fa fa-plus-circle"></i><span
                                                            class="text"> <?= lang('Daily_Disabled_Allocations'); ?></span></a></li>
                                        <?php } ?>
                                        <?php if ($GP['routes-add']) { ?>
                                            <!--<li id="routes_view"><a class="submenu"
                                                                     href="<?= site_url('vehicles/routeStartingPoints'); ?>"><i
                                                            class="fa fa-plus-circle"></i><span
                                                            class="text"> <?= lang('Route_Starting_Points'); ?></span></a></li>-->
                                        <?php } ?>
                                        <?php if ($GP['routes-add']) { ?>
                                            <li id="routes_view"><a class="submenu"
                                                                     href="<?= site_url('vehicles/tomorrowRouteplan'); ?>"><i
                                                            class="fa fa-plus-circle"></i><span
                                                            class="text"> <?= lang('Tomorrow_Allocations'); ?></span></a></li>
                                        <?php } ?>
                                        <?php if ($GP['routes-add']) { ?>
                                            <li id="routes_add"><a class="submenu"
                                                                     href="<?= site_url('vehicles/vehiclesForManualPlannig'); ?>"><i
                                                            class="fa fa-plus-circle"></i><span
                                                            class="text"> <?= lang('Manual Route Planning'); ?></span></a></li>
                                        <?php } ?>
                                        <?php if ($GP['routes-add']) { ?>
                                            <li id="routes_add"><a class="submenu"
                                                                     href="<?= site_url('routes/add'); ?>"><i
                                                            class="fa fa-plus-circle"></i><span
                                                            class="text"> <?= lang('Add_Route'); ?></span></a></li>
                                        <?php } ?>
                                        
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($GP['vehicles-index']) { ?>
                                <li class="mm_vehicles">
                                    <a class="dropmenu" href="#"><i class="fa fa-car"></i><span
                                                class="text"> <?= lang('Vehicles'); ?> </span> <span
                                                class="chevron closed"></span></a>
                                    <ul>
                                        <li id="vehicles_index"><a class="submenu"
                                                                 href="<?= site_url('vehicles'); ?>"><i
                                                        class="fa fa-barcode"></i><span
                                                        class="text"> <?= lang('List_Vehicles'); ?></span></a></li>
                                        <?php if ($GP['vehicles-add']) { ?>
                                            <li id="vehicles_add"><a class="submenu"
                                                                   href="<?= site_url('vehicles/add'); ?>"><i
                                                            class="fa fa-plus-circle"></i><span
                                                            class="text"> <?= lang('Add_Vehicle'); ?></span></a></li>
                                        <?php } ?>
                                        <?php if ($GP['vehicles-add-stock']) { ?>
                                        <li id="vehicles_stock"><a class="submenu"
                                                                   href="<?= site_url('purchases/add2'); ?>"><i
                                                        class="fa fa-plus-circle"></i><span
                                                        class="text"> <?= lang('Add_Vehicle_Stock'); ?></span></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            
                            <?php if ($GP['distributors-index-targets']) { ?>
                              <li class="mm_targets">
                                    <a class="dropmenu" href="#"><i class="fa fa-bullseye"></i><span
                                            class="text"> <?= lang('Targets'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('customers/targets'); ?>"><i
                                                    class="fa fa-bullseye"></i><span
                                                    class="text"> <?= lang('List_Targets'); ?></span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <li class="mm_towns">
                                <a class="dropmenu" href="#"><i class="fa fa-building-o"></i><span
                                            class="text"> <?= lang('Towns'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                <ul>
                                    <li id="towns_index"><a class="submenu" href="<?= site_url('towns'); ?>"><i
                                                    class="fa fa-list"></i><span
                                                    class="text"> <?= lang('List_Towns'); ?></span></a></li>
                                    <li id="towns_add"><a class="submenu" href="<?= site_url('towns/add'); ?>"><i
                                                    class="fa fa-plus-circle"></i><span
                                                    class="text"> <?= lang('Add_Town'); ?></span></a></li>
                                </ul>
                            </li>
                            <?php if ($GP['sales-index']) { ?>
                                <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'settings' ? '' : 'mm_pos' ?>">
                                    <a class="dropmenu" href="#"><i class="fa fa-heart"></i><span
                                            class="text"> <?= lang('sales'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('sales'); ?>"><i
                                                    class="fa fa-heart"></i><span
                                                    class="text"> <?= lang('list_sales'); ?></span></a></li>
                                        <?php if (POS && $GP['pos-index']) { ?>
                                            <li id="pos_sales"><a class="submenu"
                                                                  href="<?= site_url('pos/sales'); ?>"><i
                                                        class="fa fa-heart"></i><span
                                                        class="text"> <?= lang('pos_sales'); ?></span></a></li>
                                        <?php } ?>
										
                                        <?php if ($GP['sales-add']) { ?>
                                            <li id="sales_add"><a class="submenu"
                                                                  href="<?= site_url('sales/add'); ?>"><i
                                                        class="fa fa-plus-circle"></i><span
                                                        class="text"> <?= lang('add_sale'); ?></span></a></li>
														
                                        <?php }
                                        if ($GP['sales-deliveries']=='allowed') { ?>
                                        <!--    <li id="sales_deliveries"><a class="submenu"
                                                                         href="<?= site_url('sales/deliveries'); ?>"><i
                                                        class="fa fa-truck"></i><span
                                                        class="text"> <?= lang('deliveries'); ?></span></a></li>-->
                                        <?php }
                                        if ($GP['sales-gift_cards']) { ?>
                                            <li id="sales_gift_cards"><a class="submenu"
                                                                         href="<?= site_url('sales/gift_cards'); ?>"><i
                                                        class="fa fa-gift"></i><span
                                                        class="text"> <?= lang('gift_cards'); ?></span></a></li>
                                        <?php }
                                        if ($GP['sales-return_sales']) { ?>
                                            <li id="sales_return_sales"><a class="submenu"
                                                                           href="<?= site_url('sales/return_sales'); ?>"><i
                                                        class="fa fa-reply"></i><span
                                                        class="text"> <?= lang('list_return_sales'); ?></span></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($GP['sales-index']) { ?>
                                <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'settings' ? '' : 'mm_pos' ?>">
                                    <a class="dropmenu" href="#"><i class="fa fa-heart"></i><span
                                            class="text"> <?= lang('Discounts'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('sales/index2'); ?>"><i
                                                    class="fa fa-heart"></i><span
                                                    class="text"> <?= lang('List_Discounts'); ?></span></a></li>
                                    
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('sales/index3'); ?>"><i
                                                    class="fa fa-heart"></i><span
                                                    class="text"> <?= lang('List_Approved_Discount'); ?></span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>
                           <?php if ($GP['sales-index']) { ?>
                                <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'settings' ? '' : 'mm_pos' ?>">
                                    <a class="dropmenu" href="#"><i class="fa fa-heart"></i><span
                                            class="text"> <?= lang('Invoices'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('sales/invoice'); ?>"><i
                                                    class="fa fa-heart"></i><span
                                                    class="text"> <?= lang('List_Invoices'); ?></span></a></li>
                                    
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('sales/invoice1'); ?>"><i
                                                    class="fa fa-heart"></i><span
                                                    class="text"> <?= lang('List_Approved_Invoices'); ?></span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($GP['sales-index']) { ?>
                                <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'settings' ? '' : 'mm_pos' ?>">
                                    <a class="dropmenu" href="#"><i class="fa fa-heart"></i><span
                                            class="text"> <?= lang('Cheques'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('sales/cheque'); ?>"><i
                                                    class="fa fa-heart"></i><span
                                                    class="text"> <?= lang('List_Cheques'); ?></span></a></li>
                                    
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('sales/cheque1'); ?>"><i
                                                    class="fa fa-heart"></i><span
                                                    class="text"> <?= lang('List_Approved_Cheques'); ?></span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($GP['sales-index']) { ?>
                                <li class="mm_sales <?= strtolower($this->router->fetch_method()) == 'settings' ? '' : 'mm_pos' ?>">
                                    <a class="dropmenu" href="#"><i class="fa fa-heart"></i><span
                                            class="text"> <?= lang('Tickets'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('customers/index2'); ?>"><i
                                                    class="fa fa-heart"></i><span
                                                    class="text"> <?= lang('List_Tickets'); ?></span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($GP['quotes-index']) { ?>
                              <!-- <li class="mm_quotes">
                                    <a class="dropmenu" href="#"><i class="fa fa-heart-o"></i><span
                                            class="text"> <?= lang('quotes'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('quotes'); ?>"><i
                                                    class="fa fa-heart-o"></i><span
                                                    class="text"> <?= lang('list_quotes'); ?></span></a></li>
                                        <?php if ($GP['quotes-add']) { ?>
                                            <li id="sales_add"><a class="submenu" href="<?= site_url('quotes/add'); ?>"><i
                                                        class="fa fa-plus-circle"></i><span
                                                        class="text"> <?= lang('add_quote'); ?></span></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>-->
                            <?php } ?>
                            
                            <?php if ($GP['expenses-index']) { ?>
                              <li class="mm_expenses">
                                    <a class="dropmenu" href="#"><i class="fa fa-heart-o"></i><span
                                            class="text"> <?= lang('Expenses'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="sales_index"><a class="submenu" href="<?= site_url('purchases/expenses'); ?>"><i
                                                    class="fa fa-heart-o"></i><span
                                                    class="text"> <?= lang('List_Expenses'); ?></span></a></li>
                                                    <li id="sales_index"><a class="submenu" href="<?= site_url('purchases/expenses_approved'); ?>"><i
                                                    class="fa fa-heart-o"></i><span
                                                    class="text"> <?= lang('List_Approved_Expenses'); ?></span></a></li>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($GP['suppliers-index']) { ?>
                            <li class="mm_suppliers">
                                <a class="dropmenu" href="#"><i class="fa fa-users"></i><span
                                            class="text"> <?= lang('Suppliers'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                <ul>

                                    <li id="suppliers_index"><a class="submenu" href="<?= site_url('suppliers'); ?>"><i
                                                    class="fa fa-users"></i><span
                                                    class="text"> <?= lang('List_Suppliers'); ?></span></a></li>
                                </ul>
                            </li>
                            <?php } ?>
                            
                            <?php if ($GP['purchases-index']) { ?>
                                <li class="mm_purchases">
                                    <a class="dropmenu" href="#"><i class="fa fa-star"></i><span
                                            class="text"> <?= lang('purchases'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <li id="purchases_index"><a class="submenu"
                                                                    href="<?= site_url('reports/purchases'); ?>"><i
                                                    class="fa fa-star"></i><span
                                                    class="text"> <?= lang('List_Stock'); ?></span></a></li>
                                        <?php if ($GP['stock-taking-index']) { ?>
                                        <li id="stock_taking_index"><a class="submenu" href="<?= site_url('purchases/getStockTaking'); ?>"><i
                                                        class="fa fa-star"></i><span
                                                        class="text"> <?= lang('List Stock Taking History'); ?></span></a></li>
                                        <?php } ?>
                                        <?php if ($GP['purchases-expenses']) { ?>
                                           <!-- <li id="purchases_expenses"><a class="submenu"
                                                                           href="<?= site_url('purchases/expenses'); ?>"><i
                                                        class="fa fa-dollar"></i><span
                                                        class="text"> <?= lang('expenses'); ?></span></a></li>
                                            <li id="purchases_add_expense"><a class="submenu"
                                                                              href="<?= site_url('purchases/add_expense'); ?>"
                                                                              data-toggle="modal"
                                                                              data-target="#myModal"><i
                                                        class="fa fa-plus-circle"></i><span
                                                        class="text"> <?= lang('add_expense'); ?></span></a></li>-->
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>


                            <?php if ($GP['customers-index']) { ?>
                                <!--							<li class="mm_budget">-->
                                <!--                                    <a class="dropmenu" href="#"><i class="fa fa-star-o"></i><span-->
                                <!--                                            class="text"> --><?//= lang('budget'); ?><!-- </span> <span-->
                                <!--                                            class="chevron closed"></span></a>-->
                                <!--                                    <ul>-->
                                <!--                                        <li id="budget_index"><a class="submenu"-->
                                <!--                                                                    href="--><?//= site_url('budget'); ?><!--"><i-->
                                <!--                                                    class="fa fa-star-o"></i><span-->
                                <!--                                                    class="text"> --><?//= lang('list_budget'); ?><!--</span></a></li>-->
                                <!--                                        --><?php //if ($GP['budget-add']) { ?>
                                <!--                                            <li id="budget_add"><a class="submenu"-->
                                <!--                                                                      href="--><?//= site_url('budget/add'); ?><!--"><i-->
                                <!--                                                        class="fa fa-plus-circle"></i><span-->
                                <!--                                                        class="text"> --><?//= lang('add_transfer'); ?><!--</span></a></li>-->
                                <!--                                        --><?php //} ?>
                                <!--                                    </ul>-->
                                <!--                                </li>-->
                                <li class="mm_customers">
                                    <a class="dropmenu" href="#"><i class="fa fa-users"></i><span
                                                class="text"> <?= lang('Customers'); ?> </span> <span
                                                class="chevron closed"></span></a>
                                    <ul>
                                        <?php
                                        if ($GP['customers-index']) { ?>


                                            <li id="customers_index"><a class="submenu" href="<?= site_url('customers/customers'); ?>"><i
                                                            class="fa fa-users"></i><span
                                                            class="text"> <?= lang('list_customers1'); ?></span></a></li>
                                            <li id="customers_index"><a class="submenu" href="<?= site_url('customers/maps'); ?>"><i
                                                            class="fa fa-users"></i><span
                                                            class="text"> <?= lang('Customer_Mapping'); ?></span></a></li>
                                                            <li id="sms_code"><a class="submenu" href="<?= site_url('customers/smscode'); ?>"><i
                                                class="fa fa-users"></i><span
                                                class="text"> <?= lang('SMS_Codes'); ?></span></a></li>
                                           <li id="customers_index"><a class="submenu" href="<?= site_url('customers/select_county'); ?>"><i
                                                class="fa fa-plus-circle"></i><span
                                                class="text"> <?= lang('Select_customers_by_counties'); ?></span></a></li>
                                        <?php }
                                        ?>
                                    </ul>
                                </li>
                            <?php } ?>
                            <?php if ($GP['salesp-index']) { ?>
<!--							<li class="mm_budget">-->
<!--                                    <a class="dropmenu" href="#"><i class="fa fa-star-o"></i><span-->
<!--                                            class="text"> --><?//= lang('budget'); ?><!-- </span> <span-->
<!--                                            class="chevron closed"></span></a>-->
<!--                                    <ul>-->
<!--                                        <li id="budget_index"><a class="submenu"-->
<!--                                                                    href="--><?//= site_url('budget'); ?><!--"><i-->
<!--                                                    class="fa fa-star-o"></i><span-->
<!--                                                    class="text"> --><?//= lang('list_budget'); ?><!--</span></a></li>-->
<!--                                        --><?php //if ($GP['budget-add']) { ?>
<!--                                            <li id="budget_add"><a class="submenu"-->
<!--                                                                      href="--><?//= site_url('budget/add'); ?><!--"><i-->
<!--                                                        class="fa fa-plus-circle"></i><span-->
<!--                                                        class="text"> --><?//= lang('add_transfer'); ?><!--</span></a></li>-->
<!--                                        --><?php //} ?>
<!--                                    </ul>-->
<!--                                </li>-->
                                <li class="mm_suppliers">
                                    <a class="dropmenu" href="#"><i class="fa fa-users"></i><span
                                            class="text"> <?= lang('Sales_People'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <?php if ($GP['salesp-index']) { ?>
                                            <li id="salesp_index"><a class="submenu" href="<?= site_url('customers/customers2'); ?>"><i
                                                            class="fa fa-users"></i><span
                                                            class="text"> <?= lang('List_Sales_People'); ?></span></a></li>


                                        <?php }
                                         ?>
                                    </ul>
                                </li>
                            <?php } ?>

                            <?php if ($GP['reports-salespeople'] || $GP['reports-vehicles'] || $GP['reports-quantity_alerts'] || $GP['reports-expiry_alerts'] || $GP['reports-products'] || $GP['reports-monthly_sales'] || $GP['reports-sales'] || $GP['reports-payments'] || $GP['reports-purchases'] || $GP['reports-customers'] || $GP['reports-suppliers']) { ?>
                                <li class="mm_reports">
                                    <a class="dropmenu" href="#"><i class="fa fa-bar-chart-o"></i><span
                                            class="text"> <?= lang('reports'); ?> </span> <span
                                            class="chevron closed"></span></a>
                                    <ul>
                                        <?php if ($GP['reports-quantity_alerts']) { ?>
                                            <li id="reports_quantity_alerts"><a
                                                    href="<?= site_url('reports/quantity_alerts') ?>"><i
                                                        class="fa fa-bar-chart-o"></i><span
                                                        class="text"> <?= lang('product_quantity_alerts'); ?></span></a>
                                            </li>
                                        <?php }
                                        if ($GP['reports-expiry_alerts']) { ?>
                                            <?php if ($this->Settings->product_expiry) { ?>
                                                <li id="reports_expiry_alerts"><a
                                                    href="<?= site_url('reports/expiry_alerts') ?>"><i
                                                        class="fa fa-bar-chart-o"></i><span
                                                        class="text"> <?= lang('product_expiry_alerts'); ?></span></a>
                                                </li><?php } ?>
                                        <?php }
                                        if ($GP['reports-vehicles']) { ?>
                                            <li id="reports_sale_listing"><a
                                                        href="<?= site_url('reports/vehicles') ?>"><i
                                                            class="fa fa-car"></i><span
                                                            class="text"> <?= lang('Vehicles_Report'); ?></span></a></li>
                                        <?php }

                                        if ($GP['reports-vehicles']) { ?>
                                            <li id="reports_sale_listing"><a
                                                        href="<?= site_url('reports/routes') ?>"><i
                                                            class="fa fa-car"></i><span
                                                            class="text"> <?= lang('Routes_Report'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-vehicles']) { ?>
                                            <li id="reports_sale_listing"><a
                                                        href="<?= site_url('reports/customer_payment_method') ?>"><i
                                                            class="fa fa-car"></i><span
                                                            class="text"> <?= lang('Customer_Payment_Method_Report'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-salespeople']) { ?>
                                            <li id="reports_sale_listing"><a
                                                        href="<?= site_url('reports/salespeople') ?>"><i
                                                            class="fa fa-money"></i><span
                                                            class="text"> <?= lang('Salesperson_Report'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-products']) { ?>
                                            <li id="reports_products"><a href="<?= site_url('reports/products') ?>"><i
                                                        class="fa fa-barcode"></i><span
                                                        class="text"> <?= lang('products_report'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-daily_sales']) { ?>
                                            <li id="reports_daily_sales"><a
                                                    href="<?= site_url('reports/daily_sales') ?>"><i
                                                        class="fa fa-calendar-o"></i><span
                                                        class="text"> <?= lang('daily_sales'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-monthly_sales']) { ?>
                                            <li id="reports_monthly_sales"><a
                                                    href="<?= site_url('reports/monthly_sales') ?>"><i
                                                        class="fa fa-calendar-o"></i><span
                                                        class="text"> <?= lang('monthly_sales'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-sales']) { ?>
                                            <li id="reports_sales"><a href="<?= site_url('reports/sales') ?>"><i
                                                        class="fa fa-heart"></i><span
                                                        class="text"> <?= lang('sales_report'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-payments']) { ?>
                                            <li id="reports_payments"><a href="<?= site_url('reports/payments') ?>"><i
                                                        class="fa fa-money"></i><span
                                                        class="text"> <?= lang('payments_report'); ?></span></a></li>
                                                          <li id="reports_daily_sales"><a href="<?= site_url('pos/closes_register/'.$this->session->userdata('user_id')) ?>"><i
                                                class="fa fa-calendar-o"></i><span
                                                class="text"> <?= lang('zed_report'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-purchases']) { ?>
                                            <li id="reports_purchases"><a href="<?= site_url('reports/purchases') ?>"><i
                                                        class="fa fa-star"></i><span
                                                        class="text"> <?= lang('purchases_report'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-customers']) { ?>
                                            <li id="reports_customer_report"><a
                                                    href="<?= site_url('reports/customers') ?>"><i
                                                        class="fa fa-users"></i><span
                                                        class="text"> <?= lang('customers_report'); ?></span></a></li>
                                        <?php }
                                        if ($GP['reports-suppliers']) { ?>
                                            <li id="reports_supplier_report"><a
                                                    href="<?= site_url('reports/suppliers') ?>"><i
                                                        class="fa fa-users"></i><span
                                                        class="text"> <?= lang('suppliers_report'); ?></span></a></li>
                                        <?php } ?>
                                    </ul>
                                </li>
                            <?php } ?>

                        <?php } ?>
                    </ul>
                </div>
                <a href="#" id="main-menu-act" class="full visible-md visible-lg"><i
                        class="fa fa-angle-double-left"></i></a>
            </div>

            <div id="content" class="col-lg-10 col-md-10">
                <div class="row">
                    <div class="col-sm-12 col-md-12">
                        <ul class="breadcrumb">
                            <?php
                            foreach ($bc as $b) {
                                if ($b['link'] === '#') {
                                    echo '<li class="active">' . $b['page'] . '</li>';
                                } else {
                                    echo '<li><a href="' . $b['link'] . '">' . $b['page'] . '</a></li>';
                                }
                            }
                            ?>
                            <li class="right_log hidden-xs">
                                <?= lang('your_ip') . ' ' . $ip_address . " <span class='hidden-sm'>( " . lang('last_login_at') . ": " . date($dateFormats['php_ldate'], $this->session->userdata('old_last_login')) . " " . ($this->session->userdata('last_ip') != $ip_address ? lang('ip:') . ' ' . $this->session->userdata('last_ip') : '') . " )</span>" ?>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-12">
                        <?php if ($message) { ?>
                            <div class="alert alert-success">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= $message; ?>
                            </div>
                        <?php } ?>
                        <?php if ($error) { ?>
                            <div class="alert alert-danger">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= $error; ?>
                            </div>
                        <?php } ?>
                        <?php if ($warning) { ?>
                            <div class="alert alert-warning">
                                <button data-dismiss="alert" class="close" type="button">×</button>
                                <?= $warning; ?>
                            </div>
                        <?php } ?>
                        <?php
                        if ($info) {
                            foreach ($info as $n) {
                                if (!$this->session->userdata('hidden' . $n->id)) {
                                    ?>
                                   <!-- <div class="alert alert-info">
                                        <a href="#" id="<?= $n->id ?>" class="close hideComment external"
                                           data-dismiss="alert">&times;</a>
                                        <?= $n->comment; ?>
                                    </div>-->
                                <?php }
                            }
                        } ?>
                        <div id="alerts"></div>
