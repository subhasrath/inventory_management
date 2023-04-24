<?php
require('fpdf184/fpdf.php');

//db connection
$con = mysqli_connect('localhost','root','');
mysqli_select_db($con,'shop_inventory');

//get invoices data
$query = mysqli_query($con,"select * from sale
	inner join customer using(customerID)
	inner join item using(itemNumber)
	where
	customerID = '".$_GET['customerID']."' and   
	saleDate = '".$_GET['saleDate']."'");
	
$invoice = mysqli_fetch_array($query);
//A4 width : 219mm
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm

$pdf = new FPDF('P','mm','A4');

$pdf->AddPage('P');

$pdf->SetFont('Arial','B',20);
//Cell(width , height , text , border , end line , [align] )
$pdf->Cell(70	,5,'',0,0);
$pdf->Cell(50	,10,'TAX INVOICE',1,0,'C');
$pdf->SetFont('Arial','',10);
$pdf->Cell(20	,10,'',0,0);
$pdf->Cell(10	,10,'Date:',0,0);
$pdf->Cell(20	,10,$invoice['saleDate'],0,1);//end of line

$pdf->SetFont('Arial','B',20);
//Cell(width , height , text , border , end line , [align] )
$pdf->Cell(60	,10,'',0,0);
$pdf->Cell(50	,10,'MAA KALI TRADERS',0,0);
$pdf->Cell(50	,10,'',0,1);//end of line

//set font to arial, regular, 12pt
$pdf->SetFont('Arial','B',10);

$pdf->Cell(70	,5,'',0,0);
$pdf->Cell(60	,5,'GSTIN NO : 19BPPPR6799C1ZY',1,0,'C');
$pdf->Cell(34	,5,'',0,1);//end of line
$pdf->SetFont('Arial','B',12);
$pdf->Cell(60	,5,'',0,0); 
$pdf->Cell(50	,6,'NANDIGRAM * PURBA MEDINIPUR',0,0);
$pdf->Cell(34	,5,'',0,1);
//$pdf->Cell(34	,5,$invoice['saleDate'],0,1);//end of line


$pdf->Cell(45	,5,'',0,0);
$pdf->Cell(60	,5,'MOBILE NO: 8348793964 /7908576267/6296843124',0,0);
$pdf->Cell(60	,7,'',0,1);

$pdf->SetFont('Arial','B',10);

$pdf->Cell(30	,5,'Customer Name:','B',0);
$pdf->Cell(70	,5,$invoice['fullName'],'B',0);//end of line

$pdf->Cell(35	,5,'Customer Address:','B',0);
$pdf->Cell(60	,5,$invoice['address'],'B',1);//end of line

$pdf->Cell(13	,5,'GSTIN:','B',0,'L');
$pdf->Cell(40	,5,$invoice['phone2'],'B',1);//end of line

//make a dummy empty cell as a vertical spacer
$pdf->Cell(5	,5,'',0,1);//end of line

//invoice contents
$pdf->SetFont('Arial','B',10);

$pdf->Cell(8	,5,'SL',1,0);
$pdf->Cell(100	,5,'DESCRIPTION OF GOODS',1,0);
$pdf->Cell(10	,5,'Qty',1,0);
$pdf->Cell(25	,5,'MRP',1,0);
$pdf->Cell(25	,5,'Price',1,0);
$pdf->Cell(25	,5,'Total',1,1);//end of line

$pdf->SetFont('Arial','',10);

//Numbers are right-aligned so we give 'R' after new line parameter

//items
$query = mysqli_query($con,"SELECT sale.itemName,sale.saleDate,sale.discount,sale.quantity as qty,
sale.unitPrice,sale.total,customer.fullName,customer.address,
customer.address2,customer.city,item.unitPrice as price 
FROM  ( sale sale INNER JOIN customer customer ON 
sale.customerID=customer.customerID )  INNER JOIN item item ON 
sale.itemNumber=item.itemNumber
where sale.customerID = '".$invoice['customerID']."'
and   
sale.saleDate = '".$invoice['saleDate']."'");



//$tax = 0; //total tax
$amount = 0; 
$prevdue = 0;
$adj = 0;
$totldue = 0; //total amount
$srl = 1;
$discount = 0;
$saleunitpric = 0;
$cal = 0;
$price = 0;
$totll = 0;
$qty = 0;
$uprice = 0;
//display the items
while($item = mysqli_fetch_array($query)){
	$pdf->Cell(8	,6,$srl,1,0);
	//$pdf->SetFont('Arial','B',10);
	$pdf->Cell(100	,6,$item['itemName'],1,0);
	//add thousand separator using number_format function
	//$pdf->Cell(40	,5,($item['saleDate']),1,0);
	$pdf->Cell(10	,6,number_format($item['qty']),1,0,'C'); //qty

	$price = $item['price'];
	$pdf->Cell(25	,6,number_format($price,2),1,0); //MRP

	$saleunitpric = $item['unitPrice'];
	$discount = $item['discount'];
	$cal = (($saleunitpric * $discount) / 100);
	$uprice = (($saleunitpric)-$cal);
	$pdf->Cell(25	,6,number_format($uprice,2),1,0); // Unit Price

	//
	$qty = $item['qty'];
	$totll = $qty * $uprice;
	$pdf->Cell(25	,6,number_format($totll,2),1,1);//end of line

	$amount += $totll;
	$srl++;
	$prevdue = $item['address2'];
	$adj = $item['city'];
	
}

//summary
$pdf->Cell(138	,5,'',0,1);

$pdf->Cell(134	,5,'',0,0);
$pdf->Cell(30	,5,'Subtotal',1,0);
$pdf->Cell(30	,5,number_format($amount,2),1,1,'R');//end of line

$pdf->Cell(134	,5,'',0,0);
$pdf->Cell(30	,5,'Previous Due',1,0);
$pdf->Cell(30	,5,number_format($prevdue,2),1,1,'R');//end of line

$pdf->Cell(134	,5,'',0,0);
$pdf->Cell(30	,5,'Adjustment',1,0);
$pdf->Cell(30	,5,number_format($adj,2),1,1,'R');//end of line

$pdf->Cell(134	,5,'',0,0);
$pdf->Cell(30	,5,'Total Due',1,0);
$pdf->Cell(30	,5,number_format(($amount + $prevdue)-$adj,2),1,1,'R');//end of line

$pdf->Output();
?>
