<?php
//db connection
$con = mysqli_connect('localhost','root','');
mysqli_select_db($con,'shop_inventory');
?>
<html>
	<style>
input[type=text], select {
  width: 20%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}
input[type=date], select {
  width: 20%;
  padding: 12px 20px;
  margin: 8px 0;
  display: inline-block;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

input[type=submit] {
  background-color: #04AA6D;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
  float: left;
}


input[type=submit]:hover {
  background-color: #45a049;
}

div {
  border-radius: 5px;
  background-color: #f2f2f2;
  padding: 20px;
}
</style>
<body>

<h1>: Invoice generator :</h1>

<div>
	<form method='get' action='invoice-db.php'>
    <label for="CustomerID">Customer ID:</label>
    <input type="text" id="customerID" name='customerID' placeholder="Customer ID">

    <label for="saleDate">Sale Date:</label>
    <input type="date" id="saleDate" name="saleDate" placeholder="Your last name.."><br>

    <input type="submit" value='Generate'>
  </form>
</div>
	</body>
</html>
