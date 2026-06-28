<?php // <--- do NOT put anything before this PHP tag

include('functions.php');

// Did the user click the buy button AND did they provide a ProductID?
if(!isset($_POST['BuyButton']))
{
	echo "BuyButton not provided, the submit button should be named BuyButton and the form method should be POST"; 
	print_r($_POST);
}
elseif(!isset($_POST['ProductID']))
{
	echo "ProductID not provided, make sure you have passed the ProductID via POST"; 
	print_r($_POST);
}
else 
{
	$productToBuy = trim($_POST['ProductID']);
	
	// Get quantity from form if provided, otherwise default to 1
	$qtyToAdd = isset($_POST['Quantity']) ? (int)$_POST['Quantity'] : 1;
	if ($qtyToAdd < 1) {
		$qtyToAdd = 1;
	}

	// Case 1: The shopping cart already has items
	if(isset($_COOKIE['ShoppingCart']) && $_COOKIE['ShoppingCart'] != "")
	{
		$currentCart = $_COOKIE['ShoppingCart'];
		
		// Split cart into individual items (Format: ProductID:Quantity)
		$items = explode(",", $currentCart);
		$productFound = false;
		$updatedItems = [];

		foreach($items as $item)
		{
			// Separate the ProductID from its Quantity
			$parts = explode(":", $item);
			$currentProductID = $parts[0];
			// If no quantity exists yet in the cookie, default to 1
			$currentQty = isset($parts[1]) ? (int)$parts[1] : 1;

			if ($currentProductID == $productToBuy) 
			{
				// Product exists: increment its quantity
				$currentQty += $qtyToAdd;
				$productFound = true;
			}
			
			// Rebuild the item string
			$updatedItems[] = $currentProductID . ":" . $currentQty;
		}

		// Product does not exist in cart yet: append it
		if (!$productFound) 
		{
			$updatedItems[] = $productToBuy . ":" . $qtyToAdd;
		}

		// Turn the array back into a comma-separated string
		$updatedCart = implode(",", $updatedItems);
	}
	// Case 2: This is the very first item in the cart
	else 
	{
		$updatedCart = $productToBuy . ":" . $qtyToAdd;
	}
	
	// Set the "ShoppingCart" cookie for 30 days
	setcookie("ShoppingCart", $updatedCart, time() + (86400 * 30), "/"); 
	
	// redirect the user back to ViewProduct.php 
	redirect("ViewProduct.php?ProductID=$productToBuy");
}
