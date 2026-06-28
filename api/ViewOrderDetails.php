
<?php 
	session_start(); // Ensure this is at the top of your script

	if (isset($_SESSION['UserName'])) {
		$UserName = htmlspecialchars($_SESSION['UserName'], ENT_QUOTES, 'UTF-8'); 
	} else {
		$UserName = 'Guest'; // Fallback prevents "undefined variable" errors later
	}
?>
<!DOCTYPE HTML>
<html>
<head>
	<title>Our Gadget Tree Online Store</title>
	<link rel="icon" type="image/jpeg" href="Images/IFU_Assets/ProductPictures/logo.jpg">	
	<link rel="stylesheet" type="text/css" href="shopstyle.css" />
	<meta charset="UTF-8" /> 
</head>
<body>
<header style="display: flex; align-items: center; gap: 20px;">
    <img id="logo" src="Images/IFU_Assets/ProductPictures/logo.jpg" alt="IntlTrade" width="60" height="60"> 
     <?php echo "<h1 >Welcome $UserName  to Gadget Tree Online Store</h1>"; ?>
</header>	
<h2><span style="color: #333; font-size: 24px; align: center; margin-left: 40%;">Order Details</span></h2>
	<div id="navbar">
		<ul> 
			<li><a href="index.php">HomePage</a></li>			
			<li style='margin-top: 15px;'> <form method='GET' action='ProductList.php' class='search-form'>
					<input name='page' type='hidden' value='0'/>
					<input name='search' type='hidden'class='search-input' placeholder='Search products...'  type='text' />
					<button type='submit' class='search-submit'>Product List</button>
				</form>
			</li>
			<li><a href="ViewCart.php">View Cart</a></li>			
			<?php 
			if ($UserName == 'admin') {
				echo '<li><a href="OrderList.php">Order List</a></li>';
				echo '<li><a href="CustomerList.php">Customer List</a></li>';
			} 	elseif ($UserName != 'Guest' && $UserName != 'admin' ) {
				echo '<li><a href="OrderHistory.php">Order History</a></li>';
				echo '<li><a href="UserDetails.php">Customer List</a></li>';					
			}
			?>				
		</ul>
	</div>		
<?php

