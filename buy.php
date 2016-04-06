<!--
	Student Name: Dev Mehta
-->
<?php
session_start();
if(empty($_SESSION['cart'])){
    $_SESSION['cart'] = array();
}
$sum=0;	
?>
<html>
<head>
	<style>
		td, th {
			border: 1px solid transparent; 
			border-bottom: 1px solid #ddd;
			height: 31px;
		  }

		th {
			background: #000;
  			color: #FFF;
  			font-weight: bold;
		  }
		  td {
			text-align: center;
		  }
	</style>
</head>
<body>
	<?php
	//Adding it to the cart
	if(isset($_GET['buy'])){
		$buy=$_GET['buy'];
		$prodstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&productId='.$buy);
		$productXML = new SimpleXMLElement($prodstr);
			$pro=$productXML->categories->category->items->product;
 			$prodName=$pro->name;
		 	$imgLink=$pro->images->image[0]->sourceURL;
		 	$tempArray=array();
		 	$flag=0;
		 	if(!array_key_exists($buy, $_SESSION['cart'])){
			 	array_push($tempArray, (string)$buy);
			 	array_push($tempArray, (string)$prodName);
			 	array_push($tempArray, (string)$pro->minPrice);
			 	array_push($tempArray, (string)'<a href="'.$pro->productOffersURL.'"><img src='.$imgLink.'></a>');
			 	$_SESSION['cart'][$buy]=$tempArray;
		 	}
	}
	//Delete the product
	if(isset($_GET['delete'])){
		$deleteID=$_GET['delete'];
		unset($_SESSION['cart'][$deleteID]);
	}
	//Clear the cart
	if(isset($_GET['clear'])){
		unset($_SESSION['cart']);
	}
	//Displaying the cart
	if(!empty($_SESSION['cart'])){
		echo '<table border="1" style="width:auto">';
		echo '<tr>';
		echo '<th>Name</th>';
		echo '<th>Min Price</th>';
		echo '<th>Image</th>';
		echo '</tr>';
		foreach ($_SESSION['cart'] as $print) {
	 		echo '<tr>';
	 		echo '<td>'.$print[1].'</td>';
	 		echo '<td>'.$print[2].'</td>';
	 		echo '<td>'.$print[3].'</td>';
	 		echo '<td><a href="buy.php?delete='.$print[0].'"> DELETE ITEM </a></td>';
		 	echo '</tr>';
		 	$sum=$sum+$print[2];
		}
		  echo '</table>';
	}
	?>
<p>Your Cart:</p>
<table border=1>
</table>
<?php
echo '<p/>Total: $'.$sum.'</p>';
?>
<form action="buy.php" method="GET">
<input type="hidden" name="clear" value="1"/>
<input type="submit" value="Empty Basket"/>
</form>
<p/>
<form action="buy.php" method="GET">
<fieldset><legend>Find products:</legend>
<label>Category: 
<?php
		//Fetching the categories
$xmlcat = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/CategoryTree?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&visitorUserAgent&visitorIPAddress&trackingId=7000610&categoryId=72&showAllDescendants=true');
$cat = new SimpleXMLElement($xmlcat);
$catID=$cat->category['id'];
$catName=(string)$cat->category->name; 
print '<select name="category"><option value=72>Computers</option>';
foreach ($cat->category->categories->category as $b) {
	print ' <option value='.$b['id'].'>'.(string)$b->name.'</option>';
	foreach ($b->categories->category as $d ) {
		print '<optgroup><option value='.$d['id'].'>'.(string)$d->name.'</option></optgroup>';
	}
}
?>
</select></label>
<label>Search keywords: <input type="text" name="search"/><label>
<input type="submit" value="Search"/>
</fieldset>
</form>
<?php
		//Displaying the products.
if(isset($_GET['search'])){
error_reporting(E_ALL);
ini_set('display_errors','On');
$search=str_replace(' ', '+', $_GET['search']);
$categoryID=$_GET['category'];
$xmlstr = file_get_contents('http://sandbox.api.ebaycommercenetwork.com/publisher/3.0/rest/GeneralSearch?apiKey=78b0db8a-0ee1-4939-a2f9-d3cd95ec0fcc&trackingId=7000610&categoryId='.$categoryID.'&keyword='.$search.'&numItems=20');
$xml = new SimpleXMLElement($xmlstr);
echo '<table border="1" style="width:100%">';
echo '<tr>';
echo '<th>Name</th>';
echo '<th>Image</th>';
echo '<th>Product Description</th>';
echo '<th>Price</th>';
echo '</tr>';
foreach ($xml->categories->category->items->product as $temp) {
 	$pID=$temp['id'];
 	$name=$temp->name;
 	echo '<tr>';
 	echo '<td>'.(string)$name.'</td>';
 	$img=$temp->images->image[0]->sourceURL;
 	echo '<td><a href="buy.php?buy='.$pID.'"><img src='.$img.'></a></td>';
 	echo '<td>'.$temp->fullDescription.'</td>';
 	echo '<td>'.$temp->minPrice.'</td>';
 	echo '</tr>';
 } 
 echo '</table>';
}
?>
</body>
</html>