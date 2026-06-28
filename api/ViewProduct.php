	<?php 
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
	<h1>Product Details</h1>
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
				echo '<li><a href="UserDetails.php">Customer List</a></li>';						
			}
			?>	
		</ul>
	</div>	
	<?php 
		
		// include some functions from another file.
		include('functions.php');
		
		if(isset($_GET['ProductID']))
		{		
			// connect to the database using our function (and enable errors, etc)
			$dbh = connectToDatabase();
			
			// select all the products with the specified ID.
			$statement = $dbh->prepare(' Select Products.ProductID, Products.Description, Products.Price, products.BrandID, Brands.BrandName, Brands.Website
			FROM Products LEFT JOIN Brands ON Products.BrandID = Brands.BrandID
			WHERE Products.ProductID = ?');
			
			// TODO: bind the value here
			$statement->bindValue(1, $_GET['ProductID'], PDO::PARAM_STR);
			
			//execute the SQL.
			$statement->execute();

			// get the result, there will only ever be one product with a given ID (because products ids must be unique)
			// so we can just use an if() rather than a while()
			if($row = $statement->fetch(PDO::FETCH_ASSOC))
			{

		echo "<table style='border: 1px solid black; 
					 margin-bottom: 50px; width: 50%; margin-left: 20%; 
					 margin-top: 50px; box-shadow: 0px 3px 20px 3px rgb(0, 0, 0, 0.25); 
					 border-collapse: collapse; 
					 font-family: sans-serif;
					 background-color: #ffffff; '>";

		// Row 1: Product ID (Left), Middle spacing spacer, and Price (Right)
		echo "<tr>";
		echo "  <th style='text-align: left; padding: 10px; color: #333;'>Product ID: " . makeOutputSafe($row['ProductID']) . "</th>";
		echo "  <td></td>"; // Fixed: Added missing semicolon at the end of this line
		echo "  <td style='text-align: right;  padding: 10px; color: green; font-weight: bold;'> Price " . makeOutputSafe($row['Price']) . "</td>";
		echo "</tr>";

		// Row 2: Three columns (Product | Add to Cart | Brand)
		echo "<tr>";
		
		// Left Column: Product Image + Product Description below
		echo "  <td style='width: 40%; padding: 15px; vertical-align: top;'>";
		echo "    <div style='display: flex; flex-direction: column; align-items: center; gap: 10px; border: 2px solid black;'>";
		echo "      <img src='Images/IFU_Assets/ProductPictures/" . makeOutputSafe($row['ProductID']) . ".jpg' style='max-width: 100%; height: auto;'>";
		echo "      <p style='margin: 0; text-align: center; color: #333;'>" . makeOutputSafe($row['Description']) . "</p>";
		echo "    </div>";
		echo "  </td>";
		
		// Middle Column: Add to Cart Form
		echo "  <td style='width: 20%; text-align: center; padding: 15px; vertical-align: middle;'>";
		echo "    <form method='POST' action='AddToCart.php' class='add-to-cart-form' onsubmit='handleCartSubmit(event, this);' >";
		echo "      <input type='hidden' name='ProductID' value='" . makeOutputSafe($row['ProductID']) . "' />"; 
		echo "		<input type='hidden' name='BuyButton' value='' />"; 
		echo "      <div>";
		// Combined duplicate class attributes into one single attribute
		echo "        <button style='border-radius: 50px;'  type='submit' name='BuyButton' class='search-submit btn-cart'>Add to cart</button>";
		echo "      </div>";
		echo "    </form>";		
		echo "  </td>";
		
		// Right Column: Brand Image + Brand Name below
		echo "  <td style='width: 40%; padding: 15px; vertical-align: top;'>";
		echo "    <div style='display: flex; flex-direction: column; align-items: center; gap: 10px; border: 2px solid black'>";
		echo "      <a href='" . makeOutputSafe($row['Website']) . "' target='_blank' alt='Visit " . makeOutputSafe($row['BrandName']) . " website'>";
		echo "        <img src='Images/IFU_Assets/BrandPictures/" . makeOutputSafe($row['BrandID']) . ".jpg' style='max-width: 100%; height: auto;'>";
		echo "      </a>";
		echo "      <p style='margin: 0; text-align: center; font-weight: bold; color: #333;'>" . makeOutputSafe($row['BrandName']) . "</p>";
		echo "    </div>";
		echo "  </td>";
		echo "</tr>";
		echo "</table>";

			}
			else
			{
				echo "Unknown Product ID";
			}
		}
		else
		{
			echo "No ProductID provided!";
		}
	?>

<script>
function handleCartSubmit(event, formElement) {
    // 1. Stop the browser from reloading the page
    event.preventDefault();

    // 2. Prepare the form data to send to PHP
    const formData = new FormData(formElement);

    // 3. Send data to AddToCart.php in the background
    fetch(formElement.action, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (response.ok) {
            // 4. Hide the form completely when successful
            formElement.style.display = 'none';
            
            // 5. Show the success confirmation text right next to it
            const successMsg = formElement.nextElementSibling;
            if (successMsg) {
                successMsg.style.display = 'block';
            }
        } else {
            alert('Something went wrong. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>

</body>
</html>