// did the user provided an OrderID via the URL?
if(isset($_GET['OrderID']))
{
	$UnsafeOrderID = $_GET['OrderID'];
	
	include('functions.php');
	$dbh = connectToDatabase();
	
	// select the order details and customer details. (you need to use an INNER JOIN)
	// but only show the row WHERE the OrderID is equal to $UnsafeOrderID.
	$statement = $dbh->prepare('
		SELECT * 
		FROM Orders 
		INNER JOIN Customers ON Customers.CustomerID = Orders.CustomerID 
		WHERE OrderID = ? ; 
	');
	$statement->bindValue(1,$UnsafeOrderID);
	$statement->execute();
	
	// did we get any results?
	if($row1 = $statement->fetch(PDO::FETCH_ASSOC))
	{
		// Output the Order Details.
		$FirstName = makeOutputSafe($row1['FirstName']); 
		$LastName = makeOutputSafe($row1['LastName']); 
		$OrderID = makeOutputSafe($row1['OrderID']); 
		$UserName = makeOutputSafe($row1['UserName']); 
		
		// display the OrderID
		
		// its up to you how the data is displayed on the page. I have used a table as an example.
		// the first two are done for you.
		echo "<table style='border: 1px solid black; margin-bottom: 50px; width: 50%; margin-left: 20%; margin-top: 50px; box-shadow: 0px 3px 20px 3px rgb(0, 0, 0, 0.25);	'>";
		echo "<tr><td colspan='2' style='color: #333; display: flex; flex-direction: row; justify-content: space-between; align-items: center; padding: 0 15px;'><h2>OrderID: $OrderID</h2></td></tr>";
		echo "<tr><th>UserName:</th><td>$UserName</td></tr>";
		echo "<tr><th>Customer Name:</th><td>$FirstName $LastName </td></tr>";
		echo "<tr><th>Customer Address:</th><td>" . makeOutputSafe($row1['Address']) . "</td></tr>";
		echo "<tr><th>Customer City:</th><td>" . makeOutputSafe($row1['City']) . "</td></tr>";
		//TODO show the date and time of the order.
		echo "<tr><th>Order Date:</th><td>" . makeOutputSafe(date('d-M-Y H:i:s', strtotime($row1['TimeStamp']))) . "</td></tr>";
		echo "</table>";
		
		// TODO: select all the products that are in this order (you need to use INNER JOIN)
		// this will involve three tables: OrderProducts, Products and Brands.
		$statement2 = $dbh->prepare('
			
			SELECT * FROM OrderProducts as op inner join Products as p on op.ProductID = p.ProductID 
			inner join brands as b on p.BrandID = b.BrandID
			WHERE OrderID = ? ; 
		');
		$statement2->bindValue(1,$UnsafeOrderID);
		$statement2->execute();
		
		$totalPrice = 0;
		echo "<span style='display: block; margin-bottom: 20px; margin-left: 20%;'><h2>Order Details:</h2></span>";
		
		// loop over the products in this order. 
		while($row2 = $statement2->fetch(PDO::FETCH_ASSOC))
		{
			//NOTE: pay close attention to the variable names.
			$ProductID = makeOutputSafe($row2['ProductID']); 
			$Description = makeOutputSafe($row2['Description']); 

		echo "<table style='border: 1px solid black; margin-bottom: 50px; width: 50%; margin-left: 20%; margin-top: 50px; box-shadow: 0px 3px 20px 3px rgb(0, 0, 0, 0.25); border-collapse: collapse; font-family: sans-serif;'>";

		// Row 1: Product ID (Left aligned) and Price (Right aligned, green color)
		echo "<tr>";
		echo "  <th style='text-align: left; padding: 10px; color: #333;'>Product ID: $ProductID</th>";
		echo "  <td style='text-align: center; color: #333; padding: 10px;  font-weight: bold;'> Quantity " . makeOutputSafe($row2['Quantity']) . "</td>";		
		echo "  <td style='text-align: right; padding: 10px; color: green; font-weight: bold;'> Price " . makeOutputSafe($row2['Price']) . "</td>";
		echo "</tr>";

		// Row 2: Two columns for images and text stacked vertically
		echo "<tr>";
		// Left Column: Product Image + Product Description below
		echo "  <td style='width: 50%; padding: 15px; vertical-align: top;'>";
		echo "    <div style='display: flex; flex-direction: column; align-items: center; gap: 10px;'>";
		echo "      <a href='ViewProduct.php?ProductID=$ProductID'><img src='Images/IFU_Assets/ProductPictures/$ProductID.jpg' style='max-width: 100%; height: auto;'> </a>";
		echo "      <p style='margin: 0; text-align: center; color: #333;'>$Description</p>";
		echo "    </div>";
		echo "  </td>";

		// Right Column: Brand Image + Brand Name below
		echo "  <td style='width: 50%; padding: 15px; vertical-align: top;'>";
		echo "    <div style='display: flex; flex-direction: column; align-items: center; gap: 10px;'>";
		echo "      <img src='Images/IFU_Assets/BrandPictures/" . makeOutputSafe($row2['BrandID']) . ".jpg' style='max-width: 100%; height: auto;'>";
		echo "      <p style='margin: 0; text-align: center; font-weight: bold; color: #333;'>" . makeOutputSafe($row2['BrandName']) . "</p>";
		echo "    </div>";
		echo "  </td>";
		echo "</tr>";

		echo "</table>";


			// TODO show the Products Description, Brand, Price, Picture of the Product and a picture of the Brand.
			// TODO The product Picture must also be a link to ViewProduct.php.
			
			// TODO add the price to the $totalPrice variable.
			$totalPrice = $totalPrice += ( $row2['Price'] * $row2['Quantity'] );		
		}		
		
		//TODO display the $totalPrice .
		echo "<p style='font-weight: bold; color: green; text-align: right; margin-right: 30%;'>Total Price: $totalPrice</p>";		
	}
	else 
	{
		echo "System Error: OrderID not found";
	}
}
else
{
	echo "System Error: OrderID was not provided";
}
?>
</body>
</html>
