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

else
{
	$dbh = connectToDatabase();
	
	//TODO trim all 5 inputs, to make sure they have no extra spaces.
	$UserName = trim($_POST['UserName']);
	$FirstName = trim($_POST['FirstName']);
	$LastName = trim($_POST['LastName']);
	$Address = trim($_POST['Address']);
	$City = trim($_POST['City']);
	$changedate = date('d/m/Y');	

	// lets check to see if the user name is taken, COLLATE NOCASE tells SQLite to do a case insensitive match.
$statement = $dbh->prepare('UPDATE Customers SET FirstName = ?,
                                                 LastName = ?,
                                                 Address = ?,
                                                 City = ?,
                                                 ChangeDte = ?
                            WHERE UserName = ? COLLATE NOCASE;');

	$statement->bindValue(1,  $FirstName  );
	$statement->bindValue(2,  $LastName  );
	$statement->bindValue(3,  $Address  );	
	$statement->bindValue(4,  $City  );	
	$statement->bindValue(5,  $changedate  );
	$statement->bindValue(6,  $UserName  );
	//$statement->execute();
		
if ($statement->execute()) {
    echo "Customer details updated successfully.";
} else {
    // Optional: Get detailed error info if silent failure occurs
    $errorInfo = $statement->errorInfo();
    echo "Update failed: " . $errorInfo[2];
}
}
