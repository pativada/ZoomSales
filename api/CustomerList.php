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
<html lang="en">
<head>
	<title>Our Gadget Tree Online Store</title>
	<link rel="icon" type="image/jpeg" href="Images/IFU_Assets/ProductPictures/logo.jpg">	
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" type="text/css" href="shopstyle.css" />
  <link href="https://fonts.googleapis.com/css2?family=Lato:wght@700&display=swap" rel="stylesheet">
</head>
<body>
<header style="display: flex; align-items: center; gap: 20px;">
    <img id="logo" src="Images/IFU_Assets/ProductPictures/logo.jpg" alt="IntlTrade" width="60" height="60"> 
       <?php echo "<h1 >Welcome $UserName  to Gadget Tree Online Store</h1>"; ?>
</header> 

<div class="container" style="margin-top:10px;">
 <h2 style="width:100%;text-align:center"><br>Customer list</h2><br>   
 	<div id="navbar">
		<ul> 
      <li><a href="index.php">HomePage</a></li>            
			<li><a href="ViewCart.php">View Cart</a></li>
			<li style='margin-top: 15px;'> <form method='GET' action='ProductList.php' class='search-form'>
					<input name='page' type='hidden' value='0'/>
					<input name='search' type='hidden'class='search-input' placeholder='Search products...'  type='text' />
					<button type='submit' class='search-submit'>Product List</button>
				</form>
			</li>
			<li><a href="OrderList.php">Order List</a></li>
		</ul>
	</div>	       
  <table class="table table-bordered" style="width:90%;margin-left:5% ;margin-top:10px; border: 1px solid black;">
    <thead>
      <tr>
        <th style="background-color:green;color:white">CustomerID</th>
        <th style="background-color:green;color:white" >UserName</th>
        <th style="background-color:green;color:white">Name</th>
        <th style="background-color:green;color:white">Address</th>
        <th style="background-color:green;color:white">City</th>
      </tr>
    </thead>
    <tbody>

<?php
	$dbh = connectToDatabase();
    // TODO prepare and execute your database query here:
		$statement = $dbh->prepare('SELECT * FROM Customers ;');    
    $statement->execute();
    while($row = $statement->fetch(PDO::FETCH_ASSOC))
		    
		{ ?>
      <tr style="border: 1px solid black; background-color: lightgray;">
        <td style="text-align:left;margin:5px; "><?php echo $row['CustomerID']?></td>
        <td style="text-align:left;margin:5px; "><?php echo $row['UserName']?></td>
        <td style="text-align:left;margin:5px;"><?php echo $row['FirstName']."&nbsp;&nbsp;".$row['LastName']?></td>
        <td style="text-align:left;margin:5px;"><?php echo $row['Address']?></td>
        <td style="text-align:left;margin:5px;"><?php echo $row['City']?></td>
      </tr>
     <?php } ?>
    </tbody>
  </table>
</div>

</body>
</html>
