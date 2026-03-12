<?php

$fecha = date('F j, Y', strtotime($info[0]['date_issue']));

$html = '

<style>
body{
    font-family: helvetica;
    font-size:10px;
}

.header-title{
    font-size:22px;
    font-weight:bold;
    color:#2c3e50;
}

.invoice-info{
    font-size:11px;
}

.section-title{
    background-color:#2c3e50;
    color:white;
    font-weight:bold;
    padding:6px;
}

.table-items th{
    background-color:#f4f6f7;
    font-weight:bold;
    border-bottom:1px solid #ddd;
}

.table-items td{
    border-bottom:1px solid #eee;
}

.total-table td{
    font-size:11px;
}

.total-final{
    background-color:#2c3e50;
    color:white;
    font-weight:bold;
}
</style>


<!-- HEADER -->

<table width="100%" cellpadding="4">

<tr>

<td width="40%">
<img src="' . $logo . '" height="60">
</td>

<td width="60%" align="right">

<span style="font-size:22px;font-weight:bold;">INVOICE</span><br>

<b>Invoice #:</b>' . $info[0]['number'] . '<br>
<b>Date:</b>' . $fecha . '

</td>

</tr>

</table>



<table width="100%">
<tr>
<td width="60%">
    <span class="header-title">INVOICE</span><br>
    <span class="invoice-info">
        Invoice #: '.$info[0]['number'].'<br>
        Date: '.$fecha.'
    </span>
</td>

<td width="40%" align="right">
    <b>Lev-West</b><br>
    P.O. Box 84209 RPO MARKET MALL<br>
    Calgary - Alberta - T3A 5C4<br>
    Phone: 587-892-9616<br>
    www.lev-west.com
</td>
</tr>
</table>

<br><br>


<!-- CLIENT INFO -->
<table width="100%" cellpadding="6">

<tr>
<td width="50%" class="section-title">CLIENT</td>
<td width="50%" class="section-title">PROJECT</td>
</tr>

<tr>
<td>
<b>Company:</b> '.$info[0]['company_name'].'<br>
<b>Phone:</b> '.$info[0]['movil_number'].'
</td>

<td>
<b>Project name:</b> '.$info[0]['company_name'].'<br>
<b>Project number:</b> '.$info[0]['job_description'].'
</td>

</tr>

</table>

<br><br>


<!-- ITEMS TABLE -->
<table width="100%" cellpadding="6" class="table-items">

<tr>
<th width="6%" align="center">#</th>
<th width="49%">Description</th>
<th width="10%" align="center">Unit</th>
<th width="10%" align="center">Qty</th>
<th width="12%" align="right">Unit Price</th>
<th width="13%" align="right">Total</th>
</tr>
';


$records = 0;
$total = 0;

if($items){
    foreach ($items as $data){

        $records++;
        $total += $data['value'];

$html .= '

<tr>
<td align="center">'.$records.'</td>
<td>'.$data['description'].'</td>
<td align="center">'.$data['unit'].'</td>
<td align="center">'.$data['quantity'].'</td>
<td align="right">$ '.number_format($data['rate'],2).'</td>
<td align="right">$ '.number_format($data['value'],2).'</td>
</tr>

';
    }
}

$html .= '
</table>

<br><br>

<table width="100%">
<tr>

<td width="60%"></td>

<td width="40%">

<table width="100%" cellpadding="6" class="total-table">

<tr>
<td width="60%" align="right"><b>Subtotal</b></td>
<td width="40%" align="right">$ '.number_format($total,2).'</td>
</tr>

<tr class="total-final">
<td align="right">TOTAL</td>
<td align="right">$ '.number_format($total,2).'</td>
</tr>

</table>

</td>

</tr>
</table>
';


$html .= '

<br><br>

<table width="100%" cellpadding="6">

<tr>
<td class="section-title">Notes</td>
</tr>

<tr>
<td>

1. All Work Tickets will be attached to the Invoice.<br>
2. Please refer to the Invoice number in your correspondence.<br><br>

<b>Contact:</b><br>
Fabian Villamil<br>
fabian.v@lev-west.com<br>
Ph: (403) 399-0160

<br><br>

<b>Signature:</b><br>
<img src="http://v-contracting.ca/app/images/employee/signature/hugo_boss.png" width="120">

</td>
</tr>

</table>
';




				
echo $html;
						
?>