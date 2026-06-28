<?php // <--- do NOT put anything before this PHP tag

include('functions.php');

// get the cookieMessage, this must be done before any HTML is sent to the browser.
$cookieMessage = getCookieMessage();

	session_start(); // Ensure this is at the top of your script

	if (isset($_SESSION['UserName'])) {
		$UserName = htmlspecialchars($_SESSION['UserName'], ENT_QUOTES, 'UTF-8'); 
	} else {
		$UserName = 'Guest'; // Fallback prevents "undefined variable" errors later
	}
?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8" /> 
	<title>Our IntlTrade Online Store</title>
	<link rel="icon" type="image/jpeg" href="Images/IFU_Assets/ProductPictures/logo.jpg">	
	<link rel="stylesheet" type="text/css" href="shopstyle.css" />
</head>
<body>
<header style="display: flex; align-items: center; gap: 20px;">
    <img id="logo" src="Images/IFU_Assets/ProductPictures/logo.jpg" alt="IntlTrade" width="60" height="60"> 
     <?php echo "<h1 >Welcome $UserName  to Gadget Tree Online Store</h1>"; ?>
</header>	
	<h2 style="width:100%;text-align:center">View Cart</h2>
	<div id="navbar">
		<ul> 
			<li><a href="index.php">HomePage</a></li>				
			<li style='margin-top: 10px;'> <form method='GET' action='ProductList.php' class='search-form'>
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
				echo '<li><a href="UserDetails.php">User Details</a></li>';					
			} 	elseif ($UserName == 'Guest'  ) {
					echo '<li><a href="SignUp.php">Sign Up</a></li>';
			}
			?>			
			
			<li style='margin-top: 5px;'><form action = 'EmptyCart.php' method = 'POST'>
					<button style = 'margin-top: 10px;' name = 'EmptyCart' value = 'Empty Shopping Cart' id = 'EmptyCart' type='submit' class='search-submit'>Empty Shopping Cart</button>
				</form>
			</li>
			<li style='margin-top: 5px;'>
				<form action = 'ProcessOrder.php' method = 'POST'>
					<input type = 'text' name = 'UserName' placeholder = 'Enter your username here' id = 'UserName' style = 'border-radius: 50px;' />
					<button style = 'margin-top: 10px;' name = 'ProcessOrder' value = 'Process Order' id = 'ProcessOrder' type='submit' class='search-submit'>Process Order</button>
				</form>
			</li>	
			<li><a href="Login.php">Log-in</a></li> 				
		</ul>
	</div>		
	<?php

	// does the user have items in the shopping cart?
	if(isset($_COOKIE['ShoppingCart']) && $_COOKIE['ShoppingCart'] != '')
	{
		// The cookie string looks like: "101:2,105:1,108:5"
		$cartItems = explode(",", $_COOKIE['ShoppingCart']);
		
		// Remove duplicates from the array
		$cartItems = array_unique($cartItems);
		
		$dbh = connectToDatabase();

		// create a SQL statement to select the product and brand info about a given ProductID
		$statement = $dbh->prepare('
			SELECT Products.ProductID, Products.Description, Products.Price, Brands.BrandName
			FROM Products
			INNER JOIN Brands ON Products.BrandID = Brands.BrandID
			WHERE Products.ProductID = ?;
		');

		$totalPrice = 0;
		
		// loop over each item bundle in the shopping cart
		foreach($cartItems as $item)
		{
			// Separate the ProductID from its Quantity
			$parts = explode(":", $item);
			$productID = trim($parts[0]);
			$quantity = isset($parts[1]) ? (int)$parts[1] : 1;
			
			// Safety check for empty records or invalid formats
			if (empty($productID) || $productID == '1') {
				continue;
			}

			// bind the first question mark to the isolated productID
			$statement->bindValue(1, $productID);
			$statement->execute();
			
			// did we find a match?
			if($row = $statement->fetch(PDO::FETCH_ASSOC))
			{				
				// Calculate item total based on quantity
				$itemSubtotal = $row['Price'] * $quantity;
				$totalPrice += $itemSubtotal;						

				echo "<table class='viewcart-table-bordered' style=' width: 80%; background-color: #f9f9f9; border: 1px solid black; border-radius: 5px; padding: 15px; margin-top: 20px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);'><tbody><tr><td>";
				echo "<p>Product ID: " . htmlspecialchars($row['ProductID']) . "</p>";
				echo "<p>" . htmlspecialchars($row['Description']) . "</p>";
				echo "<p>Brand: " . htmlspecialchars($row['BrandName']) . "</p>";
				echo "<p style='font-weight: bold; color: #333; text-align: right;'>Quantity: $quantity</p>";	
				echo "<p style='font-weight: bold; color: green; text-align: right;'>Unit Price: $" . number_with_decimals_or_format($row['Price']) . "</p>";	
				echo "<p style='font-weight: bold; color: green; text-align: right;'>Item Total: $" . number_with_decimals_or_format($itemSubtotal) . "</p>";	
				echo "</td></tr></tbody></table>";			
			}
		}

		// Output the total price formatted nicely
		echo "<p style='font-weight: bold; font-size: 1.2em; color: green; text-align: right; margin-right: 20%; margin-top: 20px;'>Total Price: $" . number_with_decimals_or_format($totalPrice) . "</p>";

		// if we have any error messages echo them now.
		if (!empty($cookieMessage)) {
			echo "<div style='color: maroon; font-weight: bold; margin-top: 15px;'>$cookieMessage</div>";
		}
	}
	else
	{
		if (!empty($cookieMessage)) {
			echo "<div style='color: red; font-weight: bold; margin-top: 15px;'>$cookieMessage</div><br/>";
		}
		echo "<p style='text-align: center; font-size: 1.1em;'>You have no items in your cart!</p>";
	}

	// Helper function fallback if a formatting function isn't globally available in your project
	function number_with_decimals_or_format($number) {
		return number_format((float)$number, 2, '.', ',');
	}
	?>
</body>
</html>
