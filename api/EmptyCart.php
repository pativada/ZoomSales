
<?php // <--- do NOT put anything before this PHP tag

include('functions.php');

// did they click the empty cart button?
if(isset($_POST['EmptyCart']))
{
	setcookie("ShoppingCart", " ", time() - 36000, "/"); // if this does not work, use: deleteCookie('ShoppingCart');
	//deleteCookie('ShoppingCart');
	setCookieMessage("Your Cart Has been emptied");
	$_SESSION['flash_message'] = "Your Cart Has been emptied";
	redirect("ViewCart.php");
}
else
{
	echo "System Error: button not pressed, make sure the submit button is named EmptyCart and the method is post";
}
?>