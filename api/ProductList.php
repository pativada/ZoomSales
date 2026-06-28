<?php
		// include some functions from another file.
		include('functions.php');
	session_start(); // Ensure this is at the top of your script

	if (isset($_SESSION['UserName'])) {
		$UserName = htmlspecialchars($_SESSION['UserName'], ENT_QUOTES, 'UTF-8'); 
	} else {
		$UserName = 'Guest'; // Fallback prevents "undefined variable" errors later
	}

		// connect to the database using our function (and enable errors, etc)
		$dbh = connectToDatabase();

		// 1. FIX PAGINATION MATH
		$limit = 10;
		if(isset($_GET['page']))
		{
			$currentPageNum = intval($_GET['page']);
			$offset = $currentPageNum * $limit;
			$nextPage = $currentPageNum + 1;
			$prevPage = max($currentPageNum - 1, 0); // Ensure previous page doesn't go below 0
		}
		else
		{
			$offset = 0;
			$nextPage = 1;
			$prevPage = 0;
		}

		// Initialize default search variable to prevent undefined notices
		$safeSearchString = "";

		if(isset($_GET['search']))
		{
			$searchString = $_GET['search'];
			$safeSearchString = htmlspecialchars($searchString, ENT_QUOTES,"UTF-8");
			$SqlSearchString = "%$searchString%";
    		$searchType = isset($_GET['search_type']) ? $_GET['search_type'] : 'Top';		
			// 2. FIX SQL ORDER: WHERE comes BEFORE LIMIT/OFFSET
			//$statement = $dbh->prepare('SELECT * FROM Products WHERE Description LIKE ? LIMIT ? OFFSET ?;');
			  if ($searchType === 'Brand') {
				$statement = $dbh->prepare('SELECT p.ProductID, p.Description, p.Price, COUNT(o.OrderID) AS TotalOrders, b.BrandName
				FROM products p
				LEFT JOIN OrderProducts o ON p.ProductID = o.ProductID
				LEFT JOIN Brands b on p.brandid = b.brandid 
				WHERE b.brandname like ?
				GROUP BY p.ProductID, p.Description, p.Price
				ORDER BY BrandName
				LIMIT ? OFFSET ?;');	
				$statement->bindValue(1, $SqlSearchString, PDO::PARAM_STR);	
				// 3. FIX BIND TYPES: Explicitly bind numbers as integers
				$statement->bindValue(2, $limit, PDO::PARAM_INT);
				$statement->bindValue(3, $offset, PDO::PARAM_INT);				
				$statement->execute();						
			  }
			  elseif ($searchType === 'Description') {
				$statement = $dbh->prepare('SELECT p.ProductID, p.Description, p.Price, COUNT(o.OrderID) AS TotalOrders, b.BrandName
				FROM products p
				LEFT JOIN OrderProducts o ON p.ProductID = o.ProductID
				LEFT JOIN Brands b on p.brandid = b.brandid 
				WHERE p.Description LIKE ? 
				GROUP BY p.ProductID, p.Description, p.Price
				ORDER BY Description
				LIMIT ? OFFSET ?;');		
				$statement->bindValue(1, $SqlSearchString, PDO::PARAM_STR);	
				// 3. FIX BIND TYPES: Explicitly bind numbers as integers
				$statement->bindValue(2, $limit, PDO::PARAM_INT);
				$statement->bindValue(3, $offset, PDO::PARAM_INT);				
				$statement->execute();	
			  }
				elseif ($searchType === 'Top') {
					$limit = 10; 			
					$offset = 0;
					$statement = $dbh->prepare('SELECT p.ProductID, p.Description, p.Price, COUNT(o.OrderID) AS TotalOrders, b.BrandName
					FROM products p
					LEFT JOIN OrderProducts o ON p.ProductID = o.ProductID
					LEFT JOIN Brands b on p.brandid = b.brandid 
					WHERE p.Description LIKE ?
					GROUP BY p.ProductID, p.Description, p.Price, b.BrandName
					ORDER BY TotalOrders DESC
					LIMIT ? OFFSET ?;'); 
					
					// 2. Bind all 3 parameters just like your other standard options
					$statement->bindValue(1, $SqlSearchString, PDO::PARAM_STR);
					$statement->bindValue(2, $limit, PDO::PARAM_INT);
					$statement->bindValue(3, $offset, PDO::PARAM_INT);
					
					$statement->execute();
			  } else {
				$statement = $dbh->prepare('SELECT p.ProductID, p.Description, p.Price, COUNT(o.OrderID) AS TotalOrders, b.BrandName
				FROM products p
				LEFT JOIN OrderProducts o ON p.ProductID = o.ProductID
				LEFT JOIN Brands b on p.brandid = b.brandid 
				WHERE p.Description LIKE ?				
				GROUP BY p.ProductID, p.Description, p.Price
				ORDER BY TotalOrders DESC
				LIMIT ? OFFSET ?;');				
				// 3. FIX BIND TYPES: Explicitly bind numbers as integers			
				$statement->bindValue(1, $SqlSearchString, PDO::PARAM_STR);
				$statement->bindValue(2, $limit, PDO::PARAM_INT);
				$statement->bindValue(3, $offset, PDO::PARAM_INT);		
				$statement->execute();						
			  }

		}
		// if the user did NOT provide a search string, assume an empty string
		else 
		{
			$statement = $dbh->prepare('SELECT p.ProductID, p.Description, p.Price, COUNT(o.OrderID) AS TotalOrders, b.BrandName
			FROM products p
			LEFT JOIN OrderProducts o ON p.ProductID = o.ProductID
			LEFT JOIN Brands b on p.brandid = b.brandid 
			GROUP BY p.ProductID, p.Description, p.Price
			ORDER BY TotalOrders DESC
			LIMIT ? OFFSET ?;');			
			$statement->bindValue(1, $limit, PDO::PARAM_INT);
			$statement->bindValue(2, $offset, PDO::PARAM_INT);
			$statement->execute();									
		}
		// URL encode the search param to handle spaces and special characters safely in the link
		$urlSearch = urlencode($safeSearchString);
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
	<h2 style="width:100%;text-align:center">Products List</h2>

	<div id="navbar">
		<ul> 
			<li><a href="index.php">HomePage</a></li>
			<li><a href="ViewCart.php">View Cart</a></li>
			<?php 
			if ($UserName == 'admin') {
				echo '<li><a href="OrderList.php">Order List</a></li>';
				echo '<li><a href="CustomerList.php">Customer List</a></li>';				
			} 	elseif ($UserName != 'Guest' && $UserName != 'admin' ) {
				echo '<li><a href="OrderHistory.php">Order History</a></li>';
				echo '<li><a href="UserDetails.php">User Details</a></li>';					
			}  elseif ($UserName == 'Guest'  ) {
					echo '<li><a href="SignUp.php">Sign Up</a></li>';
			}
			?>
			<li><a href="Login.php">Log-in</a></li> 	
			<li class="search-dropdown-container"> 
				<form method='GET' action='ProductList.php' class='search-form' id='searchForm'>
					<input name='page' type='hidden' value='0'/>
					<input type="text" name="search" class="search-input" placeholder="Search products..." />
					<!-- Changed to type="button" so it toggles the menu first -->
					<button type='button' class='search-submit' onclick='toggleSearchMenu(event)'>Search</button>
					
					<!-- The Hidden Dropdown Menu -->
					<div id="searchDropdown" class="dropdown-content">
						<p class="dropdown-title">Sort Options</p>
						<label><input type="radio" name="search_type" value="All" checked> All </label>						
						<label><input type="radio" name="search_type" value="Top" > 10 Top Selling</label>
						<label><input type="radio" name="search_type" value="Brand"> Brand</label>						
						<label><input type="radio" name="search_type" value="Description"> Product Description</label>
						<hr>
						<button type="submit" class="dropdown-submit-btn">Apply & Search</button>
					</div>
				</form>
			</li>
				
			<li> 
				<a href='?page=<?php echo $prevPage; ?>&search=<?php echo $urlSearch; ?>&search_type=<?php echo isset($_GET['search_type']) ? urlencode($_GET['search_type']) : 'All'; ?>' class='btn-pagination'>
				<svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><path d='M19 12H5M12 19l-7-7 7-7'/></svg>
				<span>Previous Page</span> 	
			</a>
			</li>				
			<li> 
				<a href='?page=<?php echo $nextPage; ?>&search=<?php echo $urlSearch; ?>&search_type=<?php echo isset($_GET['search_type']) ? urlencode($_GET['search_type']) : 'all'; ?>'<span>Next Page</span> 
				<svg width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><path d='M5 12h14M12 5l7 7-7 7'/></svg> 	
				</a>
			</li>
		</ul>
	</div>		
	<?php 
		echo "<div class = 'productContainer'>";
		// get the results
		while($row = $statement->fetch(PDO::FETCH_ASSOC))
		{
			// Remember that the data in the database could be untrusted data. 
			// so we need to escape the data to make sure its free of evil XSS code.
			$ProductID = htmlspecialchars($row['ProductID'], ENT_QUOTES, 'UTF-8'); 
			$Price = htmlspecialchars($row['Price'], ENT_QUOTES, 'UTF-8'); 
			$Description = htmlspecialchars($row['Description'], ENT_QUOTES, 'UTF-8'); 
			$BrandName = htmlspecialchars($row['BrandName'], ENT_QUOTES, 'UTF-8'); 
			// output the data in a div with a class of 'productBox' we can apply css to this class.
			echo "<div id='product-$ProductID' class = 'productBox' style='border-radius: 25px;' > \n";
			echo "<div class = 'productBox' data-url='ViewProduct.php?ProductID=$ProductID' onclick='window.location.href=this.dataset.url'>";
			echo "<img src = 'Images/IFU_Assets/ProductPictures/$ProductID.jpg'>";
			// [Put Task 5A here]  
			echo "<p id='product-id'>Product ID: $ProductID</p>";
			echo "<p id='Brand-id' style='color:blue'>Brand: $BrandName</p>";
			echo "<p id='description'>$Description</p></br>";
			echo "<p id='price'>Price: $Price</p>";
			echo "</div>";
			echo "<p style='color:red'>Total Orders: " . htmlspecialchars($row['TotalOrders'], ENT_QUOTES, 'UTF-8') . "</p>";			
			echo "</div> \n";			
		}
		echo "</div> \n";	
	?>
<script>
function toggleSearchMenu(event) {
    // Prevent default actions and bubbling
    event.stopPropagation();
    // Toggle the 'show' class on the menu
    document.getElementById("searchDropdown").classList.toggle("show");
}

// Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (!event.target.matches('.search-submit') && !event.target.closest('#searchDropdown')) {
        var dropdowns = document.getElementsByClassName("dropdown-content");
        for (var i = 0; i < dropdowns.length; i++) {
            var openDropdown = dropdowns[i];
            if (openDropdown.classList.contains('show')) {
                openDropdown.classList.remove('show');
            }
        }
    }
}
</script>
</body>
</html>
