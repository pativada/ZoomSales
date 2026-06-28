<?php // <--- do NOT put anything before this PHP tag

// this php file will have no HTML

include('functions.php');

if(!isset($_POST['UserName']))
{
	echo "UserName not provided, make sure your form is using POST"; 
}
elseif(!isset($_POST['FirstName']))
{
	echo "FirstName not provided"; 
}	
elseif(!isset($_POST['LastName']))
{
	echo "LastName not provided"; 
}
elseif(!isset($_POST['Address']))
{
	echo "Address not provided"; 
}
elseif(!isset($_POST['City']))
{
	echo "City not provided"; 
}
elseif( !isset($_POST['UserPasswd']))
{
echo "Password not provided";
}
else
{
	$dbh = connectToDatabase();
	
	//TODO trim all 5 inputs, to make sure they have no extra spaces.
	$UserName = trim($_POST['UserName']);
	$FirstName = trim($_POST['FirstName']);
	$LastName = trim($_POST['LastName']);
	$Address = trim($_POST['Address']);
	$City = trim($_POST['City']);
	$Passwd = trim($_POST['UserPasswd']);
	$createdate = date('d/m/Y');
	$hashedPassword = password_hash($Passwd, PASSWORD_DEFAULT);

	// lets check to see if the user name is taken, COLLATE NOCASE tells SQLite to do a case insensitive match.
	$statement = $dbh->prepare('SELECT * FROM Customers WHERE UserName = ? COLLATE NOCASE; ');
	$statement->bindValue(1,  $UserName  );
	$statement->execute();
		
	// we found a match, so inform the user that they cant use the user-name.
	if($row2 = $statement->fetch(PDO::FETCH_ASSOC))
	{
		setCookieMessage("The UserName: '$UserName' is Taken by someone else :(");
		redirect("SignUp.php");
	}
	else
	{		
		// add the new customer to the customers table.
		// TODO insert the new customer and their details into the Customers table.
		// NOTE: you must NOT provide the customerID, the database will generate one for you.
		$statement2 = $dbh->prepare('
			
			Insert into Customers (UserName, FirstName, LastName, Address, City, Password, CreateDate)
			Values ( ?, ?, ?, ?, ?, ?, ?);	
			
		');
		
		// TODO: bind the 5 variables to the question marks. the first one is done for you.
		$statement2->bindValue(1, $UserName );
		$statement2->bindValue(2, $FirstName );
		$statement2->bindValue(3, $LastName );
		$statement2->bindValue(4, $Address );
		$statement2->bindValue(5, $City );
		$statement2->bindValue(6, $hashedPassword );
		$statement2->bindValue(7, $createdate );

		$statement2->execute();
		
		setCookieMessage("$FirstName!, you can now buy some products!");
		redirect("Login.php");			
	}
}
