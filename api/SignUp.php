<?php // <--- do NOT put anything before this PHP tag
	include('functions.php');
	$cookieMessage = getCookieMessage();
	session_start(); // Ensure this is at the top of your script

	if (isset($_SESSION['UserName'])) {
		$UserName = htmlspecialchars($_SESSION['UserName'], ENT_QUOTES, 'UTF-8'); 
	} else {
		$UserName = 'Guest'; // Fallback prevents "undefined variable" errors later
	}

	$password_rules = [
		"length" => "At least 8 characters long",
		"uppercase" => "Include at least one uppercase letter (A-Z)",
		"number" => "Include at least one number (0-9)",
		"special" => "Include at least one special character (@, $, !, %, *, ?, &)"
	];

?>
<!doctype html>
<html>
<head>
	<meta charset="UTF-8" /> 
	<title>Our IntlTrade Online Store</title>
	<link rel="icon" type="image/jpeg" href="Imges/IFU_Assets/ProductPictures/logo.jpg">	
	<link rel="stylesheet" type="text/css" href="shopstyle.css" />
</head>
<body>
<header style="display: flex; align-items: center; gap: 20px;">
    <img id="logo" src="Images/IFU_Assets/ProductPictures/logo.jpg" alt="IntlTrade" width="60" height="60"> 
    <h1>Welcome to Our Gadget Tree Online Store</h1>
</header> 	
	<h2 style="width:100%;text-align:center">Sign Up!</h2>

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
			<?php 
			if ($UserName == 'admin') {
				echo '<li><a href="OrderList.php">Order List</a></li>';
				echo '<li><a href="CustomerList.php">Customer List</a></li>';
			}
			?>
		</ul>
	</div>		
	<?php
		// display any error messages. TODO style this message so that it is noticeable.
		echo $cookieMessage;
	?>
	<div id="newCustomerForm">
	<form id="signupForm" action = 'AddNewCustomer.php' method = 'POST'>
		<!-- 
			TODO make a sign up <form>, don't forget to use <label> tags, <fieldset> tags and placeholder text. 
			all inputs are required.
			
			Make sure you <input> tag names match the names in AddNewCustomer.php
			
			your form tag should use the POST method. don't forget to specify the action attribute.
		-->
		<fieldset>
			<legend>Sign Up Form</legend>
			<table>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>	
						<label for="UserName">User Name:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<input type="text" name="UserName" id="UserName" placeholder="Enter User Name" required />
					</td>
				</tr>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<label for="FirstName">First Name:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<input type="text" name="FirstName" id="FirstName" placeholder="Enter First Name" required />
					</td>
				</tr>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<label for="LastName">Last Name:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<input type="text" name="LastName" id="LastName" placeholder="Enter Last Name" required />
					</td>
				</tr>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<label for="Address">Address:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<input type="text" name="Address" id="Address" placeholder="Enter Address" required />
					</td>
				</tr>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<label for="City">City:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<input type="text" name="City" id="City" placeholder="Enter City" required />
					</td>
				</tr>
			</table>
			<div class="password-container">			
			<table>
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<label for="UserPasswd">Password:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<input type="password" 
								name="UserPasswd" 
								id="UserPasswd" 
								placeholder="Enter Password" 
								maxlength="15" 
								autocomplete="off" required />

			<!-- Popover element placed adjacent to the field -->
					<div id="password-popover" class="popover hidden">
						<h4>Password Requirements</h4>
						<ul>
							<?php foreach ($password_rules as $key => $rule): ?>
								<!-- Each rule gets a unique data attribute -->
								<li data-rule="<?php echo $key; ?>" class="rule-item">
									<?php echo htmlspecialchars($rule); ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>			
					</td>
				</tr>				
				<tr>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<label for="conf-user-passwd">Confirm Password:</label>
					</td>
					<td style = 'padding-bottom: 10px; width: 100px;'>
						<input type="password" name="conf-user-passwd" id="conf-user-passwd" placeholder="Enter Password" maxlength="15" required />
					    <span id="match-error" class="error-text hidden">Passwords do not match</span>
					</td>
				</tr>									
			</table>
			</div>
			<input type="submit" value="Sign Up!" />
		</fieldset>
	</form>
	</div>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const signupForm = document.getElementById('signupForm');
    const passwordInput = document.getElementById('UserPasswd');
    const confirmInput = document.getElementById('conf-user-passwd');
    const matchError = document.getElementById('match-error');
    const passwordPopover = document.getElementById('password-popover');
    
    const rules = {
          length: document.querySelector('[data-rule="length"]'),
          uppercase: document.querySelector('[data-rule="uppercase"]'),
          number: document.querySelector('[data-rule="number"]'),
          special: document.querySelector('[data-rule="special"]')
    };

    const patterns = {
        length: (val) => val.length >= 8,
        uppercase: (val) => /[A-Z]/.test(val),
        number: (val) => /[0-9]/.test(val),
        special: (val) => /[@$!%*?&]/.test(val)
    };

    // --- Original Password Validation Logic ---
    passwordInput.addEventListener('focus', () => {
        passwordPopover.classList.remove('hidden');
    });

    passwordInput.addEventListener('blur', () => {
        validatePasswordRules();
        if (confirmInput.value !== '') {
            checkMatch();
        }
    });

    // Helper to validate just the primary password rules
    function validatePasswordRules() {
        const value = passwordInput.value;
        let allValid = true;

        Object.keys(patterns).forEach(key => {
            const isValid = patterns[key](value);
            if (isValid) {
                rules[key].classList.add('valid');
                rules[key].classList.remove('invalid');
            } else {
                rules[key].classList.add('invalid');
                rules[key].classList.remove('valid');
                allValid = false;
            }
        });

        if (allValid) {
            passwordPopover.classList.add('hidden');
        }
        return allValid; // Returns true if password meets all rules
    }

    // --- Updated Confirm Password Logic (Returns True/False) ---
    function checkMatch() {
        const passwordValue = passwordInput.value;
        const confirmValue = confirmInput.value;

        if (passwordValue === confirmValue && passwordValue !== '') {
            matchError.classList.add('hidden');
            confirmInput.style.borderColor = '#2e7d32';
            return true; // Passwords match
        } else {
            matchError.classList.remove('hidden');
            confirmInput.style.borderColor = '#c62828';
            return false; // Passwords do not match
        }
    }

    confirmInput.addEventListener('blur', checkMatch);
    confirmInput.addEventListener('input', checkMatch);

    // --- NEW: Intercept Form Submission ---
    signupForm.addEventListener('submit', (event) => {
        // Run both validation checks
        const isPasswordValid = validatePasswordRules();
        const isMatchValid = checkMatch();

        // If either check fails, cancel submission
        if (!isPasswordValid || !isMatchValid) {
            event.preventDefault(); // STOPS the form from going to AddNewCustomer.php
            
            // Forces the popover back open if the rules were broken
            if (!isPasswordValid) {
                passwordPopover.classList.remove('hidden');
            }
            
 //           alert('Please fix the errors in your password fields before submitting.');
        }
    });
});

</script>	
</body>
</html>