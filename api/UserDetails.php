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
    <style>
      /* By default, hide editable inputs when in view-mode */
      .view-mode .editable,
      .view-mode .btn-save {
        display: none;
      }

      /* When switched to edit-mode, hide the static spans and show inputs */
      .edit-mode .readonly {
        display: none;
      }
      .edit-mode .editable,
      .edit-mode .btn-save {
        display: inline-block;
      }
    </style>
</head>
<body>
<header style="display: flex; align-items: center; gap: 20px;">
    <img id="logo" src="Images/IFU_Assets/ProductPictures/logo.jpg" alt="IntlTrade" width="60" height="60"> 
    <?php echo "<h1 >Welcome $UserName  to Gadget Tree Online Store</h1>"; ?>
</header> 

<div class="container" style="margin-top:10px;">
 <h2 style="width:100%;text-align:center"><br>User Details</h2><br>   
 	<div id="navbar">
		<ul> 
      <li><a href="index.php">HomePage</a></li>            
			<li><a href="ViewCart.php">View Cart</a></li>
			<li style='margin-top: 15px;'> 
        <form method='GET' action='ProductList.php' class='search-form'>
					<input name='page' type='hidden' value='0'/>
					<input name='search' type='hidden'class='search-input' placeholder='Search products...'  type='text' />
					<button type='submit' class='search-submit'>Product List</button>
				</form>
			</li>
			<li><a href="OrderHistory.php">Order History</a></li>
      <li><a href="ViewCart.php">View Cart</a></li>
		</ul>
	</div>	       
<?php
	$dbh = connectToDatabase();
    // TODO prepare and execute your database query here:
		$statement = $dbh->prepare('SELECT * FROM Customers where username = ?;');    
    $statement->bindValue(1, $UserName, PDO::PARAM_STR);	
    $statement->execute();
    while($row = $statement->fetch(PDO::FETCH_ASSOC))
		{ 
    $customerid = htmlspecialchars($row['CustomerID']);
    $username = htmlspecialchars($row['UserName']);
    $firstname = htmlspecialchars($row['FirstName']);
    $lastname = htmlspecialchars($row['LastName']);
    $address = htmlspecialchars($row['Address']);
    $city =  htmlspecialchars($row['City']);
    }  
    ?>
<div id="userform" class="view-mode">
	<form id="userdetform" action = 'UpdateCustomer.php' method = 'POST'>  
	<fieldset>
			<legend>User Details Form</legend>
			<table>
        <tr>
      <td colspan="2">
        <button type="button" id="toggleBtn" class="btn-toggle">Edit Profile</button>   
      </td>             
       </tr>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>	
						<label for="CustomerID">Customer ID:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
            <span class="readonly"><?php echo $customerid; ?></span>
            <input type="text" name="CustomerID" id="CustomerID"  class="editable" value="<?php echo $customerid ?>" />
					</td>
				</tr>      
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>	
						<label for="UserName">User Name:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
            <span class="readonly"><?php echo $username; ?></span>
						<input type="text" name="UserName" id="UserName" class="editable" value=<?php echo $username ?>  />
					</td>
				</tr>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>	
						<label for="FirstName">First Name:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
            <span class="readonly"><?php echo $firstname; ?></span>
						<input type="text" name="FirstName" id="FirstName" class="editable" value=<?php echo $firstname ?> />
					</td>
				</tr>        
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>	
						<label for="LastName">Last Name:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
            <span class="readonly"><?php echo $lastname; ?></span>
						<input type="text" name="LastName" id="LastName" class="editable" value=<?php echo $lastname ?> />
					</td>
				</tr>      
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>	
						<label for="Address">Address:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
            <span class="readonly"><?php echo $address; ?></span>
						<input type="text" name="Address" id="Address" class="editable" value=<?php echo $address ?> />
					</td>
				</tr>       
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>	
						<label for="City">City:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
            <span class="readonly"><?php echo $city; ?></span>
						<input type="City" name="City" id="City" class="editable" value=<?php echo $city ?> />
					</td>
				</tr>          
        <tr>
          <td colspan="2" > 
            <input type="submit" class="btn-save" value="Save Customer" />
          </td>
        </tr>              
        </table>

      </fieldset>		  
</form> 
</div>
  <script>
    document.getElementById('toggleBtn').addEventListener('click', function() {
      const formWrapper = document.getElementById('userform');
      
      // Toggle the classes on the container div
      if (formWrapper.classList.contains('view-mode')) {
        formWrapper.classList.remove('view-mode');
        formWrapper.classList.add('edit-mode');
        this.textContent = 'Cancel Edit'; // Change button text
      } else {
        formWrapper.classList.remove('edit-mode');
        formWrapper.classList.add('view-mode');
        this.textContent = 'Edit Profile'; // Reset button text
      }
    });
  </script>
</body>
</html>
