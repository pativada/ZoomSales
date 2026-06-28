<?php // <--- do NOT put anything before this PHP tag
	include('functions.php');
	$cookieMessage = getCookieMessage();
	session_start();

?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8" /> 
	<title>Our Gadget Tree Online Store</title>
	<link rel="icon" type="image/jpeg" href="Imges/IFU_Assets/ProductPictures/logo.jpg">	
	<link rel="stylesheet" type="text/css" href="shopstyle.css" />
</head>
<body>
<header style="display: flex; align-items: center; gap: 20px;">
    <img id="logo" src="Images/IFU_Assets/ProductPictures/logo.jpg" alt="IntlTrade" width="60" height="60"> 
    <h1>Welcome to Our Gadget Tree Online Store</h1>
</header> 	
	<h2 style="width:100%;text-align:center">Log-In!</h2>

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
		</ul>
	</div>		
	<?php
		// display any error messages. TODO style this message so that it is noticeable.
		echo $cookieMessage;
	?>
	<div id="LoginForm">
	<form method = 'POST'>
		<fieldset>
			<legend></legend> 
			<table>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>	
						<label for="user-mail">User ID</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<input type="text" name="UserName" id="UserName" placeholder="Enter User Email" maxlength="50" required />
					</td>
				</tr>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<label for="UserPasswd">Password:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<input type="password" name="UserPasswd" id="UserPasswd" placeholder="Enter Password" maxlength="15" required />
					</td>
				</tr>
			</table>
			<input type="submit" value="Log In!" />
		</fieldset>
	</form>
	</div>
<?php

if(!isset($_POST['UserName']))
{
	//echo "UserName! not provided"; 
}
elseif(!isset($_POST['UserPasswd']))
{
	//echo "Password not provided"; 
}	
else {
	$UserName = trim($_POST['UserName']);
	$loginuser = htmlspecialchars($UserName, ENT_QUOTES,"UTF-8");
	$loginPassword = trim($_POST['UserPasswd']);	

	// lets check to see if the user name is taken, COLLATE NOCASE tells SQLite to do a case insensitive match.

	$dbh = connectToDatabase();
	$statement = $dbh->prepare('SELECT * FROM Customers WHERE UserName = ? COLLATE NOCASE; ');
	$statement->bindValue(1,  $loginuser  );
	$statement->execute();	

	$row = $statement->fetch(PDO::FETCH_ASSOC);
	if ($row) {
	$storedHashFromDatabase = $row['Password'] ;
	$FirstName =  $row['FirstName'];
	// Check if the input matches the stored hash
	if (password_verify($loginPassword, $storedHashFromDatabase)) {
		session_regenerate_id(true);
		$_SESSION['UserName']  = $UserName;
		setCookieMessage("$FirstName!, you can now browse some products!");
		echo "Login successful!";
		redirect("index.php");			
	} else {
		echo "Invalid username or password.";
	}
	} else {
		echo "Invalid username or password.";
	}
}
?>
</body>
</html>