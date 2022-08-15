<!DOCTYPE html>
<html>
<head>
    <title>RECEIPT</title>
</head>
<body>
<h4>RECEIPT</h4>
<h4>POWERGAS</h4>
<p>TEL NO: 0710357700</p>

<p>PAYBILL NO: 868456</p>
<p>ACCOUNT NO: CUS-<?= strtoupper($customer->id); ?></p>

<p>VAN : <?= $vehicle->plate_no; ?></p>
<p>RECEIPT NO: <?= $inv->id; ?></p>
<p>DATE : <?= $inv->created; ?></p>
<hr>
<p>ACCOUNT NO: <?= strtoupper($customer->name); ?></p>
<table>
    <tr>
        <th>ITEM</th>
        <th>QTY</th>
        <th>PRICE</th>
        <th>DISCOUNT</th>
        <th>AMOUNT</th>
    </tr>
    <?php $r = 1;
    foreach ($rows as $row){
        ?>
        <tr>
            
            <td><?= $row->product_name; ?>
            </td>
            <td><?= $this->sma->formatNumber($row->quantity); ?></td>
            
            <td><?= $this->sma->formatMoney($row->net_unit_price); ?></td>
            <?php
            if ($Settings->tax1) {
                echo '<td style="width: 90px; text-align:right; vertical-align:middle;">' . ($row->item_tax != 0 && $row->tax_code ? '<small>(' . $row->tax_code . ')</small> ' : '') . $this->sma->formatMoney($row->item_tax) . '</td>';
            }
            if ($Settings->product_discount) {
                echo '<td style="width: 90px; text-align:right; vertical-align:middle;">' . ($row->discount != 0 ? '<small>(' . $row->discount . ')</small> ' : '') . $this->sma->formatMoney($row->item_discount) . '</td>';
            }
            ?>
            <td><?= $this->sma->formatMoney($row->subtotal); ?></td>
        </tr>
        <?php
        $r++;
    }
    ?>
</table>

<?php
$paid=0;
foreach ($payments as $payment){
    if($payment->type=='received'){
        $paid+=$payment->amount;
    }
}
?>
<h5>TOTAL : <?= $this->sma->formatMoney($inv->grand_total); ?></h5>
<h5>PAID : <?= $this->sma->formatMoney($paid); ?></h5>
<h5>BAL : <?= $this->sma->formatMoney($inv->grand_total - $paid); ?></h5>

<?php
foreach ($payments as $payment){
echo "<h5>PAYMENT MODE: ".$payment->paid_by."</h5>";
}
?>
<h5>SERVED BY: <?= $created_by->name; ?></h5>
<p>RAFIKI JIKONI</p>
<p>
<?php if ($inv->note || $inv->note != "") { ?>
        <?= $inv->note; ?>
<?php } ?>
</p>
</body>
</html>
