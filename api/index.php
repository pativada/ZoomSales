<?php // <--- do NOT put anything before this PHP tag
	include('functions.php');
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
	<title>Our Gadget Tree Online Store</title>
	<link rel="icon" type="image/jpeg" href="./logo.jpg">	
	<link rel="stylesheet" type="text/css" href="./shopstyle.css" />
</head>
<body >
<header style="display: flex; align-items: center; gap: 20px;">
    <img id="logo" src="./logo.jpg" alt="IntlTrade" width="60" height="60"> 
   <?php echo "<h1 >Welcome $UserName  to Gadget Tree Online Store</h1>"; ?>
</header>

	<div class="announcement-bar-wrapper">
		<div class="marquee-inner-container">
			<span class="moving-announcement-text">
				<?php echo htmlspecialchars($cookieMessage, ENT_QUOTES, 'UTF-8'); ?> 🎉 Special Promo: Free delivery on all orders over $50! • Shop our latest arrivals now! &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</span>
			<span class="moving-announcement-text">
				<?php echo htmlspecialchars($cookieMessage, ENT_QUOTES, 'UTF-8'); ?> 🎉 Special Promo: Free delivery on all orders over $50! • Shop our latest arrivals now! &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			</span>
		</div>
	</div>	
	<div id="navbar">
		<ul> 
			<li> <form method='GET' action='./ProductList.php' class='search-form'>
					<input name='page' type='hidden' value='0'/>
					<input name='search' class='search-input' placeholder='Search products...'  type='text' />
					<button type='submit' class='search-submit'>Search</button>
				</form>
			</li>
			<li><a href="ViewCart.php">View Cart</a></li>
			<?php 
				if ($UserName == 'admin') {
					echo '<li><a href="./OrderList.php">Order List</a></li>';
					echo '<li><a href="./CustomerList.php">Customer List</a></li>';
				} 	elseif ($UserName != 'Guest' && $UserName != 'admin' ) {
					echo '<li><a href="./OrderHistory.php">Order History</a></li>';
					echo '<li><a href="./UserDetails.php">User Details</a></li>';					
				}  elseif ($UserName == 'Guest'  ) {
					echo '<li><a href="./SignUp.php">Sign Up</a></li>';
				}
			?>
			<li><a href="./Login.php">Log-in</a></li> 		
			<li><a href="./Logout.php" onclick="return confirm('Are you sure you want to log out?');">Log-Out</a></li> 		
		</ul>
	</div>

		<div class="promo-section" >
			<h2>Special Promotion!</h2>
			<p id="promo-description">Get 20% off on all products this week only! Use code: PROMO20 at checkout.</p>
			<p id="promo-text"> 20 % off!</p>
			<p> Click to browse products on below slider </p>
		</div>

		<div class="slider-wrapper">
		<div class="slider">
			<a href="ProductList.php??page=0&search=TV"><img id="slide-1" src="./FlashSales_TV.jpg" alt="Image 2"></a>
			<a href="ProductList.php??page=0&search=Camera"><img id="slide-2" src="./FlashSales_Camera.jpg" alt="Image 4"></a>	
			<a href="ProductList.php??page=0&search=Headphones"><img id="slide-3" src="./FlashSales_HeadPhones.jpg" alt="Image 3"></a>
			<a href="ProductList.php??page=0&search=Camera"><img id="slide-4" src="./FlashSales_Camera2.jpg" alt="Image 1"></a>			
		</div>
		<!-- Navigation dots -->
		<div class="slider-nav">
			<a href="#slide-1"></a>
			<a href="#slide-2"></a>
			<a href="#slide-3"></a>
			<a href="#slide-4"></a>
		</div>
		</div>			
	
</body>
</html>