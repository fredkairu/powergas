<!DOCTYPE html>
<html>
<head>
 <style type="text/css">
        html, body {
            height: 100%;
            background: #FFF;
        }

        body:before, body:after {
            display: none !important;
        }

        .table th {
            text-align: center;
            padding: 5px;
        }

        .table td {
            padding: 4px;
        }
    </style>
</head>

<body>
<div id="wrap">
    <div class="row">
        <div class="col-lg-12">
             <div class="text-center" style="margin-bottom:20px;">
                    <!--<img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>" alt="<?= $Settings->site_name; ?>">-->
                    <img src="<?= base_url() . 'assets/uploads/logos/' . $biller->logo; ?>"
                         alt="<?= $biller->company != '-' ? $biller->company : $biller->name; ?>">
                </div>
           
                <div class="text-center" style="margin-bottom:5px;">
                    <h3>RETURN PURCHASE</h3>
                    <!--<img src="<?= base_url() . 'assets/uploads/logos/' . $Settings->logo; ?>"
                         alt="<?= $Settings->site_name; ?>">-->
                </div>
         
            <div class="well well-sm">
                <div class="row bold">
                    <div class="col-xs-4"><?= lang("Return_date"); ?>: <?= $this->sma->hrld($date); ?>
                        <br><?= lang("ref"); ?>: <?= $id; ?><br>
                   <p><?= lang("created_by"); ?>
                                : <?= $created_by->first_name . ' ' . $created_by->last_name; ?> </p>
                    </div>
                    <div class="col-xs-6 pull-right text-right">
                      
                        <?php $this->sma->qrcode('link', urlencode(site_url('purchases/view/' . $did)), 1); ?>
                        <img src="<?= base_url() ?>assets/uploads/qrcode<?= $this->session->userdata('user_id') ?>.png"
                             alt="<?= $did?>"/>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            </div>

        <div class="clearfix"></div>
        <div class="row">
            <div class="col-sm-5">
                <?php if ($product->image != 'no_image.png') { ?><img
                    src="<?= base_url() ?>assets/uploads/<?= $product->image ?>" alt="<?= $product->name ?>"
                    class="img-responsive img-thumbnail" /><?php } ?>
            </div>
            <div class="col-sm-7">
                <div class="clearfix"></div>
                <div class="table-responsive">
                     <div class="row padding10">
                <div class="col-xs-5">
                    <h3 class=""><?= $biller->company != '-' ? $biller->company : $biller->name; ?></h3>
                    <?= $biller->company ? "" : "Attn: " . $biller->name ?>
                    <?php
                    echo $biller->address . "<br />" . $biller->city . " " . $biller->postal_code . " " . $biller->state . "<br />" . $biller->country;
                    echo "<p>";
                    if ($biller->cf1 != "-" && $biller->cf1 != "") {
                        echo "<br>" . lang("bcf1") . ": " . $biller->cf1;
                    }
                    if ($biller->cf2 != "-" && $biller->cf2 != "") {
                        echo "<br>" . lang("bcf2") . ": " . $biller->cf2;
                    }
                    if ($biller->cf3 != "-" && $biller->cf3 != "") {
                        echo "<br>" . lang("bcf3") . ": " . $biller->cf3;
                    }
                    if ($biller->cf4 != "-" && $biller->cf4 != "") {
                        echo "<br>" . lang("bcf4") . ": " . $biller->cf4;
                    }
                    if ($biller->cf5 != "-" && $biller->cf5 != "") {
                        echo "<br>" . lang("bcf5") . ": " . $biller->cf5;
                    }
                    if ($biller->cf6 != "-" && $biller->cf6 != "") {
                        echo "<br>" . lang("bcf6") . ": " . $biller->cf6;
                    }
                    echo "</p>";
                    echo lang("tel") . ": " . $biller->phone . "<br />" . lang("email") . ": " . $biller->email;
                    ?>
                    <div class="clearfix"></div>
                </div>
                    <div class="col-xs-5"></div>
            </div>
                    <table class="table table-bordered table-striped dfTable table-right-left">
                        <tbody>
                     
                        <tr>
                            <td><?php echo $this->lang->line("product_name"); ?></td>
                            <td><?php echo $product->name; ?></td>
                        </tr>
                      
                       
                   
                        <tr>
                            <td><?php echo $this->lang->line("product_unit"); ?></td>
                            <td><?php echo $product->unit; ?></td>
                        </tr>
                        <?php
                           
                                echo '<tr><td>' . $this->lang->line("Price_Purchased") . '</td><td>' . $this->sma->formatMoney($product->cost) . '</td></tr>';
                          
                            if ($this->session->userdata('show_price')) {
                                echo '<tr><td>' . $this->lang->line("product_price") . '</td><td>' . $this->sma->formatMoney($product->price) . '</td></tr>';
                            }
                        
                        ?>
 <tr>
                            <td><?php echo $this->lang->line("Returned_Quantity"); ?></td>
                            <td><?php echo $quantity ?></td>
                         
                        </tr>
                        <tr>    <td><?php echo $this->lang->line("Returned_Note"); ?></td>
                            <td><?php echo $this->sma->decode_html($note) ?></td></tr>
                   
                        <?php if ($product->alert_quantity != 0) { ?>
                           
                        <?php } ?>
                        <?php if ($variants) { ?>
                            <tr>
                                <td><?php echo $this->lang->line("product_variants"); ?></td>
                                <td><?php foreach ($variants as $variant) {
                                        echo '<span class="label label-primary">' . $variant->name . '</span> ';
                                    } ?></td>
                            </tr>
                        <?php } ?>
                        </tbody>
                    </table>
                </div>
                <br><br><br>
                   <p><?= lang("stamp_sign"); ?>....................................................</p>
            </div>
            <div class="clearfix"></div>
           
        </div>

        <?php
        if (!empty($images)) {
            foreach ($images as $ph) {
                echo '<img class="img-responsive" src="' . base_url() . 'assets/uploads/' . $ph->photo . '" alt="' . $ph->photo . '" style="width:' . $Settings->iwidth . 'px; height:' . $Settings->iheight . 'px;" />';
            }
        }
        ?>
    </div>
</div>
</div>
</body>
</html>
