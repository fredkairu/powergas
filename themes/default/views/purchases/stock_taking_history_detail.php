<style type="text/css">
    @media print {
        #myModal .modal-content {
            display: none !important;
        }
    }
</style>
<div class="modal-dialog modal-lg no-modal-header">
    <div class="modal-content">
        <div class="modal-body print">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-2x">&times;</i>
            </button>
            
            <div class="clearfix"></div>

            <div style="clear: both;"></div>
            <p>&nbsp;</p>

            <div class="row">
                <div class="col-sm-6">
                    
                    <p style="font-weight:bold;"><?= lang("date"); ?>: <?= $this->sma->hrld($stock->created_at); ?></p>

                    <p style="font-weight:bold;"><?= lang("Total_short"); ?>: <?= $stock->total_short; ?></p>
                </div>
                <p>&nbsp;</p>

                <div style="clear: both;"></div>
            </div>
            <div class="row">
            <div class="col-sm-6">
            <div><?php echo $this->lang->line("CURRENT_STOCK");?></div>
            <table class="table table-bordered table-hover table-striped" style="margin-bottom: 5px;">

        <thead>

        <tr>

            <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("no"); ?></th>
            <th style="vertical-align:middle;"><?php echo $this->lang->line("Product_Name"); ?></th>


            <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("quantity"); ?></th>
            <th style="padding-right:20px; text-align:center; vertical-align:middle;"><?php echo $this->lang->line("price"); ?></th>
            
        </tr>

        </thead>

        <tbody>

        <?php $r = 1;
        foreach ($expected as $row): ?>
            <tr>
                <td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $r; ?></td>
                <td style="vertical-align:middle;"><?php echo $row->product_name . " (" . $row->product_id . ")"; ?>


                <td style="width: 70px; text-align:center; vertical-align:middle;"><?php echo $row->product_quantity; ?></td>
                <td style="width: 80px; text-align:right; padding-right:10px; vertical-align:middle;"><?php echo $row->product_price; ?></td>

            </tr>
            <?php
            $r++;
        endforeach;
        ?>
        
        </tr>

        </tbody>

    </table>
    </div>     
    <div class="col-sm-6">
        <div><?php echo $this->lang->line("EXPECTED_STOCK");?></div>
            <table class="table table-bordered table-hover table-striped" style="margin-bottom: 5px;">

        <thead>

        <tr>

            <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("no"); ?></th>
            <th style="vertical-align:middle;"><?php echo $this->lang->line("Product_Name"); ?></th>


            <th style="text-align:center; vertical-align:middle;"><?php echo $this->lang->line("quantity"); ?></th>
            <th style="padding-right:20px; text-align:center; vertical-align:middle;"><?php echo $this->lang->line("price"); ?></th>
            <th style="padding-right:20px; text-align:center; vertical-align:middle;"><?php echo $this->lang->line("Short/Excess"); ?></th>
            
        </tr>

        </thead>

        <tbody>

        <?php $rw = 1;
        foreach ($current as $curr): ?>
            <tr>
                <td style="text-align:center; width:40px; vertical-align:middle;"><?php echo $rw; ?></td>
                <td style="vertical-align:middle;"><?php echo $curr->product_name . " (" . $curr->product_id . ")"; ?>


                <td style="width: 70px; text-align:center; vertical-align:middle;"><?php echo $curr->product_quantity; ?></td>
                <td style="width: 80px; text-align:right; padding-right:10px; vertical-align:middle;"><?php echo $curr->product_price; ?></td>
                <?php
                foreach($expected as $expect)
                {
                    $prod=$curr->product_quantity;
                    $prod2=($expect->product_quantity);
                if($expect->product_id == $curr->product_id) {
                $diff=$prod2-$prod;
                break;
                }
                } ?>
                
            <?php
            if($diff > 0){
        ?>
                <td style="width: 80px; text-align:right; padding-right:10px; vertical-align:middle;"><?php echo "Excess (" .$diff.")"; ?></td>
             
        
        <?php
           } elseif($diff < 0){
        ?>
                <td style="width: 80px; text-align:right; padding-right:10px; vertical-align:middle;"><?php echo "Short (" .$diff.")"; ?></td>
        
        <?php
           } else{
        ?>
                <td style="width: 80px; text-align:right; padding-right:10px; vertical-align:middle;"><?php echo $diff; ?></td>
             <?php
           } 
        ?>
          </tr>  
            <?php
            
            $rw++;
        endforeach;
        ?>
        
        </tr>

        </tbody>

    </table>
    </div>  
    </div>        
                <div class="clearfix"></div>
            </div>
        </div>
    </div>
</div>