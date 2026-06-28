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
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Our IntlTrade Online Store</title>
  <link rel="icon" type="image/jpeg" href="Images/IFU_Assets/ProductPictures/logo.jpg">	
  <meta charset="utf-8">
  <link rel="stylesheet" type="text/css" href="shopstyle.css" />
</head>

<body>
<header style="display: flex; align-items: center; gap: 20px;">
    <img id="logo" src="Images/IFU_Assets/ProductPictures/logo.jpg" alt="IntlTrade" width="60" height="60"> 
    <h1>Welcome to Our Gadget Tree Online Store</h1>
</header> 
<div class="container" style="margin-top:20px">
 <div style="width:100%;text-align:center;font-size:24px;font-weight:600">Order List</div><br>
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
			<li><a href="UserDetails.php">User Details</a></li>
		</ul>
	</div>	
  
<?php 
	$dbh = connectToDatabase();
  // TODO prepare and execute your database query here:
		$statement = $dbh->prepare('SELECT O.OrderID, O.CustomerID, C.UserName, C.FirstName, C.LastName, 
    op.ProductID, P.Description, P.Price, P.BrandID, B.BrandName
     FROM Orders as O
    inner join Customers as C on O.CustomerID = C.CustomerID  
    inner join orderproducts as OP on O.OrderID = OP.OrderID
    inner join Products as P on OP.ProductID = P.ProductID
    inner join Brands as B on P.BrandID = B.BrandID
    where C.UserName = ?
    order by O.OrderID 
    ;');    
    
    $statement->bindValue(1, trim($UserName), PDO::PARAM_STR);
    $statement->execute();
    while($row = $statement->fetch(PDO::FETCH_ASSOC))
		    
		{ ?>
    <div style="display: flex; align-items: flex-start; gap: 20px; flex-wrap: wrap; margin: 20px;">
        <table style="box-shadow: 0px 3px 25px 3px rgba(0,0,0,0.25); width: 100%;">
        <!--<tr><td colspan="2"><h4 style="background-color:green;color:white;padding:20px;margin:0;">OrderID: <?php echo $row['OrderID']?> <span style='text-align:right;'> Brand:<?php echo $row['BrandName']?> </span></h4></td></tr> -->
        <td colspan="2">
            <h4 style="background-color:green; color:white; padding:20px; margin:0; display:flex; justify-content:space-between; align-items:center;">
                <span>OrderID: <?php echo $row['OrderID']?></span>
                <span>Brand: <?php echo $row['BrandName']?></span>
                <span>
                    <a href="ViewOrderDetails.php?OrderID=<?php echo $row['OrderID']?>" style="color: white; text-decoration: none; background-color: #333; padding: 8px 12px; border-radius: 4px;">
                        View Order Details
                    </a>
                </span>
            </h4>
        </td>        
        <tr><td style="width: 30%; text-align: center; vertical-align: top;">
        <!-- Left Column: Image and Brand -->   
        <div style="flex-shrink: 0;">
            <img style="width: 200px; height: 200px; display: block;" src="Images/IFU_Assets/ProductPictures/<?php echo $row['ProductID'] ?>.jpg" alt="Product Image">
            <br>
            
        </div>
        </td><td style="width: 70%; text-align: left; vertical-align: top;    ">
        <!-- Right Column: Table -->
        <div style="flex-grow: 1; background-color: #ffffff">
            <table class="table table-bordered" >
                <tbody>
                    <tr><td style="text-align:left;font-size:16px; width: 30%;">CustomerID: </td><td style="text-align:left;font-size:16px;"><?php echo $row['CustomerID'] ?></td></tr>
                    <tr><td style="text-align:left;font-size:16px;">UserName:</td><td style="text-align:left;font-size:16px;"><?php echo $row['UserName'] ?></td></tr>
                    <tr><td style="text-align:left;font-size:16px;">Customer Name:</td><td style="text-align:left;font-size:16px;"><?php echo $row['FirstName']."&nbsp;&nbsp;".$row['LastName'] ?></td></tr>
                    <tr><td style="text-align:left;font-size:16px;">ProductID:</td><td style="text-align:left;font-size:16px;"><?php echo $row['ProductID'] ?></td></tr>
                    <tr><td style="text-align:left;font-size:16px;">Description:</td><td style="text-align:left;font-size:16px;"><?php echo $row['Description'] ?></td></tr>
                    <tr><td style="text-align:left;font-size:16px;">Price:</td><td style="text-align:left;font-size:16px;"><?php echo $row['Price'] ?></td></tr>
                    <tr><td style="text-align:left;font-size:16px;">BrandID:</td><td style="text-align:left;font-size:16px;"><?php echo $row['BrandID'] ?></td></tr>
                    <tr><td style="text-align:left;font-size:16px;">BrandName:</td><td style="text-align:left;font-size:16px;"><?php echo $row['BrandName'] ?></td></tr>
                </tbody>
            </table>
        </div>
        </td></tr>
        </table>
    </div>
       <?php  } ?>
 
</div>
</body>
</html>
